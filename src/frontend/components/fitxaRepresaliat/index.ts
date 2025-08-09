// src/pages/fitxaRepresaliat/index.ts
import { fetchDataFitxaRepresaliat } from '../../services/api/fetchDataFitxaRepresaliat';
import { fetchDataFitxaRepresaliatFamiliar } from '../../services/api/fetchDataFitxaRepresaliatFamiliar';
import { cache } from './cache';
import { initButtons } from './initButtons';
import type { Fitxa, FitxaFamiliars } from '../../types/types';

export async function fitxaRepresaliat(slug: string): Promise<void> {
  try {
    const fetchedFitxa = await fetchDataFitxaRepresaliat(slug);
    if (!fetchedFitxa) {
      console.warn('No se encontró la fitxa para el slug:', slug);
      return;
    }

    // Guardar en cache
    cache.setFitxa(fetchedFitxa as Fitxa);

    // Intentar cargar familiars (si falla, guardamos array vacío)
    try {
      const familiars = await fetchDataFitxaRepresaliatFamiliar(fetchedFitxa.id);
      cache.setFitxaFam(Array.isArray(familiars) ? (familiars as FitxaFamiliars[]) : []);
    } catch (err) {
      console.warn('Error al obtener familiars:', err);
      cache.setFitxaFam([]);
    }

    // Iniciar botones / UI (implementaremos initButtons next)
    await initButtons(fetchedFitxa.id);
  } catch (error) {
    console.error('fitxaRepresaliat - error:', error);
    const divInfo = document.getElementById('fitxa');
    if (divInfo) divInfo.innerHTML = '<p class="text-danger">Error carregant la fitxa.</p>';
  }
}
