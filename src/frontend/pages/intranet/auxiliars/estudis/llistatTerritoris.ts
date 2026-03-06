// src/frontend/pages/gestio/estudis/taules/taulaTerritoris.ts
import { renderTaulaCercadorFiltres } from '../../../../services/renderTaula/renderTaulaCercadorFiltres';
import { initDeleteHandlers, registerDeleteCallback } from '../../../../services/fetchData/handleDelete';
import { getIsAdmin } from '../../../../services/auth/getIsAdmin';
import { getIsAutor } from '../../../../services/auth/getIsAutor';

interface TerritoriRow {
  id: number;
  sort_order: number;
  nom: string;
}

type Column<T> = {
  header: string;
  field: keyof T;
  render?: (value: T[keyof T], row: T) => string;
};

export async function taulaTerritoris(): Promise<void> {
  const isAdmin = await getIsAdmin();
  const isAutor = await getIsAutor();
  const reloadKey = 'reload-taula-territoris';

  const columns: Column<TerritoriRow>[] = [
    {
      header: 'Territori',
      field: 'nom',
      render: (_: unknown, row: TerritoriRow) => {
        return `<a href="https://${window.location.hostname}/gestio/auxiliars/estudis/territoris-traduccions/${row.id}">
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
      render: (_: unknown, row: TerritoriRow) =>
        `<a title="Modifica" href="https://${window.location.hostname}/gestio/auxiliars/estudis/modifica-territori/${row.id}">
          <button type="button" class="btn btn-warning btn-sm">Modifica</button>
        </a>`,
    });
  }

  if (isAdmin) {
    columns.push({
      header: '',
      field: 'id',
      render: (_: unknown, row: TerritoriRow) => `
        <button
          type="button"
          class="btn btn-danger btn-sm delete-button"
          data-id="${row.id}"
          data-url="/api/estudis/delete/territori/${row.id}"
          data-reload-callback="${reloadKey}"
        >
          Elimina
        </button>`,
    });
  }

  renderTaulaCercadorFiltres<TerritoriRow>({
    url: `https://${window.location.host}/api/estudis/get/territoris`,
    containerId: 'taulaTerritoris',
    columns,
    filterKeys: ['nom'],
  });

  registerDeleteCallback(reloadKey, () => taulaTerritoris());
  initDeleteHandlers();
}
