import { DOMAIN_WEB } from '../../config/constants';

export function renderAvisContacte(): void {
  const container = document.getElementById('avisContacte');
  if (!(container instanceof HTMLDivElement)) {
    console.warn('[renderAvisContacte] Falta #avisContacte');
    return;
  }
  if (container.dataset.rendered === '1') return;

  const p = document.createElement('p');
  p.className = 'avis-text';
  p.textContent = "Per aportar noves informacions sobre aquest represaliat/da, siguin documents, fotografies o noves dades o sol·licitar una correcció de dades, pots posar-te en contacte amb l'equip de l'Espai Virtual de la Memòria Històrica de Terrassa a través del nostre formulari:";

  const btn = document.createElement('button');
  btn.type = 'button';
  btn.className = 'botoCategoriaRepresio';
  btn.textContent = "Contact'ns";
  // abrir en nueva pestaña (como un enlace con target="_blank")
  btn.addEventListener('click', () => {
    window.location.href = `${DOMAIN_WEB}/contacte`;
  });

  container.append(p, btn);
  container.dataset.rendered = '1';
}
