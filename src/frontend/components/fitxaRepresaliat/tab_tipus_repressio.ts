import { categoriesRepressio } from '../taulaDades/categoriesRepressio';
import { FitxaJudicial } from '../../types/types';
import { formatDatesForm } from '../../services/formatDates/dates';

// Mostrar la información dependiendo de la categoría
export const fitxaTipusRepressio = async (categoriaNumerica: string, fitxa2: FitxaJudicial): Promise<void> => {
  const divInfo = document.getElementById('fitxa-categoria');
  if (!divInfo) return;

  // 1. Mostrar el div y el spinner
  divInfo.style.display = 'block';
  divInfo.innerHTML = `<div class="spinner">Carregant dades...</div>`; // Pon aquí tu HTML spinner

  // 2. Esperar por los datos (simulado o real)
  const colectiusArray = await categoriesRepressio('ca');

  // 3. Cuando estén los datos, construir el contenido
  const colectiusRepressio = colectiusArray.reduce((acc, categoria) => {
    acc[categoria.id] = categoria.name;
    return acc;
  }, {} as { [key: string]: string });

  const dades = fitxa2;

  // 4. Reemplaza el contenido del div con la info final (quitando spinner)
  let htmlContent = `
    <h3><span class="blau1 raleway">${colectiusRepressio[categoriaNumerica]}: </span></h3>
  `;

  if (parseInt(categoriaNumerica) === 1) {
    htmlContent += `
    <div class="negreta raleway">
        <p><span class='marro2'>Data d'execució:</span> <span class='blau1'>${formatDatesForm(dades.data_execucio)}</span></p>
        <p><span class='marro2'>Lloc d'execució:</span> <span class='blau1'>${dades.lloc_execucio}</span> ${dades.ciutat_execucio && dades.ciutat_execucio.trim() !== '' ? `<span class="normal blau1">(${dades.ciutat_execucio})</span>` : ''}</p>
        <p><span class='marro2'>Lloc d'enterrament:</span> <span class='blau1'>${dades.lloc_enterrament} </span> ${dades.ciutat_enterrament && dades.ciutat_enterrament.trim() !== '' ? `<span class="normal blau1">(${dades.ciutat_enterrament})</span>` : ''}</p>
        ${dades.observacions && dades.observacions.trim() !== '' ? `  <p><span class='marro2'>Observacions:</span> <span class='blau1'> ${dades.observacions}.</span></p>` : ''}
    </div>`;
  } else if (parseInt(categoriaNumerica) === 2) {
    const situacioDeportat = dades.situacio && dades.situacio.trim() !== '' ? dades.situacio : 'Desconegut';
    const situacioId = dades.situacioId;
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

    const dataAlliberament = dades.data_alliberament && dades.data_alliberament.trim() !== '' ? formatDatesForm(dades.data_alliberament) : 'Desconeguda';
    const municipiAlliberament = dades.ciutat_mort_alliberament && dades.ciutat_mort_alliberament.trim() !== '' ? dades.ciutat_mort_alliberament : 'Desconegut';
    const tipusPreso = dades.preso_tipus && dades.preso_tipus.trim() !== '' ? dades.preso_tipus : 'Desconeguda';
    const nomPreso = dades.preso_nom && dades.preso_nom.trim() !== '' ? dades.preso_nom : 'Desconeguda';
    const dataSortidaPreso = dades.preso_data_sortida && dades.preso_data_sortida.trim() !== '' ? formatDatesForm(dades.preso_data_sortida) : 'Desconeguda';
    const municipiPreso = dades.preso_localitat && dades.preso_localitat.trim() !== '' ? dades.preso_localitat : 'Desconegut';
    const numMatriculaPreso = dades.preso_num_matricula && dades.preso_num_matricula.trim() !== '' ? dades.preso_num_matricula : 'Desconegut';

    const nomCamp = dades.deportacio_nom_camp && dades.deportacio_nom_camp.trim() !== '' ? dades.deportacio_nom_camp : 'Desconegut';
    const dataEntradaCamp = dades.deportacio_data_entrada && dades.deportacio_data_entrada.trim() !== '' ? formatDatesForm(dades.deportacio_data_entrada) : 'Desconeguda';
    const numeroMatriculaCamp = dades.deportacio_num_matricula && dades.deportacio_num_matricula.trim() !== '' ? dades.deportacio_num_matricula : 'Desconegut';
    const nomSubCamp = dades.deportacio_nom_sub && dades.deportacio_nom_sub.trim() !== '' ? dades.deportacio_nom_sub : 'Desconegut';
    const dataEntradaSubCamp = dades.deportacio_data_entrada_subcamp && dades.deportacio_data_entrada_subcamp.trim() !== '' ? dades.deportacio_data_entrada_subcamp : 'Desconegut';
    const numeroMatriculaSubCamp = dades.deportacio_nom_matricula_subcamp && dades.deportacio_nom_matricula_subcamp.trim() !== '' ? dades.deportacio_nom_matricula_subcamp : 'Desconegut';

    htmlContent += `
    <div class="negreta raleway">
      <div style="margin-top:25px">
        <h5><span class="negreta blau1">1) Dades bàsiques:</span></h5>
          <p><span class='marro2'>Situació del deportat: </span> <span class='blau1'>${situacioDeportat}</span></p>
          <p><span class='marro2'>${alliberamentMort}:</span> <span class='blau1'>${dataAlliberament}</span></p>
          <p><span class='marro2'>${municipiMort}:</span> <span class='blau1'>${municipiAlliberament}</span></p>
        </div>

      <div style="margin-top:25px">
        <h5><span class="negreta blau1">2) Dades sobre l'empresonament (previ a la deportació):</span></h5>
          <p><span class='marro2'>Tipus de presó: </span> <span class='blau1'>${tipusPreso}</span></p>
          <p><span class='marro2'>Nom de la presó:</span> <span class='blau1'>${nomPreso}</span></p>
          <p><span class='marro2'>Data de la sortida de la presó:</span> <span class='blau1'>${dataSortidaPreso}</span></p>
          <p><span class='marro2'>Municipi de la presó:</span> <span class='blau1'>${municipiPreso}</span></p>
          <p><span class='marro2'>Número de matrícula presó:</span> <span class='blau1'>${numMatriculaPreso}</span></p>
        </div>

      <div style="margin-top:25px">
        <h5><span class="negreta blau1">3) Dades sobre la deportació:</span></h5>
          <p><span class='marro2'>Nom del camp de deportació : </span> <span class='blau1'>${nomCamp}</span></p>
          <p><span class='marro2'>Data d'entrada al camp</span> <span class='blau1'>${dataEntradaCamp}</span></p>
          <p><span class='marro2'>Número de matrícula:</span> <span class='blau1'>${numeroMatriculaCamp}</span></p>
          <p><span class='marro2'>Nom del subcamp :</span> <span class='blau1'>${nomSubCamp}</span></p>
          <p><span class='marro2'>Data d'entrada al subcamp:</span> <span class='blau1'>${dataEntradaSubCamp}</span></p>
          <p><span class='marro2'>Número de matrícula del subcamp:</span> <span class='blau1'>${numeroMatriculaSubCamp}</span></p>
        </div>
    </div>
        `;
  } else if (parseInt(categoriaNumerica) === 3) {
    const condicio = dades.condicio && dades.condicio.trim() !== '' ? dades.condicio : 'Desconegut';
    const bandol = dades.bandol && dades.bandol.trim() !== '' ? dades.bandol : 'Desconegut';
    const any_lleva = dades.any_lleva && dades.any_lleva.trim() !== '' ? dades.any_lleva : 'Desconegut';

    const unitat_inicial = dades.unitat_inicial && dades.unitat_inicial.trim() !== '' ? dades.unitat_inicial : 'Desconegut';
    const cos = dades.cos && dades.cos.trim() !== '' ? dades.cos : 'Desconegut';
    const unitat_final = dades.unitat_final && dades.unitat_final.trim() !== '' ? dades.unitat_final : 'Desconegut';
    const graduacio_final = dades.graduacio_final && dades.graduacio_final.trim() !== '' ? dades.graduacio_final : 'Desconegut';
    const periple_militar = dades.periple_militar && dades.periple_militar.trim() !== '' ? dades.periple_militar : 'Desconegut';

    const circumstancia_mort = dades.circumstancia_mort && dades.circumstancia_mort.trim() !== '' ? dades.circumstancia_mort : 'Desconeguda';
    const desaparegut_data = dades.desaparegut_data && dades.desaparegut_data.trim() !== '' ? formatDatesForm(dades.desaparegut_data) : '-';
    const desaparegut_lloc = dades.desaparegut_lloc && dades.desaparegut_lloc.trim() !== '' ? dades.desaparegut_lloc : '-';
    const desaparegut_data_aparicio = dades.desaparegut_data_aparicio && dades.desaparegut_data_aparicio.trim() !== '' ? formatDatesForm(dades.desaparegut_data_aparicio) : '-';
    const desaparegut_lloc_aparicio = dades.desaparegut_lloc_aparicio && dades.desaparegut_lloc_aparicio.trim() !== '' ? dades.desaparegut_lloc_aparicio : '-';

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
          <p><span class='marro2'>Periple militar:</span> <span class='blau1'>${periple_militar}</span></p>
        </div>

      <div style="margin-top:25px">
        <h5><span class="negreta blau1">3) Circumstàncies de la mort:</span></h5>
          <p><span class='marro2'>Causa de defunció/desaparació:</span> <span class='blau1'>${circumstancia_mort}</span></p>
          <p><span class='marro2'>Data de la desaparació:</span> <span class='blau1'>${desaparegut_data}</span></p>
          <p><span class='marro2'>Lloc de desaparació:</span> <span class='blau1'>${desaparegut_lloc}</span></p>
          <p><span class='marro2'>Data d'aparació del desaparegut:</span> <span class='blau1'>${desaparegut_data_aparicio}</span></p>
          <p><span class='marro2'>Lloc d'aparació del desaparegut:</span> <span class='blau1'>${desaparegut_lloc_aparicio}</span></p>
        </div>
    </div>

        `;
  } else if (parseInt(categoriaNumerica) === 4 || parseInt(categoriaNumerica) === 5) {
    const cirscumstancies_mortId = dades.cirscumstancies_mortId;

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
      data_bombardeig = dades.data_bombardeig && dades.data_bombardeig.trim() !== '' ? dades.data_bombardeig : 'Desconegut';
      municipi_bombardeig = dades.municipi_bombardeig && dades.municipi_bombardeig.trim() !== '' ? dades.municipi_bombardeig : 'Desconegut';
      lloc_bombardeig = dades.lloc_bombardeig && dades.lloc_bombardeig.trim() !== '' ? dades.lloc_bombardeig : 'Desconegut';
      const responsablesMap: Record<string, string> = {
        '1': 'Aviació feixista italiana',
        '2': 'Aviació nazista alemanya',
        '3': 'Aviació franquista',
      };

      titolMunicipiCadaver = 'Municipi del bombargeig';
      const responsable_bombardeig = dades.responsable_bombardeig != null ? responsablesMap[dades.responsable_bombardeig] || 'Desconegut' : 'Desconegut';

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
      data_detencio = dades.data_detencio && dades.data_detencio.trim() !== '' ? dades.data_detencio : 'Desconegut';
      lloc_detencio = dades.lloc_detencio && dades.lloc_detencio.trim() !== '' ? dades.lloc_detencio : 'Desconegut';
      qui_detencio = dades.qui_detencio && dades.qui_detencio.trim() !== '' ? dades.qui_detencio : 'Desconegut';
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
      qui_ordena_afusellat = dades.qui_ordena_afusellat && dades.qui_ordena_afusellat.trim() !== '' ? dades.qui_ordena_afusellat : 'Desconegut';
      qui_executa_afusellat = dades.qui_executa_afusellat && dades.qui_executa_afusellat.trim() !== '' ? dades.qui_executa_afusellat : 'Desconegut';
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

    const cirscumstancies_mort = dades.cirscumstancies_mort && dades.cirscumstancies_mort.trim() !== '' ? dades.cirscumstancies_mort : 'Desconegut';
    const data_trobada_cadaver = dades.data_trobada_cadaver && dades.data_trobada_cadaver.trim() !== '' ? formatDatesForm(dades.data_trobada_cadaver) : 'Desconegut';
    const lloc_trobada_cadaver = dades.lloc_trobada_cadaver && dades.lloc_trobada_cadaver.trim() !== '' ? dades.lloc_trobada_cadaver : 'Desconegut';

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
    const dataDetencio = dades.data_detencio && dades.data_detencio.trim() !== '' ? formatDatesForm(dades.data_detencio) : 'Desconeguda';
    const llocDetencio = dades.lloc_detencio && dades.lloc_detencio.trim() !== '' ? dades.lloc_detencio : 'Desconegut';

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
    htmlContent += `
          <h5>En elaboració:</h5>
        `;
  } else if (parseInt(categoriaNumerica) === 8) {
    htmlContent += `
          <h5>En elaboració:</h5>
        `;
  } else if (parseInt(categoriaNumerica) === 10) {
    const dataExili = dades.data_exili && dades.data_exili.trim() !== '' ? formatDatesForm(dades.data_exili) : 'Sense dades';
    const lloc_partida = dades.lloc_partida && dades.lloc_partida.trim() !== '' ? dades.lloc_partida : 'Sense dades';
    const llocPas = dades.lloc_pas_frontera && dades.lloc_pas_frontera.trim() !== '' ? dades.lloc_pas_frontera : 'Sense dades';
    const ambQuiPasaFrontera = dades.amb_qui_passa_frontera && dades.amb_qui_passa_frontera.trim() !== '' ? dades.amb_qui_passa_frontera : 'Sense dades';
    const primerMunicipiExili = dades.primer_desti_exili && dades.primer_desti_exili.trim() !== '' ? dades.primer_desti_exili : 'Sense dades';
    const dataPrimerDesti = dades.primer_desti_data && dades.primer_desti_data.trim() !== '' ? formatDatesForm(dades.primer_desti_data) : 'Sense dades';
    const tipologiaPrimerDesti = dades.tipologia_primer_desti && dades.tipologia_primer_desti.trim() !== '' ? dades.tipologia_primer_desti : 'Sense dades';
    const dadesPrimerDesti = dades.dades_lloc_primer_desti && dades.dades_lloc_primer_desti.trim() !== '' ? dades.dades_lloc_primer_desti : 'Sense dades';

    const peripleExili = dades.periple_recorregut && dades.periple_recorregut.trim() !== '' ? dades.periple_recorregut : 'Sense dades';
    const deportat = dades.deportat === '1' ? 'Sí' : 'No';
    const resistencia = dades.participacio_resistencia === '1' ? 'Sí' : 'No';
    const dadesResistencia = dades.dades_resistencia && dades.dades_resistencia.trim() !== '' ? dades.dades_resistencia : 'Sense dades';
    const activitatPolitica = dades.activitat_politica_exili && dades.activitat_politica_exili.trim() !== '' ? dades.activitat_politica_exili : 'Sense dades';
    const activitatSindical = dades.activitat_sindical_exili && dades.activitat_sindical_exili.trim() !== '' ? dades.activitat_sindical_exili : 'Sense dades';
    const situacioEspanya = dades.situacio_legal_espanya && dades.situacio_legal_espanya.trim() !== '' ? dades.situacio_legal_espanya : 'Sense dades';
    const darrerDestiExili = dades.ultim_desti_exili && dades.ultim_desti_exili.trim() !== '' ? dades.ultim_desti_exili : 'Sense dades';
    const tipologiaDarrerDesti = dades.tipologia_ultim_desti && dades.tipologia_ultim_desti.trim() !== '' ? dades.tipologia_ultim_desti : 'Sense dades';

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
    const optionsMap: Record<string, string> = {
      '1': 'Sí',
      '2': 'No',
      '3': 'Sense dades',
    };

    const data_empresonament = dades.data_empresonament && dades.data_empresonament.trim() !== '' ? formatDatesForm(dades.data_empresonament) : 'Sense dades';
    const trasllats = optionsMap[dades.trasllats] || 'Sense dades';
    const lloc_trasllat = dades.lloc_trasllat && dades.lloc_trasllat.trim() !== '' ? dades.lloc_trasllat : 'Sense dades';
    const data_trasllat = dades.data_trasllat && dades.data_trasllat.trim() !== '' ? formatDatesForm(dades.data_trasllat) : 'Sense dades';
    const llibertat = optionsMap[dades.llibertat] || 'Sense dades';
    const data_llibertat = dades.data_llibertat && dades.data_llibertat.trim() !== '' ? formatDatesForm(dades.data_llibertat) : 'Sense dades';
    const modalitat = dades.modalitat ?? 'Sense dades';
    const vicissituds = dades.vicissituds && dades.vicissituds.trim() !== '' ? dades.vicissituds : 'Sense dades';
    const observacions = dades.observacions && dades.observacions.trim() !== '' ? dades.observacions : 'Sense dades';

    htmlContent += `
     <div class="negreta raleway">
      <div style="margin-top:25px">
        <h5><span class="negreta blau1">1) Dades de l'empresonament:</span></h5>
        <p><span class='marro2'>Data d'empresonament: </span> <span class='blau1'>${data_empresonament}</span></p>
        <p><span class='marro2'>Modalitat de presó:</span> <span class='blau1'>${modalitat}</span></p>
        <p><span class='marro2'>Llibertat:</span> <span class='blau1'>${llibertat}</span></p>
        <p><span class='marro2'>Data llibertat:</span> <span class='blau1'>${data_llibertat}</span></p>
      </div>

        <div style="margin-top:45px">
          <h5><span class="negreta blau1">2) Trasllats de presó:</span></h5>
          <p><span class='marro2'>Trasllats: </span> <span class='blau1'>${trasllats}</span></p>
          <p><span class='marro2'>Lloc trasllat:</span> <span class='blau1'>${lloc_trasllat}</span></p>
          <p><span class='marro2'>Data trasllat:</span> <span class='blau1'>${data_trasllat}</span></p>
        </div>

         <div style="margin-top:45px">
            <h5><span class="negreta blau1">3) Altres dades:</span></h5>
            <p><span class='marro2'>Vicissituds: </span> <span class='blau1'>${vicissituds}</span></p>
             <p><span class='marro2'>Observacions:</span> <span class='blau1'>${observacions}</span></p>
        </div>
      </div>     
        `;
  } else {
    htmlContent += `
     <div class="negreta raleway">
     Sense dades
     `;
  }

  // Finalmente actualiza el div
  divInfo.innerHTML = htmlContent;
};
