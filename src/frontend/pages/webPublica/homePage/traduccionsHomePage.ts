import { HomeKeys, LABELS_HOME } from '../../../services/i18n/homepage';
import { DEFAULT_LANG, isLang, t } from '../../../services/i18n/i18n';

// Lista tipada de claves
const HOME_KEYS = Object.keys(LABELS_HOME.ca) as HomeKeys[];

function isHomeKey(s: string | null): s is HomeKeys {
  return !!s && (HOME_KEYS as readonly string[]).includes(s);
}

export function traduccionsHomePagei18n(lang: string): void {
  const l = isLang(lang) ? lang : DEFAULT_LANG;

  // textContent
  document.querySelectorAll<HTMLElement>('[data-i18n]').forEach((el) => {
    const key = el.getAttribute('data-i18n');
    if (isHomeKey(key)) el.textContent = t(LABELS_HOME, key, l);
  });

  // innerHTML (cuando hay <br> o spans)
  document.querySelectorAll<HTMLElement>('[data-i18n-html]').forEach((el) => {
    const key = el.getAttribute('data-i18n-html');
    if (isHomeKey(key)) el.innerHTML = t(LABELS_HOME, key, l);
  });

  // placeholders
  document.querySelectorAll<HTMLInputElement>('[data-i18n-ph]').forEach((el) => {
    const key = el.getAttribute('data-i18n-ph');
    if (isHomeKey(key)) el.placeholder = t(LABELS_HOME, key, l);
  });
}
