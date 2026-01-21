// src/utils/auxiliarSelect.ts
import Choices from 'choices.js';
import 'choices.js/public/assets/styles/choices.min.css';

type Item = { id: number | string; [key: string]: unknown };

const choicesRegistry = new Map<string, Choices>();
const removeItemHandlerRegistry = new Map<string, (e: Event) => void>();

/**
 * Rellena un <select> desde /api/auxiliars/get/{api} y lo mejora con Choices.js.
 * - Funciona para INSERT (crear) y UPDATE (editar).
 * - No rompe formularios existentes: mantiene placeholder y permite "vaciar" con la X.
 * - Evita que el placeholder quede seleccionado "a la fuerza" (bug de value="").
 */
export async function auxiliarSelect(idAux: number | string | null | undefined, api: string, elementId: string, valorText: string, fallbackValue?: number | string, config?: Partial<Choices['config']>): Promise<Choices | void> {
  const baseUrl = `https://${window.location.hostname}`;
  const urlAjax = `${baseUrl}/api/auxiliars/get/${api}`;

  try {
    const response = await fetch(urlAjax, {
      method: 'GET',
      headers: { Accept: 'application/json' },
    });
    if (!response.ok) throw new Error(`Error en la solicitud (${response.status})`);

    const jsonResponse = await response.json();

    // Acepta {data:[...]} o directamente [...]
    const raw = Array.isArray(jsonResponse?.data) ? jsonResponse.data : jsonResponse;
    const data: Item[] = Array.isArray(raw) ? raw : [];

    const selectElement = document.getElementById(elementId) as HTMLSelectElement | null;
    if (!selectElement) return;

    // Destruir instancia anterior (Choices crea wrappers/eventos)
    const prev = choicesRegistry.get(elementId);
    if (prev) {
      prev.destroy();
      choicesRegistry.delete(elementId);
    }

    // Quitar listener anterior de removeItem (para no duplicar handlers)
    const prevHandler = removeItemHandlerRegistry.get(elementId);
    if (prevHandler) {
      selectElement.removeEventListener('removeItem', prevHandler as EventListener);
      removeItemHandlerRegistry.delete(elementId);
    }

    // === Construcción del <select> base ===
    selectElement.innerHTML = '';

    // Placeholder (se mantiene seleccionable para no romper flujos antiguos)
    // IMPORTANTE: NO forzamos selected aquí (eso fue lo que provocó value="" incluso con selección)
    const placeholder = document.createElement('option');
    placeholder.value = '';
    placeholder.textContent = 'Selecciona una opció:';
    selectElement.appendChild(placeholder);

    // Opciones (respetando el orden recibido)
    for (const item of data) {
      const opt = document.createElement('option');
      opt.value = String(item.id);

      const rawLabel = item[valorText];
      opt.textContent = typeof rawLabel === 'string' ? rawLabel : String(rawLabel ?? '');

      selectElement.appendChild(opt);
    }

    // Calcular selección inicial:
    // - Si idAux viene definido y no es 0 -> ese
    // - Si no, y hay fallbackValue -> fallback
    // - Si no -> vacío (placeholder)
    const initial = idAux !== null && idAux !== undefined && String(idAux) !== '' && String(idAux) !== '0' ? String(idAux) : fallbackValue !== undefined && String(fallbackValue) !== '' ? String(fallbackValue) : '';

    // Setear el value ANTES de Choices, para que Choices lo lea correctamente
    selectElement.value = initial;

    // === Inicializar Choices ===
    const choices = new Choices(selectElement, {
      searchEnabled: true,
      allowHTML: false,
      shouldSort: false,
      placeholder: true,
      placeholderValue: 'Selecciona una opció:',
      removeItemButton: true, // mantiene la “x” como en tu versión
      itemSelectText: '',
      noResultsText: 'Sense resultats',
      searchResultLimit: 100,
      renderChoiceLimit: -1,
      ...config,
    });

    // Si initial es '', dejamos vacío y mostramos placeholder
    if (!initial) {
      choices.removeActiveItems();
      selectElement.value = '';
    } else {
      // Asegura selección incluso si Choices necesitara sincronización
      choices.setChoiceByValue(initial);
      selectElement.value = initial;
    }

    // Al pulsar “x” (removeItem), dejamos el select en vacío y notificamos change
    const onRemoveItem = () => {
      choices.removeActiveItems();
      selectElement.value = '';
      selectElement.dispatchEvent(new Event('change', { bubbles: true }));
    };
    selectElement.addEventListener('removeItem', onRemoveItem as EventListener);
    removeItemHandlerRegistry.set(elementId, onRemoveItem as (e: Event) => void);

    choicesRegistry.set(elementId, choices);
    return choices;
  } catch (error) {
    console.error('Error al cargar las opciones:', error);
  }
}
