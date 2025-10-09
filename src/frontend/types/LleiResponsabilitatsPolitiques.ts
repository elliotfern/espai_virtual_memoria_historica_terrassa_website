// 1) Tipos
export type Nullable<T> = T | null | undefined;

export interface LRPData {
  lloc_empresonament?: Nullable<string>; // nombre prisión
  preso_ciutat?: Nullable<string>; // ciudad prisión
  lloc_exili?: Nullable<string>; // país de exilio
  condemna?: Nullable<string>;
  observacions?: Nullable<string>;
}
