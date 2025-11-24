import { mostrarBotonsNomesAdmin } from '../../components/mostrarBotoAdmin/mostrarBotoAdmin';
import { getPageType } from '../../services/url/splitUrl';
import { auxiliars } from './auxiliars/auxiliars';
import { transmissioDadesDB } from '../../services/fetchData/transmissioDades';
import { baseDadesIntranet } from './base-dades/baseDades';
import { fontsDocumentals } from './fonts-documentals/fontsDocumentals';
import { formBiografies } from './biografies/formBiografies';
import { formFamiliars } from './familiars/formFamiliars';
import { taulaMissatgesRebuts } from './missatges/taulaMissatgesRebuts';
import { carregarMissatge } from './missatges/missatgeId';
import { renderRespostaForm } from './missatges/respondreMissatge';

export function intranet() {
  const url = window.location.href;
  const pageType = getPageType(url);

  mostrarBotonsNomesAdmin();

  if (pageType[1] === 'auxiliars') {
    auxiliars();
  } else if (pageType[1] === 'base-dades') {
    baseDadesIntranet();
  } else if (pageType[1] === 'crear-arxiu') {
    const llibre = document.getElementById('arxiuForm');
    if (llibre) {
      // Lanzar actualizador de datos
      llibre.addEventListener('submit', function (event) {
        transmissioDadesDB(event, 'POST', 'arxiuForm', '/api/fonts_documentals/post/arxiu', true);
      });
    }
  } else if (pageType[1] === 'fonts-documentals') {
    fontsDocumentals();
  } else if (pageType[1] === 'biografies') {
    const idPersona = Number.parseInt(pageType?.[3] ?? '', 10);
    if (pageType[2] === 'nova-biografia') {
      formBiografies(false, idPersona);
    } else if (pageType[2] === 'modifica-biografia') {
      formBiografies(true, idPersona);
    }
  } else if (pageType[1] === 'familiars') {
    const idPersona = Number.parseInt(pageType?.[3] ?? '', 10);
    const id = Number.parseInt(pageType?.[4] ?? '', 10);

    if (pageType[2] === 'nou-familiar') {
      formFamiliars(false, idPersona);
    } else if (pageType[2] === 'modifica-familiar') {
      formFamiliars(true, idPersona, id);
    }
  } else if (pageType[1] === 'missatges') {
    if (pageType[2] === 'llistat-missatges') {
      taulaMissatgesRebuts();
    } else if (pageType[2] === 'veure-missatge') {
      const id = Number.parseInt(pageType?.[3] ?? '', 10);
      carregarMissatge(id);
    } else if (pageType[2] === 'respondre-missatge') {
      const id = Number.parseInt(pageType?.[3] ?? '', 10);
      renderRespostaForm(id);
    }
  }
}
