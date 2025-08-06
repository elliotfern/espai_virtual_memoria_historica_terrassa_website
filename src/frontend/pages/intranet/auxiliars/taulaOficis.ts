import { API_URLS } from '../../../services/api/ApiUrls';
import { renderTaulaCercadorFiltres } from '../../../services/renderTaula/renderTaulaCercadorFiltres';
import { initDeleteHandlers, registerDeleteCallback } from '../../../services/fetchData/handleDelete';
import { getIsAdmin } from '../../../services/auth/getIsAdmin';
import { getIsAutor } from '../../../services/auth/getIsAutor';

interface EspaiRow {
  id: number;
  ciutat: string;
  comarca: string;
  provincia: string;
  comunitat: string;
  estat: string;
  ofici_cat: string;
}

type Column<T> = {
  header: string;
  field: keyof T;
  render?: (value: T[keyof T], row: T) => string;
};

// Estado global para mantener la paginación, búsqueda y filtros
let currentPageOficis = 1;
let currentSearchText = '';
let currentFilterValue: EspaiRow[keyof EspaiRow] | null = null;

export async function taulaOficis() {
  const container = document.getElementById('taulaOficis');
  if (container) container.innerHTML = '';

  const isAdmin = await getIsAdmin();
  const isAutor = await getIsAutor();
  const reloadKey = 'reload-taula-taulaOficis';

  const columns: Column<EspaiRow>[] = [{ header: 'Ofici', field: 'ofici_cat' }];

  if (isAdmin || isAutor) {
    columns.push({
      header: 'Accions',
      field: 'id',
      render: (_: unknown, row: EspaiRow) =>
        `<a id="${row.id}" title="Modifica" href="https://${window.location.hostname}/gestio/auxiliars/modifica-ofici/${row.id}">
           <button type="button" class="btn btn-warning btn-sm">Modifica</button>
         </a>`,
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
          data-url="/api/auxiliars/delete/ofici/${row.id}"
          data-reload-callback="${reloadKey}"
        >
          Elimina
        </button>`,
    });
  }

  const result = await renderTaulaCercadorFiltres<EspaiRow>({
    url: API_URLS.GET.OFICIS,
    containerId: 'taulaOficis',
    columns,
    filterKeys: ['ofici_cat'],
    initialPage: currentPageOficis,
    initialSearch: currentSearchText,
    initialFilterValue: currentFilterValue,
  });

  // Guardar el estado actual para mantenerlo en la siguiente recarga
  currentPageOficis = result.page;
  currentSearchText = result.search;
  currentFilterValue = result.filter;

  console.log('Estado actualizado:', currentPageOficis, currentSearchText, currentFilterValue);

  registerDeleteCallback(reloadKey, async () => {
    console.log('Recargando tabla con página', currentPageOficis);
    await taulaOficis();
  });

  initDeleteHandlers();
}
