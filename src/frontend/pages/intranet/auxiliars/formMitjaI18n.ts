import { fetchDataGet } from '../../../services/fetchData/fetchDataGet';
import { renderFormInputs } from '../../../services/fetchData/renderInputsForm';
import { API_URLS } from '../../../services/api/ApiUrls';
import { transmissioDadesDB } from '../../../services/fetchData/transmissioDades';

type Lang = 'ca' | 'es' | 'en' | 'fr' | 'it' | 'pt';

const LANGS: readonly Lang[] = ['ca', 'es', 'en', 'fr', 'it', 'pt'] as const;

interface ApiResponse<T> {
  status: string;
  message: string;
  errors?: unknown[];
  data: T;
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

interface MitjaI18nFormData {
  id: number;
  slug: string;
  tipus: string;
  web_url: string | null;

  // Campos por idioma (coinciden con el HTML)
  nom_ca: string;
  descripcio_ca: string;

  nom_es: string;
  descripcio_es: string;

  nom_en: string;
  descripcio_en: string;

  nom_fr: string;
  descripcio_fr: string;

  nom_it: string;
  descripcio_it: string;

  nom_pt: string;
  descripcio_pt: string;
}

function normalizeLang(raw: string): Lang | null {
  const s = raw.toLowerCase().trim();
  return (LANGS as readonly string[]).includes(s) ? (s as Lang) : null;
}

function emptyFormData(): MitjaI18nFormData {
  return {
    id: 0,
    slug: '',
    tipus: '',
    web_url: null,

    nom_ca: '',
    descripcio_ca: '',

    nom_es: '',
    descripcio_es: '',

    nom_en: '',
    descripcio_en: '',

    nom_fr: '',
    descripcio_fr: '',

    nom_it: '',
    descripcio_it: '',

    nom_pt: '',
    descripcio_pt: '',
  };
}

function mapRowsToI18nFormData(rows: ApiRowPremsaMitja[]): MitjaI18nFormData {
  const first = rows[0];
  if (!first) throw new Error("No s'ha trobat el mitjà.");

  const out = emptyFormData();
  out.id = first.id;
  out.slug = first.slug;
  out.tipus = first.tipus;
  out.web_url = first.web_url ?? null;

  for (const r of rows) {
    if (typeof r.lang !== 'string') continue;
    const lang = normalizeLang(r.lang);
    if (!lang) continue;

    const nom = r.nom ?? '';
    const des = r.descripcio ?? '';

    switch (lang) {
      case 'ca':
        out.nom_ca = nom;
        out.descripcio_ca = des;
        break;
      case 'es':
        out.nom_es = nom;
        out.descripcio_es = des;
        break;
      case 'en':
        out.nom_en = nom;
        out.descripcio_en = des;
        break;
      case 'fr':
        out.nom_fr = nom;
        out.descripcio_fr = des;
        break;
      case 'it':
        out.nom_it = nom;
        out.descripcio_it = des;
        break;
      case 'pt':
        out.nom_pt = nom;
        out.descripcio_pt = des;
        break;
    }
  }

  return out;
}

function renderInfoCard(data: MitjaI18nFormData): void {
  const infoSlug = document.getElementById('infoSlug');
  const infoTipus = document.getElementById('infoTipus');
  const infoWeb = document.getElementById('infoWeb');

  if (infoSlug) infoSlug.textContent = data.slug || '—';
  if (infoTipus) infoTipus.textContent = data.tipus || '—';

  if (infoWeb) {
    if (data.web_url && data.web_url.trim().length > 0) {
      // Link bonito
      infoWeb.innerHTML = `<a href="${data.web_url}" target="_blank" rel="noopener noreferrer">${data.web_url}</a>`;
    } else {
      infoWeb.textContent = '—';
    }
  }
}

/**
 * Carga + rellena + añade submit PUT para traducciones
 */
export async function formMitjaPremsaI18n(slugMitja: string): Promise<void> {
  const divTitol = document.getElementById('titolForm') as HTMLDivElement | null;
  const form = document.getElementById('mitjaI18nForm') as HTMLFormElement | null;

  const hiddenSlug = document.getElementById('slug') as HTMLInputElement | null;
  const btnSubmit = document.getElementById('btnMitjaI18n') as HTMLButtonElement | null;

  if (!divTitol || !form) return;
  if (!slugMitja || slugMitja.trim().length === 0) return;

  // título inicial
  divTitol.innerHTML = `<h2>Traduccions del mitjà: ${slugMitja}</h2>`;

  const response = await fetchDataGet<ApiResponse<ApiRowPremsaMitja[]>>(API_URLS.GET.FITXA_MITJA(slugMitja), true);

  if (!response || response.status !== 'success' || !response.data) return;

  const data = mapRowsToI18nFormData(response.data);

  // título definitivo
  const titol = data.nom_ca.trim().length > 0 ? data.nom_ca : data.slug;
  divTitol.innerHTML = `<h2>Traduccions del mitjà: ${titol}</h2>`;

  // ✅ asegurar que el form tenga el slug para el PUT
  if (hiddenSlug) hiddenSlug.value = data.slug;

  // Rellenar inputs
  renderFormInputs(data as unknown as Record<string, unknown>);

  // Info card
  renderInfoCard(data);

  // Etiqueta del botón (opcional)
  if (btnSubmit) btnSubmit.textContent = 'Desar traduccions';

  // Submit PUT (una sola vez)
  form.addEventListener('submit', function (event) {
    transmissioDadesDB(event, 'PUT', 'mitjaI18nForm', API_URLS.PUT.PREMSA_MITJA_I18N);
  });
}
