//import { transmissioDadesDB } from '../../../services/fetchData/transmissioDades';
import { getPageType } from '../../../services/url/splitUrl';
import { taulaLlistatArxius } from './taulaLlistatArxius';
import { taulaLlistatBibliografia } from './taulaLlistatBibliografia';
import { formBibliografiaLlibre } from './formBibliografiaLlibre';
import { formArxiu } from './formArxiu';

export function fontsDocumentals() {
  const url = window.location.href;
  const pageType = getPageType(url);

  if (pageType[2] === 'llistat-llibres') {
    taulaLlistatBibliografia();
  } else if (pageType[2] === 'llistat-arxius') {
    taulaLlistatArxius();
  } else if (pageType[2] === 'nou-llibre') {
    formBibliografiaLlibre(false);
  } else if (pageType[2] === 'modifica-llibre') {
    formBibliografiaLlibre(true, Number(pageType[3]));
  } else if (pageType[2] === 'nou-arxiu') {
    formArxiu(false);
  } else if (pageType[2] === 'modifica-arxiu') {
    formArxiu(true, Number(pageType[3]));
  }
}
