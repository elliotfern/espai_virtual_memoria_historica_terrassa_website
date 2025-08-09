// src/pages/fitxaRepresaliat/tabs/tab2.ts
import type { Fitxa } from '../../../types/types';

export function renderTab3(fitxa: Fitxa, label: string): void {
  const divInfo = document.getElementById('fitxa');

  if (!divInfo) return;

  const carrecText = fitxa.carrec_cat === '' || fitxa.carrec_cat === null || fitxa.carrec_cat === undefined ? 'Desconegut' : fitxa.carrec_cat;
  const sectorText = fitxa.sector_cat === '' || fitxa.sector_cat === null || fitxa.sector_cat === undefined ? 'Desconegut' : fitxa.sector_cat;
  const subsectorText = fitxa.sub_sector_cat === '' || fitxa.sub_sector_cat === null || fitxa.sub_sector_cat === undefined ? 'Desconegut' : fitxa.sub_sector_cat;
  const empresa = fitxa.empresa === '' || fitxa.empresa === null || fitxa.empresa === undefined ? 'Desconeguda' : fitxa.empresa;

  divInfo.innerHTML = `
    <h3 class="titolSeccio">${label}</h3>
        <p><span class='marro2'>Estudis:</span> <span class='blau1'>${fitxa.estudi_cat}</span></p>
        <p><span class='marro2'>Ofici:</span> <span class='blau1'>${fitxa.ofici_cat}</span></p>
        <p><span class='marro2'>Empresa:</span> <span class='blau1'>${empresa}</span></p>
        <p><span class='marro2'>Càrrec:</span> <span class='blau1'>${carrecText}</span></p>
        <p><span class='marro2'>Sector econòmic:</span> <span class='blau1'>${sectorText}</span></p>
        <p><span class='marro2'>Sub-sector econòmic:</span> <span class='blau1'>${subsectorText}</span></p>
      `;
}
