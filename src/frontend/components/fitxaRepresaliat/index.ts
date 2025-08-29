// src/pages/fitxaRepresaliat/index.ts
import { cache } from './cache';
import { initButtons } from './initButtons';
import type { Fitxa, FitxaFamiliars } from '../../types/types';
import { getApiArray, getApiFirst } from '../../services/api/http';
import { DOMAIN_API } from '../../config/constants';
import { getIsAdmin } from '../../services/auth/getIsAdmin';
import { getIsAutor } from '../../services/auth/getIsAutor';

function showNotFound(msg: string): void {
  // Oculta las fichas normales (menos el contenedor de error)
  const containers = document.querySelectorAll<HTMLDivElement>('.container.fitxaRepresaliat');
  containers.forEach((el) => {
    if (el.id !== 'fitxaRepresaliat_error') el.style.display = 'none';
  });

  // Muestra el div de error
  const errorDiv = document.getElementById('fitxaRepresaliat_error') as HTMLDivElement | null;
  if (errorDiv) {
    errorDiv.style.display = 'block';
    errorDiv.innerHTML = `
      <div class="fitxa-persona blau1 raleway">
        ${msg || 'No hi ha resultats disponibles per a aquesta fitxa.'}
      </div>`;
  }
}

export async function fitxaRepresaliat(slug: string): Promise<void> {
  try {
    // 1) Detalle por slug → usamos getApiFirst<Fitxa>
    const fitxaUrl = `${DOMAIN_API}/api/dades_personals/get/?type=fitxaRepresaliat&slug=${encodeURIComponent(slug)}`;
    const fitxa = await getApiFirst<Fitxa>(fitxaUrl);

    if (!fitxa) {
      showNotFound("Ho sentim, però l'adreça web introduïda no es correspon amb cap fitxa de represaliat.");
      return;
    }

    // 2) Validar visibilidad
    const isAdmin = getIsAdmin();
    const isAutor = getIsAutor();

    const completat = Number(fitxa.completat ?? 0);
    const visibilitat = Number(fitxa.visibilitat ?? 0);

    const esVisiblePublicament = completat === 2 && visibilitat === 2;
    const tePermisos = isAdmin || isAutor;

    if (!(esVisiblePublicament || tePermisos)) {
      showNotFound('Aquesta fitxa no està disponible públicament.');
      return;
    }

    cache.setFitxa(fitxa);

    // 2) Familiares por id → usamos getApiArray<FitxaFamiliars>
    try {
      const familiarsUrl = `${DOMAIN_API}/api/dades_personals/get/?type=fitxaDadesFamiliars&id=${fitxa.id}`;
      const familiars = await getApiArray<FitxaFamiliars>(familiarsUrl);
      cache.setFitxaFam(familiars);
    } catch (err) {
      console.warn('Error al obtenir familiars:', err);
      cache.setFitxaFam([]);
    }

    // 3) Iniciar UI
    await initButtons(fitxa.id);
  } catch (error) {
    console.error('fitxaRepresaliat - error:', error);
    showNotFound("Ho sentim, però l'adreça web introduïda no es correspon amb cap fitxa de represaliat.");
  }
}

// a.completat = 2
// visibilitat = 2
