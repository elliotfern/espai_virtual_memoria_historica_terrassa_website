import { renderTaulaCercadorFiltres } from '../../../services/renderTaula/renderTaulaCercadorFiltres';
import {
  initDeleteHandlers,
  registerDeleteCallback,
} from '../../../services/fetchData/handleDelete';
import { getIsAdmin } from '../../../services/auth/getIsAdmin';
import { getIsAutor } from '../../../services/auth/getIsAutor';
import { ENV } from '../../../config/env';

interface EspaiRow {
  id: number;
  nom: string;
  cognoms: string;
  carrec: string;
}

type Column<T> = {
  header: string;
  field: keyof T;
  render?: (value: T[keyof T], row: T) => string;
};

export async function llistatPresidentsTribunal() {
  const isAdmin = await getIsAdmin();
  const isAutor = await getIsAutor();
  const reloadKey = 'reload-taula-tabla1';

  const columns: Column<EspaiRow>[] = [
    {
      header: 'Nom i cognoms',
      field: 'nom',
      render: (_: unknown, row: EspaiRow) => `${row.nom} ${row.cognoms}`,
    },

    {
      header: 'Càrrec',
      field: 'carrec',
      render: (_: unknown, row: EspaiRow) => `${row.carrec}`,
    },
  ];

  if (isAdmin || isAutor) {
    columns.push({
      header: 'Accions',
      field: 'id',
      render: (_: unknown, row: EspaiRow) =>
        `<a id="${row.id}" title="Modifica" href="${ENV.domainWeb}/gestio/auxiliars/modifica-president-tribunal/${row.id}"><button type="button" class="btn btn-warning btn-sm">Modifica</button></a>`,
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
          data-url="${ENV.apiBaseUrl}/auxiliars/delete/tipusJudici/${row.id}"
          data-reload-callback="${reloadKey}"
        >
          Elimina
        </button>`,
    });
  }

  renderTaulaCercadorFiltres<EspaiRow>({
    url: `${ENV.apiBaseUrl}/auxiliars/get/presidents_tribunals`,
    containerId: 'tabla1',
    columns,
    //filterKeys: ,
    //filterByField: 'sector_cat',
  });

  // Registra el callback con una clave única
  registerDeleteCallback(reloadKey, () => llistatPresidentsTribunal());

  // Inicia el listener una sola vez
  initDeleteHandlers();
}
