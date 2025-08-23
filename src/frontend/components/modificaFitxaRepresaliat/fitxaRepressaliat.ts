import { fetchFitxa } from '../../services/fetchData/fetchFitxa';
import { enviarDadesFormFitxaRepressaliat } from '../../services/fetchData/updateFormFitxaRepressaliat';
import { Fitxa } from '../../types/types';
import { tab1 } from './tab1';
import { tab10 } from './tab10';
import { tab2 } from './tab2';
import { tab3 } from './tab3';
import { tab4 } from './tab4';
import { tab5 } from './tab5';
import { tab6 } from './tab6';
import { tab7 } from './tab7';
import { tab8 } from './tab8';
import { tab9 } from './tab9';

export async function fitxaRepressaliat(idRepressaliat?: number): Promise<void> {
  if (idRepressaliat !== undefined) {
    const fitxa = (await fetchFitxa(idRepressaliat)) as Fitxa;

    if (!fitxa) {
      console.error('Fitxa no trobada per id:', idRepressaliat);
      return;
    }

    // Mostrar nom i cognoms
    const fitxaNomCognoms = document.getElementById('fitxaNomCognoms');
    if (fitxaNomCognoms) {
      fitxaNomCognoms.innerHTML = `Modificació de la fitxa: <a href="https://memoriaterrassa.cat/fitxa/${fitxa.slug}" target="_blank"> ${fitxa.nom} ${fitxa.cognom1} ${fitxa.cognom2}</a>`;
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

    // tab9 - Registre canvis
    tab9(fitxa);

    // tab10 - Imatge fitxa represaliat
    tab10('imatgePerfil', fitxa);

    // Escolta event
    const form = document.getElementById('formFitxaRepressaliat') as HTMLFormElement | null;

    if (form) {
      // 2) log de click en tus botones submit
      form.addEventListener('submit', (event) => {
        enviarDadesFormFitxaRepressaliat(event, 'PUT');
      });
    }
  } else {
    // tab1 - Categories repressió
    tab1();

    // tab2 - Dades personals
    tab2();

    // tab3 - Dades familiars
    tab3();

    // tab4 - Dades acadèmiques i laborals
    tab4();

    // tab5 - Dades politiques i sindicals
    tab5();

    // tab6 - Biografia
    tab6();

    // tab7 - Fonts documentals
    tab7();

    // tab8 - Altres dades
    tab8();

    // tab9 - Registre canvis
    tab9();

    // tab10 - Imatge fitxa represaliat
    tab10('imatgePerfil');

    // Escolta event
    const form = document.getElementById('formFitxaRepressaliat') as HTMLFormElement | null;
    if (form) {
      form.addEventListener('submit', (event) => {
        enviarDadesFormFitxaRepressaliat(event, 'POST');
      });
    }
  }
}
