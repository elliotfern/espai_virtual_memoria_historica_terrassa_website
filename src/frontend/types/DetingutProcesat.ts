// 1) Tipos
export type Nullable<T> = T | null | undefined;

export interface DetingutProcesat {
  // detenció
  data_detencio?: Nullable<string>;
  lloc_detencio?: Nullable<string>;

  // procés bàsic
  tipus_procediment?: Nullable<string>;
  tipus_judici?: Nullable<string>;
  num_causa?: Nullable<string>;
  anyDetingut?: Nullable<string>; // número (string) d’anys
  any_inicial?: Nullable<string>;
  any_final?: Nullable<string>;
  data_inici_proces?: Nullable<string>;
  sentencia_data?: Nullable<string>;

  // resolució
  sentencia?: Nullable<string>;
  pena?: Nullable<string>;
  commutacio?: Nullable<string>; // es mostra "-" si no hi ha dada

  // info detallada
  jutjat?: Nullable<string>;
  jutge_instructor?: Nullable<string>;
  secretari_instructor?: Nullable<string>;
  consell_guerra_data?: Nullable<string>;
  lloc_consell_guerra?: Nullable<string>;
  president_tribunal?: Nullable<string>;
  defensor?: Nullable<string>;
  fiscal?: Nullable<string>;
  ponent?: Nullable<string>;
  tribunal_vocals?: Nullable<string>;
  acusacio?: Nullable<string>;
  acusacio_2?: Nullable<string>;
  testimoni_acusacio?: Nullable<string>;
  observacions?: Nullable<string>;
}
