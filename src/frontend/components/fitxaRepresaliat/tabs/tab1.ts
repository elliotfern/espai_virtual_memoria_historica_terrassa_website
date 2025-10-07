// src/pages/fitxaRepresaliat/tabs/tab1.ts
import { calcularEdadAlMorir } from '../../../config';
import { formatDatesForm } from '../../../services/formatDates/dates';
import { joinValors } from '../../../services/formatDates/joinValors';
import { valorTextDesconegut } from '../../../services/formatDates/valorTextDesconegut';
import { t } from '../../../services/i18n/i18n';
import { LABELS } from '../../../services/i18n/labels-tab1';
import { getSexeText } from '../../../services/i18n/sex-labels';
import type { Fitxa } from '../../../types/types';

export function renderTab1(fitxa: Fitxa, label: string, lang: string): void {
  const divInfo = document.getElementById('fitxa');

  // variables tab1
  const dataFormatada = formatDatesForm(fitxa.data_naixement);
  const dataNaixement = valorTextDesconegut(dataFormatada, 4, lang);

  const dataFormatada2 = formatDatesForm(fitxa.data_defuncio);
  const dataDefuncio = valorTextDesconegut(dataFormatada2, 4, lang);

  const ciutatNaixement = valorTextDesconegut(fitxa.ciutat_naixement, 2, lang);
  const comarcaNaixement = valorTextDesconegut(fitxa.comarca_naixement, 3, lang);
  const provinciaNaixement = valorTextDesconegut(fitxa.provincia_naixement, 3, lang);
  const comunitatNaixement = valorTextDesconegut(fitxa.comunitat_naixement, 3, lang);
  const paisNaixement = valorTextDesconegut(fitxa.pais_naixement, 3, lang);

  const naixement = joinValors([comarcaNaixement, provinciaNaixement, comunitatNaixement, paisNaixement], ', ', true);

  const adreca = valorTextDesconegut(fitxa.adreca, 3, lang);
  const tipusViaResidencia = valorTextDesconegut(fitxa.tipus_ca, 3, lang);
  const via = joinValors([tipusViaResidencia, adreca, Number.isFinite(fitxa.adreca_num) ? String(fitxa.adreca_num) : ''], ' ', false);

  const ciutatResidencia = valorTextDesconegut(fitxa.ciutat_residencia, 2, lang);

  const adrecaText = joinValors([via, ciutatResidencia], ', ', false);

  const comarcaResidencia = valorTextDesconegut(fitxa.comarca_residencia, 3, lang);
  const provinciaResidencia = valorTextDesconegut(fitxa.provincia_residencia, 3, lang);
  const comunitatResidencia = valorTextDesconegut(fitxa.comunitat_residencia, 3, lang);
  const paisResidencia = valorTextDesconegut(fitxa.pais_residencia, 3, lang);

  const residencia = joinValors([comarcaResidencia, provinciaResidencia, comunitatResidencia, paisResidencia], ', ', true);

  const ciutatDefuncio = valorTextDesconegut(fitxa.ciutat_defuncio, 2, lang);
  const comarcaDefuncio = valorTextDesconegut(fitxa.comarca_defuncio, 3, lang);
  const provinciaDefuncio = valorTextDesconegut(fitxa.provincia_defuncio, 3, lang);
  const comunitatDefuncio = valorTextDesconegut(fitxa.comunitat_defuncio, 3, lang);
  const paisDefuncio = valorTextDesconegut(fitxa.pais_defuncio, 3, lang);

  const defuncio = joinValors([comarcaDefuncio, provinciaDefuncio, comunitatDefuncio, paisDefuncio], ', ', true);

  const tipologiaEspaiDefuncio = valorTextDesconegut(fitxa.tipologia_espai_ca, 4, lang);
  const observacionsTipologiaEspacioDefuncio = valorTextDesconegut(fitxa.observacions_espai, 4, lang);
  const causaDefuncio = valorTextDesconegut(fitxa.causa_defuncio_ca, 4, lang);
  const causa_defuncio_detalls = valorTextDesconegut(fitxa.defuncio_detalls_ca, 2, lang);

  const fechaNacimiento = fitxa.data_naixement;
  const fechaDefuncion = fitxa.data_defuncio;

  let edatAlMorir = 'Desconeguda';
  if (fechaNacimiento && fechaDefuncion) {
    const edat = calcularEdadAlMorir(fechaNacimiento, fechaDefuncion);
    if (edat !== null) {
      edatAlMorir = `${edat} anys`;
    }
  }

  if (!divInfo) return;

  const sexeText = getSexeText(fitxa.sexe, lang);

  divInfo.innerHTML = `
  <h3 class="titolSeccio">${label}</h3>

  <p><span class='marro2'>${t(LABELS, 'sex', lang)}:</span> <span class='blau1'>${sexeText}</span></p>
  <p><span class='marro2'>${t(LABELS, 'dob', lang)}:</span> <span class='blau1'>${dataNaixement}</span></p>
  <p><span class='marro2'>${t(LABELS, 'dod', lang)}:</span> <span class='blau1'>${dataDefuncio}</span></p>
  <p><span class='marro2'>${t(LABELS, 'age', lang)}:</span> <span class='blau1'>${edatAlMorir}</span></p>

  <p><span class='marro2'>${t(LABELS, 'birthTown', lang)}:</span>
     <span class='blau1'>${ciutatNaixement} <span class='normal'>${naixement}</span></span>
  </p>

  <p><span class='marro2'>${t(LABELS, 'residenceAddress', lang)}:</span>
     <span class='blau1'>${adrecaText} <span class='normal'>${residencia}</span></span>
  </p>

  <p><span class='marro2'>${t(LABELS, 'deathTown', lang)}:</span>
     <span class='blau1'>${ciutatDefuncio} <span class='normal'>${defuncio}</span></span>
  </p>

  <p><span class='marro2'>${t(LABELS, 'deathPlaceType', lang)}:</span>
     <span class='blau1'>${tipologiaEspaiDefuncio}</span>
  </p>

  <p><span class='marro2'>${t(LABELS, 'deathPlaceNotes', lang)}:</span>
     <span class='blau1'>${observacionsTipologiaEspacioDefuncio}</span>
  </p>

  <p><span class='marro2'>${t(LABELS, 'deathCause', lang)}:</span>
     <span class='blau1'>${causaDefuncio}</span>
  </p>

  <p><span class='marro2'>${t(LABELS, 'deathCauseDetails', lang)}:</span>
     <span class='blau1'>${causa_defuncio_detalls}</span>
  </p>
`;
}
