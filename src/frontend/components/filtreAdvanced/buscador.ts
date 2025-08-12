// buscador.ts
import Choices from 'choices.js';
import 'choices.js/public/assets/styles/choices.min.css';
import { formatDatesForm } from '../../services/formatDates/dates';

interface Persona {
  id: number;
  nom: string;
  cognom1: string;
  cognom2: string;
  slug: string;
  municipi_naixement: number;
  provincia_naixement?: string;
  municipi_defuncio?: number;
  causa_defuncio?: number;
  sexe?: number; // 1=Home, 2=Dona
  estat_civil: number;
  estudis: number;
  ofici: number;
  filiacio_politica?: number[];
  filiacio_sindical?: number[];

  // NUEVO: campos para años y categoría repressió
  data_naixement?: string | null;
  data_defuncio?: string | null;
  categoria?: number[]; // mismo formato que filiacio_politica
}

interface Municipio {
  id: number;
  ciutat: string;
  provincia: string;
}
interface EstatCivil {
  id: number;
  estat_cat: string;
}
interface Estudi {
  id: number;
  estudi_cat: string;
}
interface Ofici {
  id: number;
  ofici_cat: string;
}
interface PartitPolitic {
  id: number;
  partit_politic: string;
  sigles: string | null;
}
interface Sindicat {
  id: number;
  sindicat: string; // ya corregido
}
interface Causa {
  id: number;
  causa_defuncio_ca: string; // ya corregido
}
interface CategoriaRepressio {
  id: number;
  // el endpoint puede devolver 'categoria_ca' o 'categoria'
  categoria_ca?: string;
  categoria?: string;
  name: string;
}

interface OpcionesFiltros {
  municipis: Municipio[];
  estats_civils: EstatCivil[];
  estudis: Estudi[];
  oficis: Ofici[];
  partits: PartitPolitic[];
  sindicats: Sindicat[];
  causes: Causa[];
  categories: CategoriaRepressio[]; // NUEVO
}

type SelectionState = {
  municipis_naixement: string[];
  provincies: string[];
  anys_naixement: string[]; // NUEVO
  estats: string[];
  estudis: string[];
  oficis: string[];
  municipis_defuncio: string[];
  sexes: string[];
  partits: string[];
  sindicats: string[];
  causes: string[];
  anys_defuncio: string[]; // NUEVO
  categories: string[]; // NUEVO
};

// ====== Estat global ======
let datosOriginales: Persona[] = [];
let opcionesGlobales: OpcionesFiltros;

let choicesMunicipiN: Choices | null = null;
let choicesProvincia: Choices | null = null;
let choicesAnyNaix: Choices | null = null; // NUEVO
let choicesEstatCivil: Choices | null = null;
let choicesEstudis: Choices | null = null;
let choicesOfici: Choices | null = null;
let choicesMunicipiD: Choices | null = null;
let choicesSexe: Choices | null = null;
let choicesPartits: Choices | null = null;
let choicesSindicats: Choices | null = null;
let choicesCauses: Choices | null = null;
let choicesAnyDef: Choices | null = null; // NUEVO
let choicesCategories: Choices | null = null; // NUEVO

let resultadosFiltrados: Persona[] = [];
let currentPage = 1;
const PAGE_SIZE = 20;

type SortKey = 'cognoms' | 'nom' | 'municipi';
let sortKey: SortKey = 'cognoms';

const municipiById = new Map<number, Municipio>();

// ====== Utils ======
async function fetchJSON<T>(url: string): Promise<T> {
  const res = await fetch(url);
  if (!res.ok) throw new Error(`Error en fetch: ${res.status}`);
  return res.json();
}

function norm(s?: string) {
  return (s || '')
    .normalize('NFD')
    .replace(/[\u0300-\u036f]/g, '')
    .trim()
    .toLowerCase();
}

function getProvinciaByPersona(p: Persona): string {
  const directa = p.provincia_naixement && p.provincia_naixement.trim() !== '' ? p.provincia_naixement : '';
  if (directa) return directa;
  const m = p.municipi_naixement ? municipiById.get(p.municipi_naixement) : undefined;
  return m?.provincia || '';
}

function parseIdArray(raw: unknown): number[] | undefined {
  if (Array.isArray(raw)) return raw.filter((x) => typeof x === 'number') as number[];
  if (typeof raw === 'string') {
    try {
      const arr = JSON.parse(raw);
      if (Array.isArray(arr)) return arr.filter((x) => typeof x === 'number') as number[];
    } catch {
      /* ignore */
    }
  }
  return undefined;
}

function parseYear(dateStr?: string | null): number | undefined {
  if (!dateStr) return undefined;
  const m = /^(\d{4})/.exec(dateStr.trim());
  return m ? Number(m[1]) : undefined;
}

function fullName(p: Persona) {
  return `${p.cognom1 || ''} ${p.cognom2 || ''} ${p.nom || ''}`.trim();
}

function sindicatName(s: Sindicat): string {
  return s.sindicat ?? `#${s.id}`;
}
function causaName(c: Causa): string {
  return c.causa_defuncio_ca ?? `#${c.id}`;
}
function categoriaName(c: CategoriaRepressio): string {
  return c.name ?? `#${c.id}`;
}

function uniqueSortedNumbers(arr: number[]): number[] {
  const set = new Set<number>(arr);
  return Array.from(set).sort((a, b) => a - b);
}

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

  // NUEVO
  data_naixement?: string | null;
  data_defuncio?: string | null;
  categoria?: string | number[] | null;
}

async function fetchPersonas(): Promise<Persona[]> {
  const json = await fetchJSON<{ data: PersonaRaw[] }>('https://memoriaterrassa.cat/api/dades_personals/get/?type=filtreExiliats');
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

    // NUEVO
    data_naixement: r.data_naixement ?? null,
    data_defuncio: r.data_defuncio ?? null,
    categoria: parseIdArray(r.categoria),
  }));
}

async function fetchOpcionesFiltros(): Promise<OpcionesFiltros> {
  const [municipis, estats_civils, estudis, oficis, partits, sindicats, causes, categories] = await Promise.all([fetchJSON<{ data: Municipio[] }>('https://memoriaterrassa.cat/api/auxiliars/get/municipis/').then((r) => r.data), fetchJSON<{ data: EstatCivil[] }>('https://memoriaterrassa.cat/api/auxiliars/get/estats_civils/').then((r) => r.data), fetchJSON<{ data: Estudi[] }>('https://memoriaterrassa.cat/api/auxiliars/get/estudis/').then((r) => r.data), fetchJSON<{ data: Ofici[] }>('https://memoriaterrassa.cat/api/auxiliars/get/oficis/').then((r) => r.data), fetchJSON<{ data: PartitPolitic[] }>('https://memoriaterrassa.cat/api/auxiliars/get/partitsPolitics/').then((r) => r.data), fetchJSON<{ data: Sindicat[] }>('https://memoriaterrassa.cat/api/auxiliars/get/sindicats/').then((r) => r.data), fetchJSON<{ data: Causa[] }>('https://memoriaterrassa.cat/api/auxiliars/get/causa_defuncio/').then((r) => r.data), fetchJSON<{ data: CategoriaRepressio[] }>('https://memoriaterrassa.cat/api/auxiliars/get/categoriesRepressio?lang=ca').then((r) => r.data)]);
  return { municipis, estats_civils, estudis, oficis, partits, sindicats, causes, categories };
}

// ====== Helpers selecció ======
function getSelections(): SelectionState {
  return {
    municipis_naixement: (choicesMunicipiN?.getValue(true) as string[]) ?? [],
    provincies: (choicesProvincia?.getValue(true) as string[]) ?? [],
    anys_naixement: (choicesAnyNaix?.getValue(true) as string[]) ?? [], // NUEVO
    estats: (choicesEstatCivil?.getValue(true) as string[]) ?? [],
    estudis: (choicesEstudis?.getValue(true) as string[]) ?? [],
    oficis: (choicesOfici?.getValue(true) as string[]) ?? [],
    municipis_defuncio: (choicesMunicipiD?.getValue(true) as string[]) ?? [],
    sexes: (choicesSexe?.getValue(true) as string[]) ?? [],
    partits: (choicesPartits?.getValue(true) as string[]) ?? [],
    sindicats: (choicesSindicats?.getValue(true) as string[]) ?? [],
    causes: (choicesCauses?.getValue(true) as string[]) ?? [],
    anys_defuncio: (choicesAnyDef?.getValue(true) as string[]) ?? [], // NUEVO
    categories: (choicesCategories?.getValue(true) as string[]) ?? [], // NUEVO
  };
}

function destroyChoices() {
  choicesMunicipiN?.destroy();
  choicesMunicipiN = null;
  choicesProvincia?.destroy();
  choicesProvincia = null;
  choicesAnyNaix?.destroy();
  choicesAnyNaix = null;
  choicesEstatCivil?.destroy();
  choicesEstatCivil = null;
  choicesEstudis?.destroy();
  choicesEstudis = null;
  choicesOfici?.destroy();
  choicesOfici = null;
  choicesMunicipiD?.destroy();
  choicesMunicipiD = null;
  choicesSexe?.destroy();
  choicesSexe = null;
  choicesPartits?.destroy();
  choicesPartits = null;
  choicesSindicats?.destroy();
  choicesSindicats = null;
  choicesCauses?.destroy();
  choicesCauses = null;
  choicesAnyDef?.destroy();
  choicesAnyDef = null;
  choicesCategories?.destroy();
  choicesCategories = null;
}

// ====== Comptadors + opcions disponibles ======
function countBy<T extends string | number>(arr: T[]): Map<T, number> {
  const m = new Map<T, number>();
  for (const v of arr) m.set(v, (m.get(v) || 0) + 1);
  return m;
}

function calcAvailableWithCounts(op: OpcionesFiltros, personas: Persona[]) {
  const muniNCounts = countBy(personas.map((p) => p.municipi_naixement).filter(Boolean) as number[]);
  const municipisN = op.municipis.filter((m) => muniNCounts.has(m.id)).map((m) => ({ ...m, __count: muniNCounts.get(m.id) || 0 }));

  const provs = personas.map((p) => getProvinciaByPersona(p)).filter((x) => x && x.trim() !== '');
  const provCounts = countBy(provs);
  const provincies = Array.from(provCounts.entries()).map(([prov, cnt]) => ({ prov, __count: cnt }));

  // NUEVO: Años de nacimiento
  const anysN = personas.map((p) => parseYear(p.data_naixement)).filter((x): x is number => typeof x === 'number');
  const anysNCounts = countBy(anysN);
  const anysNaixement = uniqueSortedNumbers(anysN).map((y) => ({ any: y, __count: anysNCounts.get(y) || 0 }));

  const estCounts = countBy(personas.map((p) => p.estat_civil).filter(Boolean) as number[]);
  const estats = op.estats_civils.filter((e) => estCounts.has(e.id)).map((e) => ({ ...e, __count: estCounts.get(e.id) || 0 }));

  const estuCounts = countBy(personas.map((p) => p.estudis).filter(Boolean) as number[]);
  const estudis = op.estudis.filter((e) => estuCounts.has(e.id)).map((e) => ({ ...e, __count: estuCounts.get(e.id) || 0 }));

  const ofiCounts = countBy(personas.map((p) => p.ofici).filter(Boolean) as number[]);
  const oficis = op.oficis.filter((o) => ofiCounts.has(o.id)).map((o) => ({ ...o, __count: ofiCounts.get(o.id) || 0 }));

  const muniDCounts = countBy(personas.map((p) => p.municipi_defuncio).filter(Boolean) as number[]);
  const municipisD = op.municipis.filter((m) => muniDCounts.has(m.id)).map((m) => ({ ...m, __count: muniDCounts.get(m.id) || 0 }));

  const sexeCounts = countBy(personas.map((p) => p.sexe).filter((x): x is number => typeof x === 'number'));
  const sexes = [1, 2]
    .filter((id) => sexeCounts.has(id))
    .map((id) => ({
      id,
      label: id === 1 ? 'Home' : 'Dona',
      __count: sexeCounts.get(id) || 0,
    }));

  const partitIdsAll: number[] = [];
  for (const p of personas) if (p.filiacio_politica) partitIdsAll.push(...p.filiacio_politica);
  const partitCounts = countBy(partitIdsAll);
  const partits = op.partits.filter((pp) => partitCounts.has(pp.id)).map((pp) => ({ ...pp, __count: partitCounts.get(pp.id) || 0 }));

  const sindIdsAll: number[] = [];
  for (const p of personas) if (p.filiacio_sindical) sindIdsAll.push(...p.filiacio_sindical);
  const sindCounts = countBy(sindIdsAll);
  const sindicats = op.sindicats.filter((s) => sindCounts.has(s.id)).map((s) => ({ ...s, __count: sindCounts.get(s.id) || 0 }));

  const causeCounts = countBy(personas.map((p) => p.causa_defuncio).filter((x): x is number => typeof x === 'number'));
  const causes = op.causes.filter((c) => causeCounts.has(c.id)).map((c) => ({ ...c, __count: causeCounts.get(c.id) || 0 }));

  // NUEVO: Años de defunción
  const anysD = personas.map((p) => parseYear(p.data_defuncio)).filter((x): x is number => typeof x === 'number');
  const anysDCounts = countBy(anysD);
  const anysDefuncio = uniqueSortedNumbers(anysD).map((y) => ({ any: y, __count: anysDCounts.get(y) || 0 }));

  // NUEVO: Categorías repressió
  const catIdsAll: number[] = [];
  for (const p of personas) if (p.categoria) catIdsAll.push(...p.categoria);
  const catCounts = countBy(catIdsAll);
  const categories = op.categories ? op.categories.filter((c) => catCounts.has(c.id)).map((c) => ({ ...c, __count: catCounts.get(c.id) || 0 })) : [];

  return {
    municipisN,
    provincies,
    anysNaixement,
    estats,
    estudis,
    oficis,
    municipisD,
    sexes,
    partits,
    sindicats,
    causes,
    anysDefuncio,
    categories,
  };
}

function intersectSelections(sel: SelectionState, avail: ReturnType<typeof calcAvailableWithCounts>): SelectionState {
  const muniNAvail = new Set(avail.municipisN.map((m) => String(m.id)));
  const provAvail = new Set(avail.provincies.map((p) => p.prov));
  const anysNAvail = new Set(avail.anysNaixement.map((a) => String(a.any)));
  const estAvail = new Set(avail.estats.map((e) => String(e.id)));
  const estuAvail = new Set(avail.estudis.map((e) => String(e.id)));
  const ofiAvail = new Set(avail.oficis.map((o) => String(o.id)));
  const muniDAvail = new Set(avail.municipisD.map((m) => String(m.id)));
  const sexeAvail = new Set(avail.sexes.map((s) => String(s.id)));
  const partitAvail = new Set(avail.partits.map((p) => String(p.id)));
  const sindAvail = new Set(avail.sindicats.map((s) => String(s.id)));
  const causeAvail = new Set(avail.causes.map((c) => String(c.id)));
  const anysDAvail = new Set(avail.anysDefuncio.map((a) => String(a.any)));
  const catAvail = new Set(avail.categories.map((c) => String(c.id)));

  return {
    municipis_naixement: sel.municipis_naixement.filter((v) => muniNAvail.has(v)),
    provincies: sel.provincies.filter((v) => provAvail.has(v)),
    anys_naixement: sel.anys_naixement.filter((v) => anysNAvail.has(v)),
    estats: sel.estats.filter((v) => estAvail.has(v)),
    estudis: sel.estudis.filter((v) => estuAvail.has(v)),
    oficis: sel.oficis.filter((v) => ofiAvail.has(v)),
    municipis_defuncio: sel.municipis_defuncio.filter((v) => muniDAvail.has(v)),
    sexes: sel.sexes.filter((v) => sexeAvail.has(v)),
    partits: sel.partits.filter((v) => partitAvail.has(v)),
    sindicats: sel.sindicats.filter((v) => sindAvail.has(v)),
    causes: sel.causes.filter((v) => causeAvail.has(v)),
    anys_defuncio: sel.anys_defuncio.filter((v) => anysDAvail.has(v)),
    categories: sel.categories.filter((v) => catAvail.has(v)),
  };
}

// ====== Render filtres amb comptadors ======
function renderFiltros(opciones: OpcionesFiltros, personas: Persona[], keepSelection?: SelectionState) {
  const container = document.getElementById('filtros');
  if (!container) return;

  const avail = calcAvailableWithCounts(opciones, personas);

  // Orden solicitado:
  // ... Municipi, Provincia, Any Naixement, Estat civil, Estudis, Ofici, Municipi defunció, Sexe, Partits, Sindicats, Causes, Any defunció, Categoria repressió
  container.innerHTML = `
  <div class="filtro-grupo"><label for="filtro-categoria">Categoria repressió</label><select id="filtro-categoria" multiple></select></div>
  <div class="filtro-grupo"><label for="filtro-municipi_naixement">Municipi de naixement</label><select id="filtro-municipi_naixement" multiple></select></div>
  <div class="filtro-grupo"><label for="filtro-provincia_naixement">Província</label><select id="filtro-provincia_naixement" multiple></select></div>
  <div class="filtro-grupo"><label for="filtro-any_naixement">Any naixement</label><select id="filtro-any_naixement" multiple></select></div>
  <div class="filtro-grupo"><label for="filtro-any_defuncio">Any defunció</label><select id="filtro-any_defuncio" multiple></select></div>
  <div class="filtro-grupo"><label for="filtro-estat_civil">Estat civil</label><select id="filtro-estat_civil" multiple></select></div>
  <div class="filtro-grupo"><label for="filtro-estudis">Estudis</label><select id="filtro-estudis" multiple></select></div>
  <div class="filtro-grupo"><label for="filtro-ofici">Ofici</label><select id="filtro-ofici" multiple></select></div>
  <div class="filtro-grupo"><label for="filtro-municipi_defuncio">Municipi de defunció</label><select id="filtro-municipi_defuncio" multiple></select></div>
  <div class="filtro-grupo"><label for="filtro-sexe">Sexe</label><select id="filtro-sexe" multiple></select></div>
  <div class="filtro-grupo"><label for="filtro-partits">Filiació política</label><select id="filtro-partits" multiple></select></div>
  <div class="filtro-grupo"><label for="filtro-sindicats">Filiació sindical</label><select id="filtro-sindicats" multiple></select></div>
  <div class="filtro-grupo"><label for="filtro-causes">Causa de defunció</label><select id="filtro-causes" multiple></select></div>
`;

  // omplir selects amb comptadors
  const sMunicipiN = document.getElementById('filtro-municipi_naixement') as HTMLSelectElement;
  avail.municipisN.forEach((m) => {
    const op = document.createElement('option');
    op.value = String(m.id);
    op.text = `${m.ciutat} (${m.__count})`;
    sMunicipiN.appendChild(op);
  });

  const sProvincia = document.getElementById('filtro-provincia_naixement') as HTMLSelectElement;
  avail.provincies.forEach((p) => {
    const op = document.createElement('option');
    op.value = p.prov;
    op.text = `${p.prov} (${p.__count})`;
    sProvincia.appendChild(op);
  });

  const sAnyNaix = document.getElementById('filtro-any_naixement') as HTMLSelectElement;
  avail.anysNaixement.forEach((a) => {
    const op = document.createElement('option');
    op.value = String(a.any);
    op.text = `${a.any} (${a.__count})`;
    sAnyNaix.appendChild(op);
  });

  const sEstat = document.getElementById('filtro-estat_civil') as HTMLSelectElement;
  avail.estats.forEach((e) => {
    const op = document.createElement('option');
    op.value = String(e.id);
    op.text = `${e.estat_cat} (${e.__count})`;
    sEstat.appendChild(op);
  });

  const sEstudis = document.getElementById('filtro-estudis') as HTMLSelectElement;
  avail.estudis.forEach((e) => {
    const op = document.createElement('option');
    op.value = String(e.id);
    op.text = `${e.estudi_cat} (${e.__count})`;
    sEstudis.appendChild(op);
  });

  const sOfici = document.getElementById('filtro-ofici') as HTMLSelectElement;
  avail.oficis.forEach((o) => {
    const op = document.createElement('option');
    op.value = String(o.id);
    op.text = `${o.ofici_cat} (${o.__count})`;
    sOfici.appendChild(op);
  });

  const sMunicipiD = document.getElementById('filtro-municipi_defuncio') as HTMLSelectElement;
  avail.municipisD.forEach((m) => {
    const op = document.createElement('option');
    op.value = String(m.id);
    op.text = `${m.ciutat} (${m.__count})`;
    sMunicipiD.appendChild(op);
  });

  const sSexe = document.getElementById('filtro-sexe') as HTMLSelectElement;
  avail.sexes.forEach((s) => {
    const op = document.createElement('option');
    op.value = String(s.id);
    op.text = `${s.label} (${s.__count})`;
    sSexe.appendChild(op);
  });

  const sPartits = document.getElementById('filtro-partits') as HTMLSelectElement;
  avail.partits.forEach((p) => {
    const label = p.sigles ? `${p.partit_politic} (${p.sigles})` : p.partit_politic;
    const op = document.createElement('option');
    op.value = String(p.id);
    op.text = `${label} (${p.__count})`;
    sPartits.appendChild(op);
  });

  const sSindicats = document.getElementById('filtro-sindicats') as HTMLSelectElement;
  avail.sindicats.forEach((s) => {
    const op = document.createElement('option');
    op.value = String(s.id);
    op.text = `${sindicatName(s)} (${s.__count})`;
    sSindicats.appendChild(op);
  });

  const sCauses = document.getElementById('filtro-causes') as HTMLSelectElement;
  avail.causes.forEach((c) => {
    const op = document.createElement('option');
    op.value = String(c.id);
    op.text = `${causaName(c)} (${c.__count})`;
    sCauses.appendChild(op);
  });

  const sAnyDef = document.getElementById('filtro-any_defuncio') as HTMLSelectElement;
  avail.anysDefuncio.forEach((a) => {
    const op = document.createElement('option');
    op.value = String(a.any);
    op.text = `${a.any} (${a.__count})`;
    sAnyDef.appendChild(op);
  });

  const sCategories = document.getElementById('filtro-categoria') as HTMLSelectElement;
  avail.categories.forEach((c) => {
    const op = document.createElement('option');
    op.value = String(c.id);
    op.text = `${categoriaName(c)} (${c.__count})`;
    sCategories.appendChild(op);
  });

  // re-instanciar Choices
  destroyChoices();
  const common = { removeItemButton: true, searchEnabled: true, shouldSort: false, itemSelectText: '' };
  choicesMunicipiN = new Choices('#filtro-municipi_naixement', { ...common, searchPlaceholderValue: 'Cerca municipis...' });
  choicesProvincia = new Choices('#filtro-provincia_naixement', { ...common, searchPlaceholderValue: 'Cerca províncies...' });
  choicesAnyNaix = new Choices('#filtro-any_naixement', { ...common, searchPlaceholderValue: 'Cerca anys...' }); // NUEVO
  choicesEstatCivil = new Choices('#filtro-estat_civil', { ...common, searchPlaceholderValue: 'Cerca estat civil...' });
  choicesEstudis = new Choices('#filtro-estudis', { ...common, searchPlaceholderValue: 'Cerca estudis...' });
  choicesOfici = new Choices('#filtro-ofici', { ...common, searchPlaceholderValue: 'Cerca oficis...' });
  choicesMunicipiD = new Choices('#filtro-municipi_defuncio', { ...common, searchPlaceholderValue: 'Cerca municipis...' });
  choicesSexe = new Choices('#filtro-sexe', { ...common, searchPlaceholderValue: 'Cerca...' });
  choicesPartits = new Choices('#filtro-partits', { ...common, searchPlaceholderValue: 'Cerca partits...' });
  choicesSindicats = new Choices('#filtro-sindicats', { ...common, searchPlaceholderValue: 'Cerca sindicats...' });
  choicesCauses = new Choices('#filtro-causes', { ...common, searchPlaceholderValue: 'Cerca causes...' });
  choicesAnyDef = new Choices('#filtro-any_defuncio', { ...common, searchPlaceholderValue: 'Cerca anys...' }); // NUEVO
  choicesCategories = new Choices('#filtro-categoria', { ...common, searchPlaceholderValue: 'Cerca categories...' });

  // restaurar selecció vàlida
  if (keepSelection) {
    const inter = intersectSelections(keepSelection, avail);
    if (inter.municipis_naixement.length) choicesMunicipiN!.setChoiceByValue(inter.municipis_naixement);
    if (inter.provincies.length) choicesProvincia!.setChoiceByValue(inter.provincies);
    if (inter.anys_naixement.length) choicesAnyNaix!.setChoiceByValue(inter.anys_naixement);
    if (inter.estats.length) choicesEstatCivil!.setChoiceByValue(inter.estats);
    if (inter.estudis.length) choicesEstudis!.setChoiceByValue(inter.estudis);
    if (inter.oficis.length) choicesOfici!.setChoiceByValue(inter.oficis);
    if (inter.municipis_defuncio.length) choicesMunicipiD!.setChoiceByValue(inter.municipis_defuncio);
    if (inter.sexes.length) choicesSexe!.setChoiceByValue(inter.sexes);
    if (inter.partits.length) choicesPartits!.setChoiceByValue(inter.partits);
    if (inter.sindicats.length) choicesSindicats!.setChoiceByValue(inter.sindicats);
    if (inter.causes.length) choicesCauses!.setChoiceByValue(inter.causes);
    if (inter.anys_defuncio.length) choicesAnyDef!.setChoiceByValue(inter.anys_defuncio);
    if (inter.categories.length) choicesCategories!.setChoiceByValue(inter.categories);
  }

  // events
  choicesMunicipiN!.passedElement.element.addEventListener('change', onCriteriaChange);
  choicesProvincia!.passedElement.element.addEventListener('change', onCriteriaChange);
  choicesAnyNaix!.passedElement.element.addEventListener('change', onCriteriaChange);
  choicesEstatCivil!.passedElement.element.addEventListener('change', onCriteriaChange);
  choicesEstudis!.passedElement.element.addEventListener('change', onCriteriaChange);
  choicesOfici!.passedElement.element.addEventListener('change', onCriteriaChange);
  choicesMunicipiD!.passedElement.element.addEventListener('change', onCriteriaChange);
  choicesSexe!.passedElement.element.addEventListener('change', onCriteriaChange);
  choicesPartits!.passedElement.element.addEventListener('change', onCriteriaChange);
  choicesSindicats!.passedElement.element.addEventListener('change', onCriteriaChange);
  choicesCauses!.passedElement.element.addEventListener('change', onCriteriaChange);
  choicesAnyDef!.passedElement.element.addEventListener('change', onCriteriaChange);
  choicesCategories!.passedElement.element.addEventListener('change', onCriteriaChange);
}

// ====== Render “chips” de filtres actius ======
function labelForFilter(key: keyof SelectionState, value: string): string {
  switch (key) {
    case 'municipis_naixement': {
      const m = opcionesGlobales.municipis.find((x) => String(x.id) === value);
      return m ? `Naixement: ${m.ciutat}` : value;
    }
    case 'provincies':
      return `Província: ${value}`;
    case 'anys_naixement':
      return `Any naixement: ${value}`;
    case 'estats': {
      const e = opcionesGlobales.estats_civils.find((x) => String(x.id) === value);
      return e ? `Estat civil: ${e.estat_cat}` : value;
    }
    case 'estudis': {
      const e = opcionesGlobales.estudis.find((x) => String(x.id) === value);
      return e ? `Estudis: ${e.estudi_cat}` : value;
    }
    case 'oficis': {
      const o = opcionesGlobales.oficis.find((x) => String(x.id) === value);
      return o ? `Ofici: ${o.ofici_cat}` : value;
    }
    case 'municipis_defuncio': {
      const m = opcionesGlobales.municipis.find((x) => String(x.id) === value);
      return m ? `Defunció: ${m.ciutat}` : value;
    }
    case 'sexes':
      return `Sexe: ${value === '1' ? 'Home' : 'Dona'}`;
    case 'partits': {
      const p = opcionesGlobales.partits.find((x) => String(x.id) === value);
      return p ? `Partit: ${p.partit_politic}${p.sigles ? ` (${p.sigles})` : ''}` : value;
    }
    case 'sindicats': {
      const s = opcionesGlobales.sindicats.find((x) => String(x.id) === value);
      return s ? `Sindicat: ${sindicatName(s)}` : value;
    }
    case 'causes': {
      const c = opcionesGlobales.causes.find((x) => String(x.id) === value);
      return c ? `Causa: ${causaName(c)}` : value;
    }
    case 'anys_defuncio':
      return `Any defunció: ${value}`;
    case 'categories': {
      const c = opcionesGlobales.categories.find((x) => String(x.id) === value);
      return c ? `Categoria: ${categoriaName(c)}` : value;
    }
    default:
      return value;
  }
}

function renderActiveChips(sel: SelectionState) {
  // assegura contenidors
  let chips = document.getElementById('filtres-actius');
  if (!chips) {
    chips = document.createElement('div');
    chips.id = 'filtres-actius';
    chips.style.margin = '8px 0';
    const resultados = document.getElementById('resultados');
    resultados?.insertBefore(chips, resultados.firstChild);
  }
  let orden = document.getElementById('ordenacio');
  if (!orden) {
    orden = document.createElement('div');
    orden.id = 'ordenacio';
    orden.style.margin = '8px 0 12px 0';
    chips?.insertAdjacentElement('afterend', orden);
  }

  // chips
  const parts: string[] = [];
  (Object.keys(sel) as (keyof SelectionState)[]).forEach((key) => {
    const vals = sel[key];
    vals.forEach((v) => {
      const text = labelForFilter(key, v);
      parts.push(
        `<span class="chip" data-filter="${key}" data-value="${v}" style="display:inline-flex;align-items:center;margin:4px; padding:2px 8px; border:1px solid #ddd; border-radius:999px; font-size:12px;">
          ${text}
          <button type="button" aria-label="Treu filtre" style="margin-left:6px;border:none;background:transparent;cursor:pointer;font-weight:bold;">×</button>
        </span>`
      );
    });
  });
  chips!.innerHTML = parts.length ? parts.join('') : `<span style="color:#666;font-size:12px;">Sense filtres actius</span>`;

  // click per treure
  chips!.querySelectorAll('.chip button').forEach((btn) => {
    btn.addEventListener('click', (ev) => {
      const wrap = (ev.currentTarget as HTMLElement).closest('.chip') as HTMLElement | null;
      if (!wrap) return;
      const key = wrap.getAttribute('data-filter') as keyof SelectionState;
      const val = wrap.getAttribute('data-value') || '';

      // llevar valor de Choices corresponent
      switch (key) {
        case 'municipis_naixement':
          choicesMunicipiN?.removeActiveItemsByValue(val);
          break;
        case 'provincies':
          choicesProvincia?.removeActiveItemsByValue(val);
          break;
        case 'anys_naixement':
          choicesAnyNaix?.removeActiveItemsByValue(val);
          break;
        case 'estats':
          choicesEstatCivil?.removeActiveItemsByValue(val);
          break;
        case 'estudis':
          choicesEstudis?.removeActiveItemsByValue(val);
          break;
        case 'oficis':
          choicesOfici?.removeActiveItemsByValue(val);
          break;
        case 'municipis_defuncio':
          choicesMunicipiD?.removeActiveItemsByValue(val);
          break;
        case 'sexes':
          choicesSexe?.removeActiveItemsByValue(val);
          break;
        case 'partits':
          choicesPartits?.removeActiveItemsByValue(val);
          break;
        case 'sindicats':
          choicesSindicats?.removeActiveItemsByValue(val);
          break;
        case 'causes':
          choicesCauses?.removeActiveItemsByValue(val);
          break;
        case 'anys_defuncio':
          choicesAnyDef?.removeActiveItemsByValue(val);
          break;
        case 'categories':
          choicesCategories?.removeActiveItemsByValue(val);
          break;
      }
      onCriteriaChange();
    });
  });

  // ordenació
  (orden as HTMLElement).innerHTML = `
    <label style="font-size:12px;color:#666;display:inline-block;margin-right:8px;">Ordena per:</label>
    <select id="ordre" style="padding:6px 8px;border:1px solid #ddd;border-radius:6px;">
      <option value="cognoms"${sortKey === 'cognoms' ? ' selected' : ''}>Cognoms</option>
      <option value="nom"${sortKey === 'nom' ? ' selected' : ''}>Nom</option>
      <option value="municipi"${sortKey === 'municipi' ? ' selected' : ''}>Municipi de naixement</option>
    </select>
  `;
  document.getElementById('ordre')?.addEventListener('change', (e) => {
    sortKey = ((e.target as HTMLSelectElement).value as SortKey) || 'cognoms';
    currentPage = 1;
    applySortAndRender();
  });
}

// ====== Filtrat ======
function filtrarPersonas(personas: Persona[], _opciones: OpcionesFiltros, textoBusqueda: string, sel: SelectionState): Persona[] {
  const muniN = sel.municipis_naixement;
  const provs = new Set(sel.provincies.map(norm));
  const anysNaix = new Set(sel.anys_naixement.map(Number));
  const estats = sel.estats;
  const estudis = sel.estudis;
  const oficis = sel.oficis;
  const muniD = sel.municipis_defuncio;
  const sexes = sel.sexes;
  const partits = new Set(sel.partits.map(Number));
  const sindicats = new Set(sel.sindicats.map(Number));
  const causes = sel.causes;
  const anysDef = new Set(sel.anys_defuncio.map(Number));
  const categories = new Set(sel.categories.map(Number));

  return personas.filter((p) => {
    const matchMuniN = muniN.length === 0 || muniN.includes(String(p.municipi_naixement));
    const provPersona = norm(getProvinciaByPersona(p));
    const matchProv = provs.size === 0 || provs.has(provPersona);

    const anyN = parseYear(p.data_naixement);
    const matchAnyNaix = anysNaix.size === 0 || (typeof anyN === 'number' && anysNaix.has(anyN));

    const matchEstat = estats.length === 0 || estats.includes(String(p.estat_civil));
    const matchEstudis = estudis.length === 0 || estudis.includes(String(p.estudis));
    const matchOfici = oficis.length === 0 || oficis.includes(String(p.ofici));
    const matchMuniD = muniD.length === 0 || (p.municipi_defuncio ? muniD.includes(String(p.municipi_defuncio)) : false);
    const matchSexe = sexes.length === 0 || (p.sexe ? sexes.includes(String(p.sexe)) : false);

    const poli = p.filiacio_politica || [];
    const sind = p.filiacio_sindical || [];
    const matchPartits = partits.size === 0 || poli.some((id) => partits.has(id));
    const matchSindicats = sindicats.size === 0 || sind.some((id) => sindicats.has(id));
    const matchCauses = causes.length === 0 || (p.causa_defuncio ? causes.includes(String(p.causa_defuncio)) : false);

    const anyD = parseYear(p.data_defuncio);
    const matchAnyDef = anysDef.size === 0 || (typeof anyD === 'number' && anysDef.has(anyD));

    const matchCategories = categories.size === 0 || Array.from(categories).every((id) => (p.categoria || []).includes(id));

    const t = textoBusqueda.trim().toLowerCase();
    const full = `${p.nom} ${p.cognom1} ${p.cognom2}`.toLowerCase();
    const matchText = t === '' || full.includes(t);

    return matchMuniN && matchProv && matchAnyNaix && matchEstat && matchEstudis && matchOfici && matchMuniD && matchSexe && matchPartits && matchSindicats && matchCauses && matchAnyDef && matchCategories && matchText;
  });
}

// ====== Ordenació ======
function sortResults(arr: Persona[]): Persona[] {
  const copy = [...arr];
  if (sortKey === 'cognoms') {
    copy.sort((a, b) => norm(`${a.cognom1} ${a.cognom2}`).localeCompare(norm(`${b.cognom1} ${b.cognom2}`)) || norm(a.nom).localeCompare(norm(b.nom)));
  } else if (sortKey === 'nom') {
    copy.sort((a, b) => norm(a.nom).localeCompare(norm(b.nom)) || norm(`${a.cognom1} ${a.cognom2}`).localeCompare(norm(`${b.cognom1} ${b.cognom2}`)));
  } else if (sortKey === 'municipi') {
    copy.sort((a, b) => {
      const ma = municipiById.get(a.municipi_naixement)?.ciutat || '';
      const mb = municipiById.get(b.municipi_naixement)?.ciutat || '';
      return norm(ma).localeCompare(norm(mb)) || norm(fullName(a)).localeCompare(norm(fullName(b)));
    });
  }
  return copy;
}

// ====== Render resultats + paginació ======
function renderListaPersonasPaginated(personas: Persona[], opciones: OpcionesFiltros) {
  const cont = document.getElementById('tabla-resultados');
  const info = document.getElementById('pageInfo');
  const prev = document.getElementById('prevPage') as HTMLButtonElement | null;
  const next = document.getElementById('nextPage') as HTMLButtonElement | null;
  const contador = document.getElementById('contador-resultados');

  if (!cont || !info || !prev || !next || !contador) return;

  const total = personas.length;
  const totalPages = Math.max(1, Math.ceil(total / PAGE_SIZE));
  currentPage = Math.min(Math.max(1, currentPage), totalPages);

  const startIdx = (currentPage - 1) * PAGE_SIZE;
  const endIdx = Math.min(startIdx + PAGE_SIZE, total);
  const pageSlice = personas.slice(startIdx, endIdx);

  if (pageSlice.length === 0) {
    cont.innerHTML = '<p>No hi ha resultats.</p>';
  } else {
    cont.innerHTML = pageSlice
      .map((p) => {
        const municipioN = opciones.municipis.find((m) => m.id === p.municipi_naixement)?.ciutat || '';
        const municipioD = p.municipi_defuncio ? opciones.municipis.find((m) => m.id === p.municipi_defuncio)?.ciutat || '' : '';

        const href = `https://memoriaterrassa.cat/fitxa/${p.slug}`;
        const dataNaixement = formatDatesForm(p.data_naixement) || '';
        const dataDefuncio = formatDatesForm(p.data_defuncio) || '';

        return `
      <div class="fila-persona">
        <div>
          <strong>
            <a href="${href}" target="_blank" rel="noopener noreferrer">
              ${p.nom} ${p.cognom1} ${p.cognom2}
            </a>
          </strong><br/>
          <small>
            ${dataNaixement}${municipioN ? ` (${municipioN})` : ''}
            ${dataDefuncio ? ` / ${dataDefuncio}${municipioD ? ` (${municipioD})` : ''}` : ''}
          </small>
        </div>
      </div>`;
      })
      .join('');
  }

  contador.textContent = total === 0 ? '0 resultats' : `Mostrant ${startIdx + 1}–${endIdx} de ${total} resultats`;

  info.textContent = `Pàgina ${currentPage} / ${totalPages}`;
  prev.disabled = currentPage <= 1;
  next.disabled = currentPage >= totalPages;

  prev.onclick = () => {
    if (currentPage > 1) {
      currentPage--;
      renderListaPersonasPaginated(personas, opciones);
      scrollToTopOfResults();
    }
  };
  next.onclick = () => {
    if (currentPage < totalPages) {
      currentPage++;
      renderListaPersonasPaginated(personas, opciones);
      scrollToTopOfResults();
    }
  };
}

function scrollToTopOfResults() {
  const resultados = document.getElementById('resultados');
  if (resultados) resultados.scrollIntoView({ behavior: 'smooth', block: 'start' });
}

// ====== Orquestració canvis ======
function onCriteriaChange() {
  currentPage = 1;
  const texto = (document.getElementById('buscador-nom') as HTMLInputElement)?.value || '';
  const currentSel = getSelections();
  resultadosFiltrados = filtrarPersonas(datosOriginales, opcionesGlobales, texto, currentSel);

  // chips + ordenació UI
  renderActiveChips(currentSel);

  // re-render filtres dependents amb comptadors
  renderFiltros(opcionesGlobales, resultadosFiltrados, currentSel);

  // després de re-crear filtres, tornem a pintar chips
  renderActiveChips(currentSel);

  // aplicar ordenació + pintar
  applySortAndRender();
}

function applySortAndRender() {
  const ordenats = sortResults(resultadosFiltrados);
  renderListaPersonasPaginated(ordenats, opcionesGlobales);
}

// ====== Reset real ======
function resetearFiltros() {
  const input = document.getElementById('buscador-nom') as HTMLInputElement | null;
  if (input) input.value = '';

  currentPage = 1;
  resultadosFiltrados = [...datosOriginales];

  const emptySel: SelectionState = {
    municipis_naixement: [],
    provincies: [],
    anys_naixement: [], // NUEVO
    estats: [],
    estudis: [],
    oficis: [],
    municipis_defuncio: [],
    sexes: [],
    partits: [],
    sindicats: [],
    causes: [],
    anys_defuncio: [], // NUEVO
    categories: [], // NUEVO
  };

  renderActiveChips(emptySel);
  renderFiltros(opcionesGlobales, resultadosFiltrados, emptySel);
  applySortAndRender();
}

// ====== Init ======
export async function iniciarBuscador() {
  try {
    const [personas, opciones] = await Promise.all([fetchPersonas(), fetchOpcionesFiltros()]);
    datosOriginales = personas;
    opcionesGlobales = opciones;

    municipiById.clear();
    for (const m of opcionesGlobales.municipis) municipiById.set(m.id, m);

    resultadosFiltrados = [...datosOriginales];

    const emptySel: SelectionState = {
      municipis_naixement: [],
      provincies: [],
      anys_naixement: [], // NUEVO
      estats: [],
      estudis: [],
      oficis: [],
      municipis_defuncio: [],
      sexes: [],
      partits: [],
      sindicats: [],
      causes: [],
      anys_defuncio: [], // NUEVO
      categories: [], // NUEVO
    };

    // UI inicial
    renderActiveChips(emptySel);
    renderFiltros(opcionesGlobales, resultadosFiltrados, emptySel);
    applySortAndRender();

    // buscador de text
    const buscadorInput = document.getElementById('buscador-nom') as HTMLInputElement | null;
    buscadorInput?.addEventListener('input', onCriteriaChange);

    // reset
    document.getElementById('btn-reset')?.addEventListener('click', resetearFiltros);
  } catch (e) {
    console.error('Error inicialitzant el cercador', e);
  }
}
