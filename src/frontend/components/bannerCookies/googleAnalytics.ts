export function loadGoogleAnalytics(): void {
  window.dataLayer = window.dataLayer || [];

  window.gtag = function (...args: unknown[]): void {
    window.dataLayer.push(args);
  };

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
    window.gtag('js', new Date());

    window.gtag('config', 'G-CLFHEHQQK5');

    window.gtag('event', 'page_view', {
      page_location: window.location.href,
      page_path: window.location.pathname,
    });
  };
}
