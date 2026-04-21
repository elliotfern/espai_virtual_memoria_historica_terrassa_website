export type Lang = 'ca' | 'es' | 'en' | 'fr' | 'it' | 'pt';

type PeriodKey = 'tots' | 'restauracio' | 'republca' | 'dictadura';

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
  },
  es: {
    noResults: 'Sin resultados',
    search: 'Buscar',
    year: 'Año',
    period: 'Periodo',
    territory: 'Territorio',
    theme: 'Temática',
  },
  en: {
    noResults: 'No results',
    search: 'Search',
    year: 'Year',
    period: 'Period',
    territory: 'Territory',
    theme: 'Theme',
  },
  fr: {
    noResults: 'Aucun résultat',
    search: 'Recherche',
    year: 'Année',
    period: 'Période',
    territory: 'Territoire',
    theme: 'Thème',
  },
  it: {
    noResults: 'Nessun risultato',
    search: 'Cerca',
    year: 'Anno',
    period: 'Periodo',
    territory: 'Territorio',
    theme: 'Tema',
  },
  pt: {
    noResults: 'Sem resultados',
    search: 'Pesquisar',
    year: 'Ano',
    period: 'Período',
    territory: 'Território',
    theme: 'Tema',
  },
};

function t(lang: Lang, key: string): string {
  return dict[lang][key] ?? key;
}

/* ---------------- LABELS ---------------- */

function areaLabel(lang: Lang, area: number): string {
  const labels: Record<number, Record<Lang, string>> = {
    1: { ca: 'Terrassa', es: 'Terrassa', en: 'Terrassa', fr: 'Terrassa', it: 'Terrassa', pt: 'Terrassa' },
    2: { ca: 'Catalunya', es: 'Cataluña', en: 'Catalonia', fr: 'Catalogne', it: 'Catalogna', pt: 'Catalunha' },
    3: { ca: 'Espanya', es: 'España', en: 'Spain', fr: 'Espagne', it: 'Spagna', pt: 'Espanha' },
    4: { ca: 'Europa', es: 'Europa', en: 'Europe', fr: 'Europe', it: 'Europa', pt: 'Europa' },
    5: { ca: 'Món', es: 'Mundo', en: 'World', fr: 'Monde', it: 'Mondo', pt: 'Mundo' },
  };

  return labels[area]?.[lang] ?? '';
}

function temaLabel(lang: Lang, tema: number | null): string {
  if (!tema) return '';

  const labels: Record<number, Record<Lang, string>> = {
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
      ca: 'Esdeveniments del moviment obrer',
      es: 'Movimiento obrero',
      en: 'Labor movement events',
      fr: 'Mouvement ouvrier',
      it: 'Movimento operaio',
      pt: 'Movimento operário',
    },
  };

  return labels[tema]?.[lang] ?? '';
}

/* ---------------- HELPERS ---------------- */

function mustGetElement<T extends HTMLElement>(id: string): T {
  const el = document.getElementById(id);
  if (!el) throw new Error(`Element not found: ${id}`);
  return el as T;
}

async function fetchCronologia(lang: Lang, params: URLSearchParams): Promise<ApiResponse> {
  const url = `/api/cronologia/get/?${params.toString()}&lang=${lang}`;
  const res = await fetch(url);
  if (!res.ok) throw new Error(`HTTP ${res.status}`);
  return (await res.json()) as ApiResponse;
}

/* ---------------- BADGE ---------------- */

function badge(label: string, value: string): string {
  return `
    <span style="
      background:#c2af96;
      padding:4px 10px;
      border-radius:6px;
      margin-right:6px;
      font-size:12px;
      display:inline-block;
    ">
      <strong>${label}:</strong> ${value}
    </span>
  `;
}

/* ---------------- OPTIONS ---------------- */

function fillSelect(select: HTMLSelectElement, values: string[]): void {
  select.innerHTML = `<option value="tots">Tots</option>`;
  for (const v of values) {
    const opt = document.createElement('option');
    opt.value = v;
    opt.textContent = v;
    select.appendChild(opt);
  }
}

/* ---------------- MAIN ---------------- */

export function initCronologia(lang: Lang): void {
  const container = document.getElementById('cronologia') as HTMLDivElement | null;
  if (!container) return;

  container.innerHTML = `
    <div class="p-4 mb-3 rounded-3" style="background-color:#EEEAD9;">
      <div class="row g-3">

        <div class="col-md-3">
          <label class="form-label fw-bold raleway">${t(lang, 'territory')}</label>
          <select id="fArea" class="form-select"></select>
        </div>

        <div class="col-md-3">
          <label class="form-label fw-bold raleway">${t(lang, 'theme')}</label>
          <select id="fTema" class="form-select"></select>
        </div>

        <div class="col-md-3">
          <label class="form-label fw-bold raleway">${t(lang, 'period')}</label>
          <select id="fPeriod" class="form-select"></select>
        </div>

        <div class="col-md-3">
          <label class="form-label fw-bold raleway">${t(lang, 'year')}</label>
          <select id="fAny" class="form-select"></select>
        </div>

      </div>
    </div>

    <div id="statusCronologia" class="text-muted raleway"></div>
    <div id="listCronologia"></div>
  `;

  const list = mustGetElement<HTMLDivElement>('listCronologia');
  const status = mustGetElement<HTMLDivElement>('statusCronologia');
  const fAny = mustGetElement<HTMLSelectElement>('fAny');
  const fPeriod = mustGetElement<HTMLSelectElement>('fPeriod');
  const fArea = mustGetElement<HTMLSelectElement>('fArea');
  const fTema = mustGetElement<HTMLSelectElement>('fTema');

  let area = 'tots';
  let tema = 'tots';
  // eslint-disable-next-line @typescript-eslint/no-unused-vars
  let period: PeriodKey = 'tots';
  let any = 'tots';

  /* ---------------- LOAD ---------------- */

  function buildParams(): URLSearchParams {
    return new URLSearchParams({
      area,
      tema,
      any,
      pagina: '1',
    });
  }

  function render(data: ApiResponse): void {
    list.innerHTML = '';

    if (!data.eventos.length) {
      status.textContent = t(lang, 'noResults');
      return;
    }

    let html = '';
    let lastYear: number | null = null;

    for (const ev of data.eventos) {
      if (ev.any !== lastYear) {
        html += `<h2 class="mt-3">${ev.any}</h2>`;
        lastYear = ev.any;
      }

      const areaText = areaLabel(lang, ev.area);
      const temaText = temaLabel(lang, ev.tema);

      const badges = badge('Territori', areaText) + (temaText ? badge('Temàtica', temaText) : '');

      html += `
        <div class="p-3 mb-2" style="background:#fff;border-left:5px solid #c2af96;border-radius:6px;">
          <div class="mb-2">${badges}</div>
          <div>${ev.textCa}</div>
        </div>
      `;
    }

    status.textContent = `${data.totalEventos} resultat(s)`;
    list.innerHTML = html;
  }

  async function load(): Promise<void> {
    const data = await fetchCronologia(lang, buildParams());
    render(data);
  }

  /* ---------------- EVENTS ---------------- */

  document.addEventListener('change', (e: Event) => {
    const el = e.target as HTMLSelectElement;

    if (el.id === 'fAny') any = el.value;
    if (el.id === 'fPeriod') period = el.value as PeriodKey;
    if (el.id === 'fArea') area = el.value;
    if (el.id === 'fTema') tema = el.value;

    load();
  });

  /* ---------------- INIT SELECTS ---------------- */

  fillSelect(fArea, ['Terrassa', 'Catalunya', 'Espanya', 'Europa', 'Món']);
  fillSelect(fTema, ['Fets econòmic-laborals', 'Fets polítics i socials', 'Esdeveniments del moviment obrer']);
  fillSelect(fPeriod, ['restauracio', 'republca', 'dictadura']);
  fillSelect(fAny, ['2026', '2025', '2024']); // idealmente dinámico desde API

  load();
}
