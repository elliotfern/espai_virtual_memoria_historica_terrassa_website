export function formatNumberSpanish(number: number): string {
  // Verificar tipo y valor antes de formatear
  const newNumber = new Intl.NumberFormat('es-ES', { maximumSignificantDigits: 3 }).format(number);
  return newNumber;
}
