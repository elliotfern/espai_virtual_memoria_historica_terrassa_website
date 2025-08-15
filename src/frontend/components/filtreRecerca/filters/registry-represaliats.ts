import { FilterSpec } from './types';
import { COMMON_FILTERS } from './common';
import { REPRESALIATS_FILTERS_ONLY } from './represaliats';

export const REPRESALIATS_FILTERS: FilterSpec[] = [
  ...COMMON_FILTERS, // los de siempre
  ...REPRESALIATS_FILTERS_ONLY, // los propios de esta página (si los hay)
];
