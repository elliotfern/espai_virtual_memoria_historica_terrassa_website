import { DOMAIN_WEB } from '../../config/constants';

export function renderAvisContacte(): void {
  const container = document.getElementById('avisContacte');
  if (!(container instanceof HTMLDivElement)) {
    console.warn('[renderAvisContacte] Falta #avisContacte');
    return;
  }
  if (container.dataset.rendered === '1') return;

  const p = document.createElement('p');
  // clases solicitadas para el texto
  p.className = 'fitxa-persona marro2 raleway';
  p.textContent = "Per aportar noves informacions sobre aquest represaliat/da, siguin documents, fotografies o noves dades o sol·licitar una correcció de dades, pots posar-te en contacte amb l'equip de l'Espai Virtual de la Memòria Històrica de Terrassa a través del nostre formulari:";

  const btn = document.createElement('button');
  btn.type = 'button';
  // clases solicitadas para el botón
  btn.className = 'btn btn-primary btn-custom-2';
  btn.textContent = "Contact'ns";
  btn.addEventListener('click', () => {
    window.location.href = `${DOMAIN_WEB}/contacte`;
  });

  container.append(p, btn);
  container.dataset.rendered = '1';
}
