// fitxaApi.ts
import { Fitxa } from '../../types/types';

export async function fetchFitxa(idRepressaliat: number): Promise<Fitxa | null> {
  const devDirectory = `https://${window.location.hostname}`;
  const urlAjax = `${devDirectory}/api/dades_personals/get/?type=fitxa&id=${idRepressaliat}`;

  try {
    const response = await fetch(urlAjax, {
      method: 'GET',
      headers: {
        'Content-Type': 'application/json',
      },
    });

    if (!response.ok) throw new Error('Error en la solicitud');

    const fitxaData: Fitxa[] = await response.json();
    return fitxaData[0] || null;
  } catch (error) {
    console.error('Error al obtener la fitxa:', error);
    return null;
  }
}
