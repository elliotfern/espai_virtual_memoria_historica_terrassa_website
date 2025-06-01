import { mostrarPestanyaPerDefecte } from './pestanyaDefecte';
import { pestanyesInformacio } from './pestanyesInformacio';
import { fitxaRepressaliat } from './fitxaRepressaliat';

export function modificaFitxa(id: number) {
  mostrarPestanyaPerDefecte();
  fitxaRepressaliat(id);

  // Aquí agrega los event listeners para los botones para que llamen a pestanyesInformacio al hacer click
  const tabLinks = document.getElementsByClassName('tablinks');
  for (let i = 0; i < tabLinks.length; i++) {
    const tabLink = tabLinks[i] as HTMLElement;

    // Suponiendo que cada botón tiene un atributo data-tab con el id de la pestaña correspondiente
    const tabName = tabLink.getAttribute('data-tab');
    if (tabName) {
      tabLink.onclick = (evt) => {
        pestanyesInformacio(evt as MouseEvent, tabName);
      };
    }
  }
}

/*


    // 04. dades politiques
    fetchCheckBoxs(fitxa[0].filiacio_politica, 'partitsPolitics', 'partit_politic');

    document.getElementById('refreshButtonPartits').addEventListener('click', function (event) {
      event.preventDefault();
      fetchCheckBoxs(fitxa[0].filiacio_politica, 'partitsPolitics', 'partit_politic');
    });

    fetchCheckBoxs(fitxa[0].filiacio_sindical, 'sindicats', 'sindicat');

    document.getElementById('refreshButtonSindicats').addEventListener('click', function (event) {
      event.preventDefault();
      fetchCheckBoxs(fitxa[0].filiacio_sindical, 'sindicats', 'sindicat');
    });

    document.getElementById('activitat_durant_guerra').value = fitxa[0].activitat_durant_guerra;

    // 05. Biografia

    // 06. Fonts documentals

    // 07. Altres dades
    document.getElementById('observacions').value = fitxa[0].observacions;
    auxiliarSelect(fitxa[0].autor_id, 'autors_fitxa', 'autor', 'nom');

    const dataCreacio = cambiarFormatoFecha(fitxa[0].data_creacio);
    const dataActualitzacio = cambiarFormatoFecha(fitxa[0].data_actualitzacio);

    document.getElementById('data_creacio').innerText = dataCreacio;
    document.getElementById('data_actualitzacio').innerText = dataActualitzacio;

    let completatValue = fitxa[0].completat; // Este valor puede venir de la API

    // Seleccionamos los botones de radio y les asignamos el valor correspondiente
    if (completatValue == 1) {
      document.getElementById('completat_no').checked = true;
    } else if (completatValue == 2) {
      document.getElementById('completat_si').checked = true;
    }

    document.getElementById("authorPhoto").src = `../../public/img/library-author/${data.nameImg}.jpg`;
  } catch (error) {
    console.error('Error al parsear JSON:', error); // Muestra el error de parsing
  }
}

function cambiarFormatoFecha(fechaOriginal) {
  const [year, month, day] = fechaOriginal.split('-');
  return `${day}/${month}/${year}`;
}

// Carregar el select
async function auxiliarSelect(idAux, api, elementId, valorText) {
  const devDirectory = `https://${window.location.hostname}`;
  let urlAjax = devDirectory + '/api/auxiliars/get/' + api;

  // Configurar las opciones de la solicitud
  const options = {
    method: 'GET',
    headers: {
      'Content-Type': 'application/json',
    },
  };

  try {
    // Hacer la solicitud fetch y esperar la respuesta
    const response = await fetch(urlAjax, options);

    // Verificar si la respuesta es correcta
    if (!response.ok) {
      throw new Error('Error en la solicitud');
    }

    // Parsear los datos JSON
    const data = await response.json();

    // Obtener la referencia al elemento select
    var selectElement = document.getElementById(elementId);

    // Limpiar el select por si ya tenía opciones anteriores
    selectElement.innerHTML = '';

    // Agregar una opción predeterminada "Selecciona una opción"
    var defaultOption = document.createElement('option');
    defaultOption.text = 'Selecciona una opció:';
    defaultOption.value = ''; // Valor vacío
    selectElement.appendChild(defaultOption);

    // Iterar sobre los datos obtenidos de la API
    data.forEach(function (item) {
      // Crear una opción y agregarla al select
      // console.log(item.ciutat)
      var option = document.createElement('option');
      option.value = item.id; // Establecer el valor de la opción
      option.text = item[valorText]; // Establecer el texto visible de la opción
      selectElement.appendChild(option);
    });

    // Seleccionar automáticamente el valor
    if (idAux) {
      selectElement.value = idAux;
    }
  } catch (error) {
    console.error('Error al parsear JSON:', error); // Muestra el error de parsing
  }
}

function goBack() {
  window.history.back();
}


// Función para obtener el listado de partidos políticos desde la API
const fetchCheckBoxs = async (elementId, apiUrl, nodeElement) => {
  try {
    // Simulamos una llamada a la API
    const devDirectory = `https://${window.location.hostname}`;
    let urlAjax = devDirectory + '/api/auxiliars/get/' + apiUrl;

    const response = await fetch(urlAjax); // Cambia la URL a tu API real
    const data = await response.json(); // Convertimos la respuesta en JSON

    // Generar los checkboxes
    renderCheckboxes(data, elementId, nodeElement);
  } catch (error) {
    console.error('Error al obtener los partidos políticos:', error);
  }
};

// Función para generar los checkboxes dinámicamente
const renderCheckboxes = (data, elementId, nodeElement) => {
  const container = document.getElementById(nodeElement);

  // Limpiar el contenedor antes de agregar nuevos checkboxes
  container.innerHTML = '';

  // Si partitId es una cadena con formato '{10}' o '{1,2,3}', limpiamos y convertimos a array de números
  const selectedPartits = elementId
    .replace(/[{}]/g, '') // Eliminamos los caracteres '{' y '}'
    .split(',') // Dividimos por coma para obtener un array de strings
    .map((id) => parseInt(id, 10)); // Convertimos cada elemento a un número entero

  let checkboxName = '';
  let nomElement = '';
  if (nodeElement === 'partit_politic') {
    checkboxName = 'partido';
    nomElement = 'partit_politic';
  } else if (nodeElement === 'sindicat') {
    checkboxName = 'sindicat';
    nomElement = 'sindicat';
  }

  data.forEach((partido) => {
    // Crear el checkbox
    const checkbox = document.createElement('input');
    checkbox.type = 'checkbox';
    checkbox.id = `${checkboxName}-${partido.id}`;
    checkbox.name = `${checkboxName}`;
    checkbox.value = partido.id; // El valor debe ser el id del partido
    checkbox.className = 'form-check-input me-2'; // Clases de Bootstrap para estilo

    // Verificamos si el partido está seleccionado
    if (selectedPartits.includes(partido.id)) {
      checkbox.checked = true; // Marcamos el checkbox si el partido está en el listado de seleccionados
    }

    // Crear la etiqueta
    const label = document.createElement('label');
    label.htmlFor = `${checkbox.id}`; // Corregimos el id de la etiqueta
    label.textContent = partido[nomElement]; // Nombre del partido
    label.className = 'form-check-label me-4'; // Clases para espaciado

    // Agrupar checkbox y label en un div
    const div = document.createElement('div');
    div.className = 'd-flex align-items-center'; // Clases de alineación
    div.appendChild(checkbox);
    div.appendChild(label);

    // Añadir al contenedor principal
    container.appendChild(div);
  });
};
*/
