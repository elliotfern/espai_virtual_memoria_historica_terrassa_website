// src/pages/fitxaRepresaliat/index.ts
import { cache } from './cache';
import { initButtons } from './initButtons';
import type { Fitxa, FitxaFamiliars } from '../../types/types';
import { getApiArray, getApiFirst } from '../../services/api/http';
import { getIsAdmin } from '../../services/auth/getIsAdmin';
import { getIsAutor } from '../../services/auth/getIsAutor';
import { getIsLogged } from '../../services/auth/getIsLogged';
import { API_URLS } from '../../services/api/ApiUrls';

function showNotFound(msg: string): void {
  // Oculta posibles contenedores de la ficha (ajusta IDs/clases reales)
  document.querySelectorAll<HTMLElement>('.container.fitxaRepresaliat, #fitxaRepresaliat_main, .fitxaRepresaliat').forEach((el) => {
    el.style.display = 'none';
  });

  const errorDiv = document.getElementById('fitxaRepresaliat_error') as HTMLDivElement | null;
  if (errorDiv) {
    errorDiv.style.display = 'block';
    errorDiv.innerHTML = `
      <div class="fitxa-persona blau1 raleway">
        ${msg || 'No hi ha resultats disponibles per a aquesta fitxa.'}
      </div>`;
  }
}

export async function fitxaRepresaliat(slug: string, lang: string): Promise<void> {
  try {
    const fitxa = await getApiFirst<Fitxa>(API_URLS.GET.FITXA_REPRESALIAT_WEB(slug, lang));

    if (!fitxa) {
      showNotFound("Ho sentim, però l'adreça web introduïda no es correspon amb cap fitxa de represaliat.");
      return;
    }

    // 2) Validar visibilidad
    const isAdmin = await Promise.resolve(getIsAdmin());
    const isAutor = await Promise.resolve(getIsAutor());
    const isLogged = await Promise.resolve(getIsLogged());

    const completat = Number(fitxa.completat);
    const visibilitat = Number(fitxa.visibilitat);

    const esVisiblePublicament = completat === 2 && visibilitat === 2;
    const tePermisos = Boolean(isAdmin || isAutor || isLogged);

    const nom = fitxa.nom !== null ? fitxa.nom : '';
    const cognom1 = fitxa.cognom1 !== null ? fitxa.cognom1 : '';
    const cognom2 = fitxa.cognom2 !== null ? fitxa.cognom2 : '';
    const nombreCompleto = `${nom} ${cognom1} ${cognom2 ?? ''}`;

    if (!(esVisiblePublicament || tePermisos)) {
      showNotFound(`
      <h4 class="titolRepresaliat"> ${nombreCompleto}</h4>
        <h3>Ho sentim!</h3>
        <p>Encara estem treballant en el processament de les dades d'aquesta fitxa.</p>
        <a href="/contacte" class="btn btn-primary btn-custom-3" rel="noopener">Necessites contactar amb nosaltres?</a>
      `);
      return;
    }

    cache.setFitxa(fitxa);

    // 2) Familiares por id → usamos getApiArray<FitxaFamiliars>
    try {
      const familiars = await getApiArray<FitxaFamiliars>(API_URLS.GET.FITXA_REPRESALIAT_DADES_FAMILIARS_WEB(fitxa.id, lang));
      cache.setFitxaFam(familiars);
    } catch (err) {
      console.warn('Error al obtenir familiars:', err);
      cache.setFitxaFam([]);
    }

    // 3) Iniciar UI
    await initButtons(fitxa.id, lang);
  } catch (error) {
    console.error('fitxaRepresaliat - error:', error);
    showNotFound("Ho sentim, però l'adreça web introduïda no es correspon amb cap fitxa de represaliat.");
  }
}
