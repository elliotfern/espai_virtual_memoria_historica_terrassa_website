import { auxiliarSelect } from '../../../../services/fetchData/auxiliarSelect';
import { transmissioDadesDB } from '../../../../services/fetchData/transmissioDades';
import { API_URLS } from '../../../../services/api/ApiUrls';
import { fetchDataGet } from '../../../../services/fetchData/fetchDataGet';
import { renderFormInputs } from '../../../../services/fetchData/renderInputsForm';

interface FitxaAntecedent {
  [key: string]: unknown;

  id: number;
  ordre: number;
  image_id: number | null;
  layout_image_left: number;
  show_in_timeline: number;

  any_text_ca: string;
  titol_ca: string;
  resum_timeline_ca: string;
  contingut_html_ca: string;
  link_url_ca: string;

  any_text_es: string;
  titol_es: string;
  resum_timeline_es: string;
  contingut_html_es: string;
  link_url_es: string;

  any_text_en: string;
  titol_en: string;
  resum_timeline_en: string;
  contingut_html_en: string;
  link_url_en: string;

  any_text_fr: string;
  titol_fr: string;
  resum_timeline_fr: string;
  contingut_html_fr: string;
  link_url_fr: string;

  any_text_it: string;
  titol_it: string;
  resum_timeline_it: string;
  contingut_html_it: string;
  link_url_it: string;

  any_text_pt: string;
  titol_pt: string;
  resum_timeline_pt: string;
  contingut_html_pt: string;
  link_url_pt: string;
}

interface ApiResponse<T> {
  status: string;
  message: string;
  data: T;
}

export async function formAntecedent(isUpdate: boolean, id?: number): Promise<void> {
  const divTitol = document.getElementById('titolForm') as HTMLDivElement | null;
  const btnAntecedent = document.getElementById('btnSubmitAntecedent') as HTMLButtonElement | null;
  const antecedentForm = document.getElementById('antecedentForm') as HTMLFormElement | null;

  let data: Partial<FitxaAntecedent> = {
    ordre: 0,
    image_id: null,
    layout_image_left: 0,
    show_in_timeline: 1,

    any_text_ca: '',
    titol_ca: '',
    resum_timeline_ca: '',
    contingut_html_ca: '',
    link_url_ca: '',

    any_text_es: '',
    titol_es: '',
    resum_timeline_es: '',
    contingut_html_es: '',
    link_url_es: '',

    any_text_en: '',
    titol_en: '',
    resum_timeline_en: '',
    contingut_html_en: '',
    link_url_en: '',

    any_text_fr: '',
    titol_fr: '',
    resum_timeline_fr: '',
    contingut_html_fr: '',
    link_url_fr: '',

    any_text_it: '',
    titol_it: '',
    resum_timeline_it: '',
    contingut_html_it: '',
    link_url_it: '',

    any_text_pt: '',
    titol_pt: '',
    resum_timeline_pt: '',
    contingut_html_pt: '',
    link_url_pt: '',
  };

  if (!divTitol || !btnAntecedent || !antecedentForm) return;

  if (id && isUpdate) {
    const response = await fetchDataGet<ApiResponse<FitxaAntecedent>>(API_URLS.GET.FORM_ANTECEDENT_ID(id), true);

    if (!response || !response.data) return;

    data = response.data;

    divTitol.innerHTML = `<h2>Modificació antecedent: ${data.titol_ca ?? ''}</h2>`;

    renderFormInputs(data);

    // Selects auxiliars
    await auxiliarSelect(data.image_id ?? 0, 'antecedentsImatges', 'image_id', 'nom');

    btnAntecedent.textContent = 'Modificar dades';

    antecedentForm.addEventListener('submit', function (event) {
      transmissioDadesDB(event, 'PUT', 'antecedentForm', API_URLS.PUT.ANTECEDENT);
    });
  } else {
    divTitol.innerHTML = '<h2>Creació de nou antecedent</h2>';

    // Selects auxiliars
    await auxiliarSelect(0, 'antecedentsImatges', 'image_id', 'nom');

    btnAntecedent.textContent = 'Inserir dades';

    antecedentForm.addEventListener('submit', function (event) {
      transmissioDadesDB(event, 'POST', 'antecedentForm', API_URLS.POST.ANTECEDENT, true);
    });
  }
}
