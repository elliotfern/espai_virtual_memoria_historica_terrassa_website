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
  tipus_professional: number;
  professio: number;
  observacions: string;
  empresa_id: number;
}

export async function depurats(idRepresaliat: number) {
  let data: Partial<Fitxa> = {
    tipus_professional: 0,
    professio: 0,
    empresa_id: 0,
  };

  const response = await fetchDataGet<Fitxa>(`/api/depurats/get/fitxaRepressio?id=${idRepresaliat}`);
  const data2 = await fetchDataGet<Fitxa>(`/api/dades_personals/get/?type=nomCognoms&id=${idRepresaliat}`);

  const btnForm = document.getElementById('btnDepurat') as HTMLButtonElement;
  const container = document.getElementById('fitxaNomCognoms');
  const htmlForm = document.getElementById('depuratForm');
  const inputIdPersona = document.getElementById('idPersona') as HTMLInputElement | null;
  const btn1 = document.getElementById('refreshButton1');
  const btn2 = document.getElementById('refreshButton2');

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

  await auxiliarSelect(data?.tipus_professional, 'tipusEmpleat', 'tipus_professional', 'tipus_ca');
  await auxiliarSelect(data?.professio, 'oficis', 'professio', 'ofici_cat');
  await auxiliarSelect(data?.empresa_id, 'empreses', 'empresa', 'empresa_ca', '1');

  if (btn1 && btn2) {
    btn1.addEventListener('click', function (event) {
      event.preventDefault();
      auxiliarSelect(data?.professio, 'oficis', 'professio', 'ofici_cat');
    });

    btn2.addEventListener('click', function (event) {
      event.preventDefault();
      auxiliarSelect(data?.empresa_id, 'empreses', 'empresa', 'empresa_ca', '1');
    });
  }

  if (!response) {
    if (htmlForm) {
      htmlForm.addEventListener('submit', function (event) {
        transmissioDadesDB(event, 'POST', 'depuratForm', '/api/depurats/post', true);
      });
    }
  } else {
    if (htmlForm) {
      htmlForm.addEventListener('submit', function (event) {
        transmissioDadesDB(event, 'PUT', 'depuratForm', '/api/depurats/put');
      });
    }
  }
}
