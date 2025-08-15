import { FilterSpec } from './types';
import { COMMON_FILTERS } from './common';
import { GENERAL_FILTERS_ONLY } from './general';

export const GENERAL_FILTERS: FilterSpec[] = [
  ...COMMON_FILTERS, // los de siempre
  ...GENERAL_FILTERS_ONLY, // los propios de esta página (si los hay)
];
