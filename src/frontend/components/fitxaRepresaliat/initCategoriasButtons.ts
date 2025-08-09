import { traduirCategoriesRepressioArray } from '../taulaDades/traduirCategoriesRepressio';

import { mostrarCategoria } from './mostrarCategoria';
import { categoriesRepressio } from '../taulaDades/categoriesRepressio';

export async function initCategoriaButtons(categoriasNumericasString: string, idPersona: number): Promise<void> {
  try {
    // Obtener categorías (array de objetos {id, name}) para el idioma catalán
    const colectiusRepressio = await categoriesRepressio('ca');
    const nombresCategorias = traduirCategoriesRepressioArray(categoriasNumericasString, colectiusRepressio);
    const contenedorCategorias = document.getElementById('botons2');

    if (!contenedorCategorias) {
      console.warn('initCategoriaButtons: no se encontró el contenedor de botons2');
      return;
    }

    contenedorCategorias.innerHTML = '';

    const categoriaIds = categoriasNumericasString.replace(/[{}]/g, '').split(',').map(Number);

    nombresCategorias.forEach((nombre, index) => {
      const catNum = categoriaIds[index];

      const btn = document.createElement('button');
      btn.className = 'botoCategoriaRepresio';
      btn.innerText = nombre;
      btn.dataset.tab = `categoria${catNum}`;
      btn.style.marginRight = '25px';
      btn.style.marginTop = '25px';

      btn.onclick = async () => {
        const divInfo = document.getElementById('fitxa-categoria');
        if (!divInfo) return;

        const currentCategoria = String(catNum);
        const isActive = divInfo.style.display === 'block' && divInfo.dataset.categoria === currentCategoria;

        if (isActive) {
          divInfo.style.display = 'none';
          divInfo.dataset.categoria = '';
          btn.classList.remove('active');
          return;
        }

        divInfo.dataset.categoria = currentCategoria;
        divInfo.innerHTML = '';

        const allButtons = contenedorCategorias.getElementsByClassName('botoCategoriaRepresio');
        Array.from(allButtons).forEach((b) => b.classList.remove('active'));
        btn.classList.add('active');

        await mostrarCategoria(currentCategoria, idPersona);

        divInfo.style.display = 'block';
      };

      contenedorCategorias.appendChild(btn);
    });
  } catch (error) {
    console.error('Error al generar botones de categoría:', error);
  }
}
