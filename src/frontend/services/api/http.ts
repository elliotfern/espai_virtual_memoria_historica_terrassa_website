import { isApiResponse } from './types';

async function getJson(url: string, init?: RequestInit): Promise<unknown> {
  const resp = await fetch(url, { method: 'GET', headers: { Accept: 'application/json' }, ...init });
  if (!resp.ok) {
    const text = await resp.text().catch(() => '');
    throw new Error(`HTTP ${resp.status} ${resp.statusText} - ${text}`);
  }
  return resp.json();
}

function normalizeToArray<T>(data: T | T[] | null | undefined): T[] {
  if (Array.isArray(data)) return data;
  if (data == null) return [];
  return [data];
}

export async function getApiArray<T>(url: string, init?: RequestInit): Promise<T[]> {
  const raw = await getJson(url, init);
  if (!isApiResponse<T>(raw)) throw new Error('Formato de respuesta no válido');
  if (raw.status !== 'success') return [];
  return normalizeToArray<T>(raw.data); // 👈 da igual si viene objeto o array
}

export async function getApiFirst<T>(url: string, init?: RequestInit): Promise<T | null> {
  const arr = await getApiArray<T>(url, init);
  return arr[0] ?? null;
}
