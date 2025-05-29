import { initButtons } from './components/fitxaRepresaliat/fitxaRepresaliat';
import { cargarTabla } from './components/taulaDades/taulaDades';
import { botonsEstat } from './components/taulaDades/botonsEstat';
import { initBuscador } from './components/cercadorHomepage/cercadorPaginaInici';
import { TaulaDadesFonts } from './components/fontsDocumentals/taulaDadesFonts';
import { nameUser } from './components/userName/userName';
import { getPageType } from './services/url/splitUrl';
import { intranet } from './pages/intranet/intranet';
import { login } from './services/auth/login';
import 'bootstrap/dist/css/bootstrap.min.css';
import './estils/style.css';
import 'bootstrap';

nameUser();

const url = window.location.href;
const pageType = getPageType(url);
console.log(pageType);

document.addEventListener('DOMContentLoaded', () => {
  if (pageType[0] === 'acces') {
    const btnLogin = document.querySelector('#btnLogin') as HTMLButtonElement;

    btnLogin?.addEventListener('click', (event: Event) => {
      event.preventDefault();
      const userName = (document.querySelector('#username') as HTMLInputElement)?.value;
      const password = (document.querySelector('#password') as HTMLInputElement)?.value;

      if (userName && password) {
        login(userName, password);
      } else {
        console.log('Faltan datos para iniciar sesión.');
      }
    });
  } else if (pageType[0] === 'gestio') {
    intranet();
  } else if (pageType[1] === 'general' || pageType[1] === 'general#filtre') {
    botonsEstat(pageType[1]);
    cargarTabla(pageType[1], 1);
  } else if (pageType[1] === 'represaliats' || pageType[1] === 'represaliats#filtre') {
    botonsEstat(pageType[1]);
    cargarTabla(pageType[1], 1);
  } else if (pageType[1] === 'exiliats-deportats' || pageType[1] === 'exiliats-deportats#filtre') {
    botonsEstat(pageType[1]);
    cargarTabla(pageType[1], 1);
  } else if (pageType[1] === 'cost-huma' || pageType[1] === 'cost-huma#filtre') {
    botonsEstat(pageType[1]);
    cargarTabla(pageType[1], 1);
  } else if (pageType[0] === 'fitxa') {
    const id = pageType[1];
    initButtons(id); // Pasar el id
  } else if (pageType[0] === 'inici') {
    initBuscador();
  } else if (pageType[0] === 'fonts-documentals') {
    TaulaDadesFonts();
  }
});
