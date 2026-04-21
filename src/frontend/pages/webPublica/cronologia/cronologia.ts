import { initCronologiaSelects } from './selectsCronologia';

export type Lang = 'ca' | 'es' | 'en' | 'fr' | 'it' | 'pt';

type PeriodKey = 'tots' | 'restauracio' | 'republica' | 'dictadura';

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
}

/* =========================
   STATE
========================= */

const state = {
  any: 'tots',
  period: 'tots' as PeriodKey,
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
   BADGE (ESTUDIS STYLE EXACTO)
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
  return res.json();
}

/* =========================
   INIT
========================= */

export function initCronologia(lang: Lang): void {
  const container = document.getElementById('cronologia');
  if (!container) return;

  container.innerHTML = `
    <div class="p-4 mb-3 rounded-3" style="background-color:#EEEAD9;">
      <div class="row g-3">

      <div class="col-md-3">
          <label class="form-label fw-bold raleway">Període</label>
          <select id="fPeriod" class="form-select shadow-sm"></select>
        </div>

        <div class="col-md-3">
          <label class="form-label fw-bold raleway">Any</label>
          <select id="fAny" class="form-select shadow-sm"></select>
        </div>

        <div class="col-md-3">
          <label class="form-label fw-bold raleway">Territori</label>
          <select id="fArea" class="form-select shadow-sm"></select>
        </div>

        <div class="col-md-3">
          <label class="form-label fw-bold raleway">Temàtica</label>
          <select id="fTema" class="form-select shadow-sm"></select>
        </div>

      </div>
    </div>

    <div id="statusCronologia" class="text-muted raleway mb-2"></div>
    <div id="listCronologia"></div>
  `;

  // 🔥 1. PRIMERO crear selects
  initCronologiaSelects(lang);

  // 🔥 2. luego eventos
  setTimeout(() => {
    bindEvents(lang);
    load(lang);
  }, 0);
}

/* =========================
   EVENTS
========================= */

function bindEvents(lang: Lang): void {
  const tryBind = () => {
    const fAny = document.getElementById('fAny') as HTMLSelectElement | null;
    const fPeriod = document.getElementById('fPeriod') as HTMLSelectElement | null;
    const fArea = document.getElementById('fArea') as HTMLSelectElement | null;
    const fTema = document.getElementById('fTema') as HTMLSelectElement | null;

    if (!fAny || !fPeriod || !fArea || !fTema) return false;

    fAny.addEventListener('change', () => {
      state.any = fAny.value || 'tots';
      load(lang); // 🔥 ESTO ES LO QUE TE FALTABA
    });

    fArea.addEventListener('change', () => {
      state.area = fArea.value || 'tots';
      load(lang);
    });

    fTema.addEventListener('change', () => {
      state.tema = fTema.value || 'tots';
      load(lang); // 🔥
    });

    fPeriod.addEventListener('change', () => {
      state.period = (fPeriod.value || 'tots') as PeriodKey;

      // 🔥 REGENERAR AÑOS SEGÚN PERIODO
      const event = new Event('change');
      document.getElementById('fAny')?.dispatchEvent(event);
    });

    return true;
  };

  if (tryBind()) return;

  const interval = setInterval(() => {
    if (tryBind()) clearInterval(interval);
  }, 50);
}

/* =========================
   LOAD
========================= */

async function load(lang: Lang): Promise<void> {
  const list = document.getElementById('listCronologia') as HTMLDivElement;
  const status = document.getElementById('statusCronologia') as HTMLDivElement;

  const data = await fetchCronologia(lang);

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
