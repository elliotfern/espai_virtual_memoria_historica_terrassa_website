import { formatDatesForm } from '../../../services/formatDates/dates';
import { LABELS } from '../../../services/i18n/fitxaRepresaliat/labels-tab1';
import { LABELS_DEPORT } from '../../../services/i18n/fitxaRepresaliatRepressio/label-tab2-deportat';
import { DEFAULT_LANG, isLang, t } from '../../../services/i18n/i18n';
import { LABELS_VTD } from '../../../services/i18n/valor-desconegut';
import { DeportatData } from '../../../types/DeportatData';

export function tab2Deportat(dada: DeportatData, htmlContent: string, lang: string): string {
  const l = isLang(lang) ? lang : DEFAULT_LANG;

  // helper cortos
  const orUnknownM = (s?: string | null) => (s && s.trim() !== '' ? s : t(LABELS_VTD, 'unknownM', l));
  const orUnknownF = (s?: string | null) => (s && s.trim() !== '' ? s : t(LABELS_VTD, 'unknownF', l));
  const orNoData = (s?: string | null) => (s && s.trim() !== '' ? s : t(LABELS_VTD, 'noData', l));
  const orUnknownDt = (s?: string | null, fmt?: (x: string) => string) => (s && s.trim() !== '' ? (fmt ? fmt(s) : s) : t(LABELS_VTD, 'unknownDate', l));

  const situacioDeportat = orUnknownM(dada.situacio);
  const situacioId = dada.situacioId;

  let alliberamentMort = '';
  let municipiMort = '';

  if (situacioId === 1) {
    alliberamentMort = t(LABELS, 'dod', l); // "Data de defunció"
    municipiMort = t(LABELS, 'deathTown', l); // "Municipi de defunció"
  } else if (situacioId === 2) {
    alliberamentMort = t(LABELS_DEPORT, 'liberationDate', l);
    municipiMort = t(LABELS_DEPORT, 'liberationTown', l);
  } else {
    alliberamentMort = t(LABELS_DEPORT, 'escapeDate', l);
    municipiMort = t(LABELS_DEPORT, 'escapeTown', l);
  }

  const dataAlliberament = orUnknownDt(dada.data_alliberament, formatDatesForm);
  const municipiAlliberament = orUnknownM(dada.ciutat_mort_alliberament);

  const tipusPreso = orUnknownF(dada.tipusPresoFranca);
  const nomPreso = orUnknownF(dada.situacioFrancaNom);
  const dataSortidaPresoFranca = orUnknownDt(dada.situacioFranca_sortida, formatDatesForm);
  const municipiPreso = orUnknownM(dada.ciutat_situacioFranca_preso);
  const numMatriculaPreso = orUnknownM(dada.situacioFranca_num_matricula);
  const situacioFrancaObservacions = orNoData(dada.situacioFrancaObservacions);

  const estat_mort_allibertament = dada.estat_mort_allibertament;

  const tipusPreso1 = orNoData(dada.tipusPreso1);
  const nomPreso1 = orNoData(dada.nomPreso1);
  const ciutatPreso1 = orNoData(dada.ciutatPreso1);
  const presoClasificacioData1 = orNoData(dada.presoClasificacioData1 ? formatDatesForm(dada.presoClasificacioData1) : '');

  const presoClasificacioDataEntrada1 = orNoData(dada.presoClasificacioDataEntrada1 ? formatDatesForm(dada.presoClasificacioDataEntrada1) : '');
  const presoClasificacioMatr1 = orNoData(dada.presoClasificacioMatr1);

  const presoClasificacioDataEntrada2 = orNoData(dada.presoClasificacioDataEntrada2 ? formatDatesForm(dada.presoClasificacioDataEntrada2) : '');
  const presoClasificacioMatr2 = orNoData(dada.presoClasificacioMatr2);

  const tipusPreso2 = orNoData(dada.tipusPreso2);
  const nomPreso2 = orNoData(dada.nomPreso2);
  const ciutatPreso2 = orNoData(dada.ciutatPreso2);
  const presoClasificacioData2 = orNoData(dada.presoClasificacioData2 ? formatDatesForm(dada.presoClasificacioData2) : '');

  const deportacio_observacions = orNoData(dada.deportacio_observacions);

  const tipusCamp1 = orNoData(dada.tipusCamp1);
  const ciutatCamp1 = orNoData(dada.ciutatCamp1);

  const nomCamp1 = orUnknownM(dada.nomCamp1);
  const deportacio_data_entrada = orUnknownDt(dada.deportacio_data_entrada, formatDatesForm);
  const numeroMatriculaCamp = orUnknownM(dada.deportacio_num_matricula);

  const tipusCamp2 = orNoData(dada.tipusCamp2);
  const ciutatCamp2 = orNoData(dada.ciutatCamp2);
  const nomSubCamp = orUnknownM(dada.nomCamp2);
  const dataEntradaSubCamp = orUnknownDt(dada.deportacio_data_entrada_subcamp, formatDatesForm);
  const numeroMatriculaSubCamp = orUnknownM(dada.deportacio_nom_matricula_subcamp);

  const estat_preso1 = dada.estat_preso1?.trim() ? dada.estat_preso1 : '';
  const estat_preso2 = dada.estat_preso2?.trim() ? dada.estat_preso2 : '';

  htmlContent += `
  <div class="negreta raleway">
    <div style="margin-top:25px">
      <h5><span class="negreta blau1">${t(LABELS_DEPORT, 's1_title', lang)}</span></h5>
      <p><span class='marro2'>${t(LABELS_DEPORT, 'deporteeStatus', lang)} </span><span class='blau1'>${situacioDeportat}</span></p>
      <p><span class='marro2'>${alliberamentMort}:</span> <span class='blau1'>${dataAlliberament}</span></p>
      <p><span class='marro2'>${municipiMort}:</span> <span class='blau1'>${municipiAlliberament} (${estat_mort_allibertament})</span></p>
    </div>

    <div style="margin-top:25px">
      <h5><span class="negreta blau1">${t(LABELS_DEPORT, 's2_title', lang)}</span></h5>
      <p><span class='marro2'>${t(LABELS_DEPORT, 'prisonType', lang)} </span><span class='blau1'>${tipusPreso}</span></p>
      <p><span class='marro2'>${t(LABELS_DEPORT, 'prisonName', lang)} </span><span class='blau1'>${nomPreso}</span></p>
      <p><span class='marro2'>${t(LABELS_DEPORT, 'prisonTown', lang)} </span><span class='blau1'>${municipiPreso}</span></p>
      <p><span class='marro2'>${t(LABELS_DEPORT, 'prisonExitDate', lang)} </span><span class='blau1'>${dataSortidaPresoFranca}</span></p>
      <p><span class='marro2'>${t(LABELS_DEPORT, 'prisonMatricNo', lang)}</span> <span class='blau1'>${numMatriculaPreso}</span></p>
      <p><span class='marro2'>${t(LABELS_DEPORT, 'franceSituationDesc', lang)} </span><span class='blau1'>${situacioFrancaObservacions}</span></p>
    </div>

    <div style="margin-top:25px">
      <h5><span class="negreta blau1">${t(LABELS_DEPORT, 's3_title', lang)}</span></h5>
      <br>
      <h6><span class="blau1 negreta">${t(LABELS_DEPORT, 'firstPrisonHeading', lang)}</span></h6>
      <p><span class='marro2'>${t(LABELS_DEPORT, 'prisonType', lang)} </span><span class='blau1'>${tipusPreso1}</span></p>
      <p><span class='marro2'>${t(LABELS_DEPORT, 'prisonName', lang)} </span><span class='blau1'>${nomPreso1}</span></p>
      <p><span class='marro2'>${t(LABELS_DEPORT, 'prisonTown', lang)} </span><span class='blau1'>${ciutatPreso1} (${estat_preso1})</span></p>
      <p><span class='marro2'>${t(LABELS_DEPORT, 'prisonEntryDate', lang)} </span><span class='blau1'>${presoClasificacioDataEntrada1}</span></p>
      <p><span class='marro2'>${t(LABELS_DEPORT, 'prisonExitDate_short', lang)} </span><span class='blau1'>${presoClasificacioData1}</span></p>
      <p><span class='marro2'>${t(LABELS_DEPORT, 'matricNo', lang)} </span><span class='blau1'>${presoClasificacioMatr1}</span></p>
      <br>
      <h6><span class="blau1 negreta">${t(LABELS_DEPORT, 'secondPrisonHeading', lang)}</span></h6>
      <p><span class='marro2'>${t(LABELS_DEPORT, 'prisonType', lang)} </span><span class='blau1'>${tipusPreso2}</span></p>
      <p><span class='marro2'>${t(LABELS_DEPORT, 'prisonName', lang)} </span><span class='blau1'>${nomPreso2}</span></p>
      <p><span class='marro2'>${t(LABELS_DEPORT, 'prisonTown', lang)} </span><span class='blau1'>${ciutatPreso2} (${estat_preso2})</span></p>
      <p><span class='marro2'>${t(LABELS_DEPORT, 'prisonEntryDate', lang)} </span><span class='blau1'>${presoClasificacioDataEntrada2}</span></p>
      <p><span class='marro2'>${t(LABELS_DEPORT, 'prisonExitDate_short', lang)} </span><span class='blau1'>${presoClasificacioData2}</span></p>
      <p><span class='marro2'>${t(LABELS_DEPORT, 'matricNo', lang)} </span><span class='blau1'>${presoClasificacioMatr2}</span></p>
    </div>

    <div style="margin-top:25px">
      <h5><span class="negreta blau1">${t(LABELS_DEPORT, 's4_title', lang)}</span></h5>
      <br>
      <h6><span class="blau1 negreta">${t(LABELS_DEPORT, 'concCampHeading', lang)}</span></h6>
      <p><span class='marro2'>${t(LABELS_DEPORT, 'campName', lang)} </span><span class='blau1'>${nomCamp1}</span></p>
      <p><span class='marro2'>${t(LABELS_DEPORT, 'campType', lang)} </span><span class='blau1'>${tipusCamp1}</span></p>
      <p><span class='marro2'>${t(LABELS_DEPORT, 'campTown', lang)} </span><span class='blau1'>${ciutatCamp1}</span></p>
      <p><span class='marro2'>${t(LABELS_DEPORT, 'campEntryDate', lang)} </span><span class='blau1'>${deportacio_data_entrada}</span></p>
      <p><span class='marro2'>${t(LABELS_DEPORT, 'campMatricNo', lang)}</span> <span class='blau1'>${numeroMatriculaCamp}</span></p>
      <br>
      <h6><span class="blau1 negreta">${t(LABELS_DEPORT, 'subcampHeading', lang)}</span></h6>
      <p><span class='marro2'>${t(LABELS_DEPORT, 'subcampName', lang)} </span><span class='blau1'>${nomSubCamp}</span></p>
      <p><span class='marro2'>${t(LABELS_DEPORT, 'subcampType', lang)} </span><span class='blau1'>${tipusCamp2}</span></p>
      <p><span class='marro2'>${t(LABELS_DEPORT, 'subcampTown', lang)} </span><span class='blau1'>${ciutatCamp2}</span></p>
      <p><span class='marro2'>${t(LABELS_DEPORT, 'subcampEntryDate', lang)} </span><span class='blau1'>${dataEntradaSubCamp}</span></p>
      <p><span class='marro2'>${t(LABELS_DEPORT, 'subcampMatricNo', lang)}</span> <span class='blau1'>${numeroMatriculaSubCamp}</span></p>
      <br>
      <h6><span class="blau1 negreta">${t(LABELS_DEPORT, 'otherInfoHeading', lang)}</span></h6>
      <p><span class='marro2'>${t(LABELS_DEPORT, 'observations', lang)}</span> <span class='blau1'>${deportacio_observacions}</span></p>
    </div>
  </div>`;

  return htmlContent;
}
