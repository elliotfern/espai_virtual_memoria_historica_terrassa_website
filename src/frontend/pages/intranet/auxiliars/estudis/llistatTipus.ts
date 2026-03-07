import { renderTaulaCercadorFiltres } from '../../../../services/renderTaula/renderTaulaCercadorFiltres';
import { initDeleteHandlers, registerDeleteCallback } from '../../../../services/fetchData/handleDelete';
import { getIsAdmin } from '../../../../services/auth/getIsAdmin';
import { getIsAutor } from '../../../../services/auth/getIsAutor';

interface TipusRow {
  id: number;
  sort_order: number;
  nom: string;
}

type Column<T> = {
  header: string;
  field: keyof T;
  render?: (value: T[keyof T], row: T) => string;
};

export async function taulaTipus(): Promise<void> {
  const isAdmin = await getIsAdmin();
  const isAutor = await getIsAutor();
  const reloadKey = 'reload-taula-tipus';

  const columns: Column<TipusRow>[] = [
    {
      header: 'Tipus',
      field: 'nom',
      render: (_: unknown, row: TipusRow) => {
        return `<a href="https://${window.location.hostname}/gestio/auxiliars/estudis/tipus-traduccions/${row.id}">
          ${row.nom}
        </a>`;
      },
    },
    {
      header: 'Ordre',
      field: 'sort_order',
    },
  ];

  if (isAdmin || isAutor) {
    columns.push({
      header: 'Accions',
      field: 'id',
      render: (_: unknown, row: TipusRow) =>
        `<a title="Modifica" href="https://${window.location.hostname}/gestio/auxiliars/estudis/modifica-tipus/${row.id}">
          <button type="button" class="btn btn-warning btn-sm">Modifica</button>
        </a>`,
    });
  }

  if (isAdmin) {
    columns.push({
      header: '',
      field: 'id',
      render: (_: unknown, row: TipusRow) => `
        <button
          type="button"
          class="btn btn-danger btn-sm delete-button"
          data-id="${row.id}"
          data-url="/api/estudis/delete/tipus/${row.id}"
          data-reload-callback="${reloadKey}"
        >
          Elimina
        </button>`,
    });
  }

  renderTaulaCercadorFiltres<TipusRow>({
    url: `https://${window.location.host}/api/estudis/get/tipus`,
    containerId: 'taulaTipus',
    columns,
    filterKeys: ['nom'],
  });

  registerDeleteCallback(reloadKey, () => taulaTipus());
  initDeleteHandlers();
}
