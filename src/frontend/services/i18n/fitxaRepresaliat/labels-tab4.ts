// labels-tab4.ts
import { makeDict } from '../i18n';

type Tab4Keys =
  | 'preWarHeading' // h5
  | 'politicalAffiliation' // label
  | 'unionAffiliation' // label
  | 'warDictatorshipHeading'; // h5

export const LABELS_TAB4 = makeDict<Tab4Keys>({
  ca: {
    preWarHeading: "Activitat política i sindical abans de l'esclat de la guerra:",
    politicalAffiliation: 'Afiliació política',
    unionAffiliation: 'Afiliació sindical',
    warDictatorshipHeading: 'Activitat política i sindical durant la guerra civil i la dictadura:',
  },
  es: {
    preWarHeading: 'Actividad política y sindical antes del estallido de la guerra:',
    politicalAffiliation: 'Afiliación política',
    unionAffiliation: 'Afiliación sindical',
    warDictatorshipHeading: 'Actividad política y sindical durante la guerra civil y la dictadura:',
  },
  en: {
    preWarHeading: 'Political and union activity before the outbreak of the war:',
    politicalAffiliation: 'Political affiliation',
    unionAffiliation: 'Union affiliation',
    warDictatorshipHeading: 'Political and union activity during the civil war and the dictatorship:',
  },
  fr: {
    preWarHeading: 'Activité politique et syndicale avant le déclenchement de la guerre :',
    politicalAffiliation: 'Affiliation politique',
    unionAffiliation: 'Affiliation syndicale',
    warDictatorshipHeading: 'Activité politique et syndicale pendant la guerre civile et la dictature :',
  },
  it: {
    preWarHeading: 'Attività politica e sindacale prima dello scoppio della guerra:',
    politicalAffiliation: 'Affiliazione politica',
    unionAffiliation: 'Affiliazione sindacale',
    warDictatorshipHeading: 'Attività politica e sindacale durante la guerra civile e la dittatura:',
  },
  pt: {
    preWarHeading: 'Atividade política e sindical antes do início da guerra:',
    politicalAffiliation: 'Afiliação política',
    unionAffiliation: 'Afiliação sindical',
    warDictatorshipHeading: 'Atividade política e sindical durante a guerra civil e a ditadura:',
  },
});
