// src/pages/fitxaRepresaliat/sindicats.ts
type ApiWrapper<T> = { status?: string; message?: string; errors?: unknown[]; data?: T[] };
type Sindicat = { id: number; sindicat: string; sigles?: string | null };

const base = window.location.origin;

async function fetchAux<T>(endpoint: string): Promise<T[]> {
  const res = await fetch(`${base}/api/auxiliars/get/${endpoint}`);
  if (!res.ok) throw new Error(`HTTP ${res.status}`);
  const json = (await res.json()) as ApiWrapper<T> | T[];
  const arr = Array.isArray(json) ? json : json.data ?? [];
  if (!Array.isArray(arr)) throw new Error('Formato de respuesta inesperado');
  return arr;
}

export async function sindicats(ids: number[]): Promise<string[]> {
  if (!ids?.length) return [];
  try {
    const rows = await fetchAux<Sindicat>('sindicats');
    const byId = new Map<number, string>(rows.map((r) => [r.id, r.sigles ? `${r.sindicat} (${r.sigles})` : r.sindicat]));
    return ids.map((id) => byId.get(id)).filter((v): v is string => Boolean(v));
  } catch (e) {
    console.error('Error al procesar los sindicats:', e);
    return [];
  }
}
