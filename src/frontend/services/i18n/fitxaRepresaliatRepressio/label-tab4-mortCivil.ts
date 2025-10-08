import { makeDict } from '../i18n';

type CircKeys =
  // Sección básica
  | 's1_title'
  | 'deathCirc'
  | 'bodyFoundDate'
  | 'muni_bodyFound'
  // Causa (heading compuesto "Causa de la mort: X")
  | 'causeOfDeath'
  | 'bombing'
  | 'extrajudicial'
  | 'firingSquad'
  // Bombardeig
  | 'bombingDate'
  | 'bombingMunicipality'
  | 'bombingPlaceType'
  | 'bombingResponsible'
  | 'muni_bombing'
  // Assassinat extra-judicial
  | 'detentionDate'
  | 'detentionPlace'
  | 'whoDetains'
  | 'muni_extrajudicial'
  // Afusellat
  | 'whoOrdersExec'
  | 'whoExecutes'
  | 'muni_execution'
  // Responsables bombardeig
  | 'resp_it_fasc_air'
  | 'resp_de_nazi_air'
  | 'resp_es_franco_air';

export const LABELS_CIRC = makeDict<CircKeys>({
  ca: {
    // Básico
    s1_title: '1) Dades bàsiques:',
    deathCirc: 'Circumstàncies de la mort:',
    bodyFoundDate: 'Data trobada del cadàver:',
    muni_bodyFound: 'Municipi trobada del cadàver',
    // Causas
    causeOfDeath: 'Causa de la mort',
    bombing: 'Bombardeig',
    extrajudicial: 'extra-judicial (assassinat)',
    firingSquad: 'afusellat',
    // Bombardeig
    bombingDate: 'Data del bombardeig:',
    bombingMunicipality: 'Municipi del bombardeig:',
    bombingPlaceType: "Tipus d'espai del bombardeig:",
    bombingResponsible: 'Responsable del bombardeig:',
    muni_bombing: 'Municipi del bombardeig',
    // Extra-judicial
    detentionDate: 'Data de la detenció:',
    detentionPlace: 'Lloc de la detenció:',
    whoDetains: 'Qui el deté?:',
    muni_extrajudicial: 'Municipi assassinat extra-judicial',
    // Afusellat
    whoOrdersExec: "Qui ordena l'afusellament:",
    whoExecutes: "Qui l'executa:",
    muni_execution: 'Municipi afusellament',
    // Responsables (bombardeig)
    resp_it_fasc_air: 'Aviació feixista italiana',
    resp_de_nazi_air: 'Aviació nazista alemanya',
    resp_es_franco_air: 'Aviació franquista',
  },
  es: {
    s1_title: '1) Datos básicos:',
    deathCirc: 'Circunstancias de la muerte:',
    bodyFoundDate: 'Fecha de hallazgo del cadáver:',
    muni_bodyFound: 'Municipio de hallazgo del cadáver',
    causeOfDeath: 'Causa de la muerte',
    bombing: 'Bombardeo',
    extrajudicial: 'extrajudicial (asesinato)',
    firingSquad: 'fusilado',
    bombingDate: 'Fecha del bombardeo:',
    bombingMunicipality: 'Municipio del bombardeo:',
    bombingPlaceType: 'Tipo de espacio bombardeado:',
    bombingResponsible: 'Responsable del bombardeo:',
    muni_bombing: 'Municipio del bombardeo',
    detentionDate: 'Fecha de la detención:',
    detentionPlace: 'Lugar de la detención:',
    whoDetains: '¿Quién lo detiene?:',
    muni_extrajudicial: 'Municipio del asesinato extrajudicial',
    whoOrdersExec: 'Quién ordena el fusilamiento:',
    whoExecutes: 'Quién lo ejecuta:',
    muni_execution: 'Municipio del fusilamiento',
    resp_it_fasc_air: 'Aviación fascista italiana',
    resp_de_nazi_air: 'Aviación nazi alemana',
    resp_es_franco_air: 'Aviación franquista',
  },
  en: {
    s1_title: '1) Basic data:',
    deathCirc: 'Circumstances of death:',
    bodyFoundDate: 'Date body was found:',
    muni_bodyFound: 'Municipality where the body was found',
    causeOfDeath: 'Cause of death',
    bombing: 'Bombing',
    extrajudicial: 'extrajudicial (assassination)',
    firingSquad: 'executed by firing squad',
    bombingDate: 'Date of the bombing:',
    bombingMunicipality: 'Municipality of the bombing:',
    bombingPlaceType: 'Type of place bombed:',
    bombingResponsible: 'Responsible for the bombing:',
    muni_bombing: 'Municipality of the bombing',
    detentionDate: 'Date of arrest:',
    detentionPlace: 'Place of arrest:',
    whoDetains: 'Who arrests them?:',
    muni_extrajudicial: 'Municipality of the extrajudicial killing',
    whoOrdersExec: 'Who ordered the execution:',
    whoExecutes: 'Who carried it out:',
    muni_execution: 'Municipality of the execution',
    resp_it_fasc_air: 'Italian fascist air force',
    resp_de_nazi_air: 'German Nazi air force',
    resp_es_franco_air: 'Francoist air force',
  },
  fr: {
    s1_title: '1) Données de base :',
    deathCirc: 'Circonstances du décès :',
    bodyFoundDate: 'Date de découverte du corps :',
    muni_bodyFound: 'Commune où le corps a été trouvé',
    causeOfDeath: 'Cause du décès',
    bombing: 'Bombardement',
    extrajudicial: 'extrajudiciaire (assassinat)',
    firingSquad: 'exécuté par fusillade',
    bombingDate: 'Date du bombardement :',
    bombingMunicipality: 'Commune du bombardement :',
    bombingPlaceType: 'Type de lieu bombardé :',
    bombingResponsible: 'Responsable du bombardement :',
    muni_bombing: 'Commune du bombardement',
    detentionDate: 'Date de l’arrestation :',
    detentionPlace: 'Lieu de l’arrestation :',
    whoDetains: 'Qui l’arrête ? :',
    muni_extrajudicial: 'Commune de l’exécution extrajudiciaire',
    whoOrdersExec: "Qui ordonne l'exécution :",
    whoExecutes: "Qui l'exécute :",
    muni_execution: "Commune de l'exécution",
    resp_it_fasc_air: 'Aviation fasciste italienne',
    resp_de_nazi_air: 'Aviation nazie allemande',
    resp_es_franco_air: 'Aviation franquiste',
  },
  it: {
    s1_title: '1) Dati di base:',
    deathCirc: 'Circostanze della morte:',
    bodyFoundDate: 'Data del ritrovamento del cadavere:',
    muni_bodyFound: 'Comune del ritrovamento del cadavere',
    causeOfDeath: 'Causa della morte',
    bombing: 'Bombardamento',
    extrajudicial: 'extragiudiziale (assassinio)',
    firingSquad: 'fucilato',
    bombingDate: 'Data del bombardamento:',
    bombingMunicipality: 'Comune del bombardamento:',
    bombingPlaceType: 'Tipo di luogo bombardato:',
    bombingResponsible: 'Responsabile del bombardamento:',
    muni_bombing: 'Comune del bombardamento',
    detentionDate: 'Data dell’arresto:',
    detentionPlace: 'Luogo dell’arresto:',
    whoDetains: 'Chi lo arresta?:',
    muni_extrajudicial: 'Comune dell’uccisione extragiudiziale',
    whoOrdersExec: "Chi ordina l'esecuzione:",
    whoExecutes: 'Chi la esegue:',
    muni_execution: "Comune dell'esecuzione",
    resp_it_fasc_air: 'Aviazione fascista italiana',
    resp_de_nazi_air: 'Aviazione nazista tedesca',
    resp_es_franco_air: 'Aviazione franchista',
  },
  pt: {
    s1_title: '1) Dados básicos:',
    deathCirc: 'Circunstâncias da morte:',
    bodyFoundDate: 'Data em que o cadáver foi encontrado:',
    muni_bodyFound: 'Município onde o cadáver foi encontrado',
    causeOfDeath: 'Causa da morte',
    bombing: 'Bombardeamento',
    extrajudicial: 'extrajudicial (assassinato)',
    firingSquad: 'fuzilado',
    bombingDate: 'Data do bombardeamento:',
    bombingMunicipality: 'Município do bombardeamento:',
    bombingPlaceType: 'Tipo de local bombardeado:',
    bombingResponsible: 'Responsável pelo bombardeamento:',
    muni_bombing: 'Município do bombardeamento',
    detentionDate: 'Data da detenção:',
    detentionPlace: 'Local da detenção:',
    whoDetains: 'Quem o detém?:',
    muni_extrajudicial: 'Município do assassinato extrajudicial',
    whoOrdersExec: 'Quem ordena a execução:',
    whoExecutes: 'Quem a executa:',
    muni_execution: 'Município da execução',
    resp_it_fasc_air: 'Aviação fascista italiana',
    resp_de_nazi_air: 'Aviação nazi alemã',
    resp_es_franco_air: 'Aviação franquista',
  },
});
