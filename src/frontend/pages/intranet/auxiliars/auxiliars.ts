import { taulaDadesUsuaris } from './taulaUsuaris';
import { transmissioDadesDB } from '../../../services/fetchData/transmissioDades';
import { getPageType } from '../../../services/url/splitUrl';
import { taulaMunicipis } from './taulaMunicipis';
import { taulaPartits } from './taulaPartits';
import { taulaSindicats } from './taulaSindicats';
import { avatarUsuari } from './avatarUsuari';
import { taulaComarques } from './taulaComarques';
import { taulaProvincies } from './taulaProvincies';
import { taulaComunitats } from './taulaComunitats';
import { taulaEstats } from './taulaEstats';
import { taulaCategoriesRepressio } from './taulaCategoriesRepressio';
import { API_URLS } from '../../../services/api/ApiUrls';
import { formMunicipi } from './formMunicipi';

export function auxiliars() {
  const url = window.location.href;
  const pageType = getPageType(url);

  if (pageType[2] === 'llistat-usuaris') {
    taulaDadesUsuaris();
  } else if (pageType[2] === 'nou-usuari') {
    const peli = document.getElementById('usuariForm');
    if (peli) {
      peli.addEventListener('submit', function (event) {
        transmissioDadesDB(event, 'POST', 'usuariForm', '/api/auxiliars/post/usuari', true);
      });
    }
  } else if (pageType[2] === 'modifica-usuari') {
    const peli = document.getElementById('usuariForm');
    if (peli) {
      peli.addEventListener('submit', function (event) {
        transmissioDadesDB(event, 'PUT', 'usuariForm', '/api/auxiliars/put/usuari');
      });
    }
  } else if (pageType[2] === 'llistat-municipis') {
    taulaMunicipis();
  } else if (pageType[2] === 'llistat-partits-politics') {
    taulaPartits();
  } else if (pageType[2] === 'llistat-sindicats') {
    taulaSindicats();
  } else if (pageType[2] === 'nou-avatar-usuari') {
    avatarUsuari();
  } else if (pageType[2] === 'llistat-comarques') {
    taulaComarques();
  } else if (pageType[2] === 'llistat-provincies') {
    taulaProvincies();
  } else if (pageType[2] === 'llistat-comunitats') {
    taulaComunitats();
  } else if (pageType[2] === 'llistat-estats') {
    taulaEstats();
  } else if (pageType[2] === 'nou-carrec-empresa') {
    const causaMortForm = document.getElementById('causaMortForm');
    if (causaMortForm) {
      causaMortForm.addEventListener('submit', function (event) {
        transmissioDadesDB(event, 'POST', 'causaMortForm', API_URLS.POST.CARREC_EMPRESA, true);
      });
    }
  } else if (pageType[2] === 'modifica-carrec-empresa') {
    const causaMortForm = document.getElementById('causaMortForm');
    if (causaMortForm) {
      causaMortForm.addEventListener('submit', function (event) {
        transmissioDadesDB(event, 'PUT', 'causaMortForm', API_URLS.PUT.CARREC_EMPRESA);
      });
    }
  } else if (pageType[2] === 'nou-ofici') {
    const oficiForm = document.getElementById('oficiForm');
    if (oficiForm) {
      oficiForm.addEventListener('submit', function (event) {
        transmissioDadesDB(event, 'POST', 'oficiForm', API_URLS.POST.OFICI, true);
      });
    }
  } else if (pageType[2] === 'modifica-ofici') {
    const oficiForm = document.getElementById('oficiForm');
    if (oficiForm) {
      oficiForm.addEventListener('submit', function (event) {
        transmissioDadesDB(event, 'PUT', 'oficiForm', API_URLS.PUT.OFICI);
      });
    }
  } else if (pageType[2] === 'nou-sub-sector-economic') {
    const subSectorForm = document.getElementById('subSectorForm');
    if (subSectorForm) {
      subSectorForm.addEventListener('submit', function (event) {
        transmissioDadesDB(event, 'POST', 'subSectorForm', API_URLS.POST.SUB_SECTOR_ECONOMIC, true);
      });
    }
  } else if (pageType[2] === 'modifica-sub-sector-economic') {
    const subSectorForm = document.getElementById('subSectorForm');
    if (subSectorForm) {
      subSectorForm.addEventListener('submit', function (event) {
        transmissioDadesDB(event, 'PUT', 'subSectorForm', API_URLS.PUT.SUB_SECTOR_ECONOMIC);
      });
    }
  } else if (pageType[2] === 'llistat-categories-repressio') {
    taulaCategoriesRepressio();
  } else if (pageType[2] === 'modifica-categoria-repressio') {
    const categoriesForm = document.getElementById('categoriesForm');
    if (categoriesForm) {
      categoriesForm.addEventListener('submit', function (event) {
        transmissioDadesDB(event, 'PUT', 'categoriesForm', API_URLS.PUT.CATEGORIA_REPRESSIO);
      });
    }
  } else if (pageType[2] === 'nova-categoria-repressio') {
    const categoriesForm = document.getElementById('categoriesForm');
    if (categoriesForm) {
      categoriesForm.addEventListener('submit', function (event) {
        transmissioDadesDB(event, 'POST', 'categoriesForm', API_URLS.POST.CATEGORIA_REPRESSIO, true);
      });
    }
  } else if (pageType[2] === 'nova-causa-mort') {
    const causaMortForm = document.getElementById('causaMortForm');
    if (causaMortForm) {
      causaMortForm.addEventListener('submit', function (event) {
        transmissioDadesDB(event, 'POST', 'causaMortForm', API_URLS.POST.CAUSA_MORT, true);
      });
    }
  } else if (pageType[2] === 'modifica-causa-mort') {
    const causaMortForm = document.getElementById('causaMortForm');
    if (causaMortForm) {
      causaMortForm.addEventListener('submit', function (event) {
        transmissioDadesDB(event, 'PUT', 'causaMortForm', API_URLS.PUT.CAUSA_MORT);
      });
    }
  } else if (pageType[2] === 'nova-comarca') {
    const formComarca = document.getElementById('formComarca');
    if (formComarca) {
      formComarca.addEventListener('submit', function (event) {
        transmissioDadesDB(event, 'POST', 'formComarca', API_URLS.POST.MUNICIPI_COMARCA, true);
      });
    }
  } else if (pageType[2] === 'modifica-comarca') {
    const formComarca = document.getElementById('formComarca');
    if (formComarca) {
      formComarca.addEventListener('submit', function (event) {
        transmissioDadesDB(event, 'PUT', 'formComarca', API_URLS.PUT.MUNICIPI_COMARCA);
      });
    }
  } else if (pageType[2] === 'nova-comunitat') {
    const formComunitat = document.getElementById('formComunitat');
    if (formComunitat) {
      formComunitat.addEventListener('submit', function (event) {
        transmissioDadesDB(event, 'POST', 'formComunitat', API_URLS.POST.MUNICIPI_COMUNITAT, true);
      });
    }
  } else if (pageType[2] === 'modifica-comunitat') {
    const formComunitat = document.getElementById('formComunitat');
    if (formComunitat) {
      formComunitat.addEventListener('submit', function (event) {
        transmissioDadesDB(event, 'PUT', 'formComunitat', API_URLS.PUT.MUNICIPI_COMUNITAT);
      });
    }
  } else if (pageType[2] === 'modifica-estat') {
    const formEstat = document.getElementById('formEstat');
    if (formEstat) {
      formEstat.addEventListener('submit', function (event) {
        transmissioDadesDB(event, 'PUT', 'formEstat', API_URLS.PUT.MUNICIPI_ESTAT);
      });
    }
  } else if (pageType[2] === 'nou-estat') {
    const formEstat = document.getElementById('formEstat');
    if (formEstat) {
      formEstat.addEventListener('submit', function (event) {
        transmissioDadesDB(event, 'POST', 'formEstat', API_URLS.POST.MUNICIPI_ESTAT, true);
      });
    }
  } else if (pageType[2] === 'modifica-municipi') {
    formMunicipi(true, Number(pageType[3]));
  } else if (pageType[2] === 'nou-municipi') {
    formMunicipi(false);
  }
}
