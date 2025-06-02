import { Fitxa } from '../../types/types';
import { generarLinks } from './generarLinks';
import { categoriesRepressio } from '../taulaDades/categoriesRepressio';
import { crearBotoActualitzacioRepressio } from './crearBotonsRepressio';

export async function tab1(fitxa?: Fitxa, idRepressaliat?: number) {
  const lang = 'ca';

  // Obtenir el nom de les categories de la repressió
  const categorias = await categoriesRepressio(lang);

  if (fitxa && idRepressaliat) {
    const idInput = document.getElementById('id') as HTMLInputElement;
    if (idInput) idInput.value = fitxa.id.toString();

    // Transformar fitxa.categoria en array de ids (strings)
    const selectedValues = fitxa.categoria.replace(/[{}]/g, '').split(',');

    // Marcar checkboxes
    selectedValues.forEach((value) => {
      const checkbox = document.querySelector<HTMLInputElement>(`input[type="checkbox"][value="categoria${value}"]`);
      if (checkbox) {
        checkbox.checked = true;
      }
    });

    // Crear mapa id -> nombre para generarLinks (botones con nombre)
    const categoriasMap = categorias.reduce((acc, cat) => {
      acc[cat.id] = cat.name;
      return acc;
    }, {} as { [key: number]: string });

    // Pasar ids y mapa a generarLinks, que crea los botones con el nombre
    generarLinks(selectedValues, categoriasMap, idRepressaliat);

    // Actualizar botons
    crearBotoActualitzacioRepressio(idRepressaliat);
  }

  const mortCivilCheckbox = document.getElementById('categoria4') as HTMLInputElement | null;
  const represaliaRepublicanaCheckbox = document.getElementById('categoria5') as HTMLInputElement | null;

  if (mortCivilCheckbox && represaliaRepublicanaCheckbox) {
    represaliaRepublicanaCheckbox.addEventListener('change', () => {
      mortCivilCheckbox.checked = represaliaRepublicanaCheckbox.checked;
    });
  }
}
