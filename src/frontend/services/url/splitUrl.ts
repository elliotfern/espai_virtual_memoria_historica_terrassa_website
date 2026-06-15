// Verificar la URL y llamar a las funciones correspondientes

export function getPageType(url: string): string[] {
  try {
    const pathname = new URL(url).pathname; // "/acces" o "/gestio/admin"
    const partes = pathname.split('/').filter(Boolean); // elimina strings vacíos
    return partes;
  } catch {
    return [];
  }
}
