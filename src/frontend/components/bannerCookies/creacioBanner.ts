export function createCookieBanner(): HTMLElement {
  const banner = document.createElement('div');
  banner.id = 'cookie-banner';
  banner.className = 'position-fixed bottom-0 w-100 bg-dark text-white p-4 text-center z-index-fixed';
  banner.style.zIndex = '1000'; // Bootstrap no tiene clase para z-index alto directamente

  banner.innerHTML = `
    <div class="container">
      <p class="mb-3">
        Amb el vostre acord, utilitzarem galetes o tecnologies similars per emmagatzemar, accedir i processar dades personals com la vostra visita a aquest lloc web, adreces IP i identificadors de galetes. Estàs d'acord?
      </p>
      <div class="d-flex flex-column flex-sm-row justify-content-center gap-2">
        <button id="info-cookies" class="btn btn-outline-light">Saber-ne més</button>
        <button id="accept-cookies" class="btn btn-success">Acceptar</button>
        <button id="reject-cookies" class="btn btn-danger">Rebutjar</button>
      </div>
    </div>
  `;

  return banner;
}
