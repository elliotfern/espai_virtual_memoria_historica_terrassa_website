// src/pages/fitxaRepresaliat/tabs/tab2.ts
import { t } from '../../../services/i18n/i18n';
import { LABELS_TAB5 } from '../../../services/i18n/labels-tab5';
import type { Fitxa } from '../../../types/types';

export function renderTab5(fitxa: Fitxa, label: string, lang: string): void {
  const divInfo = document.getElementById('fitxa');

  if (!divInfo) return;

  let bioHtml = '';

  if (lang === 'ca') {
    if (fitxa.biografiaCa) {
      bioHtml = `<span class='blau1 normal'>${fitxa.biografiaCa}</span>`;
    } else if (fitxa.biografiaEs) {
      if (!fitxa.biografiaCa && fitxa.biografiaEs) {
        bioHtml = `
          <div class="alert alert-warning">
            ${t(LABELS_TAB5, 'bioWarnCaMissingEsAvailable', lang)}
          </div>
          <span class='blau1 normal'>${fitxa.biografiaEs}</span>
        `;
      } else if (!fitxa.biografiaCa && !fitxa.biografiaEs) {
        bioHtml = t(LABELS_TAB5, 'bioUnavailable', lang);
      }
    }
  } else if (lang === 'es') {
    if (!fitxa.biografiaEs) {
      bioHtml = `
          <div class="alert alert-warning">
            ${t(LABELS_TAB5, 'bioUnavailable', lang)}
          </div>`;
    } else {
      bioHtml = `<span class='blau1 normal'>${fitxa.biografiaEs}</span>`;
    }
  } else if (lang === 'en') {
    if (!fitxa.biografiaEn) {
      bioHtml = `
          <div class="alert alert-warning">
            ${t(LABELS_TAB5, 'bioUnavailable', lang)}
          </div>`;
    } else {
      bioHtml = `<span class='blau1 normal'>${fitxa.biografiaEn}</span>`;
    }
  } else if (lang === 'fr') {
    if (!fitxa.biografiaFr) {
      bioHtml = `
          <div class="alert alert-warning">
            ${t(LABELS_TAB5, 'bioUnavailable', lang)}
          </div>`;
    } else {
      bioHtml = `<span class='blau1 normal'>${fitxa.biografiaFr}</span>`;
    }
  } else if (lang === 'it') {
    if (!fitxa.biografiaIt) {
      bioHtml = `
          <div class="alert alert-warning">
            ${t(LABELS_TAB5, 'bioUnavailable', lang)}
          </div>`;
    } else {
      bioHtml = `<span class='blau1 normal'>${fitxa.biografiaIt}</span>`;
    }
  } else if (lang === 'pt') {
    if (!fitxa.biografiaPt) {
      bioHtml = `
          <div class="alert alert-warning">
            ${t(LABELS_TAB5, 'bioUnavailable', lang)}
          </div>`;
    } else {
      bioHtml = `<span class='blau1 normal'>${fitxa.biografiaPt}</span>`;
    }
  }

  divInfo.innerHTML = `
    <h3 class="titolSeccio">${label}</h3>
    <div style="margin-top:30px;margin-bottom:30px">
        ${bioHtml}
    </div>
      `;
}
