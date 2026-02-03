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
import { taulaProcedimentsJudicials } from './taulaProcedimentsJudicials';
import { taulaTipusJudicis } from './taulaTipusJudicis';
import { taulaSentencies } from './taulaSentencies';
import { taulaPenes } from './taulaPenes';
import { taulaJutjats } from './taulaJutjats';
import { taulaModalitatsPreso } from './taulaModalitatsPreso';
import { taulaMotiusDetencio } from './taulaMotiusDetencio';
import { taulaGrupsRepressio } from './taulaGrupsRepressio';
import { fetchDataGet } from '../../../services/fetchData/fetchDataGet';
import { auxiliarSelect } from '../../../services/fetchData/auxiliarSelect';
import { taulaPresons } from './taulaPresons';
import { formUsuaris } from './formUsuaris';
import { formCampPreso } from './formCampPreso';
import { taulaCampsDetencio } from './taulaCampsDetencio';
import { taulaCampsConcentracio } from './taulaCampsConcentracio';
import { formCampConcentracio } from './formCampConcentracio';
import { formUsuarisBiografies } from './formUsuarisBiografies';
import { taulaMitjansComunicacio } from './taulaMitjansComunicacio';
import { initFitxaDetallsMitja } from './fitxaMitja';
import { formMitjaPremsa } from './formMitja';
import { formMitjaPremsaI18n } from './formMitjaI18n';
import { taulaAparicionsPremsa } from './taulaAparicionsMitjans';
import { taulaImatges } from './taulaImatges';
import { formImatge } from './formImatge';
import { initFitxaDetallsImatge } from './fitxaImatge';
import { formAparicioPremsa } from './formAparicioPremsa';
import { initFitxaDetallsAparicioMitja } from './fitxaAparicioPremsa';

export async function auxiliars() {
  const url = window.location.href;
  const pageType = getPageType(url);

  if (pageType[2] === 'llistat-usuaris') {
    taulaDadesUsuaris();
  } else if (pageType[2] === 'nou-usuari') {
    formUsuaris(false);
  } else if (pageType[2] === 'modifica-usuari') {
    formUsuaris(true, Number(pageType[3]));
  } else if (pageType[2] === 'modifica-bio-usuari') {
    formUsuarisBiografies(true, Number(pageType[3]));
  } else if (pageType[2] === 'nova-bio-usuari') {
    formUsuarisBiografies(false);
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
  } else if (pageType[2] === 'modifica-acusacio') {
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
  } else if (pageType[2] === 'nou-tipus-procediment-judicial') {
    const procedimentJudicialForm = document.getElementById('procedimentJudicialForm');
    if (procedimentJudicialForm) {
      procedimentJudicialForm.addEventListener('submit', function (event) {
        transmissioDadesDB(event, 'POST', 'procedimentJudicialForm', API_URLS.POST.PROCEDIMENT_JUDICIAL, true);
      });
    }
  } else if (pageType[2] === 'modifica-tipus-procediment-judicial') {
    const procedimentJudicialForm = document.getElementById('procedimentJudicialForm');
    if (procedimentJudicialForm) {
      procedimentJudicialForm.addEventListener('submit', function (event) {
        transmissioDadesDB(event, 'PUT', 'procedimentJudicialForm', API_URLS.PUT.PROCEDIMENT_JUDICIAL);
      });
    }
  } else if (pageType[2] === 'llistat-tipus-procediments-judicials') {
    taulaProcedimentsJudicials();
  } else if (pageType[2] === 'nou-tipus-judici') {
    const tipusJudiciForm = document.getElementById('tipusJudiciForm');
    if (tipusJudiciForm) {
      tipusJudiciForm.addEventListener('submit', function (event) {
        transmissioDadesDB(event, 'POST', 'tipusJudiciForm', API_URLS.POST.TIPUS_JUDICI, true);
      });
    }
  } else if (pageType[2] === 'modifica-tipus-judici') {
    const tipusJudiciForm = document.getElementById('tipusJudiciForm');
    if (tipusJudiciForm) {
      tipusJudiciForm.addEventListener('submit', function (event) {
        transmissioDadesDB(event, 'PUT', 'tipusJudiciForm', API_URLS.PUT.TIPUS_JUDICI);
      });
    }
  } else if (pageType[2] === 'llistat-tipus-judicis') {
    taulaTipusJudicis();
  } else if (pageType[2] === 'nova-sentencia') {
    const sentenciaForm = document.getElementById('sentenciaForm');
    if (sentenciaForm) {
      sentenciaForm.addEventListener('submit', function (event) {
        transmissioDadesDB(event, 'POST', 'sentenciaForm', API_URLS.POST.SENTENCIA, true);
      });
    }
  } else if (pageType[2] === 'modifica-sentencia') {
    const sentenciaForm = document.getElementById('sentenciaForm');
    if (sentenciaForm) {
      sentenciaForm.addEventListener('submit', function (event) {
        transmissioDadesDB(event, 'PUT', 'sentenciaForm', API_URLS.PUT.SENTENCIA);
      });
    }
  } else if (pageType[2] === 'llistat-sentencies') {
    taulaSentencies();
  } else if (pageType[2] === 'nova-pena') {
    const penaForm = document.getElementById('penaForm');
    if (penaForm) {
      penaForm.addEventListener('submit', function (event) {
        transmissioDadesDB(event, 'POST', 'penaForm', API_URLS.POST.PENA, true);
      });
    }
  } else if (pageType[2] === 'modifica-pena') {
    const penaForm = document.getElementById('penaForm');
    if (penaForm) {
      penaForm.addEventListener('submit', function (event) {
        transmissioDadesDB(event, 'PUT', 'penaForm', API_URLS.PUT.PENA);
      });
    }
  } else if (pageType[2] === 'llistat-penes') {
    taulaPenes();
  } else if (pageType[2] === 'nou-jutjat') {
    const jutjatForm = document.getElementById('jutjatForm');
    if (jutjatForm) {
      jutjatForm.addEventListener('submit', function (event) {
        transmissioDadesDB(event, 'POST', 'jutjatForm', API_URLS.POST.JUTJAT, true);
      });
    }
  } else if (pageType[2] === 'modifica-jutjat') {
    const jutjatForm = document.getElementById('jutjatForm');
    if (jutjatForm) {
      jutjatForm.addEventListener('submit', function (event) {
        transmissioDadesDB(event, 'PUT', 'jutjatForm', API_URLS.PUT.JUTJAT);
      });
    }
  } else if (pageType[2] === 'llistat-jutjats') {
    taulaJutjats();
  } else if (pageType[2] === 'nova-modalitat-preso') {
    const modalitatPresoForm = document.getElementById('modalitatPresoForm');
    if (modalitatPresoForm) {
      modalitatPresoForm.addEventListener('submit', function (event) {
        transmissioDadesDB(event, 'POST', 'modalitatPresoForm', API_URLS.POST.MODALITAT_PRESO, true);
      });
    }
  } else if (pageType[2] === 'modifica-modalitat-preso') {
    const modalitatPresoForm = document.getElementById('modalitatPresoForm');
    if (modalitatPresoForm) {
      modalitatPresoForm.addEventListener('submit', function (event) {
        transmissioDadesDB(event, 'PUT', 'modalitatPresoForm', API_URLS.PUT.MODALITAT_PRESO);
      });
    }
  } else if (pageType[2] === 'llistat-modalitats-preso') {
    taulaModalitatsPreso();
  } else if (pageType[2] === 'nou-motiu-detencio') {
    const motiuDetencioForm = document.getElementById('motiuDetencioForm');
    if (motiuDetencioForm) {
      motiuDetencioForm.addEventListener('submit', function (event) {
        transmissioDadesDB(event, 'POST', 'motiuDetencioForm', API_URLS.POST.MOTIU_DETENCIO, true);
      });
    }
  } else if (pageType[2] === 'modifica-motiu-detencio') {
    const motiuDetencioForm = document.getElementById('motiuDetencioForm');
    if (motiuDetencioForm) {
      motiuDetencioForm.addEventListener('submit', function (event) {
        transmissioDadesDB(event, 'PUT', 'motiuDetencioForm', API_URLS.PUT.MOTIU_DETENCIO);
      });
    }
  } else if (pageType[2] === 'llistat-motius-detencio') {
    taulaMotiusDetencio();
  } else if (pageType[2] === 'nou-grup-repressio') {
    await auxiliarSelect(null, 'sistemaRepressiuGrup', 'grup_institucio', 'grup');

    const grupRepressioForm = document.getElementById('grupRepressioForm');
    if (grupRepressioForm) {
      grupRepressioForm.addEventListener('submit', function (event) {
        transmissioDadesDB(event, 'POST', 'grupRepressioForm', API_URLS.POST.GRUP_REPRESSIO, true);
      });
    }
  } else if (pageType[2] === 'modifica-grup-repressio') {
    interface Fitxa {
      [key: string]: unknown;
      status: string;
      message: string;
      data: {
        grup_institucio: number;
      };
    }
    const id = pageType[3];
    const data = await fetchDataGet<Fitxa>(`/api/auxiliars/get/sistemaRepressiuID?id=${id}`);
    if (data && data.status === 'success') {
      await auxiliarSelect(data.data.grup_institucio, 'sistemaRepressiuGrup', 'grup_institucio', 'grup');
    }

    const grupRepressioForm = document.getElementById('grupRepressioForm');
    if (grupRepressioForm) {
      grupRepressioForm.addEventListener('submit', function (event) {
        transmissioDadesDB(event, 'PUT', 'grupRepressioForm', API_URLS.PUT.GRUP_REPRESSIO);
      });
    }
  } else if (pageType[2] === 'llistat-grups-repressio') {
    taulaGrupsRepressio();
  } else if (pageType[2] === 'nova-preso') {
    auxiliarSelect(null, 'municipis', 'municipi_preso', 'ciutat');
    const btn1 = document.getElementById('refreshButton1');
    if (btn1) {
      btn1.addEventListener('click', function (event) {
        event.preventDefault();
        auxiliarSelect(null, 'municipis', 'municipi_preso', 'ciutat');
      });
    }

    const presoForm = document.getElementById('presoForm');
    if (presoForm) {
      presoForm.addEventListener('submit', function (event) {
        transmissioDadesDB(event, 'POST', 'presoForm', API_URLS.POST.PRESO, true);
      });
    }
  } else if (pageType[2] === 'modifica-preso') {
    interface Fitxa {
      [key: string]: unknown;
      status: string;
      message: string;
      data: {
        municipi_preso: number;
      };
    }
    const id = pageType[3];
    const data = await fetchDataGet<Fitxa>(`/api/auxiliars/get/presonsID?id=${id}`);
    if (data && data.status === 'success') {
      await auxiliarSelect(data.data.municipi_preso, 'municipis', 'municipi_preso', 'ciutat');
    }

    const btn1 = document.getElementById('refreshButton1');

    if (btn1 && data) {
      btn1.addEventListener('click', function (event) {
        event.preventDefault();
        auxiliarSelect(data.data.municipi_preso, 'municipis', 'municipi_preso', 'ciutat');
      });
    }

    const presoForm = document.getElementById('presoForm');
    if (presoForm) {
      presoForm.addEventListener('submit', function (event) {
        transmissioDadesDB(event, 'PUT', 'presoForm', API_URLS.PUT.PRESO);
      });
    }
  } else if (pageType[2] === 'llistat-presons') {
    taulaPresons();
  } else if (pageType[2] === 'llistat-camps-detencio') {
    taulaCampsDetencio();
  } else if (pageType[2] === 'modifica-camp-detencio') {
    formCampPreso(true, Number(pageType[3]));
  } else if (pageType[2] === 'nou-camp-detencio') {
    formCampPreso(false);
  } else if (pageType[2] === 'llistat-camps-concentracio') {
    taulaCampsConcentracio();
  } else if (pageType[2] === 'modifica-camp-concentracio') {
    formCampConcentracio(true, Number(pageType[3]));
  } else if (pageType[2] === 'nou-camp-concentracio') {
    formCampConcentracio(false);
  } else if (pageType[2] === 'llistat-mitjans') {
    taulaMitjansComunicacio();
  } else if (pageType[2] === 'fitxa-mitja-comunicacio') {
    const slug = pageType[3];
    initFitxaDetallsMitja(slug);
  } else if (pageType[2] === 'modifica-mitja-comunicacio') {
    const slug = pageType[3];
    formMitjaPremsa(true, slug);
  } else if (pageType[2] === 'nou-mitja-comunicacio') {
    formMitjaPremsa(false);
  } else if (pageType[2] === 'modifica-mitja-comunicacio-i18n') {
    const slug = pageType[3];
    formMitjaPremsaI18n(slug);
  } else if (pageType[2] === 'llistat-aparicions-mitjans') {
    taulaAparicionsPremsa();
  } else if (pageType[2] === 'nova-aparicio-premsa') {
    formAparicioPremsa(false);
  } else if (pageType[2] === 'modifica-aparicio-premsa') {
    const id = Number(pageType[3]);
    formAparicioPremsa(true, id);
  } else if (pageType[2] === 'llistat-imatges') {
    taulaImatges();
  } else if (pageType[2] === 'nova-imatge') {
    formImatge(false);
  } else if (pageType[2] === 'modifica-imatge') {
    const id = Number(pageType[3]);
    formImatge(true, id);
  } else if (pageType[2] === 'fitxa-imatge') {
    const id = Number(pageType[3]);
    initFitxaDetallsImatge(id);
  } else if (pageType[2] === 'fitxa-aparicio-premsa') {
    const id = Number(pageType[3]);
    initFitxaDetallsAparicioMitja(id);
  }
}
