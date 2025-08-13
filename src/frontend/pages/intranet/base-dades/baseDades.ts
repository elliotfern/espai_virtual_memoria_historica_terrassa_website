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
import { taulaQuadreGeneral } from './taulaQuadreGeneral';
import { taulaPresoModel } from './taulaPresoModel';
import { formDetingutsGuardiaUrbana } from './tipusRepressio/ detingutsGuardiaUrbana';
import { formPresoModel } from './tipusRepressio/presoModel';
import { formcomiteSolidaritat } from './tipusRepressio/comiteSolidaritat';
import { formcomiteRelacionsSolidaritat } from './tipusRepressio/comiteRelacionsSolidaritat';

export function baseDadesIntranet() {
  const url = window.location.href;
  const pageType = getPageType(url);

  mostrarBotonsNomesAdmin();

  if (pageType[2] === 'general') {
    console.log('hola');
    botonsEstat(pageType[1]);
    cargarTabla(pageType[1], 1);
    if (pageType[3] === 'llistat-duplicats') {
      taulaDuplicats();
    } else if (pageType[3] === 'quadre-general') {
      taulaQuadreGeneral();
    } else {
      botonsEstat(pageType[2]);
      cargarTabla(pageType[2], 2);
    }
  } else if (pageType[2] === 'represaliats') {
    botonsEstat(pageType[1]);
    cargarTabla(pageType[1], 1);
    if (pageType[3] === 'llistat-processats') {
      taulaProcessats();
    } else if (pageType[3] === 'llistat-afusellats') {
      taulaAfusellats();
    } else if (pageType[3] === 'llistat-preso-model') {
      taulaPresoModel();
    } else {
      botonsEstat(pageType[2]);
      cargarTabla(pageType[2], 2);
    }
  } else if (pageType[2] === 'exiliats-deportats') {
    botonsEstat(pageType[1]);
    cargarTabla(pageType[1], 1);
    if (pageType[3] === 'llistat-exiliats') {
      taulaExiliats();
    } else if (pageType[3] === 'llistat-deportats') {
      taulaDeportats();
    } else {
      botonsEstat(pageType[2]);
      cargarTabla(pageType[2], 2);
    }
  } else if (pageType[2] === 'cost-huma') {
    botonsEstat(pageType[1]);
    cargarTabla(pageType[1], 1);
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
  } else if (pageType[2] === 'empresonaments') {
    formDetingutsGuardiaUrbana(Number(pageType[4]), Number(pageType[5]));
  } else if (pageType[2] === 'empresonaments-preso-model') {
    formPresoModel(Number(pageType[4]), Number(pageType[5]));
  } else if (pageType[2] === 'empresonaments-comite-solidaritat') {
    formcomiteSolidaritat(Number(pageType[4]), Number(pageType[5]));
  } else if (pageType[2] === 'empresonaments-comite-relacions-solidaritat') {
    formcomiteRelacionsSolidaritat(Number(pageType[4]), Number(pageType[5]));
  }
}
