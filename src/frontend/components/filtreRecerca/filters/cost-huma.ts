// src/buscador/filters/cost-huma.ts
import Choices from 'choices.js';
import { FilterSpec, AvailablePayload, SelectionShape } from './types';
import { Persona, OpcionesFiltros } from '../types';
import { parseYear } from '../utils';

function makeSelectSlot(id: string, label: string) {
  return (container: HTMLElement) => {
    const wrap = document.createElement('div');
    wrap.className = 'filtro-grupo';
    wrap.innerHTML = `<label for="${id}">${label}</label><select id="${id}" multiple></select>`;
    container.appendChild(wrap);
  };
}
function hydrateChoices(av: AvailablePayload): Choices {
  const sel = document.getElementById(av.id) as HTMLSelectElement | null;
  if (!sel) throw new Error(`No s'ha trobat el select #${av.id}`);
  sel.innerHTML = '';
  av.options.forEach((o) => {
    const op = document.createElement('option');
    op.value = o.value;
    op.text = o.label;
    sel.appendChild(op);
  });
  return new Choices(`#${av.id}`, { removeItemButton: true, searchEnabled: true, shouldSort: false, itemSelectText: '' });
}

// Ejemplo A
export const FILTER_ANY_FERITS: FilterSpec = {
  id: 'filtro-any_ferits',
  stateKey: 'anys_ferits',
  renderSlot: makeSelectSlot('filtro-any_ferits', 'Any (ferits)'),
  available(personas: Persona[], opciones: OpcionesFiltros): AvailablePayload | null {
    void opciones; // <- evita el warning del linter
    const counts = new Map<number, number>();
    personas.forEach((p) => {
      const y = parseYear((p as unknown as { data_ferits?: string | null }).data_ferits ?? null);
      if (typeof y === 'number') counts.set(y, (counts.get(y) || 0) + 1);
    });
    const options = Array.from(counts.entries())
      .sort((a, b) => a[0] - b[0])
      .map(([y, n]) => ({ value: String(y), label: `${y} (${n})` }));
    return { id: this.id, stateKey: this.stateKey, options };
  },
  hydrate: hydrateChoices,
  predicate(p, sel: SelectionShape) {
    const chosen = new Set((sel['anys_ferits'] ?? []).map(Number));
    if (chosen.size === 0) return true;
    const y = parseYear((p as unknown as { data_ferits?: string | null }).data_ferits ?? null);
    return typeof y === 'number' && chosen.has(y);
  },
};

// Ejemplo B
export const FILTER_VA_MORIR: FilterSpec = {
  id: 'filtro-va_morir',
  stateKey: 'va_morir',
  renderSlot: makeSelectSlot('filtro-va_morir', 'Va morir'),
  available(_personas: Persona[], opciones: OpcionesFiltros): AvailablePayload | null {
    void opciones; // <- evita el warning del linter
    return {
      id: 'filtro-va_morir',
      stateKey: 'va_morir',
      options: [
        { value: '1', label: 'Sí' },
        { value: '2', label: 'No' },
      ],
    };
  },
  hydrate: hydrateChoices,
  predicate(p, sel: SelectionShape) {
    const chosen = new Set((sel['va_morir'] ?? []) as string[]);
    if (chosen.size === 0) return true;
    const val = (p as unknown as { va_morir?: number | null }).va_morir ?? null;
    return typeof val === 'number' && chosen.has(String(val));
  },
};

export const COST_HUMA_ONLY_FILTERS: FilterSpec[] = [
  // Añade aquí los filtros específicos que realmente uses.
  FILTER_VA_MORIR,
];
