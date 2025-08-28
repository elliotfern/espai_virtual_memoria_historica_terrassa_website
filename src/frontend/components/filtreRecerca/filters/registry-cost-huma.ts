import { FilterSpec } from './types';
import { COMMON_FILTERS } from './common';
import { COST_HUMA_ONLY_FILTERS, COST_HUMA_FILTERS_ONLY } from './cost-huma';

export const COST_HUMA_FILTERS: FilterSpec[] = [
  ...COMMON_FILTERS, // los de siempre
  ...COST_HUMA_ONLY_FILTERS, // los propios de esta página (si los hay)
  ...(COST_HUMA_FILTERS_ONLY ?? []), // ← guard
];
