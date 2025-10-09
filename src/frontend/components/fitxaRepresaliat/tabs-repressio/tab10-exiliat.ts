import { formatDatesForm } from '../../../services/formatDates/dates';
import { valorTextDesconegut } from '../../../services/formatDates/valorTextDesconegut';
import { LABELS_EXILI, LABELS_YESNO } from '../../../services/i18n/fitxaRepresaliatRepressio/label-tab10-exiliat';
import { DEFAULT_LANG, isLang, t } from '../../../services/i18n/i18n';
import { Exiliat } from '../../../types/Exiliat';

export function tab10Exiliat(dada: Exiliat, htmlContent: string, lang: string): string {
  const l = isLang(lang) ? lang : DEFAULT_LANG;

  // 1) Sortida
  const dataExili = valorTextDesconegut(
    dada.data_exili?.trim() ? formatDatesForm(dada.data_exili) : null,
    1, // noData (en tus ejemplos ponías "Sense dades")
    l
  );
  const lloc_partida = valorTextDesconegut(dada.lloc_partida, 1, l);
  const llocPas = valorTextDesconegut(dada.lloc_pas_frontera, 1, l);
  const ambQuiPasaFrontera = valorTextDesconegut(dada.amb_qui_passa_frontera, 1, l);

  // 2) Arribada
  const primerMunicipiExili = valorTextDesconegut(dada.primer_desti_exili, 1, l);
  const dataPrimerDesti = valorTextDesconegut(dada.primer_desti_data?.trim() ? formatDatesForm(dada.primer_desti_data) : null, 1, l);
  const tipologiaPrimerDesti = valorTextDesconegut(dada.tipologia_primer_desti, 1, l);
  const dadesPrimerDesti = valorTextDesconegut(dada.dades_lloc_primer_desti, 1, l);

  // 3) Periple / flags
  const peripleExili = valorTextDesconegut(dada.periple_recorregut, 1, l);
  const deportat = (dada.deportat ?? 0) === 1 ? t(LABELS_YESNO, 'yes', l) : t(LABELS_YESNO, 'no', l);

  // 4) Resistència i activitats
  const resistencia = (dada.participacio_resistencia ?? 0) === 1 ? t(LABELS_YESNO, 'yes', l) : t(LABELS_YESNO, 'no', l);

  const dadesResistencia = valorTextDesconegut(dada.dades_resistencia, 1, l);
  const activitatPolitica = valorTextDesconegut(dada.activitat_politica_exili, 1, l);
  const activitatSindical = valorTextDesconegut(dada.activitat_sindical_exili, 1, l);
  const situacioEspanya = valorTextDesconegut(dada.situacio_legal_espanya, 1, l);

  // 5) Final
  const darrerDestiExili = valorTextDesconegut(dada.ultim_desti_exili, 1, l);
  const tipologiaDarrerDesti = valorTextDesconegut(dada.tipologia_ultim_desti, 1, l);

  // Render
  htmlContent += `
    <div class="negreta raleway">
      <div style="margin-top:25px">
        <h5><span class="negreta blau1">${t(LABELS_EXILI, 's1_title', l)}</span></h5>
        <p><span class='marro2'>${t(LABELS_EXILI, 'exileDate', l)} </span><span class='blau1'>${dataExili}</span></p>
        <p><span class='marro2'>${t(LABELS_EXILI, 'departurePlace', l)}</span> <span class='blau1'>${lloc_partida}</span></p>
        <p><span class='marro2'>${t(LABELS_EXILI, 'borderCrossingPlace', l)}</span> <span class='blau1'>${llocPas}</span></p>
        <p><span class='marro2'>${t(LABELS_EXILI, 'withWhomExiles', l)}</span> <span class='blau1'>${ambQuiPasaFrontera}</span></p>
      </div>

      <div style="margin-top:45px">
        <h5><span class="negreta blau1">${t(LABELS_EXILI, 's2_title', l)}</span></h5>
        <p><span class='marro2'>${t(LABELS_EXILI, 'firstDestMunicipality', l)} </span><span class='blau1'>${primerMunicipiExili}</span></p>
        <p><span class='marro2'>${t(LABELS_EXILI, 'firstDestDate', l)} </span><span class='blau1'>${dataPrimerDesti}</span></p>
        <p><span class='marro2'>${t(LABELS_EXILI, 'firstDestType', l)} </span><span class='blau1'>${tipologiaPrimerDesti}</span></p>
        <p><span class='marro2'>${t(LABELS_EXILI, 'firstDestData', l)} </span><span class='blau1'>${dadesPrimerDesti}</span></p>
      </div>

      <div style="margin-top:45px">
        <h5><span class="negreta blau1">${t(LABELS_EXILI, 's3_title', l)}</span></h5>
        <p><span class='marro2'>${t(LABELS_EXILI, 'journey', l)} </span><span class='blau1'>${peripleExili}</span></p>
        <p><span class='marro2'>${t(LABELS_EXILI, 'deportedToNaziCamps', l)} </span><span class='blau1'>${deportat}</span></p>
      </div>

      <div style="margin-top:45px">
        <h5><span class="negreta blau1">${t(LABELS_EXILI, 's4_title', l)}</span></h5>
        <p><span class='marro2'>${t(LABELS_EXILI, 'resistanceParticipation', l)} </span><span class='blau1'>${resistencia}</span></p>
        <p><span class='marro2'>${t(LABELS_EXILI, 'resistanceData', l)} </span><span class='blau1'>${dadesResistencia}</span></p>
        <p><span class='marro2'>${t(LABELS_EXILI, 'politicalActivity', l)} </span><span class='blau1'>${activitatPolitica}</span></p>
        <p><span class='marro2'>${t(LABELS_EXILI, 'unionActivity', l)} </span><span class='blau1'>${activitatSindical}</span></p>
        <p><span class='marro2'>${t(LABELS_EXILI, 'legalStatusSpain', l)} </span><span class='blau1'>${situacioEspanya}</span></p>
      </div>

      <div style="margin-top:45px">
        <h5><span class="negreta blau1">${t(LABELS_EXILI, 's5_title', l)}</span></h5>
        <p><span class='marro2'>${t(LABELS_EXILI, 'lastDestMunicipality', l)} </span><span class='blau1'>${darrerDestiExili}</span></p>
        <p><span class='marro2'>${t(LABELS_EXILI, 'lastDestType', l)} </span><span class='blau1'>${tipologiaDarrerDesti}</span></p>
      </div>
    </div>
  `;

  return htmlContent;
}
