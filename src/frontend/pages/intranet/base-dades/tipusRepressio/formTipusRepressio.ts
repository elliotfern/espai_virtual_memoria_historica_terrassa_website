import { getPageType } from '../../../../services/url/splitUrl';
import { costHumaCivils } from './costHumaCivils';
import { costHumaCombat } from './costHumaCombat';
import { exili } from './exili';
import { deportat } from './deportat';
import { afusellat } from './afusellat';
import { processat } from './processat';
import { empresonatsPresoModel } from './presoModel';
import { depurats } from './depurats';
import { detingutsGuardiaUrbana } from './detingutsGuardiaUrbana';
import { responsabilitatsPolitiques } from './responsabilitatsPolitiques';
import { top } from './top';
import { comiteSolidaritat } from './comiteSolidaritat';
import { comiteRelacionsSolidaritat } from './comiteRelacionsSolidaritat';

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
  } else if (pageType[3] === '12') {
    empresonatsPresoModel(Number(pageType[4]));
  } else if (pageType[3] === '7') {
    depurats(Number(pageType[4]));
  } else if (pageType[3] === '13' || pageType[3] === '16') {
    detingutsGuardiaUrbana(Number(pageType[4]));
  } else if (pageType[3] === '15') {
    responsabilitatsPolitiques(Number(pageType[4]));
  } else if (pageType[3] === '17') {
    top(Number(pageType[4]));
  } else if (pageType[3] === '14') {
    comiteSolidaritat(Number(pageType[4]));
  } else if (pageType[3] === '18') {
    comiteRelacionsSolidaritat(Number(pageType[4]));
  }
}
