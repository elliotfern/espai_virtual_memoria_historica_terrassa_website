import { initCronologiaSelects } from './selectsCronologia';

export type Lang = 'ca' | 'es' | 'en' | 'fr' | 'it' | 'pt';

interface CronologiaEvent {
  id: number;
  any: number;
  mes: string | null;
  mesOrdre: number | null;
  diaInici: number | null;
  diaFi: number | null;
  tema: number | null;
  area: number;
  textCa: string;
}

interface ApiResponse {
  eventos: CronologiaEvent[];
  totalEventos: number;
  totalPaginas: number;
}

/* =========================
   STATE CENTRAL
========================= */

type State = {
  any: string;
  period: string;
  area: string;
  tema: string;
};

const state: State = {
  any: 'tots',
  period: 'tots',
  area: 'tots',
  tema: 'tots',
};

/* =========================
   LABELS
========================= */

function areaLabel(lang: Lang, id: number): string {
  const dict: Record<number, Record<Lang, string>> = {
    1: { ca: 'Terrassa', es: 'Terrassa', en: 'Terrassa', fr: 'Terrassa', it: 'Terrassa', pt: 'Terrassa' },
    2: { ca: 'Catalunya', es: 'Cataluña', en: 'Catalonia', fr: 'Catalogne', it: 'Catalogna', pt: 'Catalunha' },
    3: { ca: 'Espanya', es: 'España', en: 'Spain', fr: 'Espagne', it: 'Spagna', pt: 'Espanha' },
    4: { ca: 'Europa', es: 'Europa', en: 'Europe', fr: 'Europe', it: 'Europa', pt: 'Europa' },
    5: { ca: 'Món', es: 'Mundo', en: 'World', fr: 'Monde', it: 'Mondo', pt: 'Mundo' },
  };
  return dict[id]?.[lang] ?? String(id);
}

function temaLabel(lang: Lang, id: number): string {
  const dict: Record<number, Record<Lang, string>> = {
    1: { ca: 'Econòmic-laboral', es: 'Económico-laboral', en: 'Economic-labor', fr: 'Économique', it: 'Economico', pt: 'Económico' },
    2: { ca: 'Polític-social', es: 'Político-social', en: 'Political-social', fr: 'Politique', it: 'Politico', pt: 'Político' },
    3: { ca: 'Moviment obrer', es: 'Movimiento obrero', en: 'Labor movement', fr: 'Mouvement ouvrier', it: 'Movimento operaio', pt: 'Movimento operário' },
  };
  return dict[id]?.[lang] ?? String(id);
}

/* =========================
   BADGE UI (igual estudis)
========================= */

function badge(text: string): string {
  return `
    <span style="
      background-color:#c2af96;
      color:#2f2a24;
      border:1px solid #b19d84;
      padding:6px 12px;
      border-radius:6px;
      display:inline-flex;
      align-items:center;
      font-size:0.92rem;
      margin-right:6px;
    ">${text}</span>
  `;
}

/* =========================
   FETCH
========================= */

async function fetchCronologia(lang: Lang): Promise<ApiResponse> {
  const params = new URLSearchParams();

  if (state.area !== 'tots') params.append('area', state.area);
  if (state.tema !== 'tots') params.append('tema', state.tema);
  if (state.any !== 'tots') params.append('any', state.any);

  params.append('pagina', '1');

  const res = await fetch(`/api/cronologia/get/?${params.toString()}&lang=${lang}`);
  if (!res.ok) throw new Error('HTTP ' + res.status);

  return res.json();
}

/* =========================
   1. INIT MAIN
========================= */

export function initCronologia(lang: Lang): void {
  const container = document.getElementById('cronologia');
  if (!container) return;

  container.innerHTML = `
    <div id="statusCronologia" class="text-muted raleway mb-2"></div>
    <div id="listCronologia"></div>
  `;

  initFilters(lang);
  bindEvents(lang);
  load(lang);
}

/* =========================
   2. INIT FILTERS
========================= */

function initFilters(lang: Lang): void {
  initCronologiaSelects(lang);
}

/* =========================
   3. BIND EVENTS
========================= */

function bindEvents(lang: Lang): void {
  const fAny = document.getElementById('fAny') as HTMLSelectElement;
  const fPeriod = document.getElementById('fPeriod') as HTMLSelectElement;
  const fArea = document.getElementById('fArea') as HTMLSelectElement;
  const fTema = document.getElementById('fTema') as HTMLSelectElement;

  fAny.addEventListener('change', () => {
    state.any = fAny.value || 'tots';
    load(lang);
  });

  fArea.addEventListener('change', () => {
    state.area = fArea.value || 'tots';
    load(lang);
  });

  fTema.addEventListener('change', () => {
    state.tema = fTema.value || 'tots';
    load(lang);
  });

  fPeriod.addEventListener('change', () => {
    state.period = fPeriod.value || 'tots';
  });
}

/* =========================
   LOAD + RENDER
========================= */

async function load(lang: Lang): Promise<void> {
  const list = document.getElementById('listCronologia') as HTMLDivElement;
  const status = document.getElementById('statusCronologia') as HTMLDivElement;

  const data = await fetchCronologia(lang);

  if (!data.eventos.length) {
    status.textContent = '0 resultat(s)';
    list.innerHTML = '';
    return;
  }

  let html = '';
  let lastYear: number | null = null;

  for (const ev of data.eventos) {
    if (ev.any !== lastYear) {
      html += `<h2 class="mt-3 mb-2">${ev.any}</h2>`;
      lastYear = ev.any;
    }

    html += `
      <div class="p-3 mb-2" style="background:#fff;border-left:5px solid #c2af96;border-radius:6px;">
        <div class="mb-2">
          ${badge(areaLabel(lang, ev.area))}
          ${ev.tema ? badge(temaLabel(lang, ev.tema)) : ''}
        </div>

        <div style="font-weight:600;margin-bottom:6px;">
          ${ev.diaInici ? ev.diaInici + ' ' : ''}${ev.mes ?? ''} ${ev.any}
        </div>

        <div>${ev.textCa}</div>
      </div>
    `;
  }

  status.textContent = `${data.totalEventos} resultat(s)`;
  list.innerHTML = html;
}
