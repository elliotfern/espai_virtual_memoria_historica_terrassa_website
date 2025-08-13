// src/buscador/filters/types.ts
import Choices from 'choices.js';
import { OpcionesFiltros, Persona } from '../types';

/** Forma mínima del estado de selección (sin acoplar a store.ts para evitar ciclos). */
export type SelectionShape = Record<string, string[]>;

export type AvailableOption = { value: string; label: string };

export type AvailablePayload = {
  /** id del <select> en el DOM, p.ej. "filtro-any_naixement" */
  id: string;
  /** clave en SelectionState/SelectionShape, p.ej. "anys_naixement" */
  stateKey: string;
  options: AvailableOption[];
};

export interface FilterSpec {
  /** Debe coincidir con el id real del <select>. */
  id: string;
  /** Clave EXACTA en el estado (SelectionState). */
  stateKey: string;

  /** Pinta el slot (label + select) en el contenedor. */
  renderSlot(container: HTMLElement): void;

  /** Devuelve las opciones disponibles (con contadores, etc.) para el estado actual. */
  available(personas: Persona[], opciones: OpcionesFiltros): AvailablePayload | null;

  /** Inicia Choices en el select y devuelve la instancia. */
  hydrate(av: AvailablePayload): Choices;

  /** (Opcional) Predicado de filtrado específico del filtro. */
  predicate?: (p: Persona, sel: SelectionShape) => boolean;
}
