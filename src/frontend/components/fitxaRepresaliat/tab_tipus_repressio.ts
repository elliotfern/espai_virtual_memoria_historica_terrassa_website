import { categoriesRepressio } from '../taulaDades/categoriesRepressio';
import { FitxaJudicial } from '../../types/types';
import { formatDatesForm } from '../../services/formatDates/dates';
import { valorTextDesconegut } from '../../services/formatDates/valorTextDesconegut';
import { joinValors } from '../../services/formatDates/joinValors';

// Mostrar la información dependiendo de la categoría
export const fitxaTipusRepressio = async (categoriaNumerica: string, fitxa2: FitxaJudicial | FitxaJudicial[]): Promise<void> => {
  const divInfo = document.getElementById('fitxa-categoria');
  if (!divInfo) return;

  // 1. Mostrar el div y el spinner
  divInfo.style.display = 'block';
  divInfo.innerHTML = `<div class="spinner">Carregant dada...</div>`; // Pon aquí tu HTML spinner

  // 2. Esperar por los datos (simulado o real)
  const colectiusArray = await categoriesRepressio('ca');

  // 3. Cuando estén los datos, construir el contenido
  const colectiusRepressio = colectiusArray.reduce((acc, categoria) => {
    acc[categoria.id] = categoria.name;
    return acc;
  }, {} as { [key: string]: string });

  const dades = Array.isArray(fitxa2) ? fitxa2 : [fitxa2];

  // 4. Reemplaza el contenido del div con la info final (quitando spinner)
  let htmlContent = `
    <h3><span class="blau1 raleway">${colectiusRepressio[categoriaNumerica]}: </span></h3>
  `;

  for (const dada of dades) {
    if (parseInt(categoriaNumerica) === 1) {
      htmlContent += `
    <div class="negreta raleway">
        <p><span class='marro2'>Data d'execució:</span> <span class='blau1'>${formatDatesForm(dada.data_execucio)}</span></p>
        <p><span class='marro2'>Lloc d'execució:</span> <span class='blau1'>${dada.lloc_execucio}</span> ${dada.ciutat_execucio && dada.ciutat_execucio.trim() !== '' ? `<span class="normal blau1">(${dada.ciutat_execucio})</span>` : ''}</p>
        <p><span class='marro2'>Lloc d'enterrament:</span> <span class='blau1'>${dada.lloc_enterrament} </span> ${dada.ciutat_enterrament && dada.ciutat_enterrament.trim() !== '' ? `<span class="normal blau1">(${dada.ciutat_enterrament})</span>` : ''}</p>
        ${dada.observacions && dada.observacions.trim() !== '' ? `  <p><span class='marro2'>Observacions:</span> <span class='blau1'> ${dada.observacions}.</span></p>` : ''}
    </div>`;
    } else if (parseInt(categoriaNumerica) === 2) {
      const situacioDeportat = dada.situacio && dada.situacio.trim() !== '' ? dada.situacio : 'Desconegut';
      const situacioId = dada.situacioId;
      let alliberamentMort = '';
      let municipiMort = '';

      if (situacioId === 1) {
        alliberamentMort = 'Data de defunció';
        municipiMort = 'Municipi de defunció';
      } else if (situacioId === 2) {
        alliberamentMort = "Data d'alliberament del camp";
        municipiMort = "Municipi d'alliberament";
      } else {
        alliberamentMort = "Data d'evasió";
        municipiMort = "Municipi d'evasió";
      }

      const dataAlliberament = dada.data_alliberament && dada.data_alliberament.trim() !== '' ? formatDatesForm(dada.data_alliberament) : 'Desconeguda';
      const municipiAlliberament = dada.ciutat_mort_alliberament && dada.ciutat_mort_alliberament.trim() !== '' ? dada.ciutat_mort_alliberament : 'Desconegut';

      const tipusPreso = dada.tipusPresoFranca && dada.tipusPresoFranca.trim() !== '' ? dada.tipusPresoFranca : 'Desconeguda';
      const nomPreso = dada.situacioFrancaNom && dada.situacioFrancaNom.trim() !== '' ? dada.situacioFrancaNom : 'Desconeguda';
      const dataSortidaPresoFranca = dada.situacioFranca_sortida && dada.situacioFranca_sortida.trim() !== '' ? formatDatesForm(dada.situacioFranca_sortida) : 'Desconeguda';
      const municipiPreso = dada.ciutat_situacioFranca_preso && dada.ciutat_situacioFranca_preso.trim() !== '' ? dada.ciutat_situacioFranca_preso : 'Desconegut';
      const numMatriculaPreso = dada.situacioFranca_num_matricula && dada.situacioFranca_num_matricula.trim() !== '' ? dada.situacioFranca_num_matricula : 'Desconegut';
      const situacioFrancaObservacions = dada.situacioFrancaObservacions && dada.situacioFrancaObservacions.trim() !== '' ? dada.situacioFrancaObservacions : 'Sense dades';

      const estat_mort_allibertament = dada.estat_mort_allibertament;

      const tipusPreso1 = dada.tipusPreso1 && dada.tipusPreso1.trim() !== '' ? dada.tipusPreso1 : 'Sense dades';
      const nomPreso1 = dada.nomPreso1 && dada.nomPreso1.trim() !== '' ? dada.nomPreso1 : 'Sense dades';
      const ciutatPreso1 = dada.ciutatPreso1 && dada.ciutatPreso1.trim() !== '' ? dada.ciutatPreso1 : 'Sense dades';
      const presoClasificacioData1 = dada.presoClasificacioData1 && dada.presoClasificacioData1.trim() !== '' ? formatDatesForm(dada.presoClasificacioData1) : 'Sense dades';

      const presoClasificacioDataEntrada1 = dada.presoClasificacioDataEntrada1 && dada.presoClasificacioDataEntrada1.trim() !== '' ? formatDatesForm(dada.presoClasificacioDataEntrada1) : 'Sense dades';
      const presoClasificacioMatr1 = dada.presoClasificacioMatr1 && dada.presoClasificacioMatr1.trim() !== '' ? dada.presoClasificacioMatr1 : 'Sense dades';

      const presoClasificacioDataEntrada2 = dada.presoClasificacioDataEntrada2 && dada.presoClasificacioDataEntrada2.trim() !== '' ? formatDatesForm(dada.presoClasificacioDataEntrada2) : 'Sense dades';
      const presoClasificacioMatr2 = dada.presoClasificacioMatr2 && dada.presoClasificacioMatr2.trim() !== '' ? dada.presoClasificacioMatr2 : 'Sense dades';

      const tipusPreso2 = dada.tipusPreso2 && dada.tipusPreso2.trim() !== '' ? dada.tipusPreso2 : 'Sense dades';
      const nomPreso2 = dada.nomPreso2 && dada.nomPreso2.trim() !== '' ? dada.nomPreso2 : 'Sense dades';
      const ciutatPreso2 = dada.ciutatPreso2 && dada.ciutatPreso2.trim() !== '' ? dada.ciutatPreso2 : 'Sense dades';
      const presoClasificacioData2 = dada.presoClasificacioData2 && dada.presoClasificacioData2.trim() !== '' ? formatDatesForm(dada.presoClasificacioData2) : 'Sense dades';

      const deportacio_observacions = dada.deportacio_observacions && dada.deportacio_observacions.trim() !== '' ? dada.deportacio_observacions : 'Sense dades';

      const tipusCamp1 = dada.tipusCamp1 && dada.tipusCamp1.trim() !== '' ? dada.tipusCamp1 : 'Sense dades';
      const ciutatCamp1 = dada.ciutatCamp1 && dada.ciutatCamp1.trim() !== '' ? dada.ciutatCamp1 : 'Sense dades';

      const nomCamp1 = dada.nomCamp1 && dada.nomCamp1.trim() !== '' ? dada.nomCamp1 : 'Desconegut';
      const deportacio_data_entrada = dada.deportacio_data_entrada && dada.deportacio_data_entrada.trim() !== '' ? formatDatesForm(dada.deportacio_data_entrada) : 'Desconeguda';
      const numeroMatriculaCamp = dada.deportacio_num_matricula && dada.deportacio_num_matricula.trim() !== '' ? dada.deportacio_num_matricula : 'Desconegut';

      const tipusCamp2 = dada.tipusCamp2 && dada.tipusCamp2.trim() !== '' ? dada.tipusCamp2 : 'Sense dades';
      const ciutatCamp2 = dada.ciutatCamp2 && dada.ciutatCamp2.trim() !== '' ? dada.ciutatCamp2 : 'Sense dades';
      const nomSubCamp = dada.nomCamp2 && dada.nomCamp2.trim() !== '' ? dada.nomCamp2 : 'Desconegut';
      const dataEntradaSubCamp = dada.deportacio_data_entrada_subcamp && dada.deportacio_data_entrada_subcamp.trim() !== '' ? formatDatesForm(dada.deportacio_data_entrada_subcamp) : 'Desconegut';
      const numeroMatriculaSubCamp = dada.deportacio_nom_matricula_subcamp && dada.deportacio_nom_matricula_subcamp.trim() !== '' ? dada.deportacio_nom_matricula_subcamp : 'Desconegut';

      const estat_preso1 = dada.estat_preso1 && dada.estat_preso1.trim() !== '' ? dada.estat_preso1 : '';
      const estat_preso2 = dada.estat_preso2 && dada.estat_preso2.trim() !== '' ? dada.estat_preso2 : '';

      htmlContent += `
    <div class="negreta raleway">
      <div style="margin-top:25px">
        <h5><span class="negreta blau1">1) Dades bàsiques sobre la deportació:</span></h5> 
          <p><span class='marro2'>Situació del deportat: </span> <span class='blau1'>${situacioDeportat}</span></p>
          <p><span class='marro2'>${alliberamentMort}:</span> <span class='blau1'>${dataAlliberament}</span></p>
          <p><span class='marro2'>${municipiMort}:</span> <span class='blau1'>${municipiAlliberament} (${estat_mort_allibertament})</span></p>
        </div>

        <div style="margin-top:25px">
        <h5><span class="negreta blau1">2) Dades sobre la situació a França, prèvia a la deportació:</span></h5>
          <p><span class='marro2'>Tipus de Presó/camp de detenció: </span> <span class='blau1'>${tipusPreso}</span></p>
          <p><span class='marro2'>Nom de la presó/camp: </span> <span class='blau1'>${nomPreso}</span></p>
          <p><span class='marro2'>Municipi de la presó/camp: </span> <span class='blau1'>${municipiPreso}</span></p>
          <p><span class='marro2'>Data de la sortida de la presó/camp: </span> <span class='blau1'>${dataSortidaPresoFranca}</span></p>
          <p><span class='marro2'>Número de matrícula presó:</span> <span class='blau1'>${numMatriculaPreso}</span></p>
          <p><span class='marro2'>Descripció situació a França: </span> <span class='blau1'> ${situacioFrancaObservacions}</span></p>
        </div>

      <div style="margin-top:25px">
        <h5><span class="negreta blau1">3) Camp de classificació/detenció previ a la deportació al camp de concentració:</span></h5>
            <br>
            <h6><span class="blau1 negreta">Primera presó/camp de classificació</span></h6>
                <p><span class='marro2'>Tipus de Presó/camp de detenció: </span> <span class='blau1'>${tipusPreso1}</span></p>
                <p><span class='marro2'>Nom de la Presó/camp de detenció: </span> <span class='blau1'>${nomPreso1}   </span></p>
                <p><span class='marro2'>Municipi de la Presó/camp de detenció: </span> <span class='blau1'>${ciutatPreso1}  - ${estat_preso1}</span></p>
                <p><span class='marro2'>Data d'entrada de la presó: </span> <span class='blau1'>${presoClasificacioDataEntrada1}   </span></p>
                <p><span class='marro2'>Data de la sortida de la presó: </span> <span class='blau1'>${presoClasificacioData1}   </span></p>
                 <p><span class='marro2'>Número de matrícula: </span> <span class='blau1'>${presoClasificacioMatr1} </span></p>
            <br>
            <h6><span class="blau1 negreta">Segona presó/camp de classificació</span></h6>
                <p><span class='marro2'>Tipus de Presó/camp de detenció: </span> <span class='blau1'>${tipusPreso2} </span></p>
                <p><span class='marro2'>Nom de la Presó/camp de detenció: </span> <span class='blau1'>${nomPreso2} </span></p>
                <p><span class='marro2'>Municipi de la Presó/camp de detenció: </span> <span class='blau1'>${ciutatPreso2} - ${estat_preso2}</span></p>
                 <p><span class='marro2'>Data d'entrada de la presó: </span> <span class='blau1'>${presoClasificacioDataEntrada2} </span></p>
                <p><span class='marro2'>Data de la sortida de la presó: </span> <span class='blau1'>${presoClasificacioData2} </span></p>
                <p><span class='marro2'>Número de matrícula: </span> <span class='blau1'>${presoClasificacioMatr2} </span></p>
        </div>

      <div style="margin-top:25px">
        <h5><span class="negreta blau1">4) Dades sobre la deportació al camp de concentració/extermini:</span></h5>
        <br>
         <h6><span class="blau1 negreta">Dades sobre el camp de concentració:</span></h6>
              <p><span class='marro2'>Nom del camp de deportació: </span> <span class='blau1'>${nomCamp1}</span></p>
              <p><span class='marro2'>Tipus de camp: </span> <span class='blau1'>${tipusCamp1}</span></p>
              <p><span class='marro2'>Municipi del camp: </span> <span class='blau1'>${ciutatCamp1}</span></p>
              <p><span class='marro2'>Data d'entrada al camp: </span> <span class='blau1'>${deportacio_data_entrada}</span></p>
              <p><span class='marro2'>Número de matrícula:</span> <span class='blau1'>${numeroMatriculaCamp}</span></p>

              <br>
          <h6><span class="blau1 negreta">Dades sobre el subcamp:</span></h6>
              <p><span class='marro2'>Nom del subcamp :</span> <span class='blau1'>${nomSubCamp}</span></p>
              <p><span class='marro2'>Tipus de subcamp: </span> <span class='blau1'>${tipusCamp2}</span></p>
              <p><span class='marro2'>Municipi del subcamp: </span> <span class='blau1'>${ciutatCamp2}</span></p>
              <p><span class='marro2'>Data d'entrada al subcamp:</span> <span class='blau1'>${dataEntradaSubCamp}</span></p>
              <p><span class='marro2'>Número de matrícula del subcamp:</span> <span class='blau1'>${numeroMatriculaSubCamp}</span></p>

              <br>
          <h6><span class="blau1 negreta">Altres informacions:</span></h6>
              <p><span class='marro2'>Observacions:</span> <span class='blau1'>${deportacio_observacions}</span></p>
        </div>
    </div>
        `;
    } else if (parseInt(categoriaNumerica) === 3) {
      const condicio = dada.condicio && dada.condicio.trim() !== '' ? dada.condicio : 'Desconegut';
      const bandol = dada.bandol && dada.bandol.trim() !== '' ? dada.bandol : 'Desconegut';
      const any_lleva = dada.any_lleva && dada.any_lleva.trim() !== '' ? dada.any_lleva : 'Desconegut';

      const unitat_inicial = dada.unitat_inicial && dada.unitat_inicial.trim() !== '' ? dada.unitat_inicial : 'Desconegut';
      const cos = dada.cos && dada.cos.trim() !== '' ? dada.cos : 'Desconegut';
      const unitat_final = dada.unitat_final && dada.unitat_final.trim() !== '' ? dada.unitat_final : 'Desconegut';
      const graduacio_final = dada.graduacio_final && dada.graduacio_final.trim() !== '' ? dada.graduacio_final : 'Desconegut';
      const periple_militar = dada.periple_militar && dada.periple_militar.trim() !== '' ? dada.periple_militar : 'Sense dades';

      const circumstancia_mort = dada.circumstancia_mort && dada.circumstancia_mort.trim() !== '' ? dada.circumstancia_mort : 'Desconeguda';
      const desaparegut_data = dada.desaparegut_data && dada.desaparegut_data.trim() !== '' ? formatDatesForm(dada.desaparegut_data) : '-';
      const desaparegut_lloc = dada.desaparegut_lloc && dada.desaparegut_lloc.trim() !== '' ? dada.desaparegut_lloc : '-';
      const desaparegut_data_aparicio = dada.desaparegut_data_aparicio && dada.desaparegut_data_aparicio.trim() !== '' ? formatDatesForm(dada.desaparegut_data_aparicio) : '-';
      const desaparegut_lloc_aparicio = dada.desaparegut_lloc_aparicio && dada.desaparegut_lloc_aparicio.trim() !== '' ? dada.desaparegut_lloc_aparicio : '-';

      const fragment =
        dada.reaparegut === 1
          ? `
          <div style="margin-top:25px">
            <h5><span class="negreta blau1">4) Dades conegudes sobre l'aparició posterior del desaparegut:</span></h5>
                <p><span class='marro2'>Data d'aparació del desaparegut:</span> <span class='blau1'>${desaparegut_data_aparicio}</span></p>
                <p><span class='marro2'>Lloc d'aparació del desaparegut:</span> <span class='blau1'>${desaparegut_lloc_aparicio}</span></p>
                <p><span class='marro2'>Observacions:</span> <span class='blau1'>${dada.aparegut_observacions}</span></p>
          </div>
      `
          : '';

      htmlContent += `
    <div class="negreta raleway">
      <div style="margin-top:25px">
        <h5><span class="negreta blau1">1) Dades bàsiques:</span></h5>
          <p><span class='marro2'>Condició: </span> <span class='blau1'>${condicio}</span></p>
          <p><span class='marro2'>Bàndol durant la guerra:</span> <span class='blau1'>${bandol}</span></p>
          <p><span class='marro2'>Any lleva:</span> <span class='blau1'>${any_lleva}</span></p>
        </div>

      <div style="margin-top:25px">
        <h5><span class="negreta blau1">2) Dades militars:</span></h5>
          <p><span class='marro2'>Unitat inicial:</span> <span class='blau1'>${unitat_inicial}</span></p>
          <p><span class='marro2'>Cos militar:</span> <span class='blau1'>${cos}</span></p>
          <p><span class='marro2'>Unitat final:</span> <span class='blau1'>${unitat_final}</span></p>
          <p><span class='marro2'>Graduació final:</span> <span class='blau1'>${graduacio_final}</span></p>
          <p><span class='marro2'>Periple militar i altres observacions:</span> <span class='blau1'>${periple_militar}</span></p>
        </div>

      <div style="margin-top:25px">
        <h5><span class="negreta blau1">3) Circumstàncies de la mort o desaparació:</span></h5>
          <p><span class='marro2'>Causa de defunció/desaparació:</span> <span class='blau1'>${circumstancia_mort}</span></p>
          <h6><span class="negreta blau1">Si el combatent és donat per desaparegut:</span></h6>
          <p><span class='marro2'>Data de la desaparació:</span> <span class='blau1'>${desaparegut_data}</span></p>
          <p><span class='marro2'>Lloc de desaparació:</span> <span class='blau1'>${desaparegut_lloc}</span></p>
        </div>

          ${fragment}

    </div>

        `;
    } else if (parseInt(categoriaNumerica) === 4 || parseInt(categoriaNumerica) === 5) {
      const cirscumstancies_mortId = dada.cirscumstancies_mortId;

      let data_bombardeig = '';
      let municipi_bombardeig = '';
      let lloc_bombardeig = '';
      let data_detencio = '';
      let lloc_detencio = '';
      let qui_detencio = '';
      let qui_ordena_afusellat = '';
      let qui_executa_afusellat = '';
      let titolMunicipiCadaver = '';
      let contingutHtmlBombargeig = '';
      let contingutHtmlAssassinat = '';
      let contingutHtmlAfusellat = '';

      if (cirscumstancies_mortId === 5) {
        // Causa de la mort: Bombardeig
        data_bombardeig = dada.data_bombardeig && dada.data_bombardeig.trim() !== '' ? dada.data_bombardeig : 'Desconegut';
        municipi_bombardeig = dada.municipi_bombardeig && dada.municipi_bombardeig.trim() !== '' ? dada.municipi_bombardeig : 'Desconegut';
        lloc_bombardeig = dada.lloc_bombardeig && dada.lloc_bombardeig.trim() !== '' ? dada.lloc_bombardeig : 'Desconegut';
        const responsablesMap: Record<string, string> = {
          '1': 'Aviació feixista italiana',
          '2': 'Aviació nazista alemanya',
          '3': 'Aviació franquista',
        };

        titolMunicipiCadaver = 'Municipi del bombargeig';
        const responsable_bombardeig = dada.responsable_bombardeig != null ? responsablesMap[dada.responsable_bombardeig] || 'Desconegut' : 'Desconegut';

        contingutHtmlBombargeig = `
        <div class="negreta raleway">
          <div style="margin-top:25px">
            <h5><span class="negreta blau1">Causa de la mort: Bombardeig</span></h5>
              <p><span class='marro2'>Data del bombardeig: </span> <span class='blau1'>${data_bombardeig}</span></p>
              <p><span class='marro2'>Municipi del bombardeig:</span> <span class='blau1'>${municipi_bombardeig}</span></p>
              <p><span class='marro2'>Tipus d'espai del bombardeig:</span> <span class='blau1'>${lloc_bombardeig}</span></p>
              <p><span class='marro2'>Responsable del bombardeig:</span> <span class='blau1'>${responsable_bombardeig}</span></p>
            </div>
        </div> `;
      } else if (cirscumstancies_mortId === 8) {
        // Causa de la mort: Extra-judicial (assassinat)
        data_detencio = dada.data_detencio && dada.data_detencio.trim() !== '' ? dada.data_detencio : 'Desconegut';
        lloc_detencio = dada.lloc_detencio && dada.lloc_detencio.trim() !== '' ? dada.lloc_detencio : 'Desconegut';
        qui_detencio = dada.qui_detencio && dada.qui_detencio.trim() !== '' ? dada.qui_detencio : 'Desconegut';
        titolMunicipiCadaver = 'Municipi assassinat extra-judicial';

        contingutHtmlAssassinat = `
        <div class="negreta raleway">
          <div style="margin-top:25px">
            <h5><span class="negreta blau1">Causa de la mort: extra-judicial (assassinat)</span></h5>
              <p><span class='marro2'>Data de la detenció: </span> <span class='blau1'>${data_detencio}</span></p>
              <p><span class='marro2'>Lloc de la detenció:</span> <span class='blau1'>${lloc_detencio}</span></p>
              <p><span class='marro2'>Qui el deté?:</span> <span class='blau1'>${qui_detencio}</span></p>
            </div>
        </div> `;
      } else if (cirscumstancies_mortId === 9) {
        //Causa de la mort: Afusellat
        qui_ordena_afusellat = dada.qui_ordena_afusellat && dada.qui_ordena_afusellat.trim() !== '' ? dada.qui_ordena_afusellat : 'Desconegut';
        qui_executa_afusellat = dada.qui_executa_afusellat && dada.qui_executa_afusellat.trim() !== '' ? dada.qui_executa_afusellat : 'Desconegut';
        titolMunicipiCadaver = 'Municipi afusellament';

        contingutHtmlAfusellat = `
        <div class="negreta raleway">
          <div style="margin-top:25px">
            <h5><span class="negreta blau1">Causa de la mort: afusellat</span></h5>
              <p><span class='marro2'>Qui ordena l'afusellament: </span> <span class='blau1'>${qui_ordena_afusellat}</span></p>
              <p><span class='marro2'>Qui l'executa:</span> <span class='blau1'>${qui_executa_afusellat}</span></p>
            </div>
        </div> `;
      } else {
        titolMunicipiCadaver = 'Municipi trobada del cadàver';
      }

      const cirscumstancies_mort = dada.cirscumstancies_mort && dada.cirscumstancies_mort.trim() !== '' ? dada.cirscumstancies_mort : 'Desconegut';
      const data_trobada_cadaver = dada.data_trobada_cadaver && dada.data_trobada_cadaver.trim() !== '' ? formatDatesForm(dada.data_trobada_cadaver) : 'Desconegut';
      const lloc_trobada_cadaver = dada.lloc_trobada_cadaver && dada.lloc_trobada_cadaver.trim() !== '' ? dada.lloc_trobada_cadaver : 'Desconegut';

      htmlContent += `
    <div class="negreta raleway">
      <div style="margin-top:25px">
        <h5><span class="negreta blau1">1) Dades bàsiques:</span></h5>
          <p><span class='marro2'>Circumstàncies de la mort: </span> <span class='blau1'>${cirscumstancies_mort}</span></p>
          <p><span class='marro2'>Data trobada del càdaver:</span> <span class='blau1'>${data_trobada_cadaver}</span></p>
          <p><span class='marro2'>${titolMunicipiCadaver}:</span> <span class='blau1'>${lloc_trobada_cadaver}</span></p>
        </div>
    </div> `;

      htmlContent += contingutHtmlBombargeig;
      htmlContent += contingutHtmlAssassinat;
      htmlContent += contingutHtmlAfusellat;
    } else if (parseInt(categoriaNumerica) === 6) {
      const dataDetencio = dada.data_detencio && dada.data_detencio.trim() !== '' ? formatDatesForm(dada.data_detencio) : 'Desconeguda';
      const llocDetencio = valorTextDesconegut(dada.lloc_detencio, 1);

      const tipusProcediment = dada.tipus_procediment && dada.tipus_procediment.trim() !== '' ? dada.tipus_procediment : 'Desconegut';
      const tipusJudici = dada.tipus_judici && dada.tipus_judici.trim() !== '' ? dada.tipus_judici : 'Desconegut';
      const numCausa = dada.num_causa && dada.num_causa.trim() !== '' ? dada.num_causa : 'Desconegut';
      const anyDetingut = dada.anyDetingut && dada.anyDetingut.trim() !== '' ? dada.anyDetingut + ' anys' : 'Desconegut';
      const anyInici = dada.any_inicial && dada.any_inicial.trim() !== '' ? dada.any_inicial : 'Desconegut';
      const anyFinal = dada.any_final && dada.any_final.trim() !== '' ? dada.any_final : 'Desconegut';
      const dataInici = dada.data_inici_proces && dada.data_inici_proces.trim() !== '' ? formatDatesForm(dada.data_inici_proces) : 'Desconegut';
      const dataSentencia = dada.sentencia_data && dada.sentencia_data.trim() !== '' ? formatDatesForm(dada.sentencia_data) : 'Desconegut';

      const sentencia = dada.sentencia && dada.sentencia.trim() !== '' ? dada.sentencia : 'Desconeguda';
      const pena = dada.pena && dada.pena.trim() !== '' ? dada.pena : 'Desconeguda';
      const commutacio = dada.commutacio && dada.commutacio.trim() !== '' ? dada.commutacio : '-';

      const jutgeInstructor = dada.jutge_instructor && dada.jutge_instructor.trim() !== '' ? dada.jutge_instructor : 'Desconegut';
      const secretariInstructor = dada.secretari_instructor && dada.secretari_instructor.trim() !== '' ? dada.secretari_instructor : 'Desconegut';
      const jutjat = dada.jutjat && dada.jutjat.trim() !== '' ? dada.jutjat : 'Desconegut';
      const consellGuerraData = dada.consell_guerra_data && dada.consell_guerra_data.trim() !== '' ? formatDatesForm(dada.consell_guerra_data) : 'Desconegut';
      const llocConsellGuerra = dada.lloc_consell_guerra && dada.lloc_consell_guerra.trim() !== '' ? dada.lloc_consell_guerra : 'Desconegut';
      const presidentTribunal = dada.president_tribunal && dada.president_tribunal.trim() !== '' ? dada.president_tribunal : 'Desconegut';
      const defensor = dada.defensor && dada.defensor.trim() !== '' ? dada.defensor : 'Desconegut';
      const fiscal = dada.fiscal && dada.fiscal.trim() !== '' ? dada.fiscal : 'Desconegut';
      const ponent = dada.ponent && dada.ponent.trim() !== '' ? dada.ponent : 'Desconegut';
      const tribunalVocals = dada.tribunal_vocals && dada.tribunal_vocals.trim() !== '' ? dada.tribunal_vocals : 'Desconegut';
      const acusacio = dada.acusacio && dada.acusacio.trim() !== '' ? dada.acusacio : 'Desconeguda';
      const acusacio2 = dada.acusacio_2 && dada.acusacio_2.trim() !== '' ? dada.acusacio_2 : 'Desconeguda';
      const testimoniAcusacio = dada.testimoni_acusacio && dada.testimoni_acusacio.trim() !== '' ? dada.testimoni_acusacio : 'Desconegut';
      const observacions = dada.observacions && dada.observacions.trim() !== '' ? dada.observacions : 'Sense dades';

      htmlContent += `
     <div class="negreta raleway">

     <div style="margin-top:25px">
        <h5><span class="negreta blau1">1) Dades sobre la detenció</span></h5>
          <p><span class='marro2'>Data de detenció: </span> <span class='blau1'>${dataDetencio}</span></p>
          <p><span class='marro2'>Lloc de detenció:</span> <span class='blau1'>${llocDetencio}</span></p>
        </div>

      <div style="margin-top:45px">
        <h5><span class="negreta blau1">2) Dades bàsiques del procés judicial:</span></h5>
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
          <h5><span class="negreta blau1">3) Resolució del procés judicial:</span></h5>
            <p><span class='marro2'>Sentència: </span> <span class='blau1'>${sentencia}</span></p>
            <p><span class='marro2'>Pena: </span> <span class='blau1'>${pena}</span></p>
            <p><span class='marro2'>Commutació o indult: </span> <span class='blau1'>${commutacio}</span></p>
      </div>

      <div style="margin-top:45px">
         <h5><span class="negreta blau1">4) Informació detallada del procés judicial:</span></h5>
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
            <h5><span class="negreta blau1">5) Altres dades:</span></h5>
            <p><span class='marro2'>Observacions:</span> <span class='blau1'>${observacions}</span></p>
        </div>
      </div>`;
    } else if (parseInt(categoriaNumerica) === 7) {
      const empresa = valorTextDesconegut(dada.empresa, 1);
      type ProfessionalTypeKeys = 1 | 2 | 3;
      const professionalTypes = {
        1: 'Empleat sector públic: (funcionari públic)',
        2: 'Empleat sector públic: (professor educació pública)',
        3: 'Empleat sector privat',
      };

      const tipusProfessional = professionalTypes[dada.tipus_professional as ProfessionalTypeKeys] || 'Desconegut';
      const professio = valorTextDesconegut(dada.professio, 1);
      const sancio = valorTextDesconegut(dada.sancio, 5);
      const observacions = valorTextDesconegut(dada.observacions, 3);

      htmlContent += `
          <div class="negreta raleway">

     <div style="margin-top:25px">
        <h5><span class="negreta blau1">1) Dades sobre la depuració al lloc de treball</span></h5>
          <p><span class='marro2'>Sector professional: </span> <span class='blau1'>${tipusProfessional}</span></p>
          <p><span class='marro2'>Professió:</span> <span class='blau1'>${professio}</span></p>
          <p><span class='marro2'>Empresa:</span> <span class='blau1'>${empresa}</span></p>
          <p><span class='marro2'>Sanció:</span> <span class='blau1'>${sancio}</span></p>
          <p><span class='marro2'>Observacions:</span> <span class='blau1'>${observacions}</span></p>     
        </div>
        `;
    } else if (parseInt(categoriaNumerica) === 8) {
      htmlContent += `
          <h5>Dona:</h5>
        `;
    } else if (parseInt(categoriaNumerica) === 10) {
      const dataExili = dada.data_exili && dada.data_exili.trim() !== '' ? formatDatesForm(dada.data_exili) : 'Sense dades';
      const lloc_partida = dada.lloc_partida && dada.lloc_partida.trim() !== '' ? dada.lloc_partida : 'Sense dades';
      const llocPas = dada.lloc_pas_frontera && dada.lloc_pas_frontera.trim() !== '' ? dada.lloc_pas_frontera : 'Sense dades';
      const ambQuiPasaFrontera = dada.amb_qui_passa_frontera && dada.amb_qui_passa_frontera.trim() !== '' ? dada.amb_qui_passa_frontera : 'Sense dades';
      const primerMunicipiExili = dada.primer_desti_exili && dada.primer_desti_exili.trim() !== '' ? dada.primer_desti_exili : 'Sense dades';
      const dataPrimerDesti = dada.primer_desti_data && dada.primer_desti_data.trim() !== '' ? formatDatesForm(dada.primer_desti_data) : 'Sense dades';
      const tipologiaPrimerDesti = dada.tipologia_primer_desti && dada.tipologia_primer_desti.trim() !== '' ? dada.tipologia_primer_desti : 'Sense dades';
      const dadesPrimerDesti = dada.dades_lloc_primer_desti && dada.dades_lloc_primer_desti.trim() !== '' ? dada.dades_lloc_primer_desti : 'Sense dades';

      const peripleExili = dada.periple_recorregut && dada.periple_recorregut.trim() !== '' ? dada.periple_recorregut : 'Sense dades';
      const deportat = dada.deportat === 1 ? 'Sí' : 'No';
      const resistencia = dada.participacio_resistencia === 1 ? 'Sí' : 'No';
      const dadesResistencia = dada.dades_resistencia && dada.dades_resistencia.trim() !== '' ? dada.dades_resistencia : 'Sense dades';
      const activitatPolitica = dada.activitat_politica_exili && dada.activitat_politica_exili.trim() !== '' ? dada.activitat_politica_exili : 'Sense dades';
      const activitatSindical = dada.activitat_sindical_exili && dada.activitat_sindical_exili.trim() !== '' ? dada.activitat_sindical_exili : 'Sense dades';
      const situacioEspanya = dada.situacio_legal_espanya && dada.situacio_legal_espanya.trim() !== '' ? dada.situacio_legal_espanya : 'Sense dades';
      const darrerDestiExili = dada.ultim_desti_exili && dada.ultim_desti_exili.trim() !== '' ? dada.ultim_desti_exili : 'Sense dades';
      const tipologiaDarrerDesti = dada.tipologia_ultim_desti && dada.tipologia_ultim_desti.trim() !== '' ? dada.tipologia_ultim_desti : 'Sense dades';

      htmlContent += `
     <div class="negreta raleway">
      <div style="margin-top:25px">
        <h5><span class="negreta blau1">1) Sortida de Catalunya:</span></h5>
          <p><span class='marro2'>Data d'exili: </span> <span class='blau1'>${dataExili}</span></p>
          <p><span class='marro2'>Lloc de partida per a l'exili:</span> <span class='blau1'>${lloc_partida}</span></p>
          <p><span class='marro2'>Lloc de pas de la frontera:</span> <span class='blau1'>${llocPas}</span></p>
          <p><span class='marro2'>Amb qui pasa a l'exili:</span> <span class='blau1'>${ambQuiPasaFrontera}</span></p>
      </div>

      <div style="margin-top:45px">
        <h5><span class="negreta blau1">2) Arribada al lloc d'exili:</span></h5>
          <p><span class='marro2'>Primer municipi de destí a l'exili: </span> <span class='blau1'>${primerMunicipiExili}</span></p>
          <p><span class='marro2'>Data del primer destí de l'exili: </span> <span class='blau1'>${dataPrimerDesti}</span></p>
          <p><span class='marro2'>Tipologia del primer destí a l'exili: </span> <span class='blau1'>${tipologiaPrimerDesti}</span></p>
          <p><span class='marro2'>Dades del primer destí de l'exili: </span> <span class='blau1'>${dadesPrimerDesti}</span></p>
        </div>

      <div style="margin-top:45px">
        <h5><span class="negreta blau1">3) Periple durant l'exili:</span></h5>
          <p><span class='marro2'>Periple del recorregut a l'exili: </span> <span class='blau1'>${peripleExili}</span></p>
          <p><span class='marro2'>Deportat als camps de concentració nazi: </span> <span class='blau1'>${deportat}</span></p>
        </div>

        <div style="margin-top:45px">
        <h5><span class="negreta blau1">4) Activitat política i sindical durant l'exili:</span></h5>
          <p><span class='marro2'>Participació a la Resistència francesa: </span> <span class='blau1'>${resistencia}</span></p>
          <p><span class='marro2'>Dades de la Resistència: </span> <span class='blau1'>${dadesResistencia}</span></p>
          <p><span class='marro2'>Activitat política a l'exili: </span> <span class='blau1'>${activitatPolitica}</span></p>
          <p><span class='marro2'>Activitat sindical a l'exili: </span> <span class='blau1'>${activitatSindical}</span></p>
           <p><span class='marro2'>Situació legal a Espanya: </span> <span class='blau1'>${situacioEspanya}</span></p>
        </div>

      <div style="margin-top:45px">
        <h5><span class="negreta blau1">5) Final del període d'exili:</span></h5>
          <p><span class='marro2'>Darrer municipi de destí a l'exili: </span> <span class='blau1'>${darrerDestiExili}</span></p>
          <p><span class='marro2'>Tipologia del darrer destí a l'exili: </span> <span class='blau1'>${tipologiaDarrerDesti}</span></p>
        </div> 
    </div>      
        `;
    } else if (parseInt(categoriaNumerica) === 12) {
      // RESULTATS MULTIPLES
      const optionsMap: Record<string, string> = {
        '1': 'Sí',
        '2': 'No',
        '3': 'Sense dades',
      };

      htmlContent = '';

      for (const [index, dada] of dades.entries()) {
        const dataEmpresonament = formatDatesForm(dada.data_empresonament);
        const data_empresonament = valorTextDesconegut(dataEmpresonament, 5);
        const trasllats = optionsMap[dada.trasllats] || 'Sense dades';
        const lloc_trasllat = valorTextDesconegut(dada.lloc_trasllat, 5);
        const dataTrasllat = formatDatesForm(dada.data_trasllat);
        const data_trasllat = valorTextDesconegut(dataTrasllat, 5);
        const llibertat = optionsMap[dada.llibertat] || 'Sense dades';
        const dataLlibertat = formatDatesForm(dada.data_llibertat);
        const data_llibertat = valorTextDesconegut(dataLlibertat, 5);
        const modalitat = dada.modalitat ?? 'Sense dades';
        const vicissituds = valorTextDesconegut(dada.vicissituds, 1);
        const observacions = valorTextDesconegut(dada.observacions, 1);

        htmlContent += `
        <div class="negreta raleway" style="margin-bottom:10px; padding-bottom:30px">
          <h4 class="blau1">Empresonament Presó Model, registre núm. ${index + 1}</h4>
        <div class="negreta raleway" style="margin-bottom:10px; padding-bottom:30px">
          <div style="margin-top:25px">
            <h5><span class="negreta blau1">1) Dades de l'empresonament:</span></h5>
            <p><span class='marro2'>Data d'empresonament:</span> <span class='blau1'>${data_empresonament}</span></p>
            <p><span class='marro2'>Modalitat de presó:</span> <span class='blau1'>${modalitat}</span></p>
            <p><span class='marro2'>Llibertat:</span> <span class='blau1'>${llibertat}</span></p>
            <p><span class='marro2'>Data llibertat:</span> <span class='blau1'>${data_llibertat}</span></p>
          </div>

          <div style="margin-top:45px">
            <h5><span class="negreta blau1">2) Trasllats de presó:</span></h5>
            <p><span class='marro2'>Trasllats:</span> <span class='blau1'>${trasllats}</span></p>
            <p><span class='marro2'>Lloc trasllat:</span> <span class='blau1'>${lloc_trasllat}</span></p>
            <p><span class='marro2'>Data trasllat:</span> <span class='blau1'>${data_trasllat}</span></p>
          </div>

          <div style="margin-top:45px">
            <h5><span class="negreta blau1">3) Altres dades:</span></h5>
            <p><span class='marro2'>Vicissituds:</span> <span class='blau1'>${vicissituds}</span></p>
            <p><span class='marro2'>Observacions:</span> <span class='blau1'>${observacions}</span></p>
          </div>
        </div>
      </div>
      `;
      }
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
