// Verificar la URL y llamar a las funciones correspondientes

export function getPageType(url: string): string[] {
  // Extraer todo después de ".cat/"
  const partes = url.split('.cat/')[1]?.split('/') || [];
  return partes;
}
