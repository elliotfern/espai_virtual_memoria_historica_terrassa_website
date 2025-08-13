// src/filters/registry.ts
import { FilterSpec } from './types';
import { COMMON_FILTERS } from './common';
import { EXILI_ONLY_FILTERS } from './exili';

export const EXILI_FILTERS: FilterSpec[] = [...COMMON_FILTERS, ...EXILI_ONLY_FILTERS];
