import { getPageType } from '../../../../services/url/splitUrl';
import { fitxaAntecedent } from './fitxaAntecedent';
import { formAntecedent } from './formAntecedents';
import { taulaAntecedents } from './llistatAntecedents';

export async function espaiVirtual() {
  const url = window.location.href;
  const pageType = getPageType(url);

  if (pageType[3] === 'llistat-antecedents') {
    taulaAntecedents();
  } else if (pageType[3] === 'fitxa-antecedent') {
    const id = Number(pageType[4]);
    fitxaAntecedent(id);
  } else if (pageType[3] === 'nou-antecedent') {
    formAntecedent(false);
  } else if (pageType[3] === 'modifica-antecedent') {
    const id = Number(pageType[4]);
    formAntecedent(true, id);
  }
}
