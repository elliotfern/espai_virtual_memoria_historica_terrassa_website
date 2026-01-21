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
  es_processat: string;
  slug: string;
  num_registre: string;
  ciutat: string;
  num_causa: string;
  copia_exp: string;
}

type Column<T> = {
  header: string;
  field: keyof T;
  render?: (value: T[keyof T], row: T) => string;
};

export async function taulaProcessats() {
  const isAdmin = await getIsAdmin();
  const isAutor = await getIsAutor();
  const isLogged = await getIsLogged();
  const reloadKey = 'reload-taula-taulaLlistatProcessats';

  const columns: Column<EspaiRow>[] = [
    { header: 'ID', field: 'id' },

    { header: 'Nom i cognoms', field: 'id', render: (_: unknown, row: EspaiRow) => `<a id="${row.id}" title="Fitxa" href="https://${window.location.hostname}/fitxa/${row.slug}" target="_blank">${row.nom_complet}</a>` },
    {
      header: 'Naixement',
      field: 'id',
      render: (_: unknown, row: EspaiRow) => {
        const date = row.data_naixement;
        if (date && date !== '0000-00-00') {
          const info = `${formatDatesForm(date)} - ${row.ciutat}`;
          return info;
        } else {
          return '';
        }
      },
    },

    {
      header: 'Núm. Causa',
      field: 'id',
      render: (_: unknown, row: EspaiRow) => {
        const numCausa = row.num_causa;
        return numCausa;
      },
    },

    {
      header: 'Núm. Registre',
      field: 'id',
      render: (_: unknown, row: EspaiRow) => {
        const nomRegistre = row.num_registre;
        return nomRegistre;
      },
    },

    { header: 'Digitalitzat', field: 'copia_exp' },

    { header: 'Fitxa web', field: 'es_processat' },
  ];

  if (isAdmin || isAutor || isLogged) {
    columns.push({
      header: 'Accions',
      field: 'id',
      render: (_: unknown, row: EspaiRow) => `<a id="${row.id}" title="Modifica" target="_blank" href="https://${window.location.hostname}/gestio/base-dades/modifica-fitxa/${row.id}"><button type="button" class="btn btn-success btn-sm">Modifica Fitxa</button></a>`,
    });
  }

  if (isAdmin || isAutor || isLogged) {
    columns.push({
      header: 'Accions',
      field: 'id',
      render: (_: unknown, row: EspaiRow) => `<a id="${row.id}" title="Modifica" target="_blank" href="https://${window.location.hostname}/gestio/base-dades/modifica-repressio/6/${row.id}"><button type="button" class="btn btn-warning btn-sm">Modifica Dades Consell Guerra</button></a>`,
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
    url: API_URLS.GET.LLISTAT_PROCESSATS,
    containerId: 'taulaLlistatProcessats',
    columns,
    filterKeys: ['nom_complet'],
    //filterByField: 'es_deportat',
  });

  // Registra el callback con una clave única
  registerDeleteCallback(reloadKey, () => taulaProcessats());

  // Inicia el listener una sola vez
  initDeleteHandlers();
}
