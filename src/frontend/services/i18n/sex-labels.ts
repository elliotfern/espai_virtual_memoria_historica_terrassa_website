// sex-labels.ts
import { makeDict, t } from './i18n';

type SexKey = '1' | '2' | 'unknown';

export const SEX_LABELS = makeDict<SexKey>({
  ca: { '1': 'Home', '2': 'Dona', unknown: 'desconegut' },
  es: { '1': 'Hombre', '2': 'Mujer', unknown: 'desconocido' },
  en: { '1': 'Male', '2': 'Female', unknown: 'unknown' },
  fr: { '1': 'Homme', '2': 'Femme', unknown: 'inconnu' },
  it: { '1': 'Uomo', '2': 'Donna', unknown: 'sconosciuto' },
  pt: { '1': 'Homem', '2': 'Mulher', unknown: 'desconhecido' },
});

function toSexKey(v: unknown): SexKey {
  if (v === 1 || v === '1') return '1';
  if (v === 2 || v === '2') return '2';
  return 'unknown';
}

export function getSexeText(sexe: unknown, lang: string): string {
  return t(SEX_LABELS, toSexKey(sexe), lang);
}
