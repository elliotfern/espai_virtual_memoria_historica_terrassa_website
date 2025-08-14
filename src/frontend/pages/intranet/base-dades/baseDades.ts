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
import { taulaRevisio } from './taulaRevisio';

export function baseDadesIntranet() {
  const url = window.location.href;
  const pageType = getPageType(url);
  // Estructura esperada: [ 'gestio', 'base-dades', section, sub? , ... ]
  const section = pageType[2]; // 'general' | 'represaliats' | 'exiliats-deportats' | 'cost-huma' | ...
  const sub = pageType[3]; // 'llistat-*' | 'quadre-general' | id | undefined

  mostrarBotonsNomesAdmin();

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
  if (section === 'empresonaments-comite-solidaritat') {
    formcomiteSolidaritat(Number(pageType[4]), Number(pageType[5]));
    return;
  }
  if (section === 'empresonaments-comite-relacions-solidaritat') {
    formcomiteRelacionsSolidaritat(Number(pageType[4]), Number(pageType[5]));
    return;
  }

  // A partir de aquí, solo secciones de listados en INTRANET (context = 2)
  // Llamamos UNA sola vez a los botones y al listado base de la sección
  const context = 2;
  botonsEstat(section);

  // Valor por defecto para “completat” en intranet: 3 (totes)
  // (ajústalo si quieres otro por defecto)
  cargarTabla(section, context, 3);

  // Subrutas específicas: pintan tablas/consultas concretas SIN volver a crear botones
  if (section === 'general') {
    if (sub === 'llistat-duplicats') {
      taulaDuplicats();
    } else if (sub === 'quadre-general') {
      taulaQuadreGeneral();
    } else if (sub === 'llistat-revisio') {
      taulaRevisio();
    }
    return;
  }

  if (section === 'represaliats') {
    if (sub === 'llistat-processats') {
      taulaProcessats();
    } else if (sub === 'llistat-afusellats') {
      taulaAfusellats();
    } else if (sub === 'llistat-preso-model') {
      taulaPresoModel();
    }
    return;
  }

  if (section === 'exiliats-deportats') {
    if (sub === 'llistat-exiliats') {
      taulaExiliats();
    } else if (sub === 'llistat-deportats') {
      taulaDeportats();
    }
    return;
  }

  if (section === 'cost-huma') {
    if (sub === 'llistat-morts-al-front') {
      taulaMortsFronts();
    } else if (sub === 'llistat-morts-civils') {
      taulaMortsCivils();
    } else if (sub === 'llistat-represalia-republicana') {
      taulaRepresaliaRepublicana();
    }
    return;
  }
}
