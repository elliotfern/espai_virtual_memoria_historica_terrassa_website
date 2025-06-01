import { formatDates } from '../../services/formatDates/dates';
import { Fitxa } from '../../types/types';
import { auxiliarSelect } from '../../services/fetchData/auxiliarSelect';

export function tab8(fitxa: Fitxa) {
  const observacionsInput = document.getElementById('observacions') as HTMLInputElement | null;
  if (observacionsInput) {
    observacionsInput.value = fitxa.observacions ?? '';
  }

  auxiliarSelect(fitxa.autor_id, 'autors_fitxa', 'autor', 'nom');

  const dataCreacioElement = document.getElementById('data_creacio');
  if (dataCreacioElement) {
    dataCreacioElement.innerText = formatDates(fitxa.data_creacio);
  }

  const dataActualitzacioElement = document.getElementById('data_actualitzacio');
  if (dataActualitzacioElement) {
    dataActualitzacioElement.innerText = formatDates(fitxa.data_actualitzacio);
  }

  const completatNoRadio = document.getElementById('completat_no') as HTMLInputElement;
  const completatSiRadio = document.getElementById('completat_si') as HTMLInputElement;

  if (fitxa.completat === 1 && completatNoRadio) {
    completatNoRadio.checked = true;
  } else if (fitxa.completat === 2 && completatSiRadio) {
    completatSiRadio.checked = true;
  }
}
