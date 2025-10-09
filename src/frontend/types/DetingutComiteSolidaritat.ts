// 1) Tipos
export type Nullable<T> = T | null | undefined;

export interface DetingutComiteData {
  any_detencio?: Nullable<string>;
  motiu?: Nullable<string>;
  advocat?: Nullable<string>;
  observacions?: Nullable<string>;
}
