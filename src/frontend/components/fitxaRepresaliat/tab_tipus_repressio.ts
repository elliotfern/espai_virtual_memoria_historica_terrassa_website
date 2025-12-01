import { categoriesRepressio } from '../taulaDades/categoriesRepressio';
import { FitxaJudicial } from '../../types/types';
import { tab1Afusellat } from './tabs-repressio/tab1-afusellat';
import { AfusellatData } from '../../types/AfusellatData';
import { tab2Deportat } from './tabs-repressio/tab2-deportat';
import { DeportatData } from '../../types/DeportatData';
import { tab3MortCombat } from './tabs-repressio/tab3-mortCombat';
import { MortEnCombatData } from '../../types/mortEnCombatData';
import { tab4MortCivil } from './tabs-repressio/tab4-mortCivil';
import { MortCivilData } from '../../types/MortCivilData';
import { tab6Detingut } from './tabs-repressio/tab6-detingut';
import { DetingutProcesat } from '../../types/DetingutProcesat';
import { tab7Depurat } from './tabs-repressio/tab7-depurat';
import { Depurat } from '../../types/Depurat';
import { tab10Exiliat } from './tabs-repressio/tab10-exiliat';
import { Exiliat } from '../../types/Exiliat';
import { tab11Pendent } from './tabs-repressio/tab11-pendents';
import { tab12PresoModel } from './tabs-repressio/tab12-presoModel';
import { PresoModel } from '../../types/PresoModel';
import { DetingutGUData } from '../../types/DetingutGuardiaUrbana';
import { tab13DetingutGU } from './tabs-repressio/tab13-detingutGuardiaUrbana';
import { tab14DetingutComiteSolidaritat } from './tabs-repressio/tab14-detingutComiteSolidaritat';
import { DetingutComiteData } from '../../types/DetingutComiteSolidaritat';
import { tab15ResponsabilitatsPolitiques } from './tabs-repressio/tab15-lleiResponsabilitatsPolitiques';
import { LRPData } from '../../types/LleiResponsabilitatsPolitiques';
import { tab16TribunalOrdenPublico } from './tabs-repressio/tab17-tribunalOrdenPublico';
import { ProcessatTOPData } from '../../types/TribunalOrdenPublico';
import { tab18ComiteRelacionsSolidaritat } from './tabs-repressio/tab18-comiteRelacionsSolidaritat';
import { tab19CampsTreball } from './tabs-repressio/tab19-campsTreball';
import { tab20BatalloPresos } from './tabs-repressio/tab20-batalloPresos';

// Mostrar la información dependiendo de la categoría
export const fitxaTipusRepressio = async (categoriaNumerica: string, fitxa2: FitxaJudicial | FitxaJudicial[], lang: string): Promise<void> => {
  const divInfo = document.getElementById('fitxa-categoria');
  if (!divInfo) return;

  // 1. Mostrar el div y el spinner
  divInfo.style.display = 'block';
  divInfo.innerHTML = `<div class="spinner">Carregant dada...</div>`; // Pon aquí tu HTML spinner

  // 2. Esperar por los datos (simulado o real)
  const colectiusArray = await categoriesRepressio(lang);

  // 3. Cuando estén los datos, construir el contenido
  const colectiusRepressio = colectiusArray.reduce((acc, categoria) => {
    acc[categoria.id] = categoria.name;
    return acc;
  }, {} as { [key: string]: string });

  const dades = Array.isArray(fitxa2) ? fitxa2 : [fitxa2];
  const catNum = Number(categoriaNumerica); // calcúlalo una vez

  // 4. Reemplaza el contenido del div con la info final (quitando spinner)
  let htmlContent = `
    <h3><span class="blau1 raleway">${colectiusRepressio[categoriaNumerica]}: </span></h3>
  `;

  for (const dada of dades) {
    if (catNum === 1) {
      htmlContent = tab1Afusellat(dada as AfusellatData, htmlContent, lang);
    } else if (catNum === 2) {
      htmlContent = tab2Deportat(dada as DeportatData, htmlContent, lang);
    } else if (catNum === 3 || catNum === 22) {
      htmlContent = tab3MortCombat(dada as MortEnCombatData, htmlContent, lang);
    } else if (catNum === 4 || catNum === 5) {
      htmlContent = tab4MortCivil(dada as MortCivilData, htmlContent, lang);
    } else if (catNum === 6) {
      htmlContent = tab6Detingut(dada as DetingutProcesat, htmlContent, lang);
    } else if (catNum === 7) {
      htmlContent = tab7Depurat(dada as Depurat, htmlContent, lang);
    } else if (catNum === 8) {
      htmlContent += `
          <h5>Dona:</h5>
        `;
    } else if (catNum === 10) {
      htmlContent = tab10Exiliat(dada as Exiliat, htmlContent, lang);
    } else if (catNum === 11) {
      htmlContent = tab11Pendent(htmlContent, lang);
    } else if (catNum === 12) {
      htmlContent = tab12PresoModel([dada as unknown as PresoModel], htmlContent, lang);
    } else if (catNum === 13 || catNum === 16) {
      htmlContent = tab13DetingutGU([dada as unknown as DetingutGUData], htmlContent, lang);
    } else if (catNum === 14) {
      htmlContent = tab14DetingutComiteSolidaritat([dada as unknown as DetingutComiteData], htmlContent, lang);
    } else if (catNum === 15) {
      htmlContent = tab15ResponsabilitatsPolitiques(dada as LRPData, htmlContent, lang);
    } else if (catNum === 17) {
      htmlContent = tab16TribunalOrdenPublico(dada as ProcessatTOPData, htmlContent, lang);
    } else if (catNum === 18) {
      htmlContent = tab18ComiteRelacionsSolidaritat(htmlContent, lang);
    } else if (catNum === 19) {
      htmlContent = tab19CampsTreball(htmlContent, lang);
    } else if (catNum === 20) {
      htmlContent = tab20BatalloPresos(htmlContent, lang);
    } else {
      htmlContent += `
     <div class="negreta raleway">
     Sense dades
     `;
    }
  }

  // Finalmente actualiza el div
  divInfo.innerHTML = htmlContent;
};
