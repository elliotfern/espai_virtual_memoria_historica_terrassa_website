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
  sector_cat: string;
  motiuEmpresonament_ca: string;
}

type Column<T> = {
  header: string;
  field: keyof T;
  render?: (value: T[keyof T], row: T) => string;
};

export async function taulaMotiusDetencio() {
  const isAdmin = await getIsAdmin();
  const isAutor = await getIsAutor();
  const reloadKey = 'reload-taula-taulaMotiusDetencio';

  const columns: Column<EspaiRow>[] = [{ header: 'Motiu detenció/empresonament', field: 'motiuEmpresonament_ca' }];

  if (isAdmin || isAutor) {
    columns.push({
      header: 'Accions',
      field: 'id',
      render: (_: unknown, row: EspaiRow) => `<a id="${row.id}" title="Modifica" href="https://${window.location.hostname}/gestio/auxiliars/modifica-motiu-detencio/${row.id}"><button type="button" class="btn btn-warning btn-sm">Modifica</button></a>`,
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
          data-url="/api/auxiliars/delete/motiuDetencio/${row.id}"
          data-reload-callback="${reloadKey}"
        >
          Elimina
        </button>`,
    });
  }

  renderTaulaCercadorFiltres<EspaiRow>({
    url: API_URLS.GET.MOTIUS_DETENCIO,
    containerId: 'taulaMotiusDetencio',
    columns,
    filterKeys: ['motiuEmpresonament_ca'],
    //filterByField: 'sector_cat',
  });

  // Registra el callback con una clave única
  registerDeleteCallback(reloadKey, () => taulaMotiusDetencio());

  // Inicia el listener una sola vez
  initDeleteHandlers();
}
