export function loadGoogleAnalytics() {
  // Asegúrate de que 'gtag' esté disponible antes de continuar
  if (!window.gtag) return;

  // Actualiza el consentimiento a 'granted' cuando el usuario lo acepte
  window.gtag('consent', 'update', {
    ad_user_data: 'granted',
    ad_personalization: 'granted',
    ad_storage: 'granted',
    analytics_storage: 'granted',
  });

  // Ahora podemos cargar el script gtag.js
  const script = document.createElement('script');
  script.async = true;
  script.src = 'https://www.googletagmanager.com/gtag/js?id=G-CLFHEHQQK5';
  document.head.appendChild(script);

  // Una vez cargado el script, configuramos Google Analytics
  script.onload = () => {
    window.gtag('js', new Date());
    window.gtag('config', 'G-CLFHEHQQK5'); // Inicia la configuración de GA

    // Aquí puedes enviar eventos si quieres
    window.gtag('event', 'page_view', {
      page_location: window.location.href,
      page_path: window.location.pathname,
    });
  };
}
