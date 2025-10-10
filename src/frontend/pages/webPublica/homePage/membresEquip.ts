// modules/equip/llistatMembresEquip.ts
import { fetchDataGet } from '../../../services/fetchData/fetchDataGet';
import { LABELS_EQUIP } from '../../../services/i18n/homePage/equip';
import { DEFAULT_LANG, isLang, t } from '../../../services/i18n/i18n';

interface UsuariItem {
  nom: string;
  slug: string;
  bio_curta: string; // puede traer <br>
  urlImatge?: string;
  grup: number; // 1,2,3
}

interface ApiResponse<T> {
  status: 'success' | 'error';
  message: string;
  data: T | null;
}

type Lang = 'ca' | 'es' | 'en' | 'fr' | 'it' | 'pt';

const API_LIST = (lang: string) => `https://memoriaterrassa.cat/api/auth/get/usuarisLlistaWeb?lang=${encodeURIComponent(lang)}`;

const MEDIA_BASE = 'https://media.memoriaterrassa.cat/assets_usuaris/';
const WEB_ASSETS = 'https://media.memoriaterrassa.cat/assets_web/';

// Orden de grupos: 2 → 1 → 3
const GROUP_ORDER: number[] = [2, 1, 3];

function normLang(lang: string): Lang {
  const l = isLang(lang) ? lang : DEFAULT_LANG;
  return l;
}

function groupTitle(grup: number, lang: string): string {
  const L = normLang(lang);
  if (grup === 2) return t(LABELS_EQUIP, 'groupTitleResearch', L);
  if (grup === 1) return t(LABELS_EQUIP, 'groupTitleWeb', L);
  return t(LABELS_EQUIP, 'groupTitleData', L); // 3 u otros
}

function ctaLabel(lang: string): string {
  const L = normLang(lang);
  return t(LABELS_EQUIP, 'ctaViewBio', L);
}

function altPhoto(lang: string): string {
  const L = normLang(lang);
  return t(LABELS_EQUIP, 'altPhoto', L);
}

function altDecoration(lang: string): string {
  const L = normLang(lang);
  return t(LABELS_EQUIP, 'altDecoration', L);
}

function buildUserImgSrc(urlImatge?: string): string {
  if (!urlImatge) return `${MEDIA_BASE}avatar-defecte.jpg`;
  const raw = urlImatge.trim();
  if (/^(https?:)?\/\//i.test(raw)) return raw;
  const safe = raw.replace(/[^a-zA-Z0-9_\-./]/g, '');
  return `${MEDIA_BASE}${encodeURIComponent(safe)}.jpg`;
}

function buildProfileHref(slug: string, lang: string): string {
  const L = normLang(lang);
  const base = '/equip/' + encodeURIComponent(slug);
  return L === 'ca' ? base : `/${L}${base}`;
}

function chunk<T>(arr: T[], size: number): T[][] {
  const out: T[][] = [];
  for (let i = 0; i < arr.length; i += size) out.push(arr.slice(i, i + size));
  return out;
}

function createCard(item: UsuariItem, lang: string, withLeftBorder: boolean): HTMLElement {
  const L = normLang(lang);
  const col = document.createElement('div');
  col.className = `col-md-6 d-flex align-items-center px-3${withLeftBorder ? ' border-start' : ''}`;

  const left = document.createElement('div');
  left.className = 'col-md-6 d-flex flex-column g-3';

  // Título (nombre) — salto antes del último apellido
  const h3 = document.createElement('h3');
  h3.className = 'fw-bold lora gran blau1';
  h3.innerHTML = item.nom.replace(/\s+([^\s]+)$/, '<br>$1');
  left.appendChild(h3);

  // Bio curta (puede traer <br>)
  const spanBioCurta = document.createElement('span');
  spanBioCurta.className = 'marro1 lora italic-text';
  spanBioCurta.innerHTML = item.bio_curta || '';
  left.appendChild(spanBioCurta);

  // Botón
  const a = document.createElement('a');
  a.href = buildProfileHref(item.slug, L);
  a.className = 'btn btn-primary btn-custom-2 w-auto align-self-start';
  a.style.marginTop = '15px';
  a.textContent = ctaLabel(L);
  left.appendChild(a);

  const right = document.createElement('div');
  right.className = 'col-md-4 d-flex align-items-center';

  const imCol = document.createElement('div');
  imCol.className = 'col-md-10';

  const img = document.createElement('img');
  img.src = buildUserImgSrc(item.urlImatge);
  img.className = 'rounded-circle img-petita';
  img.alt = altPhoto(L);
  img.loading = 'lazy';
  img.decoding = 'async';
  imCol.appendChild(img);

  const decoCol = document.createElement('div');
  decoCol.className = 'col-md-2';

  const deco = document.createElement('img');
  deco.src = WEB_ASSETS + 'vector.png';
  deco.className = 'img-s';
  deco.alt = altDecoration(L);
  decoCol.appendChild(deco);

  right.appendChild(imCol);
  right.appendChild(decoCol);

  col.appendChild(left);
  col.appendChild(right);
  return col;
}

function renderGroup(container: HTMLElement, title: string, people: UsuariItem[], lang: string): void {
  if (people.length === 0) return;

  const L = normLang(lang);
  const groupBox = document.createElement('div');
  groupBox.className = 'container my-5';

  const titleSpan = document.createElement('span');
  titleSpan.className = 'titol italic-text gran lora';
  titleSpan.textContent = title;
  groupBox.appendChild(titleSpan);

  // Orden alfabético por nombre según idioma activo
  const collator = new Intl.Collator(L, { sensitivity: 'base' });
  const sorted = [...people].sort((a, b) => collator.compare(a.nom, b.nom));

  // Filas de 2
  const rows = chunk(sorted, 2);
  rows.forEach((rowItems, rowIndex) => {
    const row = document.createElement('div');
    row.className = 'row mt-4 gy-4 gx-4' + (rowIndex > 0 ? ' border-top' : '');
    rowItems.forEach((item, colIndex) => {
      row.appendChild(createCard(item, L, colIndex === 1));
    });
    groupBox.appendChild(row);
  });

  container.appendChild(groupBox);
}

export async function llistatMembresEquip(lang: string): Promise<void> {
  const root = document.getElementById('equipLlistaRoot');
  if (!root) return;

  const L = normLang(lang);

  try {
    const res = await fetchDataGet<ApiResponse<UsuariItem[]>>(API_LIST(L), true);

    if (!res || res.status !== 'success' || !Array.isArray(res.data)) {
      root.innerHTML = `
        <div class="container my-5">
          <div class="alert alert-danger">${t(LABELS_EQUIP, 'errorLoad', L)}</div>
        </div>`;
      return;
    }

    const items = res.data;

    // Agrupar por grup
    const byGroup = new Map<number, UsuariItem[]>();
    for (const item of items) {
      const g = item.grup;
      if (!byGroup.has(g)) byGroup.set(g, []);
      byGroup.get(g)!.push(item);
    }

    // Pintar en orden 2 → 1 → 3
    root.innerHTML = '';
    for (const g of GROUP_ORDER) {
      const people = byGroup.get(g) ?? [];
      renderGroup(root, groupTitle(g, L), people, L);
    }
  } catch {
    root.innerHTML = `
      <div class="container my-5">
        <div class="alert alert-danger">${t(LABELS_EQUIP, 'errorConn', L)}</div>
      </div>`;
  }
}
