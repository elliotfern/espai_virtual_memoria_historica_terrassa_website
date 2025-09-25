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
  return 'pendents'; // por defecto 1 u otros/indefinido
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
  firstLevelField: keyof T & string; // campo de botones 1er nivel (p.ej. 'categoria_button_label')

  /** Campo del estado (por defecto 'completat'). Se usa con el mapa estándar 1/2/3. */
  statusField?: keyof T;

  /** (Opcional) Mapeo propio si NO quieres usar el estándar del helper. */
  mapRowToKey?: (row: T) => NonTotsStatus;

  /** (Opcional) Etiquetas personalizadas */
  labels?: Partial<Record<StatusKey, string>>;

  initialKey?: StatusKey;
  initialFirstLevelValue?: T[keyof T] | null;
  initialSearch?: string;
  initialPage?: number;
  secondLevelTitle?: string; // texto encima de los botones

  /** Dedupe cuando el 1er nivel está en "Tots" */
  dedupeBy?: (row: T) => PropertyKey; // clave para deduplicar (ej. r => r.id)
  dedupeWhenFirstLevelAll?: boolean; // por defecto true
}): Promise<void> {
  let currentKey: StatusKey = opts.initialKey ?? 'tots';
  let currentFirstLevel: T[keyof T] | null = opts.initialFirstLevelValue ?? null;
  let currentSearch = opts.initialSearch ?? '';
  let currentPage = opts.initialPage ?? 1;

  const { containerId, firstLevelField, columns, filterKeys = [], data, statusField = 'completat' as keyof T, mapRowToKey, labels = {}, secondLevelTitle = 'Estat de les fitxes:', dedupeBy, dedupeWhenFirstLevelAll = true } = opts;

  const labTots = labels.tots ?? 'Tots';
  const labCompletats = labels.completats ?? 'Completats (visibles al web)';
  const labRevisio = labels.revisio ?? 'Cal revisió';
  const labPendents = labels.pendents ?? 'Pendents';

  const mapFn = mapRowToKey ? mapRowToKey : (row: T) => defaultMapRowToKey(row, statusField);

  const renderNow = async (): Promise<void> => {
    // 1) Aplica 2º nivel (estado)
    let filtered: ReadonlyArray<T>;
    if (currentKey === 'tots') {
      filtered = data;
    } else {
      const key = currentKey as NonTotsStatus;
      filtered = data.filter((row) => mapFn(row) === key);
    }

    // 2) Calcula las categorías disponibles tras el filtro de estado
    const availableCats = new Set<unknown>();
    for (const r of filtered) {
      const rec = r as unknown as Record<PropertyKey, unknown>;
      availableCats.add(rec[firstLevelField]);
    }

    // 3) Decide el valor inicial del 1er nivel para este render:
    //    - Si teníamos categoría activa y sigue disponible → mantenla
    //    - Si no está disponible → pásala como null para no forzar un valor inexistente
    const initialFirstLevelValue = currentFirstLevel != null && availableCats.has(currentFirstLevel) ? currentFirstLevel : null;

    // 4) Si el 1er nivel está en "Tots", deduplicamos por la clave indicada
    const isFirstLevelAll = initialFirstLevelValue === null || initialFirstLevelValue === undefined;

    if (dedupeWhenFirstLevelAll && isFirstLevelAll && typeof dedupeBy === 'function') {
      filtered = dedupeByKey(filtered, dedupeBy);
    }

    // 5) Render de la tabla
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
      filterByField: firstLevelField,
      initialFilterValue: initialFirstLevelValue, // 👈 valor coherente con lo disponible
      initialSearch: currentSearch,
      initialPage: currentPage,
    });

    URL.revokeObjectURL(blobUrl);

    // 6) Persistimos el estado devuelto por el renderer
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
      btn.className = `btn ${def.bsClass} me-2 mb-2`; // separación horizontal/vertical
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
