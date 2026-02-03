// fitxaAparicioMitja.ts
type Lang = 'ca' | 'es' | 'en' | 'fr' | 'it' | 'pt';
const LANGS: readonly Lang[] = ['ca', 'es', 'en', 'fr', 'it', 'pt'] as const;

interface ApiResponse<T> {
  status: string;
  message: string;
  errors: unknown[];
  data: T;
}

interface ApiRowPremsaAparicio {
  // base
  id: number;
  data_aparicio: string; // YYYY-MM-DD
  tipus_aparicio: string;
  mitja_id: number;
  url_noticia: string | null;
  image_id: number | null;
  destacat: number; // 0/1
  estat: 'draft' | 'publicat';

  created_at?: string | null;
  updated_at?: string | null;

  // ✅ datos de la imagen (LEFT JOIN imatges)
  image_nomArxiu: string | null;
  image_mime: string | null;

  // i18n
  lang: string | null;
  titol: string | null;
  resum: string | null;
  notes: string | null;
  pdf_url: string | null;
}

interface AparicioBase {
  id: number;
  dataAparicio: string;
  tipusAparicio: string;
  mitjaId: number;
  urlNoticia: string | null;
  imageId: number | null;
  destacat: number;
  estat: 'draft' | 'publicat';
  createdAt?: string | null;
  updatedAt?: string | null;

  // ✅ imagen
  imageNomArxiu: string | null;
  imageMime: string | null;
  imageUrl: string | null;
}

interface AparicioI18nEntry {
  titol: string | null;
  resum: string | null;
  notes: string | null;
  pdfUrl: string | null;
}
type AparicioI18n = Record<Lang, AparicioI18nEntry>;

interface AparicioDetall {
  base: AparicioBase;
  i18n: AparicioI18n;
}

/** ---------- Utils ---------- */
function escapeHtml(input: string): string {
  return input.replaceAll('&', '&amp;').replaceAll('<', '&lt;').replaceAll('>', '&gt;').replaceAll('"', '&quot;').replaceAll("'", '&#039;');
}

function isRecord(value: unknown): value is Record<string, unknown> {
  return typeof value === 'object' && value !== null;
}

function isApiResponse(value: unknown): value is ApiResponse<unknown> {
  if (!isRecord(value)) return false;
  return typeof value['status'] === 'string' && typeof value['message'] === 'string' && Array.isArray(value['errors']) && 'data' in value;
}

function normalizeLang(raw: string): Lang | null {
  const s = raw.toLowerCase().trim();
  return (LANGS as readonly string[]).includes(s) ? (s as Lang) : null;
}

function emptyI18n(): AparicioI18n {
  return {
    ca: { titol: null, resum: null, notes: null, pdfUrl: null },
    es: { titol: null, resum: null, notes: null, pdfUrl: null },
    en: { titol: null, resum: null, notes: null, pdfUrl: null },
    fr: { titol: null, resum: null, notes: null, pdfUrl: null },
    it: { titol: null, resum: null, notes: null, pdfUrl: null },
    pt: { titol: null, resum: null, notes: null, pdfUrl: null },
  };
}

function mimeToExt(mime: string | null): string {
  switch (mime) {
    case 'image/jpeg':
      return 'jpg';
    case 'image/png':
      return 'png';
    case 'application/pdf':
      return 'pdf';
    default:
      return '';
  }
}

function buildPremsaImageUrl(nomArxiu: string | null, mime: string | null): string | null {
  if (!nomArxiu) return null;
  const ext = mimeToExt(mime);
  if (!ext) return null;
  return `https://media.memoriaterrassa.cat/assets_premsa/${encodeURIComponent(nomArxiu)}.${ext}`;
}

function isApiRowPremsaAparicio(value: unknown): value is ApiRowPremsaAparicio {
  if (!isRecord(value)) return false;

  // base requeridos
  if (typeof value['id'] !== 'number') return false;
  if (typeof value['data_aparicio'] !== 'string') return false;
  if (typeof value['tipus_aparicio'] !== 'string') return false;
  if (typeof value['mitja_id'] !== 'number') return false;

  // nullables base
  const urlNoticia = value['url_noticia'];
  if (!(typeof urlNoticia === 'string' || urlNoticia === null || urlNoticia === undefined)) return false;

  const imageId = value['image_id'];
  if (!(typeof imageId === 'number' || imageId === null || imageId === undefined)) return false;

  const destacat = value['destacat'];
  if (!(typeof destacat === 'number' || destacat === null || destacat === undefined)) return false;

  const estat = value['estat'];
  if (!(typeof estat === 'string' || estat === null || estat === undefined)) return false;

  // timestamps
  const created = value['created_at'];
  if (!(typeof created === 'string' || created === null || created === undefined)) return false;

  const updated = value['updated_at'];
  if (!(typeof updated === 'string' || updated === null || updated === undefined)) return false;

  // ✅ imagen (LEFT JOIN)
  const nomArxiu = value['image_nomArxiu'];
  if (!(typeof nomArxiu === 'string' || nomArxiu === null || nomArxiu === undefined)) return false;

  const mime = value['image_mime'];
  if (!(typeof mime === 'string' || mime === null || mime === undefined)) return false;

  // i18n (LEFT JOIN)
  const lang = value['lang'];
  if (!(typeof lang === 'string' || lang === null || lang === undefined)) return false;

  const titol = value['titol'];
  if (!(typeof titol === 'string' || titol === null || titol === undefined)) return false;

  const resum = value['resum'];
  if (!(typeof resum === 'string' || resum === null || resum === undefined)) return false;

  const notes = value['notes'];
  if (!(typeof notes === 'string' || notes === null || notes === undefined)) return false;

  const pdf = value['pdf_url'];
  if (!(typeof pdf === 'string' || pdf === null || pdf === undefined)) return false;

  return true;
}

function badgeEstat(estat: 'draft' | 'publicat'): string {
  return estat === 'publicat' ? `<span class="badge bg-success">Publicat</span>` : `<span class="badge bg-secondary">Esborrany</span>`;
}

function badgeDestacat(destacat: number): string {
  return destacat === 1 ? `<span class="badge bg-warning text-dark">Destacat</span>` : `<span class="badge bg-light text-dark">No</span>`;
}

function safeUrlLink(url: string | null): string {
  if (!url) return `<span class="text-muted">—</span>`;
  const u = escapeHtml(url);
  return `<a href="${u}" target="_blank" rel="noopener noreferrer">${u}</a>`;
}

function langHasContent(e: AparicioI18nEntry): boolean {
  return !!(e.titol || e.resum || e.notes || e.pdfUrl);
}

/** ---------- Fetch + mapeo ---------- */
async function fetchAparicioRows(id: number): Promise<ApiRowPremsaAparicio[]> {
  const url = `/api/auxiliars/get/premsaAparicio?id=${encodeURIComponent(String(id))}`;
  const res = await fetch(url, { method: 'GET', headers: { Accept: 'application/json' } });
  if (!res.ok) throw new Error(`HTTP ${res.status}`);

  const json: unknown = await res.json();
  if (!isApiResponse(json)) throw new Error('Resposta inesperada: objecte API invàlid.');

  const payload = json.data;
  if (!Array.isArray(payload)) throw new Error('Resposta inesperada: data no és un array.');

  const rows: ApiRowPremsaAparicio[] = [];
  for (const item of payload) {
    if (!isApiRowPremsaAparicio(item)) throw new Error('Resposta inesperada: format de fila incorrecte.');
    rows.push(item);
  }
  return rows;
}

function mapRowsToDetall(rows: ApiRowPremsaAparicio[]): AparicioDetall {
  const first = rows[0];
  if (!first) throw new Error("No s'ha trobat l'aparició.");

  const imageUrl = buildPremsaImageUrl(first.image_nomArxiu ?? null, first.image_mime ?? null);

  const base: AparicioBase = {
    id: first.id,
    dataAparicio: first.data_aparicio,
    tipusAparicio: first.tipus_aparicio,
    mitjaId: first.mitja_id,
    urlNoticia: first.url_noticia ?? null,
    imageId: first.image_id ?? null,
    destacat: typeof first.destacat === 'number' ? first.destacat : 0,
    estat: (first.estat ?? 'publicat') as 'draft' | 'publicat',
    createdAt: first.created_at ?? null,
    updatedAt: first.updated_at ?? null,

    imageNomArxiu: first.image_nomArxiu ?? null,
    imageMime: first.image_mime ?? null,
    imageUrl,
  };

  const i18n = emptyI18n();
  for (const r of rows) {
    if (typeof r.lang !== 'string') continue;
    const lang = normalizeLang(r.lang);
    if (!lang) continue;

    i18n[lang] = {
      titol: r.titol ?? null,
      resum: r.resum ?? null,
      notes: r.notes ?? null,
      pdfUrl: r.pdf_url ?? null,
    };
  }

  return { base, i18n };
}

/** ---------- Render ---------- */
function renderAparicioDetall(target: HTMLElement, detall: AparicioDetall): void {
  const { base, i18n } = detall;
  const title = i18n.ca.titol ?? `Aparició #${base.id}`;

  const previewHtml = base.imageUrl
    ? `
      <div class="ratio ratio-4x3 border rounded bg-light overflow-hidden">
        <img
          src="${escapeHtml(base.imageUrl)}"
          alt="${escapeHtml(title)}"
          class="w-100 h-100"
          style="object-fit: contain;"
          loading="lazy"
        >
      </div>
      <div class="small text-muted mt-2">
        <span><strong>Arxiu:</strong> <code>${escapeHtml(base.imageNomArxiu ?? '')}</code></span>
        <span class="mx-2">·</span>
        <span><strong>MIME:</strong> <code>${escapeHtml(base.imageMime ?? '')}</code></span>
      </div>
    `
    : `<div class="p-4 bg-light border rounded text-muted text-center">Sense imatge</div>`;

  const rowsI18n = LANGS.map((lang) => {
    const entry = i18n[lang];
    const titol = entry.titol ? escapeHtml(entry.titol) : `<span class="text-muted">—</span>`;
    const resum = entry.resum ? escapeHtml(entry.resum) : `<span class="text-muted">—</span>`;
    const notes = entry.notes ? escapeHtml(entry.notes) : `<span class="text-muted">—</span>`;
    const pdf = entry.pdfUrl ? safeUrlLink(entry.pdfUrl) : `<span class="text-muted">—</span>`;

    const dot = langHasContent(entry) ? `<span class="d-inline-block rounded-circle bg-success" style="width:10px;height:10px" title="Traducció present"></span>` : `<span class="d-inline-block rounded-circle bg-secondary" style="width:10px;height:10px" title="Sense traducció"></span>`;

    return `
      <tr>
        <td class="fw-semibold">
          <div class="d-flex align-items-center gap-2">
            ${dot}
            <code>${lang}</code>
          </div>
        </td>
        <td>${titol}</td>
        <td>${resum}</td>
        <td>${notes}</td>
        <td>${pdf}</td>
        <td class="text-end">
          <button
            type="button"
            class="btn btn-sm btn-outline-secondary"
            data-action="edit-i18n-lang"
            data-id="${base.id}"
            data-lang="${lang}"
            title="Editar ${lang}">
            ✎
          </button>
        </td>
      </tr>
    `;
  }).join('');

  target.innerHTML = `
    <div class="card shadow-sm">
      <div class="card-header d-flex justify-content-between align-items-start gap-3">
        <div>
          <h4 class="mb-1">${escapeHtml(title)}</h4>
          <div class="text-muted small">
            <span><strong>ID:</strong> <code>${escapeHtml(String(base.id))}</code></span>
            <span class="mx-2">·</span>
            <span><strong>Data:</strong> ${escapeHtml(base.dataAparicio)}</span>
            <span class="mx-2">·</span>
            <span><strong>Tipus:</strong> <code>${escapeHtml(base.tipusAparicio)}</code></span>
          </div>
        </div>

        <div class="d-flex gap-2 flex-shrink-0">
          <button type="button" class="btn btn-sm btn-outline-primary" data-action="edit-aparicio" data-id="${base.id}">
            Editar aparició
          </button>
          <button type="button" class="btn btn-sm btn-outline-secondary" data-action="edit-i18n-all" data-id="${base.id}">
            Editar traduccions
          </button>
        </div>
      </div>

      <div class="card-body">
        <div class="row g-4 mb-4">
          <div class="col-md-5">
            ${previewHtml}
          </div>

          <div class="col-md-7">
            <dl class="row mb-0">
              <dt class="col-sm-4 text-muted">Mitjà (ID)</dt>
              <dd class="col-sm-8 mb-0"><code>${escapeHtml(String(base.mitjaId))}</code></dd>

              <dt class="col-sm-4 text-muted">URL notícia</dt>
              <dd class="col-sm-8 mb-0">${safeUrlLink(base.urlNoticia)}</dd>

              <dt class="col-sm-4 text-muted">Imatge (ID)</dt>
              <dd class="col-sm-8 mb-0">${base.imageId ? `<code>${escapeHtml(String(base.imageId))}</code>` : `<span class="text-muted">—</span>`}</dd>

              <dt class="col-sm-4 text-muted">Destacat</dt>
              <dd class="col-sm-8 mb-0">${badgeDestacat(base.destacat)}</dd>

              <dt class="col-sm-4 text-muted">Estat</dt>
              <dd class="col-sm-8 mb-0">${badgeEstat(base.estat)}</dd>
            </dl>
          </div>
        </div>

        <h5 class="mb-3">Traduccions</h5>

        <div class="table-responsive">
          <table class="table table-sm table-striped align-middle">
            <thead class="table-light">
              <tr>
                <th style="width: 90px;">Lang</th>
                <th style="min-width: 220px;">Títol</th>
                <th style="min-width: 220px;">Resum</th>
                <th style="min-width: 220px;">Notes</th>
                <th style="min-width: 220px;">PDF URL</th>
                <th style="width: 70px;" class="text-end">Editar</th>
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
      const id = btn.dataset.id ?? '';
      const lang = btn.dataset.lang ?? '';

      if (action === 'edit-aparicio') {
        window.location.href = `/gestio/auxiliars/modifica-aparicio-premsa/${encodeURIComponent(id)}`;
      }
      if (action === 'edit-i18n-all') {
        window.location.href = `/gestio/auxiliars/modifica-aparicio-premsa-i18n/${encodeURIComponent(id)}`;
      }
      if (action === 'edit-i18n-lang') {
        window.location.href = `/gestio/auxiliars/modifica-aparicio-premsa-i18n/${encodeURIComponent(id)}?lang=${encodeURIComponent(lang)}`;
      }
    });
  });
}

/** ---------- Init ---------- */
export async function initFitxaDetallsAparicioMitja(id: number): Promise<void> {
  const container = document.getElementById('fitxaDetallsAparicioMitja');
  if (!container) return;

  container.innerHTML = `<div class="text-muted">Carregant…</div>`;

  try {
    const rows = await fetchAparicioRows(id);
    const detall = mapRowsToDetall(rows);
    renderAparicioDetall(container, detall);
  } catch (e: unknown) {
    const msg = e instanceof Error ? e.message : 'Error desconegut';
    container.innerHTML = `<div class="alert alert-danger mb-0">No s'ha pogut carregar l'aparició. ${escapeHtml(msg)}</div>`;
  }
}
