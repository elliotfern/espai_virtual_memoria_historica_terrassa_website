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

export async function formVocalTribunal(isUpdate: boolean, id?: number) {
  const divTitol = document.getElementById('titolForm') as HTMLDivElement;
  const btnForm = document.getElementById('btn') as HTMLButtonElement;
  const form = document.getElementById('vocalForm');

  let data: Partial<Fitxa> = {
    comarca: 0,
    provincia: 0,
    comunitat: 0,
    estat: 0,
  };

  if (!divTitol || !btnForm || !form) return;

  if (id && isUpdate) {
    const response = await fetchDataGet<ApiResponse<Fitxa>>(`https://memoriaterrassa.cat/api/processats/get/tribunalVocals?id=${id}`);

    if (!response || !response.data) return;
    data = response.data;

    divTitol.innerHTML = `<h2>Modificació vocal tribunal: ${data.nom} ${data.cognoms}</h2>`;

    renderFormInputs(data);

    btnForm.textContent = 'Modificar dades';

    form.addEventListener('submit', function (event) {
      transmissioDadesDB(event, 'PUT', 'vocalForm', `https://memoriaterrassa.cat/api/processats/aux/put/tribunalVocals`);
    });
  } else {
    divTitol.innerHTML = `<h2>Alta d'un nou vocal tribunal</h2>`;
    btnForm.textContent = 'Inserir dades';

    form.addEventListener('submit', function (event) {
      transmissioDadesDB(event, 'POST', 'vocalForm', `https://memoriaterrassa.cat/api/processats/aux/post/tribunalVocals`, true);
    });
  }
}
