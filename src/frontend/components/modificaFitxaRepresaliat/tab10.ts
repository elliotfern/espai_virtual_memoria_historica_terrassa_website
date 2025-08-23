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

  // Imagen inicial: prioridad hidden → fitxa.img
  const currentImgId: number | null = (hidden?.value && hidden.value.trim() ? Number(hidden.value) : null) ?? (typeof fitxa?.img === 'number' ? fitxa!.img! : null);

  const nomComplet = [fitxa?.nom, fitxa?.cognom1, fitxa?.cognom2].filter(Boolean).join(' ').trim() || (fitxa?.id ? `ID ${fitxa.id}` : 'Nova fitxa');

  const imageUrl = typeof currentImgId === 'number' ? `${IMG_BASE_URL}/${currentImgId}.jpg` : '';
  const hasImage = imageUrl.length > 0;

  container.innerHTML = `
    <div class="card shadow-sm border-0">
      <div class="card-body">
        <h5 class="card-title mb-3">Imatge de perfil${hasImage ? ` de ${nomComplet}` : ''}</h5>

        <div class="mb-3" id="imgWrapper">
          ${
            hasImage
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
                <h6 class="mb-2">${hasImage ? 'Substituir imatge' : 'Pujar nova imatge'}</h6>
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
                <button class="btn ${hasImage ? 'btn-primary' : 'btn-success'} w-100" id="btnImatgePerfil" type="submit">
                  ${hasImage ? 'Substituir' : 'Pujar'}
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

  // ------- wiring fuertemente tipado -------
  const form = container.querySelector('#imatgePerfilForm') as HTMLFormElement;
  const okBox = container.querySelector('#okMessage') as HTMLDivElement;
  const okText = container.querySelector('#okText') as HTMLDivElement;
  const errBox = container.querySelector('#errMessage') as HTMLDivElement;
  const errText = container.querySelector('#errText') as HTMLDivElement;
  const btn = container.querySelector('#btnImatgePerfil') as HTMLButtonElement;
  const fileInput = container.querySelector('#nomArxiu') as HTMLInputElement;
  const preview = container.querySelector('#previewImagen') as HTMLImageElement;
  const imgWrapper = container.querySelector('#imgWrapper') as HTMLDivElement;

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
    btn.innerHTML = busy ? `<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Pujant…` : hasImage ? 'Substituir' : 'Pujar';
  };

  // preview (sin destructuring para evitar el error)
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

      // 1) Escribir el ID en el hidden global (está en el form maestro)
      if (hidden) hidden.value = String(json.data.id);

      // 2) Actualizar la imagen mostrada
      const finalUrl = json.data.url && json.data.url.length > 0 ? `${json.data.url}?ts=${Date.now()}` : `${IMG_BASE_URL}/${json.data.id}.jpg?ts=${Date.now()}`;

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

      // limpiar form/preview
      form.reset();
      form.classList.remove('was-validated');
      preview.classList.add('d-none');

      showOk('Imatge pujada. Es vincularà en guardar la fitxa.');
    } catch (err: unknown) {
      const message = err instanceof Error ? err.message : 'Hi ha hagut un error pujant la imatge.';
      // console.error(err);
      showErr(message);
    } finally {
      setBusy(false);
    }
  });
}
