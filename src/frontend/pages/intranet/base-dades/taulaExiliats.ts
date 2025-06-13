import { API_URLS } from '../../../services/api/ApiUrls';
import { renderTaulaCercadorFiltres } from '../../../services/renderTaula/renderTaulaCercadorFiltres';
import { initDeleteHandlers, registerDeleteCallback } from '../../../services/fetchData/handleDelete';
import { getIsAdmin } from '../../../services/auth/getIsAdmin';
import { getIsAutor } from '../../../services/auth/getIsAutor';
import { formatDatesForm } from '../../../services/formatDates/dates';

interface EspaiRow {
  id: number;
  nom_complet: string;
  data_defuncio: string;
  data_naixement: string;
  es_exiliat: string;
}

type Column<T> = {
  header: string;
  field: keyof T;
  render?: (value: T[keyof T], row: T) => string;
};

export async function taulaExiliats() {
  const isAdmin = await getIsAdmin();
  const isAutor = await getIsAutor();
  const reloadKey = 'reload-taula-taulaLlistatExiliats';

  const columns: Column<EspaiRow>[] = [
    { header: 'Nom i cognoms', field: 'id', render: (_: unknown, row: EspaiRow) => `<a id="${row.id}" title="Fitxa" href="https://${window.location.hostname}/fitxa/${row.id}" target="_blank">${row.nom_complet}</a>` },

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
    { header: 'Fitxa exiliat creada', field: 'es_exiliat' },
  ];

  if (isAdmin || isAutor) {
    columns.push({
      header: 'Accions',
      field: 'id',
      render: (_: unknown, row: EspaiRow) => `<a id="${row.id}" title="Modifica" href="https://${window.location.hostname}/gestio/base-dades/modifica-repressio/10/${row.id}"><button type="button" class="btn btn-warning btn-sm">Modifica</button></a>`,
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
          data-url="/api/auxiliars/delete/causa_defuncio/${row.id}"
          data-reload-callback="${reloadKey}"
        >
          Elimina
        </button>`,
    });
  }

  renderTaulaCercadorFiltres<EspaiRow>({
    url: API_URLS.GET.LLISTAT_EXILIATS,
    containerId: 'taulaLlistatExiliats',
    columns,
    filterKeys: ['nom_complet'],
    filterByField: 'es_exiliat',
  });

  // Registra el callback con una clave única
  registerDeleteCallback(reloadKey, () => taulaExiliats());

  // Inicia el listener una sola vez
  initDeleteHandlers();
}
