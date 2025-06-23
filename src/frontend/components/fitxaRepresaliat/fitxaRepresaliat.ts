import { calcularEdadAlMorir } from '../../config';
import { categoriesRepressio } from '../taulaDades/categoriesRepressio';
import { fetchData } from '../../services/api/api';
import { Fitxa, FitxaJudicial, FitxaFamiliars, ApiResponse } from '../../types/types';
import { fitxaTipusRepressio } from './tab_tipus_repressio';
import { formatDates, formatDatesForm } from '../../services/formatDates/dates';
import { carregarTraduccions, getTraducciones } from '../../services/textosIdiomes/traduccio';
import { traduirCategoriesRepressioArray } from '../taulaDades/traduirCategoriesRepressio';
import { valorTextDesconegut } from '../../services/formatDates/valorTextDesconegut';
import { joinValors } from '../../services/formatDates/joinValors';
import { taulaBibliografia } from '../modificaFitxaRepresaliat/taulaBibliografia';
import { taulaArxius } from '../modificaFitxaRepresaliat/taulaArxius';

interface Partit {
  id: number;
  partit_politic: string;
  sigles: string;
}

interface Sindicat {
  id: number;
  sindicat: string;
  sigles: string;
}

async function obtenerNombresPartidos(ids: number[]): Promise<string[]> {
  try {
    // Llamada a la API para obtener todos los partidos políticos
    const devDirectory = `https://${window.location.hostname}`;
    const url = `${devDirectory}/api/auxiliars/get/partitsPolitics`;

    const response = await fetch(url, {
      method: 'GET', // O cualquier otro método que necesites (POST, PUT, etc.)
      headers: {
        'Content-Type': 'application/json', // Opcional, dependiendo de lo que espera el backend
      },
      credentials: 'include', // Esto asegura que la cookie se envíe con la solicitud
    });
    if (!response.ok) throw new Error('Error al obtener los datos de la API');

    const partits: Partit[] = await response.json();

    // Filtrar los partidos que coinciden con los IDs proporcionados
    const partidosFiltrados = partits.filter((partit) => ids.includes(partit.id)).map((partit) => (partit.id === 10 ? partit.partit_politic : `${partit.partit_politic} (${partit.sigles})`));

    return partidosFiltrados;
  } catch (error) {
    console.error('Error al procesar los partidos:', error);
    return [];
  }
}

async function obtenerNombresSindicats(ids: number[]): Promise<string[]> {
  try {
    // Llamada a la API para obtener todos los partidos políticos
    const devDirectory = `https://${window.location.hostname}`;
    const url = `${devDirectory}/api/auxiliars/get/sindicats`;

    const response = await fetch(url);
    if (!response.ok) throw new Error('Error al obtener los datos de la API');

    const sindicats: Sindicat[] = await response.json();

    // Filtrar los partidos que coinciden con los IDs proporcionados
    const sindicatsFiltrados = sindicats.filter((sindicat) => ids.includes(sindicat.id)).map((sindicat) => (sindicat.id === 4 ? sindicat.sindicat : `${sindicat.sindicat} (${sindicat.sigles})`));

    return sindicatsFiltrados;
  } catch (error) {
    console.error('Error al procesar los partidos:', error);
    return [];
  }
}

export async function initButtons(id: string): Promise<void> {
  await carregarTraduccions(); // Asegurar que las traducciones están cargadas antes de usarlas
  const traducciones = getTraducciones(); // Obtener la versión actualizada

  const contenedorBotones = document.getElementById('botons1');
  if (!contenedorBotones) return; // Asegurarse de que el contenedor de botones existe

  const buttons: { id: number; label: string; category: string }[] = [
    { id: 1, label: traducciones['tab1'] || 'Dades personals', category: 'tab1' },
    { id: 2, label: traducciones['tab2'] || 'Dades familiars', category: 'tab2' },
    { id: 3, label: traducciones['tab3'] || 'Dades acadèmiques i laborals', category: 'tab3' },
    { id: 4, label: traducciones['tab4'] || 'Dades polítiques i sindicals', category: 'tab4' },
    { id: 5, label: traducciones['tab5'] || 'Biografia', category: 'tab5' },
    { id: 6, label: traducciones['tab6'] || 'Fonts documentals', category: 'tab6' },
    { id: 8, label: traducciones['tab8'] || 'Multimèdia', category: 'tab8' },
    { id: 7, label: traducciones['tab7'] || 'Altres dades', category: 'tab7' },
  ];

  buttons.forEach((button, index) => {
    const btn = document.createElement('button');

    // Asignar las clases base y las columnas de Bootstrap
    btn.className = 'tablinks col'; // Asegura que ocupe todo el ancho

    // Alternar colores: azul para índices impares, gris para índices pares
    if (index % 2 === 0) {
      btn.classList.add('colorBtn1'); // Azul (Bootstrap)
    } else {
      btn.classList.add('colorBtn2'); // Gris (Bootstrap)
    }

    // Establecer el texto y el dataset del botón
    btn.innerText = button.label;
    btn.dataset.tab = button.category;

    // Agregar la clase 'active' al botón tab1 al inicio
    if (button.category === 'tab1') {
      btn.classList.add('active');
    }

    btn.onclick = () => {
      // Eliminar la clase 'active' de todos los botones
      const allButtons = contenedorBotones.getElementsByClassName('tablinks');
      Array.from(allButtons).forEach((b) => b.classList.remove('active'));

      // Agregar la clase 'active' al botón actual
      btn.classList.add('active');

      // Mostrar información correspondiente
      mostrarInformacion(button.category, id, button.label); // Pasar el ID de la persona
    };

    contenedorBotones.appendChild(btn);
  });

  // Cargar automáticamente el tab1 y el div de info al iniciar
  mostrarInformacion('tab1', id, buttons[0].label);

  // Generar botones de categorías dinámicamente
  generarBotonesCategoria(id);
}

// Función principal para generar botones según categorías de la ficha
async function generarBotonesCategoria(idPersona: string): Promise<void> {
  const devDirectory = `https://${window.location.hostname}`;
  const url = `${devDirectory}/api/dades_personals/get/?type=fitxa&id=${idPersona}`;

  try {
    // Obtener categorías (array de objetos {id, name}) para el idioma catalán
    const colectiusRepressio = await categoriesRepressio('ca');

    const data = await fetchData(url); // Obtener ficha persona
    if (!Array.isArray(data)) throw new Error('La API no devolvió un array.');

    const fitxa = data[0];
    const categoriasNumericasString = fitxa.categoria || ''; // Ej: "{11,4,6}"

    // Obtener array de nombres para esas categorías
    const nombresCategorias = traduirCategoriesRepressioArray(categoriasNumericasString, colectiusRepressio);

    const contenedorCategorias = document.getElementById('botons2');
    if (!contenedorCategorias) return;

    contenedorCategorias.innerHTML = ''; // Limpiar contenedor

    // Obtener array de ids de las categorías (para saber qué id corresponde a cada nombre)
    const categoriaIds = categoriasNumericasString.replace(/[{}]/g, '').split(',').map(Number);

    // Crear botones dinámicamente con el nombre y el id correspondientes
    nombresCategorias.forEach((nombre, index) => {
      const catNum = categoriaIds[index]; // El id numérico de la categoría

      const btn = document.createElement('button');
      btn.className = 'botoCategoriaRepresio';
      btn.innerText = nombre;
      btn.dataset.tab = `categoria${catNum}`;
      btn.style.marginRight = '25px';

      btn.onclick = async () => {
        const divInfo = document.getElementById('fitxa-categoria');
        if (!divInfo) return;

        const currentCategoria = String(catNum);
        const isActive = divInfo.style.display === 'block' && divInfo.dataset.categoria === currentCategoria;

        if (isActive) {
          divInfo.style.display = 'none';
          divInfo.dataset.categoria = '';
          btn.classList.remove('active');
          return;
        }

        // Asigna la categoría antes de cargar
        divInfo.dataset.categoria = currentCategoria;
        divInfo.innerHTML = '';
        btn.classList.add('active');

        const allButtons = contenedorCategorias.getElementsByClassName('botoCategoriaRepresio');
        Array.from(allButtons).forEach((b) => b.classList.remove('active'));

        await mostrarCategoria(currentCategoria, idPersona);

        divInfo.style.display = 'block';
      };

      contenedorCategorias.appendChild(btn);
    });
  } catch (error) {
    console.error('Error al generar botones de categoría:', error);
  }
}

// Cache para almacenar los datos de las categorías
const categoriaCache: { [key: string]: FitxaJudicial | null } = {};

// Función para mostrar la información de la categoría
async function mostrarCategoria(categoriaNumerica: string, idPersona: string): Promise<void> {
  const divInfo = document.getElementById('fitxa-categoria');
  if (!divInfo) return; // Verifica si el div existe

  // Limpiar contenido previo y actualizar el dataset
  divInfo.innerHTML = '';
  divInfo.dataset.categoria = String(categoriaNumerica);

  let urlAjax2 = '';
  const devDirectory = `https://${window.location.hostname}`;

  // Definir la URL de la API dependiendo de la categoría
  if (parseInt(categoriaNumerica) === 1) {
    urlAjax2 = `${devDirectory}/api/afusellats/get/fitxaId?id=${idPersona}`;
  } else if (parseInt(categoriaNumerica) === 2) {
    urlAjax2 = `${devDirectory}/api/deportats/get/fitxaId?id=${idPersona}`;
  } else if (parseInt(categoriaNumerica) === 3) {
    urlAjax2 = `${devDirectory}/api/cost_huma_front/get/fitxaId?id=${idPersona}`;
  } else if (parseInt(categoriaNumerica) === 4) {
    urlAjax2 = `${devDirectory}/api/cost_huma_civils/get/fitxaId?id=${idPersona}`;
  } else if (parseInt(categoriaNumerica) === 5) {
    urlAjax2 = `${devDirectory}/api/cost_huma_civils/get/fitxaId?id=${idPersona}`;
  } else if (parseInt(categoriaNumerica) === 6) {
    urlAjax2 = `${devDirectory}/api/processats/get/fitxaId?id=${idPersona}`;
  } else if (parseInt(categoriaNumerica) === 7) {
    urlAjax2 = `${devDirectory}/api/depurats/get/fitxaId?id=${idPersona}`;
  } else if (parseInt(categoriaNumerica) === 8) {
    urlAjax2 = `${devDirectory}/api/dones/get/fitxaId?id=${idPersona}`;
  } else if (parseInt(categoriaNumerica) === 10) {
    urlAjax2 = `${devDirectory}/api/exiliats/get/fitxaId?id=${idPersona}`;
  } else if (parseInt(categoriaNumerica) === 11) {
    urlAjax2 = '';
  } else if (parseInt(categoriaNumerica) === 12) {
    urlAjax2 = `${devDirectory}/api/preso_model/get/fitxaId?id=${idPersona}`;
  } else if (parseInt(categoriaNumerica) === 13) {
    urlAjax2 = `${devDirectory}/api/detinguts_guardia_urbana/get/fitxaId?id=${idPersona}`;
  } else if (parseInt(categoriaNumerica) === 14) {
    urlAjax2 = `${devDirectory}/api/----/get/fitxaId?id=${idPersona}`;
  } else if (parseInt(categoriaNumerica) === 15) {
    urlAjax2 = `${devDirectory}/api/responsabilitats_politiques/get/fitxaId?id=${idPersona}`;
  } else if (parseInt(categoriaNumerica) === 16) {
    urlAjax2 = `${devDirectory}/api/---/get/fitxaId?id=${idPersona}`;
  } else if (parseInt(categoriaNumerica) === 17) {
    urlAjax2 = `${devDirectory}/api/---/get/fitxaId?id=${idPersona}`;
  } else if (parseInt(categoriaNumerica) === 18) {
    urlAjax2 = `${devDirectory}/api/detinguts_comite_solidaritat/get/fitxaId?id=${idPersona}`;
  } else {
    console.error('Categoria no válida:', categoriaNumerica);
    return;
  }

  // Comprobar si los datos ya están en caché
  if (!categoriaCache[categoriaNumerica]) {
    const divInfo = document.getElementById('fitxa-categoria');
    if (!divInfo) return;

    // 1. Mostrar missatge de càrrega
    divInfo.innerHTML = `<div id="fitxa-view">
      <div class="spinner-border" role="status">
        <span class="visually-hidden">Carregant dades...</span>
      </div>`;

    try {
      // Hacer la llamada a la API y esperar la respuesta

      const response = (await fetchData(urlAjax2)) as ApiResponse<FitxaJudicial>;

      // Verificamos si la respuesta tiene error o no tiene datos
      if (response.status === 'error' || !response.data) {
        divInfo.innerHTML = `<p class="text-danger">⚠️ ${response.message || "No s'han trobat dades."}</p>`;
        return;
      }

      const fitxa2 = response.data as FitxaJudicial;

      // 3. Substituir el missatge per les dades carregades
      divInfo.innerHTML = ''; // o renderitzar la vista amb fitxa2

      categoriaCache[categoriaNumerica] = fitxa2; // Almacenar en caché

      // Mostrar la información dependiendo de la categoría
      fitxaTipusRepressio(categoriaNumerica, fitxa2);
    } catch (error) {
      console.error('Error al obtenir la informació de la categoria:', error);
      divInfo.innerHTML = '<p class="text-danger">⚠️ Error al carregar les dades. Torna-ho a intentar més tard.</p>';
    }
  } else {
    // Si los datos ya están en caché, usarlos
    const fitxa2 = categoriaCache[categoriaNumerica];
    if (fitxa2) {
      fitxaTipusRepressio(categoriaNumerica, fitxa2);
    }
  }
}

// Variable para almacenar la información obtenida de la API
let cachedData: Fitxa | null = null;
let cachedData2: FitxaFamiliars[];

// Función para mostrar la información según el tab
async function mostrarInformacion(tab: string, idPersona: string, label: string): Promise<void> {
  // Si los datos aún no están en cache, realizamos la consulta a la API

  const devDirectory = `https://${window.location.hostname}`;
  if (!cachedData) {
    const url = `${devDirectory}/api/dades_personals/get/?type=fitxa&id=${idPersona}`;

    const url2 = `${devDirectory}/api/dades_personals/get/?type=fitxaDadesFamiliars&id=${idPersona}`; // URL para obtener la información de los familiares

    try {
      const data = await fetchData(url);
      const dataFamiliars = await fetchData(url2);

      if (Array.isArray(data)) {
        cachedData = data[0] as Fitxa; // Hacer cast explícito a 'Fitxa'
      } else {
        throw new Error('La API no devolvió un array.');
      }

      if (Array.isArray(dataFamiliars) && dataFamiliars.length > 0) {
        cachedData2 = dataFamiliars; // Hacer cast explícito a 'Fitxa'
      } else {
        console.log('No hi ha dades disponibles');
      }
    } catch (error) {
      console.error('Error al obtener la información:', error);
      return;
    }
  }

  // Ahora, independientemente de si los datos se obtuvieron de la API o del caché, se procede a mostrar la información
  const fitxa = cachedData; // Usamos los datos cacheados
  let fitxaFam;
  if (Array.isArray(cachedData2) && cachedData2.length > 0) {
    fitxaFam = cachedData2;
  }
  // Usamos los datos cacheados
  const divInfo = document.getElementById('fitxa');
  const divAdditionalInfo = document.getElementById('info');
  if (!divInfo || !divAdditionalInfo) return;

  // Limpiar el contenido anterior de fitxa
  divInfo.innerHTML = '';

  const sexeText = parseInt(fitxa.sexe, 10) === 1 ? 'Home' : parseInt(fitxa.sexe, 10) === 2 ? 'Dona' : 'desconegut';

  const fechaNacimiento = fitxa.data_naixement;
  const fechaDefuncion = fitxa.data_defuncio;

  let edatAlMorir = 'Desconeguda';
  if (fechaNacimiento && fechaDefuncion) {
    const edat = calcularEdadAlMorir(fechaNacimiento, fechaDefuncion);
    if (edat !== null) {
      edatAlMorir = `${edat} anys`;
    }
  }

  const carrecText = fitxa.carrec_cat === '' || fitxa.carrec_cat === null || fitxa.carrec_cat === undefined ? 'Desconegut' : fitxa.carrec_cat;
  const sectorText = fitxa.sector_cat === '' || fitxa.sector_cat === null || fitxa.sector_cat === undefined ? 'Desconegut' : fitxa.sector_cat;
  const subsectorText = fitxa.sub_sector_cat === '' || fitxa.sub_sector_cat === null || fitxa.sub_sector_cat === undefined ? 'Desconegut' : fitxa.sub_sector_cat;

  // partits politics
  const idsPartidos = fitxa.filiacio_politica
    .replace(/[{}]/g, '')
    .split(',')
    .map((id) => parseInt(id.trim(), 10));

  // Sindicats
  const idsSindicats = fitxa.filiacio_sindical
    .replace(/[{}]/g, '')
    .split(',')
    .map((id) => parseInt(id.trim(), 10));

  const dataCreacio = fitxa.data_creacio;
  const dataActualitzacio = fitxa.data_actualitzacio;

  // variables tab1
  const dataFormatada = formatDatesForm(fitxa.data_naixement);
  const dataNaixement = valorTextDesconegut(dataFormatada, 4);

  const dataFormatada2 = formatDatesForm(fitxa.data_defuncio);
  const dataDefuncio = valorTextDesconegut(dataFormatada2, 4);

  const ciutatNaixement = valorTextDesconegut(fitxa.ciutat_naixement, 2);
  const comarcaNaixement = valorTextDesconegut(fitxa.comarca_naixement, 3);
  const provinciaNaixement = valorTextDesconegut(fitxa.provincia_naixement, 3);
  const comunitatNaixement = valorTextDesconegut(fitxa.comunitat_naixement, 3);
  const paisNaixement = valorTextDesconegut(fitxa.pais_naixement, 3);

  const naixement = joinValors([comarcaNaixement, provinciaNaixement, comunitatNaixement, paisNaixement], ', ', true);

  const adreca = valorTextDesconegut(fitxa.adreca, 3);
  const ciutatResidencia = valorTextDesconegut(fitxa.ciutat_residencia, 2);
  const adrecaText = joinValors([adreca, ciutatResidencia]);

  const comarcaResidencia = valorTextDesconegut(fitxa.comarca_residencia, 3);
  const provinciaResidencia = valorTextDesconegut(fitxa.provincia_residencia, 3);
  const comunitatResidencia = valorTextDesconegut(fitxa.comunitat_residencia, 3);
  const paisResidencia = valorTextDesconegut(fitxa.pais_residencia, 3);

  const residencia = joinValors([comarcaResidencia, provinciaResidencia, comunitatResidencia, paisResidencia], ', ', true);

  const ciutatDefuncio = valorTextDesconegut(fitxa.ciutat_defuncio, 2);
  const comarcaDefuncio = valorTextDesconegut(fitxa.comarca_defuncio, 3);
  const provinciaDefuncio = valorTextDesconegut(fitxa.provincia_defuncio, 3);
  const comunitatDefuncio = valorTextDesconegut(fitxa.comunitat_defuncio, 3);
  const paisDefuncio = valorTextDesconegut(fitxa.pais_defuncio, 3);

  const defuncio = joinValors([comarcaDefuncio, provinciaDefuncio, comunitatDefuncio, paisDefuncio], ', ', true);

  const tipologiaEspaiDefuncio = fitxa.tipologia_espai_ca === '' || fitxa.tipologia_espai_ca === null || fitxa.tipologia_espai_ca === undefined ? 'Desconeguda' : fitxa.tipologia_espai_ca;
  const observacionsTipologiaEspacioDefuncio = fitxa.observacions_espai === '' || fitxa.observacions_espai === null || fitxa.observacions_espai === undefined ? 'Desconeguda' : fitxa.observacions_espai;
  const causaDefuncio = fitxa.causa_defuncio_ca === '' || fitxa.causa_defuncio_ca === null || fitxa.causa_defuncio_ca === undefined ? 'Desconeguda' : fitxa.causa_defuncio_ca;

  // imatge represaliat
  // Seleccionamos la imagen con el ID 'imatgeRepresaliat'
  const imagen = document.getElementById('imatgeRepresaliat') as HTMLImageElement;

  // Comprobamos si la variable fitxa.img tiene un valor válido
  if (fitxa.img && fitxa.img !== '' && fitxa.img !== null && imagen) {
    imagen.src = `https://${window.location.hostname}/public/img/represaliats/${fitxa.img}.jpg`; // Si es válida, usamos la imagen de la variable
  } else {
    imagen.src = `https://${window.location.hostname}/public/img/foto_defecte.jpg`; // Si no, mostramos la imagen por defecto
  }

  // Dependiendo del tab, generar el contenido
  switch (tab) {
    case 'tab1':
      divInfo.innerHTML = `
        <h3 class="titolSeccio">${label}</h3>
        <p><span class='marro2'>Sexe:</span> <span class='blau1'>${sexeText}</span></p>
        <p><span class='marro2'>Data de naixement:</span> <span class='blau1'>${dataNaixement}</span></p>
        <p><span class='marro2'>Data de defunció:</span> <span class='blau1'>${dataDefuncio}</span></p>
        <p><span class='marro2'>Edat:</span> <span class='blau1'>${edatAlMorir}</span></p>

        <p><span class='marro2'>Municipi de naixement:</span> <span class='blau1'>${ciutatNaixement} <span class='normal'>${naixement}</span></span></p>

        <p><span class='marro2'>Adreça de residència:</span> <span class='blau1'>${adrecaText} <span class='normal'>${residencia}</span></span></p>
        <p><span class='marro2'>Municipi de defunció:</span> <span class='blau1'>${ciutatDefuncio} <span class='normal'>${defuncio}</span></span></p>
        <p><span class='marro2'>Tipologia espai de defunció:</span> <span class='blau1'>${tipologiaEspaiDefuncio}</span></p>
        <p><span class='marro2'>Observacions espai de defunció:</span> <span class='blau1'>${observacionsTipologiaEspacioDefuncio}</span></p>
        <p><span class='marro2'>Causa de la defunció:</span> <span class='blau1'>${causaDefuncio}</span></p>
      `;
      break;
    case 'tab2':
      divInfo.innerHTML = `
        <h3 class="titolSeccio">${label}</h3>
        <p><span class='marro2'>Estat civil:</span> <span class='blau1'>${fitxa.estat_civil}</span></p>
      `;

      // Recorremos el array de familiares y mostramos la información
      if (fitxaFam) {
        divInfo.innerHTML += `<div class="familiar"><p><span class='marro2'>Relació de familiars:</span></p>`;
        fitxaFam.forEach((familiar) => {
          const nomFamiliar = valorTextDesconegut(familiar.nomFamiliar ?? '', 3);
          const cognom1Familiar = valorTextDesconegut(familiar.cognomFamiliar1 ?? '', 3);
          const cognomFamiliar2 = valorTextDesconegut(familiar.cognomFamiliar2 ?? '', 3);
          const naixementFamiliar = valorTextDesconegut(familiar.anyNaixementFamiliar ?? '', 3);
          const familiarDades = joinValors([nomFamiliar, cognom1Familiar, cognomFamiliar2], ' ', false);

          divInfo.innerHTML += `
        <p><span class='marro2'>${familiar.relacio_parentiu}:</span> <span class='blau1'>${familiarDades} ${naixementFamiliar ? `(${naixementFamiliar})` : ''}</span> </p>
      `;
        });
        divInfo.innerHTML += `</div>`;
      }

      break;
    case 'tab3':
      divInfo.innerHTML = `
        <h3 class="titolSeccio">${label}</h3>
        <p><span class='marro2'>Estudis:</span> <span class='blau1'>${fitxa.estudi_cat}</span></p>
        <p><span class='marro2'>Ofici:</span> <span class='blau1'>${fitxa.ofici_cat}</span></p>
        <p><span class='marro2'>Empresa:</span> <span class='blau1'>${fitxa.empresa}</span></p>
        <p><span class='marro2'>Càrrec:</span> <span class='blau1'>${carrecText}</span></p>
        <p><span class='marro2'>Sector econòmic:</span> <span class='blau1'>${sectorText}</span></p>
        <p><span class='marro2'>Sub-sector econòmic:</span> <span class='blau1'>${subsectorText}</span></p>
      `;
      break;
    case 'tab4':
      Promise.all([obtenerNombresPartidos(idsPartidos), obtenerNombresSindicats(idsSindicats)]).then(([nombresPartidos, nombresSindicats]) => {
        const partitPolitic = nombresPartidos.length === 0 ? 'Desconegut' : nombresPartidos.join(', ');
        const sindicat = nombresSindicats.length === 0 ? 'Desconegut' : nombresSindicats.join(', ');

        divInfo.innerHTML = `
          <h3 class="titolSeccio">${label}</h3>
          <div style="margin-top:30px;margin-bottom:30px">
            <h5 class="titolSeccio2">Activitat política i sindical abans de l'esclat de la guerra:</h5>
            <p><span class='marro2'>Afiliació política:</span> <span class='blau1'>${partitPolitic}</span></p>
            <p><span class='marro2'>Afiliació sindical:</span> <span class='blau1'>${sindicat}</span></p>
          </div>
    
          <div style="margin-top:30px;margin-bottom:30px">
            <h5 class="titolSeccio2">Activitat política i sindical durant la guerra civil i la dictadura:</h5>
            <p><span class='blau1'>${valorTextDesconegut(fitxa.activitat_durant_guerra, 1)}</span></p>
          </div>
        `;
      });
      break;
    case 'tab5':
      divInfo.innerHTML = `
        <h3 class="titolSeccio">${label}</h3>
        ${fitxa.biografiaCa ? `<span class='blau1 normal'>${fitxa.biografiaCa}</span>` : 'La biografia no està disponible.'}
      `;
      break;
    case 'tab6': {
      divInfo.innerHTML = `<div id="bibliografia"></div>`;
      // Obtener el contenedor recién creado
      const bibliografiaContainer = document.getElementById('bibliografia');

      if (bibliografiaContainer) {
        // Crear los elementos dinámicamente
        const quadreFonts1 = document.createElement('div');
        quadreFonts1.id = 'quadreFonts1';

        const quadreBibliografia = document.createElement('div');
        quadreBibliografia.id = 'quadreFontsBibliografia';

        const quadreFonts2 = document.createElement('div');
        quadreFonts2.id = 'quadreFonts2';
        quadreFonts2.style.marginTop = '35px';

        const quadreArxius = document.createElement('div');
        quadreArxius.id = 'quadreFontsArxius';

        // Añadir los nuevos elementos al contenedor 'bibliografia'
        bibliografiaContainer.appendChild(quadreFonts1);
        bibliografiaContainer.appendChild(quadreFonts2);

        quadreFonts1.appendChild(quadreBibliografia);
        quadreFonts2.appendChild(quadreArxius);

        // Configurar y añadir contenido a los nuevos elementos
        if (quadreFonts1) {
          quadreFonts1.style.display = 'block';
          const h4 = document.createElement('h4');
          h4.textContent = 'Llistat de bibliografia';
          h4.classList.add('titolSeccio');
          quadreFonts1.prepend(h4);

          if (fitxa?.id) {
            taulaBibliografia(fitxa.id);
          }
        }

        if (quadreFonts2) {
          quadreFonts2.style.display = 'block';
          const h4 = document.createElement('h4');
          h4.textContent = "Llistat d'arxius";
          h4.classList.add('titolSeccio');
          quadreFonts2.prepend(h4);

          if (fitxa?.id) {
            taulaArxius(fitxa.id);
          }
        }
      }
      break;
    }
    case 'tab7':
      divInfo.innerHTML = `
        <h3 class="titolSeccio">${label}</h3>
        ${fitxa.observacions ? `<p><span class='marro2'>Observacions:</span> <span class='blau1'>${fitxa.observacions}</span></p>` : ''}
          <p><span class='marro2'>Fitxa creada per: </span> <span class='blau1'>${fitxa.autorNom} (${fitxa.biografia_cat})</span></p>
          <p><span class='marro2'>Data de creació: </span><span class='blau1'>${formatDates(dataCreacio)}</span></p>
          <p><span class='marro2'>Darrera actualització: </span><span class='blau1'> ${formatDates(dataActualitzacio)}</span></p>
      `;
      break;
    case 'tab8':
      divInfo.innerHTML = `
          <h3 class="titolSeccio">${label}</h3>
          <p>En elaboració</p>
        `;
      break;
    default:
      divInfo.innerHTML = `<p>No hi ha informació disponible.</p>`;
  }

  // Aquí puedes mantener el contenido de divAdditionalInfo si es necesario

  const nom = fitxa.nom !== null ? fitxa.nom : '';
  const cognom1 = fitxa.cognom1 !== null ? fitxa.cognom1 : '';
  const cognom2 = fitxa.cognom2 !== null ? fitxa.cognom2 : '';
  const nombreCompleto = `${nom} ${cognom1} ${cognom2 ?? ''}`;

  divAdditionalInfo.innerHTML = `
      <h4 class="titolRepresaliat"> ${nombreCompleto}</h4>
    `; // No se limpia el contenido
}
