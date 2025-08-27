// src/services/api/types.ts
export type ApiResponseSuccess<T> = {
  status: 'success';
  message: string;
  errors: unknown[];
  data: T[]; // Tu API siempre usa array en `data`
};

export type ApiResponseError = {
  status: 'error';
  message: string;
  errors: unknown[];
  data: null;
};

export type ApiResponse<T> = ApiResponseSuccess<T> | ApiResponseError;

export function isApiResponse<T>(v: unknown): v is ApiResponse<T> {
  if (typeof v !== 'object' || v === null) return false;
  const o = v as Record<string, unknown>;
  return typeof o.status === 'string' && typeof o.message === 'string' && 'errors' in o && 'data' in o;
}
