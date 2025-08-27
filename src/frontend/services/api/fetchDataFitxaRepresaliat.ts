// src/services/api/fetchDataFitxaRepresaliat.ts
import { DOMAIN_API } from '../../config/constants';
import type { Fitxa } from '../../types/types';
import { fetchData } from './api';

type ApiResponseSuccess<T> = {
  status: 'success';
  message: string;
  errors: unknown[];
  data: T[]; // tu API devuelve data como array
};

type ApiResponseError = {
  status: 'error';
  message: string;
  errors: unknown[];
  data: null;
};

type ApiResponse<T> = ApiResponseSuccess<T> | ApiResponseError;

export async function fetchDataFitxaRepresaliat(slug: string): Promise<Fitxa | null> {
  const url = `${DOMAIN_API}/api/dades_personals/get/?type=fitxaRepresaliat&slug=${encodeURIComponent(slug)}`;

  try {
    const res = (await fetchData(url)) as ApiResponse<Fitxa>;

    if (res.status === 'error' || !res.data || !Array.isArray(res.data) || res.data.length === 0) {
      // puedes loguear res.message si quieres
      return null;
    }

    // tu endpoint devuelve data como array con un único elemento
    return res.data[0] ?? null;
  } catch (error) {
    console.error('Error al obtener la fitxa:', error);
    return null;
  }
}
