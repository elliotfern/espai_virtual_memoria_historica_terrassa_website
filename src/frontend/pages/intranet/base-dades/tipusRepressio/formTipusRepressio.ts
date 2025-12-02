import { getPageType } from '../../../../services/url/splitUrl';
import { costHumaCivils } from './costHumaCivils';
import { costHumaCombat } from './costHumaCombat';
import { exili } from './exili';
import { deportat } from './deportat';
import { afusellat } from './afusellat';
import { llistatDetingutConsellGuerra } from './processat';
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

  const tipus = Number(pageType[3]); // <- número
  const id = Number(pageType[4]); // <- número

  switch (tipus) {
    case 4:
      costHumaCivils(id);
      break;

    case 3:
    case 22:
      costHumaCombat(id);
      break;

    case 10:
      exili(id);
      break;

    case 2:
      deportat(id);
      break;

    case 1:
      afusellat(id);
      break;

    case 6:
      llistatDetingutConsellGuerra(id);
      break;

    case 12:
      empresonatsPresoModel(id);
      break;

    case 7:
      depurats(id);
      break;

    case 13:
    case 16:
      detingutsGuardiaUrbana(id);
      break;

    case 15:
      responsabilitatsPolitiques(id);
      break;

    case 17:
      top(id);
      break;

    case 14:
      comiteSolidaritat(id);
      break;

    case 18:
      comiteRelacionsSolidaritat(id);
      break;
  }
}
