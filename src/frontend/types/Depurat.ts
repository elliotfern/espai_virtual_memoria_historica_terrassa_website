export type Nullable<T> = T | null | undefined;

export interface Depurat {
  tipus_professional?: Nullable<number>; // 1 | 2 | 3
  professio?: Nullable<string>;
  empresa?: Nullable<string>;
  sancio?: Nullable<string>;
  observacions?: Nullable<string>;
}
