import { formatDates } from '../../services/formatDates/dates';
import { Fitxa } from '../../types/types';
import { tab1 } from './tab1';
import { tab2 } from './tab2';
import { tab3 } from './tab3';
import { tab4 } from './tab4';

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

    // tab 4 - Dades acadèmiques i laborals
    tab4(fitxa);

    // Asignar valores a otros campos según sea necesario...

    const dataCreacioElement = document.getElementById('data_creacio');
    if (dataCreacioElement) {
      dataCreacioElement.innerText = formatDates(fitxa.data_creacio);
    }

    const dataActualitzacioElement = document.getElementById('data_actualitzacio');
    if (dataActualitzacioElement) {
      dataActualitzacioElement.innerText = formatDates(fitxa.data_actualitzacio);
    }

    const completatNoRadio = document.getElementById('completat_no') as HTMLInputElement;
    const completatSiRadio = document.getElementById('completat_si') as HTMLInputElement;

    if (fitxa.completat === 1 && completatNoRadio) {
      completatNoRadio.checked = true;
    } else if (fitxa.completat === 2 && completatSiRadio) {
      completatSiRadio.checked = true;
    }
  } catch (error) {
    console.error('Error al obtener los datos:', error);
  }
}
