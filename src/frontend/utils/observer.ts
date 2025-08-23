export function isMobile(): boolean {
  return window.matchMedia('(max-width: 767.98px)').matches; // breakpoint Bootstrap md
}

function smoothScrollTo(el: HTMLElement, offset = 0): void {
  const y = el.getBoundingClientRect().top + window.pageYOffset + offset;
  window.scrollTo({ top: y, behavior: 'smooth' });
}

/**
 * Observa el contenedor donde se renderiza el contenido de tab y,
 * en cuanto detecta cambios, hace scroll al título o al propio contenedor.
 */
export function scrollToTabContentOnce(): void {
  if (!isMobile()) return;

  // candidatos habituales en tu HTML
  const container = (document.querySelector('#fitxa') as HTMLElement) || (document.querySelector('#fitxa-categoria') as HTMLElement) || (document.querySelector('#info') as HTMLElement) || (document.querySelector('.fitxaRepresaliat2') as HTMLElement);

  if (!container) return;

  // Si ya existe un título, scroll inmediato
  const titleNow = container.querySelector('h3.titolSeccio') as HTMLElement | null;
  if (titleNow) {
    smoothScrollTo(titleNow, -8);
    titleNow.focus?.();
    return;
  }

  // Si no, observar una vez los cambios
  const observer = new MutationObserver(() => {
    const title = container.querySelector('h3.titolSeccio') as HTMLElement | null;
    const target = title || container;
    if (target) {
      observer.disconnect();
      smoothScrollTo(target, -8);
      (title || container).setAttribute('tabindex', '-1'); // accesibilidad
      (title || container).focus?.();
    }
  });

  observer.observe(container, { childList: true, subtree: true });

  // Fallback por si no saltara el observer (contenido ya inyectado muy rápido)
  setTimeout(() => {
    try {
      observer.disconnect();
    } catch {
      //
    }
    const title = container.querySelector('h3.titolSeccio') as HTMLElement | null;
    const target = title || container;
    if (target) smoothScrollTo(target, -8);
  }, 600);
}
