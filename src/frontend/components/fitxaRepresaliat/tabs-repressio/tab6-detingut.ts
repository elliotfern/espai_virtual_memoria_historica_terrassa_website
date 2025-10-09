import { formatDatesForm } from '../../../services/formatDates/dates';
import { DetingutProcesat } from '../../../types/DetingutProcesat';
import { DEFAULT_LANG, isLang, t } from '../../../services/i18n/i18n';
import { valorTextDesconegut } from '../../../services/formatDates/valorTextDesconegut';
import { LABELS_JUD } from '../../../services/i18n/fitxaRepresaliatRepressio/label-tab6-detingut';

export function tab6Detingut(dada: DetingutProcesat, htmlContent: string, lang: string): string {
  const l = isLang(lang) ? lang : DEFAULT_LANG;

  // Detenció
  const dataDetencio = valorTextDesconegut(
    dada.data_detencio?.trim() ? formatDatesForm(dada.data_detencio) : null,
    4, // unknownDate
    l
  );
  const llocDetencio = valorTextDesconegut(dada.lloc_detencio, 1, l); // noData (tu código original)

  // Procés bàsic
  const tipusProcediment = valorTextDesconegut(dada.tipus_procediment, 2, l); // unknownM
  const tipusJudici = valorTextDesconegut(dada.tipus_judici, 2, l);
  const numCausa = valorTextDesconegut(dada.num_causa, 2, l);

  const anyDetingut = dada.anyDetingut && dada.anyDetingut.trim() !== '' ? `${dada.anyDetingut} ${t(LABELS_JUD, 'years', l)}` : valorTextDesconegut(null, 2, l);

  const anyInici = valorTextDesconegut(dada.any_inicial, 2, l);
  const anyFinal = valorTextDesconegut(dada.any_final, 2, l);

  const dataInici = valorTextDesconegut(dada.data_inici_proces?.trim() ? formatDatesForm(dada.data_inici_proces) : null, 4, l);
  const dataSentencia = valorTextDesconegut(dada.sentencia_data?.trim() ? formatDatesForm(dada.sentencia_data) : null, 4, l);

  // Resolució
  const sentencia = valorTextDesconegut(dada.sentencia, 5, l); // femení: "Desconeguda"
  const pena = valorTextDesconegut(dada.pena, 5, l); // femení
  const commutacio = dada.commutacio?.trim() ? dada.commutacio : '-'; // tal com ho feies

  // Detall
  const jutjat = valorTextDesconegut(dada.jutjat, 2, l);
  const jutgeInstructor = valorTextDesconegut(dada.jutge_instructor, 2, l);
  const secretariInstructor = valorTextDesconegut(dada.secretari_instructor, 2, l);

  const consellGuerraData = valorTextDesconegut(dada.consell_guerra_data?.trim() ? formatDatesForm(dada.consell_guerra_data) : null, 4, l);

  const llocConsellGuerra = valorTextDesconegut(dada.lloc_consell_guerra, 2, l);
  const presidentTribunal = valorTextDesconegut(dada.president_tribunal, 2, l);
  const defensor = valorTextDesconegut(dada.defensor, 2, l);
  const fiscal = valorTextDesconegut(dada.fiscal, 2, l);
  const ponent = valorTextDesconegut(dada.ponent, 2, l);
  const tribunalVocals = valorTextDesconegut(dada.tribunal_vocals, 2, l);
  const acusacio = valorTextDesconegut(dada.acusacio, 5, l); // femení
  const acusacio2 = valorTextDesconegut(dada.acusacio_2, 5, l); // femení
  const testimoniAcusacio = valorTextDesconegut(dada.testimoni_acusacio, 2, l);
  const observacions = valorTextDesconegut(dada.observacions, 1, l); // "Sense dades"

  // Render
  htmlContent += `
    <div class="negreta raleway">

      <div style="margin-top:25px">
        <h5><span class="negreta blau1">${t(LABELS_JUD, 's1_title', l)}</span></h5>
        <p><span class='marro2'>${t(LABELS_JUD, 'arrestDate', l)} </span><span class='blau1'>${dataDetencio}</span></p>
        <p><span class='marro2'>${t(LABELS_JUD, 'arrestPlace', l)}</span> <span class='blau1'>${llocDetencio}</span></p>
      </div>

      <div style="margin-top:45px">
        <h5><span class="negreta blau1">${t(LABELS_JUD, 's2_title', l)}</span></h5>
        <p><span class='marro2'>${t(LABELS_JUD, 'procedureType', l)} </span><span class='blau1'>${tipusProcediment}</span></p>
        <p><span class='marro2'>${t(LABELS_JUD, 'trialType', l)}</span> <span class='blau1'>${tipusJudici}</span></p>
        <p><span class='marro2'>${t(LABELS_JUD, 'caseNumber', l)}</span> <span class='blau1'>${numCausa}</span></p>
        <p><span class='marro2'>${t(LABELS_JUD, 'ageAtArrest', l)}</span> <span class='blau1'>${anyDetingut}</span></p>
        <p><span class='marro2'>${t(LABELS_JUD, 'startYear', l)} </span><span class='blau1'>${anyInici}</span></p>
        <p><span class='marro2'>${t(LABELS_JUD, 'endYear', l)} </span><span class='blau1'>${anyFinal}</span></p>
        <p><span class='marro2'>${t(LABELS_JUD, 'processStartDate', l)} </span><span class='blau1'>${dataInici}</span></p>
        <p><span class='marro2'>${t(LABELS_JUD, 'sentenceDate', l)} </span><span class='blau1'>${dataSentencia}</span></p>
      </div>

      <div style="margin-top:45px">
        <h5><span class="negreta blau1">${t(LABELS_JUD, 's3_title', l)}</span></h5>
        <p><span class='marro2'>${t(LABELS_JUD, 'sentence', l)} </span><span class='blau1'>${sentencia}</span></p>
        <p><span class='marro2'>${t(LABELS_JUD, 'penalty', l)} </span><span class='blau1'>${pena}</span></p>
        <p><span class='marro2'>${t(LABELS_JUD, 'commutation', l)} </span><span class='blau1'>${commutacio}</span></p>
      </div>

      <div style="margin-top:45px">
        <h5><span class="negreta blau1">${t(LABELS_JUD, 's4_title', l)}</span></h5>
        <p><span class='marro2'>${t(LABELS_JUD, 'court', l)} </span><span class='blau1'>${jutjat}</span></p>
        <p><span class='marro2'>${t(LABELS_JUD, 'investigatingJudge', l)} </span><span class='blau1'>${jutgeInstructor}</span></p>
        <p><span class='marro2'>${t(LABELS_JUD, 'investigatingSecretary', l)}</span> <span class='blau1'>${secretariInstructor}</span></p>
        <p><span class='marro2'>${t(LABELS_JUD, 'hearingDate', l)}</span> <span class='blau1'>${consellGuerraData}</span></p>
        <p><span class='marro2'>${t(LABELS_JUD, 'hearingCity', l)}</span> <span class='blau1'>${llocConsellGuerra}</span></p>
        <p><span class='marro2'>${t(LABELS_JUD, 'courtPresident', l)}</span> <span class='blau1'>${presidentTribunal}</span></p>
        <p><span class='marro2'>${t(LABELS_JUD, 'defenseCounsel', l)}</span> <span class='blau1'>${defensor}</span></p>
        <p><span class='marro2'>${t(LABELS_JUD, 'prosecutor', l)}</span> <span class='blau1'>${fiscal}</span></p>
        <p><span class='marro2'>${t(LABELS_JUD, 'reportingJudge', l)}</span> <span class='blau1'>${ponent}</span></p>
        <p><span class='marro2'>${t(LABELS_JUD, 'courtMembers', l)}</span> <span class='blau1'>${tribunalVocals}</span></p>
        <p><span class='marro2'>${t(LABELS_JUD, 'charge', l)}</span> <span class='blau1'>${acusacio}</span></p>
        <p><span class='marro2'>${t(LABELS_JUD, 'charge2', l)}</span> <span class='blau1'>${acusacio2}</span></p>
        <p><span class='marro2'>${t(LABELS_JUD, 'chargeWitness', l)}</span> <span class='blau1'>${testimoniAcusacio}</span></p>
      </div>

      <div style="margin-top:45px">
        <h5><span class="negreta blau1">${t(LABELS_JUD, 's5_title', l)}</span></h5>
        <p><span class='marro2'>${t(LABELS_JUD, 'observations', l)}</span> <span class='blau1'>${observacions}</span></p>
      </div>
    </div>`;

  return htmlContent;
}
