import { transmissioDadesDB } from '../../../services/fetchData/transmissioDades';
import { API_URLS } from '../../../services/api/ApiUrls';
import { fetchDataGet } from '../../../services/fetchData/fetchDataGet';
import { renderFormInputs } from '../../../services/fetchData/renderInputsForm';

interface ApiResponse<T> {
  status: string;
  message: string;
  errors?: unknown[];
  data: T;
}

interface MitjaFormData {
  id: number;
  slug: string;
  tipus: string;
  web_url: string | null;
  nom_ca: string;
  descripcio_ca: string;
}

interface ApiRowPremsaMitja {
  id: number;
  slug: string;
  tipus: string;
  web_url: string | null;
  created_at?: string | null;
  updated_at?: string | null;
  lang: string | null;
  nom: string | null;
  descripcio: string | null;
}

function mapRowsToMitjaFormData(rows: ApiRowPremsaMitja[]): MitjaFormData {
  const first = rows[0];
  if (!first) throw new Error("No s'ha trobat el mitjà.");

  // buscar la fila CA si existe
  const ca = rows.find((r) => r.lang === 'ca') ?? null;

  return {
    id: first.id,
    slug: first.slug,
    tipus: first.tipus,
    web_url: first.web_url ?? null,
    nom_ca: ca?.nom ?? '',
    descripcio_ca: ca?.descripcio ?? '',
  };
}

export async function formMitjaPremsa(isUpdate: boolean, slugMitja?: string): Promise<void> {
  const divTitol = document.getElementById('titolForm') as HTMLDivElement | null;
  const btnSubmit = document.getElementById('btnMitja') as HTMLButtonElement | null;
  const form = document.getElementById('mitjaForm') as HTMLFormElement | null;
  const btnTraduccions = document.getElementById('btnAnarTraduccions') as HTMLAnchorElement | null;

  if (!divTitol || !btnSubmit || !form) return;

  let data: Partial<MitjaFormData> = {
    id: 0,
    slug: '',
    tipus: '',
    web_url: null,
    nom_ca: '',
    descripcio_ca: '',
  };

  if (isUpdate) {
    if (!slugMitja || slugMitja.trim().length === 0) return;

    const response = await fetchDataGet<ApiResponse<ApiRowPremsaMitja[]>>(API_URLS.GET.FITXA_MITJA(slugMitja), true);
    if (!response || response.status !== 'success' || !response.data) return;

    data = mapRowsToMitjaFormData(response.data);

    // ✅ Pintar siempre, aunque nom_ca esté vacío
    const titol = (data.nom_ca ?? '').trim().length > 0 ? (data.nom_ca as string) : (data.slug ?? slugMitja);
    divTitol.innerHTML = `<h2>Modificació mitjà: ${titol}</h2>`;

    renderFormInputs(data);
    btnSubmit.textContent = 'Modificar dades';

    // ✅ Mostrar traducciones solo si tenemos id válido
    if (btnTraduccions && typeof data.id === 'number' && data.id > 0) {
      btnTraduccions.style.display = 'inline-block';
      btnTraduccions.href = `/gestio/auxiliars/modifica-mitja-comunicacio-i18n/${encodeURIComponent(String(data.id))}`;
    } else if (btnTraduccions) {
      btnTraduccions.style.display = 'none';
      btnTraduccions.href = '#';
    }

    form.addEventListener('submit', function (event) {
      transmissioDadesDB(event, 'PUT', 'mitjaForm', API_URLS.PUT.PREMSA_MITJA);
    });
  } else {
    divTitol.innerHTML = `<h2>Creació de nou mitjà de comunicació</h2>`;
    btnSubmit.textContent = 'Inserir dades';

    if (btnTraduccions) {
      btnTraduccions.style.display = 'none';
      btnTraduccions.href = '#';
    }

    form.addEventListener('submit', function (event) {
      transmissioDadesDB(event, 'POST', 'mitjaForm', API_URLS.POST.PREMSA_MITJA, true);
    });
  }
}
