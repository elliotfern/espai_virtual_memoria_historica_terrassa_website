// src/pages/fitxaRepresaliat/tabs/tab2.ts
import { valorTextDesconegut } from '../../../services/formatDates/valorTextDesconegut';
import type { Fitxa } from '../../../types/types';
import { partitsPolitics } from '../partitsPolitics';
import { sindicats } from '../sindicats';

export function renderTab4(fitxa: Fitxa, label: string): void {
  const divInfo = document.getElementById('fitxa');

  if (!divInfo) return;

  // partits politics
  const idsPartidos = fitxa.filiacio_politica
    .replace(/[{}]/g, '')
    .split(',')
    .map((id) => parseInt(id.trim(), 10));

  // Sindicats
  const idsSindicats = fitxa.filiacio_sindical
    .replace(/[{}]/g, '')
    .split(',')
    .map((id) => parseInt(id.trim(), 10));

  Promise.all([partitsPolitics(idsPartidos), sindicats(idsSindicats)]).then(([nombresPartidos, nombresSindicats]) => {
    const partitPolitic = nombresPartidos.length === 0 ? 'Desconegut' : nombresPartidos.join(', ');
    const sindicat = nombresSindicats.length === 0 ? 'Desconegut' : nombresSindicats.join(', ');

    divInfo.innerHTML = `
    <h3 class="titolSeccio">${label}</h3>
    <div style="margin-top:30px;margin-bottom:30px">
                <h5 class="titolSeccio2">Activitat política i sindical abans de l'esclat de la guerra:</h5>
                <p><span class='marro2'>Afiliació política:</span> <span class='blau1'>${partitPolitic}</span></p>
                <p><span class='marro2'>Afiliació sindical:</span> <span class='blau1'>${sindicat}</span></p>
              </div>
        
              <div style="margin-top:30px;margin-bottom:30px">
                <h5 class="titolSeccio2">Activitat política i sindical durant la guerra civil i la dictadura:</h5>
                <p><span class='blau1'>${valorTextDesconegut(fitxa.activitat_durant_guerra, 1)}</span></p>
              </div>

      `;
  });
}
