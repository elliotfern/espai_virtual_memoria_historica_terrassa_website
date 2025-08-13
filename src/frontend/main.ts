import 'bootstrap/dist/css/bootstrap.min.css';
import './estils/style.css';
import 'bootstrap';

import { initBuscador } from './components/cercadorHomepage/cercadorPaginaInici';
import { TaulaDadesFonts } from './components/fontsDocumentals/taulaDadesFonts';
import { nameUser } from './components/userName/userName';
import { getPageType } from './services/url/splitUrl';
import { intranet } from './pages/intranet/intranet';
import { loginPage } from './services/auth/login';
import { initCookieConsent } from './components/bannerCookies/cookie';
import { baseDadesWebPublica } from './pages/webPublica/baseDades/baseDades';
import { fitxaRepresaliat } from './components/fitxaRepresaliat';

nameUser();

const url = window.location.href;
const pageType = getPageType(url);
console.log(pageType);

document.addEventListener('DOMContentLoaded', () => {
  initCookieConsent();

  if (pageType[0] === 'acces') {
    loginPage();
  } else if (pageType[0] === 'gestio') {
    intranet();
  } else if (pageType[0] === 'base-dades') {
    baseDadesWebPublica();
  } else if (pageType[0] === 'fitxa') {
    // const id = pageType[1];
    const slug = pageType[1];
    fitxaRepresaliat(slug);
    // initButtons(id); // Pasar el id
  } else if (pageType[0] === 'inici') {
    initBuscador();
  } else if (pageType[0] === 'fonts-documentals') {
    TaulaDadesFonts();
  }
});
