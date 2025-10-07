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
  const dataNaixement = valorTextDesconegut(dataFormatada, 4);

  const dataFormatada2 = formatDatesForm(fitxa.data_defuncio);
  const dataDefuncio = valorTextDesconegut(dataFormatada2, 4);

  const ciutatNaixement = valorTextDesconegut(fitxa.ciutat_naixement, 2);
  const comarcaNaixement = valorTextDesconegut(fitxa.comarca_naixement, 3);
  const provinciaNaixement = valorTextDesconegut(fitxa.provincia_naixement, 3);
  const comunitatNaixement = valorTextDesconegut(fitxa.comunitat_naixement, 3);
  const paisNaixement = valorTextDesconegut(fitxa.pais_naixement, 3);

  const naixement = joinValors([comarcaNaixement, provinciaNaixement, comunitatNaixement, paisNaixement], ', ', true);

  const adreca = valorTextDesconegut(fitxa.adreca, 3);
  const tipusViaResidencia = valorTextDesconegut(fitxa.tipus_ca, 3);
  const via = joinValors([tipusViaResidencia, adreca, Number.isFinite(fitxa.adreca_num) ? String(fitxa.adreca_num) : ''], ' ', false);

  const ciutatResidencia = valorTextDesconegut(fitxa.ciutat_residencia, 2);

  const adrecaText = joinValors([via, ciutatResidencia], ', ', false);

  const comarcaResidencia = valorTextDesconegut(fitxa.comarca_residencia, 3);
  const provinciaResidencia = valorTextDesconegut(fitxa.provincia_residencia, 3);
  const comunitatResidencia = valorTextDesconegut(fitxa.comunitat_residencia, 3);
  const paisResidencia = valorTextDesconegut(fitxa.pais_residencia, 3);

  const residencia = joinValors([comarcaResidencia, provinciaResidencia, comunitatResidencia, paisResidencia], ', ', true);

  const ciutatDefuncio = valorTextDesconegut(fitxa.ciutat_defuncio, 2);
  const comarcaDefuncio = valorTextDesconegut(fitxa.comarca_defuncio, 3);
  const provinciaDefuncio = valorTextDesconegut(fitxa.provincia_defuncio, 3);
  const comunitatDefuncio = valorTextDesconegut(fitxa.comunitat_defuncio, 3);
  const paisDefuncio = valorTextDesconegut(fitxa.pais_defuncio, 3);

  const defuncio = joinValors([comarcaDefuncio, provinciaDefuncio, comunitatDefuncio, paisDefuncio], ', ', true);

  const tipologiaEspaiDefuncio = fitxa.tipologia_espai_ca === '' || fitxa.tipologia_espai_ca === null || fitxa.tipologia_espai_ca === undefined ? 'Desconeguda' : fitxa.tipologia_espai_ca;
  const observacionsTipologiaEspacioDefuncio = fitxa.observacions_espai === '' || fitxa.observacions_espai === null || fitxa.observacions_espai === undefined ? 'Desconeguda' : fitxa.observacions_espai;
  const causaDefuncio = fitxa.causa_defuncio_ca === '' || fitxa.causa_defuncio_ca === null || fitxa.causa_defuncio_ca === undefined ? 'Desconeguda' : fitxa.causa_defuncio_ca;
  const causa_defuncio_detalls = fitxa.defuncio_detalls_ca === '' || fitxa.defuncio_detalls_ca === null || fitxa.defuncio_detalls_ca === undefined ? 'Desconegut' : fitxa.defuncio_detalls_ca;

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
