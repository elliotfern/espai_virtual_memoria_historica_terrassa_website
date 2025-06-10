import { auxiliarSelect } from '../../../services/fetchData/auxiliarSelect';
import { transmissioDadesDB } from '../../../services/fetchData/transmissioDades';
import { API_URLS } from '../../../services/api/ApiUrls';
import { fetchDataGet } from '../../../services/fetchData/fetchDataGet';
import { renderFormInputs } from '../../../services/fetchData/renderInputsForm';

interface Fitxa {
  [key: string]: unknown;
  status: string;
  message: string;
  id: number;
  espai_cat: string;
  municipi: number;
  comarca: number;
  provincia: number;
  comunitat: number;
  estat: number;
}

interface ApiResponse<T> {
  status: string;
  message: string;
  data: T;
}

export async function formEspai(isUpdate: boolean, id?: number) {
  const btn1 = document.getElementById('afegirMunicipi');
  const divTitol = document.getElementById('titolForm') as HTMLDivElement;
  const btnEspai = document.getElementById('btnEspai') as HTMLButtonElement;
  const espaiForm = document.getElementById('espaiForm');

  let data: Partial<Fitxa> = {
    comarca: 0,
    provincia: 0,
    comunitat: 0,
    estat: 0,
  };

  if (!btn1 || !divTitol || !btnEspai || !espaiForm) return;

  if (id && isUpdate) {
    const response = await fetchDataGet<ApiResponse<Fitxa>>(API_URLS.GET.ESPAI_ID(id), true);

    if (!response || !response.data) return;
    data = response.data;

    divTitol.innerHTML = `<h2>Modificació espai: ${data.espai_cat}</h2>`;

    renderFormInputs(data);

    btnEspai.textContent = 'Modificar dades';

    espaiForm.addEventListener('submit', function (event) {
      transmissioDadesDB(event, 'PUT', 'espaiForm', API_URLS.PUT.ESPAI);
    });
  } else {
    divTitol.innerHTML = `<h2>Creació de nou espai</h2>`;
    btnEspai.textContent = 'Inserir dades';

    espaiForm.addEventListener('submit', function (event) {
      transmissioDadesDB(event, 'POST', 'espaiForm', API_URLS.POST.ESPAI, true);
    });
  }

  await auxiliarSelect(data.municipi ?? 0, 'municipis', 'municipi', 'ciutat');

  btn1.addEventListener('click', function (event) {
    event.preventDefault();
    auxiliarSelect(data.municipi ?? 0, 'municipis', 'municipi', 'ciutat');
  });
}
