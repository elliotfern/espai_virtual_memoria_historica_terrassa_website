import { transmissioDadesDB } from '../../../../services/fetchData/transmissioDades';
import { API_URLS } from '../../../../services/api/ApiUrls';
import { fetchDataGet } from '../../../../services/fetchData/fetchDataGet';
import { renderFormInputs } from '../../../../services/fetchData/renderInputsForm';

interface FitxaTipus {
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

export async function formTipus(isUpdate: boolean, id?: number): Promise<void> {
  const divTitol = document.getElementById('titolForm') as HTMLDivElement | null;
  const btnTipus = document.getElementById('btnSubmitTipus') as HTMLButtonElement | null;
  const tipusForm = document.getElementById('tipusForm') as HTMLFormElement | null;

  let data: Partial<FitxaTipus> = {
    sort_order: 0,
    nom_ca: '',
    nom_es: '',
    nom_en: '',
    nom_fr: '',
    nom_it: '',
    nom_pt: '',
  };

  if (!divTitol || !btnTipus || !tipusForm) return;

  if (id && isUpdate) {
    const response = await fetchDataGet<ApiResponse<FitxaTipus>>(API_URLS.GET.ESTUDIS_TIPUS_ID(id), true);

    if (!response || !response.data) return;

    data = response.data;

    divTitol.innerHTML = `<h2>Modificació tipus: ${data.nom_ca ?? ''}</h2>`;

    renderFormInputs(data);

    btnTipus.textContent = 'Modificar dades';

    tipusForm.addEventListener('submit', function (event) {
      transmissioDadesDB(event, 'PUT', 'tipusForm', API_URLS.PUT.ESTUDIS_TIPUS);
    });
  } else {
    divTitol.innerHTML = '<h2>Creació de nou tipus</h2>';
    btnTipus.textContent = 'Inserir dades';

    tipusForm.addEventListener('submit', function (event) {
      transmissioDadesDB(event, 'POST', 'tipusForm', API_URLS.POST.ESTUDIS_TIPUS, true);
    });
  }
}
