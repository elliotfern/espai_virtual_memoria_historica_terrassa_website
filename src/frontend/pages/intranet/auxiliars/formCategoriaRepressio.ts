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
  categoria_cat: string;
  municipi: number;
  comarca: number;
  provincia: number;
  comunitat: number;
  estat: number;
  grup: number;
}

interface ApiResponse<T> {
  status: string;
  message: string;
  data: T;
}

export async function formCategoriaRepressio(isUpdate: boolean, id?: number) {
  const divTitol = document.getElementById('titolForm') as HTMLDivElement;
  const btnForm = document.getElementById('btnSubmitCategoria') as HTMLButtonElement;
  const divForm = document.getElementById('categoriesForm');

  let data: Partial<Fitxa> = {
    comarca: 0,
    provincia: 0,
    comunitat: 0,
    estat: 0,
  };

  if (!divTitol || !btnForm || !divForm) return;

  if (id && isUpdate) {
    const response = await fetchDataGet<ApiResponse<Fitxa>>(API_URLS.GET.CATEGORIA_REPRESSIO_ID(id), true);

    if (!response || !response.data) return;
    data = response.data;
    console.log(data);

    divTitol.innerHTML = `<h2>Modificació categoració de repressió: ${data.categoria_cat}</h2>`;

    renderFormInputs(data);

    btnForm.textContent = 'Modificar dades';

    divForm.addEventListener('submit', function (event) {
      transmissioDadesDB(event, 'PUT', 'categoriesForm', API_URLS.PUT.CATEGORIA_REPRESSIO);
    });
  } else {
    divTitol.innerHTML = `<h2>Creació de nova categoria de repressió</h2>`;
    btnForm.textContent = 'Inserir dades';

    divForm.addEventListener('submit', function (event) {
      transmissioDadesDB(event, 'POST', 'categoriesForm', API_URLS.POST.CATEGORIA_REPRESSIO, true);
    });
  }

  await auxiliarSelect(data.grup ?? 0, 'categoriesGrupRepressio', 'grup', 'grup_ca');
}
