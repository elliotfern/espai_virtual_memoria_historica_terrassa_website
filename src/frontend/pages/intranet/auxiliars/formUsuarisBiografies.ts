import { transmissioDadesDB } from '../../../services/fetchData/transmissioDades';
import { API_URLS } from '../../../services/api/ApiUrls';
import { fetchDataGet } from '../../../services/fetchData/fetchDataGet';
import { renderFormInputs } from '../../../services/fetchData/renderInputsForm';
import 'trix'; // carga Trix (extiende el DOM con <trix-editor>)

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
  user_type: number;
  avatar: number;
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
    await new Promise((r) => setTimeout(r, 50));
  }
  throw new Error(`Trix editor no inicializado para input="${inputId}"`);
}

/** Carga HTML en el editor Trix (y sincroniza el hidden input) de forma robusta */
async function setTrixHTML(inputId: string, html: string | undefined | null) {
  const safe = html ?? '';
  const hidden = document.getElementById(inputId) as HTMLInputElement | null;
  if (hidden) hidden.value = safe; // asegura el valor en el hidden

  try {
    const editorEl = await getTrixEditorForInput(inputId);

    editorEl.editor.loadHTML(safe);
  } catch {
    // Si no logramos coger el editor a tiempo, al menos queda el hidden con el valor
  }
}

export async function formUsuarisBiografies(isUpdate: boolean, id?: number) {
  const divTitol = document.getElementById('titolForm') as HTMLDivElement;
  const btnUsuari = document.getElementById('btnUsuariBio') as HTMLButtonElement;
  const usuariForm = document.getElementById('usuariBioForm');

  let data: Partial<Fitxa> = {
    comarca: 0,
    provincia: 0,
    comunitat: 0,
    estat: 0,
  };

  // IDs de tus inputs (coinciden con tu HTML)
  const INPUT_CA = 'bio_ca';
  const INPUT_ES = 'bio_es';
  const INPUT_EN = 'bio_en';
  const INPUT_FR = 'bio_fr';
  const INPUT_IT = 'bio_it';
  const INPUT_PT = 'bio_pt';

  if (!divTitol || !btnUsuari || !usuariForm) return;

  if (id && isUpdate) {
    const response = await fetchDataGet<ApiResponse<Fitxa>>(API_URLS.GET.USUARI_BIO_ID(id), true);

    if (!response || !response.data) return;
    data = response.data;

    divTitol.innerHTML = `<h2>Modificació Biografia Usuari: ${data.nom}</h2>`;

    renderFormInputs(data);

    btnUsuari.textContent = 'Modificar dades';

    // Rellena IDs ocultos si procede
    const hiddenId = document.getElementById('id') as HTMLInputElement | null;
    if (hiddenId && typeof data.id === 'number') hiddenId.value = String(data.id);

    const hiddenIdRep = document.getElementById('id_user') as HTMLInputElement | null;
    if (hiddenIdRep && typeof data.id_user === 'number') hiddenIdRep.value = String(data.id_user);

    // Contenido de biografías (acepta camelCase o snake_case según venga)
    const bioCa = (data.bio_ca as string) ?? (data.bio_ca as string) ?? '';
    const bioEs = (data.bio_es as string) ?? (data.bio_es as string) ?? '';
    const bioEn = (data.bio_ens as string) ?? (data.bio_en as string) ?? '';
    const bioFr = (data.bio_fr as string) ?? (data.bio_fr as string) ?? '';
    const bioIt = (data.bio_it as string) ?? (data.bio_it as string) ?? '';
    const bioPt = (data.bio_pt as string) ?? (data.bio_pt as string) ?? '';

    // Carga robusta en Trix (después de que Trix se haya inicializado)
    await setTrixHTML(INPUT_CA, bioCa);
    await setTrixHTML(INPUT_ES, bioEs);
    await setTrixHTML(INPUT_EN, bioEn);
    await setTrixHTML(INPUT_FR, bioFr);
    await setTrixHTML(INPUT_IT, bioIt);
    await setTrixHTML(INPUT_PT, bioPt);

    divTitol.textContent = 'Modificar dades';

    // Submit (una sola vez)
    usuariForm.addEventListener(
      'submit',
      (event) => {
        // Asegura que el hidden está sincronizado (por si el usuario no tocó nada)
        // (Trix ya lo hace, pero no cuesta nada)
        (document.getElementById(INPUT_CA) as HTMLInputElement | null)?.dispatchEvent(new Event('change'));
        (document.getElementById(INPUT_ES) as HTMLInputElement | null)?.dispatchEvent(new Event('change'));
        (document.getElementById(INPUT_EN) as HTMLInputElement | null)?.dispatchEvent(new Event('change'));
        (document.getElementById(INPUT_FR) as HTMLInputElement | null)?.dispatchEvent(new Event('change'));
        (document.getElementById(INPUT_IT) as HTMLInputElement | null)?.dispatchEvent(new Event('change'));
        (document.getElementById(INPUT_PT) as HTMLInputElement | null)?.dispatchEvent(new Event('change'));

        transmissioDadesDB(event, 'PUT', 'usuariBioForm', 'https://memoriaterrassa.cat/api/auxiliars/put/usuari-biografia');
      },
      { once: true }
    );
  } else {
    divTitol.innerHTML = `<h2>Creació de biografies usuari</h2>`;
    btnUsuari.textContent = 'Inserir dades';

    // Creación “en blanco”
    renderFormInputs(data);

    // Inicializa Trix vacío (o con lo que quieras por defecto)
    await setTrixHTML(INPUT_CA, '');
    await setTrixHTML(INPUT_ES, '');
    await setTrixHTML(INPUT_EN, '');
    await setTrixHTML(INPUT_FR, '');
    await setTrixHTML(INPUT_IT, '');
    await setTrixHTML(INPUT_PT, '');

    if (!usuariForm || !btnUsuari) return;

    usuariForm.addEventListener(
      'submit',
      (event) => {
        (document.getElementById(INPUT_CA) as HTMLInputElement | null)?.dispatchEvent(new Event('change'));
        (document.getElementById(INPUT_ES) as HTMLInputElement | null)?.dispatchEvent(new Event('change'));
        (document.getElementById(INPUT_EN) as HTMLInputElement | null)?.dispatchEvent(new Event('change'));
        (document.getElementById(INPUT_FR) as HTMLInputElement | null)?.dispatchEvent(new Event('change'));
        (document.getElementById(INPUT_IT) as HTMLInputElement | null)?.dispatchEvent(new Event('change'));
        (document.getElementById(INPUT_PT) as HTMLInputElement | null)?.dispatchEvent(new Event('change'));

        transmissioDadesDB(event, 'POST', 'usuariBioForm', 'https://memoriaterrassa.cat/api/auxiliars/post/usuari-biografia', false, 'hide');
      },
      { once: true }
    );
  }

  // Si usas tabs y el form está oculto al iniciar, re-carga el HTML en Trix al mostrar la tab (opcional)
  document.addEventListener('shown.bs.tab', async () => {
    // Fuerza re-sincronización visual
    const ca = (document.getElementById('bio_ca') as HTMLInputElement | null)?.value ?? '';
    const es = (document.getElementById('bio_es') as HTMLInputElement | null)?.value ?? '';
    const en = (document.getElementById('bio_en') as HTMLInputElement | null)?.value ?? '';
    const fr = (document.getElementById('bio_fr') as HTMLInputElement | null)?.value ?? '';
    const it = (document.getElementById('bio_it') as HTMLInputElement | null)?.value ?? '';
    const pt = (document.getElementById('bio_pt') as HTMLInputElement | null)?.value ?? '';

    await setTrixHTML('bio_ca', ca);
    await setTrixHTML('bio_es', es);
    await setTrixHTML('bio_en', en);
    await setTrixHTML('bio_fr', fr);
    await setTrixHTML('bio_it', it);
    await setTrixHTML('bio_pt', pt);
  });
}
