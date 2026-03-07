import { renderTaulaCercadorFiltres } from '../../../../services/renderTaula/renderTaulaCercadorFiltres';
import { initDeleteHandlers, registerDeleteCallback } from '../../../../services/fetchData/handleDelete';
import { getIsAdmin } from '../../../../services/auth/getIsAdmin';
import { getIsAutor } from '../../../../services/auth/getIsAutor';

interface EstudiRow {
  id: number;
  slug: string;
  any_publicacio: number | null;
  titol: string;
  periode: string;
  territori: string;
  tipus: string;
  autors: string | null;
}

type Column<T> = {
  header: string;
  field: keyof T;
  render?: (value: T[keyof T], row: T) => string;
};

export async function taulaEstudis(): Promise<void> {
  const isAdmin = await getIsAdmin();
  const isAutor = await getIsAutor();
  const reloadKey = 'reload-taula-estudis';

  const columns: Column<EstudiRow>[] = [
    {
      header: 'Títol',
      field: 'titol',
      render: (_: unknown, row: EstudiRow) => {
        return `<a href="https://${window.location.hostname}/gestio/auxiliars/estudis/modifica-estudi/${row.id}">
          ${row.titol}
        </a>`;
      },
    },
    {
      header: 'Autor/s',
      field: 'autors',
      render: (_: unknown, row: EstudiRow) => (row.autors ? String(row.autors) : ''),
    },
    {
      header: 'Any',
      field: 'any_publicacio',
      render: (_: unknown, row: EstudiRow) => (row.any_publicacio ? String(row.any_publicacio) : ''),
    },
    {
      header: 'Període',
      field: 'periode',
    },
    {
      header: 'Territori',
      field: 'territori',
    },
    {
      header: 'Tipus',
      field: 'tipus',
    },
  ];

  if (isAdmin || isAutor) {
    columns.push({
      header: 'Accions',
      field: 'id',
      render: (_: unknown, row: EstudiRow) =>
        `<a title="Modifica" href="https://${window.location.hostname}/gestio/auxiliars/estudis/modifica-estudi/${row.id}">
          <button type="button" class="btn btn-warning btn-sm">Modifica</button>
        </a>`,
    });
  }

  if (isAdmin) {
    columns.push({
      header: '',
      field: 'id',
      render: (_: unknown, row: EstudiRow) => `
        <button
          type="button"
          class="btn btn-danger btn-sm delete-button"
          data-id="${row.id}"
          data-url="/api/estudis/delete/estudi/${row.id}"
          data-reload-callback="${reloadKey}"
        >
          Elimina
        </button>`,
    });
  }

  renderTaulaCercadorFiltres<EstudiRow>({
    url: `https://${window.location.host}/api/estudis/get/estudis`,
    containerId: 'taulaEstudis',
    columns,
    filterKeys: ['titol', 'autors', 'periode', 'territori', 'tipus'],
  });

  registerDeleteCallback(reloadKey, () => taulaEstudis());
  initDeleteHandlers();
}
