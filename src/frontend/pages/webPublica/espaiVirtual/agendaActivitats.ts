// publicAgendaList.ts

import { ENV } from '../../../config/env';
import { formatDatesForm } from '../../../services/formatDates/dates';

type Lang = 'ca' | 'es' | 'en' | 'fr' | 'it' | 'pt';

type ApiResponseArr<T> = {
  status: string;
  message: string;
  data: T[];
};

interface ActeAgenda {
  id: string;
  titol: string | null;
  slug: string | null;
  descripcio: string | null;
  data: string | null; // YYYY-MM-DD o YYYY-MM-DD HH:MM:SS
  lloc: string | null;
  adreca: string | null;
  imatge: number | string | null;
  actiu: number | string | null;
  nomArxiu: string | null;

  // Pendiente de añadir en el SELECT (ver notas)
  mime?: string | null;
  tipus_activitat?: string | null;
}

const AGENDA_IMG_FOLDER = 'assets_agenda'; // ⚠️ ajusta a tu carpeta real

function escapeHtml(input: string): string {
  return input
    .replaceAll('&', '&amp;')
    .replaceAll('<', '&lt;')
    .replaceAll('>', '&gt;')
    .replaceAll('"', '&quot;')
    .replaceAll("'", '&#039;');
}

function buildImgUrlAgenda(nomArxiu: string | null): string | null {
  if (!nomArxiu) return null;
  const ext = 'jpg';
  const file = ext ? `${nomArxiu}.${ext}` : nomArxiu;
  return `${ENV.domainImg}/${AGENDA_IMG_FOLDER}/${encodeURIComponent(file)}`;
}

function yearFromYmd(dateYmd: string | null): string {
  if (!dateYmd || dateYmd.length < 4) return '';
  return dateYmd.slice(0, 4);
}

function formatDatePublic(dateYmd: string | null): string {
  if (!dateYmd) return '—';
  return formatDatesForm(dateYmd.slice(0, 10));
}

function timeFromDate(dateYmd: string | null): string {
  if (!dateYmd || dateYmd.length < 16) return '';
  const hhmm = dateYmd.slice(11, 16);
  return hhmm === '00:00' ? '' : hhmm;
}

function isUpcoming(dateYmd: string | null): boolean {
  if (!dateYmd) return false;
  const d = new Date(dateYmd.replace(' ', 'T'));
  if (Number.isNaN(d.getTime())) return false;
  const today = new Date();
  today.setHours(0, 0, 0, 0);
  return d.getTime() >= today.getTime();
}

// La descripción puede venir con HTML: la pasamos a texto plano y la recortamos
function excerpt(html: string | null, max = 180): string {
  if (!html) return '';
  const tmp = document.createElement('div');
  tmp.innerHTML = html;
  const text = (tmp.textContent ?? '').replace(/\s+/g, ' ').trim();
  return text.length > max ? `${text.slice(0, max).trimEnd()}…` : text;
}

async function fetchActes(): Promise<ActeAgenda[]> {
  const url = `${ENV.apiBaseUrl}/agenda/get/llistatActivitats`;
  const res = await fetch(url, { headers: { Accept: 'application/json' } });
  if (!res.ok) throw new Error(`HTTP ${res.status}`);

  const json: unknown = await res.json();
  const parsed = json as ApiResponseArr<ActeAgenda>;
  if (!parsed || !Array.isArray(parsed.data)) return [];
  return parsed.data;
}

function renderFilters(showTipus: boolean): string {
  const tipusCol = showTipus
    ? `
        <div class="col-md-3">
          <label for="fTipus" class="form-label fw-bold raleway">Tipus d'activitat</label>
          <select class="form-select" id="fTipus">
            <option value="">Tots</option>
          </select>
        </div>`
    : '';

  return `
    <div class="p-4" style="background-color:#EEEAD9;border-radius:6px;">
      <div class="row g-3 align-items-end">
        <div class="col-md-${showTipus ? '3' : '5'}">
          <label for="fText" class="form-label fw-bold raleway">Cerca</label>
          <input type="search" class="form-control" id="fText" placeholder="Títol o lloc…">
        </div>

        <div class="col-md-2">
          <label for="fAny" class="form-label fw-bold raleway">Any</label>
          <select class="form-select" id="fAny">
            <option value="">Tots</option>
          </select>
        </div>

        <div class="col-md-2">
          <label for="fQuan" class="form-label fw-bold raleway">Quan</label>
          <select class="form-select" id="fQuan">
            <option value="">Tots</option>
            <option value="proxims">Propers</option>
            <option value="passats">Passats</option>
          </select>
        </div>
        ${tipusCol}
      </div>
    </div>
  `;
}

function renderCard(item: ActeAgenda, detailHref: string): string {
  const imgUrl = buildImgUrlAgenda(item.nomArxiu);
  const titol = item.titol ? escapeHtml(item.titol) : '—';
  const data = formatDatePublic(item.data);
  const hora = timeFromDate(item.data);
  const lloc = item.lloc ? escapeHtml(item.lloc) : '';
  const adreca = item.adreca ? escapeHtml(item.adreca) : '';
  const resum = escapeHtml(excerpt(item.descripcio));
  const tipus = item.tipus_activitat ? escapeHtml(item.tipus_activitat) : '';

  const proximBadge = isUpcoming(item.data)
    ? `<span class="badge rounded-pill" style="background-color:#B39B7C;">Proper</span>`
    : '';

  const tipusBadge = tipus
    ? `<span class="badge rounded-pill text-bg-light border">${tipus}</span>`
    : '';

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

  const llocHtml =
    lloc || adreca
      ? `<div class="text-muted raleway">
           ${lloc ? `<span class="negreta">${lloc}</span>` : ''}
           ${lloc && adreca ? '<span class="mx-2">·</span>' : ''}
           ${adreca ? `<span>${adreca}</span>` : ''}
         </div>`
      : '';

  return `
    <div class="col-12">
      <article class="p-4" style="background-color:#ffffff;border-radius:6px;">
        <div class="row g-4 align-items-start">
          <div class="col-12 col-md-4">
            ${imgHtml}
          </div>

          <div class="col-12 col-md-8 d-flex flex-column gap-2">
            <div class="d-flex flex-wrap gap-2 align-items-center">
              ${proximBadge}
              ${tipusBadge}
            </div>

            <a href="${escapeHtml(detailHref)}" class="text-decoration-none">
              <span class="titol mitja lora negreta" style="line-height:1.15;display:block;">
                ${titol}
              </span>
            </a>

            <div class="text-muted raleway">
              <span class="negreta">${escapeHtml(data)}</span>
              ${hora ? `<span class="mx-2">·</span><span>${escapeHtml(hora)} h</span>` : ''}
            </div>

            ${llocHtml}

            ${resum ? `<p class="raleway mb-0">${resum}</p>` : ''}

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

export async function initPublicAgendaList(lang: Lang): Promise<void> {
  const container = document.getElementById('agenda') as HTMLDivElement | null;
  if (!container) return;

  // Estado de carga mientras llegan los datos
  container.innerHTML = `<div class="text-muted raleway">Carregant…</div>`;

  let all: ActeAgenda[] = [];
  try {
    const raw = await fetchActes();
    all = raw.filter((x) => Number(x.actiu ?? 1) === 1); // solo actos activos
  } catch (e) {
    container.innerHTML = `<div class="text-muted raleway">No s'ha pogut carregar l'agenda.</div>`;
    console.log(e);
    return;
  }

  if (!all.length) {
    container.innerHTML = `<div class="text-muted raleway">No hi ha activitats programades.</div>`;
    return;
  }

  const tipusList = Array.from(
    new Set(all.map((x) => (x.tipus_activitat ?? '').trim()).filter(Boolean))
  ).sort((a, b) => a.localeCompare(b));
  const showTipus = tipusList.length > 0;

  container.innerHTML = `
    ${renderFilters(showTipus)}
    <div id="statusAgenda" class="text-muted raleway mt-3"></div>
    <div id="listAgenda" class="row g-4 mt-1"></div>
  `;

  const status = document.getElementById('statusAgenda') as HTMLDivElement | null;
  const list = document.getElementById('listAgenda') as HTMLDivElement | null;
  const fText = document.getElementById('fText') as HTMLInputElement | null;
  const fAny = document.getElementById('fAny') as HTMLSelectElement | null;
  const fQuan = document.getElementById('fQuan') as HTMLSelectElement | null;
  const fTipus = document.getElementById('fTipus') as HTMLSelectElement | null;

  if (!status || !list || !fText || !fAny || !fQuan) return;

  const anys = Array.from(new Set(all.map((x) => yearFromYmd(x.data)).filter(Boolean)))
    .sort()
    .reverse();
  fillSelect(fAny, anys);
  if (fTipus) fillSelect(fTipus, tipusList);

  const prefix = lang === 'ca' ? '' : `/${lang}`;

  const apply = () => {
    const q = fText.value.trim().toLowerCase();
    const any = fAny.value;
    const quan = fQuan.value;
    const tipus = fTipus?.value ?? '';

    const filtered = all.filter((x) => {
      const okQ =
        !q || (x.titol ?? '').toLowerCase().includes(q) || (x.lloc ?? '').toLowerCase().includes(q);
      const okAny = !any || yearFromYmd(x.data) === any;
      const okTipus = !tipus || (x.tipus_activitat ?? '') === tipus;
      const upcoming = isUpcoming(x.data);
      const okQuan = !quan || (quan === 'proxims' ? upcoming : !upcoming);
      return okQ && okAny && okTipus && okQuan;
    });

    status.textContent = `${filtered.length} resultat(s)`;

    list.innerHTML = filtered
      .map((item) => {
        const key = item.slug ? item.slug : String(item.id);
        const detailHref = `${prefix}/espai-virtual/agenda-activitat/${encodeURIComponent(key)}`;
        return renderCard(item, detailHref);
      })
      .join('');
  };

  fText.addEventListener('input', apply);
  fAny.addEventListener('change', apply);
  fQuan.addEventListener('change', apply);
  fTipus?.addEventListener('change', apply);

  apply();
}
