// src/utils/export.ts
type SelScalar = string | number | boolean;
type SelArray = SelScalar[];
export type Selection = Record<string, SelScalar | SelArray | null | undefined>;

/** Construye <input type="hidden"> y lo añade al form */
function addHidden(form: HTMLFormElement, name: string, value: SelScalar) {
  const inp = document.createElement('input');
  inp.type = 'hidden';
  inp.name = name;
  inp.value = String(value);
  form.appendChild(inp);
}

/** Envia la selección por POST (arrays con name="key[]") */
function postDownload(url: string, selection: Selection) {
  const form = document.createElement('form');
  form.method = 'POST';
  form.action = url;
  form.style.display = 'none';

  // Copiamos solo claves con valor
  Object.entries(selection ?? {}).forEach(([key, val]) => {
    if (val === null || val === undefined) return;
    if (Array.isArray(val)) {
      if (val.length === 0) return;
      val.forEach((v) => addHidden(form, `${key}[]`, v));
    } else {
      addHidden(form, key, val);
    }
  });

  document.body.appendChild(form);
  form.submit();
  form.remove();
}

/** Construye la URL del endpoint según formato */
function buildExportUrl(fmt: 'csv' | 'xlsx'): string {
  const host = `https://${window.location.hostname}`;
  // Usa tus rutas reales (PHP ejecutable)
  return fmt === 'csv' ? `${host}/api/export/persones_csv/ca` : `${host}/api/export/persones_xlsx/ca`;
}

/** API pública: dispara la descarga (POST) */
export function downloadExportFromSelection(selection: Selection, fmt: 'csv' | 'xlsx' = 'csv'): void {
  const url = buildExportUrl(fmt);
  postDownload(url, selection);
}

/** UI helper: inserta los dos botones */
export function mountExportToolbar(container: HTMLElement, getSelection: () => Selection): void {
  if (!container) return;
  container.querySelector('[data-export-toolbar]')?.remove();

  const wrap = document.createElement('div');
  wrap.setAttribute('data-export-toolbar', 'true');
  wrap.className = 'd-flex gap-2 flex-wrap my-2';

  const b1 = document.createElement('button');
  b1.type = 'button';
  b1.className = 'btn btn-outline-primary';
  b1.textContent = 'Exportar CSV';
  b1.addEventListener('click', () => downloadExportFromSelection(getSelection(), 'csv'));

  const b2 = document.createElement('button');
  b2.type = 'button';
  b2.className = 'btn btn-outline-success';
  b2.textContent = 'Exportar Excel';
  b2.addEventListener('click', () => downloadExportFromSelection(getSelection(), 'xlsx'));

  wrap.appendChild(b1);
  wrap.appendChild(b2);
  container.appendChild(wrap);
}
