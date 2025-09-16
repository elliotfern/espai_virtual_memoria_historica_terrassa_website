// helpers/transmissioHelper.ts
import { transmissioDadesDB } from '../services/fetchData/transmissioDades';

export type SuccessBehavior = 'none' | 'hide' | 'disable';

export interface TransmissioOptions {
  tipus?: 'POST' | 'PUT' | 'PATCH' | 'DELETE';
  formId: string;
  urlAjax: string;
  neteja?: boolean;
  successBehavior?: SuccessBehavior;

  /** ——— Scroll options ——— */
  /** Si true, hace scroll al terminar con éxito (por defecto true cuando successBehavior === 'hide') */
  scrollOnSuccess?: boolean;
  /** Selector del objetivo al que hacer scroll (por defecto '#okMessage') */
  scrollTargetSelector?: string;
  /** Desplazamiento extra para cabeceras fijas, ej. 80px */
  scrollOffset?: number;
  /** 'auto' o 'smooth' (por defecto 'smooth') */
  scrollBehavior?: ScrollBehavior;
}

/** Utilidad de scroll con offset para cabeceras fijas */
function smartScrollIntoView(el: Element, opts?: { offset?: number; behavior?: ScrollBehavior }) {
  const offset = opts?.offset ?? 80;
  const behavior = opts?.behavior ?? 'smooth';
  const rect = el.getBoundingClientRect();
  const absoluteTop = rect.top + window.scrollY;
  const targetY = Math.max(absoluteTop - offset, 0);
  window.scrollTo({ top: targetY, behavior });
}

/**
 * Devuelve un handler de submit ya configurado con options.
 */
export function makeFormSubmitHandler(options: TransmissioOptions) {
  const { tipus = 'POST', formId, urlAjax, neteja = false, successBehavior = 'none', scrollOnSuccess, scrollTargetSelector = '#okMessage', scrollOffset = 80, scrollBehavior = 'smooth' } = options;

  // Activación por defecto: si ocultas el form, hacemos scroll al mensaje OK
  const shouldScroll = scrollOnSuccess ?? successBehavior === 'hide';

  return (e: Event) => {
    // Si queremos hacer scroll, preparamos un listener one-off al evento 'form:success'
    if (shouldScroll) {
      const form = document.getElementById(formId);
      if (form) {
        const once = () => {
          // pequeño delay para permitir que el DOM pinte los mensajes
          setTimeout(() => {
            const target = (scrollTargetSelector && document.querySelector(scrollTargetSelector)) || document.getElementById('okMessage') || form; // fallback
            if (target) {
              smartScrollIntoView(target, { offset: scrollOffset, behavior: scrollBehavior });
              // intenta enfocar el contenedor de texto si existe
              const okText = document.getElementById('okText');
              (okText as HTMLElement | null)?.focus?.();
            }
          }, 30);
        };
        form.addEventListener('form:success', once, { once: true });
      }
    }

    return transmissioDadesDB(e, tipus, formId, urlAjax, neteja, successBehavior);
  };
}

/**
 * Conecta el submit del formulario indicado a transmissioDadesDB con options.
 * Devuelve una función para des-enganchar el listener.
 */
export function wireForm(options: TransmissioOptions): () => void {
  const handler = makeFormSubmitHandler(options);
  const form = document.getElementById(options.formId) as HTMLFormElement | null;

  if (!form) {
    console.warn(`[wireForm] Form with id "${options.formId}" not found`);
    return () => {};
  }

  form.addEventListener('submit', handler);
  return () => form.removeEventListener('submit', handler);
}
