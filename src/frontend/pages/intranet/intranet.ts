import { mostrarBotonsNomesAdmin } from '../../components/mostrarBotoAdmin/mostrarBotoAdmin';
import { getPageType } from '../../services/url/splitUrl';
import { auxiliars } from './auxiliars/auxiliars';
import { cargarTabla } from '../../components/taulaDades/taulaDades';
import { botonsEstat } from '../../components/taulaDades/botonsEstat';
import { transmissioDadesDB } from '../../services/fetchData/transmissioDades';

export function intranet() {
  const url = window.location.href;
  const pageType = getPageType(url);

  mostrarBotonsNomesAdmin();

  if (pageType[1] === 'auxiliars') {
    auxiliars();
  } else if (pageType[1] === 'tots') {
    botonsEstat(pageType[1]);
    cargarTabla(pageType[1], 2);
  } else if (pageType[1] === 'represaliats') {
    botonsEstat(pageType[1]);
    cargarTabla(pageType[1], 2);
  } else if (pageType[1] === 'exiliats') {
    botonsEstat(pageType[1]);
    cargarTabla(pageType[1], 2);
  } else if (pageType[1] === 'cost-huma') {
    botonsEstat(pageType[1]);
    cargarTabla(pageType[1], 2);
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
