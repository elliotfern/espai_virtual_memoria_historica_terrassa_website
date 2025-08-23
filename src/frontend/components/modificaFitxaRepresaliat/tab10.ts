// src/pages/fitxa/tabs/tab10.ts
import { Fitxa } from '../../types/types';

type UploadResponse = {
  status: 'ok' | 'error';
  message?: string;
  data?: { id: number; url?: string; filename?: string };
};

type ClearResponse = { status: 'ok' | 'error'; message?: string };

const API_UPLOAD = `https://${window.location.hostname}/api/aux_imatges/upload`;
const API_CLEAR = `https://${window.location.hostname}/api/dades_personals/clear-image`;
const IMATGE_URL = `https://memoriaterrassa.cat/public/img/represaliats/`;
const MAX_BYTES = 3 * 1024 * 1024; // 3 MB
const JPG_MIME = 'image/jpeg';

// ===== API =====
export function tab10(containerId: string, fitxa?: Fitxa | null): void {
  const container = document.getElementById(containerId);
  if (!container) return;

  // hidden global que viaja al guardar la ficha (id de imagen)
  const hidden = document.querySelector<HTMLInputElement>('#imatgePerfilHidden') || null;
  if (hidden && !hidden.value && fitxa?.imatgePerfil && fitxa.imatgePerfil > 0) {
    hidden.value = String(fitxa.imatgePerfil);
  }

  const idPersona: number | null = typeof fitxa?.id === 'number' && fitxa.id > 0 ? fitxa.id : null;

  const titleName = getNomComplet(fitxa);
  const displayUrl = getDisplayUrlFromFitxa(fitxa);
  container.innerHTML = renderCard({ titleName, displayUrl, fitxa });

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
    hidden, // aquí vive el idImatge actual
    titleName,
    idPersona, // <- lo pasamos para el bindeo del botón eliminar si se crea dinámicamente
  });

  wireDelete({
    btnDelete: nodes.btnDelete, // puede no existir si no hay imagen al arrancar
    btnUpload: nodes.btn,
    imgWrapper: nodes.imgWrapper,
    okBox: nodes.okBox,
    okText: nodes.okText,
    errBox: nodes.errBox,
    errText: nodes.errText,
    formTitle: nodes.formTitle,
    hidden,
    idPersona, // <- enviamos idPersona al backend
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

        <div id="imatgePerfilBox" class="container" style="margin-bottom:12px;border:1px solid #ced4da;border-radius:10px;padding:16px;background-color:#f8f9fa">
          <div class="row g-3">
            <div class="col-12 d-flex align-items-center justify-content-between">
              <h6 class="mb-2" id="formTitle">${buttonText}</h6>
              ${hasImage ? `<button class="btn btn-outline-danger btn-sm" id="btnEliminarImatge" type="button">Eliminar imatge</button>` : ''}
            </div>

            <div class="alert alert-success d-none" role="alert" id="okMessage" aria-live="polite">
              <div id="okText"></div>
            </div>
            <div class="alert alert-danger d-none" role="alert" id="errMessage" aria-live="polite">
              <div id="errText"></div>
            </div>

            <input type="hidden" id="tipusHidden" value="1">

            <div class="col-md-4">
              <label for="nomImatge" class="form-label">Nom imatge</label>
              <input type="text" class="form-control" id="nomImatge" maxlength="120" placeholder="p. ex., ${placeholder}">
            </div>

            <div class="col-md-5">
              <label for="nomArxiu" class="form-label">Selecciona la imatge (JPG, ≤ 3 MB)</label>
              <input class="form-control" type="file" id="nomArxiu" accept="image/jpeg">
              <div class="form-text">Només es permet JPG. Mida màxima: 3 MB.</div>
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
    btnDelete: root.querySelector<HTMLButtonElement>('#btnEliminarImatge'),
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
  fileInput.addEventListener('keydown', (e) => {
    if (e.key === 'Enter') e.preventDefault();
  });
}

/* ---------- Upload ---------- */
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
  hidden: HTMLInputElement | null; // #imatgePerfilHidden (idImatge actual)
  titleName: string;
  idPersona: number | null; // <- lo necesitaremos al crear el botón eliminar
}): void {
  const { btn, nomImatge, fileInput } = ctx;
  if (!btn || !nomImatge || !fileInput) return;

  nomImatge.addEventListener('keydown', (e) => {
    if (e.key === 'Enter') e.preventDefault();
  });

  btn.addEventListener('click', async () => {
    const nameVal = (nomImatge.value || '').trim();
    const file: File | null = fileInput.files?.item(0) ?? null;

    if (!nameVal) return showErr(ctx.errBox, ctx.errText, 'Indica un nom per a la imatge.');
    if (!file) return showErr(ctx.errBox, ctx.errText, 'Selecciona un fitxer d’imatge.');
    if (file.type !== JPG_MIME) return showErr(ctx.errBox, ctx.errText, 'Només es permet JPG (.jpg).');
    if (file.size > MAX_BYTES) return showErr(ctx.errBox, ctx.errText, 'La imatge supera 3 MB.');

    setBusy(btn, true);

    try {
      const fd = new FormData();
      fd.append('nomImatge', nameVal);
      fd.append('nomArxiu', file);
      fd.append('tipus', '1');

      const res = await fetch(API_UPLOAD, { method: 'POST', body: fd });
      const json = (await res.json()) as UploadResponse;

      if (!res.ok || json.status !== 'ok' || !json.data || typeof json.data.id !== 'number') {
        throw new Error(json.message && json.message.length > 0 ? json.message : 'Error pujant la imatge');
      }

      // Guardar idImatge en el hidden global (lo usará el save de la fitxa)
      if (ctx.hidden) ctx.hidden.value = String(json.data.id);

      // Actualizar preview y modo
      if (json.data.url && json.data.url.length > 0) {
        updateImagePreview(ctx.imgWrapper, ctx.titleName, `${json.data.url}?ts=${Date.now()}`);
        switchToReplaceMode(btn, ctx.formTitle);

        // si no existía botón eliminar, créalo y bindea su click con idPersona
        let del = document.getElementById('btnEliminarImatge') as HTMLButtonElement | null;
        if (!del) {
          const titleBar = document.getElementById('formTitle')?.parentElement;
          if (titleBar) {
            del = document.createElement('button');
            del.id = 'btnEliminarImatge';
            del.type = 'button';
            del.className = 'btn btn-outline-danger btn-sm';
            del.textContent = 'Eliminar imatge';
            titleBar.appendChild(del);

            // si no tenemos idPersona en ctx (alta de ficha), intentamos leerlo del form maestro
            let idPersona = ctx.idPersona;
            if (!idPersona || idPersona <= 0) {
              const idInput = document.querySelector<HTMLInputElement>('[name="id"]');
              const val = (idInput?.value ?? '').trim();
              const n = Number(val);
              idPersona = Number.isFinite(n) && n > 0 ? n : null;
            }

            wireDelete({
              btnDelete: del,
              btnUpload: btn,
              imgWrapper: ctx.imgWrapper,
              okBox: ctx.okBox,
              okText: ctx.okText,
              errBox: ctx.errBox,
              errText: ctx.errText,
              formTitle: ctx.formTitle,
              hidden: ctx.hidden,
              idPersona, // <- importante
            });
          }
        }
      }

      hideError(ctx.errBox, ctx.errText);
      showOk(ctx.okBox, ctx.okText, 'Imatge pujada. Es vincularà en guardar la fitxa.');

      // limpiar inputs
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

/* ---------- Delete (por idPersona) ---------- */
function wireDelete(ctx: {
  btnDelete: HTMLButtonElement | null;
  btnUpload: HTMLButtonElement | null;
  imgWrapper: HTMLDivElement | null;
  okBox: HTMLDivElement | null;
  okText: HTMLDivElement | null;
  errBox: HTMLDivElement | null;
  errText: HTMLDivElement | null;
  formTitle: HTMLHeadingElement | null;
  hidden: HTMLInputElement | null; // limpiamos el idImatge aquí
  idPersona: number | null; // <- enviamos este valor al backend
}): void {
  const b = ctx.btnDelete;
  if (!b) return;

  // evita doble binding
  if (b.dataset.bound === '1') return;
  b.dataset.bound = '1';

  b.addEventListener('click', async () => {
    // idPersona puede venir en ctx; si no, intentamos leer del form maestro
    let idPersona = ctx.idPersona;
    if (!idPersona || idPersona <= 0) {
      const idInput = document.querySelector<HTMLInputElement>('[name="id"]');
      const val = (idInput?.value ?? '').trim();
      const n = Number(val);
      idPersona = Number.isFinite(n) && n > 0 ? n : null;
    }

    if (!idPersona || idPersona <= 0) {
      return showErr(ctx.errBox, ctx.errText, 'No hi ha ID de la fitxa.');
    }

    const ok = window.confirm('Vols eliminar la imatge d’aquesta fitxa?');
    if (!ok) return;

    b.disabled = true;
    try {
      const res = await fetch(API_CLEAR, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ idPersona }), // <- enviamos idPersona
      });
      const json = (await res.json()) as ClearResponse;

      if (!res.ok || json.status !== 'ok') {
        throw new Error(json.message || 'No s’ha pogut eliminar la imatge.');
      }

      // limpiar hidden y UI
      if (ctx.hidden) ctx.hidden.value = '';
      if (ctx.imgWrapper) {
        ctx.imgWrapper.innerHTML = `<p class="text-muted mb-0">Cap imatge associada encara.</p>`;
      }
      if (ctx.btnUpload) {
        ctx.btnUpload.classList.remove('btn-primary');
        ctx.btnUpload.classList.add('btn-success');
        ctx.btnUpload.textContent = 'Pujar imatge';
      }
      if (ctx.formTitle) ctx.formTitle.textContent = 'Pujar imatge';

      // ocultar botón eliminar
      b.remove();

      hideError(ctx.errBox, ctx.errText);
      showOk(ctx.okBox, ctx.okText, 'Imatge eliminada de la fitxa.');
    } catch (e: unknown) {
      const msg = e instanceof Error ? e.message : 'Error eliminant la imatge.';
      hideOk(ctx.okBox, ctx.okText);
      showErr(ctx.errBox, ctx.errText, msg);
    } finally {
      b.disabled = false;
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
