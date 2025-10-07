import { scrollToTabContentOnce } from '../../utils/observer';
import { mostrarInformacion } from './mostrarInformacion';

export function initTabs(translations: Record<string, string>, idPersona: number, lang: string): void {
  const tabs = [
    { id: 'tab1', label: translations.tab1 },
    { id: 'tab2', label: translations.tab2 },
    { id: 'tab3', label: translations.tab3 },
    { id: 'tab4', label: translations.tab4 },
    { id: 'tab5', label: translations.tab5 },
    { id: 'tab6', label: translations.tab6 },
    { id: 'tab8', label: translations.tab8 },
    { id: 'tab9', label: translations.tab9 },
    { id: 'tab7', label: translations.tab7 },
  ];

  const tabsContainer = document.getElementById('botons1');
  if (!tabsContainer) {
    console.error('initTabs: no se encontró el contenedor de tabs');
    return;
  }

  tabsContainer.innerHTML = '';

  tabs.forEach((tab, index) => {
    const btn = document.createElement('button');

    // Clases base para estilo y layout
    btn.classList.add('tablinks', 'col-12', 'col-md');

    // Alternar colores: azul (colorBtn1) para índices pares, gris (colorBtn2) para impares
    if (index % 2 === 0) {
      btn.classList.add('colorBtn1');
    } else {
      btn.classList.add('colorBtn2');
    }

    btn.textContent = tab.label;
    btn.dataset.tab = tab.id;

    // Marcar activo el tab1 inicialmente
    if (tab.id === 'tab1') {
      btn.classList.add('active');
    }

    btn.addEventListener('click', () => {
      // Quitar active de todos los botones
      const allButtons = tabsContainer.getElementsByClassName('tablinks');
      Array.from(allButtons).forEach((b) => b.classList.remove('active'));

      // Poner active al botón pulsado
      btn.classList.add('active');

      // Mostrar la información correspondiente
      mostrarInformacion(tab.id, idPersona, tab.label, lang);

      // desplazar en móvil hacia el contenido
      scrollToTabContentOnce();
    });

    tabsContainer.appendChild(btn);
  });

  // Mostrar contenido de la primera pestaña por defecto
  mostrarInformacion('tab1', idPersona, translations.tab1, lang);
}
