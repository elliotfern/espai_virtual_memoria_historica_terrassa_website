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
  sub_sector_cat: string;
}

type Column<T> = {
  header: string;
  field: keyof T;
  render?: (value: T[keyof T], row: T) => string;
};

export async function taulaSubsectorsEconomics() {
  const isAdmin = await getIsAdmin();
  const isAutor = await getIsAutor();
  const reloadKey = 'reload-taula-taulaLlistatSubsectorsEconomics';

  const columns: Column<EspaiRow>[] = [
    { header: 'Sub-sector econòmic', field: 'sub_sector_cat' },
    { header: 'Sector econòmic', field: 'sector_cat' },
  ];

  if (isAdmin || isAutor) {
    columns.push({
      header: 'Accions',
      field: 'id',
      render: (_: unknown, row: EspaiRow) => `<a id="${row.id}" title="Modifica" href="https://${window.location.hostname}/gestio/auxiliars/modifica-sub-sector-economic/${row.id}"><button type="button" class="btn btn-warning btn-sm">Modifica</button></a>`,
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
          data-url="/api/auxiliars/delete/carrecEmpresa/${row.id}"
          data-reload-callback="${reloadKey}"
        >
          Elimina
        </button>`,
    });
  }

  renderTaulaCercadorFiltres<EspaiRow>({
    url: API_URLS.GET.SUB_SECTORS_ECONOMICS,
    containerId: 'taulaLlistatSubsectorsEconomics',
    columns,
    filterKeys: ['sub_sector_cat'],
    filterByField: 'sector_cat',
  });

  // Registra el callback con una clave única
  registerDeleteCallback(reloadKey, () => taulaSubsectorsEconomics());

  // Inicia el listener una sola vez
  initDeleteHandlers();
}
