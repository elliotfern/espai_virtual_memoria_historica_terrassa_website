// labels-tab5.ts
import { makeDict } from '../i18n';

type Tab5Keys = 'bioWarnCaMissingEsAvailable' | 'bioUnavailable';

export const LABELS_TAB5 = makeDict<Tab5Keys>({
  ca: {
    bioWarnCaMissingEsAvailable: 'La biografia en català no està disponible, però hi ha disponible la versió en castellà.',
    bioUnavailable: 'La biografia no està disponible.',
  },
  es: {
    bioWarnCaMissingEsAvailable: 'La biografía en catalán no está disponible, pero está disponible la versión en castellano.',
    bioUnavailable: 'La biografía no está disponible.',
  },
  en: {
    bioWarnCaMissingEsAvailable: 'The biography in Catalan is not available, but a Spanish version is available.',
    bioUnavailable: 'The biography is not available.',
  },
  fr: {
    bioWarnCaMissingEsAvailable: 'La biographie en catalan n’est pas disponible, mais une version en espagnol est disponible.',
    bioUnavailable: 'La biographie n’est pas disponible.',
  },
  it: {
    bioWarnCaMissingEsAvailable: 'La biografia in catalano non è disponibile, ma è disponibile una versione in spagnolo.',
    bioUnavailable: 'La biografia non è disponibile.',
  },
  pt: {
    bioWarnCaMissingEsAvailable: 'A biografia em catalão não está disponível, mas existe uma versão em espanhol.',
    bioUnavailable: 'A biografia não está disponível.',
  },
});
