// labels-deport.ts
import { makeDict } from '../i18n';

type DeportKeys =
  | 'liberationDate'
  | 'liberationTown'
  | 'escapeDate'
  | 'escapeTown'
  // Sección 1
  | 's1_title'
  | 'deporteeStatus'
  // Sección 2
  | 's2_title'
  | 'prisonType'
  | 'prisonName'
  | 'prisonTown'
  | 'prisonExitDate'
  | 'prisonMatricNo'
  | 'franceSituationDesc'
  // Sección 3
  | 's3_title'
  | 'firstPrisonHeading'
  | 'secondPrisonHeading'
  | 'prisonEntryDate'
  | 'prisonExitDate_short'
  | 'matricNo'
  // Sección 4
  | 's4_title'
  | 'concCampHeading'
  | 'campName'
  | 'campType'
  | 'campTown'
  | 'campEntryDate'
  | 'campMatricNo'
  | 'subcampHeading'
  | 'subcampName'
  | 'subcampType'
  | 'subcampTown'
  | 'subcampEntryDate'
  | 'subcampMatricNo'
  | 'otherInfoHeading'
  | 'observations';

export const LABELS_DEPORT = makeDict<DeportKeys>({
  ca: {
    // ya existentes
    liberationDate: "Data d'alliberament del camp",
    liberationTown: "Municipi d'alliberament",
    escapeDate: "Data d'evasió",
    escapeTown: "Municipi d'evasió",
    // sección 1
    s1_title: '1) Dades bàsiques sobre la deportació:',
    deporteeStatus: 'Situació del deportat:',
    // sección 2
    s2_title: '2) Dades sobre la situació a França, prèvia a la deportació:',
    prisonType: 'Tipus de Presó/camp de detenció:',
    prisonName: 'Nom de la presó/camp:',
    prisonTown: 'Municipi de la presó/camp:',
    prisonExitDate: 'Data de la sortida de la presó/camp:',
    prisonMatricNo: 'Número de matrícula presó:',
    franceSituationDesc: 'Descripció situació a França:',
    // sección 3
    s3_title: '3) Camp de classificació/detenció previ a la deportació al camp de concentració:',
    firstPrisonHeading: 'Primera presó/camp de classificació',
    secondPrisonHeading: 'Segona presó/camp de classificació',
    prisonEntryDate: "Data d'entrada de la presó:",
    prisonExitDate_short: 'Data de la sortida de la presó:',
    matricNo: 'Número de matrícula:',
    // sección 4
    s4_title: '4) Dades sobre la deportació al camp de concentració/extermini:',
    concCampHeading: 'Dades sobre el camp de concentració:',
    campName: 'Nom del camp de deportació:',
    campType: 'Tipus de camp:',
    campTown: 'Municipi del camp:',
    campEntryDate: "Data d'entrada al camp:",
    campMatricNo: 'Número de matrícula:',
    subcampHeading: 'Dades sobre el subcamp:',
    subcampName: 'Nom del subcamp :',
    subcampType: 'Tipus de subcamp:',
    subcampTown: 'Municipi del subcamp:',
    subcampEntryDate: "Data d'entrada al subcamp:",
    subcampMatricNo: 'Número de matrícula del subcamp:',
    otherInfoHeading: 'Altres informacions:',
    observations: 'Observacions:',
  },
  es: {
    liberationDate: 'Fecha de liberación del campo',
    liberationTown: 'Municipio de liberación',
    escapeDate: 'Fecha de evasión',
    escapeTown: 'Municipio de evasión',
    s1_title: '1) Datos básicos sobre la deportación:',
    deporteeStatus: 'Situación de la persona deportada:',
    s2_title: '2) Datos sobre la situación en Francia, previa a la deportación:',
    prisonType: 'Tipo de prisión/campo de detención:',
    prisonName: 'Nombre de la prisión/campo:',
    prisonTown: 'Municipio de la prisión/campo:',
    prisonExitDate: 'Fecha de salida de la prisión/campo:',
    prisonMatricNo: 'Número de matrícula de la prisión:',
    franceSituationDesc: 'Descripción de la situación en Francia:',
    s3_title: '3) Campo de clasificación/detención previo a la deportación al campo de concentración:',
    firstPrisonHeading: 'Primera prisión/campo de clasificación',
    secondPrisonHeading: 'Segunda prisión/campo de clasificación',
    prisonEntryDate: 'Fecha de entrada en la prisión:',
    prisonExitDate_short: 'Fecha de salida de la prisión:',
    matricNo: 'Número de matrícula:',
    s4_title: '4) Datos sobre la deportación al campo de concentración/exterminio:',
    concCampHeading: 'Datos sobre el campo de concentración:',
    campName: 'Nombre del campo de deportación:',
    campType: 'Tipo de campo:',
    campTown: 'Municipio del campo:',
    campEntryDate: 'Fecha de entrada en el campo:',
    campMatricNo: 'Número de matrícula:',
    subcampHeading: 'Datos sobre el subcampo:',
    subcampName: 'Nombre del subcampo:',
    subcampType: 'Tipo de subcampo:',
    subcampTown: 'Municipio del subcampo:',
    subcampEntryDate: 'Fecha de entrada en el subcampo:',
    subcampMatricNo: 'Número de matrícula del subcampo:',
    otherInfoHeading: 'Otras informaciones:',
    observations: 'Observaciones:',
  },
  en: {
    liberationDate: 'Date of camp liberation',
    liberationTown: 'Municipality of liberation',
    escapeDate: 'Date of escape',
    escapeTown: 'Municipality of escape',
    s1_title: '1) Basic data on the deportation:',
    deporteeStatus: 'Status of the deportee:',
    s2_title: '2) Data on the situation in France, prior to deportation:',
    prisonType: 'Type of prison/detention camp:',
    prisonName: 'Name of the prison/camp:',
    prisonTown: 'Municipality of the prison/camp:',
    prisonExitDate: 'Date of release from the prison/camp:',
    prisonMatricNo: 'Prison registration number:',
    franceSituationDesc: 'Description of the situation in France:',
    s3_title: '3) Classification/detention camp prior to deportation to the concentration camp:',
    firstPrisonHeading: 'First classification prison/camp',
    secondPrisonHeading: 'Second classification prison/camp',
    prisonEntryDate: 'Date of entry into the prison:',
    prisonExitDate_short: 'Date of release from the prison:',
    matricNo: 'Registration number:',
    s4_title: '4) Data on deportation to the concentration/extermination camp:',
    concCampHeading: 'Data on the concentration camp:',
    campName: 'Name of the deportation camp:',
    campType: 'Type of camp:',
    campTown: 'Municipality of the camp:',
    campEntryDate: 'Date of entry into the camp:',
    campMatricNo: 'Registration number:',
    subcampHeading: 'Data on the subcamp:',
    subcampName: 'Name of the subcamp:',
    subcampType: 'Type of subcamp:',
    subcampTown: 'Municipality of the subcamp:',
    subcampEntryDate: 'Date of entry into the subcamp:',
    subcampMatricNo: 'Subcamp registration number:',
    otherInfoHeading: 'Other information:',
    observations: 'Observations:',
  },
  fr: {
    liberationDate: 'Date de libération du camp',
    liberationTown: 'Commune de libération',
    escapeDate: "Date d'évasion",
    escapeTown: "Commune d'évasion",
    s1_title: '1) Données de base sur la déportation :',
    deporteeStatus: 'Situation de la personne déportée :',
    s2_title: '2) Données sur la situation en France, avant la déportation :',
    prisonType: 'Type de prison/camp de détention :',
    prisonName: 'Nom de la prison/du camp :',
    prisonTown: 'Commune de la prison/du camp :',
    prisonExitDate: 'Date de sortie de la prison/du camp :',
    prisonMatricNo: 'Numéro de matricule de la prison :',
    franceSituationDesc: 'Description de la situation en France :',
    s3_title: '3) Camp de tri/détention préalable à la déportation vers le camp de concentration :',
    firstPrisonHeading: 'Première prison/camp de tri',
    secondPrisonHeading: 'Deuxième prison/camp de tri',
    prisonEntryDate: "Date d'entrée dans la prison :",
    prisonExitDate_short: 'Date de sortie de la prison :',
    matricNo: 'Numéro de matricule :',
    s4_title: '4) Données sur la déportation vers le camp de concentration/extermination :',
    concCampHeading: 'Données sur le camp de concentration :',
    campName: 'Nom du camp de déportation :',
    campType: 'Type de camp :',
    campTown: 'Commune du camp :',
    campEntryDate: "Date d'entrée au camp :",
    campMatricNo: 'Numéro de matricule :',
    subcampHeading: 'Données sur le sous-camp :',
    subcampName: 'Nom du sous-camp :',
    subcampType: 'Type de sous-camp :',
    subcampTown: 'Commune du sous-camp :',
    subcampEntryDate: "Date d'entrée au sous-camp :",
    subcampMatricNo: 'Numéro de matricule du sous-camp :',
    otherInfoHeading: 'Autres informations :',
    observations: 'Observations :',
  },
  it: {
    liberationDate: 'Data di liberazione del campo',
    liberationTown: 'Comune di liberazione',
    escapeDate: "Data dell'evasione",
    escapeTown: "Comune dell'evasione",
    s1_title: '1) Dati di base sulla deportazione:',
    deporteeStatus: 'Situazione della persona deportata:',
    s2_title: '2) Dati sulla situazione in Francia, prima della deportazione:',
    prisonType: 'Tipo di prigione/campo di detenzione:',
    prisonName: 'Nome della prigione/del campo:',
    prisonTown: 'Comune della prigione/del campo:',
    prisonExitDate: 'Data di uscita dalla prigione/dal campo:',
    prisonMatricNo: 'Numero di matricola della prigione:',
    franceSituationDesc: 'Descrizione della situazione in Francia:',
    s3_title: '3) Campo di classificazione/detenzione precedente alla deportazione al campo di concentramento:',
    firstPrisonHeading: 'Prima prigione/campo di classificazione',
    secondPrisonHeading: 'Seconda prigione/campo di classificazione',
    prisonEntryDate: 'Data di ingresso in prigione:',
    prisonExitDate_short: 'Data di uscita dalla prigione:',
    matricNo: 'Numero di matricola:',
    s4_title: '4) Dati sulla deportazione al campo di concentramento/sterminio:',
    concCampHeading: 'Dati sul campo di concentramento:',
    campName: 'Nome del campo di deportazione:',
    campType: 'Tipo di campo:',
    campTown: 'Comune del campo:',
    campEntryDate: 'Data di ingresso nel campo:',
    campMatricNo: 'Numero di matricola:',
    subcampHeading: 'Dati sul sottocampo:',
    subcampName: 'Nome del sottocampo:',
    subcampType: 'Tipo di sottocampo:',
    subcampTown: 'Comune del sottocampo:',
    subcampEntryDate: 'Data di ingresso nel sottocampo:',
    subcampMatricNo: 'Numero di matricola del sottocampo:',
    otherInfoHeading: 'Altre informazioni:',
    observations: 'Osservazioni:',
  },
  pt: {
    liberationDate: 'Data de libertação do campo',
    liberationTown: 'Município de libertação',
    escapeDate: 'Data da fuga',
    escapeTown: 'Município da fuga',
    s1_title: '1) Dados básicos sobre a deportação:',
    deporteeStatus: 'Situação da pessoa deportada:',
    s2_title: '2) Dados sobre a situação em França, antes da deportação:',
    prisonType: 'Tipo de prisão/campo de detenção:',
    prisonName: 'Nome da prisão/campo:',
    prisonTown: 'Município da prisão/campo:',
    prisonExitDate: 'Data de saída da prisão/campo:',
    prisonMatricNo: 'Número de matrícula da prisão:',
    franceSituationDesc: 'Descrição da situação em França:',
    s3_title: '3) Campo de classificação/detenção anterior à deportação para o campo de concentração:',
    firstPrisonHeading: 'Primeira prisão/campo de classificação',
    secondPrisonHeading: 'Segunda prisão/campo de classificação',
    prisonEntryDate: 'Data de entrada na prisão:',
    prisonExitDate_short: 'Data de saída da prisão:',
    matricNo: 'Número de matrícula:',
    s4_title: '4) Dados sobre a deportação para o campo de concentração/extermínio:',
    concCampHeading: 'Dados sobre o campo de concentração:',
    campName: 'Nome do campo de deportação:',
    campType: 'Tipo de campo:',
    campTown: 'Município do campo:',
    campEntryDate: 'Data de entrada no campo:',
    campMatricNo: 'Número de matrícula:',
    subcampHeading: 'Dados sobre o subcampo:',
    subcampName: 'Nome do subcampo:',
    subcampType: 'Tipo de subcampo:',
    subcampTown: 'Município do subcampo:',
    subcampEntryDate: 'Data de entrada no subcampo:',
    subcampMatricNo: 'Número de matrícula do subcampo:',
    otherInfoHeading: 'Outras informações:',
    observations: 'Observações:',
  },
});
