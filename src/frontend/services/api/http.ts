// src/services/api/http.ts
import { isApiResponse } from './types';

async function getJson(url: string, init?: RequestInit): Promise<unknown> {
  const resp = await fetch(url, {
    method: 'GET',
    headers: { Accept: 'application/json' },
    ...init,
  });

  if (!resp.ok) {
    // 4xx / 5xx
    const text = await resp.text().catch(() => '');
    throw new Error(`HTTP ${resp.status} ${resp.statusText} - ${text}`);
  }

  // Parse seguro a unknown
  return resp.json();
}

/** Devuelve siempre un array tipado (vacío si no hay datos o status=error) */
export async function getApiArray<T>(url: string, init?: RequestInit): Promise<T[]> {
  const raw = await getJson(url, init);

  if (!isApiResponse<T>(raw)) {
    throw new Error('Formato de respuesta no válido');
  }

  if (raw.status !== 'success' || !Array.isArray(raw.data)) {
    // status=error o data=null → array vacío
    return [];
  }

  return raw.data;
}

/** Devuelve el primer elemento (o null) — útil para endpoints “por slug/id” */
export async function getApiFirst<T>(url: string, init?: RequestInit): Promise<T | null> {
  const arr = await getApiArray<T>(url, init);
  return arr[0] ?? null;
}
