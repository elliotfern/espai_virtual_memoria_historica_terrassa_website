import { mostrarBotonsNomesAdmin } from '../../components/mostrarBotoAdmin/mostrarBotoAdmin';
import { getPageType } from '../../services/url/splitUrl';
import { auxiliars } from './auxiliars/auxiliars';
import { transmissioDadesDB } from '../../services/fetchData/transmissioDades';
import { baseDadesIntranet } from './base-dades/baseDades';

export function intranet() {
  const url = window.location.href;
  const pageType = getPageType(url);

  mostrarBotonsNomesAdmin();

  if (pageType[1] === 'auxiliars') {
    auxiliars();
  } else if (pageType[1] === 'base-dades') {
    baseDadesIntranet();
  } else if (pageType[1] === 'crear-arxiu') {
    const llibre = document.getElementById('arxiuForm');
    if (llibre) {
      // Lanzar actualizador de datos
      llibre.addEventListener('submit', function (event) {
        transmissioDadesDB(event, 'POST', 'arxiuForm', '/api/fonts_documentals/post/arxiu');
      });
    }
  }
}
