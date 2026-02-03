// fitxaImatge.ts
interface ApiResponse<T> {
  status: string;
  message: string;
  errors: unknown[];
  data: T;
}

interface ApiRowImatge {
  id: number;
  idPersona: number | null;
  nomArxiu: string;
  nomImatge: string;
  tipus: number;
  mime: string;
  dateCreated: string | null;
  dateModified: string | null;
}

interface ImatgeDetall {
  id: number;
  idPersona: number | null;
  nomArxiu: string;
  nomImatge: string;
  tipus: number;
  mime: string;
  dateCreated: string | null;
  dateModified: string | null;
  urlPublica: string;
}

/** ---------- Utils ---------- */
function escapeHtml(input: string): string {
  return input.replaceAll('&', '&amp;').replaceAll('<', '&lt;').replaceAll('>', '&gt;').replaceAll('"', '&quot;').replaceAll("'", '&#039;');
}

function isRecord(v: unknown): v is Record<string, unknown> {
  return typeof v === 'object' && v !== null;
}

function isApiResponse(v: unknown): v is ApiResponse<unknown> {
  return isRecord(v) && typeof v.status === 'string' && typeof v.message === 'string' && Array.isArray(v.errors) && 'data' in v;
}

function isApiRowImatge(v: unknown): v is ApiRowImatge {
  if (!isRecord(v)) return false;

  if (typeof v.id !== 'number') return false;
  if (typeof v.nomArxiu !== 'string') return false;
  if (typeof v.nomImatge !== 'string') return false;
  if (typeof v.tipus !== 'number') return false;
  if (typeof v.mime !== 'string') return false;

  if (!(typeof v.idPersona === 'number' || v.idPersona === null)) return false;
  if (!(typeof v.dateCreated === 'string' || v.dateCreated === null)) return false;
  if (!(typeof v.dateModified === 'string' || v.dateModified === null)) return false;

  return true;
}

function buildImatgeUrl(tipus: number, nomArxiu: string, mime: string): string {
  const ext = mime.split('/')[1] ?? 'jpg';

  switch (tipus) {
    case 1:
      return `https://media.memoriaterrassa.cat/assets_represaliats/img/${nomArxiu}.${ext}`;

    case 2:
      return `https://media.memoriaterrassa.cat/assets_usuaris/${nomArxiu}.${ext}`;

    case 3:
      return `https://media.memoriaterrassa.cat/assets_represaliats/img/${nomArxiu}.${ext}`;

    case 4:
      return `https://media.memoriaterrassa.cat/assets_premsa/${nomArxiu}.${ext}`;

    default:
      return `https://media.memoriaterrassa.cat/assets_represaliats/${nomArxiu}.${ext}`;
  }
}

async function fetchImatge(id: number): Promise<ImatgeDetall> {
  const res = await fetch(`/api/auxiliars/get/imatge?id=${encodeURIComponent(id)}`, {
    headers: { Accept: 'application/json' },
  });

  if (!res.ok) {
    throw new Error(`HTTP ${res.status}`);
  }

  const json: unknown = await res.json();

  if (!isApiResponse(json)) {
    throw new Error('Resposta API invàlida');
  }

  if (!Array.isArray(json.data) || json.data.length === 0) {
    throw new Error('No s’ha trobat la imatge');
  }

  const row = json.data[0];
  if (!isApiRowImatge(row)) {
    throw new Error('Format de dades incorrecte');
  }

  return {
    ...row,
    urlPublica: buildImatgeUrl(row.tipus, row.nomArxiu, row.mime),
  };
}

function renderImatgeDetall(target: HTMLElement, img: ImatgeDetall): void {
  target.innerHTML = `
    <div class="card shadow-sm">
      <div class="card-header">
        <h4 class="mb-0">${escapeHtml(img.nomImatge)}</h4>
      </div>

      <div class="card-body">
        <div class="row g-4">
          <div class="col-md-6">
            <div class="ratio ratio-4x3 border rounded bg-light">
              <img
                src="${escapeHtml(img.urlPublica)}"
                alt="${escapeHtml(img.nomImatge)}"
                class="w-100 h-100"
                style="object-fit: contain;"
                loading="lazy"
              >
            </div>
          </div>

          <div class="col-md-6">
            <dl class="row mb-0">
              <dt class="col-sm-4">Nom arxiu</dt>
              <dd class="col-sm-8"><code>${escapeHtml(img.nomArxiu)}</code></dd>

              <dt class="col-sm-4">Tipus</dt>
              <dd class="col-sm-8">${img.tipus}</dd>

              <dt class="col-sm-4">MIME</dt>
              <dd class="col-sm-8">${escapeHtml(img.mime)}</dd>

              <dt class="col-sm-4">Creat</dt>
              <dd class="col-sm-8">${img.dateCreated ?? '—'}</dd>

              <dt class="col-sm-4">Modificat</dt>
              <dd class="col-sm-8">${img.dateModified ?? '—'}</dd>

              <dt class="col-sm-4">URL</dt>
              <dd class="col-sm-8">
                <a href="${escapeHtml(img.urlPublica)}" target="_blank" rel="noopener">
                  Obrir imatge
                </a>
              </dd>
            </dl>
          </div>
        </div>
      </div>
    </div>
  `;
}

export async function initFitxaDetallsImatge(id: number): Promise<void> {
  const container = document.getElementById('fitxaDetallsImatge');
  if (!container) return;

  container.innerHTML = `<div class="text-muted">Carregant…</div>`;

  try {
    const img = await fetchImatge(id);
    renderImatgeDetall(container, img);
  } catch (e: unknown) {
    const msg = e instanceof Error ? e.message : 'Error desconegut';
    container.innerHTML = `
      <div class="alert alert-danger">
        No s'ha pogut carregar la imatge. ${escapeHtml(msg)}
      </div>
    `;
  }
}
