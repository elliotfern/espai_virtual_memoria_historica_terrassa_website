// src/pages/fitxaRepresaliat/tabs/tab1.ts
import { calcularEdadAlMorir } from '../../../config';
import { formatDatesForm } from '../../../services/formatDates/dates';
import { joinValors } from '../../../services/formatDates/joinValors';
import { valorTextDesconegut } from '../../../services/formatDates/valorTextDesconegut';
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

  type Lang = 'ca' | 'es' | 'en' | 'fr' | 'it' | 'pt';

  const LABELS: Record<Lang, Record<string, string>> = {
    ca: {
      sex: 'Sexe',
      dob: 'Data de naixement',
      dod: 'Data de defunció',
      age: 'Edat',
      birthTown: 'Municipi de naixement',
      residenceAddress: 'Adreça de residència',
      deathTown: 'Municipi de defunció',
      deathPlaceType: 'Tipologia espai de defunció',
      deathPlaceNotes: 'Observacions espai de defunció',
      deathCause: 'Causa de la defunció',
      deathCauseDetails: 'Detalls causa de la defunció',
    },
    es: {
      sex: 'Sexo',
      dob: 'Fecha de nacimiento',
      dod: 'Fecha de defunción',
      age: 'Edad',
      birthTown: 'Municipio de nacimiento',
      residenceAddress: 'Dirección de residencia',
      deathTown: 'Municipio de defunción',
      deathPlaceType: 'Tipología del espacio de defunción',
      deathPlaceNotes: 'Observaciones del espacio de defunción',
      deathCause: 'Causa de la defunción',
      deathCauseDetails: 'Detalles de la causa de la defunción',
    },
    en: {
      sex: 'Sex',
      dob: 'Date of birth',
      dod: 'Date of death',
      age: 'Age',
      birthTown: 'Birth municipality',
      residenceAddress: 'Residential address',
      deathTown: 'Municipality of death',
      deathPlaceType: 'Type of place of death',
      deathPlaceNotes: 'Notes on place of death',
      deathCause: 'Cause of death',
      deathCauseDetails: 'Details on cause of death',
    },
    fr: {
      sex: 'Sexe',
      dob: 'Date de naissance',
      dod: 'Date de décès',
      age: 'Âge',
      birthTown: 'Commune de naissance',
      residenceAddress: 'Adresse de résidence',
      deathTown: 'Commune de décès',
      deathPlaceType: 'Typologie du lieu du décès',
      deathPlaceNotes: 'Observations sur le lieu du décès',
      deathCause: 'Cause du décès',
      deathCauseDetails: 'Détails sur la cause du décès',
    },
    it: {
      sex: 'Sesso',
      dob: 'Data di nascita',
      dod: 'Data di morte',
      age: 'Età',
      birthTown: 'Comune di nascita',
      residenceAddress: 'Indirizzo di residenza',
      deathTown: 'Comune di morte',
      deathPlaceType: 'Tipologia del luogo del decesso',
      deathPlaceNotes: 'Osservazioni sul luogo del decesso',
      deathCause: 'Causa del decesso',
      deathCauseDetails: 'Dettagli sulla causa del decesso',
    },
    pt: {
      sex: 'Sexo',
      dob: 'Data de nascimento',
      dod: 'Data de óbito',
      age: 'Idade',
      birthTown: 'Município de nascimento',
      residenceAddress: 'Morada de residência',
      deathTown: 'Município do óbito',
      deathPlaceType: 'Tipologia do local do óbito',
      deathPlaceNotes: 'Observações sobre o local do óbito',
      deathCause: 'Causa do óbito',
      deathCauseDetails: 'Detalhes da causa do óbito',
    },
  };

  function t(key: keyof (typeof LABELS)['ca'], lang: string): string {
    const l = (['ca', 'es', 'en', 'fr', 'it', 'pt'] as Lang[]).includes(lang as Lang) ? (lang as Lang) : 'ca';
    return LABELS[l][key];
  }

  const SEX_LABELS: Record<Lang, { '1': string; '2': string; unknown: string }> = {
    ca: { '1': 'Home', '2': 'Dona', unknown: 'desconegut' },
    es: { '1': 'Hombre', '2': 'Mujer', unknown: 'desconocido' },
    en: { '1': 'Male', '2': 'Female', unknown: 'unknown' },
    fr: { '1': 'Homme', '2': 'Femme', unknown: 'inconnu' },
    it: { '1': 'Uomo', '2': 'Donna', unknown: 'sconosciuto' },
    pt: { '1': 'Homem', '2': 'Mulher', unknown: 'desconhecido' },
  };

  function isLang(x: string): x is Lang {
    return ['ca', 'es', 'en', 'fr', 'it', 'pt'].includes(x);
  }

  function getSexeText(sexe: unknown, lang: string): string {
    const l: Lang = isLang(lang) ? lang : 'ca';
    const v = Number(sexe);
    if (v === 1) return SEX_LABELS[l]['1'];
    if (v === 2) return SEX_LABELS[l]['2'];
    return SEX_LABELS[l].unknown;
  }

  // Uso:
  const sexeText = getSexeText(fitxa.sexe, lang);

  divInfo.innerHTML = `
        <h3 class="titolSeccio">${label}</h3>
    <p><span class='marro2'>${t('sex', lang)}:</span> <span class='blau1'>${sexeText}</span></p>
    <p><span class='marro2'>${t('dob', lang)}:</span> <span class='blau1'>${dataNaixement}</span></p>
    <p><span class='marro2'>${t('dod', lang)}:</span> <span class='blau1'>${dataDefuncio}</span></p>
    <p><span class='marro2'>${t('age', lang)}:</span> <span class='blau1'>${edatAlMorir}</span></p>

    <p><span class='marro2'>${t('birthTown', lang)}:</span> <span class='blau1'>${ciutatNaixement} <span class='normal'>${naixement}</span></span></p>

    <p><span class='marro2'>${t('residenceAddress', lang)}:</span> <span class='blau1'>${adrecaText} <span class='normal'>${residencia}</span></span></p>
    <p><span class='marro2'>${t('deathTown', lang)}:</span> <span class='blau1'>${ciutatDefuncio} <span class='normal'>${defuncio}</span></span></p>
    <p><span class='marro2'>${t('deathPlaceType', lang)}:</span> <span class='blau1'>${tipologiaEspaiDefuncio}</span></p>
    <p><span class='marro2'>${t('deathPlaceNotes', lang)}:</span> <span class='blau1'>${observacionsTipologiaEspacioDefuncio}</span></p>
    <p><span class='marro2'>${t('deathCause', lang)}:</span> <span class='blau1'>${causaDefuncio}</span></p>
    <p><span class='marro2'>${t('deathCauseDetails', lang)}:</span> <span class='blau1'>${causa_defuncio_detalls}</span></p>
  `;
}
