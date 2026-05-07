import Choices from 'choices.js';
import 'choices.js/public/assets/styles/choices.min.css';

type Item = { id: number | string; [key: string]: unknown };

type SelectWithChoices = HTMLSelectElement & {
  choicesInstance?: Choices;
};

export async function auxiliarMultiSelect(selectedIds: (number | string)[] | null | undefined, api: string, elementId: string, valorText: string, config?: Partial<Choices['config']>): Promise<Choices | void> {
  const baseUrl = `https://${window.location.hostname}`;
  const urlAjax = `${baseUrl}/api/auxiliars/get/${api}`;

  try {
    const response = await fetch(urlAjax, {
      method: 'GET',
      headers: { Accept: 'application/json' },
    });

    if (!response.ok) {
      throw new Error(`Error en la solicitud (${response.status})`);
    }

    const json = await response.json();

    const raw = Array.isArray(json?.data) ? json.data : json;
    const data: Item[] = Array.isArray(raw) ? raw : [];

    const selectElement = document.getElementById(elementId) as SelectWithChoices | null;

    if (!selectElement) return;

    // destruir instancia previa ANTES de modificar DOM
    if (selectElement.choicesInstance) {
      selectElement.choicesInstance.destroy();
      selectElement.choicesInstance = undefined;
    }

    // limpiar options
    selectElement.innerHTML = '';

    // placeholder
    const placeholder = document.createElement('option');
    placeholder.value = '';
    placeholder.textContent = 'Selecciona una opció:';
    selectElement.appendChild(placeholder);

    // opciones
    for (const item of data) {
      const opt = document.createElement('option');

      opt.value = String(item.id);

      const label = item[valorText];
      opt.textContent = typeof label === 'string' ? label : String(label ?? '');

      selectElement.appendChild(opt);
    }

    // inicializar Choices
    const choices = new Choices(selectElement, {
      searchEnabled: true,
      removeItemButton: true,
      shouldSort: false,
      placeholder: true,
      placeholderValue: 'Selecciona una opció:',
      itemSelectText: '',
      ...config,
    });

    // guardar referencia
    selectElement.choicesInstance = choices;

    // seleccionar valores
    if (Array.isArray(selectedIds) && selectedIds.length > 0) {
      const values = selectedIds.map(String);

      requestAnimationFrame(() => {
        choices.setChoiceByValue(values);
      });
    }

    return choices;
  } catch (error) {
    console.error('Error al cargar opciones multi:', error);
  }
}
