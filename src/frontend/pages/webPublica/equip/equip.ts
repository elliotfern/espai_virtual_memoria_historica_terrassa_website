import { API_URLS } from '../../../services/api/ApiUrls';
import { fetchDataGet } from '../../../services/fetchData/fetchDataGet';

interface UsuariWeb {
  nom: string;
  slug: string;
  avatar: number | string;
  bio_curta: string;
  bio: string; // HTML (sanitizado)
  urlImatge?: string;
}

interface ApiResponse<T> {
  status: 'success' | 'error';
  message: string;
  data: T | null;
}

const MEDIA_BASE = 'https://media.memoriaterrassa.cat/assets_usuaris/';

function buildUserImgSrc(urlImatge?: unknown): string {
  if (!urlImatge) return '';
  const raw = String(urlImatge).trim();
  if (/^(https?:)?\/\//i.test(raw)) return raw;
  const safe = raw.replace(/[^a-zA-Z0-9_\-./]/g, '');
  return `${MEDIA_BASE}${encodeURIComponent(safe)}.jpg`;
}

function templateReplace(tpl: string, ctx: Record<string, unknown>): string {
  return tpl.replace(/\{([^}]+)\}/g, (_, k) => String(ctx[k] ?? ''));
}

function applyValueToElement(el: HTMLElement, key: string, value: unknown, record: Record<string, unknown>): void {
  if (el instanceof HTMLImageElement) {
    const v = String(value ?? '');
    if (v.startsWith('http') || v.startsWith('/')) {
      el.src = v;
    } else if (el.dataset.srcTemplate) {
      el.src = templateReplace(el.dataset.srcTemplate, record);
    }
    return;
  }

  if (el instanceof HTMLAnchorElement && el.dataset.hrefTemplate) {
    el.href = templateReplace(el.dataset.hrefTemplate, record);
    if (el.dataset.text === '1') el.textContent = String(value ?? '');
    return;
  }

  const wantsHtml = el.dataset.render === 'html' || key === 'bio';
  if (wantsHtml) {
    el.innerHTML = String(value ?? '');
    return;
  }

  el.textContent = typeof value === 'object' ? JSON.stringify(value) : String(value ?? '');
}

function renderByIds(record: Record<string, unknown>): void {
  Object.entries(record).forEach(([key, value]) => {
    const el = document.getElementById(key);
    if (!el) return;
    applyValueToElement(el as HTMLElement, key, value, record);
  });
}

/* ---------- Render 404 ---------- */
function isHTMLElement(n: Element | Node | null): n is HTMLElement {
  return !!n && n instanceof HTMLElement;
}

function safeStr(x: unknown): string {
  return typeof x === 'string' ? x : '';
}

function render404(apiMessage?: unknown): void {
  // Oculta TODO el bloque de contenido normal
  const root = document.getElementById('equipRoot');
  if (isHTMLElement(root)) root.hidden = true;

  // Muestra (y rellena) el bloque de error que está fuera de #equipRoot
  let box = document.getElementById('error404');
  if (!isHTMLElement(box)) {
    box = document.createElement('div');
    box.id = 'error404';
    document.body.appendChild(box);
  }
  box.hidden = false;

  const msg = safeStr(apiMessage) || "La persona sol·licitada no existeix o s'ha mogut.";

  box.innerHTML = `
    <div class="container my-5">
      <div class="alert alert-danger" role="alert">
        <h1 class="h3 mb-3">404 · Fitxa no trobada</h1>
        <p>${msg}</p>
        <p class="mb-0">
          <a href="/equip" class="btn btn-primary">Tornar a l’equip</a>
        </p>
      </div>
    </div>
  `;

  document.title = '404 · No trobat · memoriaterrassa.cat';
}

/* ---------- Main ---------- */
export async function equip(lang: string, slug: string): Promise<void> {
  try {
    const res = await fetchDataGet<ApiResponse<UsuariWeb>>(API_URLS.GET.USUARI_WEB_ID(slug, lang), true);

    // Si la API informa error o no trae datos → 404 en pantalla con el mensaje del backend
    if (!res || res.status !== 'success' || !res.data) {
      render404(res?.message);
      return;
    }

    const data = res.data;

    // Imagen
    const img = document.getElementById('urlImatge') as HTMLImageElement | null;
    if (img) {
      const src = buildUserImgSrc(data.urlImatge);
      if (src) {
        img.alt = data.nom || 'Usuari';
        img.loading = 'lazy';
        img.decoding = 'async';
        img.src = src;
      }
    }

    // Pinta por ids exactos (nom, bio_curta, bio, slug, urlImatge…)
    renderByIds(data as unknown as Record<string, unknown>);
  } catch (err: unknown) {
    // Cualquier error de red/servidor → 404 genérico
    render404();
    console.log(err);
  }
}
