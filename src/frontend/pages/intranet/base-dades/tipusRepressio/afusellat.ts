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
  enterrament_lloc: number;
  lloc_execucio_enterrament: number;
}

export async function afusellat(idRepresaliat: number) {
  let data: Partial<Fitxa> = {
    enterrament_lloc: 0,
    lloc_execucio_enterrament: 0,
  };

  const response = await fetchDataGet<Fitxa>(`/api/afusellats/get/fitxaRepressio?id=${idRepresaliat}`);
  const data2 = await fetchDataGet<Fitxa>(`/api/dades_personals/get/?type=nomCognoms&id=${idRepresaliat}`);

  const btnForm = document.getElementById('btnAfusellat') as HTMLButtonElement;
  const btn1 = document.getElementById('refreshButton1');
  const btn2 = document.getElementById('refreshButton2');
  const container = document.getElementById('fitxaNomCognoms');
  const afusellatForm = document.getElementById('afusellatForm');
  const inputIdPersona = document.getElementById('idPersona') as HTMLInputElement | null;

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
    const url = `https://memoriaterrassa.cat/fitxa/${data2.slug}`;

    container.innerHTML = `<h4>Fitxa: <a href="${url}" target="_blank">${nomComplet}</a></h4>`;
  }

  renderFormInputs(data);

  if (btn1 && btn2) {
    btn1.addEventListener('click', function (event) {
      event.preventDefault();
      auxiliarSelect(data?.lloc_execucio_enterrament, 'espais', 'lloc_execucio_enterrament', 'espai_cat');
    });

    btn2.addEventListener('click', function (event) {
      event.preventDefault();
      auxiliarSelect(data?.enterrament_lloc, 'espais', 'enterrament_lloc', 'espai_cat');
    });
  }

  await auxiliarSelect(data?.lloc_execucio_enterrament, 'espais', 'lloc_execucio_enterrament', 'espai_cat');
  await auxiliarSelect(data?.enterrament_lloc, 'espais', 'enterrament_lloc', 'espai_cat');

  if (!response) {
    if (afusellatForm) {
      afusellatForm.addEventListener('submit', function (event) {
        transmissioDadesDB(event, 'POST', 'afusellatForm', '/api/afusellats/post', true);
      });
    }
  } else {
    if (afusellatForm) {
      afusellatForm.addEventListener('submit', function (event) {
        transmissioDadesDB(event, 'PUT', 'afusellatForm', '/api/afusellats/put');
      });
    }
  }
}
