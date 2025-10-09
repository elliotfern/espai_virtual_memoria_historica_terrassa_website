// 3) Render con helper
import { joinValors } from '../../../services/formatDates/joinValors';
import { valorTextDesconegut } from '../../../services/formatDates/valorTextDesconegut';
import { LABELS_LRP } from '../../../services/i18n/fitxaRepresaliatRepressio/label-tab15-responsabilitatsPolitiques';
import { DEFAULT_LANG, isLang, t } from '../../../services/i18n/i18n';
import { LRPData } from '../../../types/LleiResponsabilitatsPolitiques';

export function tab15ResponsabilitatsPolitiques(dada: LRPData, htmlContent: string, lang: string): string {
  const l = isLang(lang) ? lang : DEFAULT_LANG;

  // Nota: usamos tus códigos del helper:
  // 6 = "No consta cap empresonament"
  // 3 = '' (vacío)
  // 7 = "No consta que marxés a l'exili"
  // 1 = "Sense dades"
  const nomPreso = valorTextDesconegut(dada.lloc_empresonament, 6, l);
  const ciutatPreso = valorTextDesconegut(dada.preso_ciutat, 3, l);
  const empresonament = joinValors([nomPreso, ciutatPreso], ' - ', false);

  const paisExili = valorTextDesconegut(dada.lloc_exili, 7, l);
  const condemna = valorTextDesconegut(dada.condemna, 1, l);
  const observacions = valorTextDesconegut(dada.observacions, 1, l);

  htmlContent += `
    <div class="negreta raleway">
      <div style="margin-top:25px">
        <p><span class='marro2'>${t(LABELS_LRP, 'prisonPlace', l)} </span> <span class='blau1'>${empresonament}</span></p>
        <p><span class='marro2'>${t(LABELS_LRP, 'exileCountry', l)}</span> <span class='blau1'>${paisExili}</span></p>
        <p><span class='marro2'>${t(LABELS_LRP, 'sentenceTitle', l)}</span> <span class='blau1'>${condemna}</span></p>
        <p><span class='marro2'>${t(LABELS_LRP, 'observations', l)} </span> <span class='blau1'>${observacions}</span></p>
      </div>
    </div>
  `;

  return htmlContent;
}
