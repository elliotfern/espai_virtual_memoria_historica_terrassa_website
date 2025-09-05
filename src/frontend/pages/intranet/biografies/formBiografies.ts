import { transmissioDadesDB } from '../../../services/fetchData/transmissioDades';
import { API_URLS } from '../../../services/api/ApiUrls';
import { fetchDataGet } from '../../../services/fetchData/fetchDataGet';
import { renderFormInputs } from '../../../services/fetchData/renderInputsForm';

interface Fitxa {
  [key: string]: unknown;
  status: string;
  message: string;
  id: number;
  ciutat: number;
  comarca: number;
  provincia: number;
  comunitat: number;
  estat: number;
  arxiu: string;

  // campos que usamos para pintar título/enlace
  slug?: string;
  nom?: string;
  cognom1?: string;
  cognom2?: string;
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

/**
 * Inicializa mejoras de UI (Choices, datepickers, etc.) dentro del form ya renderizado.
 * Déjalo como no-op si no usas nada, o llama aquí a tus inicializadores.
 */
function hydrateEnhancers(): void {
  // Ejemplos:
  // initChoicesWithin(root);
  // initDatepickersWithin(root);
}

/**
 * Ata el submit al <form> ACTUAL (el visible tras renderFormInputs), con once:true para evitar duplicados.
 */
function attachSubmit(form: HTMLFormElement, method: 'POST' | 'PUT', url: string): void {
  const handler = (event: Event) => {
    transmissioDadesDB(event, method, 'BiografiesForm', url, method === 'POST', method === 'POST' ? 'hide' : undefined);
  };
  form.addEventListener('submit', handler, { once: true });
}

export async function formBiografies(isUpdate: boolean, id?: number) {
  // ——— contenedor de título (mínimo requerido)
  const divTitol = document.getElementById('titolForm') as HTMLDivElement | null;
  if (!divTitol) return;

  // Estado base por si no hay datos
  let data: Partial<Fitxa> = {
    comarca: 0,
    provincia: 0,
    comunitat: 0,
    estat: 0,
    arxiu: '',
    ciutat: 0,
  };

  if (id && isUpdate) {
    // —— MODIFICAR
    const response = await fetchDataGet<ApiResponse<Fitxa>>(API_URLS.GET.BIOGRAFIES_ID(id), true);
    if (!response?.data) {
      divTitol.innerHTML = `<h2>Biografies: modificació</h2><p>No s'han pogut carregar les dades.</p>`;
      return;
    }

    data = response.data;

    const nom = (data.nom as string) ?? '';
    const c1 = (data.cognom1 as string) ?? '';
    const c2 = (data.cognom2 as string) ?? '';
    const slug = (data.slug as string) ?? '';

    // ¡Comilla cerrada en href y rel seguro!
    divTitol.innerHTML = `
      <h2>Biografies: modificació de biografies</h2>
      <h4>
        Fitxa represaliat:
        <a href="https://memoriaterrassa.cat/fitxa/${slug}" target="_blank" rel="noopener noreferrer">
          ${[nom, c1, c2].filter(Boolean).join(' ')}
        </a>
      </h4>
    `;

    // Render del formulario (puede reemplazar nodos internos)
    renderFormInputs(data);

    // Re-selecciona referencias AHORA (después del render)
    const btnBiografies = document.getElementById('btnBiografies') as HTMLButtonElement | null;
    const biografiesForm = document.getElementById('BiografiesForm') as HTMLFormElement | null;
    if (!btnBiografies || !biografiesForm) return;

    btnBiografies.textContent = 'Modificar dades';

    // Hidrata widgets UI si procede
    hydrateEnhancers();

    // Ata submit sobre el form actual
    attachSubmit(biografiesForm, 'PUT', API_URLS.PUT.BIOGRAFIES);
  } else {
    // —— CREAR
    if (id) {
      // Para crear con datos de la fitxa base (array en la API)
      const response = await fetchDataGet<ApiResponse2<Fitxa[]>>(API_URLS.GET.REPRESALIAT_ID(id), true);
      const fitxa = response?.data?.[0];
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
    } else {
      // Creación “en blanco”
      divTitol.innerHTML = `<h2>Biografies: creació de nova biografia</h2>`;
      renderFormInputs(data);
    }

    // Re-selecciona referencias tras el render
    const btnBiografies = document.getElementById('btnBiografies') as HTMLButtonElement | null;
    const biografiesForm = document.getElementById('BiografiesForm') as HTMLFormElement | null;
    if (!btnBiografies || !biografiesForm) return;

    btnBiografies.textContent = 'Inserir dades';

    hydrateEnhancers();
    attachSubmit(biografiesForm, 'POST', API_URLS.POST.BIOGRAFIES);
  }

  // Si el form vive dentro de tabs (Bootstrap), rehidrata cuando la tab se muestra
  document.addEventListener('shown.bs.tab', (e) => {
    const targetId = (e.target as HTMLElement)?.getAttribute?.('data-bs-target') || '';
    if (targetId && (targetId.includes('Biografies') || targetId.includes('biografies'))) {
      const form = document.getElementById('BiografiesForm') as HTMLFormElement | null;
      if (form) hydrateEnhancers();
    }
  });
}
