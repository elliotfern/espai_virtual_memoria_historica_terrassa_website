import { fetchFitxa } from '../../services/fetchData/fetchFitxa';
import { generarLinks } from './generarLinks';
import { categoriesRepressio } from '../taulaDades/categoriesRepressio';

export async function actualizarGenerarLinks(idRepressaliat: number): Promise<void> {
  const fitxa = await fetchFitxa(idRepressaliat);
  if (!fitxa) return;

  const lang = 'ca';

  // Obtenir el nom de les categories de la repressió
  const categorias = await categoriesRepressio(lang);

  // Transformar fitxa.categoria en array de ids (strings)
  const selectedValues = fitxa.categoria.replace(/[{}]/g, '').split(',');

  // Crear mapa id -> nombre para generarLinks (botones con nombre)
  const categoriasMap = categorias.reduce((acc, cat) => {
    acc[cat.id] = cat.name;
    return acc;
  }, {} as { [key: number]: string });

  generarLinks(selectedValues, categoriasMap, idRepressaliat);
}
