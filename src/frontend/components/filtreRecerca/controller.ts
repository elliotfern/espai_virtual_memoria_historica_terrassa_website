// src/buscador/controller.ts
import type Choices from 'choices.js'; // solo como tipo → no “never used”
import { fetchPersonas, fetchOpcionesFiltros } from './api';
import { PAGE_SIZE, SelectionState, createEmptySelection } from './store';
import { renderResultsPaginated } from './render';
import { OpcionesFiltros, Persona, SortKey } from './types';
import { FilterSpec } from './filters/types';
import type { FetchPersonasOptions, FetchOpcionesOptions } from './api';
import { setMuniToProv } from './prov-map';
import { renderActiveChips } from './chips';

// Extiende Persona con campo precalculado para búsqueda
type PersonaIndexed = Persona & { __search: string };

// input con debounce (en navegador setTimeout devuelve number)
type DebouncedInput = HTMLInputElement & { __deb?: number };

type BuscadorConfig = {
  personas: FetchPersonasOptions; // { type: 'filtreExiliats', lang?, baseUrl? }
  opciones?: FetchOpcionesOptions; // { lang?, baseUrl? }
};

// ---- Mensajes estrictos del Worker ----
type WorkerInitMsg = { type: 'init'; index: Array<{ i: number; s: string }> };
type WorkerFilterMsg = { type: 'filter'; id: number; texto: string };
type WorkerInMsg = WorkerInitMsg | WorkerFilterMsg;

type WorkerReady = { type: 'ready' };
type WorkerFiltered = { type: 'filtered'; id: number; idx: number[] };
type WorkerOutMsg = WorkerReady | WorkerFiltered;

// ---------------------------------------

export class BuscadorController {
  private personas: PersonaIndexed[] = [];
  private opciones!: OpcionesFiltros;
  private resultados: Persona[] = [];
  private sortKey: SortKey = 'cognoms';
  private page = 1;
  private pageSize = PAGE_SIZE;

  private filters: FilterSpec[];
  private choicesMap = new Map<string, Choices>();
  private selection: SelectionState;
  private isHydrating = false;

  private config: BuscadorConfig;

  // rendimiento / UI
  private pendingCriteria = false;

  // worker
  private worker?: Worker;
  private lastMsgId = 0;
  private newestAcceptedId = 0;
  private workerReady = false;

  constructor(filters: FilterSpec[], config: BuscadorConfig) {
    this.filters = filters;
    this.config = config;
    this.selection = createEmptySelection(filters);
  }

  async init(): Promise<void> {
    try {
      const [personas, opciones] = await Promise.all([fetchPersonas(this.config.personas), fetchOpcionesFiltros(this.config.opciones ?? { lang: this.config.personas.lang })]);

      // Pre-normaliza string de búsqueda (minúsculas y concatenado)
      this.personas = personas.map((p) => ({
        ...p,
        __search: `${p.nom ?? ''} ${p.cognom1 ?? ''} ${p.cognom2 ?? ''}`.toLowerCase(),
      }));

      this.opciones = opciones;

      // Mapa municipi -> província (para predicados)
      setMuniToProv(new Map(opciones.municipis.map((m) => [m.id, m.provincia || ''])));

      // Estado inicial
      this.resultados = [...this.personas];

      // Worker: índice compacto {i, s}
      this.ensureWorker();
      const index = this.personas.map((_, i) => ({ i, s: this.personas[i].__search }));
      this.postToWorker({ type: 'init', index });

      // Render de slots y primera hidratación (como tu versión original)
      this.renderFilterSlots();
      this.hydrateFilters(this.selection);

      // Chips iniciales
      renderActiveChips(this.selection, this.opciones, this.filters, this.choicesMap, () => this.scheduleCriteriaChange());

      // Resultados iniciales
      this.applySortAndRender();

      // Buscador texto con debounce
      const buscadorInput = document.getElementById('buscador-nom') as DebouncedInput | null;
      if (buscadorInput) {
        const onSearchInput = (e: Event) => {
          const t = e.target as DebouncedInput;
          if (t.__deb) window.clearTimeout(t.__deb);
          t.__deb = window.setTimeout(() => this.scheduleCriteriaChange(), 200);
        };
        buscadorInput.addEventListener('input', onSearchInput);
      }

      // Reset
      document.getElementById('btn-reset')?.addEventListener('click', () => this.reset());
    } catch (err) {
      // eslint-disable-next-line no-console
      console.error('Error inicialitzant el cercador:', err);
    }
  }

  // ——————————————————— Internals ———————————————————

  /** Crea un Web Worker inline (sin depender de import.meta.url). */
  private createInlineWorker(): Worker {
    const workerCode = `
    let INDEX = [];
    self.onmessage = function(e) {
      const msg = e.data;
      if (msg.type === 'init') {
        INDEX = msg.index || [];
        self.postMessage({ type: 'ready' });
        return;
      }
      if (msg.type === 'filter') {
        const t = (msg.texto || '').trim().toLowerCase();
        if (t.length === 0) {
          const allIdx = INDEX.map(x => x.i);
          self.postMessage({ type: 'filtered', id: msg.id, idx: allIdx });
          return;
        }
        const idx = [];
        for (let k = 0; k < INDEX.length; k++) {
          if (INDEX[k].s.includes(t)) idx.push(INDEX[k].i);
        }
        self.postMessage({ type: 'filtered', id: msg.id, idx });
      }
    };
  `;
    const blob = new Blob([workerCode], { type: 'application/javascript' });
    const url = URL.createObjectURL(blob);
    return new Worker(url); // simple, sin { type: 'module' }
  }

  private ensureWorker(): void {
    if (this.worker) return;
    this.worker = this.createInlineWorker();
    this.worker.onmessage = (e: MessageEvent<WorkerOutMsg>) => {
      const msg = e.data;
      if (msg.type === 'ready') {
        this.workerReady = true;
        return;
      }
      if (msg.type === 'filtered') {
        if (msg.id < this.newestAcceptedId) return; // respuesta obsoleta

        // Subconjunto por texto (índices → personas)
        let filtered: Persona[] = msg.idx.map((i) => this.personas[i]);

        // Aplica predicados de filtros sobre el subconjunto
        this.filters.forEach((spec) => {
          if (typeof spec.predicate === 'function') {
            filtered = filtered.filter((p) => spec.predicate!(p, this.selection));
          }
        });

        this.resultados = filtered;

        // Chips actuales
        renderActiveChips(this.selection, this.opciones, this.filters, this.choicesMap, () => this.scheduleCriteriaChange());

        // Rehidrata selects según resultados (siguiendo tu contrato de FilterSpec)
        this.hydrateFilters(this.selection);

        // Render
        this.applySortAndRender();
      }
    };
  }

  private postToWorker(msg: WorkerInMsg): void {
    this.ensureWorker();
    this.worker!.postMessage(msg);
  }

  private renderFilterSlots(): void {
    const container = document.getElementById('filtros');
    if (!container) return;
    container.innerHTML = '';
    this.filters.forEach((f) => f.renderSlot(container));
  }

  /**
   * Hidratación «contrato original»: usamos available() → hydrate(av)
   * y destruimos/creamos Choices para evitar desajustes de tipos.
   * Se evita cascada de eventos con isHydrating.
   */
  // --- dentro de BuscadorController ---

  /** Aplica predicados de filtros [0..uptoExclusive-1] sobre `rows`. */
  private applyFiltersUpTo(rows: Persona[], uptoExclusive: number, sel = this.selection): Persona[] {
    let out = rows;
    for (let j = 0; j < uptoExclusive; j++) {
      const f = this.filters[j];
      if (typeof f.predicate === 'function') {
        out = out.filter((p) => f.predicate!(p, sel));
      }
    }
    return out;
  }

  /** Asegura que los valores ya seleccionados existen en av.options (si no, los añade con (0)). */
  private ensureSelectedOptions(av: { id: string; stateKey: string; options: { value: string; label: string }[] }, selected: string[]) {
    const present = new Set(av.options.map((o) => o.value));
    const missing = selected.filter((v) => !present.has(v));
    if (missing.length === 0) return;

    // Etiqueta mínima: mantener el value y marcar (0).
    // Si quieres etiquetas “bonitas”, aquí podrías mapear por spec/stateKey a su catálogo.
    for (const v of missing) {
      av.options.push({ value: v, label: `${v} (0)` });
    }
  }

  private hydrateFilters(keep?: SelectionState): void {
    this.isHydrating = true;

    // Destruir previos
    this.choicesMap.forEach((c) => c.destroy());
    this.choicesMap.clear();

    // ⚠️ CASCADA: filtro i ve personas filtradas por [0..i-1], no por sí mismo.
    for (let i = 0; i < this.filters.length; i++) {
      const spec = this.filters[i];

      // Base para available del filtro i
      const baseForI = this.applyFiltersUpTo(this.personas, i, this.selection);

      const av = spec.available(baseForI, this.opciones);
      if (!av) continue;

      // Preserva selección previa de este filtro
      const prevSel = keep?.[spec.stateKey as string] ?? this.selection[spec.stateKey as string] ?? [];

      // No perder lo elegido: añadir opciones faltantes con contador (0)
      this.ensureSelectedOptions(av, prevSel);

      // Hidratar Choices
      const ch = spec.hydrate(av);
      this.choicesMap.set(spec.id, ch);

      // Restaurar selección sin disparar cambios
      if (prevSel && prevSel.length > 0) ch.setChoiceByValue(prevSel);

      ch.passedElement.element.addEventListener('change', () => {
        if (this.isHydrating) return;
        this.scheduleCriteriaChange();
      });
    }

    this.isHydrating = false;
  }

  private readSelection(): SelectionState {
    const sel = createEmptySelection(this.filters);
    this.filters.forEach((spec) => {
      const ch = this.choicesMap.get(spec.id);
      if (ch) {
        // getValue(true) suele devolver string[] (depende de tipos de Choices)
        const values = ch.getValue(true) as unknown as string[];
        sel[spec.stateKey as string] = values ?? [];
      }
    });
    return sel;
  }

  // Debounce micro: agrupa cambios de selects + input en el mismo frame
  private scheduleCriteriaChange(): void {
    if (this.pendingCriteria) return;
    this.pendingCriteria = true;
    queueMicrotask(() => {
      requestAnimationFrame(() => {
        this.pendingCriteria = false;
        this.onCriteriaChangeNow();
      });
    });
  }

  /** Recalcula resultados: Worker para texto + predicados en main thread. */
  private onCriteriaChangeNow(): void {
    this.page = 1;

    const texto = (document.getElementById('buscador-nom') as HTMLInputElement | null)?.value || '';
    const keepBefore = this.readSelection();
    this.selection = keepBefore;

    // Fallback si aún no está listo el worker
    if (!this.workerReady || !this.worker) {
      const t = texto.trim().toLowerCase();
      let filtered: Persona[] = t ? this.personas.filter((p) => p.__search.includes(t)) : this.personas;

      this.filters.forEach((spec) => {
        if (typeof spec.predicate === 'function') {
          filtered = filtered.filter((p) => spec.predicate!(p, this.selection));
        }
      });

      this.resultados = filtered;
      renderActiveChips(this.selection, this.opciones, this.filters, this.choicesMap, () => this.scheduleCriteriaChange());
      this.hydrateFilters(this.selection);
      this.applySortAndRender();
      return;
    }

    // Pide al worker el filtrado por texto
    const id = ++this.lastMsgId;
    this.newestAcceptedId = id;
    const msg: WorkerFilterMsg = { type: 'filter', id, texto };
    this.postToWorker(msg);
  }

  private applySortAndRender(): void {
    const res = renderResultsPaginated(this.resultados, this.opciones, this.sortKey, this.page, this.pageSize, (newPage) => {
      this.page = newPage;
      this.applySortAndRender();
      const resultados = document.getElementById('resultados');
      if (resultados) resultados.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }) ?? { currentPage: this.page, totalPages: 1 };

    this.page = res.currentPage;
  }

  // ——————————————————— API pública ———————————————————

  updateSort(key: SortKey): void {
    this.sortKey = key;
    this.applySortAndRender();
  }

  setPageSize(size: number): void {
    this.pageSize = Math.max(1, size | 0);
    this.page = 1;
    this.applySortAndRender();
  }

  reset(): void {
    const input = document.getElementById('buscador-nom') as HTMLInputElement | null;
    if (input) input.value = '';

    this.page = 1;
    this.selection = createEmptySelection(this.filters);

    this.hydrateFilters(this.selection);

    renderActiveChips(this.selection, this.opciones, this.filters, this.choicesMap, () => this.scheduleCriteriaChange());

    this.onCriteriaChangeNow();
  }
}
