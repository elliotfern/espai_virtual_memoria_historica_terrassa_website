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

/* ---------------- LABELS ---------------- */

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
    1: {
      ca: 'Fets econòmico-laborals',
      es: 'Hechos económico-laborales',
      en: 'Economic and labor events',
      fr: 'Faits économiques et sociaux',
      it: 'Fatti economico-lavorativi',
      pt: 'Factos económico-laborais',
    },
    2: {
      ca: 'Fets polítics i socials',
      es: 'Hechos políticos y sociales',
      en: 'Political and social events',
      fr: 'Faits politiques et sociaux',
      it: 'Fatti politici e sociali',
      pt: 'Factos políticos e sociais',
    },
    3: {
      ca: 'Moviment obrer',
      es: 'Movimiento obrero',
      en: 'Labor movement',
      fr: 'Mouvement ouvrier',
      it: 'Movimento operaio',
      pt: 'Movimento operário',
    },
  };
  return dict[id]?.[lang] ?? String(id);
}

/* ---------------- BADGE (MISMO ESTILO ESTUDIS) ---------------- */

function badge(label: string): string {
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
    ">
      ${label}
    </span>
  `;
}

/* ---------------- FORMAT FECHA ---------------- */

function formatDate(ev: CronologiaEvent): string {
  const parts = [];

  if (ev.diaInici) parts.push(String(ev.diaInici));
  if (ev.mes) parts.push(ev.mes);

  parts.push(String(ev.any));

  return parts.join(' ');
}

/* ---------------- FETCH ---------------- */

async function fetchCronologia(params: URLSearchParams, lang: Lang): Promise<ApiResponse> {
  const url = `/api/cronologia/get/?${params.toString()}&lang=${lang}`;
  const res = await fetch(url);
  if (!res.ok) throw new Error(`HTTP ${res.status}`);
  return await res.json();
}

/* ---------------- INIT ---------------- */

export function initCronologia(lang: Lang): void {
  const container = document.getElementById('cronologia');
  if (!container) return;

  container.innerHTML = `
    <div id="statusCronologia" class="text-muted raleway mb-2"></div>
    <div id="listCronologia"></div>
  `;

  const list = document.getElementById('listCronologia') as HTMLDivElement;
  const status = document.getElementById('statusCronologia') as HTMLDivElement;

  const state = {
    any: 'tots',
    period: 'tots',
    area: 'tots',
    tema: 'tots',
  };

  function buildParams(): URLSearchParams {
    const p = new URLSearchParams();

    if (state.area !== 'tots') p.append('area', state.area);
    if (state.tema !== 'tots') p.append('tema', state.tema);
    if (state.any !== 'tots') p.append('any', state.any);

    p.append('pagina', '1');
    return p;
  }

  function render(data: ApiResponse): void {
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
            ${formatDate(ev)}
          </div>

          <div>${ev.textCa}</div>
        </div>
      `;
    }

    status.textContent = `${data.totalEventos} resultat(s)`;
    list.innerHTML = html;
  }

  async function load(): Promise<void> {
    const data = await fetchCronologia(buildParams(), lang);
    render(data);
  }

  /* ---------------- 🔥 IMPORTANT FIX ---------------- */

  requestAnimationFrame(() => {
    initCronologiaSelects(lang);

    const fAny = document.getElementById('fAny') as HTMLSelectElement | null;
    const fPeriod = document.getElementById('fPeriod') as HTMLSelectElement | null;
    const fArea = document.getElementById('fArea') as HTMLSelectElement | null;
    const fTema = document.getElementById('fTema') as HTMLSelectElement | null;

    if (!fAny || !fPeriod || !fArea || !fTema) return;

    fAny.addEventListener('change', () => {
      state.any = fAny.value || 'tots';
      load();
    });

    fArea.addEventListener('change', () => {
      state.area = fArea.value || 'tots';
      load();
    });

    fTema.addEventListener('change', () => {
      state.tema = fTema.value || 'tots';
      load();
    });

    fPeriod.addEventListener('change', () => {
      state.period = fPeriod.value || 'tots';
    });

    load();
  });
}
