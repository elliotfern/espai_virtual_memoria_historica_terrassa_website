import { actualizarGenerarLinks } from './actualitzarGenerarLinks';

export function crearBotoActualitzacioRepressio(userId: number): void {
  const container = document.getElementById('btnActualitzarRepressio');
  if (!container) return;

  const btn = document.createElement('button');
  btn.textContent = 'Actualitzar';
  btn.className = 'btn btn-primary m-2';

  btn.addEventListener('click', () => {
    actualizarGenerarLinks(userId);
  });

  container.appendChild(btn);
}
