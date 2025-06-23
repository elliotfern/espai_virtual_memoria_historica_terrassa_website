// Definimos la interfaz Fitxa para tipar los datos devueltos por la API
export interface ApiResponse<T> {
  status: 'success' | 'error';
  message?: string;
  errors: string[];
  data: T | null;
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
  empresa_id: number;
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
  data: object;
  procediment_cat: string;
  ciutat_consellGuerra: string;
  data_sentencia: string; // o Date si prefieres
  data_execucio: string; // o Date si prefieres
  espai: string;
  lloc_execucio: string;
  lloc_enterrament: string;
  observacions: string;
  ciutat_execucio: string;
  ciutat_enterrament: string;

  copia_exp: string | null;
  tipus_procediment: string;
  tipus_judici: string;
  num_causa: string;
  data_inici_proces: string; // o Date si lo parseas
  jutge_instructor: string;
  secretari_instructor: string;
  jutjat: string;
  any_inicial: string;
  any_final: string;
  consell_guerra_data: string; // o Date
  lloc_consell_guerra: string;
  president_tribunal: string;
  defensor: string;
  fiscal: string;
  ponent: string;
  tribunal_vocals: string;
  acusacio: string;
  acusacio_2: string;
  testimoni_acusacio: string;
  sentencia_data: string; // o Date
  pena: string;
  sentencia: string;
  commutacio: string;
  anyDetingut: string;
  data_exili: string;
  lloc_partida: string;
  lloc_pas_frontera: string;
  amb_qui_passa_frontera: string;
  primer_desti_exili: string;
  primer_desti_data: string;
  tipologia_primer_desti: string;
  dades_lloc_primer_desti: string;
  periple_recorregut: string;
  deportat: string;
  ultim_desti_exili: string;
  tipologia_ultim_desti: string;
  participacio_resistencia: string;
  dades_resistencia: string;
  activitat_politica_exili: string;
  activitat_sindical_exili: string;
  situacio_legal_espanya: string;
  data_alliberament: string;
  preso_nom: string;
  preso_data_sortida: string;
  preso_num_matricula: string;
  deportacio_nom_camp: string;
  deportacio_data_entrada: string;
  deportacio_num_matricula: string;
  deportacio_nom_subcamp: string;
  deportacio_data_entrada_subcamp: string;
  deportacio_nom_matricula_subcamp: string;
  situacio: string;
  ciutat_mort_alliberament: string;
  lloc_mort_alliberament: string;
  preso_localitat: string;
  preso_tipus: string;
  deportacio_nom_sub: string;
  situacioId: number;
  condicio: string;
  bandol: string;
  any_lleva: string;
  unitat_inicial: string;
  cos: string;
  unitat_final: string;
  graduacio_final: string;
  periple_militar: string;
  circumstancia_mort: string;
  desaparegut_data: string;
  desaparegut_lloc: string;
  desaparegut_data_aparicio: string;
  desaparegut_lloc_aparicio: string;
  cirscumstancies_mortId: number;
  cirscumstancies_mort: string;
  data_trobada_cadaver: string;
  lloc_trobada_cadaver: string;
  data_detencio: string;
  qui_ordena_afusellat: string;
  qui_executa_afusellat: string;
  qui_detencio: string;
  lloc_detencio: string;
  data_bombardeig: string;
  municipi_bombardeig: string;
  lloc_bombardeig: string;
  responsable_bombardeig: number;

  data_empresonament: string;
  trasllats: number;
  lloc_trasllat: string;
  data_trasllat: string;
  llibertat: number;
  data_llibertat: string;
  modalitat: number;
  vicissituds: string;
  tipus_professional: number;
  empresa: string;
  professio: string;
  sancio: string;

  lloc_empresonament: string;
  preso_ciutat: string;
  lloc_exili: string;
  condemna: string;
  data_sortida: string;
  motiu_empresonament: string;
  qui_ordena_detencio: string;
  nom_institucio: string;
  grup: string;
  top: number;
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

export interface HTMLTrixEditorElement extends HTMLElement {
  editor: {
    loadHTML: (html: string) => void;
  };
}
