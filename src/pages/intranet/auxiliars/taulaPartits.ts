import { renderTaulaCercadorFiltres } from '../../../services/renderTaula/renderTaulaCercadorFiltres';
import { initDeleteHandlers, registerDeleteCallback } from '../../../services/fetchData/handleDelete';
import { getIsAdmin } from '../../../services/auth/getIsAdmin';
import { getIsAutor } from '../../../services/auth/getIsAutor';

interface EspaiRow {
  id: number;
  sigles: string;
  partit_politic: string;
}

type Column<T> = {
  header: string;
  field: keyof T;
  render?: (value: T[keyof T], row: T) => string;
};

export async function taulaPartits() {
  const isAdmin = await getIsAdmin();
  const isAutor = await getIsAutor();
  const reloadKey = 'reload-taula-partits';

  const columns: Column<EspaiRow>[] = [
    { header: 'Partit polític', field: 'partit_politic' },
    { header: 'Sigla', field: 'sigles' },
  ];

  if (isAdmin || isAutor) {
    columns.push({
      header: 'Accions',
      field: 'id',
      render: (_: unknown, row: EspaiRow) => `<a id="${row.id}" title="Modifica" href="https://${window.location.hostname}/gestio/auxiliars/modifica-partit-politic/${row.id}"><button type="button" class="btn btn-warning btn-sm">Modifica</button></a>`,
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
      data-url="/api/auxiliars/delete/partitPolitic/${row.id}"
      data-reload-callback="${reloadKey}"
    >
      Elimina
    </button>`,
    });
  }

  renderTaulaCercadorFiltres<EspaiRow>({
    url: `https://${window.location.host}/api/auxiliars/get/partitsPolitics`,
    containerId: 'taulaLlistatPartits',
    columns,
    filterKeys: ['partit_politic'],
    //filterByField: 'provincia',
  });

  // Registra el callback con una clave única
  registerDeleteCallback(reloadKey, () => taulaPartits());

  // Inicia el listener una sola vez
  initDeleteHandlers();
}
