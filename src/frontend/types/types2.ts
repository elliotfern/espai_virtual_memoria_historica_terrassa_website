// types.ts

// Datos principales (simplificado para los filtros)
export interface Represaliat {
  id: number;
  nom: string;
  cognom1: string;
  cognom2: string;
  sexe: 'Home' | 'Dona' | string;
  municipi_naixement: string;
  estat_civil: string;
  estudis: string;
  ofici: number[]; // array de ids de oficio (puede ser varios)
  filiacio_politica: number[]; // array de ids de partidos políticos
  filiacio_sindical: number[]; // array de ids sindicatos
  causa_defuncio: number[]; // array de ids causas de defunción
  completat: number; // 1 o 2
  visibilitat: number; // 1 o 2
  slug: string;
  // ... otros campos
}

// Tipos para listados (municipis, oficis, etc)
export interface Municipi {
  id: number;
  ciutat: string;
  comarca: string;
  provincia: string;
  comunitat: string;
  estat: string;
}

export interface EstatCivil {
  id: number;
  estat_cat: string;
}

export interface Estudis {
  id: number;
  estudi_cat: string;
}

export interface Ofici {
  id: number;
  ofici_cat: string;
}

export interface PartitPolitic {
  id: number;
  partit_politic: string;
  sigles: string | null;
}

export interface Sindicat {
  id: number;
  sindicat: string;
}

export interface CausaDefuncio {
  id: number;
  causa_defuncio: string;
}

// Interfaz para estado de filtros seleccionados
export interface FiltersState {
  sexe: Set<string>; // ej. {'Home','Dona'}
  municipi_naixement: Set<number>; // ids de municipis
  estat_civil: Set<number>;
  estudis: Set<number>;
  ofici: Set<number>;
  filiacio_politica: Set<number>;
  filiacio_sindical: Set<number>;
  causa_defuncio: Set<number>;
  completat: Set<number>;
  visibilitat: Set<number>;
}
