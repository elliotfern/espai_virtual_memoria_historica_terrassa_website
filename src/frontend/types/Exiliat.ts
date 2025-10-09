// 1) Tipos
export type Nullable<T> = T | null | undefined;

export interface Exiliat {
  data_exili?: Nullable<string>;
  lloc_partida?: Nullable<string>;
  lloc_pas_frontera?: Nullable<string>;
  amb_qui_passa_frontera?: Nullable<string>;

  primer_desti_exili?: Nullable<string>;
  primer_desti_data?: Nullable<string>;
  tipologia_primer_desti?: Nullable<string>;
  dades_lloc_primer_desti?: Nullable<string>;

  periple_recorregut?: Nullable<string>;
  deportat?: Nullable<number>; // 1 sí | 0 no
  participacio_resistencia?: Nullable<number>; // 1 sí | 0 no
  dades_resistencia?: Nullable<string>;
  activitat_politica_exili?: Nullable<string>;
  activitat_sindical_exili?: Nullable<string>;
  situacio_legal_espanya?: Nullable<string>;

  ultim_desti_exili?: Nullable<string>;
  tipologia_ultim_desti?: Nullable<string>;
}
