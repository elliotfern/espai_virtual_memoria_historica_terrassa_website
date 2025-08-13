// src/buscador/types.ts
export interface Persona {
  id: number;
  nom: string;
  cognom1: string;
  cognom2: string;
  slug: string;

  municipi_naixement: number;
  provincia_naixement?: string;
  municipi_defuncio?: number;

  causa_defuncio?: number;
  sexe?: number; // 1=Home, 2=Dona
  estat_civil: number;
  estudis: number;
  ofici: number;
  filiacio_politica?: number[];
  filiacio_sindical?: number[];

  // Campos “nuevos”
  data_naixement?: string | null; // "YYYY" o "YYYY-MM-DD"
  data_defuncio?: string | null;
  categoria?: number[];

  // Exili / deportació
  data_exili?: string | null;
  primer_desti_exili?: number | null; // id de municipi
  deportat?: number | null; // 1 sí, 2 no
  participacio_resistencia: number | null; // 1 sí, 2 no
}

export interface Municipio {
  id: number;
  ciutat: string;
  provincia: string;
}
export interface EstatCivil {
  id: number;
  estat_cat: string;
}
export interface Estudi {
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
export interface Causa {
  id: number;
  causa_defuncio_ca: string;
}
export interface CategoriaRepressio {
  id: number;
  name: string;
  categoria?: string;
  categoria_ca?: string;
}

export interface OpcionesFiltros {
  municipis: Municipio[];
  estats_civils: EstatCivil[];
  estudis: Estudi[];
  oficis: Ofici[];
  partits: PartitPolitic[];
  sindicats: Sindicat[];
  causes: Causa[];
  categories: CategoriaRepressio[];
}

export type SortKey = 'cognoms' | 'nom' | 'municipi';
