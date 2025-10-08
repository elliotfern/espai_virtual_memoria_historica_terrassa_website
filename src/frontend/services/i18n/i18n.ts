// i18n.ts

// ————————————————————————————————————————————
// Idiomas soportados
export const LANGS = ['ca', 'es', 'en', 'fr', 'it', 'pt'] as const;
export type Lang = (typeof LANGS)[number];
export const DEFAULT_LANG: Lang = 'ca';

// Guard
export function isLang(x: string): x is Lang {
  return (LANGS as readonly string[]).includes(x);
}

// Diccionario genérico
export type I18nDict<TKeys extends string> = Record<Lang, Record<TKeys, string>>;

// t(): lookup con fallback a DEFAULT_LANG y aviso si falta clave
export function t<TKeys extends string>(dict: I18nDict<TKeys>, key: TKeys, lang: string): string {
  const l: Lang = isLang(lang) ? (lang as Lang) : DEFAULT_LANG;
  const val = dict[l]?.[key] ?? dict[DEFAULT_LANG]?.[key];
  if (val == null) {
    // Opcional: en dev puedes lanzar o hacer console.warn
    // console.warn(`[i18n] Missing key "${key}" for lang "${l}"`);
    return key; // fallback final: muestra la clave
  }
  return val;
}

// Helper para asegurar que el diccionario tiene todas las claves/idiomas
export function makeDict<TKeys extends string>(d: I18nDict<TKeys>) {
  return d;
}

export function fmt(s: string, params: Record<string, string | number>) {
  return s.replace(/\{(\w+)\}/g, (_, k) => String(params[k] ?? ''));
}
