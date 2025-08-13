// src/buscador/filters/exili.ts
import Choices from 'choices.js';
import { FilterSpec, AvailablePayload, SelectionShape } from './types';
import { OpcionesFiltros, Persona } from '../types';
import { parseYear } from '../utils';

// --- helpers UI ---
const slot = (id: string, label: string) => (container: HTMLElement) => {
  const d = document.createElement('div');
  d.className = 'filtro-grupo';
  d.innerHTML = `<label for="${id}">${label}</label><select id="${id}" multiple></select>`;
  container.appendChild(d);
};

const hydrate = (av: AvailablePayload): Choices => {
  const s = document.getElementById(av.id) as HTMLSelectElement | null;
  if (!s) throw new Error(`No s'ha trobat el select #${av.id}`);
  s.innerHTML = '';
  av.options.forEach((o) => {
    const op = document.createElement('option');
    op.value = o.value;
    op.text = o.label;
    s.appendChild(op);
  });
  return new Choices(`#${av.id}`, {
    removeItemButton: true,
    searchEnabled: true,
    shouldSort: false,
    itemSelectText: '',
  });
};

// ===================== Any d’exili =====================
export const FILTER_ANY_EXILI: FilterSpec = {
  id: 'filtro-any_exili',
  stateKey: 'anys_exili',
  renderSlot: slot('filtro-any_exili', 'Any exili'),
  available(personas: Persona[], _opciones: OpcionesFiltros): AvailablePayload | null {
    void _opciones; // no usado aquí
    const counts = new Map<number, number>();
    personas.forEach((p) => {
      const y = parseYear(p.data_exili ?? null);
      if (typeof y === 'number') counts.set(y, (counts.get(y) || 0) + 1);
    });
    const options = Array.from(counts.entries())
      .sort((a, b) => a[0] - b[0])
      .map(([y, n]) => ({ value: String(y), label: `${y} (${n})` }));
    return { id: this.id, stateKey: this.stateKey, options };
  },
  hydrate,
  predicate(p: Persona, sel: SelectionShape): boolean {
    const wanted = new Set((sel['anys_exili'] ?? []).map(Number));
    if (wanted.size === 0) return true;
    const y = parseYear(p.data_exili ?? null);
    return typeof y === 'number' && wanted.has(y);
  },
};

// =========== Primer destí d’exili (usa municipis) ===========
export const FILTER_PRIMER_DESTI_EXILI: FilterSpec = {
  id: 'filtro-primer_desti_exili',
  stateKey: 'primer_desti_exili',
  renderSlot: slot('filtro-primer_desti_exili', 'Primer destí d’exili'),
  available(personas: Persona[], opciones: OpcionesFiltros): AvailablePayload | null {
    const counts = new Map<number, number>();
    personas.forEach((p) => {
      if (typeof p.primer_desti_exili === 'number') {
        counts.set(p.primer_desti_exili, (counts.get(p.primer_desti_exili) || 0) + 1);
      }
    });
    const options = Array.from(counts.entries())
      .map(([id, n]) => {
        const m = opciones.municipis.find((x) => x.id === id)?.ciutat ?? `#${id}`;
        return { value: String(id), label: `${m} (${n})` };
      })
      .sort((a, b) => a.label.localeCompare(b.label));
    return { id: this.id, stateKey: this.stateKey, options };
  },
  hydrate,
  predicate(p: Persona, sel: SelectionShape): boolean {
    const wanted = new Set((sel['primer_desti_exili'] ?? []).map(Number));
    return wanted.size === 0 || (typeof p.primer_desti_exili === 'number' && wanted.has(p.primer_desti_exili));
  },
};

// ==================== Deportat (1 = Sí, 2 = No) ====================
export const FILTER_DEPORTAT: FilterSpec = {
  id: 'filtro-deportat',
  stateKey: 'deportat',
  renderSlot: slot('filtro-deportat', 'Deportat?'),
  available(_personas: Persona[], _opciones: OpcionesFiltros): AvailablePayload | null {
    void _personas;
    void _opciones;
    const options = [
      { value: '1', label: 'Sí' },
      { value: '2', label: 'No' },
    ];
    return { id: this.id, stateKey: this.stateKey, options };
  },
  hydrate,
  predicate(p: Persona, sel: SelectionShape): boolean {
    const wanted = new Set(sel['deportat'] ?? []);
    if (wanted.size === 0) return true;
    return typeof p.deportat === 'number' && wanted.has(String(p.deportat));
  },
};

// ===== Participació resistència (1 = Sí, 2 = No) =====
// OJO: ahora la propiedad del modelo es `participacio_resistencia`
export const FILTER_RESISTENCIA_FR: FilterSpec = {
  id: 'filtro-resistencia_fr',
  stateKey: 'resistencia_fr',
  renderSlot: slot('filtro-resistencia_fr', 'Participació a la resistència Francesa?'),
  available(_personas: Persona[], _opciones: OpcionesFiltros): AvailablePayload | null {
    void _personas;
    void _opciones;
    const options = [
      { value: '1', label: 'Sí' },
      { value: '2', label: 'No' },
    ];
    return { id: this.id, stateKey: this.stateKey, options };
  },
  hydrate,
  predicate(p: Persona, sel: SelectionShape): boolean {
    const wanted = new Set(sel['resistencia_fr'] ?? []);
    if (wanted.size === 0) return true;
    return typeof p.participacio_resistencia === 'number' && wanted.has(String(p.participacio_resistencia));
  },
};

export const EXILI_ONLY_FILTERS: FilterSpec[] = [FILTER_ANY_EXILI, FILTER_PRIMER_DESTI_EXILI, FILTER_DEPORTAT, FILTER_RESISTENCIA_FR];
