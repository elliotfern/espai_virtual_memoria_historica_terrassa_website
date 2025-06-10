import { auxiliarSelect } from '../../../services/fetchData/auxiliarSelect';
import { transmissioDadesDB } from '../../../services/fetchData/transmissioDades';
import { API_URLS } from '../../../services/api/ApiUrls';
import { fetchDataGet } from '../../../services/fetchData/fetchDataGet';
import { renderFormInputs } from '../../../services/fetchData/renderInputsForm';
import { ENDPOINTS } from '../../../services/api/ApiUrls';

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
  idSector: number;
}

interface ApiResponse<T> {
  status: string;
  message: string;
  data: T;
}

export async function formSubSector(isUpdate: boolean, id?: number) {
  const divTitol = document.getElementById('titolForm') as HTMLDivElement;
  const btnForm = document.getElementById('btnSubSector') as HTMLButtonElement;
  const form = document.getElementById('subSectorForm');

  let data: Partial<Fitxa> = {
    comarca: 0,
    provincia: 0,
    comunitat: 0,
    estat: 0,
  };

  if (!divTitol || !btnForm || !form) return;

  if (id && isUpdate) {
    const response = await fetchDataGet<ApiResponse<Fitxa>>(API_URLS.GET.SUB_SECTOR_ID(id), true);

    if (!response || !response.data) return;
    data = response.data;

    divTitol.innerHTML = `<h2>Modificació sub-sector econòmic: ${data.sub_sector_cat}</h2>`;

    renderFormInputs(data);

    btnForm.textContent = 'Modificar dades';

    form.addEventListener('submit', function (event) {
      transmissioDadesDB(event, 'PUT', 'subSectorForm', API_URLS.PUT.SUB_SECTOR_ECONOMIC);
    });
  } else {
    divTitol.innerHTML = `<h2>Creació de nou espai</h2>`;
    btnForm.textContent = 'Inserir dades';

    form.addEventListener('submit', function (event) {
      transmissioDadesDB(event, 'POST', 'subSectorForm', API_URLS.POST.SUB_SECTOR_ECONOMIC, true);
    });
  }

  await auxiliarSelect(data.idSector ?? 0, ENDPOINTS.SECTOR_ECONOMIC, 'idSector', 'sector_cat');
}
