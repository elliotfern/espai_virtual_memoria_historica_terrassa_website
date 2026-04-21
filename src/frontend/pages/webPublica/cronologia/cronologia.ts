export type Lang = 'ca' | 'es' | 'en' | 'fr' | 'it' | 'pt';

type PeriodKey = 'tots' | 'restauracio' | 'republica' | 'dictadura';

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
  ca: { noResults: 'No hi ha resultats', search: 'Cerca', year: 'Any', period: 'Període', territory: 'Territori', theme: 'Temàtica' },
  es: { noResults: 'Sin resultados', search: 'Buscar', year: 'Año', period: 'Periodo', territory: 'Territorio', theme: 'Temática' },
  en: { noResults: 'No results', search: 'Search', year: 'Year', period: 'Period', territory: 'Territory', theme: 'Theme' },
  fr: { noResults: 'Aucun résultat', search: 'Recherche', year: 'Année', period: 'Période', territory: 'Territoire', theme: 'Thème' },
  it: { noResults: 'Nessun risultato', search: 'Cerca', year: 'Anno', period: 'Periodo', territory: 'Territorio', theme: 'Tema' },
  pt: { noResults: 'Sem resultados', search: 'Pesquisar', year: 'Ano', period: 'Período', territory: 'Território', theme: 'Tema' },
};

function t(lang: Lang, key: string): string {
  return dict[lang][key] ?? key;
}

/* ---------------- AREA ---------------- */

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

/* ---------------- TEMA ---------------- */

function temaLabel(lang: Lang, tema: number | null): string {
  if (tema === null) return '';

  const labels: Record<number, Record<Lang, string>> = {
    1: { ca: 'Fets econòmico-laborals', es: 'Hechos económico-laborales', en: 'Economic and labor events', fr: 'Faits économiques et sociaux', it: 'Fatti economico-lavorativi', pt: 'Factos económico-laborais' },
    2: { ca: 'Fets polítics i socials', es: 'Hechos políticos y sociales', en: 'Political and social events', fr: 'Faits politiques et sociaux', it: 'Fatti politici e sociali', pt: 'Factos políticos e sociais' },
    3: { ca: 'Moviment obrer', es: 'Movimiento obrero', en: 'Labor movement', fr: 'Mouvement ouvrier', it: 'Movimento operaio', pt: 'Movimento operário' },
  };

  return labels[tema]?.[lang] ?? '';
}

/* ---------------- PERIOD ---------------- */

const PERIOD_RANGES: Record<Exclude<PeriodKey, 'tots'>, { from: number; to: number }> = {
  restauracio: { from: 1910, to: 1930 },
  republica: { from: 1931, to: 1939 },
  dictadura: { from: 1939, to: 1979 },
};

function periodLabel(lang: Lang, key: PeriodKey): string {
  const labels: Record<PeriodKey, Record<Lang, string>> = {
    tots: { ca: 'Tots', es: 'Todos', en: 'All', fr: 'Tous', it: 'Tutti', pt: 'Todos' },
    restauracio: { ca: 'Restauració', es: 'Restauración', en: 'Restoration', fr: 'Restauration', it: 'Restaurazione', pt: 'Restauração' },
    republica: { ca: 'Segona República', es: 'Segunda República', en: 'Second Republic', fr: 'Deuxième République', it: 'Seconda Repubblica', pt: 'Segunda República' },
    dictadura: { ca: 'Dictadura', es: 'Dictadura', en: 'Dictatorship', fr: 'Dictature', it: 'Dittatura', pt: 'Ditadura' },
  };

  return labels[key][lang];
}

/* ---------------- HELPERS ---------------- */

function mustGet<T extends HTMLElement>(id: string): T {
  const el = document.getElementById(id);
  if (!el) throw new Error(`Missing element: ${id}`);
  return el as T;
}

async function fetchCronologia(lang: Lang, params: URLSearchParams): Promise<ApiResponse> {
  const res = await fetch(`/api/cronologia/get/?${params.toString()}&lang=${lang}`);
  if (!res.ok) throw new Error(`HTTP ${res.status}`);
  return res.json();
}

/* ---------------- PERIOD FROM YEAR ---------------- */

function getPeriodFromYear(year: number): PeriodKey {
  if (year >= 1910 && year <= 1930) return 'restauracio';
  if (year >= 1931 && year <= 1939) return 'republica';
  if (year >= 1939 && year <= 1979) return 'dictadura';
  return 'tots';
}

/* ---------------- MAIN ---------------- */

export function initCronologia(lang: Lang): void {
  const container = mustGet<HTMLDivElement>('cronologia');

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

  const list = mustGet<HTMLDivElement>('listCronologia');
  const status = mustGet<HTMLDivElement>('statusCronologia');
  const fArea = mustGet<HTMLSelectElement>('fArea');
  const fTema = mustGet<HTMLSelectElement>('fTema');
  const fPeriod = mustGet<HTMLSelectElement>('fPeriod');
  const fAny = mustGet<HTMLSelectElement>('fAny');

  let area: number | 'tots' = 'tots';
  let tema: number | 'tots' = 'tots';
  let period: PeriodKey = 'tots';
  let any: number | 'tots' = 'tots';

  /* ---------------- PERIOD OPTIONS ---------------- */

  function buildPeriodOptions(): void {
    fPeriod.innerHTML = `
      <option value="tots">${periodLabel(lang, 'tots')}</option>
      <option value="restauracio">${periodLabel(lang, 'restauracio')}</option>
      <option value="republica">${periodLabel(lang, 'republica')}</option>
      <option value="dictadura">${periodLabel(lang, 'dictadura')}</option>
    `;
  }

  /* ---------------- YEARS ---------------- */

  function updateYears(): void {
    fAny.innerHTML = `<option value="">${t(lang, 'year')}</option>`;

    if (period === 'tots') return;

    const range = PERIOD_RANGES[period];
    for (let y = range.from; y <= range.to; y++) {
      const opt = document.createElement('option');
      opt.value = String(y);
      opt.textContent = String(y);
      fAny.appendChild(opt);
    }

    any = 'tots';
  }

  function params(): URLSearchParams {
    const p = new URLSearchParams();

    if (area !== 'tots') p.set('area', String(area));
    if (tema !== 'tots') p.set('tema', String(tema));
    if (any !== 'tots') p.set('any', String(any));

    p.set('pagina', '1');
    return p;
  }

  async function load(): Promise<void> {
    const data = await fetchCronologia(lang, params());

    list.innerHTML = data.eventos
      .map((ev) => {
        const evPeriod = getPeriodFromYear(ev.any);

        return `
        <div class="p-3 mb-2" style="background:#fff;border-left:5px solid #c2af96;border-radius:6px;">
          
          <div class="mb-2">
            <span><b>${t(lang, 'territory')}:</b> ${areaLabel(lang, ev.area)}</span>
            ${ev.tema !== null ? `<span class="ms-2"><b>${t(lang, 'theme')}:</b> ${temaLabel(lang, ev.tema)}</span>` : ''}
            <span class="ms-2"><b>${t(lang, 'period')}:</b> ${periodLabel(lang, evPeriod)}</span>
          </div>

          <div>${ev.textCa}</div>
        </div>
      `;
      })
      .join('');

    status.textContent = `${data.totalEventos} resultat(s)`;
  }

  /* ---------------- EVENTS ---------------- */

  fArea.onchange = (e) => {
    area = (e.target as HTMLSelectElement).value ? Number((e.target as HTMLSelectElement).value) : 'tots';
    load();
  };

  fTema.onchange = (e) => {
    tema = (e.target as HTMLSelectElement).value ? Number((e.target as HTMLSelectElement).value) : 'tots';
    load();
  };

  fPeriod.onchange = (e) => {
    period = ((e.target as HTMLSelectElement).value || 'tots') as PeriodKey;
    updateYears();
    load();
  };

  fAny.onchange = (e) => {
    any = (e.target as HTMLSelectElement).value ? Number((e.target as HTMLSelectElement).value) : 'tots';
    load();
  };

  buildPeriodOptions();
  updateYears();
  load();
}
