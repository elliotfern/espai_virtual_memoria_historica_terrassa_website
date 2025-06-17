import { mostrarBotonsNomesAdmin } from '../../../components/mostrarBotoAdmin/mostrarBotoAdmin';
import { getPageType } from '../../../services/url/splitUrl';
import { cargarTabla } from '../../../components/taulaDades/taulaDades';
import { botonsEstat } from '../../../components/taulaDades/botonsEstat';
import { modificaFitxa } from '../../../components/modificaFitxaRepresaliat/modificaFitxa';
import { formTipusRepressio } from './tipusRepressio/formTipusRepressio';
import { taulaExiliats } from './taulaExiliats';
import { taulaDeportats } from './taulaDeportats';
import { taulaDuplicats } from './taulaDuplicats';
import { taulaMortsFronts } from './taulaMortsFront';
import { taulaMortsCivils } from './taulaMortsCivils';
import { taulaRepresaliaRepublicana } from './taulaRepresaliaRepublicana';
import { taulaProcessats } from './taulaProcessats';
import { taulaAfusellats } from './taulaAfusellats';

export function baseDadesIntranet() {
  const url = window.location.href;
  const pageType = getPageType(url);

  mostrarBotonsNomesAdmin();

  if (pageType[2] === 'general') {
    if (pageType[3] === 'llistat-duplicats') {
      taulaDuplicats();
    } else {
      botonsEstat(pageType[2]);
      cargarTabla(pageType[2], 2);
    }
  } else if (pageType[2] === 'represaliats') {
    if (pageType[3] === 'llistat-processats') {
      taulaProcessats();
    } else if (pageType[3] === 'llistat-afusellats') {
      taulaAfusellats();
    } else {
      botonsEstat(pageType[2]);
      cargarTabla(pageType[2], 2);
    }
  } else if (pageType[2] === 'exiliats-deportats') {
    if (pageType[3] === 'llistat-exiliats') {
      taulaExiliats();
    } else if (pageType[3] === 'llistat-deportats') {
      taulaDeportats();
    } else {
      botonsEstat(pageType[2]);
      cargarTabla(pageType[2], 2);
    }
  } else if (pageType[2] === 'cost-huma') {
    if (pageType[3] === 'llistat-morts-al-front') {
      taulaMortsFronts();
    } else if (pageType[3] === 'llistat-morts-civils') {
      taulaMortsCivils();
    } else if (pageType[3] === 'llistat-represalia-republicana') {
      taulaRepresaliaRepublicana();
    } else {
      botonsEstat(pageType[2]);
      cargarTabla(pageType[2], 2);
    }
  } else if (pageType[2] === 'modifica-fitxa') {
    modificaFitxa(Number(pageType[3]));
  } else if (pageType[2] === 'nova-fitxa') {
    modificaFitxa();
  } else if (pageType[2] === 'modifica-repressio') {
    formTipusRepressio();
  }
}
