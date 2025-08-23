// src/pages/fitxa/tabs/tab10.ts
import { Fitxa } from '../../types/types';
// src/pages/fitxa/tabs/tab10.ts

type UploadResponse = {
  status: 'ok' | 'error';
  message?: string;
  data?: { id: number; url?: string; filename?: string };
};

const API_UPLOAD = `https://${window.location.hostname}/api/aux_imatges/upload`;
const IMATGE_URL = `https://memoriaterrassa.cat/public/img/represaliats/`;
const MAX_BYTES = 3 * 1024 * 1024; // <- 3 MB
const JPG_MIME = 'image/jpeg'; // <- solo JPG

// ===== API =====
export function tab10(containerId: string, fitxa?: Fitxa | null): void {
  const container = document.getElementById(containerId);
  if (!container) return;

  // hidden global que viaja al guardar la ficha
  const hidden = document.querySelector<HTMLInputElement>('#imatgePerfilHidden') || null;
  if (hidden && !hidden.value && fitxa?.imatgePerfil && fitxa.imatgePerfil > 0) {
    hidden.value = String(fitxa.imatgePerfil);
  }

  // Render
  const titleName = getNomComplet(fitxa);
  const displayUrl = getDisplayUrlFromFitxa(fitxa);
  container.innerHTML = renderCard({ titleName, displayUrl, fitxa });

  // Wiring
  const nodes = queryNodes(container);
  wirePreview(nodes.fileInput, nodes.preview);
  wireUpload({
    btn: nodes.btn,
    nomImatge: nodes.nomImatge,
    fileInput: nodes.fileInput,
    okBox: nodes.okBox,
    okText: nodes.okText,
    errBox: nodes.errBox,
    errText: nodes.errText,
    formTitle: nodes.formTitle,
    imgWrapper: nodes.imgWrapper,
    hidden,
    titleName,
    idPersona: typeof fitxa?.id === 'number' ? fitxa!.id! : null, // <- mandamos idPersona
  });
}

/* ---------- Render ---------- */
function renderCard(args: { titleName: string; displayUrl: string | null; fitxa?: Fitxa | null }): string {
  const { titleName, displayUrl, fitxa } = args;
  const hasImage = !!displayUrl;
  const buttonClass = hasImage ? 'btn-primary' : 'btn-success';
  const buttonText = hasImage ? 'Substituir imatge' : 'Pujar imatge';
  const placeholder = `retrat_${fitxa?.id ?? 'nou'}`;

  return `
    <div class="card shadow-sm border-0">
      <div class="card-body">
        <h5 class="card-title mb-3">Imatge de perfil${hasImage ? ` de ${titleName}` : ''}</h5>

        <div class="mb-3" id="imgWrapper">
          ${
            hasImage
              ? `<img id="perfilImg" src="${IMATGE_URL}${displayUrl!}.jpg" alt="Imatge de perfil de ${titleName}"
                     class="img-fluid rounded" style="max-height:360px;object-fit:cover" loading="lazy">`
              : `<p class="text-muted mb-0">Cap imatge associada encara.</p>`
          }
        </div>

        <!-- NO FORM aquí (evitamos formularios anidados) -->
        <div id="imatgePerfilBox" class="container" style="margin-bottom:12px;border:1px solid #ced4da;border-radius:10px;padding:16px;background-color:#f8f9fa">
          <div class="row g-3">
            <div class="col-12">
              <h6 class="mb-2" id="formTitle">${buttonText}</h6>
            </div>

            <div class="alert alert-success d-none" role="alert" id="okMessage">
              <div id="okText"></div>
            </div>
            <div class="alert alert-danger d-none" role="alert" id="errMessage">
              <div id="errText"></div>
            </div>

            <input type="hidden" id="tipusHidden" value="1">

            <div class="col-md-4">
              <label for="nomImatge" class="form-label">Nom imatge</label>
              <input type="text" class="form-control" id="nomImatge" maxlength="120" placeholder="p. ex., ${placeholder}">
              <div class="invalid-feedback">Indica un nom per a la imatge.</div>
            </div>

            <div class="col-md-5">
              <label for="nomArxiu" class="form-label">Selecciona la imatge</label>
              <input class="form-control" type="file" id="nomArxiu"  accept="image/jpeg">
              <div class="form-text">Formats: JPG</div>
              <div class="invalid-feedback">Puja un fitxer d'imatge.</div>
            </div>

            <div class="col-md-3 d-flex align-items-end">
              <button class="btn ${buttonClass} w-100" id="btnImatgePerfil" type="button">
                ${buttonText}
              </button>
            </div>

            <div class="col-12">
              <img id="previewImagen" class="img-thumbnail d-none mt-2" alt="Vista prèvia"
                   style="max-height:220px;object-fit:cover">
            </div>
          </div>
          <small class="text-muted d-block mt-2">
            La imatge pujada <strong>encara no està vinculada</strong> a la fitxa.
            Es vincularà quan premis “Guardar fitxa”.
          </small>
        </div>
      </div>
    </div>
  `;
}

/* ---------- Query helpers ---------- */
function queryNodes(root: HTMLElement) {
  return {
    btn: root.querySelector<HTMLButtonElement>('#btnImatgePerfil'),
    okBox: root.querySelector<HTMLDivElement>('#okMessage'),
    okText: root.querySelector<HTMLDivElement>('#okText'),
    errBox: root.querySelector<HTMLDivElement>('#errMessage'),
    errText: root.querySelector<HTMLDivElement>('#errText'),
    nomImatge: root.querySelector<HTMLInputElement>('#nomImatge'),
    fileInput: root.querySelector<HTMLInputElement>('#nomArxiu'),
    preview: root.querySelector<HTMLImageElement>('#previewImagen'),
    imgWrapper: root.querySelector<HTMLDivElement>('#imgWrapper'),
    formTitle: root.querySelector<HTMLHeadingElement>('#formTitle'),
  };
}

/* ---------- Preview ---------- */
function wirePreview(fileInput: HTMLInputElement | null, preview: HTMLImageElement | null): void {
  if (!fileInput || !preview) return;
  fileInput.addEventListener('change', () => {
    const file: File | null = fileInput.files?.item(0) ?? null;
    if (file) {
      preview.src = URL.createObjectURL(file);
      preview.classList.remove('d-none');
    } else {
      preview.src = '';
      preview.classList.add('d-none');
    }
  });

  // Enter en inputs no debe enviar el form maestro
  fileInput.addEventListener('keydown', (e) => {
    if (e.key === 'Enter') e.preventDefault();
  });
}

/* ---------- Upload (click, no submit) ---------- */
function wireUpload(ctx: {
  btn: HTMLButtonElement | null;
  nomImatge: HTMLInputElement | null;
  fileInput: HTMLInputElement | null;
  okBox: HTMLDivElement | null;
  okText: HTMLDivElement | null;
  errBox: HTMLDivElement | null;
  errText: HTMLDivElement | null;
  formTitle: HTMLHeadingElement | null;
  imgWrapper: HTMLDivElement | null;
  hidden: HTMLInputElement | null; // #imatgePerfilHidden (form maestro)
  titleName: string;
  idPersona: number | null; // <- NUEVO: lo mandamos al backend
}): void {
  const { btn, nomImatge, fileInput } = ctx;
  if (!btn || !nomImatge || !fileInput) return;

  // Evita que Enter en el campo texto envíe el form maestro
  nomImatge.addEventListener('keydown', (e) => {
    if (e.key === 'Enter') e.preventDefault();
  });

  btn.addEventListener('click', async () => {
    // Validación mínima
    const nameVal = (nomImatge.value || '').trim();
    const file: File | null = fileInput.files?.item(0) ?? null;

    // Validaciones front: nombre, archivo, tipo y tamaño
    if (!nameVal) return showErr(ctx.errBox, ctx.errText, 'Indica un nom per a la imatge.');
    if (!file) return showErr(ctx.errBox, ctx.errText, 'Selecciona un fitxer d’imatge.');
    if (file.type !== JPG_MIME) {
      return showErr(ctx.errBox, ctx.errText, 'Només es permet JPG (.jpg).');
    }
    if (file.size > MAX_BYTES) {
      return showErr(ctx.errBox, ctx.errText, 'La imatge supera 3 MB.');
    }

    setBusy(btn, true);

    try {
      // Montamos FormData manualmente
      const fd = new FormData();
      fd.append('nomImatge', nameVal);
      fd.append('nomArxiu', file);
      fd.append('tipus', '1');
      if (ctx.idPersona && ctx.idPersona > 0) {
        fd.append('idPersona', String(ctx.idPersona)); // <- enviar idPersona
      }

      const res = await fetch(API_UPLOAD, { method: 'POST', body: fd });
      const json = (await res.json()) as UploadResponse;

      if (!res.ok || json.status !== 'ok' || !json.data || typeof json.data.id !== 'number') {
        throw new Error(json.message && json.message.length > 0 ? json.message : 'Error pujant la imatge');
      }

      // Guardamos ID en el hidden global del form maestro
      if (ctx.hidden) ctx.hidden.value = String(json.data.id);

      // Actualizamos preview con la URL devuelta
      if (json.data.url && json.data.url.length > 0) {
        updateImagePreview(ctx.imgWrapper, ctx.titleName, `${json.data.url}?ts=${Date.now()}`);
        switchToReplaceMode(btn, ctx.formTitle);
      }

      hideError(ctx.errBox, ctx.errText);
      showOk(ctx.okBox, ctx.okText, 'Imatge pujada. Es vincularà en guardar la fitxa.');

      // Limpiamos inputs
      nomImatge.value = '';
      fileInput.value = '';
      const preview = document.querySelector<HTMLImageElement>('#previewImagen');
      if (preview) preview.classList.add('d-none');
    } catch (err: unknown) {
      const msg = err instanceof Error ? err.message : 'Hi ha hagut un error pujant la imatge.';
      hideOk(ctx.okBox, ctx.okText);
      showErr(ctx.errBox, ctx.errText, msg);
    } finally {
      setBusy(btn, false);
    }
  });
}

/* ---------- UI helpers ---------- */
function updateImagePreview(wrapper: HTMLDivElement | null, altName: string, url: string): void {
  if (!wrapper) return;
  const existing = wrapper.querySelector<HTMLImageElement>('#perfilImg');
  if (existing) existing.src = url;
  else {
    const img = document.createElement('img');
    img.id = 'perfilImg';
    img.src = url;
    img.alt = `Imatge de perfil de ${altName}`;
    img.className = 'img-fluid rounded';
    img.style.maxHeight = '360px';
    img.style.objectFit = 'cover';
    img.loading = 'lazy';
    wrapper.innerHTML = '';
    wrapper.appendChild(img);
  }
}

function switchToReplaceMode(btn: HTMLButtonElement | null, title: HTMLHeadingElement | null): void {
  if (btn) {
    btn.classList.remove('btn-success');
    btn.classList.add('btn-primary');
    btn.textContent = 'Substituir imatge';
  }
  if (title) title.textContent = 'Substituir imatge';
}

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

function setBusy(btn: HTMLButtonElement | null, busy: boolean): void {
  if (!btn) return;
  btn.disabled = busy;
  btn.innerHTML = busy ? `<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Pujant…` : btn.textContent || 'Pujar imatge';
}

/* ---------- Utils ---------- */
function getNomComplet(fitxa?: Fitxa | null): string {
  const parts = [fitxa?.nom, fitxa?.cognom1, fitxa?.cognom2].filter(Boolean) as string[];
  return parts.join(' ').trim() || (fitxa?.id ? `ID ${fitxa.id}` : 'Nova fitxa');
}

function getDisplayUrlFromFitxa(fitxa?: Fitxa | null): string | null {
  const url = (fitxa?.img ?? '').trim();
  return url.length > 0 ? url : null;
}
