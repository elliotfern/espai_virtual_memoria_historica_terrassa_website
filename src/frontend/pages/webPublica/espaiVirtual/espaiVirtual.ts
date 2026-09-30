import { getPageType } from '../../../services/url/splitUrl';
import { initPublicAgendaList } from './agendaActivitats';
import { initPublicAgendaActivitat } from './agendaDetalls';
import { blocAntecedentsPublic } from './antecedents';
import { initPublicAparicioPremsaDetalls } from './aparicioDetalls';
import { initPublicAparicionsPremsaList } from './aparicionsPremsaList';

// publicAparicionsPremsaList.ts
type Lang = 'ca' | 'es' | 'en' | 'fr' | 'it' | 'pt';

export function espaiVirtualWebPublica(lang: Lang) {
  const url = window.location.href;
  const pageType = getPageType(url);

  if (pageType[1] === 'que-es-espai-virtual') {
    //
  } else if (pageType[1] === 'premsa' || pageType[2] === 'premsa') {
    initPublicAparicionsPremsaList(lang);
  } else if (pageType[1] === 'premsa-aparicio' || pageType[2] === 'premsa-aparicio') {
    const id = Number(pageType[2]);
    initPublicAparicioPremsaDetalls(id, lang);
  } else if (pageType[1] === 'antecedents' || pageType[2] === 'antecedents') {
    blocAntecedentsPublic(lang);
  } else if (pageType[1] === 'agenda-activitats' || pageType[2] === 'agenda-activitats') {
    initPublicAgendaList(lang);
  } else if (pageType[1] === 'agenda-activitat' || pageType[2] === 'agenda-activitat') {
    const slug = pageType[2];
    initPublicAgendaActivitat(slug, lang);
  }
}
