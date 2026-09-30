// publicAgendaActivitat.ts

import { ENV } from '../../../config/env';
import { formatDatesForm } from '../../../services/formatDates/dates';

type Lang = 'ca' | 'es' | 'en' | 'fr' | 'it' | 'pt';

type ApiResponse<T> = {
  status: string;
  message: string;
  data: T | T[] | null;
};

interface ActeAgenda {
  id: string;
  titol: string | null;
  slug: string | null;
  descripcio: string | null;
  data: string | null; // YYYY-MM-DD HH:MM:SS
  lloc: string | null;
  adreca: string | null;
  imatge: number | string | null;
  actiu: number | string | null;
  nomArxiu: string | null;
  mime?: string | null;
}

const AGENDA_IMG_FOLDER = 'assets_agenda'; // ⚠️ ajusta a tu carpeta real
const AGENDA_LIST_PATH = '/espai-virtual/agenda-activitats'; // ⚠️ ruta real del listado

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

function formatDatePublic(dateYmd: string | null): string {
  if (!dateYmd) return '—';
  return formatDatesForm(dateYmd.slice(0, 10));
}

function timeFromDate(dateYmd: string | null): string {
  if (!dateYmd || dateYmd.length < 16) return '';
  const hhmm = dateYmd.slice(11, 16);
  return hhmm === '00:00' ? '' : hhmm;
}

// Texto plano -> párrafos (escapando HTML)
function textToParagraphs(text: string | null): string {
  if (!text) return '';
  return text
    .split(/\n{2,}/)
    .map((p) => p.trim())
    .filter(Boolean)
    .map((p) => `<p class="raleway">${escapeHtml(p).replaceAll('\n', '<br>')}</p>`)
    .join('');
}

class NotFoundError extends Error {}

async function fetchActe(slug: string): Promise<ActeAgenda> {
  const url = `${ENV.apiBaseUrl}/agenda/get/activitatId?slug=${encodeURIComponent(slug)}`;
  const res = await fetch(url, { headers: { Accept: 'application/json' } });
  if (res.status === 404) throw new NotFoundError();
  if (!res.ok) throw new Error(`HTTP ${res.status}`);

  const json: unknown = await res.json();
  const parsed = json as ApiResponse<ActeAgenda>;
  const data = parsed?.data;
  const acte = Array.isArray(data) ? data[0] : data;
  if (!acte) throw new NotFoundError();
  return acte;
}

function renderActe(item: ActeAgenda, backHref: string): string {
  const imgUrl = buildImgUrlAgenda(item.nomArxiu);
  const titol = item.titol ? escapeHtml(item.titol) : '—';
  const data = formatDatePublic(item.data);
  const hora = timeFromDate(item.data);
  const lloc = item.lloc ? escapeHtml(item.lloc) : '';
  const adreca = item.adreca ? escapeHtml(item.adreca) : '';
  const descripcio = textToParagraphs(item.descripcio);

  const mapsQuery = [item.adreca, item.lloc].filter(Boolean).join(', ');
  const mapsHref = mapsQuery
    ? `https://www.google.com/maps/search/?api=1&query=${encodeURIComponent(mapsQuery)}`
    : '';

  const imgHtml = imgUrl
    ? `<img src="${escapeHtml(imgUrl)}" class="img-fluid" alt="${titol}">`
    : '';

  const llocHtml =
    lloc || adreca
      ? `<div class="text-muted raleway">
           ${lloc ? `<span class="negreta">${lloc}</span>` : ''}
           ${lloc && adreca ? '<span class="mx-2">·</span>' : ''}
           ${adreca ? `<span>${adreca}</span>` : ''}
         </div>`
      : '';

  const mapsBtn = mapsHref
    ? `<a class="btn btn-primary btn-custom-2 w-auto" href="${escapeHtml(mapsHref)}" target="_blank" rel="noopener noreferrer">
         com arribar-hi
       </a>`
    : '';

  return `
    <article class="p-4" style="background-color:#ffffff;border-radius:6px;">
      <div class="row g-4 align-items-start">
        ${imgHtml ? `<div class="col-12 col-md-5">${imgHtml}</div>` : ''}

        <div class="col-12 ${imgHtml ? 'col-md-7' : ''} d-flex flex-column gap-3">
          <h1 class="titol mitja lora negreta" style="line-height:1.15;">${titol}</h1>

          <div class="text-muted raleway">
            <span class="negreta">${escapeHtml(data)}</span>
            ${hora ? `<span class="mx-2">·</span><span>${escapeHtml(hora)} h</span>` : ''}
          </div>

          ${llocHtml}

          ${descripcio ? `<div>${descripcio}</div>` : ''}

          <div class="d-flex flex-wrap gap-2 mt-2">
            ${mapsBtn}
            <a class="btn btn-primary btn-custom-2 w-auto" href="${escapeHtml(backHref)}">
              tornar a l'agenda
            </a>
          </div>
        </div>
      </div>
    </article>
  `;
}

export async function initPublicAgendaActivitat(slug: string, lang: Lang): Promise<void> {
  const container = document.getElementById('agendaActivitat') as HTMLDivElement | null;
  if (!container) return;

  const prefix = lang === 'ca' ? '' : `/${lang}`;
  const backHref = `${prefix}${AGENDA_LIST_PATH}`;

  if (!slug) {
    container.innerHTML = `<div class="text-muted raleway">Activitat no trobada.</div>`;
    return;
  }

  container.innerHTML = `<div class="text-muted raleway">Carregant…</div>`;

  try {
    const acte = await fetchActe(slug);

    if (Number(acte.actiu ?? 1) !== 1) throw new NotFoundError();

    if (acte.titol) document.title = acte.titol;
    container.innerHTML = renderActe(acte, backHref);
  } catch (e) {
    if (e instanceof NotFoundError) {
      container.innerHTML = `
        <div class="text-muted raleway">Activitat no trobada.</div>
        <a class="btn btn-primary btn-custom-2 w-auto mt-3" href="${escapeHtml(backHref)}">tornar a l'agenda</a>
      `;
      return;
    }
    container.innerHTML = `<div class="text-muted raleway">No s'ha pogut carregar l'activitat.</div>`;
    console.log(e);
  }
}
