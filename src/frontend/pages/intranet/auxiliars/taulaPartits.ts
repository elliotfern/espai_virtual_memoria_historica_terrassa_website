import { renderTaulaCercadorFiltres } from '../../../services/renderTaula/renderTaulaCercadorFiltres';
import { getIsAdmin } from '../../../services/auth/getIsAdmin';

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

  const columns: Column<EspaiRow>[] = [
    { header: 'Partit polític', field: 'partit_politic' },
    { header: 'Sigla', field: 'sigles' },
  ];

  if (isAdmin) {
    columns.push({
      header: 'Accions',
      field: 'id',
      render: (_: unknown, row: EspaiRow) => `<a id="${row.id}" title="Modifica" href="https://${window.location.hostname}/gestio/partit/modifica/${row.id}"><button type="button" class="btn btn-primary">Modifica</button></a>`,
    });
  }

  renderTaulaCercadorFiltres<EspaiRow>({
    url: `https://${window.location.host}/api/auxiliars/get/partits`,
    containerId: 'taulaLlistatPartits',
    columns,
    filterKeys: ['partit_politic'],
    //filterByField: 'provincia',
  });
}
