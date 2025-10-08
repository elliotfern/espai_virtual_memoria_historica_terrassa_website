export type Nullable<T> = T | null | undefined;

export interface MortCivilData {
  cirscumstancies_mortId?: Nullable<number>; // 5=bombardeig, 8=extra-judicial, 9=afusellat, etc.
  cirscumstancies_mort?: Nullable<string>;

  // Bombardeig
  data_bombardeig?: Nullable<string>; // ISO/fecha string
  municipi_bombardeig?: Nullable<string>;
  lloc_bombardeig?: Nullable<string>;
  responsable_bombardeig?: Nullable<string | number>; // 1/2/3

  // Extra-judicial (assassinat)
  data_detencio?: Nullable<string>;
  lloc_detencio?: Nullable<string>;
  qui_detencio?: Nullable<string>;

  // Afusellat
  qui_ordena_afusellat?: Nullable<string>;
  qui_executa_afusellat?: Nullable<string>;

  // Dades bàsiques de cadàver
  data_trobada_cadaver?: Nullable<string>;
  lloc_trobada_cadaver?: Nullable<string>;
}
