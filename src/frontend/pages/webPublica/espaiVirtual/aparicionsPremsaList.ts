// publicAparicionsPremsaList.ts

import { formatDatesForm } from '../../../services/formatDates/dates';

type Lang = 'ca' | 'es' | 'en' | 'fr' | 'it' | 'pt';

type ApiResponseArr<T> = {
  status: string;
  message: string;
  data: T[];
};

interface AparicioPublica {
  id: number;
  data_aparicio: string | null; // YYYY-MM-DD
  tipus_aparicio: string | null;

  nomMitja: string | null;
  url_noticia: string | null;

  titol: string | null;

  nomArxiu: string | null;
  mime: string | null;

  destacat?: number | null;
}

function escapeHtml(input: string): string {
  return input.replaceAll('&', '&amp;').replaceAll('<', '&lt;').replaceAll('>', '&gt;').replaceAll('"', '&quot;').replaceAll("'", '&#039;');
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

function buildImgUrlPremsa(nomArxiu: string | null, mime: string | null): string | null {
  if (!nomArxiu || !mime) return null;
  const ext = mimeToExt(mime);
  if (!ext) return null;
  return `https://media.memoriaterrassa.cat/assets_premsa/${encodeURIComponent(nomArxiu)}.${ext}`;
}

function yearFromYmd(dateYmd: string | null): string {
  if (!dateYmd || dateYmd.length < 4) return '';
  return dateYmd.slice(0, 4);
}

function formatDatePublic(dateYmd: string | null): string {
  if (!dateYmd) return '—';
  // Tu helper ya devuelve dd/mm/yyyy (lo estabas usando en intranet)
  return formatDatesForm(dateYmd);
}

function tipusAparicioHuman(tipus: string | null): string {
  switch (tipus) {
    case 'presentacio':
      return 'Presentació';
    case 'roda_premsa':
      return 'Roda de premsa';
    case 'activitat':
      return 'Activitat';
    case 'entrevista':
      return 'Entrevista';
    case 'reportatge':
      return 'Reportatge';
    case 'nota_premsa':
      return 'Nota de premsa';
    case 'publicacio_xarxes':
      return 'Publicació xarxes';
    case 'altres':
      return 'Altres';
    default:
      return '—';
  }
}

async function fetchAparicions(lang: Lang): Promise<AparicioPublica[]> {
  const url = `/api/auxiliars/get/premsaAparicionsPublic?lang=${encodeURIComponent(lang)}`;
  const res = await fetch(url, { headers: { Accept: 'application/json' } });
  if (!res.ok) throw new Error(`HTTP ${res.status}`);

  const json: unknown = await res.json();
  const parsed = json as ApiResponseArr<AparicioPublica>;
  if (!parsed || !Array.isArray(parsed.data)) return [];
  return parsed.data;
}

function renderFilters(): string {
  return `
    <div class="p-4" style="background-color:#EEEAD9;border-radius:6px;">
      <div class="row g-3 align-items-end">
        <div class="col-md-5">
          <label for="fText" class="form-label fw-bold raleway">Cerca</label>
          <input type="search" class="form-control" id="fText" placeholder="Títol o mitjà…">
        </div>

        <div class="col-md-3">
          <label for="fAny" class="form-label fw-bold raleway">Any</label>
          <select class="form-select" id="fAny">
            <option value="">Tots</option>
          </select>
        </div>

        <div class="col-md-4">
          <label for="fMitja" class="form-label fw-bold raleway">Mitjà</label>
          <select class="form-select" id="fMitja">
            <option value="">Tots</option>
          </select>
        </div>
      </div>
    </div>
  `;
}

function renderCard(item: AparicioPublica, detailHref: string): string {
  const imgUrl = buildImgUrlPremsa(item.nomArxiu, item.mime);
  const titol = item.titol ? escapeHtml(item.titol) : '—';
  const mitja = item.nomMitja ? escapeHtml(item.nomMitja) : '—';
  const data = formatDatePublic(item.data_aparicio);
  const tipus = tipusAparicioHuman(item.tipus_aparicio);

  const destacatBadge = (item.destacat ?? 0) === 1 ? `<span class="badge rounded-pill" style="background-color:#B39B7C;">Destacat</span>` : '';

  // ✅ Link en imagen + título
  const imgHtml = imgUrl
    ? `
      <a href="${escapeHtml(detailHref)}" class="d-block">
        <img src="${escapeHtml(imgUrl)}" class="img-fluid" alt="${titol}">
      </a>
    `
    : `
      <a href="${escapeHtml(detailHref)}" class="d-block text-decoration-none">
        <div class="text-muted raleway" style="background:#EEEAD9;border-radius:6px;min-height:180px;display:flex;align-items:center;justify-content:center;">
          Sense imatge
        </div>
      </a>
    `;

  return `
    <div class="col-12">
      <article class="p-4" style="background-color:#ffffff;border-radius:6px;">
        <div class="row g-4 align-items-start">
          <div class="col-12 col-md-4">
            ${imgHtml}
          </div>

          <div class="col-12 col-md-8 d-flex flex-column gap-2">
            <div class="d-flex flex-wrap gap-2 align-items-center">
              ${destacatBadge}
            </div>

            <a href="${escapeHtml(detailHref)}" class="text-decoration-none">
              <span class="titol mitja lora negreta" style="line-height:1.15;display:block;">
                ${titol}
              </span>
            </a>

            <div class="text-muted raleway">
              <span>${escapeHtml(String(data))}</span>
              <span class="mx-2">·</span>
              <span class="negreta">${mitja}</span>
              <span class="mx-2">·</span>
              <span>${escapeHtml(tipus)}</span>
            </div>

            <div class="d-flex flex-wrap gap-2 mt-2">
              <a class="btn btn-primary btn-custom-2 w-auto" href="${escapeHtml(detailHref)}">
                veure detalls
              </a>
            </div>
          </div>
        </div>
      </article>
    </div>
  `;
}

function fillSelect(select: HTMLSelectElement, values: string[]): void {
  const existing = new Set(Array.from(select.options).map((o) => o.value));
  for (const v of values) {
    if (!v || existing.has(v)) continue;
    const opt = document.createElement('option');
    opt.value = v;
    opt.textContent = v;
    select.appendChild(opt);
  }
}

export async function initPublicAparicionsPremsaList(lang: Lang): Promise<void> {
  const container = document.getElementById('publicAparicionsPremsa') as HTMLDivElement | null;
  if (!container) return;

  container.innerHTML = `
    ${renderFilters()}
    <div id="statusAparicions" class="text-muted raleway mt-3">Carregant…</div>
    <div id="listAparicions" class="row g-4 mt-1"></div>
  `;

  const status = document.getElementById('statusAparicions') as HTMLDivElement | null;
  const list = document.getElementById('listAparicions') as HTMLDivElement | null;
  const fText = document.getElementById('fText') as HTMLInputElement | null;
  const fAny = document.getElementById('fAny') as HTMLSelectElement | null;
  const fMitja = document.getElementById('fMitja') as HTMLSelectElement | null;

  if (!status || !list || !fText || !fAny || !fMitja) return;

  let all: AparicioPublica[] = [];
  try {
    all = await fetchAparicions(lang);
  } catch (e) {
    status.textContent = "No s'han pogut carregar les aparicions.";
    list.innerHTML = '';
    console.log(e);
    return;
  }

  if (!all.length) {
    status.textContent = 'No hi ha aparicions disponibles.';
    list.innerHTML = '';
    return;
  }

  const anys = Array.from(new Set(all.map((x) => yearFromYmd(x.data_aparicio)).filter(Boolean)))
    .sort()
    .reverse();
  fillSelect(fAny, anys);

  const mitjans = Array.from(new Set(all.map((x) => (x.nomMitja ?? '').trim()).filter(Boolean))).sort((a, b) => a.localeCompare(b));
  fillSelect(fMitja, mitjans);

  const prefix = lang === 'ca' ? '' : `/${lang}`;

  const apply = () => {
    const q = fText.value.trim().toLowerCase();
    const any = fAny.value;
    const mitja = fMitja.value;

    const filtered = all.filter((x) => {
      const okQ = !q || (x.titol ?? '').toLowerCase().includes(q) || (x.nomMitja ?? '').toLowerCase().includes(q);
      const okAny = !any || yearFromYmd(x.data_aparicio) === any;
      const okMitja = !mitja || (x.nomMitja ?? '') === mitja;
      return okQ && okAny && okMitja;
    });

    status.textContent = `${filtered.length} resultat(s)`;

    list.innerHTML = filtered
      .map((item) => {
        const detailHref = `${prefix}/espai-virtual/premsa-aparicio/${encodeURIComponent(String(item.id))}`;
        return renderCard(item, detailHref);
      })
      .join('');
  };

  fText.addEventListener('input', apply);
  fAny.addEventListener('change', apply);
  fMitja.addEventListener('change', apply);

  apply();
}
