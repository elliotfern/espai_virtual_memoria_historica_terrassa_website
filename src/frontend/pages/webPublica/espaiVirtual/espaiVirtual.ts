import { getPageType } from '../../../services/url/splitUrl';
import { initPublicAparicioPremsaDetalls } from './aparicioDetalls';
import { initPublicAparicionsPremsaList } from './aparicionsPremsaList';

// publicAparicionsPremsaList.ts
type Lang = 'ca' | 'es' | 'en' | 'fr' | 'it' | 'pt';

export function espaiVirtualWebPublica(lang: Lang) {
  const url = window.location.href;
  const pageType = getPageType(url);

  if (pageType[1] === 'que-es-espai-virtual') {
    //
  } else if (pageType[1] === 'premsa') {
    initPublicAparicionsPremsaList(lang);
  } else if (pageType[1] === 'premsa-aparicio') {
    console.log('hola detalls');
    const id = Number(pageType[2]);
    initPublicAparicioPremsaDetalls(id, lang);
  }
}
