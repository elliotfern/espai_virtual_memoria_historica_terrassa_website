import { DEFAULT_LANG, isLang, t } from '../../services/i18n/i18n';
import { TIPUS_TEXTS } from '../../services/i18n/tipusRepressio';

export function renderTipusRepressioTexts(lang: string): void {
  const l = isLang(lang) ? lang : DEFAULT_LANG;

  const titol = document.getElementById('titolTipusRepressio');
  if (titol) {
    titol.innerHTML = `<h6 class="titolSeccio" style="margin-top:25px"><strong>${t(TIPUS_TEXTS, 'title', l)}</strong></h6>`;
  } else {
    console.warn('[renderTipusRepressioTexts] Falta #titolTipusRepressio');
  }

  const textDiv = document.getElementById('tipusRepressioText');
  if (textDiv) {
    textDiv.innerHTML = `<p>${t(TIPUS_TEXTS, 'help', l)}</p>`;
  } else {
    console.warn('[renderTipusRepressioTexts] Falta #tipusRepressioText');
  }
}
