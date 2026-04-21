export type Lang = 'ca' | 'es' | 'en' | 'fr' | 'it' | 'pt';

interface CronologiaEvent {
  id: number;
  any: number;
  mes: number | null;
  mesFi: number | null;
  diaInici: number | null;
  diaFi: number | null;
  tema: number | null;
  area: number;
  textCa: string;
  textEs?: string;
}

interface ApiResponse {
  eventos: CronologiaEvent[];
  totalEventos: number;
  totalPaginas: number;
}

/* ---------------- I18N ---------------- */

const dict: Record<Lang, Record<string, string>> = {
  ca: {
    noResults: 'No hi ha resultats',
    search: 'Cerca',
    year: 'Any',
    period: 'Període',
    territory: 'Territori',
    theme: 'Temàtica',
    results: 'resultat(s)',
  },
  es: {
    noResults: 'Sin resultados',
    search: 'Buscar',
    year: 'Año',
    period: 'Periodo',
    territory: 'Territorio',
    theme: 'Temática',
    results: 'resultado(s)',
  },
  en: {
    noResults: 'No results',
    search: 'Search',
    year: 'Year',
    period: 'Period',
    territory: 'Territory',
    theme: 'Theme',
    results: 'result(s)',
  },
  fr: {
    noResults: 'Aucun résultat',
    search: 'Recherche',
    year: 'Année',
    period: 'Période',
    territory: 'Territoire',
    theme: 'Thème',
    results: 'résultat(s)',
  },
  it: {
    noResults: 'Nessun risultato',
    search: 'Cerca',
    year: 'Anno',
    period: 'Periodo',
    territory: 'Territorio',
    theme: 'Tema',
    results: 'risultato/i',
  },
  pt: {
    noResults: 'Sem resultados',
    search: 'Pesquisar',
    year: 'Ano',
    period: 'Período',
    territory: 'Território',
    theme: 'Tema',
    results: 'resultado(s)',
  },
};

function t(lang: Lang, key: string): string {
  return dict[lang][key] ?? key;
}

/* ---------------- MAPPINGS ---------------- */

function areaLabel(lang: Lang, area: number): string {
  const map: Record<number, Record<Lang, string>> = {
    1: { ca: 'Terrassa', es: 'Terrassa', en: 'Terrassa', fr: 'Terrassa', it: 'Terrassa', pt: 'Terrassa' },
    2: { ca: 'Catalunya', es: 'Cataluña', en: 'Catalonia', fr: 'Catalogne', it: 'Catalogna', pt: 'Catalunha' },
    3: { ca: 'Espanya', es: 'España', en: 'Spain', fr: 'Espagne', it: 'Spagna', pt: 'Espanha' },
    4: { ca: 'Europa', es: 'Europa', en: 'Europe', fr: 'Europe', it: 'Europa', pt: 'Europa' },
    5: { ca: 'Món', es: 'Mundo', en: 'World', fr: 'Monde', it: 'Mondo', pt: 'Mundo' },
  };

  return map[area]?.[lang] ?? String(area);
}

function temaLabel(lang: Lang, tema: number | null): string {
  if (!tema) return '';

  const map: Record<number, Record<Lang, string>> = {
    1: {
      ca: 'Fets econòmico-laborals',
      es: 'Hechos económico-laborales',
      en: 'Economic and labor events',
      fr: 'Faits économiques',
      it: 'Fatti economici',
      pt: 'Factos económicos',
    },
    2: {
      ca: 'Fets polítics i socials',
      es: 'Hechos políticos y sociales',
      en: 'Political and social events',
      fr: 'Faits politiques',
      it: 'Fatti politici',
      pt: 'Factos políticos',
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

  return map[tema]?.[lang] ?? `T${tema}`;
}

/* ---------------- BADGE (igual Estudis) ---------------- */

function badge(text: string): string {
  return `
    <span class="raleway" style="
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

/* ---------------- FETCH ---------------- */

async function fetchCronologia(lang: Lang, params: URLSearchParams): Promise<ApiResponse> {
  const res = await fetch(`/api/cronologia/get/?${params.toString()}&lang=${lang}`);
  if (!res.ok) throw new Error(`HTTP ${res.status}`);
  return res.json();
}

/* ---------------- INIT ---------------- */

export function initCronologia(lang: Lang): void {
  const container = document.getElementById('cronologia');
  if (!container) return;

  container.innerHTML = `
    <div class="p-4 mb-3" style="background:#EEEAD9;border-radius:6px;">
      <div class="row g-3">

        <div class="col-md-3">
          <label class="fw-bold raleway">${t(lang, 'year')}</label>
          <select id="fAny" class="form-select"></select>
        </div>

        <div class="col-md-3">
          <label class="fw-bold raleway">${t(lang, 'period')}</label>
          <select id="fPeriod" class="form-select"></select>
        </div>

        <div class="col-md-3">
          <label class="fw-bold raleway">${t(lang, 'territory')}</label>
          <select id="fArea" class="form-select"></select>
        </div>

        <div class="col-md-3">
          <label class="fw-bold raleway">${t(lang, 'theme')}</label>
          <select id="fTema" class="form-select"></select>
        </div>

      </div>
    </div>

    <div id="statusCronologia" class="text-muted raleway"></div>
    <div id="listCronologia"></div>
  `;

  const list = document.getElementById('listCronologia')!;
  const status = document.getElementById('statusCronologia')!;
  const fAny = document.getElementById('fAny') as HTMLSelectElement | null;
  const fPeriod = document.getElementById('fPeriod') as HTMLSelectElement | null;
  const fArea = document.getElementById('fArea') as HTMLSelectElement;
  const fTema = document.getElementById('fTema') as HTMLSelectElement;

  void fAny;
  void fPeriod;

  let area = 'tots';
  let tema = 'tots';
  let any = 'tots';

  function buildParams(): URLSearchParams {
    return new URLSearchParams({
      area,
      tema,
      any,
    });
  }

  function render(data: ApiResponse): void {
    list.innerHTML = '';

    if (!data.eventos.length) {
      status.textContent = t(lang, 'noResults');
      return;
    }

    let html = '';
    let lastYear: number | undefined;

    for (const ev of data.eventos) {
      if (lastYear !== ev.any) {
        html += `<h2 class="mt-3 lora">${ev.any}</h2>`;
        lastYear = ev.any;
      }

      const fecha = ev.diaInici && ev.mes ? `${ev.diaInici}/${ev.mes}/${ev.any}` : `${ev.any}`;

      html += `
        <div class="p-3 mb-2" style="background:#fff;border-left:5px solid #c2af96;border-radius:6px;">
          
          <div class="text-muted raleway mb-1">
            ${fecha}
          </div>

          <div class="mb-2">
            ${badge(areaLabel(lang, ev.area))}
            ${ev.tema ? badge(temaLabel(lang, ev.tema)) : ''}
          </div>

          <div class="raleway">
            ${ev.textCa}
          </div>

        </div>
      `;
    }

    list.innerHTML = html;
    status.textContent = `${data.totalEventos} ${t(lang, 'results')}`;
  }

  async function load(): Promise<void> {
    const data = await fetchCronologia(lang, buildParams());

    // FIX: rellenar selects correctamente SIN romper existentes
    const areas = Array.from(new Set(data.eventos.map((e) => String(e.area))));
    const temes = Array.from(new Set(data.eventos.map((e) => String(e.tema)).filter(Boolean)));

    fillSelect(fArea, areas);
    fillSelect(fTema, temes);

    render(data);
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

  document.addEventListener('change', (e) => {
    const el = e.target as HTMLSelectElement;

    if (el.id === 'fAny') any = el.value || 'tots';
    if (el.id === 'fArea') area = el.value || 'tots';
    if (el.id === 'fTema') tema = el.value || 'tots';

    load();
  });

  load();
}
