// src/frontend/pages/gestio/auxiliars/taules/taulaPeriodes.ts

import { getIsAdmin } from '../../../../services/auth/getIsAdmin';
import { getIsAutor } from '../../../../services/auth/getIsAutor';
import { initDeleteHandlers, registerDeleteCallback } from '../../../../services/fetchData/handleDelete';
import { renderTaulaCercadorFiltres } from '../../../../services/renderTaula/renderTaulaCercadorFiltres';

interface PeriodeRow {
  id: number;
  sort_order: number;
  nom: string; // nom en català (de moment) o el que retorni el GET
}

type Column<T> = {
  header: string;
  field: keyof T;
  render?: (value: T[keyof T], row: T) => string;
};

export async function taulaPeriodes(): Promise<void> {
  const isAdmin = await getIsAdmin();
  const isAutor = await getIsAutor();
  const reloadKey = 'reload-taula-periodes';

  const columns: Column<PeriodeRow>[] = [
    {
      header: 'Període',
      field: 'nom',
      render: (_: unknown, row: PeriodeRow) => {
        // Link para gestionar traducciones del período
        // Ajusta la ruta si tu vista es diferente
        return `<a href="https://${window.location.hostname}/gestio/auxiliars/estudis/periodes-traduccions/${row.id}">
          ${row.nom}
        </a>`;
      },
    },
    {
      header: 'Ordre',
      field: 'sort_order',
    },
  ];

  // Botón Modifica (admin o autor)
  if (isAdmin || isAutor) {
    columns.push({
      header: 'Accions',
      field: 'id',
      render: (_: unknown, row: PeriodeRow) =>
        `<a title="Modifica" href="https://${window.location.hostname}/gestio/auxiliars/estudis/modifica-periode/${row.id}">
          <button type="button" class="btn btn-warning btn-sm">Modifica</button>
        </a>`,
    });
  }

  // Botón Elimina (solo admin)
  if (isAdmin) {
    columns.push({
      header: '',
      field: 'id',
      render: (_: unknown, row: PeriodeRow) => `
        <button 
          type="button"
          class="btn btn-danger btn-sm delete-button"
          data-id="${row.id}" 
          data-url="/api/estudis/delete/periode/${row.id}"
          data-reload-callback="${reloadKey}"
        >
          Elimina
        </button>`,
    });
  }

  renderTaulaCercadorFiltres<PeriodeRow>({
    // Endpoint GET (lo haremos después)
    url: `https://${window.location.host}/api/estudis/get/periodes`,
    containerId: 'taulaPeriodes',
    columns,

    // Si luego quieres buscador por nombre, esto va perfecto
    filterKeys: ['nom'],
  });

  registerDeleteCallback(reloadKey, () => taulaPeriodes());
  initDeleteHandlers();
}
