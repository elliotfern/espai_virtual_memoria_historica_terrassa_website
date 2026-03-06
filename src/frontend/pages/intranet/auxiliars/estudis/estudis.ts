import { getPageType } from '../../../../services/url/splitUrl';
import { formPeriode } from './formPeriode';
import { formTerritori } from './formTerritori';
import { taulaPeriodes } from './llistatPeriodes';
import { taulaTerritoris } from './llistatTerritoris';

export async function estudis() {
  const url = window.location.href;
  const pageType = getPageType(url);

  if (pageType[3] === 'llistat-periodes') {
    taulaPeriodes();
  } else if (pageType[3] === 'nou-periode') {
    formPeriode(false);
  } else if (pageType[3] === 'modifica-periode') {
    const id = Number(pageType[4]);
    formPeriode(true, id);
  } else if (pageType[3] === 'llistat-territoris') {
    taulaTerritoris();
  } else if (pageType[3] === 'nou-territori') {
    formTerritori(false);
  } else if (pageType[3] === 'modifica-territori') {
    const id = Number(pageType[4]);
    formTerritori(true, id);
  }
}
