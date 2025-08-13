// src/buscador/api.ts
import { Persona, OpcionesFiltros, Municipio, EstatCivil, Estudi, Ofici, PartitPolitic, Sindicat, Causa, CategoriaRepressio } from './types';

// ---- Helpers HTTP ----
async function fetchJSON<T>(url: string): Promise<T> {
  const res = await fetch(url);
  if (!res.ok) throw new Error(`Error en fetch: ${res.status} (${url})`);
  return res.json();
}

// ---- Utils de parseo ----
function parseIdArray(raw: unknown): number[] | undefined {
  if (Array.isArray(raw)) {
    return (raw as unknown[]).filter((x): x is number => typeof x === 'number');
  }
  if (typeof raw === 'string') {
    try {
      const arr = JSON.parse(raw) as unknown;
      if (Array.isArray(arr)) {
        return (arr as unknown[]).filter((x): x is number => typeof x === 'number');
      }
    } catch {
      // string no es un JSON válido -> no pasa nada, devolvemos undefined
      return undefined;
    }
  }
  return undefined;
}

// ---- Tipos crudos de la API ----
interface PersonaRaw {
  id: number;
  nom: string;
  cognom1: string;
  cognom2: string;
  slug: string;

  municipi_naixement: number | string;
  provincia_naixement?: string | null;
  municipi_defuncio?: number | string | null;

  causa_defuncio?: number | string | null;
  sexe?: number | string | null;
  estat_civil: number | string;
  estudis: number | string;
  ofici: number | string;
  filiacio_politica?: string | number[] | null;
  filiacio_sindical?: string | number[] | null;

  data_naixement?: string | null;
  data_defuncio?: string | null;
  categoria?: string | number[] | null;

  data_exili?: string | null;
  primer_desti_exili?: number | string | null;
  deportat?: number | string | null;
  participacio_resistencia?: number | string | null;
}

// ---- Opciones de fetch parametrizables ----
export interface FetchPersonasOptions {
  /** Valor del query param `type`, p.ej. "filtreExiliats", "filtreRepresaliats", etc. */
  type: string;
  /** Dominio base, por defecto https://memoriaterrassa.cat */
  baseUrl?: string;
  /** Idioma opcional para algunos endpoints que lo aceptan (si aplica) */
  lang?: string;
}

/**
 * Retrocompatible:
 *  - Sin argumentos => "filtreExiliats"
 *  - string => se interpreta como `type`
 *  - objeto => opciones completas
 */
export async function fetchPersonas(typeOrOpts: string | FetchPersonasOptions = 'filtreExiliats'): Promise<Persona[]> {
  const opts: FetchPersonasOptions = typeof typeOrOpts === 'string' ? { type: typeOrOpts } : typeOrOpts;

  const base = (opts.baseUrl ?? 'https://memoriaterrassa.cat').replace(/\/+$/, '');
  const langQS = opts.lang ? `&lang=${encodeURIComponent(opts.lang)}` : '';
  const url = `${base}/api/dades_personals/get/?type=${encodeURIComponent(opts.type)}${langQS}`;

  const json = await fetchJSON<{ data: PersonaRaw[] }>(url);

  return (json.data || []).map((r) => ({
    id: r.id,
    nom: r.nom,
    cognom1: r.cognom1,
    cognom2: r.cognom2,
    slug: r.slug,

    municipi_naixement: Number(r.municipi_naixement),
    provincia_naixement: r.provincia_naixement ?? undefined,
    municipi_defuncio: r.municipi_defuncio ? Number(r.municipi_defuncio) : undefined,

    causa_defuncio: r.causa_defuncio ? Number(r.causa_defuncio) : undefined,
    sexe: r.sexe ? Number(r.sexe) : undefined,
    estat_civil: Number(r.estat_civil),
    estudis: Number(r.estudis),
    ofici: Number(r.ofici),
    filiacio_politica: parseIdArray(r.filiacio_politica),
    filiacio_sindical: parseIdArray(r.filiacio_sindical),

    data_naixement: r.data_naixement ?? null,
    data_defuncio: r.data_defuncio ?? null,
    categoria: parseIdArray(r.categoria),

    data_exili: r.data_exili ?? null,
    primer_desti_exili: r.primer_desti_exili ? Number(r.primer_desti_exili) : null,
    deportat: r.deportat ? Number(r.deportat) : null,
    participacio_resistencia: r.participacio_resistencia ? Number(r.participacio_resistencia) : null,
  }));
}

// ---- Opciones auxiliares (municipis, estats, etc.) ----
export interface FetchOpcionesOptions {
  baseUrl?: string;
  lang?: string; // para categories
}

export async function fetchOpcionesFiltros(opts: FetchOpcionesOptions = {}): Promise<OpcionesFiltros> {
  const base = (opts.baseUrl ?? 'https://memoriaterrassa.cat').replace(/\/+$/, '');
  const langQS = `?lang=${encodeURIComponent(opts.lang ?? 'ca')}`;

  const [municipis, estats_civils, estudis, oficis, partits, sindicats, causes, categoriesRaw] = await Promise.all([fetchJSON<{ data: Municipio[] }>(`${base}/api/auxiliars/get/municipis/`).then((r) => r.data), fetchJSON<{ data: EstatCivil[] }>(`${base}/api/auxiliars/get/estats_civils/`).then((r) => r.data), fetchJSON<{ data: Estudi[] }>(`${base}/api/auxiliars/get/estudis/`).then((r) => r.data), fetchJSON<{ data: Ofici[] }>(`${base}/api/auxiliars/get/oficis/`).then((r) => r.data), fetchJSON<{ data: PartitPolitic[] }>(`${base}/api/auxiliars/get/partitsPolitics/`).then((r) => r.data), fetchJSON<{ data: Sindicat[] }>(`${base}/api/auxiliars/get/sindicats/`).then((r) => r.data), fetchJSON<{ data: Causa[] }>(`${base}/api/auxiliars/get/causa_defuncio/`).then((r) => r.data), fetchJSON<{ data: CategoriaRepressio[] }>(`${base}/api/auxiliars/get/categoriesRepressio${langQS}`).then((r) => r.data)]);

  // Normaliza el campo name de categorías sin usar `any`
  const categories: CategoriaRepressio[] = (categoriesRaw || []).map((c) => ({
    ...c,
    name: c.name ?? c.categoria_ca ?? c.categoria ?? `#${c.id}`,
  }));

  return { municipis, estats_civils, estudis, oficis, partits, sindicats, causes, categories };
}
