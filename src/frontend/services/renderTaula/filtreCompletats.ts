// services/renderTaula/filtreCompletats.ts
import { renderTaulaCercadorFiltres } from './renderTaulaCercadorFiltres';

type Column<T> = {
  header: string;
  field: keyof T;
  render?: (value: T[keyof T], row: T) => string;
};

type SecondKey<K extends string> = 'tots' | K;

function toBlobUrl(payload: unknown): string {
  const blob = new Blob([JSON.stringify(payload)], { type: 'application/json' });
  return URL.createObjectURL(blob);
}

export async function renderWithSecondLevelFilters<T extends object, K extends string>(opts: {
  containerId: string;
  data: ReadonlyArray<T>; // dataset completo (ya explotado si procede)
  columns: ReadonlyArray<Column<T>>;
  filterKeys?: ReadonlyArray<keyof T>; // búsqueda textual del renderer
  firstLevelField: keyof T & string; // campo usado por el renderer para los botones de 1er nivel
  buttons: ReadonlyArray<{ key: SecondKey<K>; label: string }>;
  mapRowToKey: (row: T) => SecondKey<K>; // cómo mapear cada fila a una clave de 2º nivel
  initialKey?: SecondKey<K>;
  initialFirstLevelValue?: T[keyof T] | null;
  initialSearch?: string;
  initialPage?: number;
  secondLevelTitle?: string; // texto encima de los botones (opcional)
}): Promise<void> {
  let currentKey: SecondKey<K> = opts.initialKey ?? 'tots';
  let currentFirstLevel: T[keyof T] | null = opts.initialFirstLevelValue ?? null;
  let currentSearch = opts.initialSearch ?? '';
  let currentPage = opts.initialPage ?? 1;

  const { containerId, firstLevelField, columns } = opts;

  const renderNow = async (): Promise<void> => {
    const filtered = currentKey === 'tots' ? opts.data : opts.data.filter((row) => opts.mapRowToKey(row) === currentKey);

    const blobUrl = toBlobUrl({
      status: 'success' as const,
      message: 'OK',
      errors: [] as const,
      data: filtered,
    });

    const result = await renderTaulaCercadorFiltres<T>({
      url: blobUrl,
      containerId: containerId,
      columns: [...columns],
      filterKeys: (opts.filterKeys ?? []) as (keyof T)[],
      filterByField: firstLevelField,
      initialFilterValue: currentFirstLevel,
      initialSearch: currentSearch,
      initialPage: currentPage,
    });

    URL.revokeObjectURL(blobUrl);

    currentFirstLevel = (result.filter as T[keyof T] | null) ?? null;
    currentSearch = result.search ?? '';
    currentPage = result.page ?? 1;

    insertSecondLevelButtons();
  };

  const insertSecondLevelButtons = (): void => {
    const container = document.getElementById(containerId);
    if (!container) return;

    const firstRow = container.querySelector<HTMLDivElement>('.filter-buttons');
    const anchor = firstRow ?? container;

    const old = container.querySelector<HTMLDivElement>('.second-level-filter-buttons');
    if (old && old.parentElement) old.parentElement.removeChild(old);

    const wrap = document.createElement('div');
    wrap.className = 'second-level-filter-buttons';
    wrap.style.margin = '8px 0 12px';

    if (opts.secondLevelTitle) {
      const title = document.createElement('div');
      title.textContent = opts.secondLevelTitle;
      title.style.fontSize = '12px';
      title.style.opacity = '0.8';
      title.style.marginBottom = '6px';
      wrap.appendChild(title);
    }

    for (const def of opts.buttons) {
      const btn = document.createElement('button');
      btn.type = 'button';
      btn.className = 'filter-btn second-filter-btn';
      btn.textContent = def.label;
      if (def.key === currentKey) btn.classList.add('active');
      btn.addEventListener('click', () => {
        currentKey = def.key;
        void renderNow();
      });
      wrap.appendChild(btn);
    }

    if (anchor.nextSibling) {
      anchor.parentElement?.insertBefore(wrap, anchor.nextSibling);
    } else {
      anchor.parentElement?.appendChild(wrap);
    }
  };

  await renderNow();
}
