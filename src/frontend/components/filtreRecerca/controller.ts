// src/buscador/controller.ts
import Choices from 'choices.js';
import { fetchPersonas, fetchOpcionesFiltros } from './api';
import { PAGE_SIZE, SelectionState, createEmptySelection } from './store';
import { renderResultsPaginated } from './render';
import { OpcionesFiltros, Persona, SortKey } from './types';
import { FilterSpec } from './filters/types';
import type { FetchPersonasOptions, FetchOpcionesOptions } from './api';
import { setMuniToProv } from './prov-map';
import { renderActiveChips } from './chips';

// input con debounce (en navegador setTimeout devuelve number)
type DebouncedInput = HTMLInputElement & { __deb?: number };

type BuscadorConfig = {
  personas: FetchPersonasOptions; // { type: 'filtreExiliats', lang?, baseUrl? }
  opciones?: FetchOpcionesOptions; // { lang?, baseUrl? }
};

export class BuscadorController {
  private personas: Persona[] = [];
  private opciones!: OpcionesFiltros;
  private resultados: Persona[] = [];
  private sortKey: SortKey = 'cognoms';
  private page = 1;
  private pageSize = PAGE_SIZE;

  private filters: FilterSpec[];
  private choicesMap = new Map<string, Choices>();
  private selection: SelectionState;

  private config: BuscadorConfig;

  constructor(filters: FilterSpec[], config: BuscadorConfig) {
    this.filters = filters;
    this.config = config;
    this.selection = createEmptySelection(filters);
  }

  async init(): Promise<void> {
    try {
      const [personas, opciones] = await Promise.all([fetchPersonas(this.config.personas), fetchOpcionesFiltros(this.config.opciones ?? { lang: this.config.personas.lang })]);

      this.personas = personas;
      this.opciones = opciones;

      // Poblamos el mapa municipi -> província (lo usan los predicates)
      setMuniToProv(new Map(opciones.municipis.map((m) => [m.id, m.provincia || ''])));

      this.resultados = [...this.personas];

      // Render de “slots” de filtros y carga inicial
      this.renderFilterSlots();
      this.hydrateFilters(); // carga opciones iniciales

      // Chips iniciales (vacío)
      renderActiveChips(this.selection, this.opciones, this.filters, this.choicesMap, () => this.onCriteriaChange());

      // Resultados iniciales
      this.applySortAndRender();

      // Buscador texto con debounce
      const buscadorInput = document.getElementById('buscador-nom') as DebouncedInput | null;
      if (buscadorInput) {
        const onSearchInput = (e: Event) => {
          const t = e.target as DebouncedInput;
          if (t.__deb) window.clearTimeout(t.__deb);
          t.__deb = window.setTimeout(() => this.onCriteriaChange(), 200);
        };
        buscadorInput.addEventListener('input', onSearchInput);
      }

      // Reset
      document.getElementById('btn-reset')?.addEventListener('click', () => this.reset());
    } catch (err) {
      // No lanzamos para no romper la página; solo log
      console.error('Error inicialitzant el cercador:', err);
    }
  }

  // ——————————————————— Internals ———————————————————

  private renderFilterSlots(): void {
    const container = document.getElementById('filtros');
    if (!container) return;
    container.innerHTML = '';
    this.filters.forEach((f) => f.renderSlot(container));
  }

  private hydrateFilters(keep?: SelectionState): void {
    // destruir previos
    this.choicesMap.forEach((c) => c.destroy());
    this.choicesMap.clear();

    // pedir disponibles a cada filtro + hidratar
    this.filters.forEach((spec) => {
      const av = spec.available(this.resultados, this.opciones);
      if (!av) return;

      const ch = spec.hydrate(av);
      this.choicesMap.set(spec.id, ch);

      // restaurar si procede
      const prev = keep?.[spec.stateKey as string];
      if (prev?.length) ch.setChoiceByValue(prev);

      ch.passedElement.element.addEventListener('change', () => this.onCriteriaChange());
    });
  }

  private readSelection(): SelectionState {
    const sel = createEmptySelection(this.filters);
    this.filters.forEach((spec) => {
      const ch = this.choicesMap.get(spec.id);
      if (ch) sel[spec.stateKey as string] = (ch.getValue(true) as string[]) ?? [];
    });
    return sel;
  }

  // Filtro genérico por texto (lo específico va en los predicados de cada filtro)
  private filterAll(personas: Persona[], texto: string, sel: SelectionState): Persona[] {
    void sel; // no lo usamos aquí
    const t = texto.trim().toLowerCase();
    if (!t) return personas;
    return personas.filter((p) => `${p.nom} ${p.cognom1} ${p.cognom2}`.toLowerCase().includes(t));
  }

  private onCriteriaChange(): void {
    this.page = 1;
    const texto = (document.getElementById('buscador-nom') as HTMLInputElement | null)?.value || '';

    const keepBefore = this.readSelection(); // lee todo según specs
    this.selection = keepBefore;

    // 1) filtra por texto (genérico)
    let filtered = this.filterAll(this.personas, texto, this.selection);

    // 2) aplica PREDICADOS de cada filtro (si el filtro define uno)
    this.filters.forEach((spec) => {
      if (typeof spec.predicate === 'function') {
        filtered = filtered.filter((p) => spec.predicate!(p, this.selection));
      }
    });

    this.resultados = filtered;

    // CHIPS: pintar lo que hay ahora mismo seleccionado
    renderActiveChips(this.selection, this.opciones, this.filters, this.choicesMap, () => this.onCriteriaChange());

    // recalcular disponibles según resultados actuales (manteniendo selección válida)
    this.hydrateFilters(keepBefore);

    // Opcional: repintar chips otra vez por si el DOM ha cambiado posiciones
    renderActiveChips(this.selection, this.opciones, this.filters, this.choicesMap, () => this.onCriteriaChange());

    this.applySortAndRender();
  }

  private applySortAndRender(): void {
    const res = renderResultsPaginated(this.resultados, this.opciones, this.sortKey, this.page, this.pageSize, (newPage) => {
      this.page = newPage;
      // re-render con nueva página
      this.applySortAndRender();
      // scroll al inicio de resultados
      const resultados = document.getElementById('resultados');
      if (resultados) resultados.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }) ?? { currentPage: this.page, totalPages: 1 };

    this.page = res.currentPage;
  }

  // ——————————————————— API pública útil ———————————————————

  /** Cambia el criterio de ordenación y repinta. */
  updateSort(key: SortKey): void {
    this.sortKey = key;
    this.applySortAndRender();
  }

  /** Cambia el tamaño de página y vuelve a la primera. */
  setPageSize(size: number): void {
    this.pageSize = Math.max(1, size | 0);
    this.page = 1;
    this.applySortAndRender();
  }

  /** Resetea texto y filtros a estado inicial. */
  reset(): void {
    const input = document.getElementById('buscador-nom') as HTMLInputElement | null;
    if (input) input.value = '';

    this.page = 1;
    this.resultados = [...this.personas];
    this.selection = createEmptySelection(this.filters);

    this.hydrateFilters(this.selection);

    // Chips tras reset (vacío)
    renderActiveChips(this.selection, this.opciones, this.filters, this.choicesMap, () => this.onCriteriaChange());

    this.applySortAndRender();
  }
}
