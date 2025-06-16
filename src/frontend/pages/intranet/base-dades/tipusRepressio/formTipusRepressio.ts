import { getPageType } from '../../../../services/url/splitUrl';
import { costHumaCivils } from './costHumaCivils';
import { costHumaCombat } from './costHumaCombat';
import { exili } from './exili';
import { deportat } from './deportat';
import { afusellat } from './afusellat';
import { processat } from './processat';

export function formTipusRepressio() {
  const url = window.location.href;
  const pageType = getPageType(url);

  if (pageType[3] === '4') {
    costHumaCivils(Number(pageType[4]));
  } else if (pageType[3] === '3') {
    costHumaCombat(Number(pageType[4]));
  } else if (pageType[3] === '10') {
    exili(Number(pageType[4]));
  } else if (pageType[3] === '2') {
    deportat(Number(pageType[4]));
  } else if (pageType[3] === '1') {
    afusellat(Number(pageType[4]));
  } else if (pageType[3] === '6') {
    processat(Number(pageType[4]));
  }
}
