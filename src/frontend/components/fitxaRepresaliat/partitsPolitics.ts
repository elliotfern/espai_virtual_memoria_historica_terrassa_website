import { Partit } from '../../types/partitPolitic';

export async function partitsPolitics(ids: number[]): Promise<string[]> {
  try {
    // Llamada a la API para obtener todos los partidos políticos
    const devDirectory = `https://${window.location.hostname}`;
    const url = `${devDirectory}/api/auxiliars/get/partitsPolitics`;

    const response = await fetch(url, {
      method: 'GET', // O cualquier otro método que necesites (POST, PUT, etc.)
      headers: {
        'Content-Type': 'application/json', // Opcional, dependiendo de lo que espera el backend
      },
      credentials: 'include', // Esto asegura que la cookie se envíe con la solicitud
    });
    if (!response.ok) throw new Error('Error al obtener los datos de la API');

    const partits: Partit[] = await response.json();

    // Filtrar los partidos que coinciden con los IDs proporcionados
    const partidosFiltrados = partits.filter((partit) => ids.includes(partit.id)).map((partit) => (partit.id === 10 ? partit.partit_politic : `${partit.partit_politic} (${partit.sigles})`));

    return partidosFiltrados;
  } catch (error) {
    console.error('Error al procesar los partidos:', error);
    return [];
  }
}
