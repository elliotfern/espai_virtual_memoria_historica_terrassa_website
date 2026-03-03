import { API_URLS } from '../../../services/api/ApiUrls';
import { fetchDataGet } from '../../../services/fetchData/fetchDataGet';
import { renderTaulaCercadorFiltres } from '../../../services/renderTaula/renderTaulaCercadorFiltres';
import { initDeleteHandlers, registerDeleteCallback } from '../../../services/fetchData/handleDelete';
import { getIsAdmin } from '../../../services/auth/getIsAdmin';
import { formatDates } from '../../../services/formatDates/dates';

interface ApiResponse<T> {
  status: string;
  message: string;
  data: T;
}

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

const MONTHS_CA = ['Gener', 'Febrer', 'Març', 'Abril', 'Maig', 'Juny', 'Juliol', 'Agost', 'Setembre', 'Octubre', 'Novembre', 'Desembre'] as const;

function formatMonthCa(ym: string): string {
  // ym: YYYY-MM
  const [y, m] = ym.split('-');
  const mi = Number(m);
  if (!y || !mi || mi < 1 || mi > 12) return ym;
  return `${MONTHS_CA[mi - 1]} ${y}`;
}

function setActiveButton(container: HTMLElement, activeMonth?: string) {
  const buttons = container.querySelectorAll<HTMLButtonElement>('button[data-month]');
  buttons.forEach((b) => {
    const m = b.dataset.month || '';
    const isActive = activeMonth ? m === activeMonth : m === '';
    b.classList.toggle('btn-primary', isActive);
    b.classList.toggle('btn-outline-primary', !isActive);
  });
}

export async function taulaRegistreHorariAdmin() {
  const isAdmin = await getIsAdmin();
  if (!isAdmin) return;

  const reloadKey = 'reload-taula-taulaRegistreHorariAdmin';
  const filtrosDiv = document.getElementById('filtresMesosRegistreHorari') as HTMLDivElement | null;
  if (!filtrosDiv) return;

  const columns: Column<RegistreRow>[] = [
    {
      header: 'Dia',
      field: 'dia',
      render: (_: unknown, row: RegistreRow) => {
        const label = formatDates(row.dia);
        return `${label}`;
      },
    },

    {
      header: 'Usuari',
      field: 'userNom',
      render: (_: unknown, row: RegistreRow) => {
        const label = row.userNom ?? `(ID ${row.userId})`;
        return `<a href="https://${window.location.hostname}/gestio/registre-horari/usuari/${row.userId}">${label}</a>`;
      },
    },
    { header: 'Email', field: 'userEmail' },
    { header: 'Hores', field: 'hores' },
    { header: 'Tipus', field: 'tipusNom' },
    { header: 'Descripció', field: 'descripcio' },
    {
      header: 'Accions',
      field: 'id',
      render: (_: unknown, row: RegistreRow) =>
        `<a title="Modifica" href="https://${window.location.hostname}/gestio/registre-horari/modifica-registre/${row.id}">
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

  const renderTable = (month?: string) => {
    const url = API_URLS.GET.HORES_LLISTAT_ADMIN(month); // month opcional
    renderTaulaCercadorFiltres<RegistreRow>({
      url,
      containerId: 'taulaRegistreHorari',
      columns,
      filterKeys: ['dia', 'userNom', 'userEmail', 'tipusNom', 'descripcio'],
    });

    registerDeleteCallback(reloadKey, () => renderTable(month));
    initDeleteHandlers();

    setActiveButton(filtrosDiv, month);
  };

  // 1) Cargar meses disponibles
  const resp = await fetchDataGet<ApiResponse<string[]>>(API_URLS.GET.HORES_MESES_DISPONIBLES_ADMIN, true);
  const months = resp?.data ?? [];

  // 2) Pintar botones
  filtrosDiv.innerHTML = `
    <div class="d-flex flex-wrap gap-2">
      <button type="button" class="btn btn-primary" data-month="">Tots els mesos</button>
      ${months.map((ym) => `<button type="button" class="btn btn-outline-primary" data-month="${ym}">${formatMonthCa(ym)}</button>`).join('')}
    </div>
  `;

  // 3) Click handlers (delegación)
  filtrosDiv.addEventListener('click', (e) => {
    const target = e.target as HTMLElement | null;
    const btn = target?.closest('button[data-month]') as HTMLButtonElement | null;
    if (!btn) return;

    const m = btn.dataset.month ?? '';
    renderTable(m || undefined);
  });

  // 4) Render inicial
  renderTable(); // por defecto => tots els mesos
}
