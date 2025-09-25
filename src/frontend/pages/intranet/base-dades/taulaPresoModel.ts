import { API_URLS } from '../../../services/api/ApiUrls';
import { initDeleteHandlers, registerDeleteCallback } from '../../../services/fetchData/handleDelete';
import { getIsAdmin } from '../../../services/auth/getIsAdmin';
import { getIsAutor } from '../../../services/auth/getIsAutor';
import { getIsLogged } from '../../../services/auth/getIsLogged';
import { formatDatesForm } from '../../../services/formatDates/dates';
import { buildLabelById, parseSet } from '../../../services/fetchData/categories';
import { categoriesRepressio } from '../../../components/taulaDades/categoriesRepressio';
import { traduirCategoriesRepressio } from '../../../components/taulaDades/traduirCategoriesRepressio';
import { renderWithSecondLevelFilters } from '../../../services/renderTaula/filtreCompletats';

type Category = { id: number; name: string };

interface EspaiRow {
  id: number;
  nom: string;
  nom_complet: string;
  data_naixement: string;
  data_defuncio: string;
  cognom2: string;
  categoria: string; // "{11,22,2}"
  es_PresoModel: string;
  slug: string;
  completat: number | string; // viene de la API
}

type RowExploded = EspaiRow & { categoria_button_label: string };

type Column<T> = {
  header: string;
  field: keyof T;
  render?: (value: T[keyof T], row: T) => string;
};

// Mapa de completat → estado (ajusta si tus códigos difieren)
type StatusKey = 'completats' | 'revisio' | 'pendents';
type StatusKeyAll = 'tots' | StatusKey;

const STATUS_MAP_NUM: Record<number, StatusKey> = {
  0: 'pendents',
  1: 'revisio',
  2: 'completats',
};
function mapCompletatToStatus(v: unknown): StatusKey {
  if (typeof v === 'number') return STATUS_MAP_NUM[v] ?? 'pendents';
  if (typeof v === 'string') {
    const s = v.trim().toLowerCase();
    if (s === '2' || s.startsWith('completat')) return 'completats';
    if (s === '1' || s.startsWith('revisi')) return 'revisio';
    return 'pendents';
  }
  return 'pendents';
}

export async function taulaPresoModel(): Promise<void> {
  const isAdmin = await getIsAdmin();
  const isAutor = await getIsAutor();
  const isLogged = await getIsLogged();
  const reloadKey = 'reload-taula-taulaLlistatPresoModel';

  // 1) Diccionario categorías
  const dictRaw: Category[] = await categoriesRepressio('ca');
  const labelById = buildLabelById(dictRaw);

  // 2) Carga de datos base
  const res = await fetch(API_URLS.GET.LLISTAT_PRESO_MODEL);
  const json = await res.json();
  const base: EspaiRow[] = Array.isArray(json) ? (json as EspaiRow[]) : Array.isArray(json?.data) ? (json.data as EspaiRow[]) : json?.data ? [json.data as EspaiRow] : [];

  // 3) Explota por categorías (si quieres limitar a id=6, filtra aquí)
  const baseExploded: RowExploded[] = [];
  for (const row of base) {
    const ids = parseSet(typeof row.categoria === 'string' ? row.categoria : String(row.categoria ?? ''));
    // const ids = parseSet(row.categoria).filter(id => id === 6); // <- para solo "Presó Model"
    for (const id of ids) {
      baseExploded.push({ ...row, categoria_button_label: labelById(id) });
    }
  }

  // 4) Columnas
  const columns: Column<RowExploded>[] = [
    { header: 'ID', field: 'id' },
    {
      header: 'Nom i cognoms',
      field: 'id',
      render: (_value, row) => `<a id="${row.id}" title="Fitxa" href="https://${window.location.hostname}/fitxa/${row.slug}" target="_blank">${row.nom_complet}</a>`,
    },
    {
      header: 'Data naixement',
      field: 'id',
      render: (_value, row) => (row.data_naixement && row.data_naixement !== '0000-00-00' ? formatDatesForm(row.data_naixement) ?? '' : ''),
    },
    {
      header: 'Data defunció',
      field: 'id',
      render: (_value, row) => (row.data_defuncio && row.data_defuncio !== '0000-00-00' ? formatDatesForm(row.data_defuncio) ?? '' : ''),
    },
    { header: 'Fitxa presó model creada', field: 'es_PresoModel' },
    {
      header: 'Categoria',
      field: 'id',
      render: (_value, row) => traduirCategoriesRepressio(row.categoria, dictRaw),
    },
  ];

  if (isAdmin || isAutor || isLogged) {
    columns.push({
      header: 'Accions',
      field: 'id',
      render: (_value, row) =>
        `<a id="${row.id}" title="Modifica" target="_blank" href="https://${window.location.hostname}/gestio/base-dades/modifica-fitxa/${row.id}">
           <button type="button" class="btn btn-success btn-sm">Modifica Dades personals</button>
         </a>`,
    });
  }
  if (isAdmin || isAutor || isLogged) {
    columns.push({
      header: 'Accions',
      field: 'id',
      render: (_value, row) =>
        `<a id="${row.id}" title="Modifica" target="_blank" href="https://${window.location.hostname}/gestio/base-dades/modifica-repressio/6/${row.id}">
           <button type="button" class="btn btn-warning btn-sm">Modifica repressio</button>
         </a>`,
    });
  }
  if (isAdmin) {
    columns.push({
      header: '',
      field: 'id',
      render: (_value, row) => `
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

  // 5) Botones de 2º nivel
  const buttons: ReadonlyArray<{ key: StatusKeyAll; label: string }> = [
    { key: 'tots', label: 'Tots' },
    { key: 'completats', label: 'Completats' },
    { key: 'revisio', label: 'Revisió' },
    { key: 'pendents', label: 'Pendents' },
  ];

  await renderWithSecondLevelFilters<RowExploded, StatusKey>({
    containerId: 'taulaLlistatPresoModel',
    data: baseExploded,
    columns,
    filterKeys: ['nom_complet'],
    firstLevelField: 'categoria_button_label',
    buttons,
    mapRowToKey: (row) => mapCompletatToStatus(row.completat),
    secondLevelTitle: 'Estat de les fitxes:',
    // initialKey: 'tots',                     // opcional
    // initialFirstLevelValue: labelById(6),   // opcional: arrancar ya filtrado por categoría “6”
  });

  registerDeleteCallback(reloadKey, () => taulaPresoModel());
  initDeleteHandlers();
}
