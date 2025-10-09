// 1) Tipos
export type Nullable<T> = T | null | undefined;

export interface PresoModel {
  data_empresonament?: Nullable<string>;
  modalitat?: Nullable<string>;
  llibertat?: Nullable<string | number>; // '1' | '2' | '3'
  data_llibertat?: Nullable<string>;

  trasllats?: Nullable<string | number>; // '1' | '2' | '3'
  lloc_trasllat?: Nullable<string>;
  data_trasllat?: Nullable<string>;

  vicissituds?: Nullable<string>;
  observacions?: Nullable<string>;
}
