// admin/renderBotonsAdminSimple.ts
type Ctx = {
  id: number;
  completat: number | string; // 1=PENDENT, 2=COMPLETADA, 3=CAL REVISIÓ
  visibilitat: number | string; // 2=VISIBLE, otros=NO VISIBLE
  containerId?: string; // por defecto 'botonsAdmin'
};

function toIntOrNull(v: unknown): number | null {
  if (typeof v === 'number' && Number.isFinite(v)) return v;
  const n = Number.parseInt(String(v ?? '').trim(), 10);
  return Number.isFinite(n) ? n : null;
}

export function renderBotonsAdminSimple(ctx: Ctx): void {
  const container = document.getElementById(ctx.containerId ?? 'botonsAdmin');
  if (!container) return;

  // Limpia el contenedor .row
  container.innerHTML = '';

  // ——— Estado ———
  const compl = toIntOrNull(ctx.completat);
  const estatBtn = document.createElement('button');
  estatBtn.type = 'button';
  estatBtn.className = 'btn btn-sm me-2';

  if (compl === 2) {
    estatBtn.textContent = 'COMPLETADA';
    estatBtn.classList.add('btn-success');
  } else if (compl === 3) {
    estatBtn.textContent = 'CAL REVISIÓ';
    estatBtn.classList.add('btn-danger');
  } else {
    estatBtn.textContent = 'PENDENT';
    estatBtn.classList.add('btn-primary');
  }

  const colEstat = document.createElement('div');
  colEstat.className = 'col-auto my-2';
  colEstat.appendChild(estatBtn);

  // ——— Visibilitat ———
  const vis = toIntOrNull(ctx.visibilitat);
  const visBtn = document.createElement('button');
  visBtn.type = 'button';
  visBtn.className = 'btn btn-sm me-2';

  if (vis === 2) {
    visBtn.textContent = 'VISIBLE';
    visBtn.classList.add('btn-success');
  } else {
    visBtn.textContent = 'NO VISIBLE';
    visBtn.classList.add('btn-primary');
  }

  const colVis = document.createElement('div');
  colVis.className = 'col-auto my-2';
  colVis.appendChild(visBtn);

  // ——— Modificar ———
  const editLink = document.createElement('a');
  editLink.className = 'btn btn-outline-secondary btn-sm';
  editLink.target = '_blank';
  editLink.rel = 'noopener';
  // Usa el host actual para que funcione en dev/test/prod
  editLink.href = `https://${window.location.hostname}/gestio/base-dades/modifica-fitxa/${ctx.id}`;
  // Si prefieres fijar dominio explícito, usa:
  // editLink.href = `https://memoriaterrassa.cat/gestio/base-dades/modifica-fitxa/${ctx.id}`;
  editLink.textContent = 'Modifica la fitxa';

  const colEdit = document.createElement('div');
  colEdit.className = 'col-auto my-2';
  colEdit.appendChild(editLink);

  // ——— Añadir al row ———
  container.appendChild(colEstat);
  container.appendChild(colVis);
  container.appendChild(colEdit);
}
