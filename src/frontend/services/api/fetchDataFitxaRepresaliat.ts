import { DOMAIN_API } from '../../config/constants';
import { Fitxa } from '../../types/types';
import { fetchData } from './api';

export async function fetchDataFitxaRepresaliat(slug: string) {
  // Si los datos aún no están en cache, realizamos la consulta a la API

  const devDirectory = DOMAIN_API;

  const url = `${devDirectory}/api/dades_personals/get/?type=fitxaRepresaliat&slug=${slug}`;

  try {
    const data = await fetchData(url);

    if (Array.isArray(data)) {
      return data[0] as Fitxa;
    } else {
      throw new Error('La API no devolvió un array.');
    }
  } catch (error) {
    console.error('Error al obtener la información:', error);
    return;
  }
}
