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
        transmissioDadesDB(event, 'POST', 'causaMortForm', '/api/auxiliars/post/carrec_empresa', true);
      });
    }
  } else if (pageType[2] === 'modifica-carrec-empresa') {
    const causaMortForm = document.getElementById('causaMortForm');
    if (causaMortForm) {
      causaMortForm.addEventListener('submit', function (event) {
        transmissioDadesDB(event, 'PUT', 'causaMortForm', '/api/auxiliars/put/carrec_empresa');
      });
    }
  } else if (pageType[2] === 'nou-ofici') {
    const oficiForm = document.getElementById('oficiForm');
    if (oficiForm) {
      oficiForm.addEventListener('submit', function (event) {
        transmissioDadesDB(event, 'POST', 'oficiForm', '/api/auxiliars/post/ofici', true);
      });
    }
  } else if (pageType[2] === 'modifica-ofici') {
    const oficiForm = document.getElementById('oficiForm');
    if (oficiForm) {
      oficiForm.addEventListener('submit', function (event) {
        transmissioDadesDB(event, 'PUT', 'oficiForm', '/api/auxiliars/put/ofici');
      });
    }
  } else if (pageType[2] === 'nou-sub-sector-economic') {
    const subSectorForm = document.getElementById('subSectorForm');
    if (subSectorForm) {
      subSectorForm.addEventListener('submit', function (event) {
        transmissioDadesDB(event, 'POST', 'subSectorForm', '/api/auxiliars/post/sub_sector_economic', true);
      });
    }
  } else if (pageType[2] === 'modifica-sub-sector-economic') {
    const subSectorForm = document.getElementById('subSectorForm');
    if (subSectorForm) {
      subSectorForm.addEventListener('submit', function (event) {
        transmissioDadesDB(event, 'PUT', 'subSectorForm', '/api/auxiliars/put/sub_sector_economic');
      });
    }
  }
}
