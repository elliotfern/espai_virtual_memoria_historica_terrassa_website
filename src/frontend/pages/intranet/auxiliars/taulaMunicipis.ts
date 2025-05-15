import { renderTaulaCercadorFiltres } from '../../../services/renderTaula/renderTaulaCercadorFiltres';
import { getIsAdmin } from '../../../services/auth/getIsAdmin';

interface EspaiRow {
  id: number;
  ciutat: string;
  comarca: string;
  provincia: string;
  comunitat: string;
  estat: string;
}

type Column<T> = {
  header: string;
  field: keyof T;
  render?: (value: T[keyof T], row: T) => string;
};

export async function taulaMunicipis() {
  const isAdmin = await getIsAdmin();

  const columns: Column<EspaiRow>[] = [
    { header: 'Municipi', field: 'ciutat' },
    { header: 'Comarca', field: 'comarca' },
    { header: 'Provincia', field: 'provincia' },
    { header: 'Comunitat autonòma', field: 'comunitat' },
    { header: 'Estat', field: 'estat' },
  ];

  if (isAdmin) {
    columns.push({
      header: 'Accions',
      field: 'id',
      render: (_: unknown, row: EspaiRow) => `<a id="${row.id}" title="Modifica" href="https://${window.location.hostname}/gestio/municipi/modifica/${row.id}"><button type="button" class="btn btn-primary">Modifica</button></a>`,
    });
  }

  renderTaulaCercadorFiltres<EspaiRow>({
    url: `https://${window.location.host}/api/auxiliars/get/municipis`,
    containerId: 'taulaLlistatMunicipis',
    columns,
    filterKeys: ['ciutat'],
    filterByField: 'provincia',
  });
}
