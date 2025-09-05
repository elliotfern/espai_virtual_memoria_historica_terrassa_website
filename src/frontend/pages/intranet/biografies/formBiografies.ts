import { transmissioDadesDB } from '../../../services/fetchData/transmissioDades';
import { API_URLS } from '../../../services/api/ApiUrls';
import { fetchDataGet } from '../../../services/fetchData/fetchDataGet';
import { renderFormInputs } from '../../../services/fetchData/renderInputsForm';
import 'trix'; // carga Trix (extiende el DOM con <trix-editor>)
import 'trix/dist/trix.css'; // CSS

interface Fitxa {
  [key: string]: unknown;
  status: string;
  message: string;
  id: number; // id de la biografía (en update)
  ciutat: number;
  comarca: number;
  provincia: number;
  comunitat: number;
  estat: number;
  arxiu: string;

  // usados para pintar título/enlace
  slug?: string;
  nom?: string;
  cognom1?: string;
  cognom2?: string;

  // posibles nombres para biografías según tu API
  biografiaCa?: string;
  biografiaEs?: string;
  biografia_ca?: string;
  biografia_es?: string;

  idRepresaliat?: number; // si tu API lo devuelve
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

interface ApiResponse<T> {
  status: string;
  message: string;
  data: T;
}
type ApiResponse2<T> = {
  status: 'success' | 'error';
  message: string;
  errors: unknown[];
  data: T;
};

export async function formBiografies(isUpdate: boolean, id?: number) {
  const divTitol = document.getElementById('titolForm') as HTMLDivElement | null;
  if (!divTitol) return;

  // IDs de tus inputs (coinciden con tu HTML)
  const INPUT_CA = 'biografiaCa';
  const INPUT_ES = 'biografiaEs';

  // Estado base “en blanco”
  let data: Partial<Fitxa> = {
    comarca: 0,
    provincia: 0,
    comunitat: 0,
    estat: 0,
    arxiu: '',
    ciutat: 0,
  };

  if (id && isUpdate) {
    // ——— MODIFICAR
    const res = await fetchDataGet<ApiResponse<Fitxa>>(API_URLS.GET.BIOGRAFIES_ID(id), true);
    if (!res?.data) {
      divTitol.innerHTML = `<h2>Biografies: modificació</h2><p>No s'han pogut carregar les dades.</p>`;
      return;
    }
    data = res.data;

    const nom = (data.nom as string) ?? '';
    const c1 = (data.cognom1 as string) ?? '';
    const c2 = (data.cognom2 as string) ?? '';
    const slug = (data.slug as string) ?? '';

    divTitol.innerHTML = `
      <h2>Biografies: modificació de biografies</h2>
      <h4>
        Fitxa represaliat:
        <a href="https://memoriaterrassa.cat/fitxa/${slug}" target="_blank" rel="noopener noreferrer">
          ${[nom, c1, c2].filter(Boolean).join(' ')}
        </a>
      </h4>
    `;

    // Pinta cualquier otro campo con tu helper
    renderFormInputs(data);

    // Rellena IDs ocultos si procede
    const hiddenId = document.getElementById('id') as HTMLInputElement | null;
    if (hiddenId && typeof data.id === 'number') hiddenId.value = String(data.id);

    const hiddenIdRep = document.getElementById('idRepresaliat') as HTMLInputElement | null;
    if (hiddenIdRep && typeof data.idRepresaliat === 'number') hiddenIdRep.value = String(data.idRepresaliat);

    // Contenido de biografías (acepta camelCase o snake_case según venga)
    const bioCa = (data.biografiaCa as string) ?? (data.biografia_ca as string) ?? '';
    const bioEs = (data.biografiaEs as string) ?? (data.biografia_es as string) ?? '';

    // Carga robusta en Trix (después de que Trix se haya inicializado)
    await setTrixHTML(INPUT_CA, bioCa);
    await setTrixHTML(INPUT_ES, bioEs);

    // Re-selecciona form y botón (por si renderFormInputs reemplazó nodos)
    const biografiesForm = document.getElementById('BiografiesForm') as HTMLFormElement | null;
    const btnBiografies = document.getElementById('btnBiografies') as HTMLButtonElement | null;
    if (!biografiesForm || !btnBiografies) return;

    btnBiografies.textContent = 'Modificar dades';

    // Submit (una sola vez)
    biografiesForm.addEventListener(
      'submit',
      (event) => {
        // Asegura que el hidden está sincronizado (por si el usuario no tocó nada)
        // (Trix ya lo hace, pero no cuesta nada)
        (document.getElementById(INPUT_CA) as HTMLInputElement | null)?.dispatchEvent(new Event('change'));
        (document.getElementById(INPUT_ES) as HTMLInputElement | null)?.dispatchEvent(new Event('change'));

        transmissioDadesDB(event, 'PUT', 'BiografiesForm', API_URLS.PUT.BIOGRAFIES);
      },
      { once: true }
    );
  } else {
    // ——— CREAR
    // Si viene un id de represaliat, precargamos su ficha básica
    if (id) {
      const res = await fetchDataGet<ApiResponse2<Fitxa[]>>(API_URLS.GET.REPRESALIAT_ID(id), true);
      const fitxa = res?.data?.[0];
      if (!fitxa) {
        divTitol.innerHTML = `<h2>Biografies: creació</h2><p>No s'han trobat dades de la fitxa.</p>`;
        return;
      }

      const nomComplet = [fitxa.nom as string, fitxa.cognom1 as string, fitxa.cognom2 as string].filter(Boolean).join(' ');
      const slug = (fitxa.slug as string) ?? '';

      divTitol.innerHTML = `
        <h2>Biografies: creació de nova biografia</h2>
        <h4>
          Fitxa represaliat:
          <a href="https://memoriaterrassa.cat/fitxa/${slug}" target="_blank" rel="noopener noreferrer">
            ${nomComplet}
          </a>
        </h4>
      `;

      renderFormInputs(fitxa);

      // Setea el id del represaliat en el hidden correspondiente
      const hiddenIdRep = document.getElementById('idRepresaliat') as HTMLInputElement | null;
      if (hiddenIdRep) hiddenIdRep.value = String(id);
    } else {
      // Creación “en blanco”
      divTitol.innerHTML = `<h2>Biografies: creació de nova biografia</h2>`;
      renderFormInputs(data);
    }

    // Inicializa Trix vacío (o con lo que quieras por defecto)
    await setTrixHTML(INPUT_CA, '');
    await setTrixHTML(INPUT_ES, '');

    const biografiesForm = document.getElementById('BiografiesForm') as HTMLFormElement | null;
    const btnBiografies = document.getElementById('btnBiografies') as HTMLButtonElement | null;
    if (!biografiesForm || !btnBiografies) return;

    btnBiografies.textContent = 'Inserir dades';

    biografiesForm.addEventListener(
      'submit',
      (event) => {
        (document.getElementById(INPUT_CA) as HTMLInputElement | null)?.dispatchEvent(new Event('change'));
        (document.getElementById(INPUT_ES) as HTMLInputElement | null)?.dispatchEvent(new Event('change'));

        transmissioDadesDB(event, 'POST', 'BiografiesForm', API_URLS.POST.BIOGRAFIES, false, 'hide');
      },
      { once: true }
    );
  }

  // Si usas tabs y el form está oculto al iniciar, re-carga el HTML en Trix al mostrar la tab (opcional)
  document.addEventListener('shown.bs.tab', async () => {
    // Fuerza re-sincronización visual
    const ca = (document.getElementById('biografiaCa') as HTMLInputElement | null)?.value ?? '';
    const es = (document.getElementById('biografiaEs') as HTMLInputElement | null)?.value ?? '';
    await setTrixHTML('biografiaCa', ca);
    await setTrixHTML('biografiaEs', es);
  });
}
