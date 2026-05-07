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

export async function formJutgeInstructor(isUpdate: boolean, id?: number) {
  const divTitol = document.getElementById('titolForm') as HTMLDivElement;
  const btnForm = document.getElementById('btn') as HTMLButtonElement;
  const form = document.getElementById('jutgeForm');

  let data: Partial<Fitxa> = {
    comarca: 0,
    provincia: 0,
    comunitat: 0,
    estat: 0,
  };

  if (!divTitol || !btnForm || !form) return;

  if (id && isUpdate) {
    const response = await fetchDataGet<ApiResponse<Fitxa>>(`https://memoriaterrassa.cat/api/processats/get/jutgesInstructors?id=${id}`);

    if (!response || !response.data) return;
    data = response.data;

    divTitol.innerHTML = `<h2>Modificació Jutge instructor: ${data.sub_sector_cat}</h2>`;

    renderFormInputs(data);

    btnForm.textContent = 'Modificar dades';

    form.addEventListener('submit', function (event) {
      transmissioDadesDB(event, 'PUT', 'jutgeForm', `https://memoriaterrassa.cat/api/processats/aux/put/jutgesInstructors`);
    });
  } else {
    divTitol.innerHTML = `<h2>Alta d'un nou Jutge instructor</h2>`;
    btnForm.textContent = 'Inserir dades';

    form.addEventListener('submit', function (event) {
      transmissioDadesDB(event, 'POST', 'jutgeForm', `https://memoriaterrassa.cat/api/processats/aux/post/jutgesInstructors`, true);
    });
  }
}
