import { setCookie } from '../../services/cookies/cookiesUtils';
import { loadGoogleAnalytics } from './googleAnalytics';
import { createCookieBanner } from './creacioBanner';

export function showCookieBanner() {
  const existing = document.getElementById('cookie-banner');
  if (existing) existing.remove();

  const banner = createCookieBanner();
  document.body.appendChild(banner);

  const acceptBtn = document.getElementById('accept-cookies')!;
  const rejectBtn = document.getElementById('reject-cookies')!;
  const infoBtn = document.getElementById('info-cookies')!;

  acceptBtn.addEventListener('click', () => {
    setCookie('cookie_consent', 'true', 365);
    banner.remove();
    loadGoogleAnalytics();
  });

  rejectBtn.addEventListener('click', () => {
    setCookie('cookie_consent', 'false', 365);
    banner.remove();
  });

  infoBtn.addEventListener('click', () => {
    window.location.href = '/politica-privacitat';
  });
}
