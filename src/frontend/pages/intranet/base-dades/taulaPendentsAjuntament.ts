import { API_URLS } from '../../../services/api/ApiUrls';
import { renderTaulaCercadorFiltres } from '../../../services/renderTaula/renderTaulaCercadorFiltres';
import { initDeleteHandlers, registerDeleteCallback } from '../../../services/fetchData/handleDelete';
import { getIsAdmin } from '../../../services/auth/getIsAdmin';
import { getIsAutor } from '../../../services/auth/getIsAutor';
import { getIsLogged } from '../../../services/auth/getIsLogged';
import { formatDatesForm } from '../../../services/formatDates/dates';
import { traduirCategoriesRepressio } from '../../../components/taulaDades/traduirCategoriesRepressio';
import { categoriesRepressio } from '../../../components/taulaDades/categoriesRepressio';
import { buildLabelById, explodeSetToBlobUrl } from '../../../services/fetchData/categories';

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
}

type Column<T> = {
  header: string;
  field: keyof T;
  render?: (value: T[keyof T], row: T) => string;
};

export async function taulaPendentsAjuntament(): Promise<void> {
  const isAdmin = await getIsAdmin();
  const isAutor = await getIsAutor();
  const isLogged = await getIsLogged();
  const reloadKey = 'reload-taula-taulaLlistatPendents';

  const dictRaw: Category[] = await categoriesRepressio('ca');
  const labelById = buildLabelById(dictRaw);

  const blobUrl = await explodeSetToBlobUrl<EspaiRow, 'categoria_button_label'>({
    url: API_URLS.GET.LLISTAT_PENDENTS_AJUNTAMENT,
    setField: 'categoria',
    labelById,
    targetField: 'categoria_button_label',
    includeEmpty: true,
  });

  type RowExploded = EspaiRow & { categoria_button_label: string };

  const columns: Column<RowExploded>[] = [
    { header: 'ID', field: 'id' },
    {
      header: 'Nom i cognoms',
      field: 'id',
      render: (_value, row) => {
        void _value; // evita no-used-vars
        return `<a id="${row.id}" title="Fitxa" href="https://${window.location.hostname}/fitxa/${row.slug}" target="_blank">${row.nom_complet}</a>`;
      },
    },
    {
      header: 'Data naixement',
      field: 'id',
      render: (_value, row) => {
        void _value;
        return row.data_naixement && row.data_naixement !== '0000-00-00' ? formatDatesForm(row.data_naixement) ?? '' : '';
      },
    },
    {
      header: 'Data defunció',
      field: 'id',
      render: (_value, row) => {
        void _value;
        return row.data_defuncio && row.data_defuncio !== '0000-00-00' ? formatDatesForm(row.data_defuncio) ?? '' : '';
      },
    },
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
      render: (_value, row) => {
        void _value;
        return `<a id="${row.id}" title="Modifica" target="_blank" href="https://${window.location.hostname}/gestio/base-dades/modifica-fitxa/${row.id}">
           <button type="button" class="btn btn-success btn-sm">Modifica Dades personals</button>
         </a>`;
      },
    });
  }

  if (isAdmin) {
    columns.push({
      header: '',
      field: 'id',
      render: (_value, row) => {
        void _value;
        return `
        <button 
          type="button"
          class="btn btn-danger btn-sm delete-button"
          data-id="${row.id}" 
          data-url="/api/dades_personals/delete/eliminaDuplicat?id=${row.id}"
          data-reload-callback="${reloadKey}">
          Elimina
        </button>`;
      },
    });
  }

  await renderTaulaCercadorFiltres<RowExploded>({
    url: blobUrl,
    containerId: 'taulaLlistatPendents',
    columns,
    filterKeys: ['nom_complet'],
    filterByField: 'categoria_button_label',
  });

  // revoca el blob para liberar memoria (ya fue “fetched” por el renderer)
  URL.revokeObjectURL(blobUrl);

  registerDeleteCallback(reloadKey, () => taulaPendentsAjuntament());
  initDeleteHandlers();
}
