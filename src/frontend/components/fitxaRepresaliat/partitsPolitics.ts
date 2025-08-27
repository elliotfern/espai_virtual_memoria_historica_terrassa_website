import { DOMAIN_WEB } from '../../config/constants';

// src/pages/fitxaRepresaliat/partitsPolitics.ts
type ApiWrapper<T> = { status?: string; message?: string; errors?: unknown[]; data?: T[] };
type Partit = { id: number; partit_politic: string; sigles?: string | null };

const base = DOMAIN_WEB;

async function fetchAux<T>(endpoint: string): Promise<T[]> {
  const res = await fetch(`${base}/api/auxiliars/get/${endpoint}`);
  if (!res.ok) throw new Error(`HTTP ${res.status}`);
  const json = (await res.json()) as ApiWrapper<T> | T[];
  const arr = Array.isArray(json) ? json : json.data ?? [];
  if (!Array.isArray(arr)) throw new Error('Formato de respuesta inesperado');
  return arr;
}

export async function partitsPolitics(ids: number[]): Promise<string[]> {
  if (!ids?.length) return [];
  try {
    const rows = await fetchAux<Partit>('partitsPolitics'); // ajusta si tu endpoint difiere
    // Mapa id -> "Nombre (SIGLES)" si hay siglas
    const byId = new Map<number, string>(rows.map((r) => [r.id, r.sigles ? `${r.partit_politic} (${r.sigles})` : r.partit_politic]));
    // Devuelve en el mismo orden de 'ids' y omite los que no existan en la tabla
    return ids.map((id) => byId.get(id)).filter((v): v is string => Boolean(v));
  } catch (e) {
    console.error('Error al procesar los partidos:', e);
    return [];
  }
}
