// src/pages/fitxaRepresaliat/index.ts
import { fetchDataFitxaRepresaliat } from '../../services/api/fetchDataFitxaRepresaliat';
import { fetchDataFitxaRepresaliatFamiliar } from '../../services/api/fetchDataFitxaRepresaliatFamiliar';
import { cache } from './cache';
import { initButtons } from './initButtons';
import type { Fitxa, FitxaFamiliars } from '../../types/types';

function showNotFound(msg: string): void {
  // Oculta las fichas normales
  const containers = document.querySelectorAll<HTMLDivElement>('.container.fitxaRepresaliat');
  containers.forEach((el) => {
    if (el.id !== 'fitxaRepresaliat_error') {
      el.style.display = 'none';
    }
  });

  // Muestra el div de error
  const errorDiv = document.getElementById('fitxaRepresaliat_error') as HTMLDivElement | null;
  if (errorDiv) {
    errorDiv.style.display = 'block';
    errorDiv.innerHTML = `
      <div class="fitxa-persona marro2 raleway">
        ${msg || 'No hi ha resultats disponibles per a aquesta fitxa.'}
      </div>`;
  }
}

export async function fitxaRepresaliat(slug: string): Promise<void> {
  try {
    const fitxa = await fetchDataFitxaRepresaliat(slug);
    if (!fitxa) {
      showNotFound('Ho sentim però la adreça web introduïda no es correspon amb cap fitxa de represaliat.');
      return;
    }

    cache.setFitxa(fitxa as Fitxa);

    try {
      const familiars = await fetchDataFitxaRepresaliatFamiliar(fitxa.id);
      cache.setFitxaFam(Array.isArray(familiars) ? (familiars as FitxaFamiliars[]) : []);
    } catch (err) {
      console.warn('Error al obtenir familiars:', err);
      cache.setFitxaFam([]);
    }

    await initButtons(fitxa.id);
  } catch (error) {
    console.error('fitxaRepresaliat - error:', error);
    showNotFound('Error carregant la fitxa.');
  }
}
