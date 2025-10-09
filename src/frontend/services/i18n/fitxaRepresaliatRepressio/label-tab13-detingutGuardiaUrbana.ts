import { makeDict } from '../i18n';

// Etiquetas del bloque
type GUKeys = 'heading' | 'arrestDate' | 'releaseDate' | 'arrestReason' | 'detentionOrderedBy' | 'orderingInstitutionType' | 'orderedByTOP' | 'observations';

export const LABELS_DETINGUT_GU = makeDict<GUKeys>({
  ca: {
    heading: 'Detingut Guàrdia Urbana / Empresonat al Dipòsit municipal Sant Llàtzer, registre núm. {n}',
    arrestDate: 'Data de detenció:',
    releaseDate: 'Data de sortida:',
    arrestReason: 'Motiu de la detenció:',
    detentionOrderedBy: "Responsable d'ordenar la detenció:",
    orderingInstitutionType: "Tipus d'institució que ordena la detenció:",
    orderedByTOP: 'Detenció ordenada pel Tribunal de Orden Público?:',
    observations: 'Observacions:',
  },
  es: {
    heading: 'Detenido Guardia Urbana / Encarcelado en el Depósito municipal Sant Llàtzer, registro núm. {n}',
    arrestDate: 'Fecha de detención:',
    releaseDate: 'Fecha de salida:',
    arrestReason: 'Motivo de la detención:',
    detentionOrderedBy: 'Responsable de ordenar la detención:',
    orderingInstitutionType: 'Tipo de institución que ordena la detención:',
    orderedByTOP: '¿Detención ordenada por el Tribunal de Orden Público?:',
    observations: 'Observaciones:',
  },
  en: {
    heading: 'Detained by Urban Guard / Imprisoned at Sant Llàtzer Municipal Depot, record no. {n}',
    arrestDate: 'Date of detention:',
    releaseDate: 'Release date:',
    arrestReason: 'Reason for detention:',
    detentionOrderedBy: 'Person ordering the detention:',
    orderingInstitutionType: 'Type of ordering institution:',
    orderedByTOP: 'Detention ordered by the Public Order Court (TOP)?:',
    observations: 'Observations:',
  },
  fr: {
    heading: 'Arrêté par la Garde urbaine / Emprisonné au Dépôt municipal Sant Llàtzer, enregistrement n° {n}',
    arrestDate: "Date de l'arrestation :",
    releaseDate: 'Date de sortie :',
    arrestReason: "Motif de l'arrestation :",
    detentionOrderedBy: "Responsable ayant ordonné l'arrestation :",
    orderingInstitutionType: "Type d'institution ordonnant l'arrestation :",
    orderedByTOP: "Arrestation ordonnée par le Tribunal de l'Ordre Public (TOP) ? :",
    observations: 'Observations :',
  },
  it: {
    heading: 'Arrestato dalla Guardia Urbana / Incarcerato al Deposito municipale Sant Llàtzer, registro n. {n}',
    arrestDate: "Data dell'arresto:",
    releaseDate: 'Data di uscita:',
    arrestReason: "Motivo dell'arresto:",
    detentionOrderedBy: "Responsabile che ordina l'arresto:",
    orderingInstitutionType: "Tipo di istituzione che ordina l'arresto:",
    orderedByTOP: "Arresto ordinato dal Tribunale dell'Ordine Pubblico (TOP)?:",
    observations: 'Osservazioni:',
  },
  pt: {
    heading: 'Detido pela Guarda Urbana / Encarcerado no Depósito municipal Sant Llàtzer, registo n.º {n}',
    arrestDate: 'Data da detenção:',
    releaseDate: 'Data de saída:',
    arrestReason: 'Motivo da detenção:',
    detentionOrderedBy: 'Responsável por ordenar a detenção:',
    orderingInstitutionType: 'Tipo de instituição que ordena a detenção:',
    orderedByTOP: 'Detenção ordenada pelo Tribunal de Ordem Pública (TOP)?:',
    observations: 'Observações:',
  },
});

// Sí/No/Sense dades (reutiliza tu tri)
type TriKey = 'yes' | 'no' | 'noData';
export const LABELS_TRI = makeDict<TriKey>({
  ca: { yes: 'Sí', no: 'No', noData: 'Sense dades' },
  es: { yes: 'Sí', no: 'No', noData: 'Sin datos' },
  en: { yes: 'Yes', no: 'No', noData: 'No data' },
  fr: { yes: 'Oui', no: 'Non', noData: 'Aucune donnée' },
  it: { yes: 'Sì', no: 'No', noData: 'Senza dati' },
  pt: { yes: 'Sim', no: 'Não', noData: 'Sem dados' },
});
