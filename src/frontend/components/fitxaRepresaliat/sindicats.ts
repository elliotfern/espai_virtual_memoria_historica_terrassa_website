import { Sindicat } from '../../types/sindicat';

export async function sindicats(ids: number[]): Promise<string[]> {
  try {
    // Llamada a la API para obtener todos los partidos políticos
    const devDirectory = `https://${window.location.hostname}`;
    const url = `${devDirectory}/api/auxiliars/get/sindicats`;

    const response = await fetch(url);
    if (!response.ok) throw new Error('Error al obtener los datos de la API');

    const sindicats: Sindicat[] = await response.json();

    // Filtrar los partidos que coinciden con los IDs proporcionados
    const sindicatsFiltrados = sindicats.filter((sindicat) => ids.includes(sindicat.id)).map((sindicat) => (sindicat.id === 4 ? sindicat.sindicat : `${sindicat.sindicat} (${sindicat.sigles})`));

    return sindicatsFiltrados;
  } catch (error) {
    console.error('Error al procesar los partidos:', error);
    return [];
  }
}
