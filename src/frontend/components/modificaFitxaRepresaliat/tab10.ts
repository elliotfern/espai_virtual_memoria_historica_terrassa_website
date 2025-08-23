// src/pages/fitxa/tabs/tab10.ts
import { Fitxa } from '../../types/types';

type UploadResponse = {
  status: 'ok' | 'error';
  message?: string;
  data?: { id: number; url?: string; filename?: string };
};

const IMG_BASE_URL = `https://memoriaterrassa.cat/public/img/represaliats`;
const API_UPLOAD = `https://${window.location.hostname}/api/aux_imatges/upload`;

export function tab10(containerId: string, fitxa?: Fitxa): void {
  const container = document.getElementById(containerId);
  if (!container) return;

  const hiddenSelector = '#imgIdHidden';
  const hidden = document.querySelector<HTMLInputElement>(hiddenSelector) || null;

  // --- 1) Decisiones de estado ---
  const nomComplet = [fitxa?.nom, fitxa?.cognom1, fitxa?.cognom2].filter(Boolean).join(' ').trim() || (fitxa?.id ? `ID ${fitxa.id}` : 'Nova fitxa');

  // a) ID ya subido en esta sesión (hidden) —no está aún en servidor
  const draftImgId: number | null = hidden && hidden.value.trim() ? Number(hidden.value) : null;

  // b) ID existente en servidor, decidido por imatgePerfil (autoridad)
  const serverImageId: number | null = typeof fitxa?.imatgePerfil === 'number' && fitxa.imatgePerfil > 0 ? fitxa.imatgePerfil : typeof fitxa?.img === 'number' && fitxa.img > 0 ? fitxa.img : null;

  // Mostrar: prioriza lo subido en esta sesión; si no, lo del servidor
  const imageIdToShow: number | null = draftImgId ?? serverImageId;
  const imageUrl: string = imageIdToShow !== null ? `${IMG_BASE_URL}/${imageIdToShow}.jpg` : '';

  // Para textos/botón usamos SOLO si el servidor ya tiene imagen
  const hasServerImage: boolean = serverImageId !== null;

  // --- 2) Render ---
  container.innerHTML = `
    <div class="card shadow-sm border-0">
      <div class="card-body">
        <h5 class="card-title mb-3">Imatge de perfil${imageUrl ? ` de ${nomComplet}` : ''}</h5>

        <div class="mb-3" id="imgWrapper">
          ${
            imageUrl
              ? `<img id="perfilImg"
                       src="${imageUrl}"
                       alt="Imatge de perfil de ${nomComplet}"
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
                <h6 class="mb-2" id="formTitle">${hasServerImage ? 'Substituir imatge' : 'Pujar nova imatge'}</h6>
              </div>

              <div class="alert alert-success d-none" role="alert" id="okMessage">
                <div id="okText"></div>
              </div>
              <div class="alert alert-danger d-none" role="alert" id="errMessage">
                <div id="errText"></div>
              </div>

              <!-- Este hidden global NO se crea aquí: está en el formulario maestro con name="img" -->
              <!-- <input type="hidden" id="imgIdHidden" name="img"> -->

              <input type="hidden" name="tipus" value="1">

              <div class="col-md-4">
                <label for="nomImatge" class="form-label">Nom imatge</label>
                <input type="text" class="form-control" id="nomImatge" name="nomImatge"
                       required maxlength="120" placeholder="p. ex., retrat_${fitxa?.id ?? 'nou'}">
                <div class="invalid-feedback">Indica un nom per a la imatge.</div>
              </div>

              <div class="col-md-5">
                <label for="nomArxiu" class="form-label">Selecciona la imatge</label>
                <input class="form-control" type="file" id="nomArxiu" name="nomArxiu" accept="image/*" required>
                <div class="form-text">Formats: JPG, PNG, WebP.</div>
                <div class="invalid-feedback">Puja un fitxer d'imatge.</div>
              </div>

              <div class="col-md-3 d-flex align-items-end">
                <button class="btn ${hasServerImage ? 'btn-primary' : 'btn-success'} w-100" id="btnImatgePerfil" type="submit">
                  ${hasServerImage ? 'Substituir imatge' : 'Pujar imatge'}
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

  // --- 3) Wiring (tipado estricto) ---
  const form = container.querySelector('#imatgePerfilForm') as HTMLFormElement;
  const okBox = container.querySelector('#okMessage') as HTMLDivElement;
  const okText = container.querySelector('#okText') as HTMLDivElement;
  const errBox = container.querySelector('#errMessage') as HTMLDivElement;
  const errText = container.querySelector('#errText') as HTMLDivElement;
  const btn = container.querySelector('#btnImatgePerfil') as HTMLButtonElement;
  const fileInput = container.querySelector('#nomArxiu') as HTMLInputElement;
  const preview = container.querySelector('#previewImagen') as HTMLImageElement;
  const imgWrapper = container.querySelector('#imgWrapper') as HTMLDivElement;
  const formTitle = container.querySelector('#formTitle') as HTMLHeadingElement;

  const showOk = (msg: string): void => {
    okText.textContent = msg;
    okBox.classList.remove('d-none');
    errBox.classList.add('d-none');
  };
  const showErr = (msg: string): void => {
    errText.textContent = msg;
    errBox.classList.remove('d-none');
    okBox.classList.add('d-none');
  };
  const setBusy = (busy: boolean): void => {
    btn.disabled = busy;
    btn.innerHTML = busy ? `<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Pujant…` : hasServerImage ? 'Substituir imatge' : 'Pujar imatge';
  };

  // Vista prèvia (sin destructuring)
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

  form.addEventListener('submit', async (e) => {
    e.preventDefault();
    e.stopPropagation();
    if (!form.checkValidity()) {
      form.classList.add('was-validated');
      return;
    }

    try {
      setBusy(true);
      const fd = new FormData(form); // nomImatge, nomArxiu, tipus=1
      const res = await fetch(API_UPLOAD, { method: 'POST', body: fd });
      const json = (await res.json()) as UploadResponse;

      if (!res.ok || json.status !== 'ok' || !json.data || typeof json.data.id !== 'number') {
        const message = json.message && json.message.length > 0 ? json.message : 'Error pujant la imatge';
        throw new Error(message);
      }

      // Escribimos el ID en el hidden global (se enviará al guardar la fitxa)
      if (hidden) hidden.value = String(json.data.id);

      // Actualizamos la imagen mostrada (prioriza url del backend si viene)
      const finalUrl: string = json.data.url && json.data.url.length > 0 ? `${json.data.url}?ts=${Date.now()}` : `${IMG_BASE_URL}/${json.data.id}.jpg?ts=${Date.now()}`;

      const existingImg = container.querySelector('#perfilImg') as HTMLImageElement | null;
      if (existingImg) {
        existingImg.src = finalUrl;
      } else {
        const img = document.createElement('img');
        img.id = 'perfilImg';
        img.src = finalUrl;
        img.alt = `Imatge de perfil de ${nomComplet}`;
        img.className = 'img-fluid rounded';
        img.style.maxHeight = '360px';
        img.style.objectFit = 'cover';
        img.loading = 'lazy';
        imgWrapper.innerHTML = '';
        imgWrapper.appendChild(img);
      }

      // Tras una subida exitosa, a efectos de UI ya “hay imagen en servidor”
      btn.classList.remove('btn-success');
      btn.classList.add('btn-primary');
      btn.textContent = 'Substituir imatge';
      formTitle.textContent = 'Substituir imatge';

      form.reset();
      form.classList.remove('was-validated');
      preview.classList.add('d-none');

      showOk('Imatge pujada. Es vincularà en guardar la fitxa.');
    } catch (err: unknown) {
      const message = err instanceof Error ? err.message : 'Hi ha hagut un error pujant la imatge.';
      showErr(message);
    } finally {
      setBusy(false);
    }
  });
}
