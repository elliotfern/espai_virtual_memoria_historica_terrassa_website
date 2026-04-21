export type Lang = 'ca' | 'es' | 'en' | 'fr' | 'it' | 'pt';

type PeriodKey = 'tots' | 'restauracio' | 'republica' | 'dictadura';

interface PeriodRange {
  from: number;
  to: number;
}

const PERIOD_RANGES: Record<Exclude<PeriodKey, 'tots'>, PeriodRange> = {
  restauracio: { from: 1910, to: 1930 },
  republica: { from: 1931, to: 1939 },
  dictadura: { from: 1939, to: 1979 },
};

/* ================= LABELS ================= */

function periodLabel(lang: Lang, key: PeriodKey): string {
  const dict: Record<PeriodKey, Record<Lang, string>> = {
    tots: {
      ca: 'Tots',
      es: 'Todos',
      en: 'All',
      fr: 'Tous',
      it: 'Tutti',
      pt: 'Todos',
    },
    restauracio: {
      ca: 'Restauració (1910–1930)',
      es: 'Restauración (1910–1930)',
      en: 'Restoration (1910–1930)',
      fr: 'Restauration (1910–1930)',
      it: 'Restaurazione (1910–1930)',
      pt: 'Restauração (1910–1930)',
    },
    republica: {
      ca: 'Segona República (1931–1939)',
      es: 'Segunda República (1931–1939)',
      en: 'Second Republic (1931–1939)',
      fr: 'Deuxième République (1931–1939)',
      it: 'Seconda Repubblica (1931–1939)',
      pt: 'Segunda República (1931–1939)',
    },
    dictadura: {
      ca: 'Dictadura (1939–1979)',
      es: 'Dictadura (1939–1979)',
      en: 'Dictatorship (1939–1979)',
      fr: 'Dictature (1939–1979)',
      it: 'Dittatura (1939–1979)',
      pt: 'Ditadura (1939–1979)',
    },
  };

  return dict[key][lang];
}

/* ================= TERRITORI ================= */

function territoriLabel(lang: Lang, id: number): string {
  const dict: Record<number, Record<Lang, string>> = {
    1: { ca: 'Terrassa', es: 'Terrassa', en: 'Terrassa', fr: 'Terrassa', it: 'Terrassa', pt: 'Terrassa' },
    2: { ca: 'Catalunya', es: 'Cataluña', en: 'Catalonia', fr: 'Catalogne', it: 'Catalogna', pt: 'Catalunha' },
    3: { ca: 'Espanya', es: 'España', en: 'Spain', fr: 'Espagne', it: 'Spagna', pt: 'Espanha' },
    4: { ca: 'Europa', es: 'Europa', en: 'Europe', fr: 'Europe', it: 'Europa', pt: 'Europa' },
    5: { ca: 'Món', es: 'Mundo', en: 'World', fr: 'Monde', it: 'Mondo', pt: 'Mundo' },
  };

  return dict[id]?.[lang] ?? String(id);
}

/* ================= TEMES ================= */

function temaLabel(lang: Lang, id: number): string {
  const dict: Record<number, Record<Lang, string>> = {
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

  return dict[id]?.[lang] ?? String(id);
}

/* ================= CORE ================= */

function fillSelect(select: HTMLSelectElement | null, options: { value: string; label: string }[]): void {
  if (!select) return;

  select.innerHTML = '';

  const empty = document.createElement('option');
  empty.value = 'tots';
  empty.textContent = 'Tots';
  empty.selected = true;
  select.appendChild(empty);

  for (const opt of options) {
    const el = document.createElement('option');
    el.value = opt.value;
    el.textContent = opt.label;
    select.appendChild(el);
  }
}

function getYears(period: PeriodKey): number[] {
  if (period === 'tots') return [];

  const r = PERIOD_RANGES[period];
  const out: number[] = [];

  for (let y = r.from; y <= r.to; y++) {
    out.push(y);
  }

  return out;
}

/* ================= INIT ================= */

export function initCronologiaSelects(lang: Lang): void {
  const fAny = document.getElementById('fAny') as HTMLSelectElement | null;
  const fPeriod = document.getElementById('fPeriod') as HTMLSelectElement | null;
  const fArea = document.getElementById('fArea') as HTMLSelectElement | null;
  const fTema = document.getElementById('fTema') as HTMLSelectElement | null;

  if (!fAny || !fPeriod || !fArea || !fTema) return;

  /* AREA */
  fillSelect(fArea, [
    { value: '1', label: territoriLabel(lang, 1) },
    { value: '2', label: territoriLabel(lang, 2) },
    { value: '3', label: territoriLabel(lang, 3) },
    { value: '4', label: territoriLabel(lang, 4) },
    { value: '5', label: territoriLabel(lang, 5) },
  ]);

  /* TEMA */
  fillSelect(fTema, [
    { value: '1', label: temaLabel(lang, 1) },
    { value: '2', label: temaLabel(lang, 2) },
    { value: '3', label: temaLabel(lang, 3) },
  ]);

  /* PERIOD */
  fillSelect(fPeriod, [
    { value: 'restauracio', label: periodLabel(lang, 'restauracio') },
    { value: 'republica', label: periodLabel(lang, 'republica') },
    { value: 'dictadura', label: periodLabel(lang, 'dictadura') },
  ]);

  const updateYears = (p: PeriodKey) => {
    const years = getYears(p);

    fillSelect(
      fAny,
      years.map((y) => ({
        value: String(y),
        label: String(y),
      }))
    );
  };

  /* INIT */
  updateYears('tots');

  fPeriod.addEventListener('change', () => {
    const v = (fPeriod.value || 'tots') as PeriodKey;
    updateYears(v);
  });
}
