//import { transmissioDadesDB } from '../../../services/fetchData/transmissioDades';
import { getPageType } from '../../../services/url/splitUrl';
import { taulaLlistatArxius } from './taulaLlistatArxius';
import { taulaLlistatBibliografia } from './taulaLlistatBibliografia';
//import { API_URLS } from '../../../services/api/ApiUrls';

export function fontsDocumentals() {
  const url = window.location.href;
  const pageType = getPageType(url);

  if (pageType[2] === 'llistat-llibres') {
    taulaLlistatBibliografia();
  } else if (pageType[2] === 'llistat-arxius') {
    taulaLlistatArxius();
  }
}
