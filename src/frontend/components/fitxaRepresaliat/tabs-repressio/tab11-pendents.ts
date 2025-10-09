import { LABELS_PENDING } from '../../../services/i18n/fitxaRepresaliatRepressio/label-11-pendent';
import { DEFAULT_LANG, isLang, t } from '../../../services/i18n/i18n';

export function tab11Pendent(htmlContent: string, lang: string): string {
  const l = isLang(lang) ? lang : DEFAULT_LANG;

  htmlContent += `
    <div class="negreta raleway">
      <div style="margin-top:25px">
        <h5><span class="negreta blau1">${t(LABELS_PENDING, 'pendingNoData', l)}</span></h5>
      </div>
    </div>
  `;

  return htmlContent;
}
