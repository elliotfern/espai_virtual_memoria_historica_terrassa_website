import { renderTaulaCercadorFiltres } from '../../../services/renderTaula/renderTaulaCercadorFiltres';
import { getIsAdmin } from '../../../services/auth/getIsAdmin';

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

  const columns: Column<EspaiRow>[] = [
    { header: 'Sindicat', field: 'sindicat' },
    { header: 'Sigla', field: 'sigles' },
  ];

  if (isAdmin) {
    columns.push({
      header: 'Accions',
      field: 'id',
      render: (_: unknown, row: EspaiRow) => `<a id="${row.id}" title="Modifica" href="https://${window.location.hostname}/gestio/municipi/sindicat/${row.id}"><button type="button" class="btn btn-primary">Modifica</button></a>`,
    });
  }

  renderTaulaCercadorFiltres<EspaiRow>({
    url: `https://${window.location.host}/api/auxiliars/get/sindicats`,
    containerId: 'taulaLlistatSindicats',
    columns,
    filterKeys: ['sindicat'],
    //filterByField: 'provincia',
  });
}
