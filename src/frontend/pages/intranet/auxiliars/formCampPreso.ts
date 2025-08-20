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
  tipus: number;
}

interface ApiResponse<T> {
  status: string;
  message: string;
  data: T;
}

export async function formCampPreso(isUpdate: boolean, id?: number) {
  //const btn1 = document.getElementById('campPresoForm');
  const divTitol = document.getElementById('titolForm') as HTMLDivElement;
  const btnEspai = document.getElementById('btnSubmitCampPreso') as HTMLButtonElement;
  const espaiForm = document.getElementById('campPresoForm');

  let data: Partial<Fitxa> = {
    comarca: 0,
    provincia: 0,
    comunitat: 0,
    estat: 0,
  };

  if (!divTitol || !btnEspai || !espaiForm) return;

  if (id && isUpdate) {
    const response = await fetchDataGet<ApiResponse<Fitxa>>(API_URLS.GET.CAMP_DETENCIO_ID(id), true);

    if (!response || !response.data) return;
    data = response.data;

    divTitol.innerHTML = `<h2>Modificació Camp/presó: ${data.espai_cat}</h2>`;

    renderFormInputs(data);

    btnEspai.textContent = 'Modificar dades';

    espaiForm.addEventListener('submit', function (event) {
      transmissioDadesDB(event, 'PUT', 'campPresoForm', API_URLS.PUT.PRESO_DETENCIO);
    });
  } else {
    divTitol.innerHTML = `<h2>Creació de nova presó/camp de detenció</h2>`;
    btnEspai.textContent = 'Inserir dades';

    espaiForm.addEventListener('submit', function (event) {
      transmissioDadesDB(event, 'POST', 'campPresoForm', API_URLS.POST.PRESO_DETENCIO, true);
    });
  }

  await auxiliarSelect(data.municipi ?? 0, 'municipis', 'municipi', 'ciutat');
  await auxiliarSelect(data.tipus ?? 0, 'tipus_presons', 'tipus', 'tipus_preso_ca');

  /*
  btn1.addEventListener('click', function (event) {
    event.preventDefault();
    auxiliarSelect(data.municipi ?? 0, 'municipis', 'municipi', 'ciutat');
  });
  */
}
