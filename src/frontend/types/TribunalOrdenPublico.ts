// 1) Tipos
export type Nullable<T> = T | null | undefined;

export interface ProcessatTOPData {
  num_causa?: Nullable<string>;
  data_sentencia?: Nullable<string>;
  sentencia?: Nullable<string>;
  preso?: Nullable<string>; // nombre prisión
  preso_ciutat?: Nullable<string>; // ciudad prisión
}
