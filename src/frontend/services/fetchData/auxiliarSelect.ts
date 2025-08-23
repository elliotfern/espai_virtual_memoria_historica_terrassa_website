// src/utils/auxiliarSelect.ts
import Choices from 'choices.js';
import 'choices.js/public/assets/styles/choices.min.css';

type Item = { id: number; [key: string]: unknown };

// guardamos instancias para destruirlas si rehidratamos
const choicesRegistry = new Map<string, Choices>();

/**
 * Rellena un <select> con datos de la API y lo mejora con Choices.js
 */
export async function auxiliarSelect(idAux: number | null | undefined, api: string, elementId: string, valorText: string, fallbackValue?: string, config?: Partial<Choices['config']>): Promise<Choices | void> {
  const devDirectory = `https://${window.location.hostname}`;
  const urlAjax = `${devDirectory}/api/auxiliars/get/${api}`;

  try {
    const response = await fetch(urlAjax, {
      method: 'GET',
      headers: { 'Content-Type': 'application/json' },
    });
    if (!response.ok) throw new Error('Error en la solicitud');

    const jsonResponse = await response.json();
    const data: Item[] = Array.isArray(jsonResponse?.data) ? jsonResponse.data : jsonResponse;

    const selectElement = document.getElementById(elementId) as HTMLSelectElement | null;
    if (!selectElement) return;

    // Si ya estaba inicializado, lo destruimos para evitar fugas/eventos duplicados
    const prev = choicesRegistry.get(elementId);
    if (prev) {
      prev.destroy();
      choicesRegistry.delete(elementId);
    }

    // Limpiamos el select base y añadimos placeholder (Choices lo usará)
    selectElement.innerHTML = '';
    const placeholder = document.createElement('option');
    placeholder.value = '';
    placeholder.text = 'Selecciona una opció:';
    placeholder.setAttribute('selected', '');
    placeholder.setAttribute('disabled', '');
    placeholder.setAttribute('hidden', '');
    selectElement.appendChild(placeholder);

    // Creamos Choices
    const choices = new Choices(selectElement, {
      removeItemButton: true,
      searchEnabled: true,
      allowHTML: false,
      shouldSort: true,
      placeholder: true,
      placeholderValue: 'Selecciona una opció:',
      ...config, // permite sobreescribir desde fuera
    });

    // Cargamos opciones
    const options = data.map((item) => {
      const raw = item[valorText];
      const label = typeof raw === 'string' ? raw : String(raw ?? '');
      return {
        value: String(item.id),
        label,
        selected: false,
        disabled: false,
      };
    });

    // Reemplazamos todas las opciones del componente
    choices.clearStore();
    choices.setChoices(options, 'value', 'label', true);

    // Selección inicial (idAux > 0 o fallback)
    const initial = idAux !== null && idAux !== undefined && idAux !== 0 ? String(idAux) : fallbackValue !== undefined ? String(fallbackValue) : '';
    if (initial) {
      choices.setChoiceByValue(initial);
    } else {
      // Garantiza que el placeholder esté visible
      choices.removeActiveItems();
    }

    choicesRegistry.set(elementId, choices);
    return choices;
  } catch (error) {
    console.error('Error al cargar las opciones:', error);
  }
}
