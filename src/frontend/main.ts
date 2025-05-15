import { initButtons } from './components/fitxaRepresaliat/fitxaRepresaliat';
import { cargarTabla } from './components/taulaDades/taulaDades';
import { botonsEstat } from './components/taulaDades/botonsEstat';
import { initBuscador } from './components/cercadorHomepage/cercadorPaginaInici';
import { TaulaDadesFonts } from './components/fontsDocumentals/taulaDadesFonts';

import { getPageType } from './services/url/splitUrl';
import { intranet } from './pages/intranet/intranet';

import 'bootstrap/dist/css/bootstrap.min.css';
import './estils/style.css';
import 'bootstrap';

const url = window.location.href;
const pageType = getPageType(url);
console.log(pageType);

document.addEventListener('DOMContentLoaded', () => {
  if (pageType[0] === 'gestio') {
    intranet();
  } else if (pageType[1] === 'general') {
    botonsEstat(pageType[1]);
    cargarTabla(pageType[1], 1);
  } else if (pageType[1] === 'represaliats') {
    botonsEstat(pageType[1]);
    cargarTabla(pageType[1], 1); // También cargar para afusellats
  } else if (pageType[1] === 'exiliats-deportats') {
    botonsEstat(pageType[1]);
    cargarTabla(pageType[1], 1); // También cargar para exiliats
  } else if (pageType[1] === 'cost-huma') {
    botonsEstat(pageType[1]);
    cargarTabla(pageType[1], 1); // También cargar para exiliats
  } else if (pageType[0] === 'fitxa') {
    const id = pageType[1];
    initButtons(id); // Pasar el id
  } else if (pageType[0] === 'inici') {
    initBuscador();
  } else if (pageType[0] === 'fonts-documentals') {
    TaulaDadesFonts();
  }
});
