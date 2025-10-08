// src/pages/fitxaRepresaliat/tabs/tab2.ts
import { t } from '../../../services/i18n/i18n';
import { LABELS_LISTS } from '../../../services/i18n/fitxaRepresaliat/labels-tab6';
import type { Fitxa } from '../../../types/types';
import { taulaArxius } from '../../modificaFitxaRepresaliat/taulaArxius';
import { taulaBibliografia } from '../../modificaFitxaRepresaliat/taulaBibliografia';

export function renderTab6(fitxa: Fitxa, lang: string): void {
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
      const h4 = document.createElement('h4') as HTMLHeadingElement;
      h4.textContent = t(LABELS_LISTS, 'bibliographyList', lang);
      h4.classList.add('titolSeccio');
      quadreFonts1.prepend(h4);

      if (fitxa?.id) {
        taulaBibliografia(fitxa.id);
      }
    }

    if (quadreFonts2) {
      quadreFonts2.style.display = 'block';
      const h4 = document.createElement('h4') as HTMLHeadingElement;
      h4.textContent = t(LABELS_LISTS, 'archivesList', lang);
      h4.classList.add('titolSeccio');
      quadreFonts2.prepend(h4);

      if (fitxa?.id) {
        taulaArxius(fitxa.id);
      }
    }
  }
}
