export function loadGoogleAnalytics(): void {
  window.gtag('consent', 'update', {
    ad_user_data: 'granted',
    ad_personalization: 'granted',
    ad_storage: 'granted',
    analytics_storage: 'granted',
  });

  const script = document.createElement('script');

  script.async = true;
  script.src = 'https://www.googletagmanager.com/gtag/js?id=G-CLFHEHQQK5';

  document.head.appendChild(script);

  script.onload = () => {
    window.gtag('config', 'G-CLFHEHQQK5');
  };
}
