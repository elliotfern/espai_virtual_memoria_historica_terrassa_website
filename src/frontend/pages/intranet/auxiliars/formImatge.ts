import { auxiliarSelect } from '../../../services/fetchData/auxiliarSelect';
import { API_URLS } from '../../../services/api/ApiUrls';
import { fetchDataGet } from '../../../services/fetchData/fetchDataGet';
import { renderFormInputs } from '../../../services/fetchData/renderInputsForm';

interface FitxaImatge {
  [key: string]: unknown;

  id: number;
  idPersona: number | null;
  nomArxiu: string;
  nomImatge: string;
  tipus: number;
  mime: string | null;
  dateCreated: string;
  dateModified: string | null;
}

interface ApiResponse<T> {
  status: string;
  message: string;
  data: T;
}

function showOk(msg: string): void {
  const okMessage = document.getElementById('okMessage') as HTMLDivElement | null;
  const okText = document.getElementById('okText') as HTMLDivElement | null;
  const errMessage = document.getElementById('errMessage') as HTMLDivElement | null;

  if (!okMessage || !okText) return;

  if (errMessage) errMessage.style.display = 'none';
  okText.textContent = msg;
  okMessage.style.display = 'block';
}

function showErr(msg: string): void {
  const errMessage = document.getElementById('errMessage') as HTMLDivElement | null;
  const errText = document.getElementById('errText') as HTMLDivElement | null;
  const okMessage = document.getElementById('okMessage') as HTMLDivElement | null;

  if (!errMessage || !errText) return;

  if (okMessage) okMessage.style.display = 'none';
  errText.textContent = msg;
  errMessage.style.display = 'block';
}

function clearMessages(): void {
  const okMessage = document.getElementById('okMessage') as HTMLDivElement | null;
  const errMessage = document.getElementById('errMessage') as HTMLDivElement | null;
  if (okMessage) okMessage.style.display = 'none';
  if (errMessage) errMessage.style.display = 'none';
}

function setPreviewFromFile(file: File | null): void {
  const previewInner = document.getElementById('previewInner') as HTMLDivElement | null;
  if (!previewInner) return;

  if (!file) {
    previewInner.innerHTML = '—';
    return;
  }

  if (file.type === 'application/pdf') {
    previewInner.innerHTML = `<span class="badge bg-danger">PDF</span> <span class="small text-muted">${file.name}</span>`;
    return;
  }

  if (file.type === 'image/jpeg' || file.type === 'image/png') {
    const url = URL.createObjectURL(file);
    previewInner.innerHTML = `
      <img
        src="${url}"
        alt="preview"
        style="max-width:140px; max-height:100px; object-fit:cover; border-radius:8px; border:1px solid #ddd;"
      />
      <span class="small text-muted">${file.name}</span>
    `;
    return;
  }

  previewInner.innerHTML = `<span class="badge bg-secondary">Fitxer</span> <span class="small text-muted">${file.name}</span>`;
}

async function submitMultipart(method: 'POST' | 'PUT', form: HTMLFormElement, url: string): Promise<void> {
  const fd = new FormData(form);

  // checkbox / empty selects:
  // - si idPersona viene vacío, lo enviamos como vacío -> backend lo convierte a NULL
  // - si id está vacío en create, se ignora

  const res = await fetch(url, {
    method,
    body: fd,
    credentials: 'include',
  });

  const json = (await res.json()) as { status?: string; message?: string; data?: unknown; errors?: string[] };

  if (!res.ok || json.status === 'error') {
    const msg = json.errors && json.errors.length ? json.errors.join(' · ') : (json.message ?? 'Error');
    throw new Error(msg);
  }
}

export async function formImatge(isUpdate?: boolean, id?: number) {
  const divTitol = document.getElementById('titolForm') as HTMLDivElement | null;
  const btnImatge = document.getElementById('btnImatge') as HTMLButtonElement | null;
  const imatgeForm = document.getElementById('imatgeForm') as HTMLFormElement | null;

  const fileInput = document.getElementById('file') as HTMLInputElement | null;

  if (!divTitol || !btnImatge || !imatgeForm || !fileInput) return;

  clearMessages();

  const realId = id;
  const modeUpdate = Boolean(isUpdate && realId);

  let data: Partial<FitxaImatge> = {
    idPersona: null,
    tipus: 0,
  };

  // Cargar datos si update
  if (modeUpdate && realId) {
    const response = await fetchDataGet<ApiResponse<FitxaImatge[]>>(API_URLS.GET.IMATGE_ID(id), true);
    if (!response || !response.data || !response.data.length) return;

    data = response.data[0]; // 👈 IMPORTANTÍSIMO

    divTitol.innerHTML = `<h3>Modificar imatge #${data.id}</h3>`;
    btnImatge.textContent = 'Guardar canvis';

    // En update: el file es opcional
    fileInput.required = false;

    // Rellenar inputs (id, tipus, idPersona, nomImatge...)
    renderFormInputs(data);

    // Preview: no tenemos URL aquí (porque depende tipo + nomArxiu + mime),
    // lo dejamos para cuando conectemos el helper de URL o lo hacemos después si quieres.
    // De momento mostramos el nombre archivo si existe:
    const previewInner = document.getElementById('previewInner') as HTMLDivElement | null;
    if (previewInner && data.nomArxiu) {
      previewInner.innerHTML = `<span class="badge bg-light text-dark">Actual</span> <span class="small">${String(data.nomArxiu)}</span>`;
    }

    imatgeForm.addEventListener('submit', async (event) => {
      event.preventDefault();
      clearMessages();

      try {
        await submitMultipart('PUT', imatgeForm, API_URLS.PUT.IMATGE);
        showOk('Imatge modificada correctament.');
      } catch (e) {
        showErr(e instanceof Error ? e.message : 'Error');
      }
    });
  } else {
    // CREATE
    divTitol.innerHTML = `<h3>Pujar nova imatge</h3>`;
    btnImatge.textContent = 'Pujar imatge';

    // En create: file obligatorio
    fileInput.required = true;

    imatgeForm.addEventListener('submit', async (event) => {
      event.preventDefault();
      clearMessages();

      try {
        await submitMultipart('POST', imatgeForm, API_URLS.POST.IMATGE);
        showOk('Imatge creada correctament.');
        imatgeForm.reset();
        setPreviewFromFile(null);
      } catch (e) {
        showErr(e instanceof Error ? e.message : 'Error');
      }
    });
  }

  if (data.idPersona) {
    await auxiliarSelect(data.idPersona, 'persones', 'persona', 'idPersona');
  } else {
    // cargar opciones sin preselección (0 te rompe)
    await auxiliarSelect(0, 'persones', 'idPersona', 'nom_complet');
  }

  // Preview file
  fileInput.addEventListener('change', () => {
    const f = fileInput.files && fileInput.files.length ? fileInput.files[0] : null;
    setPreviewFromFile(f);
  });
}
