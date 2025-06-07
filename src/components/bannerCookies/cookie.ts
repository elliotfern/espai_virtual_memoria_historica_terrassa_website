import { getCookie } from '../../services/cookies/cookiesUtils';
import { loadGoogleAnalytics } from './googleAnalytics';
import { createChangePreferencesButton } from './botoCanviarPreferencies';
import { showCookieBanner } from './mostrarCookieBanner';

export function initCookieConsent() {
  const consent = getCookie('cookie_consent');

  // Botón siempre presente para cambiar preferencias
  document.body.appendChild(createChangePreferencesButton());

  if (consent === 'true') {
    // Usuario ya dio consentimiento, carga GA con consentimiento actualizado
    document.cookie = 'cookie_consent=true; path=/; max-age=31536000'; // 1 año
    loadGoogleAnalytics();
  } else if (consent === null) {
    // Mostrar banner para pedir consentimiento
    showCookieBanner();
  }
}
