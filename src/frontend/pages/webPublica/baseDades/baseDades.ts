import { iniciarBuscadorCostHuma } from '../../../components/filtreRecerca/pages/cost-huma';
import { iniciarBuscadorExiliats } from '../../../components/filtreRecerca/pages/exili';
import { botonsEstat } from '../../../components/taulaDades/botonsEstat';
import { cargarTabla } from '../../../components/taulaDades/taulaDades';
import { getPageType } from '../../../services/url/splitUrl';

export function baseDadesWebPublica() {
  const url = window.location.href;
  const pageType = getPageType(url);

  if (pageType[1] === 'general' || pageType[1] === 'general#filtre') {
    botonsEstat(pageType[1]);
    cargarTabla(pageType[1], 1);
  } else if (pageType[1] === 'represaliats' || pageType[1] === 'represaliats#filtre') {
    botonsEstat(pageType[1]);
    cargarTabla(pageType[1], 1);
  } else if (pageType[1] === 'exiliats-deportats' || pageType[1] === 'exiliats-deportats#filtre') {
    iniciarBuscadorExiliats();
  } else if (pageType[1] === 'cost-huma' || pageType[1] === 'cost-huma#filtre') {
    iniciarBuscadorCostHuma();
  }
}
