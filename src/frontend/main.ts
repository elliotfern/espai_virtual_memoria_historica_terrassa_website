import { login } from './services/auth/auth';
import { logout } from './services/cookies/cookiesUtils';
import { nameUser } from './components/userName/userName';
import { initButtons } from './components/fitxaRepresaliat/fitxaRepresaliat';
import { cargarTabla } from './components/taulaDades/taulaDades';
import { botonsEstat } from './components/taulaDades/botonsEstat';
import { initBuscador } from './components/cercadorHomepage/cercadorPaginaInici';
import { TaulaDadesFonts } from './components/fontsDocumentals/taulaDadesFonts';
import { transmissioDadesDB } from './services/fetchData/transmissioDades';
import { mostrarBotonsNomesAdmin } from './components/mostrarBotoAdmin/mostrarBotoAdmin';
import { auxiliars } from './pages/auxiliars/auxiliars';
import { getPageType } from './services/url/splitUrl';

import 'bootstrap/dist/css/bootstrap.min.css';
import './estils/style.css';
import 'bootstrap';

const url = window.location.href;
const pageType = getPageType(url);
console.log(pageType);

document.addEventListener('DOMContentLoaded', () => {
  mostrarBotonsNomesAdmin();

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

  const userIdFromStorage = localStorage.getItem('user_id');
  if (userIdFromStorage) {
    nameUser(userIdFromStorage).catch((error) => {
      console.error('Error al llamar a nameUser desde localStorage:', error);
    });
  }

  const btnLogout = document.querySelector('#btnSortir') as HTMLButtonElement;
  btnLogout?.addEventListener('click', () => {
    logout();
  });

  const contieneGestio = window.location.href.includes('gestio');
  let context: number;
  if (contieneGestio) {
    context = 2;
  } else {
    context = 1;
  }

  if (pageType[1] === 'tots' || pageType[1] === 'general') {
    if (pageType[1] === 'tots') {
      botonsEstat(pageType[1]);
      cargarTabla(pageType[1], context);
    } else {
      botonsEstat(pageType[1]);
      cargarTabla(pageType[1], context);
    }
  } else if (pageType[1] === 'represaliats') {
    botonsEstat(pageType[1]);
    cargarTabla(pageType[1], context); // También cargar para afusellats
  } else if (pageType[1] === 'exiliats' || pageType[1] === 'exiliats-deportats') {
    botonsEstat(pageType[1]);
    cargarTabla(pageType[1], context); // También cargar para exiliats
  } else if (pageType[1] === 'cost-huma') {
    botonsEstat(pageType[1]);
    cargarTabla(pageType[1], context); // También cargar para exiliats
  } else if (pageType[0] === 'fitxa') {
    const id = pageType[1];
    initButtons(id); // Pasar el id
  } else if (pageType[0] === 'inici') {
    initBuscador();
  } else if (pageType[1] === 'fonts-documentals') {
    TaulaDadesFonts();
  } else if (pageType[1] === 'crear-arxiu') {
    const llibre = document.getElementById('arxiuForm');
    if (llibre) {
      // Lanzar actualizador de datos
      llibre.addEventListener('submit', function (event) {
        transmissioDadesDB(event, 'POST', 'arxiuForm', '/api/fonts_documentals/post/arxiu');
      });
    }
  } else if (pageType[1] === 'auxiliars') {
    auxiliars();
  }
});
