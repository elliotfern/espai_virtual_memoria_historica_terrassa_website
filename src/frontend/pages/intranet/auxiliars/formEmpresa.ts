import { transmissioDadesDB } from '../../../services/fetchData/transmissioDades';
import { API_URLS } from '../../../services/api/ApiUrls';
import { fetchDataGet } from '../../../services/fetchData/fetchDataGet';
import { renderFormInputs } from '../../../services/fetchData/renderInputsForm';

interface Fitxa {
  [key: string]: unknown;
  status: string;
  message: string;
  id: number;
  empresa_ca: string;
  municipi: number;
  comarca: number;
  provincia: number;
  comunitat: number;
  estat: number;
  idSector: number;
}

interface ApiResponse<T> {
  status: string;
  message: string;
  data: T;
}

export async function formEmpresa(isUpdate: boolean, id?: number) {
  const divTitol = document.getElementById('titolForm') as HTMLDivElement;
  const btnForm = document.getElementById('btnSubmitEmpresa') as HTMLButtonElement;
  const form = document.getElementById('empresaForm');

  let data: Partial<Fitxa> = {
    comarca: 0,
    provincia: 0,
    comunitat: 0,
    estat: 0,
  };

  if (!divTitol || !btnForm || !form) return;

  if (id && isUpdate) {
    const response = await fetchDataGet<ApiResponse<Fitxa>>(API_URLS.GET.EMPRESA_ID(id), true);

    if (!response || !response.data) return;
    data = response.data;

    divTitol.innerHTML = `<h2>Modificació empresa / organisme públic: ${data.empresa_ca}</h2>`;

    renderFormInputs(data);

    btnForm.textContent = 'Modificar dades';

    form.addEventListener('submit', function (event) {
      transmissioDadesDB(event, 'PUT', 'empresaForm', API_URLS.PUT.EMPRESA);
    });
  } else {
    divTitol.innerHTML = `<h2>Creació de nova empresa o organisme públic</h2>`;
    btnForm.textContent = 'Inserir dades';

    form.addEventListener('submit', function (event) {
      transmissioDadesDB(event, 'POST', 'empresaForm', API_URLS.POST.EMPRESA, true);
    });
  }
}
