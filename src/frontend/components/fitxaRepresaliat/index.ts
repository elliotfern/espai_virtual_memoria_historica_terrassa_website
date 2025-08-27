// src/pages/fitxaRepresaliat/index.ts
import { fetchDataFitxaRepresaliat } from '../../services/api/fetchDataFitxaRepresaliat';
import { fetchDataFitxaRepresaliatFamiliar } from '../../services/api/fetchDataFitxaRepresaliatFamiliar';
import { cache } from './cache';
import { initButtons } from './initButtons';
import type { Fitxa, FitxaFamiliars } from '../../types/types';

/** Tipos del wrapper de tu API */
type ApiResponseSuccess<T> = {
  status: 'success';
  message: string;
  errors: unknown[];
  data: T | T[];
};

type ApiResponseError = {
  status: 'error';
  message: string;
  errors: unknown[];
  data: null;
};

type ApiResponse<T> = ApiResponseSuccess<T> | ApiResponseError;

/** ---- TYPE GUARDS ---- */
function isObject(value: unknown): value is Record<string, unknown> {
  return typeof value === 'object' && value !== null;
}

function isApiResponse<T>(value: unknown): value is ApiResponse<T> {
  return isObject(value) && 'status' in value && 'message' in value && 'errors' in value && 'data' in value;
}

function isFitxa(value: unknown): value is Fitxa {
  return isObject(value) && 'id' in value && 'slug' in value;
}

function isFitxaFamiliarsArray(value: unknown): value is FitxaFamiliars[] {
  return Array.isArray(value);
}

/** Normaliza la data del wrapper a un único objeto o null */
function takeFirstFromApi<T>(res: ApiResponse<T>): T | null {
  if (res.status !== 'success') return null;
  return Array.isArray(res.data) ? res.data[0] ?? null : res.data;
}

/** UI: mostrar el contenedor de error y ocultar la ficha */
function showNotFound(msg: string): void {
  // Oculta cualquier .container.fitxaRepresaliat excepto el de error
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
    /** 1) Cargar la fitxa (aceptamos dos formas de retorno) */
    const raw = await fetchDataFitxaRepresaliat(slug);

    let fitxa: Fitxa | null = null;
    if (isApiResponse<Fitxa>(raw)) {
      fitxa = takeFirstFromApi<Fitxa>(raw);
      if (!fitxa) {
        showNotFound(raw.message || "No s'ha trobat el registre sol·licitat.");
        return;
      }
    } else if (isFitxa(raw)) {
      fitxa = raw; // Tu servicio ya devolvía la Fitxa desenvuelta
    } else {
      // undefined o forma no reconocida
      showNotFound("No s'ha trobat el registre sol·licitat.");
      return;
    }

    // Guardar en cache
    cache.setFitxa(fitxa);

    /** 2) Cargar familiars con la misma estrategia de tipos */
    try {
      const famRaw = await fetchDataFitxaRepresaliatFamiliar(fitxa.id);

      if (isApiResponse<FitxaFamiliars>(famRaw)) {
        if (famRaw.status === 'success' && Array.isArray(famRaw.data)) {
          cache.setFitxaFam(famRaw.data);
        } else {
          cache.setFitxaFam([]);
        }
      } else if (isFitxaFamiliarsArray(famRaw)) {
        cache.setFitxaFam(famRaw);
      } else {
        cache.setFitxaFam([]);
      }
    } catch (err) {
      console.warn('Error al obtenir familiars:', err);
      cache.setFitxaFam([]);
    }

    /** 3) Iniciar UI */
    await initButtons(fitxa.id);
  } catch (error) {
    console.error('fitxaRepresaliat - error:', error);
    showNotFound('Error carregant la fitxa.');
  }
}
