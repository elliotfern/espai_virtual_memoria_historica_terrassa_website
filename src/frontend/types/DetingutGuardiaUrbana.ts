// 1) Tipos
export type Nullable<T> = T | null | undefined;

export interface DetingutGUData {
  data_empresonament?: Nullable<string>;
  data_sortida?: Nullable<string>;
  motiu_empresonament?: Nullable<string>;
  qui_ordena_detencio?: Nullable<string>;
  nom_institucio?: Nullable<string>;
  grup?: Nullable<string>;
  top?: Nullable<number>; // 1=Sí, 2=No, 3=Sense dades
  observacions?: Nullable<string>;
}
