// publicEstudisList.ts

type Lang = 'ca' | 'es' | 'en' | 'fr' | 'it' | 'pt';

type ApiResponseArr<T> = {
  status: string;
  message: string;
  data: T[];
};

interface EstudiPublic {
  id: number;
  slug: string;
  any_publicacio: number | null;
  titol: string | null;
  resum: string | null;
  url_document: string | null;
  document_lang: Lang | null;
  is_fallback_document: number | null;
  periode: string | null;
  territori: string | null;
  tipus: string | null;
  autors: string | null;
}

function escapeHtml(input: string): string {
  return input.replaceAll('&', '&amp;').replaceAll('<', '&lt;').replaceAll('>', '&gt;').replaceAll('"', '&quot;').replaceAll("'", '&#039;');
}

function safeUrl(u: string | null): string | null {
  if (!u) return null;
  const s = u.trim();
  if (!s) return null;
  if (s.startsWith('http://') || s.startsWith('https://') || s.startsWith('/')) return s;
  return null;
}

function t(lang: Lang, key: string): string {
  const dict: Record<Lang, Record<string, string>> = {
    ca: {
      loading: 'Carregant…',
      loadError: "No s'han pogut carregar els estudis.",
      empty: 'No hi ha estudis disponibles.',
      search: 'Cerca',
      searchPlaceholder: 'Títol, resum o autor…',
      year: 'Any',
      yearsAll: 'Tots',
      author: 'Autor/a',
      authorsAll: 'Tots',
      periode: 'Període',
      periodesAll: 'Tots',
      territori: 'Territori',
      territorisAll: 'Tots',
      tipus: 'Tipus',
      tipusAll: 'Tots',
      openDocument: 'obrir document',
      availableOnlyIn: 'Disponible només en',
      noSummary: 'Sense resum',
      results: 'resultat(s)',
      lang_ca: 'català',
      lang_es: 'castellà',
      lang_en: 'anglès',
      lang_fr: 'francès',
      lang_it: 'italià',
      lang_pt: 'portuguès',
      authorLabel: 'Autor/a:',
      yearLabel: 'Any:',
      periodeLabel: 'Període:',
      territoriLabel: 'Territori:',
      tipusLabel: 'Tipus:',
    },
    es: {
      loading: 'Cargando…',
      loadError: 'No se han podido cargar los estudios.',
      empty: 'No hay estudios disponibles.',
      search: 'Buscar',
      searchPlaceholder: 'Título, resumen o autor…',
      year: 'Año',
      yearsAll: 'Todos',
      author: 'Autor/a',
      authorsAll: 'Todos',
      periode: 'Periodo',
      periodesAll: 'Todos',
      territori: 'Territorio',
      territorisAll: 'Todos',
      tipus: 'Tipo',
      tipusAll: 'Todos',
      openDocument: 'abrir documento',
      availableOnlyIn: 'Disponible sólo en',
      noSummary: 'Sin resumen',
      results: 'resultado(s)',
      lang_ca: 'catalán',
      lang_es: 'castellano',
      lang_en: 'inglés',
      lang_fr: 'francés',
      lang_it: 'italiano',
      lang_pt: 'portugués',
      authorLabel: 'Autor/a:',
      yearLabel: 'Año:',
      periodeLabel: 'Periodo:',
      territoriLabel: 'Territorio:',
      tipusLabel: 'Tipo:',
    },
    en: {
      loading: 'Loading…',
      loadError: 'Studies could not be loaded.',
      empty: 'No studies available.',
      search: 'Search',
      searchPlaceholder: 'Title, summary or author…',
      year: 'Year',
      yearsAll: 'All',
      author: 'Author',
      authorsAll: 'All',
      periode: 'Period',
      periodesAll: 'All',
      territori: 'Territory',
      territorisAll: 'All',
      tipus: 'Type',
      tipusAll: 'All',
      openDocument: 'open document',
      availableOnlyIn: 'Available only in',
      noSummary: 'No summary',
      results: 'result(s)',
      lang_ca: 'Catalan',
      lang_es: 'Spanish',
      lang_en: 'English',
      lang_fr: 'French',
      lang_it: 'Italian',
      lang_pt: 'Portuguese',
      authorLabel: 'Author:',
      yearLabel: 'Year:',
      periodeLabel: 'Period:',
      territoriLabel: 'Territory:',
      tipusLabel: 'Type:',
    },
    fr: {
      loading: 'Chargement…',
      loadError: "Les études n'ont pas pu être chargées.",
      empty: 'Aucune étude disponible.',
      search: 'Recherche',
      searchPlaceholder: 'Titre, résumé ou auteur…',
      year: 'Année',
      yearsAll: 'Tous',
      author: 'Auteur',
      authorsAll: 'Tous',
      periode: 'Période',
      periodesAll: 'Toutes',
      territori: 'Territoire',
      territorisAll: 'Tous',
      tipus: 'Type',
      tipusAll: 'Tous',
      openDocument: 'ouvrir le document',
      availableOnlyIn: 'Disponible uniquement en',
      noSummary: 'Sans résumé',
      results: 'résultat(s)',
      lang_ca: 'catalan',
      lang_es: 'espagnol',
      lang_en: 'anglais',
      lang_fr: 'français',
      lang_it: 'italien',
      lang_pt: 'portugais',
      authorLabel: 'Autore:',
      yearLabel: 'Anno:',
      periodeLabel: 'Periodo:',
      territoriLabel: 'Territorio:',
      tipusLabel: 'Tipo:',
    },
    it: {
      loading: 'Caricamento…',
      loadError: 'Impossibile caricare gli studi.',
      empty: 'Nessuno studio disponibile.',
      search: 'Cerca',
      searchPlaceholder: 'Titolo, riassunto o autore…',
      year: 'Anno',
      yearsAll: 'Tutti',
      author: 'Autore',
      authorsAll: 'Tutti',
      periode: 'Periodo',
      periodesAll: 'Tutti',
      territori: 'Territorio',
      territorisAll: 'Tutti',
      tipus: 'Tipo',
      tipusAll: 'Tutti',
      openDocument: 'apri documento',
      availableOnlyIn: 'Disponibile solo in',
      noSummary: 'Senza riassunto',
      results: 'risultato/i',
      lang_ca: 'catalano',
      lang_es: 'spagnolo',
      lang_en: 'inglese',
      lang_fr: 'francese',
      lang_it: 'italiano',
      lang_pt: 'portoghese',
      authorLabel: 'Autore:',
      yearLabel: 'Anno:',
      periodeLabel: 'Periodo:',
      territoriLabel: 'Territorio:',
      tipusLabel: 'Tipo:',
    },
    pt: {
      loading: 'A carregar…',
      loadError: 'Não foi possível carregar os estudos.',
      empty: 'Não há estudos disponíveis.',
      search: 'Pesquisar',
      searchPlaceholder: 'Título, resumo ou autor…',
      year: 'Ano',
      yearsAll: 'Todos',
      author: 'Autor',
      authorsAll: 'Todos',
      periode: 'Período',
      periodesAll: 'Todos',
      territori: 'Território',
      territorisAll: 'Todos',
      tipus: 'Tipo',
      tipusAll: 'Todos',
      openDocument: 'abrir documento',
      availableOnlyIn: 'Disponível apenas em',
      noSummary: 'Sem resumo',
      results: 'resultado(s)',
      lang_ca: 'catalão',
      lang_es: 'espanhol',
      lang_en: 'inglês',
      lang_fr: 'francês',
      lang_it: 'italiano',
      lang_pt: 'português',
      authorLabel: 'Autor:',
      yearLabel: 'Ano:',
      periodeLabel: 'Período:',
      territoriLabel: 'Território:',
      tipusLabel: 'Tipo:',
    },
  };

  return dict[lang][key] ?? key;
}

function languageName(uiLang: Lang, docLang: Lang | null): string {
  if (!docLang) return '';
  return t(uiLang, `lang_${docLang}`);
}

function fallbackMessage(uiLang: Lang, docLang: Lang | null): string {
  const name = languageName(uiLang, docLang);
  if (!name) return '';
  return `${t(uiLang, 'availableOnlyIn')} ${name}`;
}

async function fetchEstudis(lang: Lang): Promise<EstudiPublic[]> {
  const url = `/api/estudis/get/estudisPublic?lang=${encodeURIComponent(lang)}`;
  const res = await fetch(url, { headers: { Accept: 'application/json' } });
  if (!res.ok) throw new Error(`HTTP ${res.status}`);

  const json: unknown = await res.json();
  const parsed = json as ApiResponseArr<EstudiPublic>;
  if (!parsed || !Array.isArray(parsed.data)) return [];
  return parsed.data;
}

function renderFilters(lang: Lang): string {
  return `
    <div class="p-4" style="background-color:#EEEAD9;border-radius:6px;">
      <div class="row g-3">
        <div class="col-12">
          <label for="fText" class="form-label fw-bold raleway">${escapeHtml(t(lang, 'search'))}</label>
          <input type="search" class="form-control" id="fText" placeholder="${escapeHtml(t(lang, 'searchPlaceholder'))}">
        </div>
      </div>

      <div class="row g-3 align-items-end mt-1">
        <div class="col-md-2">
          <label for="fAny" class="form-label fw-bold raleway">${escapeHtml(t(lang, 'year'))}</label>
          <select class="form-select" id="fAny">
            <option value="">${escapeHtml(t(lang, 'yearsAll'))}</option>
          </select>
        </div>

        <div class="col-md-2">
          <label for="fAutor" class="form-label fw-bold raleway">${escapeHtml(t(lang, 'author'))}</label>
          <select class="form-select" id="fAutor">
            <option value="">${escapeHtml(t(lang, 'authorsAll'))}</option>
          </select>
        </div>

        <div class="col-md-3">
          <label for="fPeriode" class="form-label fw-bold raleway">${escapeHtml(t(lang, 'periode'))}</label>
          <select class="form-select" id="fPeriode">
            <option value="">${escapeHtml(t(lang, 'periodesAll'))}</option>
          </select>
        </div>

        <div class="col-md-3">
          <label for="fTerritori" class="form-label fw-bold raleway">${escapeHtml(t(lang, 'territori'))}</label>
          <select class="form-select" id="fTerritori">
            <option value="">${escapeHtml(t(lang, 'territorisAll'))}</option>
          </select>
        </div>

        <div class="col-md-2">
          <label for="fTipus" class="form-label fw-bold raleway">${escapeHtml(t(lang, 'tipus'))}</label>
          <select class="form-select" id="fTipus">
            <option value="">${escapeHtml(t(lang, 'tipusAll'))}</option>
          </select>
        </div>
      </div>
    </div>
  `;
}

function renderCard(item: EstudiPublic, lang: Lang): string {
  const titol = item.titol ? escapeHtml(item.titol) : '—';
  const autors = item.autors ? escapeHtml(item.autors) : '—';
  const anyTxt = item.any_publicacio !== null && item.any_publicacio !== undefined ? escapeHtml(String(item.any_publicacio)) : '—';
  const periode = item.periode ? escapeHtml(item.periode) : '—';
  const territori = item.territori ? escapeHtml(item.territori) : '—';
  const tipus = item.tipus ? escapeHtml(item.tipus) : '—';
  const resum = item.resum ? escapeHtml(item.resum).replaceAll('\n', '<br>') : '';
  const docUrl = safeUrl(item.url_document);

  const fallbackMsg = (item.is_fallback_document ?? 0) === 1 ? `<div class="text-muted raleway mt-2">${escapeHtml(fallbackMessage(lang, item.document_lang))}</div>` : '';

  const resumHtml = resum ? `<div class="raleway mt-3">${resum}</div>` : `<div class="text-muted raleway mt-3">${escapeHtml(t(lang, 'noSummary'))}</div>`;

  const btnHtml = docUrl
    ? `
      <div class="d-flex flex-wrap gap-2 mt-3">
       <a class="btn w-auto raleway"
          href="${escapeHtml(docUrl)}"
          target="_blank"
          rel="noopener noreferrer"
          style="
            background-color:#133b7c;
            color:#ffffff;
            border:1px solid #0f2e60;
            padding:7px 14px;
            border-radius:6px;
            font-size:0.92rem;
            font-weight:600;
          "
        >
          ${escapeHtml(t(lang, 'openDocument'))}
        </a>
      </div>
      ${fallbackMsg}
    `
    : '';

  return `
    <div class="col-12">
     <article class="p-4" style="
          background-color:#ffffff;
          border-radius:6px;
          border-left:6px solid #c2af96;
          padding-left:18px;
          ">

        <div class="d-flex flex-column gap-2">
          <span class="titol mitja lora negreta" style="line-height:1.15;display:block;">
            ${titol}
          </span>

          <div class="d-flex flex-wrap gap-2 mt-2">

            <span class="raleway" style="
              background-color:#c2af96;
              color:#2f2a24;
              border:1px solid #b19d84;
              padding:6px 12px;
              border-radius:6px;
              display:inline-flex;
              align-items:center;
              font-size:0.92rem;
            ">
              <span class="negreta me-1">${escapeHtml(t(lang, 'authorLabel'))}</span>${autors}
            </span>

            <span class="raleway" style="
              background-color:#c2af96;
              color:#2f2a24;
              border:1px solid #b19d84;
              padding:6px 12px;
              border-radius:6px;
              display:inline-flex;
              align-items:center;
              font-size:0.92rem;
            ">
              <span class="negreta me-1">${escapeHtml(t(lang, 'yearLabel'))}</span>${anyTxt}
            </span>

            <span class="raleway" style="
              background-color:#c2af96;
              color:#2f2a24;
              border:1px solid #b19d84;
              padding:6px 12px;
              border-radius:6px;
              display:inline-flex;
              align-items:center;
              font-size:0.92rem;
            ">
              <span class="negreta me-1">${escapeHtml(t(lang, 'periodeLabel'))}</span>${periode}
            </span>

            <span class="raleway" style="
              background-color:#c2af96;
              color:#2f2a24;
              border:1px solid #b19d84;
              padding:6px 12px;
              border-radius:6px;
              display:inline-flex;
              align-items:center;
              font-size:0.92rem;
            ">
              <span class="negreta me-1">${escapeHtml(t(lang, 'territoriLabel'))}</span>${territori}
            </span>

            <span class="raleway" style="
              background-color:#c2af96;
              color:#2f2a24;
              border:1px solid #b19d84;
              padding:6px 12px;
              border-radius:6px;
              display:inline-flex;
              align-items:center;
              font-size:0.92rem;
            ">
              <span class="negreta me-1">${escapeHtml(t(lang, 'tipusLabel'))}</span>${tipus}
            </span>

          </div>

          ${resumHtml}
          ${btnHtml}
        </div>
      </article>
    </div>
  `;
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

function normalizeString(v: string | null): string {
  return (v ?? '').trim();
}

export async function initPublicEstudisList(lang: Lang): Promise<void> {
  const container = document.getElementById('publicEstudis') as HTMLDivElement | null;
  if (!container) return;

  container.innerHTML = `
    ${renderFilters(lang)}
    <div id="statusEstudis" class="text-muted raleway mt-3">${escapeHtml(t(lang, 'loading'))}</div>
    <div id="listEstudis" class="row g-4 mt-1"></div>
  `;

  const status = document.getElementById('statusEstudis') as HTMLDivElement | null;
  const list = document.getElementById('listEstudis') as HTMLDivElement | null;
  const fText = document.getElementById('fText') as HTMLInputElement | null;
  const fAny = document.getElementById('fAny') as HTMLSelectElement | null;
  const fAutor = document.getElementById('fAutor') as HTMLSelectElement | null;
  const fPeriode = document.getElementById('fPeriode') as HTMLSelectElement | null;
  const fTerritori = document.getElementById('fTerritori') as HTMLSelectElement | null;
  const fTipus = document.getElementById('fTipus') as HTMLSelectElement | null;

  if (!status || !list || !fText || !fAny || !fAutor || !fPeriode || !fTerritori || !fTipus) return;

  let all: EstudiPublic[] = [];
  try {
    all = await fetchEstudis(lang);
  } catch (e) {
    status.textContent = t(lang, 'loadError');
    list.innerHTML = '';
    console.log(e);
    return;
  }

  if (!all.length) {
    status.textContent = t(lang, 'empty');
    list.innerHTML = '';
    return;
  }

  const anys = Array.from(new Set(all.map((x) => (x.any_publicacio !== null && x.any_publicacio !== undefined ? String(x.any_publicacio) : '')).filter(Boolean)))
    .sort()
    .reverse();
  fillSelect(fAny, anys);

  const autors = Array.from(
    new Set(
      all.flatMap((x) =>
        normalizeString(x.autors)
          .split(',')
          .map((s) => s.trim())
          .filter(Boolean)
      )
    )
  ).sort((a, b) => a.localeCompare(b));
  fillSelect(fAutor, autors);

  const periodes = Array.from(new Set(all.map((x) => normalizeString(x.periode)).filter(Boolean))).sort((a, b) => a.localeCompare(b));
  fillSelect(fPeriode, periodes);

  const territoris = Array.from(new Set(all.map((x) => normalizeString(x.territori)).filter(Boolean))).sort((a, b) => a.localeCompare(b));
  fillSelect(fTerritori, territoris);

  const tipus = Array.from(new Set(all.map((x) => normalizeString(x.tipus)).filter(Boolean))).sort((a, b) => a.localeCompare(b));
  fillSelect(fTipus, tipus);

  const apply = () => {
    const q = fText.value.trim().toLowerCase();
    const any = fAny.value;
    const autor = fAutor.value;
    const periode = fPeriode.value;
    const territori = fTerritori.value;
    const tipusValue = fTipus.value;

    const filtered = all.filter((x) => {
      const haystack = [x.titol ?? '', x.resum ?? '', x.autors ?? ''].join(' ').toLowerCase();

      const okQ = !q || haystack.includes(q);
      const okAny = !any || (x.any_publicacio !== null && x.any_publicacio !== undefined && String(x.any_publicacio) === any);

      const okAutor =
        !autor ||
        normalizeString(x.autors)
          .split(',')
          .map((s) => s.trim())
          .includes(autor);

      const okPeriode = !periode || normalizeString(x.periode) === periode;
      const okTerritori = !territori || normalizeString(x.territori) === territori;
      const okTipus = !tipusValue || normalizeString(x.tipus) === tipusValue;

      return okQ && okAny && okAutor && okPeriode && okTerritori && okTipus;
    });

    status.textContent = `${filtered.length} ${t(lang, 'results')}`;
    list.innerHTML = filtered.map((item) => renderCard(item, lang)).join('');
  };

  fText.addEventListener('input', apply);
  fAny.addEventListener('change', apply);
  fAutor.addEventListener('change', apply);
  fPeriode.addEventListener('change', apply);
  fTerritori.addEventListener('change', apply);
  fTipus.addEventListener('change', apply);

  apply();
}
