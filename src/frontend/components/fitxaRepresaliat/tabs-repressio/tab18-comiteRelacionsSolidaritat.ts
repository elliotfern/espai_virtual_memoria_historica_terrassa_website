import { LABELS_COMITE_RELACIONS } from '../../../services/i18n/fitxaRepresaliatRepressio/label-tab18-comiteRelacions';
import { DEFAULT_LANG, isLang, t } from '../../../services/i18n/i18n';

export function tab18ComiteRelacionsSolidaritat(htmlContent: string, lang: string): string {
  const l = isLang(lang) ? lang : DEFAULT_LANG;

  htmlContent += `
    <div class="negreta raleway">
      <div style="margin-top:25px">
        <p>${t(LABELS_COMITE_RELACIONS, 'text', l)}</p>
      </div>
    </div>
  `;

  return htmlContent;
}
