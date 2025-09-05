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
  // Campos usados más abajo:
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

// Utilidad: espera a que exista un selector en el DOM (máx 1s)
async function waitForEl<T extends HTMLElement>(sel: string, timeoutMs = 1000): Promise<T> {
  const start = performance.now();
  while (performance.now() - start < timeoutMs) {
    const el = document.querySelector(sel) as T | null;
    if (el) return el;
    await new Promise((r) => setTimeout(r, 50));
  }
  throw new Error(`Elemento no encontrado: ${sel}`);
}

// Utilidad: intenta obtener el id del URL si no viene por parámetro
function getIdFromURL(): number | undefined {
  // /biografies/modificar/123  ó  ?id=123
  const m = location.pathname.match(/(\d+)(?:\/)?$/);
  if (m) return parseInt(m[1], 10);
  const q = new URLSearchParams(location.search).get('id');
  return q ? parseInt(q, 10) : undefined;
}

export async function formBiografies(isUpdate: boolean, id?: number) {
  // 1) Asegura DOM
  const divTitol = await waitForEl<HTMLDivElement>('#titolForm').catch(() => null);
  const btnBiografies = await waitForEl<HTMLButtonElement>('#btnBiografies').catch(() => null);
  const biografiesForm = await waitForEl<HTMLFormElement>('#BiografiesForm').catch(() => null);
  if (!divTitol || !btnBiografies || !biografiesForm) return;

  // 2) Asegura ID (si no vino aún)
  let theId = id ?? getIdFromURL();

  // Si estamos en update pero aún no tenemos id, espera brevemente (race con el router)
  if (isUpdate && (theId === undefined || Number.isNaN(theId))) {
    for (let i = 0; i < 10 && (theId === undefined || Number.isNaN(theId)); i++) {
      await new Promise((r) => setTimeout(r, 50));
      theId = id ?? getIdFromURL();
    }
  }

  // Estado base
  let data: Partial<Fitxa> = {
    comarca: 0,
    provincia: 0,
    comunitat: 0,
    estat: 0,
    arxiu: '',
    ciutat: 0,
  };

  if (isUpdate) {
    if (!theId) {
      // No hay id -> no podemos cargar datos de modificación
      divTitol.innerHTML = `<h2>Biografies: modificació</h2><p>No s'ha trobat l'ID de la fitxa.</p>`;
      return;
    }

    const response = await fetchDataGet<ApiResponse<Fitxa>>(API_URLS.GET.BIOGRAFIES_ID(theId), true);
    if (!response || !response.data) {
      divTitol.innerHTML = `<h2>Biografies: modificació</h2><p>No s'han pogut carregar les dades.</p>`;
      return;
    }
    data = response.data;

    const nom = (data.nom as string) || '';
    const c1 = (data.cognom1 as string) || '';
    const c2 = (data.cognom2 as string) || '';
    const slug = (data.slug as string) || '';

    // 👇 corregido el href (comilla cerrada)
    divTitol.innerHTML = `
      <h2>Biografies: modificació de biografies</h2>
      <h4>
        Fitxa represaliat:
        <a href="https://memoriaterrassa.cat/fitxa/${slug}" target="_blank" rel="noopener noreferrer">
          ${[nom, c1, c2].filter(Boolean).join(' ')}
        </a>
      </h4>
    `;

    renderFormInputs(data);
    btnBiografies.textContent = 'Modificar dades';

    // Evita duplicar listeners si llamas a formBiografies más de una vez
    biografiesForm.addEventListener('submit', onSubmitUpdate, { once: true });
  } else {
    // CREAR
    if (theId) {
      const response = await fetchDataGet<ApiResponse2<Fitxa[]>>(API_URLS.GET.REPRESALIAT_ID(theId), true);
      if (!response?.data || response.data.length === 0) {
        divTitol.innerHTML = `<h2>Biografies: creació</h2><p>No s'han trobat dades de la fitxa.</p>`;
        return;
      }

      const fitxa = response.data[0];
      const nomComplet = [fitxa.nom as string, fitxa.cognom1 as string, fitxa.cognom2 as string].filter(Boolean).join(' ');
      const slug = (fitxa.slug as string) || '';

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
      // Creación sin id (p.ej. biografía genérica)
      divTitol.innerHTML = `<h2>Biografies: creació de nova biografia</h2>`;
      renderFormInputs(data);
    }

    btnBiografies.textContent = 'Inserir dades';
    biografiesForm.addEventListener('submit', onSubmitCreate, { once: true });
  }

  function onSubmitUpdate(event: Event) {
    transmissioDadesDB(event, 'PUT', 'BiografiesForm', API_URLS.PUT.BIOGRAFIES);
  }
  function onSubmitCreate(event: Event) {
    transmissioDadesDB(event, 'POST', 'BiografiesForm', API_URLS.POST.BIOGRAFIES, false, 'hide');
  }
}
