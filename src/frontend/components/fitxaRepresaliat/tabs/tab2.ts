// src/pages/fitxaRepresaliat/tabs/tab2.ts
import { joinValors } from '../../../services/formatDates/joinValors';
import { valorTextDesconegut } from '../../../services/formatDates/valorTextDesconegut';
import { t } from '../../../services/i18n/i18n';
import { LABELS_TAB2 } from '../../../services/i18n/labels-tab2';
import type { Fitxa, FitxaFamiliars } from '../../../types/types';

export function renderTab2(fitxa: Fitxa, fitxaFam: FitxaFamiliars[] | null, label: string, lang: string): void {
  const divInfo = document.getElementById('fitxa');

  const estatCivil = valorTextDesconegut(fitxa.estat_civil ?? '', 2);

  if (!divInfo) return;

  divInfo.innerHTML = `
  <h3 class="titolSeccio">${label}</h3>
  <p><span class='marro2'>${t(LABELS_TAB2, 'maritalStatus', lang)}:</span> <span class='blau1'>${estatCivil}</span></p>
`;

  // Recorremos el array de familiares y mostramos la información
  if (fitxaFam) {
    divInfo.innerHTML += `<div class="familiar"><p><span class='marro2'>${t(LABELS_TAB2, 'familyList', lang)}:</span></p>`;
    fitxaFam.forEach((familiar) => {
      const nomFamiliar = valorTextDesconegut(familiar.nomFamiliar ?? '', 3, lang);
      const cognom1Familiar = valorTextDesconegut(familiar.cognomFamiliar1 ?? '', 3, lang);
      const cognomFamiliar2 = valorTextDesconegut(familiar.cognomFamiliar2 ?? '', 3, lang);
      const naixementFamiliar = valorTextDesconegut(familiar.anyNaixementFamiliar ?? '', 3, lang);
      const familiarDades = joinValors([nomFamiliar, cognom1Familiar, cognomFamiliar2], ' ', false);

      divInfo.innerHTML += `
      <p><span class='marro2'>${familiar.relacio_parentiu}:</span>
         <span class='blau1'>${familiarDades} ${naixementFamiliar ? `(${naixementFamiliar})` : ''}</span>
      </p>
    `;
    });
    divInfo.innerHTML += `</div>`;
  }
}
