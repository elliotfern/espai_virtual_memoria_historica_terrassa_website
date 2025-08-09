// src/pages/fitxaRepresaliat/tabs/tab7.ts
import type { Fitxa } from '../../../types/types';
import { formatDates } from '../../../services/formatDates/dates';

export function renderTab7(fitxa: Fitxa, label: string): void {
  const divInfo = document.getElementById('fitxa');
  if (!divInfo) return;

  const dataCreacio = fitxa.data_creacio;
  const dataActualitzacio = fitxa.data_actualitzacio;

  divInfo.innerHTML = `
    <h3 class="titolSeccio">${label}</h3>
${fitxa.observacions ? `<p><span class='marro2'>Observacions:</span> <span class='blau1'>${fitxa.observacions}</span></p>` : ''}
          <p><span class='marro2'>Fitxa creada per: </span> <span class='blau1'>${fitxa.autorNom} (${fitxa.biografia_cat})</span></p>
          ${fitxa.autor2Nom ? `<p><span class='marro2'>Revisió dades: </span> <span class='blau1'>${fitxa.autor2Nom}</span></p>` : ''}

          ${fitxa.autor3Nom ? `<p><span class='marro2'>Revisió dades: </span> <span class='blau1'>${fitxa.autor3Nom}</span></p>` : ''}

          ${fitxa.colab1Nom ? `<p><span class='marro2'>Introducció dades: </span> <span class='blau1'>${fitxa.colab1Nom}</span></p>` : ''}

          <p><span class='marro2'>Data de creació: </span><span class='blau1'>${formatDates(dataCreacio)}</span></p>
          <p><span class='marro2'>Darrera actualització: </span><span class='blau1'> ${formatDates(dataActualitzacio)}</span></p>
  `;
}
