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
  motiu_empresonament: number;
  observacions: string;
  top: number;
  qui_ordena_detencio: number;
}

export async function detingutsGuardiaUrbana(idRepresaliat: number) {
  let data: Partial<Fitxa> = {
    motiu_empresonament: 0,
    professio: 0,
    top: 0,
    qui_ordena_detencio: 0,
  };

  const response = await fetchDataGet<Fitxa>(`/api/detinguts_guardia_urbana/get/fitxaRepressio?id=${idRepresaliat}`);
  const data2 = await fetchDataGet<Fitxa>(`/api/dades_personals/get/?type=nomCognoms&id=${idRepresaliat}`);

  const btnForm = document.getElementById('btndetingutGU') as HTMLButtonElement;
  const container = document.getElementById('fitxaNomCognoms');
  const htmlForm = document.getElementById('detingutGUForm');
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

  await auxiliarSelect(data?.motiu_empresonament, 'motiusEmpresonament', 'motiu_empresonament', 'motiuEmpresonament_ca');
  await auxiliarSelect(data?.qui_ordena_detencio, 'sistemaRepressiu', 'qui_ordena_detencio', 'carrec');
  await auxiliarSelect(data?.top, 'top', 'top', 'ordena_top', '2');

  if (btn1 && btn2) {
    btn1.addEventListener('click', function (event) {
      event.preventDefault();
      auxiliarSelect(data?.motiu_empresonament, 'motiusEmpresonament', 'motiu_empresonament', 'motiuEmpresonament_ca');
    });

    btn2.addEventListener('click', function (event) {
      event.preventDefault();
      auxiliarSelect(data?.qui_ordena_detencio, 'sistemaRepressiu', 'qui_ordena_detencio', 'carrec');
    });
  }

  if (!response) {
    if (htmlForm) {
      htmlForm.addEventListener('submit', function (event) {
        transmissioDadesDB(event, 'POST', 'detingutGUForm', '/api/detinguts_guardia_urbana/post', true);
      });
    }
  } else {
    if (htmlForm) {
      htmlForm.addEventListener('submit', function (event) {
        transmissioDadesDB(event, 'PUT', 'detingutGUForm', '/api/detinguts_guardia_urbana/put');
      });
    }
  }
}
