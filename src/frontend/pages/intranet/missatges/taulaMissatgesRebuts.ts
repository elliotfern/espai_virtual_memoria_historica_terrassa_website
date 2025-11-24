import { renderTaulaCercadorFiltres } from '../../../services/renderTaula/renderTaulaCercadorFiltres';
import { initDeleteHandlers, registerDeleteCallback } from '../../../services/fetchData/handleDelete';
import { getIsAdmin } from '../../../services/auth/getIsAdmin';

interface EspaiRow {
  id: number;
  nomCognoms: string;
  email: string;
  telefon: string;
  dataEnviament: string;
  estat: string;
}

type Column<T> = {
  header: string;
  field: keyof T;
  render?: (value: T[keyof T], row: T) => string;
};

export async function taulaMissatgesRebuts() {
  const isAdmin = await getIsAdmin();
  const reloadKey = 'reload-taula-estats';

  const columns: Column<EspaiRow>[] = [
    { header: 'Nom / Cognoms', field: 'nomCognoms' },
    { header: 'Email', field: 'email' },
    { header: 'Telèfon', field: 'telefon' },
    { header: 'Data enviament', field: 'dataEnviament' },
  ];

  columns.push({
    header: 'Veure missatge',
    field: 'id',
    render: (_: unknown, row: EspaiRow) => `<a id="${row.id}" title="Modifica" href="https://${window.location.hostname}/gestio/auxiliars/modifica-estat/${row.id}"><button type="button" class="btn btn-warning btn-sm">Modifica</button></a>`,
  });

  columns.push({
    header: 'Respondre missatge',
    field: 'id',
    render: (_: unknown, row: EspaiRow) => `<a id="${row.id}" title="Modifica" href="https://${window.location.hostname}/gestio/auxiliars/modifica-estat/${row.id}"><button type="button" class="btn btn-warning btn-sm">Modifica</button></a>`,
  });

  if (isAdmin) {
    columns.push({
      header: '',
      field: 'id',
      render: (_: unknown, row: EspaiRow) => `
    <button 
      type="button"
      class="btn btn-danger btn-sm delete-button"
      data-id="${row.id}" 
      data-url="/api/auxiliars/delete/estat/${row.id}"
      data-reload-callback="${reloadKey}"
    >
      Elimina
    </button>`,
    });
  }

  renderTaulaCercadorFiltres<EspaiRow>({
    url: `https://${window.location.host}/api/form_contacte/get/missatgesRebuts`,
    containerId: 'taulaLlistatMissatgesRebuts',
    columns,
    //filterKeys: ['comarca'],
    //filterByField: 'provincia',
  });

  // Registra el callback con una clave única
  registerDeleteCallback(reloadKey, () => taulaMissatgesRebuts());

  // Inicia el listener una sola vez
  initDeleteHandlers();
}
