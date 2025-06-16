import { API_URLS } from '../../../services/api/ApiUrls';
import { renderTaulaCercadorFiltres } from '../../../services/renderTaula/renderTaulaCercadorFiltres';
import { initDeleteHandlers, registerDeleteCallback } from '../../../services/fetchData/handleDelete';
import { getIsAdmin } from '../../../services/auth/getIsAdmin';
import { getIsAutor } from '../../../services/auth/getIsAutor';
import { getIsLogged } from '../../../services/auth/getIsLogged';
import { formatDatesForm } from '../../../services/formatDates/dates';

interface EspaiRow {
  id: number;
  nom: string;
  nom_complet: string;
  data_naixement: string;
  data_defuncio: string;
  cognom2: string;
  categoria: string;
  es_represalia: string;
}

type Column<T> = {
  header: string;
  field: keyof T;
  render?: (value: T[keyof T], row: T) => string;
};

export async function taulaRepresaliaRepublicana() {
  const isAdmin = await getIsAdmin();
  const isAutor = await getIsAutor();
  const isLogged = await getIsLogged();
  const reloadKey = 'reload-taula-taulaLlistatRepresaliaRepublicana';

  const columns: Column<EspaiRow>[] = [
    { header: 'ID', field: 'id' },
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
    { header: 'Fitxa represàlia republicana creada', field: 'es_represalia' },
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
      render: (_: unknown, row: EspaiRow) => `<a id="${row.id}" title="Modifica" target="_blank" href="https://${window.location.hostname}/gestio/base-dades/modifica-repressio/4/${row.id}"><button type="button" class="btn btn-warning btn-sm">Modifica repressió</button></a>`,
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

  renderTaulaCercadorFiltres<EspaiRow>({
    url: API_URLS.GET.LLISTAT_REPRESALIA_REPUBLICANA,
    containerId: 'taulaLlistatRepresaliaRepublicana',
    columns,
    filterKeys: ['nom_complet'],
    //filterByField: 'es_deportat',
  });

  // Registra el callback con una clave única
  registerDeleteCallback(reloadKey, () => taulaRepresaliaRepublicana());

  // Inicia el listener una sola vez
  initDeleteHandlers();
}
