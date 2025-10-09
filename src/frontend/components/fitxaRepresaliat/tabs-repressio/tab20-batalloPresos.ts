import { LABELS_REPRESSIO_EMPTY } from '../../../services/i18n/fitxaRepresaliatRepressio/label-tab19-campsTreball';
import { DEFAULT_LANG, isLang, t } from '../../../services/i18n/i18n';

export function tab20BatalloPresos(htmlContent: string, lang: string): string {
  const l = isLang(lang) ? lang : DEFAULT_LANG;

  htmlContent += `
    <div class="negreta raleway">
      <div style="margin-top:25px">
        ${t(LABELS_REPRESSIO_EMPTY, 'categoryEmpty', l)}
      </div>
    </div>
  `;

  return htmlContent;
}
