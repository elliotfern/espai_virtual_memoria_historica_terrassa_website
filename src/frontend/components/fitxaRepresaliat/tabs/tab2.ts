// src/pages/fitxaRepresaliat/tabs/tab2.ts
import { joinValors } from '../../../services/formatDates/joinValors';
import { valorTextDesconegut } from '../../../services/formatDates/valorTextDesconegut';
import type { Fitxa, FitxaFamiliars } from '../../../types/types';

export function renderTab2(fitxa: Fitxa, fitxaFam: FitxaFamiliars[] | null, label: string): void {
  const divInfo = document.getElementById('fitxa');

  if (!divInfo) return;

  divInfo.innerHTML = `
    <h3 class="titolSeccio">${label}</h3>
        <p><span class='marro2'>Estat civil:</span> <span class='blau1'>${fitxa.estat_civil}</span></p>
      `;

  // Recorremos el array de familiares y mostramos la información
  if (fitxaFam) {
    divInfo.innerHTML += `<div class="familiar"><p><span class='marro2'>Relació de familiars:</span></p>`;
    fitxaFam.forEach((familiar) => {
      const nomFamiliar = valorTextDesconegut(familiar.nomFamiliar ?? '', 3);
      const cognom1Familiar = valorTextDesconegut(familiar.cognomFamiliar1 ?? '', 3);
      const cognomFamiliar2 = valorTextDesconegut(familiar.cognomFamiliar2 ?? '', 3);
      const naixementFamiliar = valorTextDesconegut(familiar.anyNaixementFamiliar ?? '', 3);
      const familiarDades = joinValors([nomFamiliar, cognom1Familiar, cognomFamiliar2], ' ', false);

      divInfo.innerHTML += `
        <p><span class='marro2'>${familiar.relacio_parentiu}:</span> <span class='blau1'>${familiarDades} ${naixementFamiliar ? `(${naixementFamiliar})` : ''}</span> </p>
      `;
    });
    divInfo.innerHTML += `</div>`;
  }
}
