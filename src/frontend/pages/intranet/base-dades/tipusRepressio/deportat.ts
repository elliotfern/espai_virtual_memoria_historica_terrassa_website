import { fetchDataGet } from '../../../../services/fetchData/fetchDataGet';
import { auxiliarSelect } from '../../../../services/fetchData/auxiliarSelect';
import { renderFormInputs } from '../../../../services/fetchData/renderInputsForm';
import { transmissioDadesDB } from '../../../../services/fetchData/transmissioDades';
import { wireForm } from '../../../../helpers/transmissioHelper';

interface Fitxa {
  [key: string]: unknown;
  status: string;
  message: string;
  id: number;
  idPersona: number;
  situacio: number;
  data_alliberament: string;
  lloc_mort_alliberament: number;
  preso_tipus: number;
  preso_nom: string;
  preso_data_sortida: string;
  preso_localitat: number;
  preso_num_matricula: string;
  deportacio_nom_camp: string;
  deportacio_data_entrada: string;
  deportacio_num_matricula: string;
  deportacio_nom_subcamp: string;
  deportacio_data_entrada_subcamp: string;
  deportacio_nom_matricula_subcamp: string;
  situacioFranca: number;
  presoClasificacio1: number;
  presoClasificacio2: number;
  deportacio_camp: number;
  deportacio_subcamp: number;
}

export async function deportat(idRepresaliat: number) {
  const data = await fetchDataGet<Fitxa>(`/api/deportats/get/fitxaRepressio?id=${idRepresaliat}`);

  const data2 = await fetchDataGet<Fitxa>(`/api/dades_personals/get/?type=nomCognoms&id=${idRepresaliat}`);

  if (data2) {
    const container = document.getElementById('fitxaNomCognoms');
    const inputIdPersona = document.getElementById('idPersona') as HTMLInputElement | null;
    if (!container) return;

    const nomComplet = `${data2.nom} ${data2.cognom1} ${data2.cognom2}`;
    const url = `https://memoriaterrassa.cat/fitxa/${data2.slug}`;

    container.innerHTML = `<h4>Fitxa: <a href="${url}" target="_blank">${nomComplet}</a></h4>`;

    if (inputIdPersona && data2.id !== undefined) {
      inputIdPersona.value = String(data2.id);
    }
  }

  if (!data || data.status === 'error') {
    const btn = document.getElementById('refreshButton');
    const btn1 = document.getElementById('refreshButton1');
    const btn2 = document.getElementById('refreshButton2');
    const btn3 = document.getElementById('refreshButton3');
    const btn4 = document.getElementById('refreshButton4');
    const btn5 = document.getElementById('refreshButton5');

    if (btn && btn1 && btn2 && btn3 && btn4 && btn5) {
      btn.addEventListener('click', function (event) {
        event.preventDefault();
        auxiliarSelect(data?.lloc_mort_alliberament, 'municipis', 'lloc_mort_alliberament', 'ciutat');
      });

      btn1.addEventListener('click', function (event) {
        event.preventDefault();
        auxiliarSelect(data?.situacioFranca, 'deportacioPreso', 'situacioFranca', 'nom_camp');
      });

      btn2.addEventListener('click', function (event) {
        event.preventDefault();
        auxiliarSelect(data?.presoClasificacio1, 'deportacioPreso', 'presoClasificacio1', 'nom_camp');
      });

      btn3.addEventListener('click', function (event) {
        event.preventDefault();
        auxiliarSelect(data?.presoClasificacio2, 'deportacioPreso', 'presoClasificacio2', 'nom_camp');
      });

      btn4.addEventListener('click', function (event) {
        event.preventDefault();
        auxiliarSelect(data?.deportacio_camp, 'campsConcentracio', 'deportacio_camp', 'nom_camp');
      });

      btn5.addEventListener('click', function (event) {
        event.preventDefault();
        auxiliarSelect(data?.deportacio_subcamp, 'campsConcentracio', 'deportacio_subcamp', 'nom_camp');
      });
    }

    auxiliarSelect(data?.situacio, 'situacions_deportats', 'situacio', 'situacio_ca');
    auxiliarSelect(data?.lloc_mort_alliberament, 'municipis', 'lloc_mort_alliberament', 'ciutat');

    auxiliarSelect(data?.situacioFranca, 'deportacioPreso', 'situacioFranca', 'nom_camp');

    auxiliarSelect(data?.presoClasificacio1, 'deportacioPreso', 'presoClasificacio1', 'nom_camp');
    auxiliarSelect(data?.presoClasificacio2, 'deportacioPreso', 'presoClasificacio2', 'nom_camp');

    auxiliarSelect(data?.deportacio_camp, 'campsConcentracio', 'deportacio_camp', 'nom_camp');
    auxiliarSelect(data?.deportacio_subcamp, 'campsConcentracio', 'deportacio_subcamp', 'nom_camp');

    const deportatForm = document.getElementById('deportatForm');
    if (deportatForm) {
      // Ocultar el form en éxito y hacer scroll automático a #okMessage
      wireForm({
        formId: 'deportatForm',
        urlAjax: '/api/deportats/post',
        successBehavior: 'hide', // oculta el formulario
        scrollOnSuccess: true, // opcional; ya se activa por defecto al usar 'hide'
        //scrollOffset: 96,             // si tienes un header fijo alto
      });
    }
  } else {
    const btn = document.getElementById('refreshButton');
    const btn1 = document.getElementById('refreshButton1');
    const btn2 = document.getElementById('refreshButton2');
    const btn3 = document.getElementById('refreshButton3');
    const btn4 = document.getElementById('refreshButton4');
    const btn5 = document.getElementById('refreshButton5');

    if (btn && btn1 && btn2 && btn3 && btn4 && btn5) {
      btn.addEventListener('click', function (event) {
        event.preventDefault();
        auxiliarSelect(data.lloc_mort_alliberament, 'municipis', 'lloc_mort_alliberament', 'ciutat');
      });

      btn1.addEventListener('click', function (event) {
        event.preventDefault();
        auxiliarSelect(data.situacioFranca, 'deportacioPreso', 'situacioFranca', 'nom_camp');
      });

      btn2.addEventListener('click', function (event) {
        event.preventDefault();
        auxiliarSelect(data.presoClasificacio1, 'deportacioPreso', 'presoClasificacio1', 'nom_camp');
      });

      btn3.addEventListener('click', function (event) {
        event.preventDefault();
        auxiliarSelect(data.presoClasificacio2, 'deportacioPreso', 'presoClasificacio2', 'nom_camp');
      });

      btn4.addEventListener('click', function (event) {
        event.preventDefault();
        auxiliarSelect(data.deportacio_camp, 'campsConcentracio', 'deportacio_camp', 'nom_camp');
      });

      btn5.addEventListener('click', function (event) {
        event.preventDefault();
        auxiliarSelect(data.deportacio_subcamp, 'campsConcentracio', 'deportacio_subcamp', 'nom_camp');
      });
    }

    auxiliarSelect(data.situacio, 'situacions_deportats', 'situacio', 'situacio_ca');
    auxiliarSelect(data.lloc_mort_alliberament, 'municipis', 'lloc_mort_alliberament', 'ciutat');

    auxiliarSelect(data.situacioFranca, 'deportacioPreso', 'situacioFranca', 'nom_camp');

    auxiliarSelect(data.presoClasificacio1, 'deportacioPreso', 'presoClasificacio1', 'nom_camp');
    auxiliarSelect(data.presoClasificacio2, 'deportacioPreso', 'presoClasificacio2', 'nom_camp');

    auxiliarSelect(data.deportacio_camp, 'campsConcentracio', 'deportacio_camp', 'nom_camp');
    auxiliarSelect(data.deportacio_subcamp, 'campsConcentracio', 'deportacio_subcamp', 'nom_camp');

    renderFormInputs(data);

    const btnForm = document.getElementById('btnDeportats') as HTMLButtonElement;
    if (btnForm) {
      btnForm.textContent = 'Modificar dades';
    }

    const deportatForm = document.getElementById('deportatForm');
    if (deportatForm) {
      deportatForm.addEventListener('submit', function (event) {
        transmissioDadesDB(event, 'PUT', 'deportatForm', '/api/deportats/put');
      });
    }
  }
}
