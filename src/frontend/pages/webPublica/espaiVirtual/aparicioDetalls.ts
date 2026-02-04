// publicAparicioPremsaDetalls.ts
// Pàgina pública: detalls d'una aparició de premsa
// Endpoint: /api/auxiliars/get/premsaAparicioI18n?id=123

import { formatDatesForm } from '../../../services/formatDates/dates';

type Lang = 'ca' | 'es' | 'en' | 'fr' | 'it' | 'pt';

type ApiResponseArr<T> = {
  status: string;
  message: string;
  data: T[];
};

type PremsaAparicioI18nRow = {
  aparicio_id: number;
  lang: Lang;

  titol: string | null;
  resum: string | null;
  notes: string | null;
  pdf_url: string | null;

  data_aparicio: string | null; // YYYY-MM-DD
  tipus_aparicio: string | null;
  mitja_id: number | null;
  url_noticia: string | null;
  nomMitja: string | null;
};

type PremsaAparicioDetalls = {
  base: {
    id: number;
    dataAparicio: string | null;
    tipusAparicio: string | null;
    mitjaId: number | null;
    urlNoticia: string | null;
    nomMitja: string | null;
  };
  i18nByLang: Record<Lang, { titol: string; resum: string; notes: string; pdfUrl: string }>;
};

const LANGS: Lang[] = ['ca', 'es', 'en', 'fr', 'it', 'pt'];

function escapeHtml(input: string): string {
  return input.replaceAll('&', '&amp;').replaceAll('<', '&lt;').replaceAll('>', '&gt;').replaceAll('"', '&quot;').replaceAll("'", '&#039;');
}

function formatDatePublic(dateYmd: string | null): string {
  if (!dateYmd) return '—';
  return formatDatesForm(dateYmd);
}

function tipusAparicioHuman(tipus: string | null): string {
  switch (tipus) {
    case 'presentacio':
      return 'Presentació';
    case 'roda_premsa':
      return 'Roda de premsa';
    case 'activitat':
      return 'Activitat';
    case 'entrevista':
      return 'Entrevista';
    case 'reportatge':
      return 'Reportatge';
    case 'nota_premsa':
      return 'Nota de premsa';
    case 'publicacio_xarxes':
      return 'Publicació xarxes';
    case 'altres':
      return 'Altres';
    default:
      return '—';
  }
}

function safeUrl(u: string | null): string | null {
  if (!u) return null;
  const s = u.trim();
  if (!s) return null;
  // Acepta http(s) y rutas relativas
  if (s.startsWith('http://') || s.startsWith('https://') || s.startsWith('/')) return s;
  return null;
}

function emptyI18n() {
  return { titol: '', resum: '', notes: '', pdfUrl: '' };
}

function buildDetalls(rows: PremsaAparicioI18nRow[]): PremsaAparicioDetalls {
  const first = rows[0];
  if (!first) throw new Error('Empty rows');

  const i18nByLang: PremsaAparicioDetalls['i18nByLang'] = {
    ca: emptyI18n(),
    es: emptyI18n(),
    en: emptyI18n(),
    fr: emptyI18n(),
    it: emptyI18n(),
    pt: emptyI18n(),
  };

  for (const r of rows) {
    i18nByLang[r.lang] = {
      titol: r.titol ?? '',
      resum: r.resum ?? '',
      notes: r.notes ?? '',
      pdfUrl: r.pdf_url ?? '',
    };
  }

  return {
    base: {
      id: first.aparicio_id,
      dataAparicio: first.data_aparicio ?? null,
      tipusAparicio: first.tipus_aparicio ?? null,
      mitjaId: first.mitja_id ?? null,
      urlNoticia: first.url_noticia ?? null,
      nomMitja: first.nomMitja ?? null,
    },
    i18nByLang,
  };
}

function pickI18n(detalls: PremsaAparicioDetalls, lang: Lang) {
  const chosen = detalls.i18nByLang[lang];
  const fallback = detalls.i18nByLang.ca;
  return {
    titol: chosen.titol || fallback.titol || '—',
    resum: chosen.resum || fallback.resum || '',
    notes: chosen.notes || fallback.notes || '',
    pdfUrl: chosen.pdfUrl || fallback.pdfUrl || '',
  };
}

function getCurrentLang(fallback: Lang = 'ca'): Lang {
  const htmlLang = (document.documentElement.getAttribute('lang') || fallback) as Lang;
  return LANGS.includes(htmlLang) ? htmlLang : fallback;
}

async function fetchAparicioDetalls(id: number): Promise<PremsaAparicioDetalls> {
  const url = `/api/auxiliars/get/premsaAparicioI18n?id=${encodeURIComponent(String(id))}`;
  const res = await fetch(url, { headers: { Accept: 'application/json' } });
  if (!res.ok) throw new Error(`HTTP ${res.status}`);

  const json: unknown = await res.json();
  const parsed = json as ApiResponseArr<PremsaAparicioI18nRow>;
  const rows = Array.isArray(parsed?.data) ? parsed.data : [];

  if (!rows.length) throw new Error('Empty data');
  return buildDetalls(rows);
}

function renderSkeleton(): string {
  // Contenedor principal (id fijo para tu plantilla)
  return `
    <article class="p-4" style="background-color:#ffffff;border-radius:6px;">
      <div class="d-flex flex-column gap-3">

        <div class="d-flex flex-wrap gap-2 align-items-center justify-content-between">
          <div class="d-flex flex-column">
            <span class="text-muted raleway" id="apMetaTop">Carregant…</span>
            <span class="titol lora negreta" id="apTitol" style="line-height:1.15;">—</span>
          </div>

          <div class="d-flex flex-wrap gap-2">
            <a id="apBtnBack" class="btn btn-primary btn-custom-2 w-auto" href="/espai-virtual/premsa">
              tornar
            </a>
          </div>
        </div>

        <div class="p-3" style="background-color:#EEEAD9;border-radius:6px;">
          <div class="row g-3">
            <div class="col-12 col-md-4">
              <div class="text-muted raleway">Data</div>
              <div class="raleway negreta" id="apData">—</div>
            </div>
            <div class="col-12 col-md-4">
              <div class="text-muted raleway">Mitjà</div>
              <div class="raleway negreta" id="apMitja">—</div>
            </div>
            <div class="col-12 col-md-4">
              <div class="text-muted raleway">Tipus</div>
              <div class="raleway negreta" id="apTipus">—</div>
            </div>
          </div>

          <div class="d-flex flex-wrap gap-2 mt-3">
            <a id="apUrlNoticia" class="btn btn-primary btn-custom-2 w-auto d-none" href="#" target="_blank" rel="noopener noreferrer">
              obrir notícia
            </a>
            <a id="apPdf" class="btn btn-primary btn-custom-2 w-auto d-none" href="#" target="_blank" rel="noopener noreferrer">
              obrir PDF
            </a>
          </div>
        </div>

        <div class="d-flex flex-column gap-2">
          <div id="apResumWrap" class="d-none">
            <div class="text-muted raleway mb-1">Resum</div>
            <div class="raleway" id="apResum"></div>
          </div>

          <div id="apNotesWrap" class="d-none">
            <div class="text-muted raleway mb-1">Notes</div>
            <div class="raleway" id="apNotes"></div>
          </div>
        </div>

        <div class="text-muted raleway" id="apStatus"></div>
      </div>
    </article>
  `;
}

function setBtnHrefForLang(el: HTMLAnchorElement, lang: Lang, hrefCa: string) {
  // Si tu web usa prefijo /es /en... en público
  const prefix = lang === 'ca' ? '' : `/${lang}`;
  el.href = `${prefix}${hrefCa}`;
}

function renderDetalls(detalls: PremsaAparicioDetalls, lang: Lang): void {
  const t = pickI18n(detalls, lang);

  const $metaTop = document.getElementById('apMetaTop');
  const $titol = document.getElementById('apTitol');
  const $data = document.getElementById('apData');
  const $mitja = document.getElementById('apMitja');
  const $tipus = document.getElementById('apTipus');

  const $urlNoticia = document.getElementById('apUrlNoticia') as HTMLAnchorElement | null;
  const $pdf = document.getElementById('apPdf') as HTMLAnchorElement | null;

  const $resumWrap = document.getElementById('apResumWrap');
  const $resum = document.getElementById('apResum');

  const $notesWrap = document.getElementById('apNotesWrap');
  const $notes = document.getElementById('apNotes');

  const $status = document.getElementById('apStatus');

  if ($titol) $titol.textContent = t.titol || '—';

  const dataTxt = formatDatePublic(detalls.base.dataAparicio);
  const mitjaTxt = detalls.base.nomMitja ? detalls.base.nomMitja : '—';
  const tipusTxt = tipusAparicioHuman(detalls.base.tipusAparicio);

  if ($data) $data.textContent = dataTxt;
  if ($mitja) $mitja.textContent = mitjaTxt;
  if ($tipus) $tipus.textContent = tipusTxt;

  if ($metaTop) {
    const parts = [dataTxt, mitjaTxt, tipusTxt].filter((x) => x && x !== '—');
    $metaTop.textContent = parts.length ? parts.join(' · ') : '';
  }

  // Links
  if ($urlNoticia) {
    const u = safeUrl(detalls.base.urlNoticia);
    if (u) {
      $urlNoticia.href = u;
      $urlNoticia.classList.remove('d-none');
    } else {
      $urlNoticia.classList.add('d-none');
      $urlNoticia.href = '#';
    }
  }

  if ($pdf) {
    const p = safeUrl(t.pdfUrl);
    if (p) {
      $pdf.href = p;
      $pdf.classList.remove('d-none');
    } else {
      $pdf.classList.add('d-none');
      $pdf.href = '#';
    }
  }

  // Textos (con tu tipografía/clases)
  if ($resumWrap && $resum) {
    const txt = (t.resum || '').trim();
    if (txt) {
      $resumWrap.classList.remove('d-none');
      $resum.innerHTML = escapeHtml(txt).replaceAll('\n', '<br>');
    } else {
      $resumWrap.classList.add('d-none');
      $resum.textContent = '';
    }
  }

  if ($notesWrap && $notes) {
    const txt = (t.notes || '').trim();
    if (txt) {
      $notesWrap.classList.remove('d-none');
      $notes.innerHTML = escapeHtml(txt).replaceAll('\n', '<br>');
    } else {
      $notesWrap.classList.add('d-none');
      $notes.textContent = '';
    }
  }

  if ($status) $status.textContent = '';
}

export async function initPublicAparicioPremsaDetalls(lang: Lang, id: number): Promise<void> {
  const container = document.getElementById('publicAparicioPremsaDetalls') as HTMLDivElement | null;
  if (!container) return;

  const currentLang = lang ?? getCurrentLang('ca');

  // Render base
  container.innerHTML = `
    <div class="row g-4">
      <div class="col-12">
        ${renderSkeleton()}
      </div>
    </div>
  `;

  // Ajusta "tornar" según idioma
  const $btnBack = document.getElementById('apBtnBack') as HTMLAnchorElement | null;
  if ($btnBack) setBtnHrefForLang($btnBack, currentLang, '/espai-virtual/premsa');

  const $status = document.getElementById('apStatus');

  if (!id || id <= 0) {
    if ($status) $status.textContent = 'Identificador no vàlid.';
    return;
  }

  let detalls: PremsaAparicioDetalls;
  try {
    detalls = await fetchAparicioDetalls(id);
  } catch (e) {
    if ($status) $status.textContent = "No s'han pogut carregar els detalls.";
    console.log(e);
    return;
  }

  renderDetalls(detalls, currentLang);
}
