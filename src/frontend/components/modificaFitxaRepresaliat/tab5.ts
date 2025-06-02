import { Fitxa } from '../../types/types';
import { fetchCheckBoxs } from '../../services/fetchData/fetchCheckBoxs';

export function tab5(fitxa?: Fitxa) {
  fetchCheckBoxs('partitsPolitics', 'partit_politic', 10, fitxa?.filiacio_politica);

  document.getElementById('refreshButtonPartits')?.addEventListener('click', (event) => {
    event.preventDefault();
    fetchCheckBoxs('partitsPolitics', 'partit_politic', 10, fitxa?.filiacio_politica);
  });

  fetchCheckBoxs('sindicats', 'sindicat', 4, fitxa?.filiacio_sindical);

  document.getElementById('refreshButtonSindicats')?.addEventListener('click', (event) => {
    event.preventDefault();
    fetchCheckBoxs('sindicats', 'sindicat', 4, fitxa?.filiacio_sindical);
  });

  const activitatInput = document.getElementById('activitat_durant_guerra') as HTMLInputElement | null;
  if (activitatInput) {
    activitatInput.value = fitxa?.activitat_durant_guerra ?? '';
  }
}
