import { categoriesRepressio } from '../taulaDades/categoriesRepressio';
import { FitxaJudicial } from '../../types/types';
import { formatDatesForm } from '../../services/formatDates/dates';

// Mostrar la información dependiendo de la categoría
export const fitxaTipusRepressio = async (categoriaNumerica: string, fitxa2: FitxaJudicial): Promise<void> => {
  const colectiusArray = await categoriesRepressio('ca');

  const dades = fitxa2.data;
  // Convertimos el array en un objeto tipo diccionario
  const colectiusRepressio = colectiusArray.reduce((acc, categoria) => {
    acc[categoria.id] = categoria.name;
    return acc;
  }, {} as { [key: string]: string });

  const divInfo = document.getElementById('fitxa-categoria');
  if (!divInfo) return;

  divInfo.innerHTML += `
      <h3><span class="blau1 raleway">${colectiusRepressio[categoriaNumerica]}: </span></h3>
    `;

  if (parseInt(categoriaNumerica) === 1) {
    divInfo.innerHTML += `
    <div class="negreta raleway">
        <p><span class='marro2'>Data d'execució:</span> <span class='blau1'>${formatDatesForm(dades.data_execucio)}</span></p>
        <p><span class='marro2'>Lloc d'execució:</span> <span class='blau1'>${dades.lloc_execucio}</span> ${dades.ciutat_execucio && dades.ciutat_execucio.trim() !== '' ? `<span class="normal blau1">(${dades.ciutat_execucio})</span>` : ''}</p>
        <p><span class='marro2'>Lloc d'enterrament:</span> <span class='blau1'>${dades.lloc_enterrament} </span> ${dades.ciutat_enterrament && dades.ciutat_enterrament.trim() !== '' ? `<span class="normal blau1">(${dades.ciutat_enterrament})</span>` : ''}</p>
        ${dades.observacions && dades.observacions.trim() !== '' ? `  <p><span class='marro2'>Observacions:</span> <span class='blau1'> ${dades.observacions}.</span></p>` : ''}
    </div>`;
  } else if (parseInt(categoriaNumerica) === 2) {
    divInfo.innerHTML += `
          <h5>En elaboració:</h5>
        `;
  } else if (parseInt(categoriaNumerica) === 3) {
    divInfo.innerHTML += `
          <h5>En elaboració :</h5>

        `;
  } else if (parseInt(categoriaNumerica) === 4) {
    divInfo.innerHTML += `
          <h5>En elaboració:</h5>
        `;
  } else if (parseInt(categoriaNumerica) === 5) {
    divInfo.innerHTML += `
          <h5>En elaboració:</h5>
        `;
  } else if (parseInt(categoriaNumerica) === 6) {
    const tipusProcediment = dades.tipus_procediment && dades.tipus_procediment.trim() !== '' ? dades.tipus_procediment : 'Desconegut';
    const tipusJudici = dades.tipus_judici && dades.tipus_judici.trim() !== '' ? dades.tipus_judici : 'Desconegut';
    const numCausa = dades.num_causa && dades.num_causa.trim() !== '' ? dades.num_causa : 'Desconegut';
    const anyDetingut = dades.anyDetingut && dades.anyDetingut.trim() !== '' ? dades.anyDetingut + ' anys' : 'Desconegut';
    const anyInici = dades.any_inicial && dades.any_inicial.trim() !== '' ? dades.any_inicial : 'Desconegut';
    const anyFinal = dades.any_final && dades.any_final.trim() !== '' ? dades.any_final : 'Desconegut';
    const dataInici = dades.data_inici_proces && dades.data_inici_proces.trim() !== '' ? formatDatesForm(dades.data_inici_proces) : 'Desconegut';
    const dataSentencia = dades.sentencia_data && dades.sentencia_data.trim() !== '' ? formatDatesForm(dades.sentencia_data) : 'Desconegut';

    const sentencia = dades.sentencia && dades.sentencia.trim() !== '' ? dades.sentencia : 'Desconeguda';
    const pena = dades.pena && dades.pena.trim() !== '' ? dades.pena : 'Desconeguda';
    const commutacio = dades.commutacio && dades.commutacio.trim() !== '' ? dades.commutacio : '-';

    const jutgeInstructor = dades.jutge_instructor && dades.jutge_instructor.trim() !== '' ? dades.jutge_instructor : 'Desconegut';
    const secretariInstructor = dades.secretari_instructor && dades.secretari_instructor.trim() !== '' ? dades.secretari_instructor : 'Desconegut';
    const jutjat = dades.jutjat && dades.jutjat.trim() !== '' ? dades.jutjat : 'Desconegut';
    const consellGuerraData = dades.consell_guerra_data && dades.consell_guerra_data.trim() !== '' ? formatDatesForm(dades.consell_guerra_data) : 'Desconegut';
    const llocConsellGuerra = dades.lloc_consell_guerra && dades.lloc_consell_guerra.trim() !== '' ? dades.lloc_consell_guerra : 'Desconegut';
    const presidentTribunal = dades.president_tribunal && dades.president_tribunal.trim() !== '' ? dades.president_tribunal : 'Desconegut';
    const defensor = dades.defensor && dades.defensor.trim() !== '' ? dades.defensor : 'Desconegut';
    const fiscal = dades.fiscal && dades.fiscal.trim() !== '' ? dades.fiscal : 'Desconegut';
    const ponent = dades.ponent && dades.ponent.trim() !== '' ? dades.ponent : 'Desconegut';
    const tribunalVocals = dades.tribunal_vocals && dades.tribunal_vocals.trim() !== '' ? dades.tribunal_vocals : 'Desconegut';
    const acusacio = dades.acusacio && dades.acusacio.trim() !== '' ? dades.acusacio : 'Desconeguda';
    const acusacio2 = dades.acusacio_2 && dades.acusacio_2.trim() !== '' ? dades.acusacio_2 : 'Desconeguda';
    const testimoniAcusacio = dades.testimoni_acusacio && dades.testimoni_acusacio.trim() !== '' ? dades.testimoni_acusacio : 'Desconegut';
    const observacions = dades.observacions && dades.observacions.trim() !== '' ? dades.observacions : 'Sense dades';

    divInfo.innerHTML += `
     <div class="negreta raleway">

      <div style="margin-top:25px">
        <h5><span class="negreta blau1">1) Informació bàsica del procés judicial:</span></h5>
          <p><span class='marro2'>Tipus de procediment: </span> <span class='blau1'>${tipusProcediment}</span></p>
          <p><span class='marro2'>Tipus de judici:</span> <span class='blau1'>${tipusJudici}</span></p>
          <p><span class='marro2'>Número de causa:</span> <span class='blau1'>${numCausa}</span></p>
          <p><span class='marro2'>Anys en ser detingut o investigat:</span> <span class='blau1'>${anyDetingut}</span></p>
          <p><span class='marro2'>Any inici procés: </span> <span class='blau1'>${anyInici}</span></p>
          <p><span class='marro2'>Any final del procés: </span> <span class='blau1'>${anyFinal}</span></p>
          <p><span class='marro2'>Data inici del procés judicial: </span> <span class='blau1'>${dataInici}</span></p>
          <p><span class='marro2'>Data sentència: </span> <span class='blau1'>${dataSentencia}</span></p>
        </div>

      <div style="margin-top:45px">
          <h5><span class="negreta blau1">2) Resolució del procés judicial:</span></h5>
            <p><span class='marro2'>Sentència: </span> <span class='blau1'>${sentencia}</span></p>
            <p><span class='marro2'>Pena: </span> <span class='blau1'>${pena}</span></p>
            <p><span class='marro2'>Commutació o indult: </span> <span class='blau1'>${commutacio}</span></p>
      </div>

      <div style="margin-top:45px">
         <h5><span class="negreta blau1">3) Informació detallada del procés judicial:</span></h5>
            <p><span class='marro2'>Jutjat: </span> <span class='blau1'>${jutjat}</span></p>
            <p><span class='marro2'>Jutge instructor: </span> <span class='blau1'>${jutgeInstructor}</span></p>
            <p><span class='marro2'>Secretari instructor:</span> <span class='blau1'>${secretariInstructor}</span></p>
            <p><span class='marro2'>Data de la vista per sentència del procediment judicial (o consell de guerra):</span> <span class='blau1'>${consellGuerraData}</span></p>
            <p><span class='marro2'>Ciutat del procediment judicial/consell de guerra:</span> <span class='blau1'>${llocConsellGuerra}</span></p>
            <p><span class='marro2'>President del tribunal:</span> <span class='blau1'>${presidentTribunal}</span></p>
            <p><span class='marro2'>Advocat defensor:</span> <span class='blau1'>${defensor}</span></p>
            <p><span class='marro2'>Fiscal:</span> <span class='blau1'>${fiscal}</span></p>
            <p><span class='marro2'>Ponent:</span> <span class='blau1'>${ponent}</span></p>
            <p><span class='marro2'>Vocals tribunal:</span> <span class='blau1'>${tribunalVocals}</span></p>
            <p><span class='marro2'>Acusació:</span> <span class='blau1'>${acusacio}</span></p>
            <p><span class='marro2'>Acusació 2:</span> <span class='blau1'>${acusacio2}</span></p>
            <p><span class='marro2'>Testimoni acusació:</span> <span class='blau1'>${testimoniAcusacio}</span></p>
        </div>

        <div style="margin-top:45px">
            <h5><span class="negreta blau1">4) Altres dades:</span></h5>
            <p><span class='marro2'>Observacions:</span> <span class='blau1'>${observacions}</span></p>
        </div>

        </div>`;
  } else if (parseInt(categoriaNumerica) === 7) {
    divInfo.innerHTML += `
          <h5>En elaboració:</h5>
        `;
  } else if (parseInt(categoriaNumerica) === 8) {
    divInfo.innerHTML += `
          <h5>En elaboració:</h5>
        `;
  } else if (parseInt(categoriaNumerica) === 10) {
    divInfo.innerHTML += `
          <h5>En elaboració</h5>
        `;
  } else {
    console.error('Categoria no válida:', categoriaNumerica);
  }
};
