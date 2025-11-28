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
  nom: string;
  nom_represaliat: string;
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

    {
      header: 'Telèfon',
      field: 'telefon',
      render: (_: unknown, row: EspaiRow) => {
        const value = row.telefon;

        if (!value) {
          // Si és null, undefined o cadena buida → mostra guió o res
          return '<span class="text-muted">—</span>';
          // o simplement: return '';
        }

        return String(value);
      },
    },

    { header: 'Data enviament', field: 'dataEnviament' },

    { header: 'Represaliat', field: 'nom_represaliat' },

    {
      header: 'Usuari que respon',
      field: 'nom',
      render: (_: unknown, row: EspaiRow) => {
        const value = row.nom;

        if (!value) {
          // Si és null, undefined o cadena buida → mostra guió o res
          return '<span class="text-muted">—</span>';
          // o simplement: return '';
        }

        return String(value);
      },
    },

    {
      header: 'Estat missatge',
      field: 'estat', // mejor que 'id', ya que usamos estat
      render: (_: unknown, row: EspaiRow) => {
        // Por si la API devuelve "1", "2", "3" como string:
        const estat = typeof row.estat === 'string' ? parseInt(row.estat, 10) : row.estat;

        let label = '';
        let btnClass = '';

        switch (estat) {
          case 1:
            label = 'Pendent resposta';
            btnClass = 'btn-warning';
            break;
          case 2:
            label = 'Resposta enviada';
            btnClass = 'btn-success';
            break;
          case 3:
            label = 'Sense resposta';
            btnClass = 'btn-secondary';
            break;
          case 4:
            label = 'Conversa tancada';
            btnClass = 'btn-dark';
            break;
          default:
            label = 'Desconegut';
            btnClass = 'btn-outline-secondary';
            break;
        }

        return `<button type="button" class="btn btn-sm ${btnClass}">${label}</button>`;
      },
    },

    {
      header: 'Veure missatge',
      field: 'id',
      render: (_: unknown, row: EspaiRow) => `<a id="${row.id}" title="Veure missatges" href="https://${window.location.hostname}/gestio/missatges/veure-missatge/${row.id}"><button type="button" class="btn btn-success btn-sm">Missatge</button></a>`,
    },

    {
      header: 'Respondre missatge',
      field: 'id',
      render: (_: unknown, row: EspaiRow) => `<a id="${row.id}" title="Respondre" href="https://${window.location.hostname}/gestio/missatges/respondre-missatge/${row.id}"><button type="button" class="btn btn-warning btn-sm">Respondre</button></a>`,
    },
  ];

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
