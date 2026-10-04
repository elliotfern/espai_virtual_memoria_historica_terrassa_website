import { getCookie } from '../../services/cookies/cookiesUtils';
import { loadGoogleAnalytics } from './googleAnalytics';
import { createChangePreferencesButton } from './botoCanviarPreferencies';
import { showCookieBanner } from './mostrarCookieBanner';

export function initCookieConsent(): void {
  const consent = getCookie('cookie_consent');

  document.body.appendChild(createChangePreferencesButton());

  if (consent === 'true') {
    loadGoogleAnalytics();
  } else if (consent === null) {
    showCookieBanner();
  }
}
