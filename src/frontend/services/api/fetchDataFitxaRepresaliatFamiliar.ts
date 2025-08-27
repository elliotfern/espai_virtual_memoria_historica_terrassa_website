// src/services/api/fetchDataFitxaRepresaliatFamiliar.ts
import { DOMAIN_API } from '../../config/constants';
import type { FitxaFamiliars } from '../../types/types';
import { fetchData } from './api';

type ApiResponseSuccess<T> = {
  status: 'success';
  message: string;
  errors: unknown[];
  data: T[]; // supongo array de familiares
};

type ApiResponseError = {
  status: 'error';
  message: string;
  errors: unknown[];
  data: null;
};

type ApiResponse<T> = ApiResponseSuccess<T> | ApiResponseError;

export async function fetchDataFitxaRepresaliatFamiliar(idPersona: number): Promise<FitxaFamiliars[]> {
  const url = `${DOMAIN_API}/api/dades_personals/get/?type=fitxaDadesFamiliars&id=${idPersona}`;

  try {
    const res = (await fetchData(url)) as ApiResponse<FitxaFamiliars>;
    if (res.status === 'success' && Array.isArray(res.data)) {
      return res.data;
    }
    return [];
  } catch (error) {
    console.warn('Error al obtenir familiars:', error);
    return [];
  }
}
