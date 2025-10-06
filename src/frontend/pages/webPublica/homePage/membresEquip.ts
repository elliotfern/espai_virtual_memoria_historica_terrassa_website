import { fetchDataGet } from '../../../services/fetchData/fetchDataGet';

interface UsuariItem {
  nom: string;
  slug: string;
  bio_curta: string; // puede traer <br> puntuales
  urlImatge?: string; // identificador o URL absoluta
  grup: number; // 1,2,3
}

interface ApiResponse<T> {
  status: 'success' | 'error';
  message: string;
  data: T | null;
}

const API_LIST = (lang: string) => `https://memoriaterrassa.cat/api/auth/get/usuarisLlistaWeb?lang=${encodeURIComponent(lang)}`;

const MEDIA_BASE = 'https://media.memoriaterrassa.cat/assets_usuaris/';
const WEB_ASSETS = 'https://media.memoriaterrassa.cat/assets_web/';
const GROUP_TITLES: Record<number, string> = {
  2: 'Membres recerca històrica:',
  1: 'Equip pàgina web:',
  3: 'Col·laboradores introducció i processament de dades:',
};
const GROUP_ORDER: number[] = [2, 1, 3];

function buildUserImgSrc(urlImatge?: string): string {
  if (!urlImatge) return `${MEDIA_BASE}avatar-defecte.jpg`;
  const raw = urlImatge.trim();
  if (/^(https?:)?\/\//i.test(raw)) return raw;
  const safe = raw.replace(/[^a-zA-Z0-9_\-./]/g, '');
  return `${MEDIA_BASE}${encodeURIComponent(safe)}.jpg`;
}

function buildProfileHref(slug: string, lang: string): string {
  const base = '/equip/' + encodeURIComponent(slug);
  return lang ? `/${encodeURIComponent(lang)}${base}` : base;
}

function chunk<T>(arr: T[], size: number): T[][] {
  const out: T[][] = [];
  for (let i = 0; i < arr.length; i += size) out.push(arr.slice(i, i + size));
  return out;
}

function createCard(item: UsuariItem, lang: string, withLeftBorder: boolean): HTMLElement {
  const col = document.createElement('div');
  col.className = `col-md-6 d-flex align-items-center px-3${withLeftBorder ? ' border-start' : ''}`;

  const left = document.createElement('div');
  left.className = 'col-md-6 d-flex flex-column g-3';

  // Título (nombre) — si quieres forzar salto antes del último apellido:
  const h3 = document.createElement('h3');
  h3.className = 'fw-bold lora gran blau1';
  h3.innerHTML = item.nom.replace(/\s+([^\s]+)$/, '<br>$1'); // “Nombre(s) <br> Apellido”
  left.appendChild(h3);

  // Bio curta (puede traer <br>)
  const spanBioCurta = document.createElement('span');
  spanBioCurta.className = 'marro1 lora italic-text';
  spanBioCurta.innerHTML = item.bio_curta || '';
  left.appendChild(spanBioCurta);

  // Botón
  const a = document.createElement('a');
  a.href = buildProfileHref(item.slug, lang);
  a.className = 'btn btn-primary btn-custom-2 w-auto align-self-start';
  a.style.marginTop = '15px';
  a.textContent = 'Veure biografia';
  left.appendChild(a);

  const right = document.createElement('div');
  right.className = 'col-md-4 d-flex align-items-center';

  const imCol = document.createElement('div');
  imCol.className = 'col-md-10';

  const img = document.createElement('img');
  img.src = buildUserImgSrc(item.urlImatge);
  img.className = 'rounded-circle img-petita';
  img.alt = 'Foto';
  img.loading = 'lazy';
  img.decoding = 'async';
  imCol.appendChild(img);

  const decoCol = document.createElement('div');
  decoCol.className = 'col-md-2';

  const deco = document.createElement('img');
  deco.src = WEB_ASSETS + 'vector.png';
  deco.className = 'img-s';
  deco.alt = 'Decoració';
  decoCol.appendChild(deco);

  right.appendChild(imCol);
  right.appendChild(decoCol);

  col.appendChild(left);
  col.appendChild(right);
  return col;
}

function renderGroup(container: HTMLElement, title: string, people: UsuariItem[], lang: string): void {
  if (people.length === 0) return;

  // Wrapper por grupo
  const groupBox = document.createElement('div');
  groupBox.className = 'container my-5';

  const titleSpan = document.createElement('span');
  titleSpan.className = 'titol italic-text gran lora';
  titleSpan.textContent = title;
  groupBox.appendChild(titleSpan);

  // Filas de 2 (col-md-6 cada una)
  const rows = chunk(people, 2);
  rows.forEach((rowItems, rowIndex) => {
    const row = document.createElement('div');
    row.className = 'row mt-4 gy-4 gx-4' + (rowIndex > 0 ? ' border-top' : '');
    rowItems.forEach((item, colIndex) => {
      row.appendChild(createCard(item, lang, colIndex === 1)); // borde en la 2ª columna
    });
    // Si queda 1 sola en la última fila, no pasa nada (ocupa su mitad).
    groupBox.appendChild(row);
  });

  container.appendChild(groupBox);
}

export async function llistatMembresEquip(lang: string): Promise<void> {
  const root = document.getElementById('equipLlistaRoot');
  if (!root) return;

  try {
    const res = await fetchDataGet<ApiResponse<UsuariItem[]>>(API_LIST(lang), true);

    if (!res || res.status !== 'success' || !Array.isArray(res.data)) {
      root.innerHTML = `
        <div class="container my-5">
          <div class="alert alert-danger">No s'ha pogut carregar l'equip.</div>
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
      // Si quieres orden alfabético por nombre dentro del grupo:
      people.sort((a, b) => a.nom.localeCompare(b.nom, 'ca', { sensitivity: 'base' }));
      renderGroup(root, GROUP_TITLES[g], people, lang);
    }
  } catch (err: unknown) {
    root.innerHTML = `
      <div class="container my-5">
        <div class="alert alert-danger">Error de connexió amb la API.</div>
      </div>`;
    console.log(err);
  }
}
