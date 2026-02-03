import { auxiliarSelect } from '../../../services/fetchData/auxiliarSelect';
import { transmissioDadesDB } from '../../../services/fetchData/transmissioDades';
import { API_URLS } from '../../../services/api/ApiUrls';
import { fetchDataGet } from '../../../services/fetchData/fetchDataGet';
import { renderFormInputs } from '../../../services/fetchData/renderInputsForm';

interface FitxaAparicio {
  [key: string]: unknown;

  // base
  id: number;
  data_aparicio: string; // YYYY-MM-DD
  tipus_aparicio: string;
  mitja_id: number;
  url_noticia: string | null;
  image_id: number | null;
  destacat: number; // 0/1
  estat: 'draft' | 'publicat';
}

interface ApiResponse<T> {
  status: string;
  message: string;
  data: T;
}

/**
 * Form create/update per db_premsa_aparicions (sense i18n)
 * - Create: POST
 * - Update: PUT (quan existeixi fitxa per id)
 */
export async function formAparicioPremsa(isUpdate: boolean, id?: number) {
  const divTitol = document.getElementById('titolForm') as HTMLDivElement;
  const btnAparicio = document.getElementById('btnAparicio') as HTMLButtonElement;
  const aparicioForm = document.getElementById('aparicioForm') as HTMLFormElement;

  let data: Partial<FitxaAparicio> = {
    mitja_id: 0,
    destacat: 0,
    estat: 'publicat',
    url_noticia: null,
    image_id: null,
  };

  if (!divTitol || !btnAparicio || !aparicioForm) return;

  // UPDATE: carrega fitxa i omple inputs
  if (id && isUpdate) {
    const response = await fetchDataGet<ApiResponse<FitxaAparicio>>(API_URLS.GET.APARICIO_ID(id), true);
    if (!response || !response.data) return;

    data = response.data;

    divTitol.innerHTML = `
      <h4 class="mb-0">Modifica aparició #${data.id}</h4>
      <div class="text-muted small">Dades generals (sense traduccions)</div>
    `;

    renderFormInputs(data);

    btnAparicio.textContent = 'Modificar aparició';

    aparicioForm.addEventListener('submit', function (event) {
      transmissioDadesDB(event, 'PUT', 'aparicioForm', API_URLS.PUT.APARICIO);
    });
  } else {
    // CREATE
    divTitol.innerHTML = `
      <h4 class="mb-0">Crear nova aparició</h4>
      <div class="text-muted small">Dades generals (sense traduccions)</div>
    `;

    btnAparicio.textContent = 'Desa aparició';

    aparicioForm.addEventListener('submit', function (event) {
      transmissioDadesDB(event, 'POST', 'aparicioForm', API_URLS.POST.APARICIO, true);
    });
  }

  /**
   * Selects
   * - mitja_id: lo cargamos desde "premsaMitjans" (igual que otros)
   * - tipus_aparicio: si tu auxiliarSelect depende de un endpoint,
   *   crea un auxiliar "tipusAparicio" en /api/auxiliars/get/... y úsalo aquí.
   */

  // Mitjans (select #mitja_id)
  await auxiliarSelect(data.mitja_id ?? 0, 'premsaMitjans', 'mitja_id', 'nom');

  // Tipus aparicio (select #tipus_aparicio)
  await auxiliarSelect(data.tipus_aparicio ?? '', 'premsaTipusAparicio', 'tipus_aparicio', 'nom');

  await auxiliarSelect(data.image_id ?? 0, 'premsaImatges', 'image_id', 'nomImatge');

  /**
   * Normalización checkbox destacat (si tu transmissioDadesDB no lo hace)
   * - aseguramos que el input tenga value="1" (ya lo tiene en el HTML)
   * - y si está unchecked, que no se quede "null": añadimos hidden fallback
   */
  const destacat = document.getElementById('destacat') as HTMLInputElement | null;
  if (destacat) {
    const hidden = document.createElement('input');
    hidden.type = 'hidden';
    hidden.name = 'destacat';
    hidden.value = '0';
    aparicioForm.appendChild(hidden);

    destacat.addEventListener('change', () => {
      // si está marcado, se enviará '1' del checkbox + '0' del hidden,
      // pero en PHP normalmente pillas el último o haces cast.
      // Si quieres evitar duplicado, me dices cómo parseas POST y lo ajusto.
    });
  }
}
