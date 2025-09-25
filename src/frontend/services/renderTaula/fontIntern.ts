// services/ui/fontIntern.ts
export type FontInternCode = 0 | 1 | 2 | 3;

const FONT_INTERN_MAP: Record<FontInternCode, string> = {
  0: 'Manel/Juan Antonio/José Luís',
  1: 'Llistat Represaliats Ajuntament',
  2: 'Manel: base dades antifranquista',
  3: 'Arxiu',
} as const;

function escapeHtml(s: string): string {
  return s.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;').replace(/'/g, '&#39;');
}

/** Normaliza `font_intern` desde number|string a 0|1|2|3, o null si no encaja. */
export function normalizeFontIntern(v: unknown): FontInternCode | null {
  if (typeof v === 'number') {
    return v === 0 || v === 1 || v === 2 || v === 3 ? v : null;
  }
  const s = String(v ?? '').trim();
  if (s === '') return null;
  const n = Number(s);
  return Number.isInteger(n) && (n === 0 || n === 1 || n === 2 || n === 3) ? (n as FontInternCode) : null;
}

/** Devuelve la etiqueta plana (texto) o '—' si no hay valor válido. */
export function fontInternLabel(v: unknown): string {
  const code = normalizeFontIntern(v);
  return code === null ? '—' : FONT_INTERN_MAP[code];
}

/** Devuelve la etiqueta lista para innerHTML (escapada). */
export function fontInternTextHTML(v: unknown): string {
  return escapeHtml(fontInternLabel(v));
}
