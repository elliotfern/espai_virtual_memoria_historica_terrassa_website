import { API_URLS } from '../../../services/api/ApiUrls';
import { renderTaulaCercadorFiltres } from '../../../services/renderTaula/renderTaulaCercadorFiltres';
import { initDeleteHandlers, registerDeleteCallback } from '../../../services/fetchData/handleDelete';
import { getIsAdmin } from '../../../services/auth/getIsAdmin';
import { getIsAutor } from '../../../services/auth/getIsAutor';
import { getIsLogged } from '../../../services/auth/getIsLogged';
import { formatDatesForm } from '../../../services/formatDates/dates';
import { buildLabelById, explodeSetToBlobUrl } from '../../../services/fetchData/categories';
import { categoriesRepressio } from '../../../components/taulaDades/categoriesRepressio';
import { traduirCategoriesRepressio } from '../../../components/taulaDades/traduirCategoriesRepressio';

type Category = { id: number; name: string };

interface EspaiRow {
  id: number;
  nom: string;
  nom_complet: string;
  data_naixement: string;
  data_defuncio: string;
  cognom2: string;
  categoria: string;
  es_PresoModel: string;
  slug: string;
}

type Column<T> = {
  header: string;
  field: keyof T;
  render?: (value: T[keyof T], row: T) => string;
};

export async function taulaPresoModel() {
  const isAdmin = await getIsAdmin();
  const isAutor = await getIsAutor();
  const isLogged = await getIsLogged();
  const reloadKey = 'reload-taula-taulaLlistatPresoModel';

  const dictRaw: Category[] = await categoriesRepressio('ca');
  const labelById = buildLabelById(dictRaw);
  const presoModelLabel = labelById(13);

  const blobUrl = await explodeSetToBlobUrl<EspaiRow, 'categoria_button_label'>({
    url: API_URLS.GET.LLISTAT_PRESO_MODEL,
    setField: 'categoria',
    labelById,
    targetField: 'categoria_button_label',
    includeEmpty: true,
  });

  type RowExploded = EspaiRow & { categoria_button_label: string };

  const columns: Column<RowExploded>[] = [
    { header: 'ID', field: 'id' },
    { header: 'Nom i cognoms', field: 'id', render: (_: unknown, row: EspaiRow) => `<a id="${row.id}" title="Fitxa" href="https://${window.location.hostname}/fitxa/${row.slug}" target="_blank">${row.nom_complet}</a>` },
    {
      header: 'Data naixement',
      field: 'id',
      render: (_: unknown, row: EspaiRow) => {
        const date = row.data_naixement;
        if (date && date !== '0000-00-00') {
          return formatDatesForm(date) ?? '';
        } else {
          return '';
        }
      },
    },

    {
      header: 'Data defunció',
      field: 'id',
      render: (_: unknown, row: EspaiRow) => {
        const date2 = row.data_defuncio;
        if (date2 && date2 !== '0000-00-00') {
          return formatDatesForm(date2) ?? '';
        } else {
          return '';
        }
      },
    },
    { header: 'Fitxa', field: 'es_PresoModel' },
    {
      header: 'Categoria',
      field: 'id',
      render: (_value, row) => {
        void _value;
        return traduirCategoriesRepressio(row.categoria, dictRaw);
      },
    },
  ];

  if (isAdmin || isAutor || isLogged) {
    columns.push({
      header: 'Accions',
      field: 'id',
      render: (_: unknown, row: EspaiRow) => `<a id="${row.id}" title="Modifica" target="_blank" href="https://${window.location.hostname}/gestio/base-dades/modifica-fitxa/${row.id}"><button type="button" class="btn btn-success btn-sm">Modifica Dades personals</button></a>`,
    });
  }

  if (isAdmin || isAutor || isLogged) {
    columns.push({
      header: 'Accions',
      field: 'id',
      render: (_: unknown, row: EspaiRow) => `<a id="${row.id}" title="Modifica" target="_blank" href="https://${window.location.hostname}/gestio/base-dades/modifica-repressio/6/${row.id}"><button type="button" class="btn btn-warning btn-sm">Modifica repressio</button></a>`,
    });
  }

  if (isAdmin) {
    columns.push({
      header: '',
      field: 'id',
      render: (_: unknown, row: EspaiRow) => `
        <button 
          type="button"
          class="btn btn-danger btn-sm delete-button"
          data-id="${row.id}" 
          data-url="/api/dades_personals/delete/eliminaDuplicat?id=${row.id}"
          data-reload-callback="${reloadKey}"
        >
          Elimina
        </button>`,
    });
  }

  await renderTaulaCercadorFiltres<RowExploded>({
    url: blobUrl,
    containerId: 'taulaLlistatPresoModel',
    columns,
    filterKeys: ['nom_complet'],
    filterByField: 'categoria_button_label',
    initialFilterValue: presoModelLabel,
  });

  // revoca el blob para liberar memoria (ya fue “fetched” por el renderer)
  URL.revokeObjectURL(blobUrl);

  // Registra el callback con una clave única
  registerDeleteCallback(reloadKey, () => taulaPresoModel());

  // Inicia el listener una sola vez
  initDeleteHandlers();
}
