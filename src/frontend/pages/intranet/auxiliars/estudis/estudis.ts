import { getPageType } from '../../../../services/url/splitUrl';
import { taulaPeriodes } from './llistatPeriodes';

export async function estudis() {
  const url = window.location.href;
  const pageType = getPageType(url);

  if (pageType[3] === 'llistat-periodes') {
    taulaPeriodes();
  } else if (pageType[2] === 'modifica-aparicio-premsa-i18n') {
    //const id = Number(pageType[3]);
    //formAparicioPremsaI18n(id);
  }
}
