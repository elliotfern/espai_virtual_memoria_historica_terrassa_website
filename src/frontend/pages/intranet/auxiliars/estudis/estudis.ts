import { getPageType } from '../../../../services/url/splitUrl';
import { formEstudi } from './formEstudis';
import { formPeriode } from './formPeriode';
import { formTerritori } from './formTerritori';
import { formTipus } from './formTipus';
import { taulaEstudis } from './llistatEstudis';
import { taulaPeriodes } from './llistatPeriodes';
import { taulaTerritoris } from './llistatTerritoris';
import { taulaTipus } from './llistatTipus';

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
  } else if (pageType[3] === 'llistat-tipus-estudis') {
    taulaTipus();
  } else if (pageType[3] === 'nou-tipus') {
    formTipus(false);
  } else if (pageType[3] === 'modifica-tipus') {
    const id = Number(pageType[4]);
    formTipus(true, id);
  } else if (pageType[3] === 'llistat-estudis') {
    taulaEstudis();
  } else if (pageType[3] === 'nou-estudi') {
    formEstudi(false);
  } else if (pageType[3] === 'modifica-estudi') {
    const id = Number(pageType[4]);
    formEstudi(true, id);
  }
}
