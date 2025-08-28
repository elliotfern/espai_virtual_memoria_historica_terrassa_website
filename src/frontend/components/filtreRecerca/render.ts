// src/buscador/render.ts
import { OpcionesFiltros, Persona, SortKey } from './types';
import { fullName, norm } from './utils';

export function renderResultsPaginated(personas: Persona[], opciones: OpcionesFiltros, sortKey: SortKey, page: number, pageSize: number, onPageChange: (newPage: number) => void): { currentPage: number; totalPages: number } {
  const cont = document.getElementById('tabla-resultados');
  const info = document.getElementById('pageInfo');
  const prev = document.getElementById('prevPage') as HTMLButtonElement | null;
  const next = document.getElementById('nextPage') as HTMLButtonElement | null;
  const contador = document.getElementById('contador-resultados');

  if (!cont || !info || !prev || !next || !contador) {
    return { currentPage: page, totalPages: 1 };
  }

  // ——— Ordenación ———
  const sorted = [...personas];
  if (sortKey === 'cognoms') {
    sorted.sort((a, b) => norm(`${a.cognom1} ${a.cognom2}`).localeCompare(norm(`${b.cognom1} ${b.cognom2}`)) || norm(a.nom).localeCompare(norm(b.nom)));
  } else if (sortKey === 'nom') {
    sorted.sort((a, b) => norm(a.nom).localeCompare(norm(b.nom)) || norm(`${a.cognom1} ${a.cognom2}`).localeCompare(norm(`${b.cognom1} ${b.cognom2}`)));
  } else if (sortKey === 'municipi') {
    sorted.sort((a, b) => {
      const ma = opciones.municipis.find((m) => m.id === a.municipi_naixement)?.ciutat || '';
      const mb = opciones.municipis.find((m) => m.id === b.municipi_naixement)?.ciutat || '';
      return norm(ma).localeCompare(norm(mb)) || norm(fullName(a)).localeCompare(norm(fullName(b)));
    });
  }

  // ——— Paginación ———
  const total = sorted.length;
  const totalPages = Math.max(1, Math.ceil(total / pageSize));
  const currentPage = Math.min(Math.max(1, page), totalPages);
  const startIdx = (currentPage - 1) * pageSize;
  const endIdx = Math.min(startIdx + pageSize, total);
  const pageSlice = sorted.slice(startIdx, endIdx);

  // Map rápido de municipios por id
  const municipiById = new Map(opciones.municipis.map((m) => [m.id, m]));
  const fmt = (d?: string | null) => (d && d.trim() ? d : '');

  // ——— Render lista ———
  if (pageSlice.length === 0) {
    cont.innerHTML = '<p>No hi ha resultats.</p>';
  } else {
    cont.innerHTML = pageSlice
      .map((p) => {
        const mN = p.municipi_naixement ? municipiById.get(p.municipi_naixement)?.ciutat || '' : '';
        const mD = p.municipi_defuncio ? municipiById.get(p.municipi_defuncio)?.ciutat || '' : '';
        const born = fmt(p.data_naixement);
        const died = fmt(p.data_defuncio);
        const birthStr = born || mN ? `${born}${mN ? ` (${mN})` : ''}` : '';
        const defStr = died || mD ? `${died}${mD ? ` (${mD})` : ''}` : '';
        const datesStr = [birthStr, defStr].filter(Boolean).join(' / ');
        const href = `https://memoriaterrassa.cat/fitxa/${p.slug}`;
        return `
          <div class="fila-persona" style="margin-top:20px">
            <div>
              <strong><a href="${href}" target="_blank" rel="noopener noreferrer">${fullName(p)}</a></strong><br/>
              <small>${datesStr}</small>
            </div>
          </div>
        `;
      })
      .join('');
  }

  // ——— Contador y controles ———
  contador.textContent = total === 0 ? '0 resultats' : `Mostrant ${startIdx + 1}–${endIdx} de ${total} resultats`;
  info.textContent = `Pàgina ${currentPage} / ${totalPages}`;
  prev.disabled = currentPage <= 1;
  next.disabled = currentPage >= totalPages;

  prev.onclick = () => {
    if (currentPage > 1) onPageChange(currentPage - 1);
  };
  next.onclick = () => {
    if (currentPage < totalPages) onPageChange(currentPage + 1);
  };

  return { currentPage, totalPages };
}
