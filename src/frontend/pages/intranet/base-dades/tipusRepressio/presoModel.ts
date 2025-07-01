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
  data_empresonament: string;
  trasllats: number;
  lloc_trasllat: string;
  data_trasllat: string;
  llibertat: number;
  data_llibertat: string;
  modalitat: number;
  vicissituds: string;
  observacions: string;
}

export async function presoModel(idRepresaliat: number) {
  let data: Partial<Fitxa> = {
    trasllats: 0,
    llibertat: 0,
    modalitat: 0,
  };

  const response = await fetchDataGet<Fitxa>(`/api/preso_model/get/fitxaRepressio?id=${idRepresaliat}`);
  const data2 = await fetchDataGet<Fitxa>(`/api/dades_personals/get/?type=nomCognoms&id=${idRepresaliat}`);

  const btnForm = document.getElementById('btnPresoModel') as HTMLButtonElement;
  const container = document.getElementById('fitxaNomCognoms');
  const presoModelForm = document.getElementById('presoModelForm');
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

  await auxiliarSelect(data?.trasllats, '', 'trasllats', '', '');
  await auxiliarSelect(data?.llibertat, '', 'llibertat', '', '');
  await auxiliarSelect(data?.modalitat, 'modalitatPreso', 'modalitat', 'modalitat_ca');

  if (btn1) {
    btn1.addEventListener('click', function (event) {
      event.preventDefault();
      auxiliarSelect(data?.modalitat, 'modalitatPreso', 'modalitat', 'modalitat_ca');
    });
  }

  if (!response) {
    if (presoModelForm) {
      presoModelForm.addEventListener('submit', function (event) {
        transmissioDadesDB(event, 'POST', 'presoModelForm', '/api/preso_model/post', true);
      });
    }
  } else {
    if (presoModelForm) {
      presoModelForm.addEventListener('submit', function (event) {
        transmissioDadesDB(event, 'PUT', 'presoModelForm', '/api/preso_model/put');
      });
    }
  }
}
