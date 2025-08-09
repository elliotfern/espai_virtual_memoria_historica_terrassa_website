import { cache } from './cache';
import { loadTranslations } from '../../services/i18n/loadTranslations';
import { initTabs } from './initTabs';
import { initCategoriaButtons } from './initCategoriasButtons';

export async function initButtons(idPersona: number): Promise<void> {
  const fitxa = cache.getFitxa();
  if (!fitxa) {
    console.error('initButtons: no fitxa in cache');
    return;
  }

  const currentLang = document.documentElement.lang || 'ca';
  const translations = await loadTranslations(currentLang);

  initTabs(translations, idPersona);
  initCategoriaButtons(fitxa.categoria || '', idPersona);
}
