// labels-map.ts
import { makeDict } from './i18n';

type MapKeys = 'mapIntro';

export const LABELS_MAP = makeDict<MapKeys>({
  ca: {
    mapIntro: "En aquest mapa apareix l'adreça coneguda de residència del represaliat abans de l'esclat de la Guerra Civil (en el cas dels exiliats, deportats o morts civils/militars) o durant la dictadura franquista.",
  },
  es: {
    mapIntro: 'En este mapa aparece la dirección conocida de residencia de la persona represaliada antes del estallido de la Guerra Civil (en el caso de exiliados, deportados o fallecimientos civiles/militares) o durante la dictadura franquista.',
  },
  en: {
    mapIntro: 'This map shows the known residential address of the repressed person prior to the outbreak of the Spanish Civil War (in the case of exiles, deportees, or civilian/military deaths) or during the Francoist dictatorship.',
  },
  fr: {
    mapIntro: 'Cette carte indique l’adresse de résidence connue de la personne réprimée avant le déclenchement de la guerre civile espagnole (dans le cas des exilés, des déportés ou des décès civils/militaires) ou durant la dictature franquiste.',
  },
  it: {
    mapIntro: 'In questa mappa compare l’indirizzo di residenza conosciuto della persona repressa prima dello scoppio della Guerra Civile spagnola (nel caso di esiliati, deportati o decessi civili/militari) oppure durante la dittatura franchista.',
  },
  pt: {
    mapIntro: 'Este mapa mostra a morada conhecida da pessoa reprimida antes do início da Guerra Civil Espanhola (no caso de exilados, deportados ou mortes civis/militares) ou durante a ditadura franquista.',
  },
});
