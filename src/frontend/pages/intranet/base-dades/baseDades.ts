import { mostrarBotonsNomesAdmin } from '../../../components/mostrarBotoAdmin/mostrarBotoAdmin';
import { getPageType } from '../../../services/url/splitUrl';
import { cargarTabla } from '../../../components/taulaDades/taulaDades';
import { botonsEstat } from '../../../components/taulaDades/botonsEstat';
import { modificaFitxa } from '../../../components/modificaFitxaRepresaliat/modificaFitxa';

export function baseDadesIntranet() {
  const url = window.location.href;
  const pageType = getPageType(url);

  mostrarBotonsNomesAdmin();

  if (pageType[2] === 'general') {
    botonsEstat(pageType[2]);
    cargarTabla(pageType[2], 2);
  } else if (pageType[2] === 'represaliats') {
    botonsEstat(pageType[2]);
    cargarTabla(pageType[2], 2);
  } else if (pageType[2] === 'exiliats-deportats') {
    botonsEstat(pageType[2]);
    cargarTabla(pageType[2], 2);
  } else if (pageType[2] === 'cost-huma') {
    botonsEstat(pageType[2]);
    cargarTabla(pageType[2], 2);
  } else if (pageType[2] === 'modifica-fitxa') {
    modificaFitxa(Number(pageType[3]));
  }
}
