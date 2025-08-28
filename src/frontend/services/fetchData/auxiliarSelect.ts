// src/utils/auxiliarSelect.ts
import Choices from 'choices.js';
import 'choices.js/public/assets/styles/choices.min.css';

type Item = { id: number; [key: string]: unknown };

const choicesRegistry = new Map<string, Choices>();

export async function auxiliarSelect(idAux: number | null | undefined, api: string, elementId: string, valorText: string, fallbackValue?: string, config?: Partial<Choices['config']>): Promise<Choices | void> {
  const devDirectory = `https://${window.location.hostname}`;
  const urlAjax = `${devDirectory}/api/auxiliars/get/${api}`;

  try {
    const response = await fetch(urlAjax, { method: 'GET', headers: { 'Content-Type': 'application/json' } });
    if (!response.ok) throw new Error('Error en la solicitud');

    const jsonResponse = await response.json();
    const data: Item[] = Array.isArray(jsonResponse?.data) ? jsonResponse.data : jsonResponse;

    const selectElement = document.getElementById(elementId) as HTMLSelectElement | null;
    if (!selectElement) return;

    // destruir instancia anterior
    const prev = choicesRegistry.get(elementId);
    if (prev) {
      prev.destroy();
      choicesRegistry.delete(elementId);
    }

    // limpiar y añadir placeholder (NO disabled)
    selectElement.innerHTML = '';
    const placeholder = document.createElement('option');
    placeholder.value = '';
    placeholder.text = 'Selecciona una opció:';
    placeholder.setAttribute('selected', '');
    // 👇 no uses disabled/hidden en el placeholder
    // placeholder.setAttribute('disabled', '');
    // placeholder.setAttribute('hidden', '');
    selectElement.appendChild(placeholder);

    // calcular selección inicial
    const initial = idAux !== null && idAux !== undefined && idAux !== 0 ? String(idAux) : fallbackValue !== undefined ? String(fallbackValue) : '';

    // crear Choices (sin removeItemButton para single select)
    const choices = new Choices(selectElement, {
      searchEnabled: true,
      shouldSort: true,
      allowHTML: false,
      placeholder: true,
      placeholderValue: 'Selecciona una opció:',
      removeItemButton: false, // ← importante para single select
      ...config,
    });

    // cargar opciones
    const options = data.map((item) => {
      const raw = item[valorText];
      const label = typeof raw === 'string' ? raw : String(raw ?? '');
      return { value: String(item.id), label };
    });

    // reemplazar opciones correctamente
    choices.clearChoices();
    choices.setChoices(options, 'value', 'label', true);

    if (initial) {
      // seleccionar valor inicial
      choices.setChoiceByValue(initial);
    } else {
      // garantizar placeholder (valor vacío) cuando no hay selección
      selectElement.value = '';
      choices.removeActiveItems();
    }

    choicesRegistry.set(elementId, choices);
    return choices;
  } catch (error) {
    console.error('Error al cargar las opciones:', error);
  }
}
