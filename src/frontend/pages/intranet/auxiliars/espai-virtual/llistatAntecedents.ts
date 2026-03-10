import { renderTaulaCercadorFiltres } from '../../../../services/renderTaula/renderTaulaCercadorFiltres';
import { initDeleteHandlers, registerDeleteCallback } from '../../../../services/fetchData/handleDelete';
import { getIsAdmin } from '../../../../services/auth/getIsAdmin';
import { getIsAutor } from '../../../../services/auth/getIsAutor';

interface AntecedentRow {
  id: number;
  ordre: number;
  image_id: number | null;
  layout_image_left: number;
  show_in_timeline: number;
  any_text: string | null;
  titol: string | null;
}

type Column<T> = {
  header: string;
  field: keyof T;
  render?: (value: T[keyof T], row: T) => string;
};

export async function taulaAntecedents(): Promise<void> {
  const isAdmin = await getIsAdmin();
  const isAutor = await getIsAutor();
  const reloadKey = 'reload-taula-antecedents';

  const columns: Column<AntecedentRow>[] = [
    {
      header: 'Data',
      field: 'any_text',
      render: (_: unknown, row: AntecedentRow) => (row.any_text ? String(row.any_text) : ''),
    },
    {
      header: 'Títol',
      field: 'titol',
      render: (_: unknown, row: AntecedentRow) => {
        return `<a href="https://${window.location.hostname}/gestio/auxiliars/espai-virtual/fitxa-antecedent/${row.id}">
          ${row.titol ? row.titol : ''}
        </a>`;
      },
    },
    {
      header: 'Ordre',
      field: 'ordre',
      render: (_: unknown, row: AntecedentRow) => String(row.ordre),
    },
    {
      header: 'Imatge',
      field: 'image_id',
      render: (_: unknown, row: AntecedentRow) => (row.image_id ? 'Sí' : 'No'),
    },
    {
      header: 'Timeline',
      field: 'show_in_timeline',
      render: (_: unknown, row: AntecedentRow) => (Number(row.show_in_timeline) === 1 ? 'Sí' : 'No'),
    },
    {
      header: 'Imatge esquerra',
      field: 'layout_image_left',
      render: (_: unknown, row: AntecedentRow) => (Number(row.layout_image_left) === 1 ? 'Sí' : 'No'),
    },
  ];

  if (isAdmin || isAutor) {
    columns.push({
      header: 'Accions',
      field: 'id',
      render: (_: unknown, row: AntecedentRow) =>
        `<a title="Modifica" href="https://${window.location.hostname}/gestio/auxiliars/espai-virtual/modifica-antecedent/${row.id}">
          <button type="button" class="btn btn-warning btn-sm">Modifica</button>
        </a>`,
    });
  }

  if (isAdmin) {
    columns.push({
      header: '',
      field: 'id',
      render: (_: unknown, row: AntecedentRow) => `
        <button
          type="button"
          class="btn btn-danger btn-sm delete-button"
          data-id="${row.id}"
          data-url="/api/antecedents/delete/antecedent/${row.id}"
          data-reload-callback="${reloadKey}"
        >
          Elimina
        </button>`,
    });
  }

  renderTaulaCercadorFiltres<AntecedentRow>({
    url: `https://${window.location.host}/api/antecedents/get/antecedents`,
    containerId: 'taulaAntecedents',
    columns,
    filterKeys: ['any_text', 'titol'],
  });

  registerDeleteCallback(reloadKey, () => taulaAntecedents());
  initDeleteHandlers();
}
