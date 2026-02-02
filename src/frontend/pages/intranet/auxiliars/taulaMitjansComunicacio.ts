import { renderTaulaCercadorFiltres } from '../../../services/renderTaula/renderTaulaCercadorFiltres';
import { initDeleteHandlers, registerDeleteCallback } from '../../../services/fetchData/handleDelete';
import { getIsAdmin } from '../../../services/auth/getIsAdmin';
import { API_URLS } from '../../../services/api/ApiUrls';

interface EspaiRow {
  id: number;
  tipus: string;
  comarca: string;
  provincia: string;
  descripcio: string;
  preso: string;
  nom: string;
  tipus_preso_ca: string;
}

type Column<T> = {
  header: string;
  field: keyof T;
  render?: (value: T[keyof T], row: T) => string;
};

export async function taulaMitjansComunicacio() {
  const isAdmin = await getIsAdmin();
  const reloadKey = 'reload-taula-taulaLlistatMitjans';

  const columns: Column<EspaiRow>[] = [
    { header: 'Nom mitjà', field: 'nom' },

    { header: 'Tipus', field: 'tipus' },

    { header: 'Descripció', field: 'descripcio' },
  ];

  if (isAdmin) {
    columns.push({
      header: 'Detalls',
      field: 'id',
      render: (_: unknown, row: EspaiRow) => `<a id="${row.id}" title="Detalls" href="https://${window.location.hostname}/gestio/auxiliars/fitxa-mitja-comunicacio/${row.id}"><button type="button" class="btn btn-warning btn-sm">Detalls</button></a>`,
    });

    columns.push({
      header: 'Accions',
      field: 'id',
      render: (_: unknown, row: EspaiRow) => `<a id="${row.id}" title="Modifica" href="https://${window.location.hostname}/gestio/auxiliars/modifica-mitja-comunicacio/${row.id}"><button type="button" class="btn btn-warning btn-sm">Modifica</button></a>`,
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
      data-url="/api/auxiliars/delete/mitjaComunicacio/${row.id}"
      data-reload-callback="${reloadKey}"
    >
      Elimina
    </button>`,
    });
  }

  renderTaulaCercadorFiltres<EspaiRow>({
    url: API_URLS.GET.LLISTAT_MITJANS,
    containerId: 'taulaLlistatMitjans',
    columns,
    filterKeys: ['nom'],
    filterByField: 'tipus',
  });

  // Registra el callback con una clave única
  registerDeleteCallback(reloadKey, () => taulaMitjansComunicacio());

  // Inicia el listener una sola vez
  initDeleteHandlers();
}
