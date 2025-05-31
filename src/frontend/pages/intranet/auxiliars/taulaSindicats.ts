import { renderTaulaCercadorFiltres } from '../../../services/renderTaula/renderTaulaCercadorFiltres';
import { initDeleteHandlers } from '../../../services/fetchData/handleDelete';
import { getIsAdmin } from '../../../services/auth/getIsAdmin';
import { getIsAutor } from '../../../services/auth/getIsAutor';

interface EspaiRow {
  id: number;
  sindicat: string;
  sigles: string;
}

type Column<T> = {
  header: string;
  field: keyof T;
  render?: (value: T[keyof T], row: T) => string;
};

export async function taulaSindicats() {
  const isAdmin = await getIsAdmin();
  const isAutor = await getIsAutor();

  const columns: Column<EspaiRow>[] = [
    { header: 'Sindicat', field: 'sindicat' },
    { header: 'Sigla', field: 'sigles' },
  ];

  if (isAdmin || isAutor) {
    columns.push({
      header: 'Accions',
      field: 'id',
      render: (_: unknown, row: EspaiRow) => `<a id="${row.id}" title="Modifica" href="https://${window.location.hostname}/gestio/auxiliars/modifica-sindicat/${row.id}"><button type="button" class="btn btn-warning btn-sm">Modifica</button></a>`,
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
      data-url="/api/auxiliars/delete/sindicat/${row.id}"
    >
      Elimina
    </button>`,
    });
  }

  renderTaulaCercadorFiltres<EspaiRow>({
    url: `https://${window.location.host}/api/auxiliars/get/sindicats`,
    containerId: 'taulaLlistatSindicats',
    columns,
    filterKeys: ['sindicat'],
    //filterByField: 'provincia',
  });

  // Iniciar los listeners de borrado
  initDeleteHandlers(() => taulaSindicats()); // Recargar tabla después de eliminar
}
