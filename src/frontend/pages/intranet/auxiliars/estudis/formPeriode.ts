import { API_URLS } from '../../../../services/api/ApiUrls';
import { fetchDataGet } from '../../../../services/fetchData/fetchDataGet';
import { renderFormInputs } from '../../../../services/fetchData/renderInputsForm';
import { transmissioDadesDB } from '../../../../services/fetchData/transmissioDades';

interface FitxaPeriode {
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

export async function formPeriode(isUpdate: boolean, id?: number): Promise<void> {
  const divTitol = document.getElementById('titolForm') as HTMLDivElement | null;
  const btnPeriode = document.getElementById('btnSubmitPeriode') as HTMLButtonElement | null;
  const periodeForm = document.getElementById('periodeForm') as HTMLFormElement | null;

  let data: Partial<FitxaPeriode> = {
    sort_order: 0,
    nom_ca: '',
    nom_es: '',
    nom_en: '',
    nom_fr: '',
    nom_it: '',
    nom_pt: '',
  };

  if (!divTitol || !btnPeriode || !periodeForm) return;

  if (id && isUpdate) {
    const response = await fetchDataGet<ApiResponse<FitxaPeriode>>(API_URLS.GET.ESTUDIS_PERIODE_ID(id), true);

    if (!response || !response.data) return;

    data = response.data;

    divTitol.innerHTML = `<h2>Modificació període històric: ${data.nom_ca ?? ''}</h2>`;

    renderFormInputs(data);

    btnPeriode.textContent = 'Modificar dades';

    periodeForm.addEventListener('submit', function (event) {
      transmissioDadesDB(event, 'PUT', 'periodeForm', API_URLS.PUT.ESTUDIS_PERIODE);
    });
  } else {
    divTitol.innerHTML = '<h2>Creació de nou període històric</h2>';
    btnPeriode.textContent = 'Inserir dades';

    periodeForm.addEventListener('submit', function (event) {
      transmissioDadesDB(event, 'POST', 'periodeForm', API_URLS.POST.ESTUDIS_PERIODE, true);
    });
  }
}
