import { getPageType } from '../../../../services/url/splitUrl';
import { formPeriode } from './formPeriode';
import { taulaPeriodes } from './llistatPeriodes';

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
  }
}
