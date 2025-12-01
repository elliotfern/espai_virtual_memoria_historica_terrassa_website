// src/pages/fitxaRepresaliat/tabs/tab9.ts
import type { Fitxa } from '../../../types/types';

// Ajusta si ya tienes Adjunt tipat en Fitxa
type Adjunt = {
  id: number;
  url: string;
  filename: string;
  mime?: string;
  tipus?: number;
};

export function renderTab8(fitxa: Fitxa, label: string, lang: string): void {
  const divInfo = document.getElementById('fitxa');
  if (!divInfo) return;

  const adjunts: Adjunt[] = (fitxa.adjunts ?? []).filter((a) => a && typeof a.id === 'number');

  const texts = getMultimediaTexts(lang);

  const images = adjunts.filter((a) => isImageAdj(a));
  const docs = adjunts.filter((a) => !isImageAdj(a));

  // No hay nada
  if (!images.length && !docs.length) {
    divInfo.innerHTML = `
      <section class="mt-3">
        <h3 class="h5 mb-3">${label}</h3>
        <p class="text-muted mb-0">${texts.emptyAll}</p>
      </section>
    `;
    return;
  }

  const imagesHtml = images.length
    ? `
      <section class="mb-4">
        <h3 class="h6 mb-3 titolSeccio">${texts.imagesTitle}</h3>  
        <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 g-3">
          ${images.map((a) => renderImageCard(a, texts)).join('')}
        </div>
      </section>
    `
    : '';

  const docsHtml = docs.length
    ? `
      <section class="mb-4">
        <h4 class="h6 mb-3">${texts.docsTitle}</h4>
        <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 g-3">
          ${docs.map((a) => renderDocCard(a, texts)).join('')}
        </div>
      </section>
    `
    : '';

  divInfo.innerHTML = `
    <section class="mt-3">
      <h3 class="h5 mb-3">${label}</h3>
      ${imagesHtml}
      ${docsHtml}
    </section>
    ${renderLightboxHtml()}
  `;

  wireLightbox(divInfo);
}

/* ---------- i18n simple ---------- */

function getMultimediaTexts(lang: string) {
  switch (lang) {
    case 'es':
      return {
        emptyAll: 'Todavía no hay elementos multimedia disponibles.',
        imagesTitle: 'Galería de imágenes',
        docsTitle: 'Documentos',
        openImage: 'Descargar imagen',
        openPdf: 'Abrir documento PDF',
        badgeJpg: 'JPG',
        badgePdf: 'PDF',
      };
    case 'en':
      return {
        emptyAll: 'No multimedia items are available yet.',
        imagesTitle: 'Images Gallery',
        docsTitle: 'Documents',
        openImage: 'Download image',
        openPdf: 'Open PDF document',
        badgeJpg: 'JPG',
        badgePdf: 'PDF',
      };
    default: // ca
      return {
        emptyAll: 'Encara no hi ha elements multimèdia disponibles.',
        imagesTitle: "Galeria d'imatges",
        docsTitle: 'Documents',
        openImage: 'Descarregar imatge',
        openPdf: 'Obrir document PDF',
        badgeJpg: 'JPG',
        badgePdf: 'PDF',
      };
  }
}

/* ---------- Helpers de tipo ---------- */

function isImageAdj(adj: Adjunt): boolean {
  const mime = (adj.mime || '').toLowerCase();
  const filename = (adj.filename || '').toLowerCase();
  return (
    mime.startsWith('image/') || filename.endsWith('.jpg') || filename.endsWith('.jpeg') || filename.endsWith('.png') // por si algún día
  );
}

function getExtensionLabel(adj: Adjunt): string {
  const mime = (adj.mime || '').toLowerCase();
  const filename = (adj.filename || '').toLowerCase();

  if (mime === 'application/pdf' || filename.endsWith('.pdf')) return 'PDF';
  if (mime.startsWith('image/') || filename.match(/\.(jpe?g|png)$/)) return 'JPG';
  return 'FILE';
}

/* ---------- Render de tarjetas ---------- */

function renderImageCard(adj: Adjunt, texts: ReturnType<typeof getMultimediaTexts>): string {
  const badge = getExtensionLabel(adj) === 'PDF' ? texts.badgePdf : texts.badgeJpg;

  return `
    <div class="col">
      <div class="card h-100 border-0 shadow-sm">
        <a href="${adj.url}"
           class="text-decoration-none js-lightbox-trigger"
           data-lightbox-url="${adj.url}"
           data-lightbox-alt="${escapeHtml(adj.filename)}">
          <img src="${adj.url}"
               class="card-img-top img-fluid"
               alt="${escapeHtml(adj.filename)}"
               style="object-fit:cover;max-height:220px;">
        </a>
        <div class="card-body p-2 d-flex flex-column">
          <div class="d-flex justify-content-between align-items-center mb-1">
            <p class="card-text small mb-1" title="${escapeHtml(adj.filename)}">
              ${escapeHtml(adj.filename)}
            </p>
            <span class="badge bg-secondary ms-2">${badge}</span>
          </div>
          <a href="${adj.url}" target="_blank" rel="noopener" class="small mt-1 align-self-start">
            ${texts.openImage}
          </a>
        </div>
      </div>
    </div>
  `;
}

function renderDocCard(adj: Adjunt, texts: ReturnType<typeof getMultimediaTexts>): string {
  const badge = getExtensionLabel(adj);

  return `
    <div class="col">
      <div class="card h-100 border-0 shadow-sm d-flex flex-column justify-content-between">
        <div class="card-body d-flex flex-column justify-content-center text-center">
          <div class="mb-2" style="font-size:2rem;">📄</div>
          <p class="card-text small mb-1" title="${escapeHtml(adj.filename)}">
            ${escapeHtml(adj.filename)}
          </p>
          <span class="badge bg-secondary mb-2">${badge}</span>
          <a href="${adj.url}" target="_blank" rel="noopener" class="btn btn-outline-primary btn-sm mt-2">
            ${texts.openPdf}
          </a>
        </div>
      </div>
    </div>
  `;
}

/* ---------- Lightbox ---------- */

function renderLightboxHtml(): string {
  return `
    <div id="lightboxOverlay" class="lightbox-overlay d-none" role="dialog" aria-modal="true" aria-label="Image lightbox">
      <div class="lightbox-content">
        <button type="button"
                class="btn btn-light btn-sm lightbox-close-btn"
                data-lightbox-close="1"
                aria-label="Tancar">
          ×
        </button>
        <img src="" alt="">
      </div>
    </div>
  `;
}

function wireLightbox(root: HTMLElement): void {
  const overlay = document.getElementById('lightboxOverlay') as HTMLDivElement | null;
  if (!overlay) return;

  const img = overlay.querySelector('img') as HTMLImageElement | null;
  const closeBtn = overlay.querySelector('[data-lightbox-close="1"]') as HTMLButtonElement | null;

  const open = (url: string, alt: string) => {
    if (!img) return;
    img.src = url;
    img.alt = alt;
    overlay.classList.remove('d-none');
  };

  const close = () => {
    if (!img) return;
    img.src = '';
    overlay.classList.add('d-none');
  };

  // Delegación sobre las tarjetas
  root.addEventListener('click', (ev) => {
    const target = ev.target as HTMLElement;
    const link = target.closest<HTMLAnchorElement>('.js-lightbox-trigger');
    if (!link) return;

    ev.preventDefault();
    const url = link.dataset.lightboxUrl || link.href;
    const alt = link.dataset.lightboxAlt || '';
    open(url, alt);
  });

  // Cerrar por botón
  closeBtn?.addEventListener('click', () => close());

  // Cerrar clicando fuera de la imagen
  overlay.addEventListener('click', (ev) => {
    if (ev.target === overlay) {
      close();
    }
  });

  // Cerrar con ESC
  document.addEventListener('keydown', (ev) => {
    if (ev.key === 'Escape' && !overlay.classList.contains('d-none')) {
      close();
    }
  });
}

/* ---------- Util ---------- */

function escapeHtml(str: string): string {
  return str.replace(/&/g, '&amp;').replace(/"/g, '&quot;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
}
