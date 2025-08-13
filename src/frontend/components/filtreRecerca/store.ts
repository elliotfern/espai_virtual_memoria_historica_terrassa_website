// src/buscador/store.ts
import { FilterSpec } from './filters/types';

export type SelectionState = Record<string, string[]>;

export function createEmptySelection(specs: FilterSpec[]): SelectionState {
  const sel: SelectionState = {};
  specs.forEach((s) => {
    sel[s.stateKey as string] = [];
  });
  return sel;
}

export const PAGE_SIZE = 20;
