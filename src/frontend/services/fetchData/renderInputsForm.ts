import { formatDatesForm } from '../formatDates/dates';

type FormEl = HTMLInputElement | HTMLTextAreaElement | HTMLSelectElement;

export function renderFormInputs<T extends Record<string, unknown>>(data: T): void {
  for (const [key, value] of Object.entries(data)) {
    const el = document.getElementById(key) as FormEl | null;
    if (!el) continue;

    // Checkbox
    if (el instanceof HTMLInputElement && el.type === 'checkbox') {
      el.checked = value === 1 || value === '1' || value === true;
      continue;
    }

    // null/undefined
    if (value === null || value === undefined) {
      el.value = '';
      continue;
    }

    // Fechas YYYY-MM-DD
    if (typeof value === 'string' && /^\d{4}-\d{2}-\d{2}$/.test(value)) {
      // ✅ input type="date" necesita YYYY-MM-DD
      if (el instanceof HTMLInputElement && el.type === 'date') {
        el.value = value;
      } else {
        // otros inputs sí pueden mostrar dd/mm/yyyy
        el.value = formatDatesForm(value);
      }
      continue;
    }

    el.value = String(value);
  }
}
