type Lang = 'ca' | 'es' | 'en' | 'fr' | 'it' | 'pt';

const LANGS: readonly Lang[] = ['ca', 'es', 'en', 'fr', 'it', 'pt'] as const;

interface ApiResponse<T> {
  status: string;
  message: string;
  errors: unknown[];
  data: T;
}

interface ApiRowPremsaMitja {
  id: number;
  slug: string;
  tipus: string;
  web_url: string | null;
  created_at?: string | null;
  updated_at?: string | null;
  lang: string | null; // LEFT JOIN puede devolver null si no hay i18n
  nom: string | null;
  descripcio: string | null;
}

interface MitjaBase {
  id: number;
  slug: string;
  tipus: string;
  webUrl: string | null;
  createdAt?: string | null;
  updatedAt?: string | null;
}

interface MitjaI18nEntry {
  nom: string | null;
  descripcio: string | null;
}

type MitjaI18n = Record<Lang, MitjaI18nEntry>;

interface MitjaDetall {
  base: MitjaBase;
  i18n: MitjaI18n;
}

/** ---------- Utilidades pequeñas (sin frameworks) ---------- */
function escapeHtml(input: string): string {
  return input.replaceAll('&', '&amp;').replaceAll('<', '&lt;').replaceAll('>', '&gt;').replaceAll('"', '&quot;').replaceAll("'", '&#039;');
}

function isRecord(value: unknown): value is Record<string, unknown> {
  return typeof value === 'object' && value !== null;
}

function isApiRowPremsaMitja(value: unknown): value is ApiRowPremsaMitja {
  if (!isRecord(value)) return false;

  const id = value['id'];
  const slug = value['slug'];
  const tipus = value['tipus'];

  // requeridos
  if (typeof id !== 'number') return false;
  if (typeof slug !== 'string') return false;
  if (typeof tipus !== 'string') return false;

  // opcionales / nullables
  const webUrl = value['web_url'];
  if (!(typeof webUrl === 'string' || webUrl === null || webUrl === undefined)) return false;

  const lang = value['lang'];
  if (!(typeof lang === 'string' || lang === null || lang === undefined)) return false;

  const nom = value['nom'];
  if (!(typeof nom === 'string' || nom === null || nom === undefined)) return false;

  const des = value['descripcio'];
  if (!(typeof des === 'string' || des === null || des === undefined)) return false;

  return true;
}

function normalizeLang(raw: string): Lang | null {
  const s = raw.toLowerCase().trim();
  return (LANGS as readonly string[]).includes(s) ? (s as Lang) : null;
}

function emptyI18n(): MitjaI18n {
  return {
    ca: { nom: null, descripcio: null },
    es: { nom: null, descripcio: null },
    en: { nom: null, descripcio: null },
    fr: { nom: null, descripcio: null },
    it: { nom: null, descripcio: null },
    pt: { nom: null, descripcio: null },
  };
}

/** ---------- Fetch + mapeo ---------- */

function isApiResponse(value: unknown): value is ApiResponse<unknown> {
  if (!isRecord(value)) return false;
  return typeof value['status'] === 'string' && typeof value['message'] === 'string' && Array.isArray(value['errors']) && 'data' in value;
}

/**
 * Ajusta aquí si tu backend envuelve el payload dentro de { data: [...] }.
 * Ahora mismo asumo que devuelve directamente array de filas (como en tus ejemplos).
 */
async function fetchMitjaRows(slug: string): Promise<ApiRowPremsaMitja[]> {
  const url = `/api/auxiliars/get/premsaMitja?slug=${encodeURIComponent(slug)}`;
  const res = await fetch(url, { method: 'GET', headers: { Accept: 'application/json' } });

  if (!res.ok) {
    throw new Error(`HTTP ${res.status}`);
  }

  const json: unknown = await res.json();

  // ✅ Ahora: el backend devuelve objeto wrapper
  if (!isApiResponse(json)) {
    throw new Error('Resposta inesperada: objecte API invàlid.');
  }

  const payload = json.data;

  if (!Array.isArray(payload)) {
    throw new Error('Resposta inesperada: data no és un array.');
  }

  const rows: ApiRowPremsaMitja[] = [];
  for (const item of payload) {
    if (!isApiRowPremsaMitja(item)) {
      throw new Error('Resposta inesperada: format de fila incorrecte.');
    }
    rows.push(item);
  }

  return rows;
}

function mapRowsToDetall(rows: ApiRowPremsaMitja[]): MitjaDetall {
  // El endpoint debería devolver al menos una fila (aunque sin i18n)
  const first = rows[0];
  if (!first) throw new Error("No s'ha trobat el mitjà.");

  const base: MitjaBase = {
    id: first.id,
    slug: first.slug,
    tipus: first.tipus,
    webUrl: first.web_url ?? null,
    createdAt: first.created_at ?? null,
    updatedAt: first.updated_at ?? null,
  };

  const i18n = emptyI18n();

  for (const r of rows) {
    if (typeof r.lang !== 'string') continue;
    const lang = normalizeLang(r.lang);
    if (!lang) continue;

    i18n[lang] = {
      nom: r.nom ?? null,
      descripcio: r.descripcio ?? null,
    };
  }

  return { base, i18n };
}

/** ---------- Render ---------- */

function renderMitjaDetall(target: HTMLElement, detall: MitjaDetall): void {
  const { base, i18n } = detall;

  const webUrlHtml = base.webUrl ? `<a href="${escapeHtml(base.webUrl)}" target="_blank" rel="noopener noreferrer">${escapeHtml(base.webUrl)}</a>` : `<span class="text-muted">—</span>`;

  const rowsI18n = LANGS.map((lang) => {
    const nom = i18n[lang].nom ? escapeHtml(i18n[lang].nom as string) : `<span class="text-muted">—</span>`;

    const des = i18n[lang].descripcio ? escapeHtml(i18n[lang].descripcio as string) : `<span class="text-muted">—</span>`;

    return `
      <tr>
        <td class="fw-semibold"><code>${lang}</code></td>
        <td>${nom}</td>
        <td>${des}</td>
      </tr>
    `;
  }).join('');

  target.innerHTML = `
    <div class="card shadow-sm">
      <div class="card-header d-flex justify-content-between align-items-start gap-3">
        <div>
          <h4 class="mb-1">${escapeHtml(i18n.ca.nom ?? base.slug)}</h4>
          <div class="text-muted small">
            <span><strong>Tipus:</strong> ${escapeHtml(base.tipus)}</span>
            <span class="mx-2">·</span>
            <span><strong>Slug:</strong> <code>${escapeHtml(base.slug)}</code></span>
          </div>
        </div>

        <div class="d-flex gap-2 flex-shrink-0">
          <button
            type="button"
            class="btn btn-sm btn-outline-primary"
            data-action="edit-mitja"
            data-slug="${base.slug}">
            Editar mitjà
          </button>

          <button
            type="button"
            class="btn btn-sm btn-outline-secondary"
            data-action="edit-i18n"
            data-slug="${base.slug}">
            Editar traduccions
          </button>
        </div>
      </div>

      <div class="card-body">
        <dl class="row mb-4">
          <dt class="col-sm-3 text-muted">Web</dt>
          <dd class="col-sm-9 mb-0">${webUrlHtml}</dd>
        </dl>

        <h5 class="mb-3">Traduccions</h5>

        <div class="table-responsive">
          <table class="table table-sm table-striped align-middle">
            <thead class="table-light">
              <tr>
                <th style="width: 80px;">Lang</th>
                <th>Nom</th>
                <th>Descripció</th>
              </tr>
            </thead>
            <tbody>
              ${rowsI18n}
            </tbody>
          </table>
        </div>
      </div>
    </div>
  `;

  // Eventos
  const actions = target.querySelectorAll<HTMLButtonElement>('button[data-action]');
  actions.forEach((btn) => {
    btn.addEventListener('click', () => {
      const action = btn.dataset.action ?? '';
      const slug = btn.dataset.slug ?? '';

      if (action === 'edit-mitja') {
        window.location.href = `/gestio/auxiliars/modifica-mitja-comunicacio/${encodeURIComponent(String(slug))}`;
      }

      if (action === 'edit-i18n') {
        window.location.href = `/gestio/auxiliars/modifica-mitja-comunicacio-i18n/${encodeURIComponent(String(slug))}`;
      }
    });
  });
}

/** ---------- Init ---------- */

export async function initFitxaDetallsMitja(slug: string): Promise<void> {
  const container = document.getElementById('fitxaDetallsMitja');
  if (!container) return;

  container.innerHTML = `<div class="loading">Carregant…</div>`;

  try {
    const rows = await fetchMitjaRows(slug);
    const detall = mapRowsToDetall(rows);
    renderMitjaDetall(container, detall);
  } catch (e: unknown) {
    const msg = e instanceof Error ? e.message : 'Error desconegut';
    container.innerHTML = `<div class="error">No s'ha pogut carregar el mitjà. ${escapeHtml(msg)}</div>`;
  }
}
