import { formatDatesForm } from '../../../services/formatDates/dates';
import { LABELS_EXEC } from '../../../services/i18n/fitxaRepresaliatRepressio/label-tab1-afusellat';
import { t } from '../../../services/i18n/i18n';
import { AfusellatData } from '../../../types/AfusellatData';

export function tab1Afusellat(dada: AfusellatData, htmlContent: string, lang: string): string {
  htmlContent += `
  <div class="negreta raleway">
    <p><span class='marro2'>${t(LABELS_EXEC, 'execDate', lang)}:</span>
       <span class='blau1'>${formatDatesForm(dada.data_execucio)}</span></p>

    <p><span class='marro2'>${t(LABELS_EXEC, 'execPlace', lang)}:</span>
       <span class='blau1'>${dada.lloc_execucio}</span>
       ${dada.ciutat_execucio?.trim() ? `<span class="normal blau1">(${dada.ciutat_execucio})</span>` : ''}</p>

    <p><span class='marro2'>${t(LABELS_EXEC, 'burialPlace', lang)}:</span>
       <span class='blau1'>${dada.lloc_enterrament}</span>
       ${dada.ciutat_enterrament?.trim() ? `<span class="normal blau1">(${dada.ciutat_enterrament})</span>` : ''}</p>

    ${
      dada.observacions?.trim()
        ? `<p><span class='marro2'>${t(LABELS_EXEC, 'observations', lang)}:</span>
           <span class='blau1'>${dada.observacions}.</span></p>`
        : ''
    }
  </div>`;
  return htmlContent;
}
