import { categorias } from '../../config';
import { FitxaJudicial } from '../../types/types';

// Mostrar la información dependiendo de la categoría
export const fitxaTipusRepressio = (categoriaNumerica: string, fitxa2: FitxaJudicial): void => {
  const divInfo = document.getElementById('fitxa-categoria');
  if (!divInfo) return;

  divInfo.innerHTML += `
      <h3>${categorias[categoriaNumerica]}</h3>
    `;

  if (parseInt(categoriaNumerica) === 1) {
    divInfo.innerHTML += `
          <p><strong>Procés judicial:</strong> ${fitxa2.procediment_cat}</p>
          <p><strong>Número de causa:</strong> ${fitxa2.num_causa}</p>
          <p><strong>Data inici del procés judicial:</strong> ${fitxa2.data_inici_proces}</p>
          <p><strong>Jutge instructor:</strong> ${fitxa2.jutge_instructor}</p>
          <p><strong>Secretari instructor:</strong> ${fitxa2.secretari_instructor}</p>
          <p><strong>Jutjat:</strong> ${fitxa2.jutjat}</p>
          <p><strong>Any inici del procés:</strong> ${fitxa2.any_inicial}</p>
          <p><strong>Data del consell de guerra:</strong> ${fitxa2.consell_guerra_data}</p>
          <p><strong>Ciutat del consell de guerra:</strong> ${fitxa2.ciutat_consellGuerra}</p>
          <p><strong>President del tribunal:</strong> ${fitxa2.president_tribunal}</p>
          <p><strong>Advocat defensor:</strong> ${fitxa2.defensor}</p>
          <p><strong>Fiscal:</strong> ${fitxa2.fiscal}</p>
          <p><strong>Ponent:</strong> ${fitxa2.ponent}</p>
          <p><strong>Vocals tribunal:</strong> ${fitxa2.tribunal_vocals}</p>
          <p><strong>Acusació:</strong> ${fitxa2.acusacio}</p>
          <p><strong>Acusació 2:</strong> ${fitxa2.acusacio_2}</p>
          <p><strong>Testimoni acusació:</strong> ${fitxa2.testimoni_acusacio}</p>
          <p><strong>Data de la sentència:</strong> ${fitxa2.sentencia_data}</p>
          <p><strong>Sentència:</strong> ${fitxa2.sentencia}</p>
          <p><strong>Data sentència:</strong> ${fitxa2.data_sentencia}</p>
          <p><strong>Data de defunció (execució):</strong> ${fitxa2.data_execucio}</p>
          <p><strong>Lloc execució:</strong> ${fitxa2.espai}</p>
        `;
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
    divInfo.innerHTML += `
          <h5>En elaboració:</h5>
        `;
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
          <h5>En elaboració exiliats:</h5>
                    <p><strong>EXILIATS</p>
        `;
  } else {
    console.error('Categoria no válida:', categoriaNumerica);
  }
};
