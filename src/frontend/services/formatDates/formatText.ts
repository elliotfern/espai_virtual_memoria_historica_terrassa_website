export function normalizeText(text: string): string {
  return text
    .normalize('NFD')
    .replace(/[\u0300-\u036f]/g, '') // elimina tildes
    .toLowerCase()
    .replace(/\s+/g, ' ') // reduce múltiples espacios a uno solo
    .trim(); // elimina espacios al inicio/final
}
