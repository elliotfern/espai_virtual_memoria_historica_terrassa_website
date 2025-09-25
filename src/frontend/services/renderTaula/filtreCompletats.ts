import { renderTaulaCercadorFiltres } from './renderTaulaCercadorFiltres';

type Column<T> = {
  header: string;
  field: keyof T;
  render?: (value: T[keyof T], row: T) => string;
};

type StatusKey = 'tots' | 'completats' | 'revisio' | 'pendents';
type NonTotsStatus = Exclude<StatusKey, 'tots'>;

function toBlobUrl(payload: unknown): string {
  const blob = new Blob([JSON.stringify(payload)], { type: 'application/json' });
  return URL.createObjectURL(blob);
}

function defaultMapRowToKey<T extends object>(row: T, statusField: keyof T): NonTotsStatus {
  // Mapa estándar global: 1→pendents, 2→completats, 3→revisio
  const v = row[statusField];
  const n = typeof v === 'number' ? v : Number.parseInt(String(v ?? '').trim(), 10);
  if (n === 2) return 'completats';
  if (n === 3) return 'revisio';
  return 'pendents';
}

function dedupeByKey<T>(arr: ReadonlyArray<T>, keyFn: (row: T) => PropertyKey): T[] {
  const seen = new Set<PropertyKey>();
  const out: T[] = [];
  for (const r of arr) {
    const k = keyFn(r);
    if (!seen.has(k)) {
      seen.add(k);
      out.push(r);
    }
  }
  return out;
}

export async function renderWithSecondLevelFilters<T extends object>(opts: {
  containerId: string;
  data: ReadonlyArray<T>; // dataset completo (ya explotado si procede)
  columns: ReadonlyArray<Column<T>>;
  filterKeys?: ReadonlyArray<keyof T>; // búsqueda textual del renderer

  /** Campo que representa la categoría ya "lista para mostrar" (ej. 'categoria_button_label') */
  firstLevelField: keyof T & string;

  /** Orden opcional de categorías (si no se pasa, orden alfabético local) */
  categoryOrder?: (a: T[keyof T], b: T[keyof T]) => number;

  /** Campo del estado (por defecto 'completat'). Se usa con el mapa estándar 1/2/3. */
  statusField?: keyof T;

  /** (Opcional) Mapeo propio si NO quieres usar el estándar del helper. */
  mapRowToKey?: (row: T) => NonTotsStatus;

  /** (Opcional) Etiquetas personalizadas */
  labels?: Partial<Record<StatusKey, string>>;

  /** Títulos opcionales encima de los grupos de botones */
  firstLevelTitle?: string;
  secondLevelTitle?: string;

  /** Estado inicial */
  initialFirstLevelValue?: T[keyof T] | null; // categoría activa (null = Tots)
  initialKey?: StatusKey; // estado activo (por defecto 'tots')
  initialSearch?: string;
  initialPage?: number;

  /** Dedupe cuando la categoría (1er nivel) está en "Tots" */
  dedupeBy?: (row: T) => PropertyKey; // clave para deduplicar (ej. r => r.id)
  dedupeWhenFirstLevelAll?: boolean; // por defecto true
}): Promise<void> {
  let currentKey: StatusKey = opts.initialKey ?? 'tots';
  let currentCategory: T[keyof T] | null = opts.initialFirstLevelValue ?? null; // null = Tots
  let currentSearch = opts.initialSearch ?? '';
  let currentPage = opts.initialPage ?? 1;

  const { containerId, data, columns, filterKeys = [], firstLevelField, categoryOrder, statusField = 'completat' as keyof T, mapRowToKey, labels = {}, firstLevelTitle = 'Categories:', secondLevelTitle = 'Estat de les fitxes:', dedupeBy, dedupeWhenFirstLevelAll = true } = opts;

  // 1) Conjunto FIJO de categorías (de TODO el dataset)
  const allCategoriesRaw = Array.from(new Set(data.map((r) => (r as unknown as Record<PropertyKey, unknown>)[firstLevelField]).filter((v) => v !== undefined && v !== null && String(v).trim() !== ''))) as Array<T[keyof T]>;

  // Orden
  const allCategories = typeof categoryOrder === 'function' ? [...allCategoriesRaw].sort(categoryOrder) : [...allCategoriesRaw].sort((a, b) => String(a).localeCompare(String(b), 'ca', { sensitivity: 'base' }));

  const labTots = labels.tots ?? 'Tots';
  const labCompletats = labels.completats ?? 'Completats (visibles al web)';
  const labRevisio = labels.revisio ?? 'Cal revisió';
  const labPendents = labels.pendents ?? 'Pendents';

  const mapFn = mapRowToKey ? mapRowToKey : (row: T) => defaultMapRowToKey(row, statusField);

  // DOM helpers
  const getContainer = () => document.getElementById(containerId);
  const q = <E extends Element>(sel: string) => getContainer()?.querySelector<E>(sel) ?? null;

  async function renderNow(): Promise<void> {
    // 2) Filtrado por estado (2º nivel)
    let filtered = currentKey === 'tots' ? data : data.filter((row) => mapFn(row) === (currentKey as NonTotsStatus));

    // 3) Filtrado por categoría (1er nivel) sobre el dataset ya filtrado por estado
    if (currentCategory !== null) {
      filtered = filtered.filter((row) => {
        const rec = row as unknown as Record<PropertyKey, unknown>;
        return rec[firstLevelField] === currentCategory;
      });
    } else if (dedupeWhenFirstLevelAll && typeof dedupeBy === 'function') {
      // Si categoría = Tots, deduplicamos (opción C)
      filtered = dedupeByKey(filtered, dedupeBy);
    }

    // 4) Render tabla (sin botones de 1er nivel del renderer)
    const blobUrl = toBlobUrl({
      status: 'success' as const,
      message: 'OK',
      errors: [] as const,
      data: filtered,
    });

    const result = await renderTaulaCercadorFiltres<T>({
      url: blobUrl,
      containerId,
      columns: [...columns],
      filterKeys: filterKeys as (keyof T)[],
      // ⚠️ No pasamos filterByField: los botones de 1er nivel los controlamos nosotros
      initialSearch: currentSearch,
      initialPage: currentPage,
      showSearch: true,
      showPagination: true,
    });

    URL.revokeObjectURL(blobUrl);

    // 5) Persistir estado de búsqueda/paginación
    currentSearch = result.search ?? '';
    currentPage = result.page ?? 1;

    // 6) Pintar/repintar los dos niveles de botones fijos
    renderFirstLevelButtons();
    renderSecondLevelButtons();

    // 7) Mensaje de “sin resultados” (manteniendo los botones)
    renderNoResultsMessage(filtered.length === 0);
  }

  function renderFirstLevelButtons(): void {
    const container = getContainer();
    if (!container) return;

    // Ancla: el propio input de búsqueda
    const searchInput = container.querySelector<HTMLInputElement>('input[type="text"]');

    // Limpia anterior
    q<HTMLDivElement>('.fixed-first-level-buttons')?.remove();

    const wrap = document.createElement('div');
    wrap.className = 'fixed-first-level-buttons d-flex flex-wrap align-items-center gap-2 my-2';

    if (firstLevelTitle) {
      const title = document.createElement('div');
      title.textContent = firstLevelTitle;
      title.className = 'text-muted small me-2';
      wrap.appendChild(title);
    }

    // Botón Tots
    wrap.appendChild(makeCategoryBtn(null, labTots));

    // Resto de categorías (todas, fijas)
    for (const cat of allCategories) {
      wrap.appendChild(makeCategoryBtn(cat, String(cat)));
    }

    // ✅ Insertar DENTRO del container, justo después del input de búsqueda
    if (searchInput && searchInput.parentElement === container) {
      container.insertBefore(wrap, searchInput.nextSibling);
    } else {
      // Fallback: al principio del container
      container.insertBefore(wrap, container.firstChild);
    }
  }

  function makeCategoryBtn(value: T[keyof T] | null, label: string): HTMLButtonElement {
    const btn = document.createElement('button');
    btn.type = 'button';
    btn.className = 'btn btn-outline-secondary me-2 mb-2 filter-btn';
    btn.textContent = label;
    const isActive = (value === null && currentCategory === null) || (value !== null && currentCategory === value);
    if (isActive) btn.classList.add('active');

    btn.addEventListener('click', () => {
      currentCategory = value; // puede ser null = Tots
      currentPage = 1; // resetea página al cambiar filtro
      void renderNow();
    });

    return btn;
  }

  function renderSecondLevelButtons(): void {
    const container = getContainer();
    if (!container) return;

    // Ancla: debajo del 1er nivel
    const firstLevel = q<HTMLDivElement>('.fixed-first-level-buttons') ?? container;

    q<HTMLDivElement>('.second-level-filter-buttons')?.remove();

    const wrap = document.createElement('div');
    wrap.className = 'second-level-filter-buttons d-flex flex-wrap align-items-center gap-2 my-2';

    if (secondLevelTitle) {
      const title = document.createElement('div');
      title.textContent = secondLevelTitle;
      title.className = 'text-muted small me-2';
      wrap.appendChild(title);
    }

    type BtnDef = { key: StatusKey; label: string; bsClass: string };
    const btns: BtnDef[] = [
      { key: 'tots', label: labTots, bsClass: 'btn-secondary' },
      { key: 'completats', label: labCompletats, bsClass: 'btn-success' },
      { key: 'revisio', label: labRevisio, bsClass: 'btn-danger' },
      { key: 'pendents', label: labPendents, bsClass: 'btn-primary' },
    ];

    for (const def of btns) {
      const btn = document.createElement('button');
      btn.type = 'button';
      btn.className = `btn btn-sm ${def.bsClass} me-2 mb-2`;
      btn.textContent = def.label;
      if (def.key === currentKey) btn.classList.add('active');
      btn.addEventListener('click', () => {
        currentKey = def.key;
        currentPage = 1; // resetea página al cambiar filtro
        void renderNow();
      });
      wrap.appendChild(btn);
    }

    // ✅ Insertar DENTRO del container, justo después del bloque de 1er nivel
    if (firstLevel && firstLevel.parentElement === container) {
      container.insertBefore(wrap, firstLevel.nextSibling);
    } else {
      container.appendChild(wrap);
    }
  }

  function renderNoResultsMessage(show: boolean): void {
    const container = getContainer();
    if (!container) return;

    // Quita mensaje previo
    q<HTMLDivElement>('.no-results-message')?.remove();

    if (!show) return;

    const msg = document.createElement('div');
    msg.className = 'no-results-message alert alert-info my-2';
    msg.textContent = 'No hi ha resultats a mostrar';

    // Insertar debajo del segundo nivel, dentro del container
    const after = q<HTMLDivElement>('.second-level-filter-buttons') ?? q<HTMLDivElement>('.fixed-first-level-buttons');

    if (after && after.parentElement === container) {
      container.insertBefore(msg, after.nextSibling);
    } else {
      container.appendChild(msg);
    }
  }

  await renderNow();
}
