import { fetchDataGet } from '../../../../services/fetchData/fetchDataGet';
import { auxiliarSelect } from '../../../../services/fetchData/auxiliarSelect';
import { renderFormInputs } from '../../../../services/fetchData/renderInputsForm';
import { transmissioDadesDB } from '../../../../services/fetchData/transmissioDades';

interface Fitxa {
  [key: string]: unknown;
  status: string;
  message: string;
  id: number;
  idPersona: number;
  preso: number;
}

export async function top(idRepresaliat: number) {
  let data: Partial<Fitxa> = {
    preso: 0,
    lloc_exili: 0,
    top: 0,
    qui_ordena_detencio: 0,
  };

  const response = await fetchDataGet<Fitxa>(`/api/top/get/fitxaRepressio?id=${idRepresaliat}`);
  const data2 = await fetchDataGet<Fitxa>(`/api/dades_personals/get/?type=nomCognoms&id=${idRepresaliat}`);

  const btnForm = document.getElementById('btnTop') as HTMLButtonElement;
  const container = document.getElementById('fitxaNomCognoms');
  const htmlForm = document.getElementById('topForm');
  const inputIdPersona = document.getElementById('idPersona') as HTMLInputElement | null;
  const btn1 = document.getElementById('refreshButton1');

  if (!response || !response.data) {
    if (btnForm) {
      btnForm.textContent = 'Inserir dades';
    }
  } else {
    if (btnForm) {
      btnForm.textContent = 'Modificar dades';
    }
    data = response.data;
  }

  if (data2) {
    if (!container) return;

    if (inputIdPersona) {
      inputIdPersona.value = String(data2.id);
    }

    const nomComplet = `${data2.nom} ${data2.cognom1} ${data2.cognom2}`;
    const url = `https://memoriaterrassa.cat/fitxa/${data2.id}`;

    container.innerHTML = `<h4>Fitxa: <a href="${url}" target="_blank">${nomComplet}</a></h4>`;
  }

  renderFormInputs(data);

  await auxiliarSelect(data?.preso, 'llistatPresons', 'preso', 'preso');

  if (btn1) {
    btn1.addEventListener('click', function (event) {
      event.preventDefault();
      auxiliarSelect(data?.preso, 'llistatPresons', 'preso', 'preso');
    });
  }

  if (!response) {
    if (htmlForm) {
      htmlForm.addEventListener('submit', function (event) {
        transmissioDadesDB(event, 'POST', 'topForm', '/api/top/post', true);
      });
    }
  } else {
    if (htmlForm) {
      htmlForm.addEventListener('submit', function (event) {
        transmissioDadesDB(event, 'PUT', 'topForm', '/api/top/put');
      });
    }
  }
}
