import { API_URLS } from '../../../services/api/ApiUrls';
import { renderTaulaCercadorFiltres } from '../../../services/renderTaula/renderTaulaCercadorFiltres';
import { initDeleteHandlers, registerDeleteCallback } from '../../../services/fetchData/handleDelete';
import { getIsAdmin } from '../../../services/auth/getIsAdmin';
import { getIsAutor } from '../../../services/auth/getIsAutor';
import { getIsLogged } from '../../../services/auth/getIsLogged';
import { formatDatesForm } from '../../../services/formatDates/dates';
import { traduirCategoriesRepressio } from '../../../components/taulaDades/traduirCategoriesRepressio';
import { categoriesRepressio } from '../../../components/taulaDades/categoriesRepressio';

// 👇 Usa el MISMO tipo que espera traduirCategoriesRepressio
type Category = { id: number; name: string };

interface EspaiRow {
  id: number;
  nom: string;
  nom_complet: string;
  data_naixement: string;
  data_defuncio: string;
  cognom2: string;
  categoria: string; // Ej: "{11,22,2}"
  es_PresoModel: string;
  slug: string;
}

type Column<T> = {
  header: string;
  field: keyof T;
  render?: (value: T[keyof T], row: T) => string;
};

// ————————————————— Helpers —————————————————

// Convierte "{11,22,2}" -> [11,22,2]
function parseCategoriaIds(raw: string | null | undefined): number[] {
  if (!raw) return [];
  return raw
    .replace(/[{}]/g, '')
    .split(',')
    .map((s) => parseInt(s.trim(), 10))
    .filter((n) => !Number.isNaN(n));
}

// Para mapear id -> label sin cambiar el tipo original
type CatDictItem = { id: number; label: string };

function makeLabelMap(dict: CatDictItem[]): Record<number, string> {
  const m: Record<number, string> = {};
  for (const it of dict) m[it.id] = it.label;
  return m;
}

function jsonToBlobUrl(obj: unknown): string {
  const blob = new Blob([JSON.stringify(obj)], { type: 'application/json' });
  return URL.createObjectURL(blob);
}

// ————————————————— Main —————————————————

export async function taulaPendentsAjuntament(): Promise<void> {
  const isAdmin = await getIsAdmin();
  const isAutor = await getIsAutor();
  const isLogged = await getIsLogged();
  const reloadKey = 'reload-taula-taulaLlistatPendents';

  // 1) Diccionario crudo EXACTO que entiende traduirCategoriesRepressio
  const dictRaw: Category[] = await categoriesRepressio('ca');

  // 2) Diccionario normalizado SOLO para construir etiquetas por id
  const dictNorm: CatDictItem[] = dictRaw.map((c) => ({ id: c.id, label: c.name }));
  const labelMap = makeLabelMap(dictNorm);

  // 3) Datos originales de la API
  const res = await fetch(API_URLS.GET.LLISTAT_PENDENTS_AJUNTAMENT);
  const json = await res.json();

  // 4) Normaliza a array
  const baseData: EspaiRow[] = Array.isArray(json) ? (json as EspaiRow[]) : Array.isArray(json?.data) ? (json.data as EspaiRow[]) : json?.data ? [json.data as EspaiRow] : [];

  // 5) “Explota” filas por categoría y añade campo monovalente
  type RowExploded = EspaiRow & { categoria_button_label: string };
  const exploded: RowExploded[] = [];

  for (const row of baseData) {
    const ids = parseCategoriaIds(row.categoria);
    if (ids.length === 0) {
      // si no tiene categorías
      exploded.push({ ...row, categoria_button_label: '' });
      continue;
    }
    for (const id of ids) {
      const label = labelMap[id] ?? String(id);
      exploded.push({ ...row, categoria_button_label: label });
    }
  }

  // 6) Columnas (muestra TODAS las categorías en la celda, usando dictRaw con 'name')
  const columns: Column<RowExploded>[] = [
    { header: 'ID', field: 'id' },
    {
      header: 'Nom i cognoms',
      field: 'id',
      render: (_: RowExploded[keyof RowExploded], row: RowExploded) => `<a id="${row.id}" title="Fitxa" href="https://${window.location.hostname}/fitxa/${row.slug}" target="_blank">${row.nom_complet}</a>`,
    },
    {
      header: 'Data naixement',
      field: 'id',
      render: (_: RowExploded[keyof RowExploded], row: RowExploded) => (row.data_naixement && row.data_naixement !== '0000-00-00' ? formatDatesForm(row.data_naixement) ?? '' : ''),
    },
    {
      header: 'Data defunció',
      field: 'id',
      render: (_: RowExploded[keyof RowExploded], row: RowExploded) => (row.data_defuncio && row.data_defuncio !== '0000-00-00' ? formatDatesForm(row.data_defuncio) ?? '' : ''),
    },
    {
      header: 'Categoria',
      field: 'id',
      // Renderiza todas las categorías traducidas (traduirCategoriesRepressio usa Category[])
      render: (_: RowExploded[keyof RowExploded], row: RowExploded) => traduirCategoriesRepressio(row.categoria, dictRaw),
    },
  ];

  if (isAdmin || isAutor || isLogged) {
    columns.push({
      header: 'Accions',
      field: 'id',
      render: (_: RowExploded[keyof RowExploded], row: RowExploded) =>
        `<a id="${row.id}" title="Modifica" target="_blank" href="https://${window.location.hostname}/gestio/base-dades/modifica-fitxa/${row.id}">
           <button type="button" class="btn btn-success btn-sm">Modifica Dades personals</button>
         </a>`,
    });
  }

  if (isAdmin) {
    columns.push({
      header: '',
      field: 'id',
      render: (_: RowExploded[keyof RowExploded], row: RowExploded) => `
        <button 
          type="button"
          class="btn btn-danger btn-sm delete-button"
          data-id="${row.id}" 
          data-url="/api/dades_personals/delete/eliminaDuplicat?id=${row.id}"
          data-reload-callback="${reloadKey}">
          Elimina
        </button>`,
    });
  }

  // 7) Empaqueta en blob:URL en el formato que espera tu renderer
  const blobUrl = jsonToBlobUrl({
    status: 'success',
    message: 'OK',
    errors: [],
    data: exploded,
  });

  // 8) Render con botones por categoría (igualdad exacta)
  await renderTaulaCercadorFiltres<RowExploded>({
    url: blobUrl,
    containerId: 'taulaLlistatPendents',
    columns,
    filterKeys: ['nom_complet'], // búsqueda textual
    filterByField: 'categoria_button_label', // BOTONES = categorías
    // Opcional para evitar ver duplicados de inicio:
    // initialFilterValue: 'Depurat',
  });

  // 9) Recarga tras eliminar
  registerDeleteCallback(reloadKey, () => taulaPendentsAjuntament());
  initDeleteHandlers();
}
