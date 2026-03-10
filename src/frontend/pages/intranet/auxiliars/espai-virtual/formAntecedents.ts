import { auxiliarSelect } from '../../../../services/fetchData/auxiliarSelect';
import { transmissioDadesDB } from '../../../../services/fetchData/transmissioDades';
import { API_URLS } from '../../../../services/api/ApiUrls';
import { fetchDataGet } from '../../../../services/fetchData/fetchDataGet';
import { renderFormInputs } from '../../../../services/fetchData/renderInputsForm';
import 'trix';

interface FitxaAntecedent {
  [key: string]: unknown;

  id: number;
  ordre: number;
  image_id: number | null;
  layout_image_left: number;
  show_in_timeline: number;

  any_text_ca: string;
  titol_ca: string;
  contingut_html_ca: string;
  link_url_ca: string;

  any_text_es: string;
  titol_es: string;
  contingut_html_es: string;
  link_url_es: string;

  any_text_en: string;
  titol_en: string;
  contingut_html_en: string;
  link_url_en: string;

  any_text_fr: string;
  titol_fr: string;
  contingut_html_fr: string;
  link_url_fr: string;

  any_text_it: string;
  titol_it: string;
  contingut_html_it: string;
  link_url_it: string;

  any_text_pt: string;
  titol_pt: string;
  contingut_html_pt: string;
  link_url_pt: string;
}

interface ApiResponse<T> {
  status: string;
  message: string;
  data: T;
}

/** Espera a que exista un trix-editor vinculado a un input concreto y devuelve ese editor */
async function getTrixEditorForInput(inputId: string, timeoutMs = 2000): Promise<HTMLTrixEditorElement> {
  const start = performance.now();

  while (performance.now() - start < timeoutMs) {
    const editor = document.querySelector(`trix-editor[input="${inputId}"]`) as HTMLTrixEditorElement | null;

    if (editor && editor.editor) return editor;
    await new Promise((resolve) => setTimeout(resolve, 50));
  }

  throw new Error(`Trix editor no inicializado para input="${inputId}"`);
}

/** Carga HTML en el editor Trix y sincroniza el hidden input */
async function setTrixHTML(inputId: string, html: string | undefined | null): Promise<void> {
  const safe = html ?? '';
  const hidden = document.getElementById(inputId) as HTMLInputElement | null;

  if (hidden) hidden.value = safe;

  try {
    const editorEl = await getTrixEditorForInput(inputId);
    editorEl.editor.loadHTML(safe);
  } catch {
    // Si Trix no está listo aún, al menos queda el hidden con el valor
  }
}

function syncTrixHidden(inputId: string): void {
  (document.getElementById(inputId) as HTMLInputElement | null)?.dispatchEvent(new Event('change'));
}

export async function formAntecedent(isUpdate: boolean, id?: number): Promise<void> {
  const divTitol = document.getElementById('titolForm') as HTMLDivElement | null;
  const btnAntecedent = document.getElementById('btnSubmitAntecedent') as HTMLButtonElement | null;
  const antecedentForm = document.getElementById('antecedentForm') as HTMLFormElement | null;

  const INPUT_CA = 'contingut_html_ca';
  const INPUT_ES = 'contingut_html_es';
  const INPUT_EN = 'contingut_html_en';
  const INPUT_FR = 'contingut_html_fr';
  const INPUT_IT = 'contingut_html_it';
  const INPUT_PT = 'contingut_html_pt';

  let data: Partial<FitxaAntecedent> = {
    ordre: 0,
    image_id: null,
    layout_image_left: 0,
    show_in_timeline: 1,

    any_text_ca: '',
    titol_ca: '',
    contingut_html_ca: '',
    link_url_ca: '',

    any_text_es: '',
    titol_es: '',
    contingut_html_es: '',
    link_url_es: '',

    any_text_en: '',
    titol_en: '',
    contingut_html_en: '',
    link_url_en: '',

    any_text_fr: '',
    titol_fr: '',
    contingut_html_fr: '',
    link_url_fr: '',

    any_text_it: '',
    titol_it: '',
    contingut_html_it: '',
    link_url_it: '',

    any_text_pt: '',
    titol_pt: '',
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

    await auxiliarSelect(data.image_id ?? 0, 'antecedentsImatges', 'image_id', 'nomImatge');

    await setTrixHTML(INPUT_CA, (data.contingut_html_ca as string) ?? '');
    await setTrixHTML(INPUT_ES, (data.contingut_html_es as string) ?? '');
    await setTrixHTML(INPUT_EN, (data.contingut_html_en as string) ?? '');
    await setTrixHTML(INPUT_FR, (data.contingut_html_fr as string) ?? '');
    await setTrixHTML(INPUT_IT, (data.contingut_html_it as string) ?? '');
    await setTrixHTML(INPUT_PT, (data.contingut_html_pt as string) ?? '');

    btnAntecedent.textContent = 'Modificar dades';

    antecedentForm.addEventListener(
      'submit',
      function (event) {
        syncTrixHidden(INPUT_CA);
        syncTrixHidden(INPUT_ES);
        syncTrixHidden(INPUT_EN);
        syncTrixHidden(INPUT_FR);
        syncTrixHidden(INPUT_IT);
        syncTrixHidden(INPUT_PT);

        transmissioDadesDB(event, 'PUT', 'antecedentForm', API_URLS.PUT.ANTECEDENT);
      },
      { once: true }
    );
  } else {
    divTitol.innerHTML = '<h2>Creació de nou antecedent</h2>';

    renderFormInputs(data);

    await auxiliarSelect(0, 'antecedentsImatges', 'image_id', 'nomImatge');

    await setTrixHTML(INPUT_CA, '');
    await setTrixHTML(INPUT_ES, '');
    await setTrixHTML(INPUT_EN, '');
    await setTrixHTML(INPUT_FR, '');
    await setTrixHTML(INPUT_IT, '');
    await setTrixHTML(INPUT_PT, '');

    btnAntecedent.textContent = 'Inserir dades';

    antecedentForm.addEventListener(
      'submit',
      function (event) {
        syncTrixHidden(INPUT_CA);
        syncTrixHidden(INPUT_ES);
        syncTrixHidden(INPUT_EN);
        syncTrixHidden(INPUT_FR);
        syncTrixHidden(INPUT_IT);
        syncTrixHidden(INPUT_PT);

        transmissioDadesDB(event, 'POST', 'antecedentForm', API_URLS.POST.ANTECEDENT, true);
      },
      { once: true }
    );
  }

  document.addEventListener('shown.bs.tab', async () => {
    const ca = (document.getElementById(INPUT_CA) as HTMLInputElement | null)?.value ?? '';
    const es = (document.getElementById(INPUT_ES) as HTMLInputElement | null)?.value ?? '';
    const en = (document.getElementById(INPUT_EN) as HTMLInputElement | null)?.value ?? '';
    const fr = (document.getElementById(INPUT_FR) as HTMLInputElement | null)?.value ?? '';
    const it = (document.getElementById(INPUT_IT) as HTMLInputElement | null)?.value ?? '';
    const pt = (document.getElementById(INPUT_PT) as HTMLInputElement | null)?.value ?? '';

    await setTrixHTML(INPUT_CA, ca);
    await setTrixHTML(INPUT_ES, es);
    await setTrixHTML(INPUT_EN, en);
    await setTrixHTML(INPUT_FR, fr);
    await setTrixHTML(INPUT_IT, it);
    await setTrixHTML(INPUT_PT, pt);
  });
}
