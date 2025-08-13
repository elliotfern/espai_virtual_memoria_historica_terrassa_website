// src/buscador/utils.ts
import { Municipio, Persona } from './types';

export function norm(s?: string) {
  return (s || '')
    .normalize('NFD')
    .replace(/[\u0300-\u036f]/g, '')
    .trim()
    .toLowerCase();
}

export function parseYear(dateStr?: string | null): number | undefined {
  if (!dateStr) return undefined;
  const m = /^(\d{4})/.exec(dateStr.trim());
  return m ? Number(m[1]) : undefined;
}

export function uniqueSortedNumbers(arr: number[]): number[] {
  const set = new Set<number>(arr);
  return Array.from(set).sort((a, b) => a - b);
}

export function fullName(p: Persona) {
  return `${p.cognom1 || ''} ${p.cognom2 || ''} ${p.nom || ''}`.trim();
}

export function getProvinciaByPersona(p: Persona, municipiById: Map<number, Municipio>): string {
  const directa = p.provincia_naixement && p.provincia_naixement.trim() !== '' ? p.provincia_naixement : '';
  if (directa) return directa;
  const m = p.municipi_naixement ? municipiById.get(p.municipi_naixement) : undefined;
  return m?.provincia || '';
}
