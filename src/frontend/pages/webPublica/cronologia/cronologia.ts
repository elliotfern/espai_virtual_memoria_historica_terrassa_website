export type Lang = 'ca' | 'es' | 'en' | 'fr' | 'it' | 'pt';

type PeriodKey = 'tots' | 'restauracio' | 'republca' | 'dictadura';

interface CronologiaEvent {
  id: number;
  any: number;
  mes: string | null;
  mesOrdre: number | null;
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
    period: 'Període històric',
    territory: 'Territori',
    theme: 'Temàtica',
  },
  es: {
    noResults: 'Sin resultados',
    search: 'Buscar',
    year: 'Año',
    period: 'Periodo histórico',
    territory: 'Territorio',
    theme: 'Temática',
  },
  en: {
    noResults: 'No results',
    search: 'Search',
    year: 'Year',
    period: 'Historical period',
    territory: 'Territory',
    theme: 'Theme',
  },
  fr: {
    noResults: 'Aucun résultat',
    search: 'Recherche',
    year: 'Année',
    period: 'Période historique',
    territory: 'Territoire',
    theme: 'Thème',
  },
  it: {
    noResults: 'Nessun risultato',
    search: 'Cerca',
    year: 'Anno',
    period: 'Periodo storico',
    territory: 'Territorio',
    theme: 'Tema',
  },
  pt: {
    noResults: 'Sem resultados',
    search: 'Pesquisar',
    year: 'Ano',
    period: 'Período histórico',
    territory: 'Território',
    theme: 'Tema',
  },
};

function t(lang: Lang, key: keyof (typeof dict)['ca']): string {
  return dict[lang][key] ?? key;
}

/* ---------------- AREA LABELS ---------------- */

function areaLabel(lang: Lang, area: number): string {
  const labels: Record<number, Record<Lang, string>> = {
    1: { ca: 'Terrassa', es: 'Terrassa', en: 'Terrassa', fr: 'Terrassa', it: 'Terrassa', pt: 'Terrassa' },
    2: { ca: 'Catalunya', es: 'Cataluña', en: 'Catalonia', fr: 'Catalogne', it: 'Catalogna', pt: 'Catalunha' },
    3: { ca: 'Espanya', es: 'España', en: 'Spain', fr: 'Espagne', it: 'Spagna', pt: 'Espanha' },
    4: { ca: 'Europa', es: 'Europa', en: 'Europe', fr: 'Europe', it: 'Europa', pt: 'Europa' },
    5: { ca: 'Món', es: 'Mundo', en: 'World', fr: 'Monde', it: 'Mondo', pt: 'Mundo' },
  };

  return labels[area]?.[lang] ?? String(area);
}

/* ---------------- PERIODS ---------------- */

const PERIODS: Record<PeriodKey, { start: number; end: number }> = {
  tots: { start: 0, end: 9999 },
  restauracio: { start: 1910, end: 1930 },
  republca: { start: 1931, end: 1939 },
  dictadura: { start: 1939, end: 1979 },
};

/* ---------------- API ---------------- */

async function fetchCronologia(lang: Lang, params: URLSearchParams): Promise<ApiResponse> {
  const url = `/api/cronologia/get/?${params.toString()}`;

  const res = await fetch(url);
  if (!res.ok) throw new Error(`HTTP ${res.status}`);

  return (await res.json()) as ApiResponse;
}

/* ---------------- BADGE ---------------- */

function badge(text: string): string {
  return `<span style="background:#c2af96;padding:4px 10px;border-radius:6px;margin-right:6px;font-size:12px;display:inline-block">${text}</span>`;
}

/* ---------------- UI HELPERS ---------------- */

function el(id: string): HTMLElement | null {
  return document.getElementById(id);
}

/* ---------------- MAIN ---------------- */

export function initCronologia(lang: Lang): void {
  const container = el('cronologia') as HTMLDivElement | null;
  if (!container) return;

  /* ---------------- STATE ---------------- */

  let area: string = 'tots';
  let tema: string = 'tots';
  let period: PeriodKey = 'tots';
  let any: string = 'tots';
  let page = 1;

  /* ---------------- RENDER FILTERS ---------------- */

  container.innerHTML = `
    <div style="margin-bottom:20px">
      <select id="fArea"></select>
      <select id="fTema"></select>
      <select id="fPeriod"></select>
      <select id="fAny"></select>
    </div>

    <div id="cronologiaList"></div>
  `;

  const list = el('cronologiaList') as HTMLDivElement;
  const fArea = el('fArea') as HTMLSelectElement;
  const fTema = el('fTema') as HTMLSelectElement;
  const fPeriod = el('fPeriod') as HTMLSelectElement;
  const fAny = el('fAny') as HTMLSelectElement;

  /* ---------------- OPTIONS ---------------- */

  fArea.innerHTML = `
    <option value="tots">Tots els territoris</option>
    <option value="1">Terrassa</option>
    <option value="2">Catalunya</option>
    <option value="3">Espanya</option>
    <option value="4">Europa</option>
    <option value="5">Món</option>
  `;

  fTema.innerHTML = `
    <option value="tots">Totes les temàtiques</option>
    <option value="1">Econòmic-laboral</option>
    <option value="2">Polític-social</option>
    <option value="3">Moviment obrer</option>
  `;

  fPeriod.innerHTML = `
    <option value="tots">Tots els períodes</option>
    <option value="restauracio">1910-1930</option>
    <option value="republca">1931-1939</option>
    <option value="dictadura">1939-1979</option>
  `;

  function updateYears(): void {
    const p = PERIODS[period];
    fAny.innerHTML = `<option value="tots">Tots els anys</option>`;

    for (let y = p.start; y <= p.end; y++) {
      const opt = document.createElement('option');
      opt.value = String(y);
      opt.textContent = String(y);
      fAny.appendChild(opt);
    }
  }

  updateYears();

  /* ---------------- LOAD ---------------- */

  async function load(): Promise<void> {
    const params = new URLSearchParams({
      area,
      tema,
      any,
      pagina: String(page),
    });

    const data = await fetchCronologia(lang, params);

    render(data);
  }

  /* ---------------- RENDER ---------------- */

  function render(data: ApiResponse): void {
    if (!data.eventos.length) {
      list.innerHTML = `<p>${t(lang, 'noResults')}</p>`;
      return;
    }

    let html = '';
    let lastYear: number | null = null;

    const sorted = [...data.eventos].sort((a, b) => a.any - b.any || (a.mesOrdre ?? 0) - (b.mesOrdre ?? 0) || (a.diaInici ?? 0) - (b.diaInici ?? 0));

    for (const ev of sorted) {
      if (ev.any !== lastYear) {
        html += `<h2 style="margin-top:20px">${ev.any}</h2>`;
        lastYear = ev.any;
      }

      html += `
        <div style="background:#fff;padding:12px;margin-bottom:10px;border-left:5px solid #007BFF;border-radius:6px">
          ${badge(areaLabel(lang, ev.area))}
          ${ev.tema ? badge('T' + ev.tema) : ''}
          <div style="margin-top:6px">${ev.textCa}</div>
        </div>
      `;
    }

    list.innerHTML = html;
  }

  /* ---------------- EVENTS ---------------- */

  fArea.onchange = () => {
    area = fArea.value;
    page = 1;
    load();
  };

  fTema.onchange = () => {
    tema = fTema.value;
    page = 1;
    load();
  };

  fPeriod.onchange = () => {
    period = fPeriod.value as PeriodKey;
    any = 'tots';
    updateYears();
    page = 1;
    load();
  };

  fAny.onchange = () => {
    any = fAny.value;
    page = 1;
    load();
  };

  load();
}
