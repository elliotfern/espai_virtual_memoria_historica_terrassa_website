import { API_URLS } from '../../../services/api/ApiUrls';
import { renderTaulaCercadorFiltres } from '../../../services/renderTaula/renderTaulaCercadorFiltres';
import { initDeleteHandlers, registerDeleteCallback } from '../../../services/fetchData/handleDelete';
import { getIsAdmin } from '../../../services/auth/getIsAdmin';
import { getIsAutor } from '../../../services/auth/getIsAutor';

interface EspaiRow {
  id: number;
  ciutat: string;
  autor: string;
  any: string;
  editorial: string;
  llibre: string;
}

type Column<T> = {
  header: string;
  field: keyof T;
  render?: (value: T[keyof T], row: T) => string;
};

export async function taulaLlistatBibliografia() {
  const isAdmin = await getIsAdmin();
  const isAutor = await getIsAutor();
  const reloadKey = 'reload-taula-taulaLlistatBibliografia';

  const columns: Column<EspaiRow>[] = [
    { header: 'Ofici', field: 'llibre' },
    { header: 'Autor', field: 'autor' },
    { header: 'Any', field: 'any' },
    { header: 'Ciutat', field: 'ciutat' },
    { header: 'Editorial', field: 'editorial' },
  ];

  if (isAdmin || isAutor) {
    columns.push({
      header: 'Accions',
      field: 'id',
      render: (_: unknown, row: EspaiRow) => `<a id="${row.id}" title="Modifica" href="https://${window.location.hostname}/gestio/fonts-documentals/modifica-llibre/${row.id}"><button type="button" class="btn btn-warning btn-sm">Modifica</button></a>`,
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
          data-url="/api/fonts/delete/llibre/${row.id}"
          data-reload-callback="${reloadKey}"
        >
          Elimina
        </button>`,
    });
  }

  renderTaulaCercadorFiltres<EspaiRow>({
    url: API_URLS.GET.LLISTAT_BIBLIOGRAFIA,
    containerId: 'taulaLlistatBibliografia',
    columns,
    filterKeys: ['llibre'],
    //filterByField: 'provincia',
  });

  // Registra el callback con una clave única
  registerDeleteCallback(reloadKey, () => taulaLlistatBibliografia());

  // Inicia el listener una sola vez
  initDeleteHandlers();
}
