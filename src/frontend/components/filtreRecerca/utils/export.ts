// utils/export.ts
type SelValue = string | number | boolean | null | undefined | (string | number | boolean)[];
export type Selection = Record<string, SelValue>;

/** Convierte tu selection a querystring (?k=1&k=6...) */
function selectionToSearchParams(selection: Selection): URLSearchParams {
  const sp = new URLSearchParams();
  Object.entries(selection ?? {}).forEach(([key, val]) => {
    if (val === null || val === undefined) return;
    if (Array.isArray(val)) {
      val.forEach((v) => v !== null && v !== undefined && sp.append(key, String(v)));
    } else {
      sp.set(key, String(val));
    }
  });
  // nunca exportamos paginación
  sp.delete('page');
  sp.delete('limit');
  return sp;
}

/** Construye la URL del endpoint de export según formato */
function buildExportUrl(fmt: 'csv' | 'xlsx', qs: string): string {
  const base = fmt === 'csv' ? `https://${window.location.hostname}/api/export/persones_csv` : `https://${window.location.hostname}/api/export/persones_xlsx`;
  return qs ? `${base}?${qs}` : base;
}
/** Dispara la descarga por GET (preferido para archivos) */
export function downloadExportFromSelection(selection: Selection, fmt: 'csv' | 'xlsx' = 'csv'): void {
  const qs = selectionToSearchParams(selection).toString();
  const url = buildExportUrl(fmt, qs);
  window.location.href = url;
}

/** Monta dos botones “Exportar” en el contenedor dado */
export function mountExportToolbar(container: HTMLElement, getSelection: () => Selection): void {
  if (!container) return;

  // Evitar duplicados si rehidratáis la vista
  const existing = container.querySelector('[data-export-toolbar]');
  if (existing) existing.remove();

  const wrapper = document.createElement('div');
  wrapper.setAttribute('data-export-toolbar', 'true');
  wrapper.className = 'd-flex gap-2 flex-wrap my-2';

  const btnCsv = document.createElement('button');
  btnCsv.type = 'button';
  btnCsv.className = 'btn btn-outline-primary';
  btnCsv.textContent = 'Exportar CSV';

  const btnXlsx = document.createElement('button');
  btnXlsx.type = 'button';
  btnXlsx.className = 'btn btn-outline-success';
  btnXlsx.textContent = 'Exportar Excel';

  btnCsv.addEventListener('click', () => downloadExportFromSelection(getSelection(), 'csv'));
  btnXlsx.addEventListener('click', () => downloadExportFromSelection(getSelection(), 'xlsx'));

  wrapper.appendChild(btnCsv);
  wrapper.appendChild(btnXlsx);
  container.appendChild(wrapper);
}
