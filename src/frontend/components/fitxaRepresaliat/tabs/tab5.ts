// src/pages/fitxaRepresaliat/tabs/tab2.ts
import type { Fitxa } from '../../../types/types';

export function renderTab5(fitxa: Fitxa, label: string): void {
  const divInfo = document.getElementById('fitxa');

  if (!divInfo) return;

  let bioHtml: string;

  if (fitxa.biografiaCa) {
    bioHtml = `<span class='blau1 normal'>${fitxa.biografiaCa}</span>`;
  } else if (fitxa.biografiaEs) {
    bioHtml = `
      <div class="alert alert-warning">
        La biografia en català no està disponible, però hi ha disponible la versió en castellà.
      </div>
      <span class='blau1 normal'>${fitxa.biografiaEs}</span>
    `;
  } else {
    bioHtml = 'La biografia no està disponible.';
  }

  divInfo.innerHTML = `
    <h3 class="titolSeccio">${label}</h3>
    <div style="margin-top:30px;margin-bottom:30px">
        ${bioHtml}
    </div>
      `;
}
