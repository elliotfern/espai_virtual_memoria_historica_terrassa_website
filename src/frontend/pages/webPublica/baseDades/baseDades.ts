import { iniciarBuscadorCostHuma } from '../../../components/filtreRecerca/pages/cost-huma';
import { iniciarBuscadorExiliats } from '../../../components/filtreRecerca/pages/exili';
import { iniciarBuscadorGeneral } from '../../../components/filtreRecerca/pages/general';
import { iniciarBuscadorRepresaliats } from '../../../components/filtreRecerca/pages/represaliats';
import { renderMapaGeolocalitzacio } from '../../../components/geolocalitzacio/mapaGeneral';
import { getPageType } from '../../../services/url/splitUrl';

export function baseDadesWebPublica() {
  const url = window.location.href;
  const pageType = getPageType(url);

  if (pageType[1] === 'general' || pageType[1] === 'general#filtre') {
    iniciarBuscadorGeneral();
  } else if (pageType[1] === 'represaliats' || pageType[1] === 'represaliats#filtre') {
    iniciarBuscadorRepresaliats();
  } else if (pageType[1] === 'exiliats-deportats' || pageType[1] === 'exiliats-deportats#filtre') {
    iniciarBuscadorExiliats();
  } else if (pageType[1] === 'cost-huma' || pageType[1] === 'cost-huma#filtre') {
    iniciarBuscadorCostHuma();
  } else if (pageType[1] === 'geolocalitzacio') {
    renderMapaGeolocalitzacio();
  }
}
