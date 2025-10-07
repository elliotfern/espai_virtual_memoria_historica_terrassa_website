// src/pages/fitxaRepresaliat/tabs/tab7.ts
import type { Fitxa } from '../../../types/types';
import { formatDates } from '../../../services/formatDates/dates';
import { LABELS_TAB7 } from '../../../services/i18n/labels-tab7';
import { t } from '../../../services/i18n/i18n';

export function renderTab7(fitxa: Fitxa, label: string, lang: string): void {
  const divInfo = document.getElementById('fitxa');
  if (!divInfo) return;

  const dataCreacio = fitxa.data_creacio;
  const dataActualitzacio = fitxa.data_actualitzacio;

  divInfo.innerHTML = `
  <h3 class="titolSeccio">${label}</h3>
  ${fitxa.observacions ? `<p><span class='marro2'>${t(LABELS_TAB7, 'observations', lang)}:</span> <span class='blau1'>${fitxa.observacions}</span></p>` : ''}
  <p><span class='marro2'>${t(LABELS_TAB7, 'createdBy', lang)}: </span> <span class='blau1'>${fitxa.autorNom}</span></p>
  ${fitxa.autor2Nom ? `<p><span class='marro2'>${t(LABELS_TAB7, 'dataReview', lang)}: </span> <span class='blau1'>${fitxa.autor2Nom}</span></p>` : ''}
  ${fitxa.autor3Nom ? `<p><span class='marro2'>${t(LABELS_TAB7, 'dataReview', lang)}: </span> <span class='blau1'>${fitxa.autor3Nom}</span></p>` : ''}
  ${fitxa.colab1Nom ? `<p><span class='marro2'>${t(LABELS_TAB7, 'dataEntry', lang)}: </span> <span class='blau1'>${fitxa.colab1Nom}</span></p>` : ''}
  <p><span class='marro2'>${t(LABELS_TAB7, 'creationDate', lang)}: </span><span class='blau1'>${formatDates(dataCreacio)}</span></p>
  <p><span class='marro2'>${t(LABELS_TAB7, 'lastUpdate', lang)}: </span><span class='blau1'>${formatDates(dataActualitzacio)}</span></p>
`;
}
