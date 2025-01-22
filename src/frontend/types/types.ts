// Definimos la interfaz Fitxa para tipar los datos devueltos por la API
export interface FitxaFamiliars {
  cognomFamiliar1?: string;
  cognomFamiliar2?: string;
  nomFamiliar?: string;
  relacio_parentiu?: string;
  anyNaixementFamiliar: string;
}

export interface Fitxa {
  nom: string;
  cognom1: string;
  cognom2: string;
  sexe: string;
  data_naixement: string;
  data_defuncio: string;
  ciutat_naixement: string;
  comarca_naixement: string;
  provincia_naixement: string;
  comunitat_naixement: string;
  pais_naixement: string;
  adreca: string;
  ciutat_residencia: string;
  comarca_residencia: string;
  provincia_residencia: string;
  comunitat_residencia: string;
  pais_residencia: string;
  ciutat_defuncio: string;
  comarca_defuncio: string;
  provincia_defuncio: string;
  comunitat_defuncio: string;
  pais_defuncio: string;
  estudi_cat: string;
  estat_civil: string;
  esposa: string;
  fills_num: number;
  fills_noms: string;
  ofici_cat: string;
  empresa: string;
  carrec_cat: string;
  sector_cat: string;
  sub_sector_cat: string;
  partit_politic: string;
  sindicat: string;
  observacions: string;
  biografia: string;
  ref_num_arxiu: string;
  font_1: string;
  font_2: string;
  data_creacio: string;
  data_actualitzacio: string;
  autorNom: string;
  biografia_cat: string;
  tipologia_espai: string;
  observacions_espai: string;
  causa_defuncio: string;
}

export interface FitxaJudicial {
  procediment_cat: string;
  num_causa: string;
  data_inici_proces: string; // o Date si prefieres
  jutge_instructor: string;
  secretari_instructor: string;
  jutjat: string;
  any_inicial: number; // o string si puede ser un año en formato de texto
  consell_guerra_data: string; // o Date si prefieres
  ciutat_consellGuerra: string;
  president_tribunal: string;
  defensor: string;
  fiscal: string;
  ponent: string;
  tribunal_vocals: string;
  acusacio: string;
  acusacio_2: string;
  testimoni_acusacio: string;
  sentencia_data: string; // o Date si prefieres
  sentencia: string;
  data_sentencia: string; // o Date si prefieres
  data_execucio: string; // o Date si prefieres
  espai: string;
}

// Interfaces para los datos
export interface Represeliat {
  id: number;
  cognom1: string;
  cognom2: string;
  nom: string;
  ciutat: string;
  ciutat2: string;
  categoria: string;
  data_naixement: string;
  data_defuncio: string;
  completat: number;
}
