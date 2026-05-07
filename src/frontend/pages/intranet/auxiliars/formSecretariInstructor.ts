import { transmissioDadesDB } from '../../../services/fetchData/transmissioDades';
import { fetchDataGet } from '../../../services/fetchData/fetchDataGet';
import { renderFormInputs } from '../../../services/fetchData/renderInputsForm';

interface Fitxa {
  [key: string]: unknown;
  status: string;
  message: string;
  id: number;
  nom: number;
  cognoms: number;
  carrec: number;
}

interface ApiResponse<T> {
  status: string;
  message: string;
  data: T;
}

export async function formSecretariInstructor(isUpdate: boolean, id?: number) {
  const divTitol = document.getElementById('titolForm') as HTMLDivElement;
  const btnForm = document.getElementById('btn') as HTMLButtonElement;
  const form = document.getElementById('secretariForm');

  let data: Partial<Fitxa> = {
    comarca: 0,
    provincia: 0,
    comunitat: 0,
    estat: 0,
  };

  if (!divTitol || !btnForm || !form) return;

  if (id && isUpdate) {
    const response = await fetchDataGet<ApiResponse<Fitxa>>(`https://memoriaterrassa.cat/api/processats/get/secretarisInstructors?id=${id}`);

    if (!response || !response.data) return;
    data = response.data;

    divTitol.innerHTML = `<h2>Modificació Secretari instructor: ${data.nom} ${data.cognoms}</h2>`;

    renderFormInputs(data);

    btnForm.textContent = 'Modificar dades';

    form.addEventListener('submit', function (event) {
      transmissioDadesDB(event, 'PUT', 'secretariForm', `https://memoriaterrassa.cat/api/processats/aux/put/secretarisInstructors`);
    });
  } else {
    divTitol.innerHTML = `<h2>Alta d'un nou Secretari instructor</h2>`;
    btnForm.textContent = 'Inserir dades';

    form.addEventListener('submit', function (event) {
      transmissioDadesDB(event, 'POST', 'secretariForm', `https://memoriaterrassa.cat/api/processats/aux/post/secretarisInstructors`, true);
    });
  }
}
