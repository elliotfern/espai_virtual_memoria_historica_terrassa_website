import { mostrarPestanyaPerDefecte } from './pestanyaDefecte';
import { pestanyesInformacio } from './pestanyesInformacio';
import { fitxaRepressaliat } from './fitxaRepressaliat';

export function modificaFitxa(id: number) {
  mostrarPestanyaPerDefecte();
  fitxaRepressaliat(id);

  // Aquí agrega los event listeners para los botones para que llamen a pestanyesInformacio al hacer click
  const tabLinks = document.getElementsByClassName('tablinks');
  for (let i = 0; i < tabLinks.length; i++) {
    const tabLink = tabLinks[i] as HTMLElement;

    // Suponiendo que cada botón tiene un atributo data-tab con el id de la pestaña correspondiente
    const tabName = tabLink.getAttribute('data-tab');
    if (tabName) {
      tabLink.onclick = (evt) => {
        pestanyesInformacio(evt as MouseEvent, tabName);
      };
    }
  }
}
