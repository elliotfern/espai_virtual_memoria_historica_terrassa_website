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

/* ---------------- API ---------------- */

async function fetchCronologia(lang: Lang, params: URLSearchParams): Promise<ApiResponse> {
  const url = `/api/cronologia/get/?${params.toString()}&lang=${lang}`;

  const res = await fetch(url);
  if (!res.ok) throw new Error(`HTTP ${res.status}`);

  const data: ApiResponse = await res.json();
  return data;
}

/* ---------------- BADGES ---------------- */

function badge(text: string): string {
  return `
    <span style="
      background:#c2af96;
      padding:4px 10px;
      border-radius:6px;
      margin-right:6px;
      font-size:12px;
      display:inline-block;
    ">${text}</span>
  `;
}

/* ---------------- MAIN ---------------- */

export function initCronologia(lang: Lang): void {
  const container = document.getElementById('cronologia') as HTMLDivElement | null;

  if (!container) {
    console.warn('Cronologia container not found');
    return;
  }

  if (!container) return;

  let area: string = 'tots';
  let tema: string = 'tots';
  // eslint-disable-next-line @typescript-eslint/no-unused-vars
  let period: PeriodKey = 'tots';
  let any: string = 'tots';
  let page = 1;

  function buildParams(): URLSearchParams {
    return new URLSearchParams({
      area,
      tema,
      any,
      pagina: String(page),
    });
  }

  function render(data: ApiResponse): void {
    const container = document.getElementById('cronologia') as HTMLDivElement | null;

    if (!container) {
      console.warn('Cronologia container not found');
      return;
    }

    container.innerHTML = '';

    if (data.eventos.length === 0) {
      container.innerHTML = `<p>${t(lang, 'noResults')}</p>`;
      return;
    }

    let html = '';
    let lastYear: number | null = null;

    for (const ev of data.eventos) {
      if (ev.any !== lastYear) {
        html += `<h2 style="margin-top:20px">${ev.any}</h2>`;
        lastYear = ev.any;
      }

      const badges = badge(areaLabel(lang, ev.area)) + (ev.tema !== null ? badge(`T${ev.tema}`) : '');

      html += `
        <div class="evento" style="
          background:#fff;
          border-left:5px solid #007BFF;
          border-radius:8px;
          padding:12px;
          margin-bottom:10px;
        ">
          <div style="margin-bottom:6px">${badges}</div>
          <div>${ev.textCa}</div>
        </div>
      `;
    }

    container.innerHTML = html;
  }

  async function load(): Promise<void> {
    const data = await fetchCronologia(lang, buildParams());
    render(data);
  }

  /* ---------------- FILTERS (EVENT DELEGATION) ---------------- */

  document.addEventListener('click', (e: Event) => {
    const target = e.target as HTMLElement;

    const periodBtn = target.closest('[data-period]') as HTMLElement | null;
    const areaBtn = target.closest('[data-area]') as HTMLElement | null;
    const temaBtn = target.closest('[data-tema]') as HTMLElement | null;

    if (periodBtn) {
      period = periodBtn.dataset.period as PeriodKey;
      any = 'tots';
      page = 1;
      load();
    }

    if (areaBtn) {
      area = areaBtn.dataset.area ?? 'tots';
      page = 1;
      load();
    }

    if (temaBtn) {
      tema = temaBtn.dataset.tema ?? 'tots';
      page = 1;
      load();
    }
  });

  load();
}
