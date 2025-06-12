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
  ciutat: number;
  comarca: number;
  provincia: number;
  comunitat: number;
  estat: number;
  arxiu: string;
}

interface ApiResponse<T> {
  status: string;
  message: string;
  data: T;
}

export async function formArxiu(isUpdate: boolean, id?: number) {
  const btn1 = document.getElementById('refreshButton');
  const divTitol = document.getElementById('titolForm') as HTMLDivElement;
  const btnArxiu = document.getElementById('btnArxiu') as HTMLButtonElement;
  const arxiuForm = document.getElementById('arxiuForm');

  let data: Partial<Fitxa> = {
    comarca: 0,
    provincia: 0,
    comunitat: 0,
    estat: 0,
    arxiu: '',
    ciutat: 0,
  };

  if (!btn1 || !divTitol || !btnArxiu || !arxiuForm) return;

  if (id && isUpdate) {
    const response = await fetchDataGet<ApiResponse<Fitxa>>(API_URLS.GET.ARXIU_ID(id), true);

    if (!response || !response.data) return;
    data = response.data;

    divTitol.innerHTML = `<h2>Fonts documentals: modificació d'arxiu/font documental: ${data.arxiu}</h2>`;

    renderFormInputs(data);

    btnArxiu.textContent = 'Modificar dades';

    arxiuForm.addEventListener('submit', function (event) {
      transmissioDadesDB(event, 'PUT', 'arxiuForm', API_URLS.PUT.ARXIU_CODI);
    });
  } else {
    divTitol.innerHTML = `<h2>Fonts documentals: creació de nou arxiu/fonts documental</h2>`;
    btnArxiu.textContent = 'Inserir dades';

    arxiuForm.addEventListener('submit', function (event) {
      transmissioDadesDB(event, 'POST', 'arxiuForm', API_URLS.POST.ARXIU_CODI, true);
    });
  }

  await auxiliarSelect(data.ciutat ?? 0, 'municipis', 'ciutat', 'ciutat');

  btn1.addEventListener('click', function (event) {
    event.preventDefault();
    auxiliarSelect(data.ciutat ?? 0, 'municipis', 'ciutat', 'ciutat');
  });
}
