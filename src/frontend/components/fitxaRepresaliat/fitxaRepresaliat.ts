import { calcularEdadAlMorir } from '../../config';
import { categoriesRepressio } from '../taulaDades/categoriesRepressio';
import { fetchData } from '../../services/api/api';
import { Fitxa, FitxaJudicial, FitxaFamiliars } from '../../types/types';
import { fitxaTipusRepressio } from './tab_tipus_repressio';
import { formatDates, formatDatesForm } from '../../services/formatDates/dates';
import { carregarTraduccions, getTraducciones } from '../../services/textosIdiomes/traduccio';
import { traduirCategoriesRepressioArray } from '../taulaDades/traduirCategoriesRepressio';

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

      btn.onclick = () => {
        const divInfo = document.getElementById('fitxa-categoria');
        if (!divInfo) return;

        if (divInfo.style.display === 'block' && divInfo.dataset.categoria === String(catNum)) {
          divInfo.style.display = 'none';
          btn.classList.remove('active');
        } else {
          divInfo.innerHTML = '';
          divInfo.dataset.categoria = String(catNum);

          // Quitar active a todos los botones y poner al actual
          const allButtons = contenedorCategorias.getElementsByClassName('botoCategoriaRepresio');
          Array.from(allButtons).forEach((b) => b.classList.remove('active'));

          btn.classList.add('active');

          mostrarCategoria(catNum, idPersona);
          divInfo.style.display = 'block';
        }
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

  // Si el contenido ya está visible, lo ocultamos
  if (divInfo.style.display === 'block' && divInfo.dataset.categoria === String(categoriaNumerica)) {
    divInfo.style.display = 'none';
    return;
  }

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
    urlAjax2 = `${devDirectory}/api/represalia_republicana/get/fitxaId?id=${idPersona}`;
  } else if (parseInt(categoriaNumerica) === 6) {
    urlAjax2 = `${devDirectory}/api/processats/get/fitxaId?id=${idPersona}`;
  } else if (parseInt(categoriaNumerica) === 7) {
    urlAjax2 = `${devDirectory}/api/depurats/get/fitxaId?id=${idPersona}`;
  } else if (parseInt(categoriaNumerica) === 8) {
    urlAjax2 = `${devDirectory}/api/dones/get/fitxaId?id=${idPersona}`;
  } else if (parseInt(categoriaNumerica) === 10) {
    urlAjax2 = `${devDirectory}/api/exiliats/get/fitxaId?id=${idPersona}`;
  } else {
    console.error('Categoria no válida:', categoriaNumerica);
    return;
  }

  // Comprobar si los datos ya están en caché
  if (!categoriaCache[categoriaNumerica]) {
    try {
      // Hacer la llamada a la API y esperar la respuesta
      const data = await fetchData(urlAjax2);
      const fitxa2 = data as FitxaJudicial;

      categoriaCache[categoriaNumerica] = fitxa2; // Almacenar en caché

      // Continúa con tu código
      const divInfo = document.getElementById('fitxa-categoria');
      if (!divInfo) return;

      // Mostrar el div en caso de estar oculto
      divInfo.style.display = 'block';

      // Mostrar la información dependiendo de la categoría
      fitxaTipusRepressio(categoriaNumerica, fitxa2);
    } catch (error) {
      console.error('Error al obtener la información de la categoría:', error);
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
  const dataNaixement = dataFormatada ?? 'Data desconeguda';

  const dataFormatada2 = formatDatesForm(fitxa.data_defuncio);
  const dataDefuncio = dataFormatada2 ?? 'Data desconeguda';

  const ciutatNaixement = fitxa.ciutat_naixement === '' || null ? 'Desconegut' : fitxa.ciutat_naixement;
  const comarcaNaixement = fitxa.comarca_naixement === '' || null ? 'Desconegut' : fitxa.comarca_naixement;
  const provinciaNaixement = fitxa.provincia_naixement === '' || null ? 'Desconegut' : fitxa.provincia_naixement;
  const comunitatNaixement = fitxa.comunitat_naixement === '' || null ? 'Desconegut' : fitxa.comunitat_naixement;
  const paisNaixement = fitxa.pais_naixement === '' || null ? 'Desconegut' : fitxa.pais_naixement;
  const adreca = fitxa.adreca === '' || null ? 'Desconeguda' : fitxa.adreca;
  const ciutatResidencia = fitxa.ciutat_residencia === '' || null ? 'Desconeguda' : fitxa.ciutat_residencia;
  const comarcaResidencia = fitxa.comarca_residencia === '' || null ? 'Desconeguda' : fitxa.comarca_residencia;
  const provinciaResidencia = fitxa.provincia_residencia === '' || null ? 'Desconeguda' : fitxa.provincia_residencia;
  const comunitatResidencia = fitxa.comunitat_residencia === '' || null ? 'Desconeguda' : fitxa.comunitat_residencia;
  const paisResidencia = fitxa.pais_residencia === '' || null ? 'Desconegut' : fitxa.pais_residencia;
  const ciutatDefuncio = fitxa.ciutat_defuncio === '' || fitxa.ciutat_defuncio === null || fitxa.ciutat_defuncio === undefined ? 'Desconeguda' : fitxa.ciutat_defuncio;
  const comarcaDefuncio = fitxa.comarca_defuncio === '' || null ? 'Desconeguda' : fitxa.comarca_defuncio;
  const provinciaDefuncio = fitxa.provincia_defuncio === '' || null ? 'Desconeguda' : fitxa.provincia_defuncio;
  const comunitatDefuncio = fitxa.comunitat_defuncio === '' || null ? 'Desconeguda' : fitxa.comunitat_defuncio;
  const paisDefuncio = fitxa.pais_defuncio === '' || null ? 'Desconegut' : fitxa.pais_defuncio;

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
        <p><span class='marro2'>Ciutat de naixement:</span> <span class='blau1'>${ciutatNaixement} <span class='normal'>(${comarcaNaixement}, ${provinciaNaixement}, ${comunitatNaixement}, ${paisNaixement})</span></span></p>
        <p><span class='marro2'>Lloc de residència:</span> <span class='blau1'>${adreca}, ${ciutatResidencia} <span class='normal'>(${comarcaResidencia}, ${provinciaResidencia}, ${comunitatResidencia}, ${paisResidencia})</span></span></p>
        <p><span class='marro2'>Ciutat de defunció:</span> <span class='blau1'>${ciutatDefuncio} <span class='normal'>(${comarcaDefuncio}, ${provinciaDefuncio}, ${comunitatDefuncio}, ${paisDefuncio})</span></span></p>
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
          divInfo.innerHTML += `
        <p><span class='marro2'>${familiar.relacio_parentiu}:</span> <span class='blau1'>${familiar.nomFamiliar} ${familiar.cognomFamiliar1} 
        ${familiar.cognomFamiliar2} ${familiar.anyNaixementFamiliar ? `(${familiar.anyNaixementFamiliar})` : ''}</span></p>
      </div>`;
        });
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
            <h5 class="titolSeccio2">Activitat política i sindical durant la guerra:</h5>
            <p><span class='marro2'>Afiliació política:</span> <span class='blau1'>-</span></p>
            <p><span class='marro2'>Afiliació sindical:</span> <span class='blau1'>-</span></p>
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
    case 'tab6':
      divInfo.innerHTML = `
        <h3 class="titolSeccio">${label}</h3>
        <p>En elaboració</p>

      `;
      break;
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
