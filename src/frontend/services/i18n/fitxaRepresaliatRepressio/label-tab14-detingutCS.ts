import { makeDict } from '../i18n';

type ComiteKeys = 'heading' | 'yearOfDetention' | 'arrestReason' | 'lawyer' | 'observations';

export const LABELS_DETINGUT_COMITE = makeDict<ComiteKeys>({
  ca: {
    heading: 'Detingut Comitè de Solidaritat (1971-1977), registre núm. {n}',
    yearOfDetention: 'Any de la detenció:',
    arrestReason: 'Motiu de la detenció:',
    lawyer: 'Advocat:',
    observations: 'Observacions:',
  },
  es: {
    heading: 'Detenido Comité de Solidaridad (1971-1977), registro núm. {n}',
    yearOfDetention: 'Año de la detención:',
    arrestReason: 'Motivo de la detención:',
    lawyer: 'Abogado:',
    observations: 'Observaciones:',
  },
  en: {
    heading: 'Detained by the Solidarity Committee (1971–1977), record no. {n}',
    yearOfDetention: 'Year of detention:',
    arrestReason: 'Reason for detention:',
    lawyer: 'Lawyer:',
    observations: 'Observations:',
  },
  fr: {
    heading: 'Arrêté par le Comité de Solidarité (1971–1977), enregistrement n° {n}',
    yearOfDetention: "Année de l'arrestation :",
    arrestReason: "Motif de l'arrestation :",
    lawyer: 'Avocat :',
    observations: 'Observations :',
  },
  it: {
    heading: 'Arrestato dal Comitato di Solidarietà (1971–1977), registro n. {n}',
    yearOfDetention: "Anno dell'arresto:",
    arrestReason: "Motivo dell'arresto:",
    lawyer: 'Avvocato:',
    observations: 'Osservazioni:',
  },
  pt: {
    heading: 'Detido pelo Comitê de Solidariedade (1971–1977), registo n.º {n}',
    yearOfDetention: 'Ano da detenção:',
    arrestReason: 'Motivo da detenção:',
    lawyer: 'Advogado:',
    observations: 'Observações:',
  },
});
