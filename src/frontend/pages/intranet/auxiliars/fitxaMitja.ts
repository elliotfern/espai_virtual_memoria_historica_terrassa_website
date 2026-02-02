type Lang = 'ca' | 'es' | 'en' | 'fr' | 'it' | 'pt';

const LANGS: readonly Lang[] = ['ca', 'es', 'en', 'fr', 'it', 'pt'] as const;

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

  if (!Array.isArray(json)) {
    throw new Error('Resposta inesperada: no és un array.');
  }

  const rows: ApiRowPremsaMitja[] = [];
  for (const item of json) {
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

  const webUrlHtml = base.webUrl ? `<a href="${escapeHtml(base.webUrl)}" target="_blank" rel="noopener noreferrer">${escapeHtml(base.webUrl)}</a>` : `<span class="muted">—</span>`;

  const rowsI18n = LANGS.map((lang) => {
    const nom = i18n[lang].nom ? escapeHtml(i18n[lang].nom as string) : `<span class="muted">—</span>`;
    const des = i18n[lang].descripcio ? escapeHtml(i18n[lang].descripcio as string) : `<span class="muted">—</span>`;
    return `
      <tr>
        <td><code>${lang}</code></td>
        <td>${nom}</td>
        <td>${des}</td>
      </tr>
    `;
  }).join('');

  target.innerHTML = `
    <div class="fitxa">
      <div class="fitxa__header">
        <div class="fitxa__title">
          <h2>${escapeHtml(i18n.ca.nom ?? base.slug)}</h2>
          <div class="fitxa__subtitle">
            <span><strong>Tipus:</strong> ${escapeHtml(base.tipus)}</span>
            <span class="sep">·</span>
            <span><strong>Slug:</strong> <code>${escapeHtml(base.slug)}</code></span>
          </div>
        </div>

        <div class="fitxa__actions">
          <button type="button" class="btn" data-action="edit-mitja" data-id="${base.id}">
            Editar mitjà
          </button>
          <button type="button" class="btn" data-action="edit-i18n" data-id="${base.id}">
            Editar traduccions
          </button>
        </div>
      </div>

      <div class="fitxa__body">
        <div class="kv">
          <div class="kv__row">
            <div class="kv__k">Web</div>
            <div class="kv__v">${webUrlHtml}</div>
          </div>
        </div>

        <h3>Traduccions</h3>
        <div class="table-wrap">
          <table class="table">
            <thead>
              <tr>
                <th>Lang</th>
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

  // Bind eventos (delegación simple)
  const actions = target.querySelectorAll<HTMLButtonElement>('button[data-action]');
  actions.forEach((btn) => {
    btn.addEventListener('click', () => {
      const action = btn.dataset.action ?? '';
      const idStr = btn.dataset.id ?? '';
      const id = Number(idStr);

      if (!Number.isFinite(id)) return;

      if (action === 'edit-mitja') {
        // Ejemplo: redirigir a tu página de edición
        window.location.href = `/gestio/auxiliars/modifica-mitja-comunicacio/${encodeURIComponent(String(id))}`;
      }

      if (action === 'edit-i18n') {
        // Ejemplo: redirigir a edición de traducciones
        window.location.href = `/gestio/auxiliars/modifica-mitja-comunicacio-i18n/${encodeURIComponent(String(id))}`;
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
