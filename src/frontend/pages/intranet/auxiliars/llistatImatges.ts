import { renderTaulaCercadorFiltres } from '../../../services/renderTaula/renderTaulaCercadorFiltres';
import { initDeleteHandlers, registerDeleteCallback } from '../../../services/fetchData/handleDelete';
import { getIsAdmin } from '../../../services/auth/getIsAdmin';
import { API_URLS } from '../../../services/api/ApiUrls';

interface ImatgeRow {
  id: number;
  idPersona: number | null;
  nomArxiu: string;
  nomImatge: string;
  tipus: number;
  mime: string | null;
  dateCreated: string; // YYYY-MM-DD
  dateModified: string | null; // YYYY-MM-DD | null
}

type Column<T> = {
  header: string;
  field: keyof T;
  render?: (value: T[keyof T], row: T) => string;
};

export async function taulaImatges() {
  const isAdmin = await getIsAdmin();
  const reloadKey = 'reload-taula-taulaImatges';

  const columns: Column<ImatgeRow>[] = [
    { header: 'ID', field: 'id' },

    { header: 'Nom', field: 'nomImatge' },

    { header: 'Arxiu', field: 'nomArxiu' },

    {
      header: 'Tipus',
      field: 'tipus',
      render: (_: unknown, row) => {
        switch (row.tipus) {
          case 1:
            return `<span class="badge bg-secondary">Represaliat</span>`;
          case 2:
            return `<span class="badge bg-info text-dark">Usuari web</span>`;
          case 3:
            return `<span class="badge bg-primary">Galeria multimèdia</span>`;
          case 4:
            return `<span class="badge bg-warning text-dark">Premsa</span>`;
          default:
            return `<span class="badge bg-light text-dark">—</span>`;
        }
      },
    },

    {
      header: 'MIME',
      field: 'mime',
      render: (_: unknown, row: ImatgeRow) => (row.mime ? String(row.mime) : ''),
    },

    { header: 'Persona', field: 'idPersona', render: (_: unknown, row: ImatgeRow) => (row.idPersona ? String(row.idPersona) : '') },

    { header: 'Creat', field: 'dateCreated' },

    { header: 'Modificat', field: 'dateModified', render: (_: unknown, row: ImatgeRow) => (row.dateModified ? String(row.dateModified) : '') },
  ];

  if (isAdmin) {
    columns.push({
      header: 'Detalls',
      field: 'id',
      render: (_: unknown, row: ImatgeRow) =>
        `<a id="${row.id}" title="Detalls" href="https://${window.location.hostname}/gestio/auxiliars/fitxa-imatge/${row.id}">
          <button type="button" class="btn btn-warning btn-sm">Detalls</button>
        </a>`,
    });

    columns.push({
      header: 'Accions',
      field: 'id',
      render: (_: unknown, row: ImatgeRow) =>
        `<a id="${row.id}" title="Modifica" href="https://${window.location.hostname}/gestio/auxiliars/modifica-imatge/${row.id}">
          <button type="button" class="btn btn-warning btn-sm">Modifica</button>
        </a>`,
    });

    columns.push({
      header: '',
      field: 'id',
      render: (_: unknown, row: ImatgeRow) => `
        <button 
          type="button"
          class="btn btn-danger btn-sm delete-button"
          data-id="${row.id}" 
          data-url="/api/auxiliars/delete/imatge/${row.id}"
          data-reload-callback="${reloadKey}"
        >
          Elimina
        </button>`,
    });
  }

  renderTaulaCercadorFiltres<ImatgeRow>({
    url: API_URLS.GET.LLISTAT_IMATGES,
    containerId: 'taulaImatges',
    columns,
    filterKeys: ['nomImatge', 'nomArxiu'],
    filterByField: 'tipus',
  });

  registerDeleteCallback(reloadKey, () => taulaImatges());
  initDeleteHandlers();
}
