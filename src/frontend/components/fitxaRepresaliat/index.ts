// src/pages/fitxaRepresaliat/index.ts
import { fetchDataFitxaRepresaliat } from '../../services/api/fetchDataFitxaRepresaliat';
import { fetchDataFitxaRepresaliatFamiliar } from '../../services/api/fetchDataFitxaRepresaliatFamiliar';
import { cache } from './cache';
import { initButtons } from './initButtons';
import type { Fitxa, FitxaFamiliars } from '../../types/types';

function showNotFound(msg: string): void {
  const containers = document.querySelectorAll<HTMLDivElement>('.container.fitxaRepresaliat');
  containers.forEach((el) => {
    if (el.id !== 'fitxaRepresaliatError') el.style.display = 'none';
  });
  const errorDiv = document.getElementById('fitxaRepresaliatError') as HTMLDivElement | null;
  if (errorDiv) {
    errorDiv.style.display = 'block';
    errorDiv.innerHTML = `
      <div class="alert alert-warning mt-4 mb-4" role="alert">
        ${msg || 'No hi ha resultats disponibles per a aquesta fitxa.'}
      </div>`;
  }
}

export async function fitxaRepresaliat(slug: string): Promise<void> {
  try {
    const fitxa = await fetchDataFitxaRepresaliat(slug);
    if (!fitxa) {
      showNotFound("No s'ha trobat el registre sol·licitat.");
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
