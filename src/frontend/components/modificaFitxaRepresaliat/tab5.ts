import { Fitxa } from '../../types/types';
import { fetchCheckBoxs } from '../../services/fetchData/fetchCheckBoxs';

export function tab5(fitxa: Fitxa) {
  fetchCheckBoxs(fitxa.filiacio_politica, 'partitsPolitics', 'partit_politic', 10);

  document.getElementById('refreshButtonPartits')?.addEventListener('click', (event) => {
    event.preventDefault();
    fetchCheckBoxs(fitxa.filiacio_politica, 'partitsPolitics', 'partit_politic', 10);
  });

  fetchCheckBoxs(fitxa.filiacio_sindical, 'sindicats', 'sindicat', 3);

  document.getElementById('refreshButtonSindicats')?.addEventListener('click', (event) => {
    event.preventDefault();
    fetchCheckBoxs(fitxa.filiacio_sindical, 'sindicats', 'sindicat', 3);
  });

  const activitatInput = document.getElementById('activitat_durant_guerra') as HTMLInputElement | null;
  if (activitatInput) {
    activitatInput.value = fitxa.activitat_durant_guerra ?? '';
  }
}
