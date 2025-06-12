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
}

interface ApiResponse<T> {
  status: string;
  message: string;
  data: T;
}

export async function formBibliografiaLlibre(isUpdate: boolean, id?: number) {
  const btn1 = document.getElementById('refreshButton');
  const divTitol = document.getElementById('titolForm') as HTMLDivElement;
  const btnBibliofrafia = document.getElementById('btnBibliografia') as HTMLButtonElement;
  const bibliografiaForm = document.getElementById('bibliografiaForm');

  let data: Partial<Fitxa> = {
    comarca: 0,
    provincia: 0,
    comunitat: 0,
    estat: 0,
    llibre: '',
    ciutat: 0,
  };

  if (!btn1 || !divTitol || !btnBibliofrafia || !bibliografiaForm) return;

  if (id && isUpdate) {
    const response = await fetchDataGet<ApiResponse<Fitxa>>(API_URLS.GET.LLIBRE_ID(id), true);

    if (!response || !response.data) return;
    data = response.data;

    divTitol.innerHTML = `<h2>Bibliografia: modificació llibre/article: ${data.llibre}</h2>`;

    renderFormInputs(data);

    btnBibliofrafia.textContent = 'Modificar dades';

    bibliografiaForm.addEventListener('submit', function (event) {
      transmissioDadesDB(event, 'PUT', 'bibliografiaForm', API_URLS.PUT.LLIBRE_BIBLIOFRAFIA);
    });
  } else {
    divTitol.innerHTML = `<h2>Bibliografia: creació de nou llibre/article</h2>`;
    btnBibliofrafia.textContent = 'Inserir dades';

    bibliografiaForm.addEventListener('submit', function (event) {
      transmissioDadesDB(event, 'POST', 'bibliografiaForm', API_URLS.POST.LLIBRE_BIBLIOFRAFIA, true);
    });
  }

  await auxiliarSelect(data.ciutat ?? 0, 'municipis', 'ciutat', 'ciutat');

  btn1.addEventListener('click', function (event) {
    event.preventDefault();
    auxiliarSelect(data.ciutat ?? 0, 'municipis', 'ciutat', 'ciutat');
  });
}
