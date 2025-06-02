type GtagFunction = {
  (command: 'js', config: Date): void;
  (command: 'config', targetId: string): void;
};

export function loadGoogleAnalytics() {
  const script = document.createElement('script');
  script.async = true;
  script.src = 'https://www.googletagmanager.com/gtag/js?id=G-CLFHEHQQK5';
  document.head.appendChild(script);

  script.onload = () => {
    if (!Array.isArray(window.dataLayer)) {
      window.dataLayer = [];
    }

    window.gtag = function (...args: Parameters<GtagFunction>) {
      window.dataLayer.push(args);
    };

    window.gtag('js', new Date());
    window.gtag('config', 'G-CLFHEHQQK5');
  };
}
