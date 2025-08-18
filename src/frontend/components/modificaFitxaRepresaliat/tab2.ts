import { Fitxa } from '../../types/types';
import { auxiliarSelect } from '../../services/fetchData/auxiliarSelect';
import { formatDatesForm } from '../../services/formatDates/dates';

export async function tab2(fitxa?: Fitxa) {
  // Asignar valores a los campos del formulario
  const nomInput = document.getElementById('nom') as HTMLInputElement;
  if (nomInput) nomInput.value = fitxa?.nom ?? '';

  const cognom1Input = document.getElementById('cognom1') as HTMLInputElement;
  if (cognom1Input) cognom1Input.value = fitxa?.cognom1 ?? '';

  const cognom2Input = document.getElementById('cognom2') as HTMLInputElement;
  if (cognom2Input) cognom2Input.value = fitxa?.cognom2 ?? '';

  const sexeSelect = document.getElementById('sexe') as HTMLSelectElement;
  if (sexeSelect && fitxa?.sexe) {
    sexeSelect.value = fitxa.sexe;
  }

  const dataNaixementInput = document.getElementById('data_naixement') as HTMLInputElement;
  const dataDefuncioInput = document.getElementById('data_defuncio') as HTMLInputElement;
  if (fitxa && dataNaixementInput && dataDefuncioInput) {
    dataNaixementInput.value = formatDatesForm(fitxa.data_naixement) ?? '';
    dataDefuncioInput.value = formatDatesForm(fitxa.data_defuncio) ?? '';
  } else {
    dataNaixementInput.value = '';
    dataDefuncioInput.value = '';
  }

  await auxiliarSelect(fitxa?.ciutat_naixement_id ?? 252, 'municipis', 'municipi_naixement', 'ciutat', '252');
  await auxiliarSelect(fitxa?.ciutat_defuncio_id ?? 252, 'municipis', 'municipi_defuncio', 'ciutat', '252');
  await auxiliarSelect(fitxa?.ciutat_residencia_id ?? 252, 'municipis', 'municipi_residencia', 'ciutat', '252');

  await auxiliarSelect(fitxa?.tipus_via_id ?? 1, 'tipusVia', 'tipus_via', 'tipus_ca', '1');

  const adrecaInput = document.getElementById('adreca') as HTMLInputElement;
  if (adrecaInput) adrecaInput.value = fitxa?.adreca ?? '';

  const adreca_num = document.getElementById('adreca_num') as HTMLInputElement;
  if (adreca_num) adreca_num.value = fitxa?.adreca_num ?? '';

  const adreca_antic = document.getElementById('adreca_antic') as HTMLInputElement;
  if (adreca_antic) adreca_antic.value = fitxa?.adreca_antic ?? '';

  // Agregar eventos a los botones de refresco
  const refreshButton1 = document.getElementById('refreshButton1');
  if (refreshButton1) {
    refreshButton1.addEventListener('click', async (event) => {
      event.preventDefault();
      await auxiliarSelect(fitxa?.ciutat_naixement_id ?? 252, 'municipis', 'municipi_naixement', 'ciutat', '252');
    });
  }

  const refreshButton2 = document.getElementById('refreshButton2');
  if (refreshButton2) {
    refreshButton2.addEventListener('click', (event: Event) => {
      event.preventDefault();
      auxiliarSelect(fitxa?.ciutat_defuncio_id ?? 252, 'municipis', 'municipi_defuncio', 'ciutat', '252');
    });
  }

  const refreshButton3 = document.getElementById('refreshButton3');
  if (refreshButton3) {
    refreshButton3.addEventListener('click', (event: Event) => {
      event.preventDefault();
      auxiliarSelect(fitxa?.ciutat_residencia_id ?? 252, 'municipis', 'municipi_residencia', 'ciutat', '252');
    });
  }

  auxiliarSelect(fitxa?.tipologia_lloc_defuncio_id ?? 2, 'tipologia_espais', 'tipologia_lloc_defuncio', 'tipologia_espai_ca', '2');

  const refreshButton4 = document.getElementById('refreshButton4');
  if (refreshButton4) {
    refreshButton4.addEventListener('click', (event: Event) => {
      event.preventDefault();
      auxiliarSelect(fitxa?.tipologia_lloc_defuncio_id ?? 2, 'tipologia_espais', 'tipologia_lloc_defuncio', 'tipologia_espai_ca');
    });
  }

  auxiliarSelect(fitxa?.causa_defuncio_id ?? 2, 'causa_defuncio', 'causa_defuncio', 'causa_defuncio_ca', '2');

  const refreshButton5 = document.getElementById('refreshButton5');
  if (refreshButton5) {
    refreshButton5.addEventListener('click', (event: Event) => {
      event.preventDefault();
      auxiliarSelect(fitxa?.causa_defuncio_id, 'causa_defuncio', 'causa_defuncio', 'causa_defuncio_ca');
    });
  }
}
