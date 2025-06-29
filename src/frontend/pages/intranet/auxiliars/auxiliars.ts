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
import { formEspai } from './formEspai';
import { formSubSector } from './formSubSector';
import { formCategoriaRepressio } from './formCategoriaRepressio';
import { taulaCausaDefuncio } from './taulaCausaDefuncio';
import { taulaEspais } from './taulaEspais';
import { taulaEstatsCivils } from './taulaEstatsCivils';
import { taulaNivellsEstudis } from './taulaNivellsEstudis';
import { taulaOficis } from './taulaOficis';
import { taulaCarrecsEmpresa } from './taulaCarrecsEmpresa';
import { taulaSubsectorsEconomics } from './taulaSubsectorsEconomics';
import { taulaSectorsEconomics } from './taulaSectorsEconomics';
import { taulaAcusacionsJudicials } from './taulaAcusacionsJudicials';
import { taulaBandolsGuerra } from './taulaBandolsGuerra';
import { taulaCondicionsMilitars } from './taulaCondicionsMilitars';
import { taulaCossosMilitars } from './taulaCossosMilitars';
import { formEmpresa } from './formEmpresa';

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
  } else if (pageType[2] === 'llistat-categories-repressio') {
    taulaCategoriesRepressio();
  } else if (pageType[2] === 'modifica-categoria-repressio') {
    formCategoriaRepressio(true, Number(pageType[3]));
  } else if (pageType[2] === 'nova-categoria-repressio') {
    formCategoriaRepressio(false);
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
  } else if (pageType[2] === 'nova-acusacio') {
    const acusacioForm = document.getElementById('acusacioForm');
    if (acusacioForm) {
      acusacioForm.addEventListener('submit', function (event) {
        transmissioDadesDB(event, 'POST', 'acusacioForm', API_URLS.POST.ACUSACIO_JUDICIAL, true);
      });
    }
  } else if (pageType[2] === 'modificacio-acusacio') {
    const acusacioForm = document.getElementById('acusacioForm');
    if (acusacioForm) {
      acusacioForm.addEventListener('submit', function (event) {
        transmissioDadesDB(event, 'PUT', 'acusacioForm', API_URLS.PUT.ACUSACIO_JUDICIAL);
      });
    }
  } else if (pageType[2] === 'modifica-bandol') {
    const bandolForm = document.getElementById('bandolForm');
    if (bandolForm) {
      bandolForm.addEventListener('submit', function (event) {
        transmissioDadesDB(event, 'PUT', 'bandolForm', API_URLS.PUT.BANDOL);
      });
    }
  } else if (pageType[2] === 'nou-bandol') {
    const bandolForm = document.getElementById('bandolForm');
    if (bandolForm) {
      bandolForm.addEventListener('submit', function (event) {
        transmissioDadesDB(event, 'POST', 'bandolForm', API_URLS.POST.BANDOL, true);
      });
    }
  } else if (pageType[2] === 'nova-condicio-militar') {
    const condicioForm = document.getElementById('condicioForm');
    if (condicioForm) {
      condicioForm.addEventListener('submit', function (event) {
        transmissioDadesDB(event, 'POST', 'condicioForm', API_URLS.POST.CONDICIO_MILITAR, true);
      });
    }
  } else if (pageType[2] === 'modifica-condicio-militar') {
    const condicioForm = document.getElementById('condicioForm');
    if (condicioForm) {
      condicioForm.addEventListener('submit', function (event) {
        transmissioDadesDB(event, 'PUT', 'condicioForm', API_URLS.PUT.CONDICIO_MILITAR);
      });
    }
  } else if (pageType[2] === 'modifica-cos-militar') {
    const condicioForm = document.getElementById('condicioForm');
    if (condicioForm) {
      condicioForm.addEventListener('submit', function (event) {
        transmissioDadesDB(event, 'PUT', 'condicioForm', API_URLS.PUT.COS_MILITAR);
      });
    }
  } else if (pageType[2] === 'nou-cos-militar') {
    const condicioForm = document.getElementById('condicioForm');
    if (condicioForm) {
      condicioForm.addEventListener('submit', function (event) {
        transmissioDadesDB(event, 'PUT', 'condicioForm', API_URLS.POST.COS_MILITAR);
      });
    }
  } else if (pageType[2] === 'modifica-espai') {
    formEspai(true, Number(pageType[3]));
  } else if (pageType[2] === 'nou-espai') {
    formEspai(false);
  } else if (pageType[2] === 'modifica-estat-civil') {
    const estatCivilForm = document.getElementById('estatCivilForm');
    if (estatCivilForm) {
      estatCivilForm.addEventListener('submit', function (event) {
        transmissioDadesDB(event, 'PUT', 'estatCivilForm', API_URLS.PUT.ESTAT_CIVIL);
      });
    }
  } else if (pageType[2] === 'nou-estat-civil') {
    const estatCivilForm = document.getElementById('estatCivilForm');
    if (estatCivilForm) {
      estatCivilForm.addEventListener('submit', function (event) {
        transmissioDadesDB(event, 'POST', 'estatCivilForm', API_URLS.POST.ESTAT_CIVIL, true);
      });
    }
  } else if (pageType[2] === 'nou-nivell-estudis') {
    const nivellEstudisForm = document.getElementById('nivellEstudisForm');
    if (nivellEstudisForm) {
      nivellEstudisForm.addEventListener('submit', function (event) {
        transmissioDadesDB(event, 'POST', 'nivellEstudisForm', API_URLS.POST.NIVELL_ESTUDIS, true);
      });
    }
  } else if (pageType[2] === 'modifica-nivell-estudis') {
    const nivellEstudisForm = document.getElementById('nivellEstudisForm');
    if (nivellEstudisForm) {
      nivellEstudisForm.addEventListener('submit', function (event) {
        transmissioDadesDB(event, 'PUT', 'nivellEstudisForm', API_URLS.PUT.NIVELL_ESTUDIS);
      });
    }
  } else if (pageType[2] === 'modifica-sub-sector-economic') {
    formSubSector(true, Number(pageType[3]));
  } else if (pageType[2] === 'nou-sub-sector-economic') {
    formSubSector(false);
  } else if (pageType[2] === 'modifica-sector-economic') {
    const sectorEconomicForm = document.getElementById('sectorEconomicForm');
    if (sectorEconomicForm) {
      sectorEconomicForm.addEventListener('submit', function (event) {
        transmissioDadesDB(event, 'PUT', 'sectorEconomicForm', API_URLS.PUT.SECTOR_ECONOMIC);
      });
    }
  } else if (pageType[2] === 'nou-sector-economic') {
    const sectorEconomicForm = document.getElementById('sectorEconomicForm');
    if (sectorEconomicForm) {
      sectorEconomicForm.addEventListener('submit', function (event) {
        transmissioDadesDB(event, 'POST', 'sectorEconomicForm', API_URLS.POST.SECTOR_ECONOMIC, true);
      });
    }
  } else if (pageType[2] === 'llistat-causa-mort') {
    taulaCausaDefuncio();
  } else if (pageType[2] === 'llistat-espais') {
    taulaEspais();
  } else if (pageType[2] === 'llistat-estats-civils') {
    taulaEstatsCivils();
  } else if (pageType[2] === 'llistat-nivells-estudis') {
    taulaNivellsEstudis();
  } else if (pageType[2] === 'llistat-oficis') {
    taulaOficis();
  } else if (pageType[2] === 'llistat-carrecs-empresa') {
    taulaCarrecsEmpresa();
  } else if (pageType[2] === 'llistat-subsectors-economics') {
    taulaSubsectorsEconomics();
  } else if (pageType[2] === 'llistat-sectors-economics') {
    taulaSectorsEconomics();
  } else if (pageType[2] === 'llistat-acusacions-judicials') {
    taulaAcusacionsJudicials();
  } else if (pageType[2] === 'llistat-bandols-guerra') {
    taulaBandolsGuerra();
  } else if (pageType[2] === 'llistat-condicions-militars') {
    taulaCondicionsMilitars();
  } else if (pageType[2] === 'llistat-cossos-militars') {
    taulaCossosMilitars();
  } else if (pageType[2] === 'modifica-empresa') {
    formEmpresa(true, Number(pageType[3]));
  } else if (pageType[2] === 'nova-empresa') {
    formEmpresa(false);
  }
}
