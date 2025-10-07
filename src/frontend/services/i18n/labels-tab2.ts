// labels-tab2.ts
import { makeDict } from './i18n';

type Tab2Keys = 'maritalStatus' | 'familyList';

export const LABELS_TAB2 = makeDict<Tab2Keys>({
  ca: {
    maritalStatus: 'Estat civil',
    familyList: 'Relació de familiars',
  },
  es: {
    maritalStatus: 'Estado civil',
    familyList: 'Relación de familiares',
  },
  en: {
    maritalStatus: 'Marital status',
    familyList: 'List of relatives',
  },
  fr: {
    maritalStatus: 'État civil',
    familyList: 'Liste des membres de la famille',
  },
  it: {
    maritalStatus: 'Stato civile',
    familyList: 'Elenco dei familiari',
  },
  pt: {
    maritalStatus: 'Estado civil',
    familyList: 'Lista de familiares',
  },
});
