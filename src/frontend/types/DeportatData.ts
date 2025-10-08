export interface DeportatData {
  // Situació / estat general
  situacio?: string | null;
  situacioId?: number | null; // 1=defunció, 2=alliberament, otro=evasió (según tu lógica)
  estat_mort_allibertament?: number | string | null;

  // Alliberament / mort / evasió
  data_alliberament?: string | null; // fecha base (se formatea con formatDatesForm)
  ciutat_mort_alliberament?: string | null; // municipio asociado (mort/alliberament/evasió)

  // Presó França (bloque “Franca”)
  tipusPresoFranca?: string | null;
  situacioFrancaNom?: string | null;
  situacioFranca_sortida?: string | null; // fecha salida
  ciutat_situacioFranca_preso?: string | null; // municipio de la prisión
  situacioFranca_num_matricula?: string | null; // nº matrícula
  situacioFrancaObservacions?: string | null;

  // Presó 1 (clasificación)
  tipusPreso1?: string | null;
  nomPreso1?: string | null;
  ciutatPreso1?: string | null;
  presoClasificacioData1?: string | null; // fecha (genérica)
  presoClasificacioDataEntrada1?: string | null;
  presoClasificacioMatr1?: string | null;

  // Presó 2 (clasificación)
  tipusPreso2?: string | null;
  nomPreso2?: string | null;
  ciutatPreso2?: string | null;
  presoClasificacioData2?: string | null;
  presoClasificacioDataEntrada2?: string | null;
  presoClasificacioMatr2?: string | null;

  // Deportació (observacions)
  deportacio_observacions?: string | null;

  // Camp 1
  tipusCamp1?: string | null;
  ciutatCamp1?: string | null;
  nomCamp1?: string | null;
  deportacio_data_entrada?: string | null; // fecha de entrada a camp 1
  deportacio_num_matricula?: string | null;

  // Camp 2 / Subcamp
  tipusCamp2?: string | null;
  ciutatCamp2?: string | null;
  nomCamp2?: string | null; // (tu variable "nomSubCamp" viene de aquí)
  deportacio_data_entrada_subcamp?: string | null;
  deportacio_nom_matricula_subcamp?: string | null;

  // Estado de las estancias en prisión (texto libre)
  estat_preso1?: string | null;
  estat_preso2?: string | null;
}
