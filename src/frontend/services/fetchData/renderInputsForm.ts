import { formatDatesForm } from '../formatDates/dates';

type FormEl = HTMLInputElement | HTMLTextAreaElement | HTMLSelectElement;

export function renderFormInputs<T extends Record<string, unknown>>(data: T): void {
  for (const [key, value] of Object.entries(data)) {
    // ✅ Para IDs, mejor getElementById (evita selector inválido '#0')
    const el = document.getElementById(key) as FormEl | null;
    if (!el) continue;

    // ✅ Checkbox (0/1, true/false, "1"/"0")
    if (el instanceof HTMLInputElement && el.type === 'checkbox') {
      el.checked = value === 1 || value === '1' || value === true;
      continue;
    }

    // ✅ Fecha YYYY-MM-DD
    if (typeof value === 'string' && /^\d{4}-\d{2}-\d{2}$/.test(value)) {
      el.value = formatDatesForm(value);
      continue;
    }

    // ✅ Null/undefined
    if (value === null || value === undefined) {
      el.value = '';
      continue;
    }

    // ✅ Resto
    el.value = String(value);
  }
}
