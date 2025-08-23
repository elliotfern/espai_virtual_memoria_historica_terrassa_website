// src/pages/fitxa/tabs/tab10.ts
import { Fitxa } from '../../types/types';

type UploadResponse = {
  status: 'ok' | 'error';
  message?: string;
  data?: { id: number; url?: string; filename?: string };
};

const API_UPLOAD = `https://${window.location.hostname}/api/aux_imatges/upload`;

export function tab10(containerId: string, fitxa?: Fitxa | null): void {
  const container = document.getElementById(containerId);
  if (!container) return;

  const hiddenSelector = '#imatgePerfilHidden';
  const hidden = document.querySelector<HTMLInputElement>(hiddenSelector) || null;

  // Si el hidden global está vacío y la API ya nos da un ID, lo reflejamos
  if (hidden && (!hidden.value || hidden.value.trim().length === 0) && typeof fitxa?.imatgePerfil === 'number' && fitxa.imatgePerfil > 0) {
    hidden.value = String(fitxa.imatgePerfil);
  }

  const nomComplet = [fitxa?.nom, fitxa?.cognom1, fitxa?.cognom2].filter(Boolean).join(' ').trim() || (fitxa?.id ? `ID ${fitxa.id}` : 'Nova fitxa');

  // Autoridad para mostrar imagen: fitxa.img (URL string)
  const serverHasImage: boolean = typeof fitxa?.img === 'string' && !!fitxa.img && fitxa.img.trim().length > 0;
  const imageUrlToShow: string = serverHasImage ? (fitxa!.img as string) : '';

  container.innerHTML = `
    <div class="card shadow-sm border-0">
      <div class="card-body">
        <h5 class="card-title mb-3">Imatge de perfil${serverHasImage ? ` de ${nomComplet}` : ''}</h5>

        <div class="mb-3" id="imgWrapper">
          ${
            serverHasImage
              ? `<img id="perfilImg"
                       src="https://memoriaterrassa.cat/public/img/represaliats/${imageUrlToShow}.jpg"
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
                <h6 class="mb-2" id="formTitle">${serverHasImage ? 'Substituir imatge' : 'Pujar nova imatge'}</h6>
              </div>

              <div class="alert alert-success d-none" role="alert" id="okMessage">
                <div id="okText"></div>
              </div>
              <div class="alert alert-danger d-none" role="alert" id="errMessage">
                <div id="errText"></div>
              </div>

              <!-- Este hidden (id=imatgePerfilHidden, name=imatgePerfil) está en el FORM maestro -->

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
                <button class="btn ${serverHasImage ? 'btn-primary' : 'btn-success'} w-100" id="btnImatgePerfil" type="submit">
                  ${serverHasImage ? 'Substituir imatge' : 'Pujar imatge'}
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

  // ------ wiring (tipado estricto) ------
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
    btn.innerHTML = busy ? `<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Pujant…` : serverHasImage ? 'Substituir imatge' : 'Pujar imatge';
  };

  // Vista prèvia
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

      // 1) Escribimos el ID en el hidden global (form maestro)
      if (hidden) hidden.value = String(json.data.id);

      // 2) Actualizamos la imagen mostrada: si el backend devuelve url, úsala tal cual
      const finalUrl: string = json.data.url && json.data.url.length > 0 ? `${json.data.url}?ts=${Date.now()}` : ''; // si tu backend siempre devuelve url, esto no se usará

      const existingImg = container.querySelector('#perfilImg') as HTMLImageElement | null;
      if (finalUrl) {
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
      }

      // Cambiamos UI a “ya hay imagen” (aunque la ficha aún no esté guardada)
      btn.classList.remove('btn-success');
      btn.classList.add('btn-primary');
      btn.textContent = 'Substituir imatge';
      formTitle.textContent = 'Substituir imatge';

      // Limpiar form y preview
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
