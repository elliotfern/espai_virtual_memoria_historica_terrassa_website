import { API_URLS } from '../../../services/api/ApiUrls';
import { fetchDataGet } from '../../../services/fetchData/fetchDataGet';

interface UsuariWeb {
  nom: string;
  slug: string;
  avatar: number | string;
  bio_curta: string;
  bio: string; // HTML (sanitizado en backend)
  urlImatge?: string; // <- llega en la API
}

interface ApiResponse<T> {
  status: string;
  message: string;
  data: T;
}

const MEDIA_BASE = 'https://media.memoriaterrassa.cat/assets_usuaris/';

function buildUserImgSrc(urlImatge?: unknown): string {
  if (!urlImatge) return '';

  const raw = String(urlImatge).trim();
  // Si ya viene una URL absoluta, úsala tal cual
  if (/^(https?:)?\/\//i.test(raw)) return raw;

  // Sanea el slug/nombre de archivo por seguridad
  const safe = raw.replace(/[^a-zA-Z0-9_\-./]/g, '');
  // Monta la ruta completa (añade .jpg)
  return `${MEDIA_BASE}${encodeURIComponent(safe)}.jpg`;
}

function templateReplace(tpl: string, ctx: Record<string, unknown>): string {
  return tpl.replace(/\{([^}]+)\}/g, (_, k) => String(ctx[k] ?? ''));
}

function applyValueToElement(el: HTMLElement, key: string, value: unknown, record: Record<string, unknown>): void {
  // <img> — si trae URL directa o plantilla
  if (el instanceof HTMLImageElement) {
    const v = String(value ?? '');
    if (v.startsWith('http') || v.startsWith('/')) {
      el.src = v;
    } else if (el.dataset.srcTemplate) {
      el.src = templateReplace(el.dataset.srcTemplate, record);
    } else {
      // si no hay plantilla, no hacemos nada
    }
    return;
  }

  // <a> — href por plantilla si existe
  if (el instanceof HTMLAnchorElement && el.dataset.hrefTemplate) {
    el.href = templateReplace(el.dataset.hrefTemplate, record);
    // texto del link: si quieres usar el valor como texto
    if (el.dataset.text === '1') el.textContent = String(value ?? '');
    return;
  }

  // Contenido HTML seguro
  const wantsHtml = el.dataset.render === 'html' || key === 'bio';
  if (wantsHtml) {
    // Asumimos que ya llega sanitizado desde el backend (sanitizeTrixHtml)
    el.innerHTML = String(value ?? '');
    return;
  }

  // Texto plano por defecto
  el.textContent = typeof value === 'object' ? JSON.stringify(value) : String(value ?? '');
}

function renderByIds(record: Record<string, unknown>): void {
  Object.entries(record).forEach(([key, value]) => {
    const el = document.getElementById(key);
    if (!el) return; // si no hay un id que coincida, lo ignoramos
    applyValueToElement(el as HTMLElement, key, value, record);
  });
}

/**
 * Carga y pinta los datos del usuario en elementos cuyo id coincide con cada campo.
 * - `bio` se inserta como HTML (ya sanitizado en backend).
 * - `avatar` puede resolverse con data-src-template en el <img>.
 * - <a> puede construir href con data-href-template.
 *
 * HTML esperado (ejemplo):
 *   <h1 id="nom"></h1>
 *   <div id="bio_curta"></div>
 *   <div id="bio" data-render="html"></div>
 *   <img id="avatar" data-src-template="/media/avatars/{avatar}.jpg" alt="Avatar">
 *   <a id="perfil" data-href-template="/equip/{slug}" data-text="1"></a>
 */
export async function equip(lang: string, slug: string) {
  const res = await fetchDataGet<ApiResponse<UsuariWeb>>(API_URLS.GET.USUARI_WEB_ID(slug, lang), true);
  if (!res?.data) return;

  const data = res.data;

  const img = document.getElementById('urlImatge') as HTMLImageElement | null;
  if (img) {
    const src = buildUserImgSrc(data.urlImatge);
    if (src) {
      img.alt = data.nom || 'Usuari';
      img.loading = 'lazy';
      img.decoding = 'async';
      img.src = src;

      // (Opcional) Fallback si la imagen falla:
      // const PLACEHOLDER = '/assets/img/user-placeholder.jpg';
      // img.onerror = () => { img.src = PLACEHOLDER; };
    } else {
      // Si no hay imagen, puedes ocultar el <img> o mostrar un placeholder
      // img.style.display = 'none';
    }
  }

  // Pinta por ids exactos (nom, slug, avatar, bio_curta, bio)
  renderByIds(data as unknown as Record<string, unknown>);
}
