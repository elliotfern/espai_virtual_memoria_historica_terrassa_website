// src/pages/fitxa/tabs/tabAdjunts.ts
import { Fitxa } from '../../types/types';

const JPG_MIME = 'image/jpeg';
const PDF_MIME = 'application/pdf';
const MAX_BYTES = 3 * 1024 * 1024; // 3 MB
const API_UPLOAD = '/api/aux_imatges/upload'; // ajusta ruta real si es distinta

type UploadResponse = {
  status: 'ok' | 'error';
  message?: string;
  data?: {
    id: number;
    url: string;
    filename: string;
    mime?: string;
    tipus: number;
  };
};

function showOk(box: HTMLDivElement | null, text: HTMLDivElement | null, msg: string): void {
  if (!box || !text) return;
  text.textContent = msg;
  box.classList.remove('d-none');
}

function hideOk(box: HTMLDivElement | null, text: HTMLDivElement | null): void {
  if (!box || !text) return;
  text.textContent = '';
  box.classList.add('d-none');
}

function showErr(box: HTMLDivElement | null, text: HTMLDivElement | null, msg: string): void {
  if (!box || !text) return;
  text.textContent = msg;
  box.classList.remove('d-none');
}

function hideError(box: HTMLDivElement | null, text: HTMLDivElement | null): void {
  if (!box || !text) return;
  text.textContent = '';
  box.classList.add('d-none');
}

export function tab11Adjunts(containerId: string, fitxa?: Fitxa | null): void {
  const container = document.getElementById(containerId);
  if (!container) return;

  const titleName = getNomComplet(fitxa);

  const adjunts = (fitxa?.adjunts ?? []).filter((a) => a && typeof a.id === 'number');

  container.innerHTML = tab11({ titleName, adjunts });

  // preview
  wirePreviewAdjunt();

  // subir archivos
  wireUploadAdjunt(fitxa?.id ?? null);
}

type AdjuntLite = {
  id: number;
  url: string;
  filename: string;
  mime?: string;
  tipus?: number;
};

/** Card bàsica per a adjunts (JPG/PDF) */
function tab11(args: { titleName: string; adjunts: AdjuntLite[] }): string {
  const { titleName, adjunts } = args;

  const hasAny = adjunts.length > 0;

  return `
    <div class="card shadow-sm border-0">
      <div class="card-body">
        <h5 class="card-title mb-3">
          Galeria d'adjunts${titleName ? ` de ${titleName}` : ''}
        </h5>

        <!-- Aquí mostrarem la galeria (imatges + PDFs) -->
        <div id="galeriaWrapper">
          ${
            hasAny
              ? renderGalleryHtml(adjunts)
              : `<p class="text-muted mb-3">
                   Encara no hi ha fitxers a la galeria.
                 </p>`
          }
        </div>

        <!-- Input hidden on guardarem els IDs dels fitxers (separats per comes) -->
        <input type="hidden" id="adjuntsHidden" value="${hasAny ? adjunts.map((a) => a.id).join(',') : ''}">

        <!-- Caixa de pujada d'un sol fitxer (JPG o PDF) -->
        <div id="adjuntUploadBox" class="container"
             style="margin-bottom:12px;border:1px solid #ced4da;border-radius:10px;padding:16px;background-color:#f8f9fa">
          <div class="row g-3">
            <div class="col-12 d-flex align-items-center justify-content-between">
              <h6 class="mb-2" id="formTitleAdjunt">Afegir fitxer a la galeria</h6>
            </div>

            <!-- Missatges OK / Error -->
            <div class="alert alert-success d-none" role="alert" id="okMessageAdjunt" aria-live="polite">
              <div id="okTextAdjunt"></div>
            </div>
            <div class="alert alert-danger d-none" role="alert" id="errMessageAdjunt" aria-live="polite">
              <div id="errTextAdjunt"></div>
            </div>

            <!-- Nom lògic del fitxer -->
            <div class="col-md-4">
              <label for="nomAdjunt" class="form-label">Nom fitxer</label>
              <input type="text" class="form-control" id="nomAdjunt"
                     maxlength="120" placeholder="p. ex., retrat_extra_001">
            </div>

            <!-- Fitxer (JPG o PDF) -->
            <div class="col-md-5">
              <label for="arxiuAdjunt" class="form-label">
                Selecciona el fitxer (JPG o PDF, ≤ 3 MB)
              </label>
              <input class="form-control" type="file" id="arxiuAdjunt"
                     accept=".jpg,.jpeg,application/pdf">
              <div class="form-text">
                Només es permeten fitxers JPG o PDF. Mida màxima: 3 MB.
              </div>
            </div>

            <!-- Botó d'afegir -->
            <div class="col-md-3 d-flex align-items-end">
              <button class="btn btn-success w-100" id="btnAfegirAdjunt" type="button">
                Afegir fitxer
              </button>
            </div>

            <!-- Vista prèvia -->
            <div class="col-12">
              <img id="previewAdjunt" class="img-thumbnail d-none mt-2" alt="Vista prèvia"
                   style="max-height:220px;object-fit:cover">
              <small id="previewAdjuntInfo" class="text-muted d-none mt-1"></small>
            </div>
          </div>

          <small class="text-muted d-block mt-2">
            Els fitxers pujats <strong>encara no estan vinculats</strong> a la fitxa.
            Es vincularan quan premis “Guardar fitxa”.
          </small>
        </div>
      </div>
    </div>
  `;
}

function renderGalleryHtml(adjunts: AdjuntLite[]): string {
  const items = adjunts
    .map((a) => {
      const mime = (a.mime || '').toLowerCase();
      const filename = (a.filename || '').toLowerCase();

      const isImage = mime.startsWith('image/') || filename.endsWith('.jpg') || filename.endsWith('.jpeg');

      const mediaHtml = isImage
        ? `<img src="${a.url}" class="img-fluid rounded"
                 style="object-fit:cover;max-height:180px;" alt="${a.filename}">`
        : `<a href="${a.url}" target="_blank" rel="noopener"
               class="btn btn-outline-danger w-100 mt-4 mb-4">
             📄 Obrir PDF
           </a>`;

      return `
        <div class="col" data-id="${a.id}">
          <div class="card h-100 border-0">
            ${mediaHtml}
            <div class="d-flex justify-content-between align-items-center mt-2">
              <small class="text-muted text-truncate" title="${a.filename}">
                ${a.filename}
              </small>
              <button class="btn btn-sm btn-outline-danger" data-delete="${a.id}">
                Eliminar
              </button>
            </div>
          </div>
        </div>
      `;
    })
    .join('');

  return `
    <div class="row row-cols-2 row-cols-md-3 row-cols-lg-4 g-3">
      ${items}
    </div>
  `;
}

/** Reutilitzo la mateixa funció que tens al teu codi original */
function getNomComplet(fitxa?: Fitxa | null): string {
  const parts = [fitxa?.nom, fitxa?.cognom1, fitxa?.cognom2].filter(Boolean) as string[];
  return parts.join(' ').trim() || (fitxa?.id ? `ID ${fitxa.id}` : 'Nova fitxa');
}

function wirePreviewAdjunt(): void {
  const input = document.getElementById('arxiuAdjunt') as HTMLInputElement | null;
  const previewImg = document.getElementById('previewAdjunt') as HTMLImageElement | null;
  const info = document.getElementById('previewAdjuntInfo') as HTMLSpanElement | null;
  const errBox = document.getElementById('errMessageAdjunt') as HTMLDivElement | null;
  const errText = document.getElementById('errTextAdjunt') as HTMLDivElement | null;

  if (!input || !previewImg || !info) return;

  input.addEventListener('change', () => {
    // Limpiar mensajes
    hideError(errBox, errText);
    previewImg.classList.add('d-none');
    info.classList.add('d-none');

    const file = input.files?.item(0) ?? null;
    if (!file) return;

    // Validar tipo
    if (file.type !== JPG_MIME && file.type !== PDF_MIME) {
      input.value = '';
      return showErr(errBox, errText, 'Només es permeten fitxers JPG o PDF.');
    }

    // Validar tamaño
    if (file.size > MAX_BYTES) {
      input.value = '';
      return showErr(errBox, errText, 'El fitxer supera els 3 MB.');
    }

    // JPG → preview visual
    if (file.type === JPG_MIME) {
      previewImg.src = URL.createObjectURL(file);
      previewImg.classList.remove('d-none');
      info.textContent = '';
      info.classList.add('d-none');
    }

    // PDF → solo texto informativo
    if (file.type === PDF_MIME) {
      previewImg.src = '';
      previewImg.classList.add('d-none');
      info.textContent = 'S’ha seleccionat un PDF (no hi ha vista prèvia).';
      info.classList.remove('d-none');
    }
  });
}

function wireUploadAdjunt(idPersona: number | null): void {
  const btn = document.getElementById('btnAfegirAdjunt') as HTMLButtonElement | null;
  const inputFile = document.getElementById('arxiuAdjunt') as HTMLInputElement | null;
  const inputName = document.getElementById('nomAdjunt') as HTMLInputElement | null;

  const okBox = document.getElementById('okMessageAdjunt') as HTMLDivElement | null;
  const okText = document.getElementById('okTextAdjunt') as HTMLDivElement | null;
  const errBox = document.getElementById('errMessageAdjunt') as HTMLDivElement | null;
  const errText = document.getElementById('errTextAdjunt') as HTMLDivElement | null;

  const hidden = document.getElementById('adjuntsHidden') as HTMLInputElement | null;

  if (!btn || !inputFile || !inputName || !hidden) return;

  btn.addEventListener('click', async () => {
    hideError(errBox, errText);

    const name = inputName.value.trim();
    const file = inputFile.files?.item(0) ?? null;

    if (!name) return showErr(errBox, errText, 'Indica un nom per al fitxer.');
    if (!file) return showErr(errBox, errText, 'Selecciona un fitxer.');

    if (file.type !== JPG_MIME && file.type !== PDF_MIME) return showErr(errBox, errText, 'Només es permeten fitxers JPG o PDF.');

    if (file.size > MAX_BYTES) return showErr(errBox, errText, 'El fitxer supera els 3 MB.');

    btn.disabled = true;
    btn.textContent = 'Pujant…';

    try {
      const fd = new FormData();
      fd.append('nomImatge', name);
      fd.append('nomArxiu', file);
      fd.append('tipus', '3'); // 1 = imagen / 2 - avatar usuari / 3 - galeria multimedia
      if (idPersona) fd.append('idPersona', String(idPersona));

      const res = await fetch(API_UPLOAD, { method: 'POST', body: fd });
      const json = (await res.json()) as UploadResponse;

      if (!res.ok || json.status !== 'ok' || !json.data) {
        throw new Error(json.message || 'Error pujant el fitxer.');
      }

      // ✅ Añadir a la galería
      addAdjuntToGallery(json.data);

      // ✅ Guardar ID en hidden
      const ids = hidden.value ? hidden.value.split(',').map(Number) : [];
      ids.push(json.data.id);
      hidden.value = ids.join(',');

      // ✅ Mensaje OK
      showOk(okBox, okText, 'Fitxer afegit correctament a la galeria.');

      // ✅ Limpieza
      inputName.value = '';
      inputFile.value = '';
      resetPreview();
    } catch (e: unknown) {
      const msg = e instanceof Error ? e.message : 'Error inesperat.';
      hideOk(okBox, okText);
      showErr(errBox, errText, msg);
    } finally {
      btn.disabled = false;
      btn.textContent = 'Afegir fitxer';
    }
  });
}

function addAdjuntToGallery(data: UploadResponse['data']): void {
  if (!data) return;

  const wrapper = document.getElementById('galeriaWrapper') as HTMLDivElement | null;
  if (!wrapper) return;

  const mime = (data.mime || '').toLowerCase();
  const filename = (data.filename || '').toLowerCase();

  const isImage = mime.startsWith('image/') || filename.endsWith('.jpg') || filename.endsWith('.jpeg');

  const card = document.createElement('div');
  card.className = 'col';
  card.dataset.id = String(data.id);

  card.innerHTML = `
    <div class="card h-100 border-0">

      ${isImage ? `<img src="${data.url}" class="img-fluid rounded" style="object-fit:cover;max-height:180px;">` : `<a href="${data.url}" target="_blank" class="btn btn-outline-danger w-100">📄 Obrir PDF</a>`}

      <div class="d-flex justify-content-between align-items-center mt-2">
        <small class="text-muted text-truncate" title="${data.filename}">
          ${data.filename}
        </small>
        <button class="btn btn-sm btn-outline-danger" data-delete="${data.id}">Eliminar</button>
      </div>

    </div>
  `;

  let grid = wrapper.querySelector('.row') as HTMLDivElement | null;
  if (!grid) {
    grid = document.createElement('div');
    grid.className = 'row row-cols-2 row-cols-md-3 row-cols-lg-4 g-3';
    wrapper.innerHTML = '';
    wrapper.appendChild(grid);
  }

  grid.appendChild(card);
}

function resetPreview(): void {
  const preview = document.getElementById('previewAdjunt') as HTMLImageElement | null;
  const info = document.getElementById('previewAdjuntInfo') as HTMLSpanElement | null;

  if (preview) {
    preview.src = '';
    preview.classList.add('d-none');
  }
  if (info) {
    info.textContent = '';
    info.classList.add('d-none');
  }
}
