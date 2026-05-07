export type Nullable<T> = T | null | undefined;

export type PersonaRelacionada = {
  id: number | string;
  nom?: Nullable<string>;
  cognoms?: Nullable<string>;
  carrec?: Nullable<string>;
};

export interface DetingutProcesat {
  // detenció
  data_detencio?: Nullable<string>;
  lloc_detencio?: Nullable<string>;

  // procés bàsic
  tipus_procediment?: Nullable<string>;
  tipus_judici?: Nullable<string>;
  num_causa?: Nullable<string>;
  anyDetingut?: Nullable<string>;
  any_inicial?: Nullable<string>;
  any_final?: Nullable<string>;
  data_inici_proces?: Nullable<string>;
  sentencia_data?: Nullable<string>;
  consell_guerra_data?: Nullable<string>;
  lloc_consell_guerra?: Nullable<string>;

  // resolució
  sentencia?: Nullable<string>;
  pena?: Nullable<string>;
  commutacio?: Nullable<string>;

  // info detallada (legacy + relacional)
  jutjat?: Nullable<string>;

  // 🔁 RELACIONS NORMALITZADES (NUEVO)
  jutges_instructors?: PersonaRelacionada[];
  secretaris_instructors?: PersonaRelacionada[];
  presidents_tribunal?: PersonaRelacionada[];
  defensors?: PersonaRelacionada[];
  fiscals?: PersonaRelacionada[];
  ponents?: PersonaRelacionada[];
  tribunals_vocals?: PersonaRelacionada[];
  testimonis_acusacions?: PersonaRelacionada[];

  // legacy (si aún lo muestras temporalmente)
  jutge_instructor?: Nullable<string>;
  secretari_instructor?: Nullable<string>;
  president_tribunal?: Nullable<string>;
  defensor?: Nullable<string>;
  fiscal?: Nullable<string>;
  ponent?: Nullable<string>;
  tribunal_vocals?: Nullable<string>;
  testimoni_acusacio?: Nullable<string>;

  // acusacions
  acusacio?: Nullable<string>;
  acusacio_2?: Nullable<string>;

  // otros
  observacions?: Nullable<string>;
}
