import { auxiliarSelect } from '../../../../services/fetchData/auxiliarSelect';
import { transmissioDadesDB } from '../../../../services/fetchData/transmissioDades';
import { API_URLS } from '../../../../services/api/ApiUrls';
import { fetchDataGet } from '../../../../services/fetchData/fetchDataGet';
import { renderFormInputs } from '../../../../services/fetchData/renderInputsForm';
import { auxiliarSelectMultipleAutors } from './formMultipleAutors';

interface FitxaEstudi {
  [key: string]: unknown;
  id: number;
  slug: string;
  any_publicacio: number | null;
  periode_id: number;
  territori_id: number;
  tipus_id: number;
  autors: number[];

  titol_ca: string;
  resum_ca: string;
  url_document_ca: string;

  titol_es: string;
  resum_es: string;
  url_document_es: string;

  titol_en: string;
  resum_en: string;
  url_document_en: string;

  titol_fr: string;
  resum_fr: string;
  url_document_fr: string;

  titol_it: string;
  resum_it: string;
  url_document_it: string;

  titol_pt: string;
  resum_pt: string;
  url_document_pt: string;
}

interface ApiResponse<T> {
  status: string;
  message: string;
  data: T;
}

export async function formEstudi(isUpdate: boolean, id?: number): Promise<void> {
  const divTitol = document.getElementById('titolForm') as HTMLDivElement | null;
  const btnEstudi = document.getElementById('btnSubmitEstudi') as HTMLButtonElement | null;
  const estudiForm = document.getElementById('estudiForm') as HTMLFormElement | null;

  let data: Partial<FitxaEstudi> = {
    slug: '',
    any_publicacio: null,
    periode_id: 0,
    territori_id: 0,
    tipus_id: 0,
    autors: [],

    titol_ca: '',
    resum_ca: '',
    url_document_ca: '',

    titol_es: '',
    resum_es: '',
    url_document_es: '',

    titol_en: '',
    resum_en: '',
    url_document_en: '',

    titol_fr: '',
    resum_fr: '',
    url_document_fr: '',

    titol_it: '',
    resum_it: '',
    url_document_it: '',

    titol_pt: '',
    resum_pt: '',
    url_document_pt: '',
  };

  if (!divTitol || !btnEstudi || !estudiForm) return;

  if (id && isUpdate) {
    const response = await fetchDataGet<ApiResponse<FitxaEstudi>>(API_URLS.GET.ESTUDI_ID(id), true);

    if (!response || !response.data) return;

    data = response.data;

    divTitol.innerHTML = `<h2>Modificació estudi: ${data.titol_ca ?? ''}</h2>`;

    renderFormInputs(data);

    // Carregar selects auxiliars
    await auxiliarSelect(data.periode_id ?? 0, 'estudisPeriodes', 'periode_id', 'nom');
    await auxiliarSelect(data.territori_id ?? 0, 'estudisTerritoris', 'territori_id', 'nom');
    await auxiliarSelect(data.tipus_id ?? 0, 'estudisTipus', 'tipus_id', 'nom');

    // TODO: aquí conectar la carga de autores múltiple con vuestra función real
    await auxiliarSelectMultipleAutors(data.autors ?? [], 'autors');

    btnEstudi.textContent = 'Modificar dades';

    estudiForm.addEventListener('submit', function (event) {
      transmissioDadesDB(event, 'PUT', 'estudiForm', API_URLS.PUT.ESTUDI);
    });
  } else {
    divTitol.innerHTML = '<h2>Creació de nou estudi</h2>';

    // Carregar selects auxiliars
    await auxiliarSelect(0, 'estudisPeriodes', 'periode_id', 'nom');
    await auxiliarSelect(0, 'estudisTerritoris', 'territori_id', 'nom');
    await auxiliarSelect(0, 'estudisTipus', 'tipus_id', 'nom');
    await auxiliarSelectMultipleAutors([], 'autors');

    // TODO: aquí conectar la carga de autores múltiple con vuestra función real
    // await auxiliarSelectMultiple([], 'estudisAutors', 'autors', 'nom');

    btnEstudi.textContent = 'Inserir dades';

    estudiForm.addEventListener('submit', function (event) {
      transmissioDadesDB(event, 'POST', 'estudiForm', API_URLS.POST.ESTUDI, true);
    });
  }
}
