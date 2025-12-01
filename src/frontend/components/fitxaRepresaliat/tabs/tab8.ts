// src/pages/fitxaRepresaliat/tabs/tab9.ts
import type { Fitxa } from '../../../types/types';

// Puedes ajustar estos tipos si ya tienes Adjunt tipado en Fitxa
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

  // Si no hi ha adjunts
  if (!adjunts.length) {
    divInfo.innerHTML = `
      <section class="mt-3">
        <h3 class="h5 mb-3">${label}</h3>
        <p class="text-muted mb-0">${texts.empty}</p>
      </section>
    `;
    return;
  }

  // Hi ha adjunts → pintem la galeria
  const itemsHtml = adjunts.map((a) => renderAdjuntCard(a, texts)).join('');

  divInfo.innerHTML = `
    <section class="mt-3">
      <h3 class="h5 mb-3">${label}</h3>
      <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-3">
        ${itemsHtml}
      </div>
    </section>
  `;
}

/** Textos simples per diferent idioma (pots afinar després amb el teu sistema i18n) */
function getMultimediaTexts(lang: string) {
  switch (lang) {
    case 'es':
      return {
        empty: 'Todavía no hay elementos multimedia disponibles.',
        openImage: 'Ver imagen',
        openPdf: 'Abrir documento PDF',
      };
    case 'en':
      return {
        empty: 'No multimedia items are available yet.',
        openImage: 'View image',
        openPdf: 'Open PDF document',
      };
    default: // ca (per defecte)
      return {
        empty: 'Encara no hi ha elements multimèdia disponibles.',
        openImage: 'Veure imatge',
        openPdf: 'Obrir document PDF',
      };
  }
}

/** Pinta una targeta (imatge o PDF) */
function renderAdjuntCard(adj: Adjunt, texts: ReturnType<typeof getMultimediaTexts>): string {
  const mime = (adj.mime || '').toLowerCase();
  const filename = adj.filename || '';
  const url = adj.url;

  const isImage = mime.startsWith('image/') || filename.toLowerCase().endsWith('.jpg') || filename.toLowerCase().endsWith('.jpeg');

  if (isImage) {
    // Targeta per imatge
    return `
      <div class="col">
        <div class="card h-100 border-0 shadow-sm">
          <a href="${url}" target="_blank" rel="noopener" class="text-decoration-none">
            <img src="${url}"
                 class="card-img-top img-fluid"
                 alt="${escapeHtml(filename)}"
                 style="object-fit:cover;max-height:220px;">
          </a>
          <div class="card-body p-2">
            <p class="card-text small mb-1 text-truncate" title="${escapeHtml(filename)}">
              ${escapeHtml(filename)}
            </p>
            <a href="${url}" target="_blank" rel="noopener" class="small">
              ${texts.openImage}
            </a>
          </div>
        </div>
      </div>
    `;
  }

  // Targeta per PDF (o altres)
  return `
    <div class="col">
      <div class="card h-100 border-0 shadow-sm d-flex flex-column justify-content-between">
        <div class="card-body d-flex flex-column justify-content-center text-center">
          <div class="mb-2" style="font-size:2rem;">📄</div>
          <p class="card-text small mb-1 text-truncate" title="${escapeHtml(filename)}">
            ${escapeHtml(filename)}
          </p>
          <a href="${url}" target="_blank" rel="noopener" class="btn btn-outline-primary btn-sm mt-2">
            ${texts.openPdf}
          </a>
        </div>
      </div>
    </div>
  `;
}

/** Petit helper per evitar problemes amb caràcters especials en HTML */
function escapeHtml(str: string): string {
  return str.replace(/&/g, '&amp;').replace(/"/g, '&quot;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
}
