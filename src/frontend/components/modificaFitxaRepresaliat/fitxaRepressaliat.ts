import { Fitxa } from '../../types/types';
import { tab1 } from './tab1';
import { tab2 } from './tab2';
import { tab3 } from './tab3';
import { tab4 } from './tab4';
import { tab5 } from './tab5';
import { tab6 } from './tab6';
import { tab7 } from './tab7';
import { tab8 } from './tab8';

export async function fitxaRepressaliat(idRepressaliat: number): Promise<void> {
  const devDirectory = `https://${window.location.hostname}`;
  const urlAjax = `${devDirectory}/api/dades_personals/get/?type=fitxa&id=${idRepressaliat}`;

  try {
    const response = await fetch(urlAjax, {
      method: 'GET',
      headers: {
        'Content-Type': 'application/json',
      },
    });

    if (!response.ok) {
      throw new Error('Error en la solicitud');
    }

    const fitxaData: Fitxa[] = await response.json();
    const fitxa = fitxaData[0];

    // Mostrar nom i cognoms
    const fitxaNomCognoms = document.getElementById('fitxaNomCognoms');
    if (fitxaNomCognoms) {
      fitxaNomCognoms.innerHTML = `Modificació de la fitxa: ${fitxa.nom} ${fitxa.cognom1} ${fitxa.cognom2}`;
    }

    // tab1 - Categories repressió
    tab1(fitxa, idRepressaliat);

    // tab2 - Dades personals
    tab2(fitxa);

    // tab3 - Dades familiars
    tab3(fitxa);

    // tab4 - Dades acadèmiques i laborals
    tab4(fitxa);

    // tab5 - Dades politiques i sindicals
    tab5(fitxa);

    // tab6 - Biografia
    tab6(fitxa);

    // tab7 - Fonts documentals
    tab7(fitxa);

    // tab8 - Altres dades
    tab8(fitxa);
  } catch (error) {
    console.error('Error al obtener los datos:', error);
  }
}
