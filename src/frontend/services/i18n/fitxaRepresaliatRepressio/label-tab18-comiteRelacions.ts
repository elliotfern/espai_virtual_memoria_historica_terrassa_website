import { makeDict } from '../../../services/i18n/i18n';

type RelacionsKeys = 'text';

export const LABELS_COMITE_RELACIONS = makeDict<RelacionsKeys>({
  ca: {
    text: 'Represaliat per haver donat suport als exiliats a través del Comitè de Relacions de Solidaritat.',
  },
  es: {
    text: 'Represaliado por haber apoyado a los exiliados a través del Comité de Relaciones de Solidaridad.',
  },
  en: {
    text: 'Repressed for having supported the exiles through the Committee of Solidarity Relations.',
  },
  fr: {
    text: 'Réprimé pour avoir soutenu les exilés par le biais du Comité des Relations de Solidarité.',
  },
  it: {
    text: 'Repressa per aver sostenuto gli esiliati attraverso il Comitato delle Relazioni di Solidarietà.',
  },
  pt: {
    text: 'Reprimido por ter apoiado os exilados através do Comitê de Relações de Solidariedade.',
  },
});
