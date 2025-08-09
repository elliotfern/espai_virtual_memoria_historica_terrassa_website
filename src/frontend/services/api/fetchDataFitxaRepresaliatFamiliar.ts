//import { Fitxa, FitxaJudicial, FitxaFamiliars, ApiResponse } from '../../types/types';
import { fetchData } from './api';

export async function fetchDataFitxaRepresaliatFamiliar(id: number) {
  // Si los datos aún no están en cache, realizamos la consulta a la API

  const devDirectory = `https://${window.location.hostname}`;
  const url2 = `${devDirectory}/api/dades_personals/get/?type=fitxaDadesFamiliars&id=${id}`;

  try {
    const dataFamiliars = await fetchData(url2);

    if (Array.isArray(dataFamiliars)) {
      return dataFamiliars; // Devuelve array aunque esté vacío
    } else if (dataFamiliars === null) {
      // La API devuelve null si no hay datos, retornamos array vacío
      return [];
    } else {
      throw new Error('La API no devolvió un array ni null.');
    }
  } catch (error) {
    console.error('Error al obtener la información:', error);
    return [];
  }
}
