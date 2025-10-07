// src/pages/fitxaRepresaliat/tabs/tab2.ts
import { valorTextDesconegut } from '../../../services/formatDates/valorTextDesconegut';
import { t } from '../../../services/i18n/i18n';
import { LABELS_TAB3 } from '../../../services/i18n/labels-tab3';
import type { Fitxa } from '../../../types/types';

export function renderTab3(fitxa: Fitxa, label: string, lang: string): void {
  const divInfo = document.getElementById('fitxa');

  if (!divInfo) return;

  const carrecText = fitxa.carrec_cat === '' || fitxa.carrec_cat === null || fitxa.carrec_cat === undefined ? 'Desconegut' : fitxa.carrec_cat;
  const sectorText = fitxa.sector_cat === '' || fitxa.sector_cat === null || fitxa.sector_cat === undefined ? 'Desconegut' : fitxa.sector_cat;
  const subsectorText = fitxa.sub_sector_cat === '' || fitxa.sub_sector_cat === null || fitxa.sub_sector_cat === undefined ? 'Desconegut' : fitxa.sub_sector_cat;
  const empresa = fitxa.empresa === '' || fitxa.empresa === null || fitxa.empresa === undefined ? 'Desconeguda' : fitxa.empresa;

  const estudis = valorTextDesconegut(fitxa.estudi_cat ?? '', 2);
  const ofici = valorTextDesconegut(fitxa.ofici_cat ?? '', 2);

  divInfo.innerHTML = `
  <h3 class="titolSeccio">${label}</h3>
  <p><span class='marro2'>${t(LABELS_TAB3, 'studies', lang)}:</span> <span class='blau1'>${estudis}</span></p>
  <p><span class='marro2'>${t(LABELS_TAB3, 'occupation', lang)}:</span> <span class='blau1'>${ofici}</span></p>
  <p><span class='marro2'>${t(LABELS_TAB3, 'company', lang)}:</span> <span class='blau1'>${empresa}</span></p>
  <p><span class='marro2'>${t(LABELS_TAB3, 'position', lang)}:</span> <span class='blau1'>${carrecText}</span></p>
  <p><span class='marro2'>${t(LABELS_TAB3, 'economicSector', lang)}:</span> <span class='blau1'>${sectorText}</span></p>
  <p><span class='marro2'>${t(LABELS_TAB3, 'economicSubsector', lang)}:</span> <span class='blau1'>${subsectorText}</span></p>
`;
}
