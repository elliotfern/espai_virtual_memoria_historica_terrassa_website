import { transmissioDadesDB } from '../../../../services/fetchData/transmissioDades';
import { API_URLS } from '../../../../services/api/ApiUrls';
import { fetchDataGet } from '../../../../services/fetchData/fetchDataGet';
import { renderFormInputs } from '../../../../services/fetchData/renderInputsForm';

interface FitxaTerritori {
  [key: string]: unknown;
  id: number;
  sort_order: number;
  nom_ca: string;
  nom_es: string;
  nom_en: string;
  nom_fr: string;
  nom_it: string;
  nom_pt: string;
}

interface ApiResponse<T> {
  status: string;
  message: string;
  data: T;
}

export async function formTerritori(isUpdate: boolean, id?: number): Promise<void> {
  const divTitol = document.getElementById('titolForm') as HTMLDivElement | null;
  const btnTerritori = document.getElementById('btnSubmitTerritori') as HTMLButtonElement | null;
  const territoriForm = document.getElementById('territoriForm') as HTMLFormElement | null;

  if (!divTitol || !btnTerritori || !territoriForm) return;

  if (id && isUpdate) {
    const response = await fetchDataGet<ApiResponse<FitxaTerritori>>(
      API_URLS.GET.TERRITORI_ID(id),
      true
    );

    if (!response || !response.data) return;

    const data = response.data;

    divTitol.innerHTML = `<h2>Modificació territori: ${data.nom_ca ?? ''}</h2>`;

    renderFormInputs(data);

    btnTerritori.textContent = 'Modificar dades';

    territoriForm.addEventListener('submit', function (event) {
      transmissioDadesDB(event, 'PUT', 'territoriForm', API_URLS.PUT.ESTUDIS_TERRITORI);
    });
  } else {
    divTitol.innerHTML = '<h2>Creació de nou territori</h2>';
    btnTerritori.textContent = 'Inserir dades';

    territoriForm.addEventListener('submit', function (event) {
      transmissioDadesDB(event, 'POST', 'territoriForm', API_URLS.POST.ESTUDIS_TERRITORI, true);
    });
  }
}
