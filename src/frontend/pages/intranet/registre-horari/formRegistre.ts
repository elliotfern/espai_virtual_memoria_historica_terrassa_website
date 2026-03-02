import { auxiliarSelect } from '../../../services/fetchData/auxiliarSelect';
import { transmissioDadesDB } from '../../../services/fetchData/transmissioDades';
import { API_URLS } from '../../../services/api/ApiUrls';
import { fetchDataGet } from '../../../services/fetchData/fetchDataGet';
import { renderFormInputs } from '../../../services/fetchData/renderInputsForm';

interface FitxaHores {
  [key: string]: unknown;

  id: number;
  user_uuid: string; // normalmente vendrá como UUID string (no binario)
  dia: string; // YYYY-MM-DD
  hores: number;
  tipusId: number;
  descripcio: string | null;
}

interface ApiResponse<T> {
  status: string;
  message: string;
  data: T;
}

/**
 * Formulari Hores (POST create / PUT update)
 * - isUpdate=true + id => carga fitxa i fa PUT
 * - isUpdate=false => form buit i fa POST
 */
export async function formHores(isUpdate: boolean, id?: number) {
  const divTitol = document.getElementById('titolForm') as HTMLDivElement | null;
  const btnHores = document.getElementById('btnHores') as HTMLButtonElement | null;
  const horesForm = document.getElementById('HoresForm') as HTMLFormElement | null;

  let data: Partial<FitxaHores> = {
    id: 0,
    user_uuid: '',
    dia: '',
    hores: 0,
    tipusId: 0,
    descripcio: '',
  };

  if (!divTitol || !btnHores || !horesForm) return;

  // --- UPDATE ---
  if (id && isUpdate) {
    const response = await fetchDataGet<ApiResponse<FitxaHores>>(API_URLS.GET.HORES_ID(id), true);

    if (!response || !response.data) return;
    data = response.data;

    divTitol.innerHTML = `<h2>Control d'hores: modificació del registre del dia ${data.dia ?? ''}</h2>`;

    // Omple inputs del form a partir de les claus de "data"
    renderFormInputs(data);

    btnHores.textContent = 'Modificar dades';

    horesForm.addEventListener('submit', function (event) {
      transmissioDadesDB(event, 'PUT', 'HoresForm', API_URLS.PUT.HORES);
    });
  } else {
    // --- CREATE ---
    divTitol.innerHTML = `<h2>Control d'hores: nou registre</h2>`;
    btnHores.textContent = 'Inserir dades';

    horesForm.addEventListener('submit', function (event) {
      transmissioDadesDB(event, 'POST', 'HoresForm', API_URLS.POST.HORES, true);
    });
  }

  // --- Carregar desplegable de tipus de tasca ---
  // Ajusta "tipusTasca" al slug real del teu endpoint auxiliar
  // i "tipusId" al name/id del <select>
  await auxiliarSelect(data.tipusId ?? 0, 'tipusTasca', 'tipusId', 'nom');
}
