import { categoriesRepressio } from '../taulaDades/categoriesRepressio';
import { FitxaJudicial } from '../../types/types';
import { formatDatesForm } from '../../services/formatDates/dates';
import { valorTextDesconegut } from '../../services/formatDates/valorTextDesconegut';
import { joinValors } from '../../services/formatDates/joinValors';
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
    } else if (catNum === 3) {
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
    } else if (parseInt(categoriaNumerica) === 13 || parseInt(categoriaNumerica) === 16) {
      // detinguts guardia urbana

      htmlContent = '';

      for (const [index, dada] of dades.entries()) {
        const dataEntradaFormat = formatDatesForm(dada.data_empresonament);
        const data_empresonament = valorTextDesconegut(dataEntradaFormat, 4);
        const dataSortidaFormat = formatDatesForm(dada.data_sortida);
        const data_sortida = valorTextDesconegut(dataSortidaFormat, 4);
        const motiu_empresonament = valorTextDesconegut(dada.motiu_empresonament, 1);
        const qui_ordena_detencio = valorTextDesconegut(dada.qui_ordena_detencio, 1);
        const nom_institucio = valorTextDesconegut(dada.nom_institucio, 1);
        const grup = valorTextDesconegut(dada.grup, 1);
        const topText = dada.top === 1 ? 'Sí' : dada.top === 2 ? 'No' : dada.top === 3 ? 'Sense dades' : 'Desconegut';
        const observacions = valorTextDesconegut(dada.observacions, 1);

        htmlContent += `
       <div class="negreta raleway" style="margin-bottom:10px; padding-bottom:30px">
          <h4 class="blau1">Detingut Guàrdia Urbana / Empresonat al Dipòsit municipal Sant Llàtzer, registre núm. ${index + 1}</h4>
            <div class="negreta raleway">
              <div style="margin-top:25px">
                <p><span class='marro2'>Data de detenció: </span> <span class='blau1'>${data_empresonament}</span></p>
                <p><span class='marro2'>Data de sortida:</span> <span class='blau1'>${data_sortida}</span></p>
                <p><span class='marro2'>Motiu de la detenció:</span> <span class='blau1'>${motiu_empresonament}</span></p>
                <p><span class='marro2'>Responsable d'ordenar la detenció:</span> <span class='blau1'>${qui_ordena_detencio} - ${nom_institucio}</span></p>
                <p><span class='marro2'>TIpus d'institució que ordena la detenció:</span> <span class='blau1'>${grup}</span></p>
                <p><span class='marro2'>Detenció ordenada pel Tribunal de Orden Público?:</span> <span class='blau1'>${topText}</span></p>
                <p><span class='marro2'>Observacions: </span> <span class='blau1'>${observacions}</span></p>
              </div>
            </div>     
        </div>
      `;
      }
    } else if (parseInt(categoriaNumerica) === 14) {
      // Detingut Comitè Solidaritat (1971-1977)
      htmlContent = '';

      for (const [index, dada] of dades.entries()) {
        const anyDetencio = valorTextDesconegut(dada.any_detencio, 4);
        const motiu_empresonament = valorTextDesconegut(dada.motiu, 1);
        const advocat = valorTextDesconegut(dada.advocat, 1);
        const observacions = valorTextDesconegut(dada.observacions, 1);

        htmlContent += `
       <div class="negreta raleway" style="margin-bottom:10px; padding-bottom:30px">
          <h4 class="blau1">Detingut Comitè de Solidaritat (1971-1977), registre núm. ${index + 1}</h4>
            <div class="negreta raleway">
              <div style="margin-top:25px">
                <p><span class='marro2'>Any de la detenció: </span> <span class='blau1'>${anyDetencio}</span></p>
                <p><span class='marro2'>Motiu de la detenció:</span> <span class='blau1'>${motiu_empresonament}</span></p>
                <p><span class='marro2'>Advocat:</span> <span class='blau1'>${advocat}</span></p>
                <p><span class='marro2'>Observacions: </span> <span class='blau1'>${observacions}</span></p>
              </div>
            </div>     
        </div>
      `;
      }
    } else if (parseInt(categoriaNumerica) === 15) {
      // Llei Responsabilitats Polítiques
      const nomPreso = valorTextDesconegut(dada.lloc_empresonament, 6);
      const ciutatPreso = valorTextDesconegut(dada.preso_ciutat, 3);
      const empresonament = joinValors([nomPreso, ciutatPreso], ' - ', false);
      const paisExili = valorTextDesconegut(dada.lloc_exili, 7);
      const condemna = valorTextDesconegut(dada.condemna, 1);
      const observacions = valorTextDesconegut(dada.observacions, 1);

      htmlContent += `
     <div class="negreta raleway">
      <div style="margin-top:25px">
        <p><span class='marro2'>Lloc d'empresonament: </span> <span class='blau1'>${empresonament}</span></p>
        <p><span class='marro2'>País exili:</span> <span class='blau1'>${paisExili}</span></p>
        <p><span class='marro2'>Condemna Expedient de Responsabilitats Politiques:</span> <span class='blau1'>${condemna}</span></p>
        <p><span class='marro2'>Observacions: </span> <span class='blau1'>${observacions}</span></p>
      </div>
      </div>     
        `;
    } else if (parseInt(categoriaNumerica) === 17) {
      // Processat Tribunal Orden Público

      const num_causa = valorTextDesconegut(dada.num_causa, 6);
      const dataSentencia = formatDatesForm(dada.data_sentencia);
      const data_sentencia = valorTextDesconegut(dataSentencia, 1);
      const ciutatPreso = valorTextDesconegut(dada.preso_ciutat, 1);
      const nomPreso = valorTextDesconegut(dada.preso, 1);
      const empresonament = joinValors([nomPreso, ciutatPreso], ' - ', false);
      const sentencia = valorTextDesconegut(dada.sentencia, 1);

      htmlContent += `
     <div class="negreta raleway">
      <div style="margin-top:25px">
        <p><span class='marro2'>Número de la causa: </span> <span class='blau1'>${num_causa}</span></p>
        <p><span class='marro2'>Data sentència:</span> <span class='blau1'>${data_sentencia}</span></p>
        <p><span class='marro2'>Sentència:</span> <span class='blau1'>${sentencia}</span></p>
        <p><span class='marro2'>Lloc d'empresonament: </span> <span class='blau1'>${empresonament}</span></p>
      </div>
      </div>     
        `;
    } else if (parseInt(categoriaNumerica) === 18) {
      // Detingut Comitè Relacions de Solidaritat (1939-1941).
      htmlContent += `
     <div class="negreta raleway">
      <div style="margin-top:25px">
        <p>Represaliat per haver donat suport als exiliats a través del Comitè de Relacions de Solidaritat.</p>
      </div>
      </div>     
        `;
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
