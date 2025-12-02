import { mostrarBotonsNomesAdmin } from '../../../components/mostrarBotoAdmin/mostrarBotoAdmin';
import { getPageType } from '../../../services/url/splitUrl';
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
import { formDetingutsGuardiaUrbana } from './tipusRepressio/detingutsGuardiaUrbana';
import { formPresoModel } from './tipusRepressio/presoModel';
import { formcomiteSolidaritat } from './tipusRepressio/comiteSolidaritat';
import { formcomiteRelacionsSolidaritat } from './tipusRepressio/comiteRelacionsSolidaritat';
import { taulaRevisio } from './taulaRevisio';
import { taulaRevisioGeolocalitzacio } from './taulaRevisioGeolocalitzacio';
import { taulaPendentsAjuntament } from './taulaPendentsAjuntament';
import { taulaPaginaTots } from './taulaPaginaTots';
import { taulaPaginaRepresaliats } from './taulaPaginaRepresaliats';
import { taulaPaginaExiliats } from './taulaPaginaExiliats';
import { taulaPaginaCostHuma } from './taulaPaginaCostHuma';
import { formDetingutConsellGuerra } from './tipusRepressio/processat';

export function baseDadesIntranet() {
  const url = window.location.href;
  const pageType = getPageType(url);

  const section = pageType[2];
  const sub = pageType[3];

  mostrarBotonsNomesAdmin();

  // Pagines

  if (section === 'general') {
    switch (sub) {
      case null:
        taulaPaginaTots();
        break;
      case 'llistat-duplicats':
        taulaDuplicats();
        break;
      case 'quadre-general':
        taulaQuadreGeneral();
        break;
      case 'llistat-revisio':
        taulaRevisio();
        break;
      case 'llistat-revisio-geolocalitzacio':
        taulaRevisioGeolocalitzacio();
        break;
      default:
        taulaPaginaTots(); // fallback sensato
        break;
    }
    return;
  }

  if (section === 'represaliats') {
    switch (sub) {
      case null:
        taulaPaginaRepresaliats();
        break;
      case 'llistat-processats':
        taulaProcessats();
        break;
      case 'llistat-afusellats':
        taulaAfusellats();
        break;
      case 'llistat-preso-model':
        taulaPresoModel();
        break;
      case 'llistat-pendents':
        taulaPendentsAjuntament();
        break;
      default:
        taulaPaginaRepresaliats(); // fallback sensato
        break;
    }
    return;
  }

  if (section === 'exiliats-deportats') {
    switch (sub) {
      case null:
        taulaPaginaExiliats();
        break;
      case 'llistat-exiliats':
        taulaExiliats();
        break;
      case 'llistat-deportats':
        taulaDeportats();
        break;
      default:
        taulaPaginaExiliats(); // fallback sensato
        break;
    }
    return;
  }

  if (section === 'cost-huma') {
    switch (sub) {
      case null:
        taulaPaginaCostHuma();
        break;
      case 'llistat-morts-al-front':
        taulaMortsFronts();
        break;
      case 'llistat-morts-civils':
        taulaMortsCivils();
        break;
      case 'llistat-represalia-republicana':
        taulaRepresaliaRepublicana();
        break;
      default:
        taulaPaginaCostHuma(); // fallback sensato
        break;
    }
    return;
  }

  // Ramas “directas” que no son listados
  if (section === 'modifica-fitxa') {
    modificaFitxa(Number(sub));
    return;
  }
  if (section === 'nova-fitxa') {
    modificaFitxa();
    return;
  }
  if (section === 'modifica-repressio') {
    formTipusRepressio();
    return;
  }
  if (section === 'empresonaments') {
    formDetingutsGuardiaUrbana(Number(pageType[4]), Number(pageType[5]));
    return;
  }
  if (section === 'empresonaments-preso-model') {
    formPresoModel(Number(pageType[4]), Number(pageType[5]));
    return;
  }
  if (section === 'detinguts-consell-guerra') {
    formDetingutConsellGuerra(Number(pageType[4]), Number(pageType[5]));
    return;
  }
  if (section === 'empresonaments-comite-solidaritat') {
    formcomiteSolidaritat(Number(pageType[4]), Number(pageType[5]));
    return;
  }
  if (section === 'empresonaments-comite-relacions-solidaritat') {
    formcomiteRelacionsSolidaritat(Number(pageType[4]), Number(pageType[5]));
    return;
  }
}
