import { DEFAULT_LANG, isLang, t } from '../i18n/i18n';
import { LABELS_VTD, VTDKey } from '../i18n/valor-desconegut';

const VTD_CODE_TO_KEY: Record<number, VTDKey> = {
  1: 'noData',
  2: 'unknownM',
  3: 'empty',
  4: 'unknownDate',
  5: 'unknownF',
  6: 'noImprisonment',
  7: 'noExile',
};

export function valorTextDesconegut(valor: string | null | undefined, perDefecte: number = 1, lang: string = DEFAULT_LANG): string {
  // si hay valor no vacío, devuélvelo tal cual
  if (typeof valor === 'string' && valor.trim() !== '') return valor;

  const key = VTD_CODE_TO_KEY[perDefecte] ?? 'noData';
  const l = isLang(lang) ? lang : DEFAULT_LANG;
  return t(LABELS_VTD, key, l);
}
