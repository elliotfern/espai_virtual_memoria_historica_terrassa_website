// Tipos utilitarios
export type ISODateString = string;
export type Nullable<T> = T | null | undefined;
export type ReaparegutFlag = 0 | 1;

export interface MortEnCombatData {
  // 1) Dades bàsiques
  condicio?: Nullable<string>;
  bandol?: Nullable<string>;
  any_lleva?: Nullable<string>;

  // 2) Dades militars
  unitat_inicial?: Nullable<string>;
  cos?: Nullable<string>;
  unitat_final?: Nullable<string>;
  graduacio_final?: Nullable<string>;
  periple_militar?: Nullable<string>;

  // 3) Circumstàncies de la mort / desaparició
  circumstancia_mort?: Nullable<string>;
  desaparegut_data?: Nullable<ISODateString>;
  desaparegut_lloc?: Nullable<string>;

  // 4) Reaparició (si n'hi ha)
  reaparegut?: Nullable<ReaparegutFlag>; // 0 | 1 (el teu codi comprova === 1)
  desaparegut_data_aparicio?: Nullable<ISODateString>;
  desaparegut_lloc_aparicio?: Nullable<string>;
  aparegut_observacions?: Nullable<string>;
}
