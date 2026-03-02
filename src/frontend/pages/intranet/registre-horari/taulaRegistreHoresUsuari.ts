import { API_URLS } from '../../../services/api/ApiUrls';
import { renderTaulaCercadorFiltres } from '../../../services/renderTaula/renderTaulaCercadorFiltres';
import { initDeleteHandlers, registerDeleteCallback } from '../../../services/fetchData/handleDelete';
import { getIsAdmin } from '../../../services/auth/getIsAdmin';

interface HoresRow {
  id: number;
  dia: string; // YYYY-MM-DD
  hores: number;
  tipusNom: string | null;
  descripcio: string | null;
}

type Column<T> = {
  header: string;
  field: keyof T;
  render?: (value: T[keyof T], row: T) => string;
};

export async function taulaHoresMe() {
  const isAdmin = await getIsAdmin();
  const reloadKey = 'reload-taula-taulaHoresMe';

  const columns: Column<HoresRow>[] = [
    { header: 'Dia', field: 'dia' },
    { header: 'Hores', field: 'hores' },
    { header: 'Tipus', field: 'tipusNom' },
    { header: 'Descripció', field: 'descripcio' },
    {
      header: 'Accions',
      field: 'id',
      render: (_: unknown, row: HoresRow) =>
        `<a id="${row.id}" title="Modifica" href="https://${window.location.hostname}/gestio/registre-horari/modifica-registre/${row.id}">
          <button type="button" class="btn btn-warning btn-sm">Modifica</button>
        </a>`,
    },
  ];

  if (isAdmin) {
    columns.push({
      header: '',
      field: 'id',
      render: (_: unknown, row: HoresRow) => `
        <button
          type="button"
          class="btn btn-danger btn-sm delete-button"
          data-id="${row.id}"
          data-url="/api/hores/delete/hores/${row.id}"
          data-reload-callback="${reloadKey}"
        >
          Elimina
        </button>`,
    });
  }

  renderTaulaCercadorFiltres<HoresRow>({
    url: API_URLS.GET.HORES_LLISTAT_ME,
    containerId: 'taulaHores',
    columns,
    filterKeys: ['dia', 'tipusNom', 'descripcio'],
  });

  registerDeleteCallback(reloadKey, () => taulaHoresMe());
  initDeleteHandlers();
}
