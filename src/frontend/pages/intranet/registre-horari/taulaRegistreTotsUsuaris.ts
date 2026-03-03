import { API_URLS } from '../../../services/api/ApiUrls';
import { renderTaulaCercadorFiltres } from '../../../services/renderTaula/renderTaulaCercadorFiltres';
import { initDeleteHandlers, registerDeleteCallback } from '../../../services/fetchData/handleDelete';
import { getIsAdmin } from '../../../services/auth/getIsAdmin';

interface RegistreRow {
  id: number;
  userId: number;
  userNom: string | null;
  userEmail: string | null;
  dia: string;
  hores: number;
  tipusNom: string | null;
  descripcio: string | null;
}

type Column<T> = {
  header: string;
  field: keyof T;
  render?: (value: T[keyof T], row: T) => string;
};

export async function taulaRegistreHorariAdmin() {
  const isAdmin = await getIsAdmin();
  if (!isAdmin) return;

  const reloadKey = 'reload-taula-taulaRegistreHorariAdmin';

  const columns: Column<RegistreRow>[] = [
    { header: 'Dia', field: 'dia' },
    { header: 'Usuari', field: 'userNom' },
    { header: 'Email', field: 'userEmail' },
    { header: 'Hores', field: 'hores' },
    { header: 'Tipus', field: 'tipusNom' },
    { header: 'Descripció', field: 'descripcio' },
    {
      header: 'Accions',
      field: 'id',
      render: (_: unknown, row: RegistreRow) =>
        `<a id="${row.id}" title="Modifica" href="https://${window.location.hostname}/gestio/registre-horari/modifica-registre/${row.id}">
          <button type="button" class="btn btn-warning btn-sm">Modifica</button>
        </a>`,
    },
    {
      header: '',
      field: 'id',
      render: (_: unknown, row: RegistreRow) => `
        <button
          type="button"
          class="btn btn-danger btn-sm delete-button"
          data-id="${row.id}"
          data-url="/api/hores/delete/hores/${row.id}"
          data-reload-callback="${reloadKey}"
        >
          Elimina
        </button>`,
    },
  ];

  const monthInput = document.getElementById('filtreMes') as HTMLInputElement | null;

  const render = (month?: string) => {
    const url = API_URLS.GET.HORES_LLISTAT_ADMIN(month);

    renderTaulaCercadorFiltres<RegistreRow>({
      url,
      containerId: 'taulaRegistreHorari',
      columns,
      filterKeys: ['dia', 'userNom', 'userEmail', 'tipusNom', 'descripcio'],
    });

    registerDeleteCallback(reloadKey, () => render(monthInput?.value || undefined));
    initDeleteHandlers();
  };

  // Valor por defecto: mes actual
  if (monthInput && !monthInput.value) {
    const now = new Date();
    const mm = String(now.getMonth() + 1).padStart(2, '0');
    monthInput.value = `${now.getFullYear()}-${mm}`;
  }

  render(monthInput?.value || undefined);

  // Recarga al cambiar mes
  if (monthInput) {
    monthInput.addEventListener('change', () => {
      render(monthInput.value);
    });
  }
}
