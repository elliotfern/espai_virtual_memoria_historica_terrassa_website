// Definimos la interfaz Fitxa para tipar los datos devueltos por la API
export interface ApiResponse<T> {
  status: 'success' | 'error';
  data?: T;
  message?: string;
}

export interface FitxaFamiliars {
  cognomFamiliar1?: string;
  cognomFamiliar2?: string;
  nomFamiliar?: string;
  relacio_parentiu?: string;
  anyNaixementFamiliar: string;
}

export interface Fitxa {
  id: number;
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
  tipologia_espai_ca: string;
  observacions_espai: string;
  causa_defuncio_ca: string;
  img: string;
  biografiaCa: string;
  biografiaEs: string;
  categoria: string;
  ciutat_naixement_id: number;
  ciutat_defuncio_id: number;
  ciutat_residencia_id: number;
  tipologia_lloc_defuncio_id: number;
  causa_defuncio_id: number;
  estat_civil_id: number;
  estudis_id: number;
  ofici_id: number;
  sector_id: number;
  sub_sector_id: number;
  carrecs_empresa_id: number;
  filiacio_politica: string;
  filiacio_sindical: string;
  activitat_durant_guerra: string;
  autor_id: number;
  completat: number;
  visibilitat: number;
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
  font_intern: number;
  visibilitat: number;
}
