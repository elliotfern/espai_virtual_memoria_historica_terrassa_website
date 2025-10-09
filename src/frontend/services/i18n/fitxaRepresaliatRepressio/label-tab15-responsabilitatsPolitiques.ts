import { makeDict } from '../i18n';

type LRPKeys = 'prisonPlace' | 'exileCountry' | 'sentenceTitle' | 'observations';

export const LABELS_LRP = makeDict<LRPKeys>({
  ca: {
    prisonPlace: "Lloc d'empresonament:",
    exileCountry: 'País exili:',
    sentenceTitle: 'Condemna Expedient de Responsabilitats Polítiques:',
    observations: 'Observacions:',
  },
  es: {
    prisonPlace: 'Lugar de encarcelamiento:',
    exileCountry: 'País de exilio:',
    sentenceTitle: 'Condena Expediente de Responsabilidades Políticas:',
    observations: 'Observaciones:',
  },
  en: {
    prisonPlace: 'Place of imprisonment:',
    exileCountry: 'Country of exile:',
    sentenceTitle: 'Sentence in the Political Responsibilities file:',
    observations: 'Observations:',
  },
  fr: {
    prisonPlace: "Lieu d'emprisonnement :",
    exileCountry: "Pays d'exil :",
    sentenceTitle: 'Condamnation – Dossier de Responsabilités Politiques :',
    observations: 'Observations :',
  },
  it: {
    prisonPlace: 'Luogo di detenzione:',
    exileCountry: "Paese d'esilio:",
    sentenceTitle: 'Condanna nel fascicolo di Responsabilità Politiche:',
    observations: 'Osservazioni:',
  },
  pt: {
    prisonPlace: 'Local de encarceramento:',
    exileCountry: 'País de exílio:',
    sentenceTitle: 'Condenação no Processo de Responsabilidades Políticas:',
    observations: 'Observações:',
  },
});
