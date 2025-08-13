// src/buscador/prov-map.ts
let MUNI_TO_PROV: Map<number, string> = new Map();

/** Inicializa o actualiza el mapa municipi → província (en minúsculas y trim). */
export function setMuniToProv(map: Map<number, string>): void {
  // normalizamos el valor a minúsculas+trim para comparar fácil
  const norm = new Map<number, string>();
  map.forEach((prov, id) => {
    norm.set(id, (prov || '').trim().toLowerCase());
  });
  MUNI_TO_PROV = norm;
}

/** Devuelve la província (normalizada) para un id de municipi, o '' si no hay. */
export function getProvinciaByMunicipi(id: number): string {
  return MUNI_TO_PROV.get(id) ?? '';
}
