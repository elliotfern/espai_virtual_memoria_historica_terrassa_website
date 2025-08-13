// src/buscador/filters/common.ts
import Choices from 'choices.js';
import { FilterSpec, AvailablePayload, SelectionShape } from './types';
import { OpcionesFiltros, Persona } from '../types';
import { parseYear } from '../utils';
import { getProvinciaByMunicipi } from '../prov-map'; // ⬅️ NUEVO

// ----------------- helpers UI -----------------
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
  return new Choices(`#${av.id}`, {
    removeItemButton: true,
    searchEnabled: true,
    shouldSort: false,
    itemSelectText: '',
  });
}

// ----------------- helpers datos -----------------
function countByNumber(arr: number[]): Map<number, number> {
  const m = new Map<number, number>();
  for (const v of arr) m.set(v, (m.get(v) || 0) + 1);
  return m;
}

export const FILTER_PROVINCIA: FilterSpec = {
  id: 'filtro-provincia_naixement',
  stateKey: 'provincies',
  renderSlot: makeSelectSlot('filtro-provincia_naixement', 'Província'),

  available(personas: Persona[], opciones: OpcionesFiltros): AvailablePayload | null {
    // mapa rápido municipio -> província
    const muniToProv = new Map<number, string>(opciones.municipis.map((m) => [m.id, (m.provincia || '').trim()]));

    // contar provincias (directa o derivada)
    const counts = new Map<string, number>();
    for (const p of personas) {
      let prov = (p.provincia_naixement ?? '').trim();
      if (!prov && typeof p.municipi_naixement === 'number') {
        prov = (muniToProv.get(p.municipi_naixement) ?? '').trim();
      }
      if (prov) counts.set(prov, (counts.get(prov) || 0) + 1);
    }

    const options = Array.from(counts.entries())
      .map(([prov, n]) => ({ value: prov, label: `${prov} (${n})` }))
      .sort((a, b) => a.label.localeCompare(b.label));

    return { id: this.id, stateKey: this.stateKey, options };
  },

  hydrate: hydrateChoices,

  predicate(p: Persona, sel: SelectionShape): boolean {
    const chosen = new Set((sel['provincies'] ?? []).map((s) => s.toLowerCase()));
    if (chosen.size === 0) return true;

    // 1) directa si existe
    let prov = (p.provincia_naixement ?? '').trim().toLowerCase();

    // 2) si no hay directa, derivada desde el municipi
    // dentro de FILTER_PROVINCIA.predicate
    if (!prov && typeof p.municipi_naixement === 'number') {
      prov = (getProvinciaByMunicipi(p.municipi_naixement) ?? '').trim().toLowerCase();
    }

    return prov ? chosen.has(prov) : false;
  },
};

// =====================================================

// Municipi de naixement (sin "#0" y solo ids válidos del catálogo)
export const FILTER_MUNICIPI_NX: FilterSpec = {
  id: 'filtro-municipi_naixement',
  stateKey: 'municipis_naixement',
  renderSlot: makeSelectSlot('filtro-municipi_naixement', 'Municipi de naixement'),

  available(personas: Persona[], opciones: OpcionesFiltros): AvailablePayload | null {
    // catálogo válido
    const validIds = new Set<number>(opciones.municipis.map((m) => m.id));
    const muniById = new Map<number, string>(opciones.municipis.map((m) => [m.id, m.ciutat]));

    // contar solo ids numéricos > 0 y presentes en el catálogo
    const counts = new Map<number, number>();
    for (const p of personas) {
      const id = p.municipi_naixement;
      if (typeof id === 'number' && id > 0 && validIds.has(id)) {
        counts.set(id, (counts.get(id) || 0) + 1);
      }
    }

    // construir opciones
    const options = Array.from(counts.keys())
      .map((id) => {
        const n = counts.get(id) || 0;
        const name = muniById.get(id) ?? `#${id}`; // debería existir por el filtro, pero dejamos fallback
        return { value: String(id), label: `${name} (${n})` };
      })
      .sort((a, b) => a.label.localeCompare(b.label));

    return { id: this.id, stateKey: this.stateKey, options };
  },

  hydrate: hydrateChoices,

  predicate(p: Persona, sel: SelectionShape): boolean {
    const chosen = new Set((sel['municipis_naixement'] ?? []).map((v) => Number(v)));
    if (chosen.size === 0) return true;
    const id = p.municipi_naixement;
    return typeof id === 'number' && id > 0 && chosen.has(id);
  },
};

// =====================================================
export const FILTER_ANY_NAIX: FilterSpec = {
  id: 'filtro-any_naixement',
  stateKey: 'anys_naixement',
  renderSlot: makeSelectSlot('filtro-any_naixement', 'Any naixement'),
  available(personas: Persona[], opciones: OpcionesFiltros): AvailablePayload | null {
    void opciones;
    const years = personas.map((p) => parseYear(p.data_naixement)).filter((y): y is number => typeof y === 'number');
    const counts = countByNumber(years);
    const options = Array.from(counts.entries())
      .sort((a, b) => a[0] - b[0])
      .map(([y, n]) => ({ value: String(y), label: `${y} (${n})` }));
    return { id: this.id, stateKey: this.stateKey, options };
  },
  hydrate: hydrateChoices,
  predicate(p: Persona, sel: SelectionShape): boolean {
    const chosen = new Set((sel['anys_naixement'] ?? []).map(Number));
    if (chosen.size === 0) return true;
    const y = parseYear(p.data_naixement);
    return typeof y === 'number' && chosen.has(y);
  },
};

// =====================================================
export const FILTER_ANY_DEF: FilterSpec = {
  id: 'filtro-any_defuncio',
  stateKey: 'anys_defuncio',
  renderSlot: makeSelectSlot('filtro-any_defuncio', 'Any defunció'),
  available(personas: Persona[], opciones: OpcionesFiltros): AvailablePayload | null {
    void opciones;
    const years = personas.map((p) => parseYear(p.data_defuncio)).filter((y): y is number => typeof y === 'number');
    const counts = countByNumber(years);
    const options = Array.from(counts.entries())
      .sort((a, b) => a[0] - b[0])
      .map(([y, n]) => ({ value: String(y), label: `${y} (${n})` }));
    return { id: this.id, stateKey: this.stateKey, options };
  },
  hydrate: hydrateChoices,
  predicate(p: Persona, sel: SelectionShape): boolean {
    const chosen = new Set((sel['anys_defuncio'] ?? []).map(Number));
    if (chosen.size === 0) return true;
    const y = parseYear(p.data_defuncio);
    return typeof y === 'number' && chosen.has(y);
  },
};

// =====================================================
export const FILTER_MUNICIPI_DEF: FilterSpec = {
  id: 'filtro-municipi_defuncio',
  stateKey: 'municipis_defuncio',
  renderSlot: makeSelectSlot('filtro-municipi_defuncio', 'Municipi de defunció'),
  available(personas: Persona[], opciones: OpcionesFiltros): AvailablePayload | null {
    const ids = personas.map((p) => p.municipi_defuncio).filter((x): x is number => typeof x === 'number');
    const counts = countByNumber(ids);
    const options = Array.from(counts.entries())
      .map(([id, n]) => {
        const name = opciones.municipis.find((m) => m.id === id)?.ciutat ?? `#${id}`;
        return { value: String(id), label: `${name} (${n})` };
      })
      .sort((a, b) => a.label.localeCompare(b.label));
    return { id: this.id, stateKey: this.stateKey, options };
  },
  hydrate: hydrateChoices,
  predicate(p: Persona, sel: SelectionShape): boolean {
    const chosen = new Set((sel['municipis_defuncio'] ?? []).map(Number));
    if (chosen.size === 0) return true;
    return typeof p.municipi_defuncio === 'number' && chosen.has(p.municipi_defuncio);
  },
};

// =====================================================
export const FILTER_ESTAT: FilterSpec = {
  id: 'filtro-estat_civil',
  stateKey: 'estats',
  renderSlot: makeSelectSlot('filtro-estat_civil', 'Estat civil'),
  available(personas: Persona[], opciones: OpcionesFiltros): AvailablePayload | null {
    const ids = personas.map((p) => p.estat_civil);
    const counts = countByNumber(ids);
    const options = opciones.estats_civils.filter((e) => counts.has(e.id)).map((e) => ({ value: String(e.id), label: `${e.estat_cat} (${counts.get(e.id) || 0})` }));
    return { id: this.id, stateKey: this.stateKey, options };
  },
  hydrate: hydrateChoices,
  predicate(p: Persona, sel: SelectionShape): boolean {
    const chosen = new Set((sel['estats'] ?? []).map(Number));
    return chosen.size === 0 || chosen.has(p.estat_civil);
  },
};

// =====================================================
export const FILTER_ESTUDIS: FilterSpec = {
  id: 'filtro-estudis',
  stateKey: 'estudis',
  renderSlot: makeSelectSlot('filtro-estudis', 'Estudis'),
  available(personas: Persona[], opciones: OpcionesFiltros): AvailablePayload | null {
    const ids = personas.map((p) => p.estudis);
    const counts = countByNumber(ids);
    const options = opciones.estudis.filter((e) => counts.has(e.id)).map((e) => ({ value: String(e.id), label: `${e.estudi_cat} (${counts.get(e.id) || 0})` }));
    return { id: this.id, stateKey: this.stateKey, options };
  },
  hydrate: hydrateChoices,
  predicate(p: Persona, sel: SelectionShape): boolean {
    const chosen = new Set((sel['estudis'] ?? []).map(Number));
    return chosen.size === 0 || chosen.has(p.estudis);
  },
};

// =====================================================
export const FILTER_OFICI: FilterSpec = {
  id: 'filtro-ofici',
  stateKey: 'oficis',
  renderSlot: makeSelectSlot('filtro-ofici', 'Ofici'),
  available(personas: Persona[], opciones: OpcionesFiltros): AvailablePayload | null {
    const ids = personas.map((p) => p.ofici);
    const counts = countByNumber(ids);
    const options = opciones.oficis.filter((o) => counts.has(o.id)).map((o) => ({ value: String(o.id), label: `${o.ofici_cat} (${counts.get(o.id) || 0})` }));
    return { id: this.id, stateKey: this.stateKey, options };
  },
  hydrate: hydrateChoices,
  predicate(p: Persona, sel: SelectionShape): boolean {
    const chosen = new Set((sel['oficis'] ?? []).map(Number));
    return chosen.size === 0 || chosen.has(p.ofici);
  },
};

// =====================================================
export const FILTER_SEXE: FilterSpec = {
  id: 'filtro-sexe',
  stateKey: 'sexes',
  renderSlot: makeSelectSlot('filtro-sexe', 'Sexe'),
  available(personas: Persona[], opciones: OpcionesFiltros): AvailablePayload | null {
    void opciones;
    const vals = personas.map((p) => p.sexe).filter((x): x is number => typeof x === 'number');
    const counts = countByNumber(vals);
    const options = [1, 2]
      .filter((id) => counts.has(id))
      .map((id) => ({
        value: String(id),
        label: `${id === 1 ? 'Home' : 'Dona'} (${counts.get(id) || 0})`,
      }));
    return { id: this.id, stateKey: this.stateKey, options };
  },
  hydrate: hydrateChoices,
  predicate(p: Persona, sel: SelectionShape): boolean {
    const chosen = new Set((sel['sexes'] ?? []).map(Number));
    if (chosen.size === 0) return true;
    return typeof p.sexe === 'number' && chosen.has(p.sexe);
  },
};

// =====================================================
export const FILTER_PARTITS: FilterSpec = {
  id: 'filtro-partits',
  stateKey: 'partits',
  renderSlot: makeSelectSlot('filtro-partits', 'Filiació política'),
  available(personas: Persona[], opciones: OpcionesFiltros): AvailablePayload | null {
    const all: number[] = [];
    personas.forEach((p) => {
      if (Array.isArray(p.filiacio_politica)) all.push(...p.filiacio_politica);
    });
    const counts = countByNumber(all);
    const options = opciones.partits
      .filter((pp) => counts.has(pp.id))
      .map((pp) => {
        const label = pp.sigles ? `${pp.partit_politic} (${pp.sigles})` : pp.partit_politic;
        return { value: String(pp.id), label: `${label} (${counts.get(pp.id) || 0})` };
      })
      .sort((a, b) => a.label.localeCompare(b.label));
    return { id: this.id, stateKey: this.stateKey, options };
  },
  hydrate: hydrateChoices,
  predicate(p: Persona, sel: SelectionShape): boolean {
    const chosen = new Set((sel['partits'] ?? []).map(Number));
    if (chosen.size === 0) return true;
    const poli = p.filiacio_politica ?? [];
    return poli.some((id) => chosen.has(id));
  },
};

// =====================================================
export const FILTER_SINDICATS: FilterSpec = {
  id: 'filtro-sindicats',
  stateKey: 'sindicats',
  renderSlot: makeSelectSlot('filtro-sindicats', 'Filiació sindical'),
  available(personas: Persona[], opciones: OpcionesFiltros): AvailablePayload | null {
    const all: number[] = [];
    personas.forEach((p) => {
      if (Array.isArray(p.filiacio_sindical)) all.push(...p.filiacio_sindical);
    });
    const counts = countByNumber(all);
    const options = opciones.sindicats
      .filter((s) => counts.has(s.id))
      .map((s) => ({ value: String(s.id), label: `${s.sindicat} (${counts.get(s.id) || 0})` }))
      .sort((a, b) => a.label.localeCompare(b.label));
    return { id: this.id, stateKey: this.stateKey, options };
  },
  hydrate: hydrateChoices,
  predicate(p: Persona, sel: SelectionShape): boolean {
    const chosen = new Set((sel['sindicats'] ?? []).map(Number));
    if (chosen.size === 0) return true;
    const sind = p.filiacio_sindical ?? [];
    return sind.some((id) => chosen.has(id));
  },
};

// =====================================================
export const FILTER_CAUSES: FilterSpec = {
  id: 'filtro-causes',
  stateKey: 'causes',
  renderSlot: makeSelectSlot('filtro-causes', 'Causa de defunció'),
  available(personas: Persona[], opciones: OpcionesFiltros): AvailablePayload | null {
    const ids = personas.map((p) => p.causa_defuncio).filter((x): x is number => typeof x === 'number');
    const counts = countByNumber(ids);
    const options = opciones.causes
      .filter((c) => counts.has(c.id))
      .map((c) => ({
        value: String(c.id),
        label: `${c.causa_defuncio_ca} (${counts.get(c.id) || 0})`,
      }))
      .sort((a, b) => a.label.localeCompare(b.label));
    return { id: this.id, stateKey: this.stateKey, options };
  },
  hydrate: hydrateChoices,
  predicate(p: Persona, sel: SelectionShape): boolean {
    const chosen = new Set((sel['causes'] ?? []).map(Number));
    if (chosen.size === 0) return true;
    return typeof p.causa_defuncio === 'number' && chosen.has(p.causa_defuncio);
  },
};

// =====================================================
// CATEGORIA REPRESSIÓ (si la usas en varias páginas, la dejamos como “común”)
export const FILTER_CATEGORIES: FilterSpec = {
  id: 'filtro-categoria',
  stateKey: 'categories',
  renderSlot: makeSelectSlot('filtro-categoria', 'Categoria repressió'),
  available(personas: Persona[], opciones: OpcionesFiltros): AvailablePayload | null {
    const all: number[] = [];
    personas.forEach((p) => {
      if (Array.isArray(p.categoria)) all.push(...p.categoria);
    });
    const counts = countByNumber(all);
    const options = (opciones.categories ?? [])
      .filter((c) => counts.has(c.id))
      .map((c) => {
        const name = c.name ?? c.categoria_ca ?? c.categoria ?? `#${c.id}`;
        return { value: String(c.id), label: `${name} (${counts.get(c.id) || 0})` };
      })
      .sort((a, b) => a.label.localeCompare(b.label));
    return { id: this.id, stateKey: this.stateKey, options };
  },
  hydrate: hydrateChoices,
  predicate(p, sel) {
    const chosen = new Set((sel['categories'] ?? []).map(Number));
    if (chosen.size === 0) return true;

    const cats = new Set(p.categoria ?? []);
    // AND interno: la persona debe tener TODAS las categorías elegidas
    return Array.from(chosen).every((id) => cats.has(id));
  },
};

// =====================================================
// Registro de filtros comunes (ordénalos como quieras que aparezcan)
export const COMMON_FILTERS: FilterSpec[] = [
  FILTER_CATEGORIES, // si la quieres la primera
  FILTER_MUNICIPI_NX,
  FILTER_PROVINCIA,
  FILTER_ANY_NAIX,
  FILTER_ANY_DEF,
  FILTER_ESTAT,
  FILTER_ESTUDIS,
  FILTER_OFICI,
  FILTER_MUNICIPI_DEF,
  FILTER_SEXE,
  FILTER_PARTITS,
  FILTER_SINDICATS,
  FILTER_CAUSES,
];
