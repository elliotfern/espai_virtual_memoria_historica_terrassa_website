import { formatDatesForm } from '../../../services/formatDates/dates';
import { AfusellatData } from '../../../types/AfusellatData';

export function tab1Afusellat(dada: AfusellatData, htmlContent: string): string {
  htmlContent += `
    <div class="negreta raleway">
        <p><span class='marro2'>Data d'execució:</span> <span class='blau1'>${formatDatesForm(dada.data_execucio)}</span></p>
        <p><span class='marro2'>Lloc d'execució:</span> <span class='blau1'>${dada.lloc_execucio}</span> ${dada.ciutat_execucio && dada.ciutat_execucio.trim() !== '' ? `<span class="normal blau1">(${dada.ciutat_execucio})</span>` : ''}</p>
        <p><span class='marro2'>Lloc d'enterrament:</span> <span class='blau1'>${dada.lloc_enterrament} </span> ${dada.ciutat_enterrament && dada.ciutat_enterrament.trim() !== '' ? `<span class="normal blau1">(${dada.ciutat_enterrament})</span>` : ''}</p>
        ${dada.observacions && dada.observacions.trim() !== '' ? `  <p><span class='marro2'>Observacions:</span> <span class='blau1'> ${dada.observacions}.</span></p>` : ''}
    </div>`;
  return htmlContent;
}
