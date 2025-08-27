// src/pages/fitxaRepresaliat/mostrarInformacion.ts
import { DOMAIN_IMG } from '../../config/constants';
import { cache } from './cache';
import { renderTab1 } from './tabs/tab1';
import { renderTab2 } from './tabs/tab2';
import { renderTab3 } from './tabs/tab3';
import { renderTab4 } from './tabs/tab4';
import { renderTab5 } from './tabs/tab5';
import { renderTab6 } from './tabs/tab6';
import { renderTab7 } from './tabs/tab7';
import { renderTab9 } from './tabs/tab9';
// aquí iremos importando más tabs: tab2, tab3, etc.

export function mostrarInformacion(tabId: string, id: number, label: string): void {
  const fitxa = cache.getFitxa();
  const fitxaFam = cache.getFitxaFam();

  if (!fitxa) {
    console.error('mostrarInformacion: no fitxa in cache');
    return;
  }

  // imatge represaliat
  // Seleccionamos la imagen con el ID 'imatgeRepresaliat'
  const imagen = document.getElementById('imatgeRepresaliat') as HTMLImageElement;

  const divAdditionalInfo = document.getElementById('info');
  if (!divAdditionalInfo) return;

  // Comprobamos si la variable fitxa.img tiene un valor válido
  if (fitxa.img && fitxa.img !== '' && fitxa.img !== null && imagen) {
    imagen.src = DOMAIN_IMG + `/assets_represaliats/img/${fitxa.img}.jpg`; // Si es válida, usamos la imagen de la variable
  } else {
    imagen.src = DOMAIN_IMG + `/assets_represaliats/img/foto_defecte.jpg`; // Si no, mostramos la imagen por defecto
  }

  // Aquí puedes mantener el contenido de divAdditionalInfo si es necesario

  const nom = fitxa.nom !== null ? fitxa.nom : '';
  const cognom1 = fitxa.cognom1 !== null ? fitxa.cognom1 : '';
  const cognom2 = fitxa.cognom2 !== null ? fitxa.cognom2 : '';
  const nombreCompleto = `${nom} ${cognom1} ${cognom2 ?? ''}`;

  divAdditionalInfo.innerHTML = `<h4 class="titolRepresaliat"> ${nombreCompleto}</h4>`; // No se limpia el contenido

  switch (tabId) {
    case 'tab1':
      renderTab1(fitxa, label);
      break;
    case 'tab2':
      renderTab2(fitxa, fitxaFam, label);
      break;
    case 'tab3':
      renderTab3(fitxa, label);
      break;
    case 'tab4':
      renderTab4(fitxa, label);
      break;
    case 'tab5':
      renderTab5(fitxa, label);
      break;
    case 'tab6':
      renderTab6(fitxa);
      break;
    case 'tab9':
      renderTab9(fitxa, label);
      break;
    case 'tab7':
      renderTab7(fitxa, label);
      break;
    default:
      document.getElementById('fitxa')!.innerHTML = `<p>El contingut de la pestanya ${label} encara no està disponible.</p>`;
      break;
  }
}
