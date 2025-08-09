import { formatDatesFormDateTime } from '../../services/formatDates/dates';
import { renderTaulaCercadorFiltres } from '../../services/renderTaula/renderTaulaCercadorFiltres';
import { getIsAdmin } from '../../services/auth/getIsAdmin';
import { parseUserAgent } from '../../services/formatDates/parseUserAgent';

interface EspaiRow {
  id: number;
  operacio: string;
  detalls: string;
  taula_afectada: string;
  dataHora: string;
  ip_usuari: string;
  user_agent: string;
  nom: string;
}

type Column<T> = {
  header: string;
  field: keyof T;
  render?: (value: T[keyof T], row: T) => string;
};

export async function taulaEdicions(idRepressaliat: number) {
  const isAdmin = await getIsAdmin();

  const columns: Column<EspaiRow>[] = [
    { header: 'Usuari', field: 'nom' },

    { header: 'Operació', field: 'operacio' },

    { header: 'Detalls', field: 'detalls' },

    { header: 'Taula', field: 'taula_afectada' },

    {
      header: 'Data',
      field: 'dataHora',
      render: (_: unknown, row: EspaiRow) => `${formatDatesFormDateTime(row.dataHora)}`,
    },

    {
      header: 'Navegador',
      field: 'user_agent',
      render: (_: unknown, row: EspaiRow) => {
        const { browser, os } = parseUserAgent(row.user_agent);
        return `${browser} - ${os}`;
      },
    },
  ];

  if (isAdmin) {
    columns.push({ header: 'IP', field: 'ip_usuari' });
  }

  // LIMPIAR CONTENEDOR ANTES DE RENDERIZAR NUEVAMENTE
  const container = document.getElementById('quadreFamiliars');
  if (container) {
    container.innerHTML = '';
  }

  renderTaulaCercadorFiltres<EspaiRow>({
    url: `https://${window.location.host}/api/dades_personals/get/?type=registreEdicions&id=${idRepressaliat}`,
    containerId: 'quadreEdicions',
    columns,
    filterKeys: ['operacio'],
  });
}
