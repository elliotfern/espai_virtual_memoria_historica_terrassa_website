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
import { estatButtonHTML } from '../../../services/renderTaula/estatBotons';
import { fontInternTextHTML } from '../../../services/renderTaula/fontIntern';
import { maybeVisibilitatButtonHTML } from '../../../services/renderTaula/estatVisibilitat';

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
  font_intern: number;
  visibilitat: number;
}

type RowExploded = EspaiRow & { categoria_button_label: string };

type Column<T> = {
  header: string;
  field: keyof T;
  render?: (value: T[keyof T], row: T) => string;
};

export async function taulaPaginaRepresaliats(): Promise<void> {
  const isAdmin = await getIsAdmin();
  const isAutor = await getIsAutor();
  const isLogged = await getIsLogged();
  const reloadKey = 'reload-taula-taulaLlistatPresoModel';

  // 1) Diccionario categorías
  const dictRaw: Category[] = await categoriesRepressio('ca');
  const labelById = buildLabelById(dictRaw);

  // 2) Carga de datos base
  const res = await fetch(API_URLS.GET.LLISTAT_REPRESALIATS_INTRANET);
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

    {
      header: 'Categoria',
      field: 'id',
      render: (_value, row) => traduirCategoriesRepressio(row.categoria, dictRaw),
    },

    {
      header: 'Dades',
      field: 'id',
      render: (_value, row) => {
        void _value;
        return fontInternTextHTML(row.font_intern); // solo texto
      },
    },

    {
      header: 'Estat',
      field: 'id',
      render: (_value, row) => {
        void _value; // evita warning eslint de arg sin uso
        // añade data-id por si luego quieres enganchar eventos delegados
        return estatButtonHTML(row.completat, {
          size: 'sm',
          attrs: { 'data-id': row.id, title: `Estat: ${row.completat}` },
        });
      },
    },

    {
      header: 'Visibilitat',
      field: 'id',
      render: (_value, row) => {
        void _value;
        return maybeVisibilitatButtonHTML(row.visibilitat, {
          size: 'sm',
          attrs: { 'data-id': row.id, title: `Visibilitat: ${row.visibilitat}` },
        });
      },
    },
  ];

  if (isAdmin || isAutor || isLogged) {
    columns.push({
      header: 'Accions',
      field: 'id',
      render: (_value, row) =>
        `<a id="${row.id}" title="Modifica" target="_blank" href="https://${window.location.hostname}/gestio/base-dades/modifica-fitxa/${row.id}">
           <button type="button" class="btn btn-secondary btn-sm">Modifica dades fitxa</button>
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

  await renderWithSecondLevelFilters<RowExploded>({
    containerId: 'taulaRepresaliats',
    data: baseExploded, // tu array ya explotado por categoría
    columns,
    filterKeys: ['nom_complet'],
    firstLevelField: 'categoria_button_label',
    statusField: 'completat',
    firstLevelTitle: 'Categories:',
    secondLevelTitle: 'Estat de les fitxes:',
    dedupeBy: (r) => r.id, // Tots sin duplicados
    // initialFirstLevelValue: labelById(6), // opcional: arrancar con una categoría ya activa
  });

  registerDeleteCallback(reloadKey, () => taulaPaginaRepresaliats());
  initDeleteHandlers();
}
