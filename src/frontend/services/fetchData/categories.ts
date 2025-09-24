// services/fetchData/categories.ts

export function parseSet(raw: string | null | undefined): number[] {
  if (!raw) return [];
  return raw
    .replace(/[{}\s]/g, '')
    .split(',')
    .filter((s) => s.length > 0)
    .map((n) => parseInt(n, 10))
    .filter((n) => !Number.isNaN(n));
}

function isObject(v: unknown): v is Record<string, unknown> {
  return typeof v === 'object' && v !== null;
}

function normalizeToArray<T>(json: unknown): T[] {
  if (Array.isArray(json)) return json as T[];
  if (isObject(json) && 'data' in json) {
    const d = (json as { data: unknown }).data;
    if (Array.isArray(d)) return d as T[];
    if (isObject(d)) return [d as T];
  }
  return [];
}

type Augment<T, K extends string> = T & Record<K, string>;

/** Construye un labelById a partir de un diccionario `{ id, name }` */
export function buildLabelById(dict: ReadonlyArray<{ id: number; name: string }>): (id: number) => string {
  const map = new Map<number, string>();
  for (const d of dict) map.set(d.id, d.name);
  return (id: number) => map.get(id) ?? String(id);
}

/**
 * Descarga datos desde `url`, EXPLOTA cada fila por `setField` y añade el campo literal `targetField`.
 * Devuelve un blob:URL con `{ status, message, errors, data }`.
 */
export async function explodeSetToBlobUrl<T extends object, K extends string>(opts: { url: string; setField: keyof T; labelById: (id: number) => string; targetField: K; includeEmpty?: boolean }): Promise<string> {
  const { url, setField, labelById, targetField, includeEmpty = true } = opts;

  const res = await fetch(url);
  const json = (await res.json()) as unknown;

  const base = normalizeToArray<T>(json);
  const exploded: Array<Augment<T, K>> = [];

  for (const row of base) {
    // Acceso seguro sin `any`
    const rec = row as unknown as Record<PropertyKey, unknown>;
    const rawVal = rec[setField as PropertyKey];
    const raw = typeof rawVal === 'string' ? rawVal : rawVal == null ? '' : String(rawVal);

    const ids = parseSet(raw);

    if (ids.length === 0) {
      if (includeEmpty) {
        const rowOut = Object.assign({}, row, { [targetField]: '' } as Record<K, string>) as Augment<T, K>;
        exploded.push(rowOut);
      }
      continue;
    }

    for (const id of ids) {
      const label = labelById(id);
      const rowOut = Object.assign({}, row, { [targetField]: label } as Record<K, string>) as Augment<T, K>;
      exploded.push(rowOut);
    }
  }

  const payload = {
    status: 'success' as const,
    message: 'OK',
    errors: [] as const,
    data: exploded,
  };

  const blob = new Blob([JSON.stringify(payload)], { type: 'application/json' });
  return URL.createObjectURL(blob);
}
