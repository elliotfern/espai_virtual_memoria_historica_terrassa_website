import { showCookieBanner } from './mostrarCookieBanner';

export function createChangePreferencesButton(): HTMLElement {
  const btn = document.createElement('button');
  btn.textContent = 'Canviar les preferències de galetes';
  btn.style.cssText = `
    position: fixed; bottom: 1rem; right: 1rem; z-index: 999;
    background: #eee; color: #000; border: 1px solid #ccc;
    padding: 0.5rem 1rem; border-radius: 5px; font-size: 0.9rem;
  `;
  btn.addEventListener('click', showCookieBanner);
  return btn;
}
