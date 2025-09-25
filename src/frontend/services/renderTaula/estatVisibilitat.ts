// services/ui/visibilitat.ts
type Size = 'sm' | 'md' | 'lg';
type Attrs = Record<string, string | number | boolean | undefined>;

function attrsToString(attrs?: Attrs): string {
  if (!attrs) return '';
  const parts: string[] = [];
  for (const [k, v] of Object.entries(attrs)) {
    if (v === false || v === undefined || v === null) continue;
    if (v === true) {
      parts.push(k);
      continue;
    }
    const escaped = String(v).replace(/"/g, '&quot;');
    parts.push(`${k}="${escaped}"`);
  }
  return parts.length ? ' ' + parts.join(' ') : '';
}

/** Normaliza visibilitat desde number|string a number, o null si no encaja. */
export function normalizeVisibilitat(v: unknown): number | null {
  if (typeof v === 'number' && Number.isFinite(v)) return v;
  const s = String(v ?? '').trim();
  if (s === '') return null;
  const n = Number(s);
  return Number.isFinite(n) ? n : null;
}

/**
 * Devuelve un botón (Bootstrap 5) según la visibilitat:
 * - 2  -> VISIBLE  (btn-success)
 * - !2 -> NO VISIBLE (btn-primary)
 */
export function visibilitatButtonHTML(visibilitat: unknown, opts?: { size?: Size; attrs?: Attrs }): string {
  const code = normalizeVisibilitat(visibilitat);
  const sizeClass = (opts?.size ?? 'sm') === 'sm' ? 'btn-sm' : opts?.size === 'lg' ? 'btn-lg' : '';

  if (code === 2) {
    const extra = attrsToString(opts?.attrs);
    return `<button type="button" class="btn ${sizeClass} btn-success"${extra}>VISIBLE</button>`;
  }

  const extra = attrsToString(opts?.attrs);
  return `<button type="button" class="btn ${sizeClass} btn-primary"${extra}>NO VISIBLE</button>`;
}

/** Helper por conveniencia: muestra el botón solo si se cumple la condición */
export function maybeVisibilitatButtonHTML(visibilitat: unknown, opts?: { size?: Size; attrs?: Attrs }): string {
  return visibilitatButtonHTML(visibilitat, opts);
}
