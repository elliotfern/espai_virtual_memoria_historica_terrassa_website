// src/pages/fitxaRepresaliat/cache.ts
import type { Fitxa, FitxaFamiliars, FitxaJudicial } from '../../types/types';

let fitxaCache: Fitxa | null = null;
let fitxaFamCache: FitxaFamiliars[] | null = null;
const categoriaCache: Record<string, FitxaJudicial | FitxaJudicial[]> = {};

export const cache = {
  getFitxa: (): Fitxa | null => fitxaCache,
  setFitxa: (f: Fitxa | null) => {
    fitxaCache = f;
  },

  getFitxaFam: (): FitxaFamiliars[] | null => fitxaFamCache,
  setFitxaFam: (f: FitxaFamiliars[] | null) => {
    fitxaFamCache = f;
  },

  getCategoria: (key: string) => categoriaCache[key],
  setCategoria(key: string, value: FitxaJudicial | FitxaJudicial[]) {
    categoriaCache[key] = value;
  },

  hasCategoria: (key: string) => Object.prototype.hasOwnProperty.call(categoriaCache, key),

  clearAll: () => {
    fitxaCache = null;
    fitxaFamCache = null;
    for (const k in categoriaCache) delete categoriaCache[k];
  },
};
