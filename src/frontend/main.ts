import 'bootstrap/dist/css/bootstrap.min.css';
import './estils/style.css';
import 'bootstrap';
import { TaulaDadesFonts } from './components/fontsDocumentals/taulaDadesFonts';
import { nameUser } from './components/userName/userName';
import { getPageType } from './services/url/splitUrl';
import { intranet } from './pages/intranet/intranet';
import { loginPage } from './services/auth/login';
import { initCookieConsent } from './components/bannerCookies/cookie';
import { baseDadesWebPublica } from './pages/webPublica/baseDades/baseDades';
import { fitxaRepresaliat } from './components/fitxaRepresaliat';
import { equip } from './pages/webPublica/equip/equip';
import { homePage } from './pages/webPublica/homePage/homePage';
import { credits } from './pages/webPublica/credits/credits';

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
    const lang = 'ca';
    homePage(lang);
  } else if (pageType[0] === 'credits') {
    const lang = 'ca';
    credits(lang);
  } else if (pageType[0] === 'fonts-documentals') {
    TaulaDadesFonts();
  } else if (pageType[0] === 'equip') {
    const lang = 'ca';
    const slug = pageType[1];
    equip(lang, slug);
  }

  if (pageType[0] === 'es') {
    const lang = 'es';
    const slug = pageType[2];
    if (pageType[1] === 'equip') {
      equip(lang, slug);
    } else if (pageType[1] === 'inici') {
      homePage(lang);
    } else if (pageType[1] === 'credits') {
      credits(lang);
    }
  }

  if (pageType[0] === 'en') {
    const lang = 'en';
    const slug = pageType[2];
    if (pageType[1] === 'equip') {
      equip(lang, slug);
    } else if (pageType[1] === 'inici') {
      homePage(lang);
    } else if (pageType[1] === 'credits') {
      credits(lang);
    }
  }

  if (pageType[0] === 'it') {
    const lang = 'it';
    const slug = pageType[2];
    if (pageType[1] === 'equip') {
      equip(lang, slug);
    } else if (pageType[1] === 'inici') {
      homePage(lang);
    } else if (pageType[1] === 'credits') {
      credits(lang);
    }
  }

  if (pageType[0] === 'fr') {
    const lang = 'fr';
    const slug = pageType[2];
    if (pageType[1] === 'equip') {
      equip(lang, slug);
    } else if (pageType[1] === 'inici') {
      homePage(lang);
    } else if (pageType[1] === 'credits') {
      credits(lang);
    }
  }

  if (pageType[0] === 'pt') {
    const lang = 'pt';
    const slug = pageType[2];
    if (pageType[1] === 'equip') {
      equip(lang, slug);
    } else if (pageType[1] === 'inici') {
      homePage(lang);
    } else if (pageType[1] === 'credits') {
      credits(lang);
    }
  }
});
