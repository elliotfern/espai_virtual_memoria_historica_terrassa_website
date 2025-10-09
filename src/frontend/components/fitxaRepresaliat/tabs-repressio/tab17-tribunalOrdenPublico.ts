// 3) Render
import { formatDatesForm } from '../../../services/formatDates/dates';
import { joinValors } from '../../../services/formatDates/joinValors';
import { valorTextDesconegut } from '../../../services/formatDates/valorTextDesconegut';
import { LABELS_TOP } from '../../../services/i18n/fitxaRepresaliatRepressio/label-tab17-tribunalOrdenPublico';
import { DEFAULT_LANG, isLang, t } from '../../../services/i18n/i18n';
import { ProcessatTOPData } from '../../../types/TribunalOrdenPublico';

export function tab16TribunalOrdenPublico(dada: ProcessatTOPData, htmlContent: string, lang: string): string {
  const l = isLang(lang) ? lang : DEFAULT_LANG;

  // num_causa: si falta usamos 6 = "No consta cap empresonament" en tu helper anterior
  const num_causa = valorTextDesconegut(dada.num_causa, 6, l);

  // fecha: si falta, mejor 4 = "Data desconeguda" (puedes cambiar a 1 si prefieres "Sense dades")
  const dataSentFmt = dada.data_sentencia?.trim() ? formatDatesForm(dada.data_sentencia) : null;
  const data_sentencia = valorTextDesconegut(dataSentFmt, 4, l);

  const ciutatPreso = valorTextDesconegut(dada.preso_ciutat, 1, l);
  const nomPreso = valorTextDesconegut(dada.preso, 1, l);
  const empresonament = joinValors([nomPreso, ciutatPreso], ' - ', false);

  const sentencia = valorTextDesconegut(dada.sentencia, 1, l);

  htmlContent += `
    <div class="negreta raleway">
      <div style="margin-top:25px">
        <p><span class='marro2'>${t(LABELS_TOP, 'caseNumber', l)} </span> <span class='blau1'>${num_causa}</span></p>
        <p><span class='marro2'>${t(LABELS_TOP, 'sentenceDate', l)}</span> <span class='blau1'>${data_sentencia}</span></p>
        <p><span class='marro2'>${t(LABELS_TOP, 'sentence', l)}</span> <span class='blau1'>${sentencia}</span></p>
        <p><span class='marro2'>${t(LABELS_TOP, 'prisonPlace', l)} </span> <span class='blau1'>${empresonament}</span></p>
      </div>
    </div>
  `;

  return htmlContent;
}
