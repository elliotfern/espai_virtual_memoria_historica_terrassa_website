import { makeDict } from '../i18n';

type PresoModelKeys =
  | 'heading' // con placeholder {n}
  | 's1_title'
  | 'prisonDate'
  | 'prisonMode'
  | 'freedom'
  | 'freedomDate'
  | 's2_title'
  | 'transfers'
  | 'transferPlace'
  | 'transferDate'
  | 's3_title'
  | 'vicissitudes'
  | 'observations';

export const LABELS_PRESO_MODEL = makeDict<PresoModelKeys>({
  ca: {
    heading: 'Empresonament Presó Model, registre núm. {n}',
    s1_title: "1) Dades de l'empresonament:",
    prisonDate: "Data d'empresonament:",
    prisonMode: 'Modalitat de presó:',
    freedom: 'Llibertat:',
    freedomDate: 'Data llibertat:',
    s2_title: '2) Trasllats de presó:',
    transfers: 'Trasllats:',
    transferPlace: 'Lloc trasllat:',
    transferDate: 'Data trasllat:',
    s3_title: '3) Altres dades:',
    vicissitudes: 'Vicissituds:',
    observations: 'Observacions:',
  },
  es: {
    heading: 'Encarcelamiento Prisión Modelo, registro núm. {n}',
    s1_title: '1) Datos del encarcelamiento:',
    prisonDate: 'Fecha de encarcelamiento:',
    prisonMode: 'Modalidad de prisión:',
    freedom: 'Libertad:',
    freedomDate: 'Fecha de libertad:',
    s2_title: '2) Traslados de prisión:',
    transfers: 'Traslados:',
    transferPlace: 'Lugar de traslado:',
    transferDate: 'Fecha de traslado:',
    s3_title: '3) Otros datos:',
    vicissitudes: 'Vicisitudes:',
    observations: 'Observaciones:',
  },
  en: {
    heading: 'Imprisonment at Model Prison, record no. {n}',
    s1_title: '1) Imprisonment data:',
    prisonDate: 'Date of imprisonment:',
    prisonMode: 'Prison modality:',
    freedom: 'Release:',
    freedomDate: 'Release date:',
    s2_title: '2) Prison transfers:',
    transfers: 'Transfers:',
    transferPlace: 'Transfer location:',
    transferDate: 'Transfer date:',
    s3_title: '3) Other data:',
    vicissitudes: 'Vicissitudes:',
    observations: 'Observations:',
  },
  fr: {
    heading: 'Emprisonnement à la Prison Model, enregistrement n° {n}',
    s1_title: "1) Données de l'emprisonnement :",
    prisonDate: "Date d'emprisonnement :",
    prisonMode: 'Modalité de prison :',
    freedom: 'Libération :',
    freedomDate: 'Date de libération :',
    s2_title: '2) Transferts de prison :',
    transfers: 'Transferts :',
    transferPlace: 'Lieu de transfert :',
    transferDate: 'Date de transfert :',
    s3_title: '3) Autres données :',
    vicissitudes: 'Vicissitudes :',
    observations: 'Observations :',
  },
  it: {
    heading: 'Carcerazione al Carcere Modelo, registro n. {n}',
    s1_title: '1) Dati della carcerazione:',
    prisonDate: 'Data di carcerazione:',
    prisonMode: 'Modalità di detenzione:',
    freedom: 'Liberazione:',
    freedomDate: 'Data di liberazione:',
    s2_title: '2) Trasferimenti carcerari:',
    transfers: 'Trasferimenti:',
    transferPlace: 'Luogo del trasferimento:',
    transferDate: 'Data del trasferimento:',
    s3_title: '3) Altri dati:',
    vicissitudes: 'Vicissitudini:',
    observations: 'Osservazioni:',
  },
  pt: {
    heading: 'Encarceramento na Prisão Modelo, registo n.º {n}',
    s1_title: '1) Dados do encarceramento:',
    prisonDate: 'Data do encarceramento:',
    prisonMode: 'Modalidade de prisão:',
    freedom: 'Liberdade:',
    freedomDate: 'Data de libertação:',
    s2_title: '2) Transferências de prisão:',
    transfers: 'Transferências:',
    transferPlace: 'Local da transferência:',
    transferDate: 'Data da transferência:',
    s3_title: '3) Outros dados:',
    vicissitudes: 'Vicissitudes:',
    observations: 'Observações:',
  },
});

// Sí / No / Sense dades
type TriKey = 'yes' | 'no' | 'noData';
export const LABELS_TRI = makeDict<TriKey>({
  ca: { yes: 'Sí', no: 'No', noData: 'Sense dades' },
  es: { yes: 'Sí', no: 'No', noData: 'Sin datos' },
  en: { yes: 'Yes', no: 'No', noData: 'No data' },
  fr: { yes: 'Oui', no: 'Non', noData: 'Aucune donnée' },
  it: { yes: 'Sì', no: 'No', noData: 'Senza dati' },
  pt: { yes: 'Sim', no: 'Não', noData: 'Sem dados' },
});
