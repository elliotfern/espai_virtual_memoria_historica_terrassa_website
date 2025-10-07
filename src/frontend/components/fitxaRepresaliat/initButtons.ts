import { cache } from './cache';
import { loadTranslations } from '../../services/i18n/loadTranslations';
import { initTabs } from './initTabs';
import { initCategoriaButtons } from './initCategoriasButtons';
import { renderBotonsAdminSimple } from './informacioFitxaAdministracio';
import { renderAvisContacte } from './avisContacte';
import { renderTipusRepressioTexts } from './renderTipusRepressio';

export async function initButtons(idPersona: number, lang: string): Promise<void> {
  const fitxa = cache.getFitxa();
  if (!fitxa) {
    console.error('initButtons: no fitxa in cache');
    return;
  }

  const translations = await loadTranslations(lang);

  initTabs(translations, idPersona, lang);
  initCategoriaButtons(fitxa.categoria || '', idPersona, lang);
  // cuando tengas los datos:
  renderBotonsAdminSimple({
    id: fitxa.id,
    completat: fitxa.completat,
    visibilitat: fitxa.visibilitat,
    containerId: 'botonsAdmin',
  });

  renderAvisContacte();
  renderTipusRepressioTexts(lang);
}
