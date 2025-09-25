// services/ui/estat.ts

export type CompletatCode = 1 | 2 | 3;

type EstatConfig = { label: string; btnClass: string };
const ESTAT_CONFIG: Record<CompletatCode, EstatConfig> = {
  1: { label: 'PENDENT', btnClass: 'btn-primary' },
  2: { label: 'COMPLETADA', btnClass: 'btn-success' },
  3: { label: 'CAL REVISIÓ', btnClass: 'btn-danger' },
};

export function normalizeCompletat(v: unknown): CompletatCode | null {
  if (typeof v === 'number') {
    if (v === 1 || v === 2 || v === 3) return v;
    return null;
  }
  const s = String(v ?? '').trim();
  if (s === '1' || /^pendent/i.test(s)) return 1;
  if (s === '2' || /^completat/i.test(s)) return 2;
  if (s === '3' || /^revisi/i.test(s)) return 3;
  return null;
}

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

/**
 * Devuelve el HTML del botón de estado (Bootstrap 5) según `completat`.
 * - size: 'sm' | 'md' | 'lg'  (por defecto 'sm')
 * - attrs: atributos extra (data-*, title, disabled, ...)
 */
export function estatButtonHTML(completat: unknown, opts?: { size?: Size; attrs?: Attrs }): string {
  const code = normalizeCompletat(completat);
  if (code === null) {
    // valor desconocido → botón gris neutro
    return `<button type="button" class="btn btn-sm btn-outline-secondary" disabled>—</button>`;
  }

  const { label, btnClass } = ESTAT_CONFIG[code];
  const sizeClass = (opts?.size ?? 'sm') === 'sm' ? 'btn-sm' : opts?.size === 'lg' ? 'btn-lg' : '';

  const extra = attrsToString(opts?.attrs);
  return `<button type="button" class="btn ${sizeClass} ${btnClass}"${extra}>${label}</button>`;
}
