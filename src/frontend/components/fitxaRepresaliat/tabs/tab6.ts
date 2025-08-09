// src/pages/fitxaRepresaliat/tabs/tab2.ts
import type { Fitxa } from '../../../types/types';
import { taulaArxius } from '../../modificaFitxaRepresaliat/taulaArxius';
import { taulaBibliografia } from '../../modificaFitxaRepresaliat/taulaBibliografia';

export function renderTab6(fitxa: Fitxa): void {
  const divInfo = document.getElementById('fitxa');

  if (!divInfo) return;

  divInfo.innerHTML = `<div id="bibliografia"></div>`;
  // Obtener el contenedor recién creado
  const bibliografiaContainer = document.getElementById('bibliografia');

  if (bibliografiaContainer) {
    // Crear los elementos dinámicamente
    const quadreFonts1 = document.createElement('div');
    quadreFonts1.id = 'quadreFonts1';

    const quadreBibliografia = document.createElement('div');
    quadreBibliografia.id = 'quadreFontsBibliografia';

    const quadreFonts2 = document.createElement('div');
    quadreFonts2.id = 'quadreFonts2';
    quadreFonts2.style.marginTop = '35px';

    const quadreArxius = document.createElement('div');
    quadreArxius.id = 'quadreFontsArxius';

    // Añadir los nuevos elementos al contenedor 'bibliografia'
    bibliografiaContainer.appendChild(quadreFonts1);
    bibliografiaContainer.appendChild(quadreFonts2);

    quadreFonts1.appendChild(quadreBibliografia);
    quadreFonts2.appendChild(quadreArxius);

    // Configurar y añadir contenido a los nuevos elementos
    if (quadreFonts1) {
      quadreFonts1.style.display = 'block';
      const h4 = document.createElement('h4');
      h4.textContent = 'Llistat de bibliografia';
      h4.classList.add('titolSeccio');
      quadreFonts1.prepend(h4);

      if (fitxa?.id) {
        taulaBibliografia(fitxa.id);
      }
    }

    if (quadreFonts2) {
      quadreFonts2.style.display = 'block';
      const h4 = document.createElement('h4');
      h4.textContent = "Llistat d'arxius";
      h4.classList.add('titolSeccio');
      quadreFonts2.prepend(h4);

      if (fitxa?.id) {
        taulaArxius(fitxa.id);
      }
    }
  }
}
