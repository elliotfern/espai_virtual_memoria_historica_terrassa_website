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
  ciutat: string;
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

export async function formMunicipi(isUpdate: boolean, id?: number) {
  const btn1 = document.getElementById('refreshButtonComarca');
  const btn2 = document.getElementById('refreshButtonProvincia');
  const btn3 = document.getElementById('refreshButtonComunitat');
  const btn4 = document.getElementById('refreshButtonEstat');
  const divTitol = document.getElementById('titolFormMunicipi') as HTMLDivElement;
  const btnmunicipiForm = document.getElementById('btnFormMunicipi') as HTMLButtonElement;
  const municipiForm = document.getElementById('municipiForm');

  let data: Partial<Fitxa> = {
    comarca: 0,
    provincia: 0,
    comunitat: 0,
    estat: 0,
  };

  if (!btn1 || !btn2 || !btn3 || !btn4 || !divTitol || !btnmunicipiForm || !municipiForm) return;

  if (id && isUpdate) {
    const response = await fetchDataGet<ApiResponse<Fitxa>>(API_URLS.GET.MUNICIPI_ID(id), true);

    if (!response || !response.data) return;
    data = response.data;

    divTitol.innerHTML = `<h2>Modificació municipi: ${data.ciutat}</h2>`;

    renderFormInputs(data);

    btnmunicipiForm.textContent = 'Modificar dades';

    municipiForm.addEventListener('submit', function (event) {
      transmissioDadesDB(event, 'PUT', 'municipiForm', API_URLS.PUT.MUNICIPI);
    });
  } else {
    btnmunicipiForm.textContent = 'Inserir dades';

    municipiForm.addEventListener('submit', function (event) {
      transmissioDadesDB(event, 'POST', 'municipiForm', API_URLS.POST.MUNICIPI, true);
    });
  }

  await auxiliarSelect(data.comarca ?? 0, 'comarques', 'comarca', 'comarca');
  await auxiliarSelect(data.provincia ?? 0, 'provincies', 'provincia', 'provincia');
  await auxiliarSelect(data.comunitat ?? 0, 'comunitats', 'comunitat', 'comunitat');
  await auxiliarSelect(data.estat ?? 0, 'estats', 'estat', 'estat');

  btn1.addEventListener('click', function (event) {
    event.preventDefault();
    auxiliarSelect(data.comarca ?? 0, 'comarques', 'comarca', 'comarca');
  });
  btn2.addEventListener('click', function (event) {
    event.preventDefault();
    auxiliarSelect(data.provincia ?? 0, 'provincies', 'provincia', 'provincia');
  });

  btn3.addEventListener('click', function (event) {
    event.preventDefault();
    auxiliarSelect(data.comunitat ?? 0, 'comunitats', 'comunitat', 'comunitat');
  });

  btn4.addEventListener('click', function (event) {
    event.preventDefault();
    auxiliarSelect(data.estat ?? 0, 'estats', 'estat', 'estat');
  });
}
