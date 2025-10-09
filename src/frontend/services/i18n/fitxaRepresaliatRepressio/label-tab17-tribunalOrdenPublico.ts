import { makeDict } from '../i18n';

type TOPKeys = 'caseNumber' | 'sentenceDate' | 'sentence' | 'prisonPlace';

export const LABELS_TOP = makeDict<TOPKeys>({
  ca: {
    caseNumber: 'Número de la causa:',
    sentenceDate: 'Data sentència:',
    sentence: 'Sentència:',
    prisonPlace: "Lloc d'empresonament:",
  },
  es: {
    caseNumber: 'Número de la causa:',
    sentenceDate: 'Fecha de la sentencia:',
    sentence: 'Sentencia:',
    prisonPlace: 'Lugar de encarcelamiento:',
  },
  en: {
    caseNumber: 'Case number:',
    sentenceDate: 'Sentence date:',
    sentence: 'Sentence:',
    prisonPlace: 'Place of imprisonment:',
  },
  fr: {
    caseNumber: "Numéro de l'affaire :",
    sentenceDate: 'Date du jugement :',
    sentence: 'Jugement :',
    prisonPlace: "Lieu d'emprisonnement :",
  },
  it: {
    caseNumber: 'Numero della causa:',
    sentenceDate: 'Data della sentenza:',
    sentence: 'Sentenza:',
    prisonPlace: 'Luogo di detenzione:',
  },
  pt: {
    caseNumber: 'Número do processo:',
    sentenceDate: 'Data da sentença:',
    sentence: 'Sentença:',
    prisonPlace: 'Local de encarceramento:',
  },
});
