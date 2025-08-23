// src/pages/fitxa/tabs/tab10.ts
import { Fitxa } from '../../types/types';
// src/pages/fitxa/tabs/tab10.ts

type UploadResponse = {
  status: 'ok' | 'error';
  message?: string;
  data?: { id: number; url?: string; filename?: string };
};

const API_UPLOAD = `https://${window.location.hostname}/api/aux_imatges/upload`;

// ===== API =====
export function tab10(containerId: string, fitxa?: Fitxa | null): void {
  const container = document.getElementById(containerId);
  if (!container) return;

  // 1) Sincroniza el hidden global con el ID que venga de la API (si no hay ya uno)
  const hidden = getHidden();
  if (hidden && !hidden.value && fitxa?.imatgePerfil && fitxa.imatgePerfil > 0) {
    hidden.value = String(fitxa.imatgePerfil);
  }

  // 2) Render
  const displayUrl = getDisplayUrlFromFitxa(fitxa);
  const hasServerImage = !!displayUrl;
  const titleName = getNomComplet(fitxa);
  container.innerHTML = renderCard({ displayUrl, hasServerImage, titleName, fitxa });

  // 3) Wiring
  const nodes = queryNodes(container);
  if (!nodes.form || !nodes.btn) return; // guard

  wirePreview(nodes.fileInput, nodes.preview);
  wireSubmit({
    form: nodes.form,
    btn: nodes.btn,
    okBox: nodes.okBox,
    okText: nodes.okText,
    errBox: nodes.errBox,
    errText: nodes.errText,
    formTitle: nodes.formTitle,
    imgWrapper: nodes.imgWrapper,
    hidden,
    titleName,
  });
}

// ===== Render =====
function renderCard(args: { displayUrl: string | null; hasServerImage: boolean; titleName: string; fitxa?: Fitxa | null }): string {
  const { displayUrl, hasServerImage, titleName, fitxa } = args;
  const titleSuffix = hasServerImage ? ` de ${titleName}` : '';
  const buttonClass = hasServerImage ? 'btn-primary' : 'btn-success';
  const buttonText = hasServerImage ? 'Substituir imatge' : 'Pujar imatge';
  const placeholderName = `retrat_${fitxa?.id ?? 'nou'}`;

  return `
    <div class="card shadow-sm border-0">
      <div class="card-body">
        <h5 class="card-title mb-3">Imatge de perfil${titleSuffix}</h5>

        <div class="mb-3" id="imgWrapper">
          ${
            displayUrl
              ? `<img id="perfilImg"
                       src="${displayUrl}"
                       alt="Imatge de perfil de ${titleName}"
                       class="img-fluid rounded"
                       style="max-height: 360px; object-fit: cover;"
                       loading="lazy" />`
              : `<p class="text-muted mb-0">Cap imatge associada encara.</p>`
          }
        </div>

        <div class="container" style="margin-bottom:12px;border:1px solid #ced4da;border-radius:10px;padding:16px;background-color:#f8f9fa">
          <form id="imatgePerfilForm" enctype="multipart/form-data" novalidate>
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

              <input type="hidden" name="tipus" value="1">

              <div class="col-md-4">
                <label for="nomImatge" class="form-label">Nom imatge</label>
                <input type="text" class="form-control" id="nomImatge" name="nomImatge"
                       required maxlength="120" placeholder="p. ex., ${placeholderName}">
                <div class="invalid-feedback">Indica un nom per a la imatge.</div>
              </div>

              <div class="col-md-5">
                <label for="nomArxiu" class="form-label">Selecciona la imatge</label>
                <input class="form-control" type="file" id="nomArxiu" name="nomArxiu" accept="image/*" required>
                <div class="form-text">Formats: JPG, PNG, WebP.</div>
                <div class="invalid-feedback">Puja un fitxer d'imatge.</div>
              </div>

              <div class="col-md-3 d-flex align-items-end">
                <button class="btn ${buttonClass} w-100" id="btnImatgePerfil" type="submit">
                  ${buttonText}
                </button>
              </div>

              <div class="col-12">
                <img id="previewImagen" class="img-thumbnail d-none mt-2" alt="Vista prèvia"
                     style="max-height: 220px; object-fit: cover;">
              </div>
            </div>
          </form>
          <small class="text-muted d-block mt-2">
            La imatge pujada <strong>encara no està vinculada</strong> a la fitxa.
            Es vincularà quan premis “Guardar fitxa”.
          </small>
        </div>
      </div>
    </div>
  `;
}

// ===== Query helpers =====
function queryNodes(root: HTMLElement) {
  const form = root.querySelector<HTMLFormElement>('#imatgePerfilForm');
  return {
    form,
    btn: root.querySelector<HTMLButtonElement>('#btnImatgePerfil') ?? null,
    okBox: root.querySelector<HTMLDivElement>('#okMessage') ?? null,
    okText: root.querySelector<HTMLDivElement>('#okText') ?? null,
    errBox: root.querySelector<HTMLDivElement>('#errMessage') ?? null,
    errText: root.querySelector<HTMLDivElement>('#errText') ?? null,
    fileInput: form?.querySelector<HTMLInputElement>('#nomArxiu') ?? null,
    preview: root.querySelector<HTMLImageElement>('#previewImagen') ?? null,
    imgWrapper: root.querySelector<HTMLDivElement>('#imgWrapper') ?? null,
    formTitle: root.querySelector<HTMLHeadingElement>('#formTitle') ?? null,
  };
}

// ===== Preview =====
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
}

// ===== Submit =====
function wireSubmit(ctx: { form: HTMLFormElement; btn: HTMLButtonElement | null; okBox: HTMLDivElement | null; okText: HTMLDivElement | null; errBox: HTMLDivElement | null; errText: HTMLDivElement | null; formTitle: HTMLHeadingElement | null; imgWrapper: HTMLDivElement | null; hidden: HTMLInputElement | null; titleName: string }): void {
  const { form, btn } = ctx;
  form.addEventListener('submit', async (e) => {
    e.preventDefault();
    e.stopPropagation();
    if (!form.checkValidity()) {
      form.classList.add('was-validated');
      return;
    }
    setBusy(btn, true);

    try {
      const fd = new FormData(form);
      const res = await fetch(API_UPLOAD, { method: 'POST', body: fd });
      const json = (await res.json()) as UploadResponse;

      if (!res.ok || json.status !== 'ok' || !json.data || typeof json.data.id !== 'number') {
        throw new Error(json.message && json.message.length > 0 ? json.message : 'Error pujant la imatge');
      }

      // Guarda ID en el hidden global (se enviará al guardar la fitxa)
      if (ctx.hidden) ctx.hidden.value = String(json.data.id);

      // Actualiza UI con la URL devuelta (siempre preferible)
      if (json.data.url && json.data.url.length > 0) {
        updateImagePreview(ctx.imgWrapper, ctx.titleName, `${json.data.url}?ts=${Date.now()}`);
        switchToReplaceMode(ctx.btn, ctx.formTitle);
      }

      form.reset();
      form.classList.remove('was-validated');
      hideError(ctx.errBox, ctx.errText);
      showOk(ctx.okBox, ctx.okText, 'Imatge pujada. Es vincularà en guardar la fitxa.');
    } catch (err: unknown) {
      const message = err instanceof Error ? err.message : 'Hi ha hagut un error pujant la imatge.';
      hideOk(ctx.okBox, ctx.okText);
      showErr(ctx.errBox, ctx.errText, message);
    } finally {
      setBusy(btn, false);
    }
  });
}

// ===== UI helpers =====
function updateImagePreview(wrapper: HTMLDivElement | null, altName: string, url: string): void {
  if (!wrapper) return;
  const existing = wrapper.querySelector<HTMLImageElement>('#perfilImg');
  if (existing) {
    existing.src = url;
  } else {
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

// ===== Utils =====
function getHidden(): HTMLInputElement | null {
  return document.querySelector<HTMLInputElement>('#imatgePerfilHidden');
}

function getNomComplet(fitxa?: Fitxa | null): string {
  const parts = [fitxa?.nom, fitxa?.cognom1, fitxa?.cognom2].filter(Boolean) as string[];
  return parts.join(' ').trim() || (fitxa?.id ? `ID ${fitxa.id}` : 'Nova fitxa');
}

/** Autoridad para mostrar imagen: fitxa.img (URL completa). */
function getDisplayUrlFromFitxa(fitxa?: Fitxa | null): string | null {
  const url = (fitxa?.img ?? '').trim();
  return url.length > 0 ? url : null;
}
