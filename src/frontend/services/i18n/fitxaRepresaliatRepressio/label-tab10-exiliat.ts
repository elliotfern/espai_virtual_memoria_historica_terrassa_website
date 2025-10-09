import { makeDict } from '../i18n';

type ExiliKeys =
  // Secciones
  | 's1_title'
  | 's2_title'
  | 's3_title'
  | 's4_title'
  | 's5_title'
  // Campos
  | 'exileDate'
  | 'departurePlace'
  | 'borderCrossingPlace'
  | 'withWhomExiles'
  | 'firstDestMunicipality'
  | 'firstDestDate'
  | 'firstDestType'
  | 'firstDestData'
  | 'journey'
  | 'deportedToNaziCamps'
  | 'resistanceParticipation'
  | 'resistanceData'
  | 'politicalActivity'
  | 'unionActivity'
  | 'legalStatusSpain'
  | 'lastDestMunicipality'
  | 'lastDestType';

export const LABELS_EXILI = makeDict<ExiliKeys>({
  ca: {
    s1_title: '1) Sortida de Catalunya:',
    s2_title: "2) Arribada al lloc d'exili:",
    s3_title: "3) Periple durant l'exili:",
    s4_title: "4) Activitat política i sindical durant l'exili:",
    s5_title: "5) Final del període d'exili:",
    exileDate: "Data d'exili:",
    departurePlace: "Lloc de partida per a l'exili:",
    borderCrossingPlace: 'Lloc de pas de la frontera:',
    withWhomExiles: "Amb qui pasa a l'exili:",
    firstDestMunicipality: "Primer municipi de destí a l'exili:",
    firstDestDate: "Data del primer destí de l'exili:",
    firstDestType: "Tipologia del primer destí a l'exili:",
    firstDestData: "Dades del primer destí de l'exili:",
    journey: "Periple del recorregut a l'exili:",
    deportedToNaziCamps: 'Deportat als camps de concentració nazi:',
    resistanceParticipation: 'Participació a la Resistència francesa:',
    resistanceData: 'Dades de la Resistència:',
    politicalActivity: "Activitat política a l'exili:",
    unionActivity: "Activitat sindical a l'exili:",
    legalStatusSpain: 'Situació legal a Espanya:',
    lastDestMunicipality: "Darrer municipi de destí a l'exili:",
    lastDestType: "Tipologia del darrer destí a l'exili:",
  },
  es: {
    s1_title: '1) Salida de Cataluña:',
    s2_title: '2) Llegada al lugar de exilio:',
    s3_title: '3) Periplo durante el exilio:',
    s4_title: '4) Actividad política y sindical durante el exilio:',
    s5_title: '5) Final del período de exilio:',
    exileDate: 'Fecha de exilio:',
    departurePlace: 'Lugar de partida hacia el exilio:',
    borderCrossingPlace: 'Lugar de paso de la frontera:',
    withWhomExiles: 'Con quién pasa al exilio:',
    firstDestMunicipality: 'Primer municipio de destino en el exilio:',
    firstDestDate: 'Fecha del primer destino del exilio:',
    firstDestType: 'Tipología del primer destino en el exilio:',
    firstDestData: 'Datos del primer destino del exilio:',
    journey: 'Periplo del recorrido en el exilio:',
    deportedToNaziCamps: 'Deportado a campos de concentración nazis:',
    resistanceParticipation: 'Participación en la Resistencia francesa:',
    resistanceData: 'Datos de la Resistencia:',
    politicalActivity: 'Actividad política en el exilio:',
    unionActivity: 'Actividad sindical en el exilio:',
    legalStatusSpain: 'Situación legal en España:',
    lastDestMunicipality: 'Último municipio de destino en el exilio:',
    lastDestType: 'Tipología del último destino en el exilio:',
  },
  en: {
    s1_title: '1) Departure from Catalonia:',
    s2_title: '2) Arrival at the place of exile:',
    s3_title: '3) Journey during exile:',
    s4_title: '4) Political and union activity during exile:',
    s5_title: '5) End of the exile period:',
    exileDate: 'Date of exile:',
    departurePlace: 'Place of departure for exile:',
    borderCrossingPlace: 'Border crossing location:',
    withWhomExiles: 'With whom they went into exile:',
    firstDestMunicipality: 'First municipality of destination in exile:',
    firstDestDate: 'Date of the first destination in exile:',
    firstDestType: 'Type of the first destination in exile:',
    firstDestData: 'Data on the first destination in exile:',
    journey: 'Journey during exile:',
    deportedToNaziCamps: 'Deported to Nazi concentration camps:',
    resistanceParticipation: 'Participation in the French Resistance:',
    resistanceData: 'Resistance details:',
    politicalActivity: 'Political activity in exile:',
    unionActivity: 'Union activity in exile:',
    legalStatusSpain: 'Legal status in Spain:',
    lastDestMunicipality: 'Last municipality of destination in exile:',
    lastDestType: 'Type of the last destination in exile:',
  },
  fr: {
    s1_title: '1) Départ de Catalogne :',
    s2_title: "2) Arrivée sur le lieu d'exil :",
    s3_title: "3) Parcours durant l'exil :",
    s4_title: "4) Activité politique et syndicale durant l'exil :",
    s5_title: "5) Fin de la période d'exil :",
    exileDate: "Date de l'exil :",
    departurePlace: "Lieu de départ vers l'exil :",
    borderCrossingPlace: 'Lieu de passage de la frontière :',
    withWhomExiles: 'Avec qui il/elle part en exil :',
    firstDestMunicipality: 'Première commune de destination en exil :',
    firstDestDate: 'Date de la première destination en exil :',
    firstDestType: 'Typologie de la première destination en exil :',
    firstDestData: 'Données sur la première destination en exil :',
    journey: "Parcours durant l'exil :",
    deportedToNaziCamps: 'Déporté(e) dans des camps de concentration nazis :',
    resistanceParticipation: 'Participation à la Résistance française :',
    resistanceData: 'Données sur la Résistance :',
    politicalActivity: 'Activité politique en exil :',
    unionActivity: 'Activité syndicale en exil :',
    legalStatusSpain: 'Statut légal en Espagne :',
    lastDestMunicipality: 'Dernière commune de destination en exil :',
    lastDestType: 'Typologie de la dernière destination en exil :',
  },
  it: {
    s1_title: '1) Partenza dalla Catalogna:',
    s2_title: "2) Arrivo nel luogo d'esilio:",
    s3_title: "3) Percorso durante l'esilio:",
    s4_title: "4) Attività politica e sindacale durante l'esilio:",
    s5_title: "5) Fine del periodo d'esilio:",
    exileDate: "Data dell'esilio:",
    departurePlace: "Luogo di partenza per l'esilio:",
    borderCrossingPlace: 'Luogo di attraversamento della frontiera:',
    withWhomExiles: 'Con chi va in esilio:',
    firstDestMunicipality: 'Primo comune di destinazione in esilio:',
    firstDestDate: 'Data della prima destinazione in esilio:',
    firstDestType: 'Tipologia della prima destinazione in esilio:',
    firstDestData: 'Dati sulla prima destinazione in esilio:',
    journey: "Percorso durante l'esilio:",
    deportedToNaziCamps: 'Deportato/a nei campi di concentramento nazisti:',
    resistanceParticipation: 'Partecipazione alla Resistenza francese:',
    resistanceData: 'Dati sulla Resistenza:',
    politicalActivity: 'Attività politica in esilio:',
    unionActivity: 'Attività sindacale in esilio:',
    legalStatusSpain: 'Status legale in Spagna:',
    lastDestMunicipality: 'Ultimo comune di destinazione in esilio:',
    lastDestType: "Tipologia dell'ultima destinazione in esilio:",
  },
  pt: {
    s1_title: '1) Saída da Catalunha:',
    s2_title: '2) Chegada ao local de exílio:',
    s3_title: '3) Percurso durante o exílio:',
    s4_title: '4) Atividade política e sindical durante o exílio:',
    s5_title: '5) Fim do período de exílio:',
    exileDate: 'Data do exílio:',
    departurePlace: 'Local de partida para o exílio:',
    borderCrossingPlace: 'Local de passagem da fronteira:',
    withWhomExiles: 'Com quem foi para o exílio:',
    firstDestMunicipality: 'Primeiro município de destino no exílio:',
    firstDestDate: 'Data do primeiro destino no exílio:',
    firstDestType: 'Tipologia do primeiro destino no exílio:',
    firstDestData: 'Dados do primeiro destino no exílio:',
    journey: 'Percurso durante o exílio:',
    deportedToNaziCamps: 'Deportado(a) para campos de concentração nazis:',
    resistanceParticipation: 'Participação na Resistência francesa:',
    resistanceData: 'Dados da Resistência:',
    politicalActivity: 'Atividade política no exílio:',
    unionActivity: 'Atividade sindical no exílio:',
    legalStatusSpain: 'Situação legal em Espanha:',
    lastDestMunicipality: 'Último município de destino no exílio:',
    lastDestType: 'Tipologia do último destino no exílio:',
  },
});

// Sí/No
type YesNoKey = 'yes' | 'no';
export const LABELS_YESNO = makeDict<YesNoKey>({
  ca: { yes: 'Sí', no: 'No' },
  es: { yes: 'Sí', no: 'No' },
  en: { yes: 'Yes', no: 'No' },
  fr: { yes: 'Oui', no: 'Non' },
  it: { yes: 'Sì', no: 'No' },
  pt: { yes: 'Sim', no: 'Não' },
});
