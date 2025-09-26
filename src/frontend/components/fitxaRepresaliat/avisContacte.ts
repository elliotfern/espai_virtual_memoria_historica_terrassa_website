import { DOMAIN_WEB } from '../../config/constants';

export function renderAvisContacte(): void {
  const container = document.getElementById('avisContacte') as HTMLDivElement | null;
  if (!container) {
    console.warn('[renderAvisContacte] No existe el div #avisContacte');
    return;
  }

  // Evita duplicar si ya se ha renderizado
  if (container.dataset.rendered === '1') return;

  container.innerHTML = `
    <p class="avis-text">
      Per aportar noves informacions sobre aquest represaliat/da, siguin documents, fotografies o noves dades, o vols sol·licitar una correcció de dades, pots posar-te en contacte amb l'equip de l'Espai Virtual de la Memòria Històrica de Terrassa a través del nostre formulari:
    </p>
    <a class="btn-contacte" href="${DOMAIN_WEB}/contacte" rel="noopener noreferrer">
      Contact'ns
    </a>
  `;

  container.dataset.rendered = '1';
}
