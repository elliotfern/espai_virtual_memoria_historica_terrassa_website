import { categorias, convertirFecha, calcularEdadAlMorir } from '../../config';
import { fetchData } from '../../services/api/api';
import { Fitxa, FitxaJudicial, FitxaFamiliars } from '../../types/types';
import { fitxaTipusRepressio } from './tab_tipus_repressio';

export function initButtons(id: string): void {
  const contenedorBotones = document.getElementById('botons1');
  if (!contenedorBotones) return; // Asegurarse de que el contenedor de botones existe

  const buttons: { id: number; label: string; category: string }[] = [
    { id: 1, label: 'Dades personals', category: 'tab1' },
    { id: 2, label: 'Dades familiars', category: 'tab2' },
    { id: 3, label: 'Dades acadèmiques i laborals', category: 'tab3' },
    { id: 4, label: 'Dades polítiques i sindicals', category: 'tab4' },
    { id: 5, label: 'Biografia', category: 'tab5' },
    { id: 6, label: 'Fonts documentals', category: 'tab6' },
    { id: 7, label: 'Altres dades', category: 'tab7' },
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

// Función para generar los botones según la categoría obtenida de la API
async function generarBotonesCategoria(idPersona: string): Promise<void> {
  const devDirectory = `https://${window.location.hostname}`;
  const url = `${devDirectory}/api/dades_personals/get/?type=fitxa&id=${idPersona}`;

  try {
    const data = await fetchData(url); // Llamada a la API para obtener la ficha
    if (Array.isArray(data)) {
      const fitxa = data[0]; // Ahora TypeScript sabe que 'data' es un array
      const categoriasNumericas = fitxa.categoria.replace(/[{}]/g, '').split(','); // Obtener las categorías en formato de array

      const contenedorCategorias = document.getElementById('botons2');
      if (!contenedorCategorias) return;

      // Iterar sobre las categorías numéricas y crear botones dinámicamente
      categoriasNumericas.forEach((catNum: string) => {
        const catTitle = categorias[catNum]; // Obtener el título de la categoría desde la constante

        if (catTitle) {
          // Solo crear botón si la categoría tiene un título definido
          const btn = document.createElement('button');
          btn.className = 'tablinks';
          btn.innerText = catTitle;
          btn.dataset.tab = `categoria${catNum}`;

          // Asignar la función que mostrará información al hacer clic en el botón
          btn.onclick = () => {
            const divInfo = document.getElementById('fitxa-categoria');
            if (!divInfo) return; // Verifica si el div existe

            // Si el contenido ya está visible, ocultarlo y eliminar la clase 'active'
            if (divInfo.style.display === 'block' && divInfo.dataset.categoria === String(catNum)) {
              divInfo.style.display = 'none';
              btn.classList.remove('active');
            } else {
              // Limpiar el contenido previo y actualizar el dataset
              divInfo.innerHTML = '';
              divInfo.dataset.categoria = String(catNum);

              // Eliminar la clase 'active' de todos los botones
              const allButtons = contenedorCategorias.getElementsByClassName('tablinks');
              Array.from(allButtons).forEach((b) => b.classList.remove('active'));

              // Agregar la clase 'active' al botón actual
              btn.classList.add('active');

              // Mostrar información de la categoría
              mostrarCategoria(catNum, idPersona);
              divInfo.style.display = 'block'; // Asegúrate de mostrar el div
            }
          };

          // Agregar el botón al contenedor de categorías
          contenedorCategorias.appendChild(btn);
        }
      });
    } else {
      throw new Error('La API no devolvió un array.');
    }
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
    urlAjax2 = `${devDirectory}/api/afusellats/get/?type=fitxa&id=${idPersona}`;
  } else if (parseInt(categoriaNumerica) === 2) {
    urlAjax2 = `${devDirectory}/api/deportats/get/?type=fitxa&id=${idPersona}`;
  } else if (parseInt(categoriaNumerica) === 3) {
    urlAjax2 = `${devDirectory}/api/cost_huma_front/get/?type=fitxa&id=${idPersona}`;
  } else if (parseInt(categoriaNumerica) === 4) {
    urlAjax2 = `${devDirectory}/api/cost_huma_civils/get/?type=fitxa&id=${idPersona}`;
  } else if (parseInt(categoriaNumerica) === 5) {
    urlAjax2 = `${devDirectory}/api/represalia_republicana/get/?type=fitxa&id=${idPersona}`;
  } else if (parseInt(categoriaNumerica) === 6) {
    urlAjax2 = `${devDirectory}/api/processats/get/?type=fitxa&id=${idPersona}`;
  } else if (parseInt(categoriaNumerica) === 7) {
    urlAjax2 = `${devDirectory}/api/depurats/get/?type=fitxa&id=${idPersona}`;
  } else if (parseInt(categoriaNumerica) === 8) {
    urlAjax2 = `${devDirectory}/api/dones/get/?type=fitxa&id=${idPersona}`;
  } else if (parseInt(categoriaNumerica) === 10) {
    urlAjax2 = `${devDirectory}/api/exiliats/get/?type=fitxa&id=${idPersona}`;
  } else {
    console.error('Categoria no válida:', categoriaNumerica);
    return;
  }

  // Comprobar si los datos ya están en caché
  if (!categoriaCache[categoriaNumerica]) {
    try {
      // Hacer la llamada a la API y esperar la respuesta
      const data = await fetchData(urlAjax2);

      if (Array.isArray(data)) {
        const fitxa2 = data[0] as FitxaJudicial; // Hacer cast explícito a 'Fitxa'
        categoriaCache[categoriaNumerica] = fitxa2; // Almacenar en caché

        // Continúa con tu código
        const divInfo = document.getElementById('fitxa-categoria');
        if (!divInfo) return;

        // Mostrar el div en caso de estar oculto
        divInfo.style.display = 'block';

        // Mostrar la información dependiendo de la categoría

        fitxaTipusRepressio(categoriaNumerica, fitxa2);
      } else {
        throw new Error('La API no devolvió un array.');
      }
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

  const fechaNacimiento = convertirFecha(fitxa.data_naixement);
  const fechaDefuncion = convertirFecha(fitxa.data_defuncio);

  let edatAlMorir = 'Desconeguda';
  if (fechaNacimiento && fechaDefuncion) {
    const edat = calcularEdadAlMorir(fechaNacimiento, fechaDefuncion);
    if (edat !== null) {
      edatAlMorir = `${edat} anys`;
    }
  }

  const carrecText = fitxa.carrec_cat === null ? 'Desconegut' : fitxa.carrec_cat;
  const partitPolitic = fitxa.partit_politic === null ? 'Desconegut' : fitxa.partit_politic;
  const sindicat = fitxa.sindicat === null ? 'Desconegut' : fitxa.sindicat;

  const dataCreacio = fitxa.data_creacio;
  const dataActualitzacio = fitxa.data_actualitzacio;

  // variables tab1
  const dataNaixement = fitxa.data_naixement === '' ? '?' : fitxa.data_naixement;
  const dataDefuncio = fitxa.data_defuncio === '' || null ? '?' : fitxa.data_defuncio;
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
  const tipologiaEspaiDefuncio = fitxa.tipologia_espai === '' || null ? 'Desconeguda' : fitxa.tipologia_espai;
  const observacionsTipologiaEspacioDefuncio = fitxa.observacions_espai === '' || null ? 'Desconeguda' : fitxa.observacions_espai;
  const causaDefuncio = fitxa.causa_defuncio === '' || null ? 'Desconeguda' : fitxa.causa_defuncio;

  // Dependiendo del tab, generar el contenido
  switch (tab) {
    case 'tab1':
      divInfo.innerHTML = `
        <h3 class="titolSeccio">${label}</h3>
        <p><span class='negreta'>Sexe:</span> ${sexeText}</p>
        <p><span class='negreta'>Data de naixement:</span> ${dataNaixement}</p>
        <p><span class='negreta'>Data de defunció:</span> ${dataDefuncio}</p>
        <p><span class='negreta'>Edat:</span> ${edatAlMorir}</p>
        <p><span class='negreta'>Ciutat de naixement:</span> ${ciutatNaixement} (${comarcaNaixement}, ${provinciaNaixement}, ${comunitatNaixement}, ${paisNaixement})</p>
        <p><span class='negreta'>Lloc de residència:</span> ${adreca}, ${ciutatResidencia} (${comarcaResidencia}, ${provinciaResidencia}, ${comunitatResidencia}, ${paisResidencia})</p>
        <p><span class='negreta'>Ciutat de defunció:</span> ${ciutatDefuncio} (${comarcaDefuncio}, ${provinciaDefuncio}, ${comunitatDefuncio}, ${paisDefuncio})</p>
        <p><span class='negreta'>Tipologia espai de defunció:</span> ${tipologiaEspaiDefuncio}</p>
        <p><span class='negreta'>Observacions espai de defunció:</span> ${observacionsTipologiaEspacioDefuncio}</p>
        <p><span class='negreta'>Causa de la defunció:</span> ${causaDefuncio}</p>
      `;
      break;
    case 'tab2':
      divInfo.innerHTML = `
        <h3 class="titolSeccio">${label}</h3>
        <p><span class='negreta'>Estat civil:</span> ${fitxa.estat_civil}</p>
      `;

      // Recorremos el array de familiares y mostramos la información
      if (fitxaFam) {
        divInfo.innerHTML += `<div class="familiar"><p><span class='negreta'>Relació de familiars:</span></p>`;
        fitxaFam.forEach((familiar) => {
          divInfo.innerHTML += `
        <p><span class='negreta'>${familiar.relacio_parentiu}:</span> ${familiar.nomFamiliar} ${familiar.cognomFamiliar1} 
        ${familiar.cognomFamiliar2} ${familiar.anyNaixementFamiliar ? `(${familiar.anyNaixementFamiliar})` : ''}</p>
      </div>`;
        });
      }

      break;
    case 'tab3':
      divInfo.innerHTML = `
        <h3 class="titolSeccio">${label}</h3>
        <p><span class='negreta'>Estudis:</span> ${fitxa.estudi_cat}</p>
        <p><span class='negreta'>Ofici:</span> ${fitxa.ofici_cat}</p>
        <p><span class='negreta'>Empresa:</span> ${fitxa.empresa}</p>
        <p><span class='negreta'>Càrrec:</span> ${carrecText}</p>
        <p><span class='negreta'>Sector econòmic:</span> ${fitxa.sector_cat}</p>
        <p><span class='negreta'>Sub-sector econòmic:</span> ${fitxa.sub_sector_cat}</p>
      `;
      break;
    case 'tab4':
      divInfo.innerHTML = `
        <h3 class="titolSeccio">${label}</h3>
        <h5>Activitat política i sindical abans de l'esclat de la guerra:</h5>
        <p><span class='negreta'>Afiliació política:</span> ${partitPolitic}</p>
        <p><span class='negreta'>Afiliació sindical:</span> ${sindicat}</p>
        
        <h5>Activitat política i sindical durant la guerra:</h5>
        <p><span class='negreta'>Afiliació política:</span> -</p>
        <p><span class='negreta'>Afiliació sindical:</span> -</p>
      `;
      break;
    case 'tab5':
      divInfo.innerHTML = `
        <h3 class="titolSeccio">${label}</h3>
        <p><span class='negreta'>Observacions:</span> ${fitxa.observacions}</p>
        <p><span class='negreta'>Biografia:</span> ${fitxa.biografia}</p>
      `;
      break;
    case 'tab6':
      divInfo.innerHTML = `
        <h3 class="titolSeccio">${label}</h3>
        <p><span class='negreta'>Referència arxiu:</span> ${fitxa.ref_num_arxiu}</p>
        <p><span class='negreta'>Font 1:</span> ${fitxa.font_1}</p>
        <p><span class='negreta'>Font 2:</span> ${fitxa.font_2}</p>
      `;
      break;
    case 'tab7':
      divInfo.innerHTML = `
        <h3 class="titolSeccio">${label}</h3>
        <span class='negreta'>Fitxa creada per: </span> ${fitxa.autorNom} (${fitxa.biografia_cat})<br>
        <span class='negreta'>Data de creació: </span>${dataCreacio}<br>
        <span class='negreta'>Darrera actualització: </span> ${dataActualitzacio}
      `;
      break;
    default:
      divInfo.innerHTML = `<p>No hay información disponible para esta categoría.</p>`;
  }

  // Aquí puedes mantener el contenido de divAdditionalInfo si es necesario
  divAdditionalInfo.innerHTML = `
      <h4 class="titolRepresaliat"> ${fitxa.nom} ${fitxa.cognom1} ${fitxa.cognom2}</h4>
    `; // No se limpia el contenido
}
