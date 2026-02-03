// formAparicioPremsaI18n.ts
import { transmissioDadesDB } from '../../../services/fetchData/transmissioDades';
import { API_URLS } from '../../../services/api/ApiUrls';
import { fetchDataGet } from '../../../services/fetchData/fetchDataGet';
import { renderFormInputs } from '../../../services/fetchData/renderInputsForm';

type Lang = 'ca' | 'es' | 'en' | 'fr' | 'it' | 'pt';
const LANGS: readonly Lang[] = ['ca', 'es', 'en', 'fr', 'it', 'pt'] as const;

type ApiResponseArr<T> = {
  status: string;
  message: string;
  data: T[];
};

interface RowAparicioI18n {
  [key: string]: unknown;

  aparicio_id: number;
  lang: string;

  titol: string | null;
  resum: string | null;
  notes: string | null;
  pdf_url: string | null;

  // info base (repetida en cada fila)
  data_aparicio: string | null;
  tipus_aparicio: string | null;
  mitja_id: number | null;
  url_noticia: string | null;
  nomMitja: string | null;
}

function escapeHtml(input: string): string {
  return input.replaceAll('&', '&amp;').replaceAll('<', '&lt;').replaceAll('>', '&gt;').replaceAll('"', '&quot;').replaceAll("'", '&#039;');
}

function normalizeLang(raw: string): Lang | null {
  const s = raw.toLowerCase().trim();
  return (LANGS as readonly string[]).includes(s) ? (s as Lang) : null;
}

function setInfoText(id: string, value: string): void {
  const el = document.getElementById(id);
  if (!el) return;
  el.textContent = value;
}

function setInfoUrl(id: string, url: string | null): void {
  const el = document.getElementById(id);
  if (!el) return;

  if (!url) {
    el.innerHTML = `<span class="text-muted">—</span>`;
    return;
  }

  const safe = escapeHtml(url);
  el.innerHTML = `<a href="${safe}" target="_blank" rel="noopener noreferrer">${safe}</a>`;
}

function buildPayloadFromRows(id: number, rows: RowAparicioI18n[]): Record<string, unknown> {
  const payload: Record<string, unknown> = { id };

  // inicializar todos los campos para que renderFormInputs los pinte (aunque no existan)
  for (const l of LANGS) {
    payload[`titol_${l}`] = '';
    payload[`resum_${l}`] = '';
    payload[`notes_${l}`] = '';
    payload[`pdf_url_${l}`] = '';
  }

  // mapear lo que venga desde la API
  for (const r of rows) {
    const lang = normalizeLang(r.lang);
    if (!lang) continue;

    payload[`titol_${lang}`] = r.titol ?? '';
    payload[`resum_${lang}`] = r.resum ?? '';
    payload[`notes_${lang}`] = r.notes ?? '';
    payload[`pdf_url_${lang}`] = r.pdf_url ?? '';
  }

  return payload;
}

/**
 * Form UPDATE per db_premsa_aparicions_i18n (traduccions)
 * - Update: PUT (sempre en bloc)
 */
export async function formAparicioPremsaI18n(id?: number) {
  const divTitol = document.getElementById('titolForm') as HTMLDivElement;
  const btn = document.getElementById('btnAparicioI18n') as HTMLButtonElement;
  const form = document.getElementById('aparicioI18nForm') as HTMLFormElement;

  if (!divTitol || !btn || !form) return;
  if (!id) return;

  // GET rows (6 idiomes)
  const response = await fetchDataGet<ApiResponseArr<RowAparicioI18n>>(API_URLS.GET.APARICIO_PREMSA_I18N_ID(id), true);
  if (!response || !Array.isArray(response.data) || !response.data[0]) return;

  const rows = response.data;

  // hidden id
  const hiddenId = document.getElementById('id') as HTMLInputElement | null;
  if (hiddenId) hiddenId.value = String(id);

  // info (cojo la primera fila)
  const first = rows[0];

  setInfoText('infoId', String(first.aparicio_id));
  setInfoText('infoDataAparicio', first.data_aparicio ?? '—');
  setInfoText('infoTipusAparicio', first.tipus_aparicio ?? '—');
  setInfoText('infoNomMitja', first.nomMitja ?? '—');
  setInfoUrl('infoUrlNoticia', first.url_noticia ?? null);

  // título del form
  divTitol.innerHTML = `
    <h4 class="mb-0">Modificar traduccions aparició #${first.aparicio_id}</h4>
    <div class="text-muted small">Edició en bloc (tots els idiomes)</div>
  `;

  // payload -> inputs
  const payload = buildPayloadFromRows(first.aparicio_id, rows);
  renderFormInputs(payload as Record<string, unknown>);

  btn.textContent = 'Desar traduccions';

  // submit PUT
  form.addEventListener('submit', function (event) {
    transmissioDadesDB(event, 'PUT', 'aparicioI18nForm', API_URLS.PUT.APARICIO_PREMSA_I18N);
  });
}
