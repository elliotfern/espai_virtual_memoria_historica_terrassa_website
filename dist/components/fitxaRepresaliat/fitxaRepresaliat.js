var __awaiter = (this && this.__awaiter) || function (thisArg, _arguments, P, generator) {
    function adopt(value) { return value instanceof P ? value : new P(function (resolve) { resolve(value); }); }
    return new (P || (P = Promise))(function (resolve, reject) {
        function fulfilled(value) { try { step(generator.next(value)); } catch (e) { reject(e); } }
        function rejected(value) { try { step(generator["throw"](value)); } catch (e) { reject(e); } }
        function step(result) { result.done ? resolve(result.value) : adopt(result.value).then(fulfilled, rejected); }
        step((generator = generator.apply(thisArg, _arguments || [])).next());
    });
};
import { categorias, devDirectory } from "../../config.js";
import { fetchData } from "../../services/api/api.js";
// BOTO MODIFICAR FITXA PERSONA
export function btnModificaAfusellat(id) {
    const url = `${devDirectory}/afusellats/fitxa/modifica/${id}`;
    window.location.href = url;
}
// Define los bloques de HTML para cada tab
const tabContent = {
    tab1: `
      <h2>Contenido de Tab 1</h2>
      <p>Este es un bloque de HTML complejo para Tab 1.</p>
          <h3>Dades personals</h3>
  <p id="nomComplet"></p>
  <p id="sexe"></p>
  <p id="data_naixement"></p>
  <p id="data_defuncio"></p>
  <p id="edat"></p>
  <p id="ciutat_naixement"></p>
  <p id="ciutat_residencia"></p>
  <p id="ciutat_defuncio"></p>
  <p id="adreca"></p>
  <p id="estudi_cat"></p>
  `,
    tab2: `
    <div>
      <h2>Contenido de Tab 2</h2>
      <p>Aquí hay más información para Tab 2.</p>
      <div style="border: 1px solid black; padding: 10px;">
        <p>Contenido adicional dentro de un div.</p>
      </div>
    </div>
  `,
    tab3: `
    <div>
      <h2>Contenido de Tab 3</h2>
      <p>Este es un bloque diferente para Tab 3.</p>
      <table>
        <tr>
          <th>Columna 1</th>
          <th>Columna 2</th>
        </tr>
        <tr>
          <td>Dato 1</td>
          <td>Dato 2</td>
        </tr>
      </table>
    </div>
  `,
};
// Función para abrir las pestañas
export function openTab(evt, tabName) {
    const tabcontent = document.getElementsByClassName("tabcontent");
    for (let i = 0; i < tabcontent.length; i++) {
        tabcontent[i].style.display = "none";
    }
    const tablinks = document.getElementsByClassName("tablinks");
    for (let i = 0; i < tablinks.length; i++) {
        tablinks[i].className = tablinks[i].className.replace(" active", "");
    }
    const tabElement = document.getElementById(tabName);
    if (tabElement) {
        tabElement.style.display = "block";
        renderTabContent(tabName); // Renderiza el contenido del tab seleccionado
    }
    evt.currentTarget.className += " active";
}
// Función para renderizar el contenido según la pestaña seleccionada
function renderTabContent(tab) {
    const contentDiv = document.getElementById(tab); // Obtiene el div correspondiente a la pestaña
    if (contentDiv) {
        contentDiv.innerHTML = tabContent[tab] || ''; // Establece el contenido del tab
    }
}
// Mostrar la primera pestaña por defecto
export function showDefaultTab() {
    const defaultTab = document.getElementById("tab1");
    const firstTabLink = document.getElementsByClassName("tablinks")[0];
    if (defaultTab)
        defaultTab.style.display = "block";
    if (firstTabLink)
        firstTabLink.className += " active";
}
// Cargar toda la información desde la base de datos
export function fitxaPersonaAfusellat(slug) {
    return __awaiter(this, void 0, void 0, function* () {
        try {
            const url = `${devDirectory}/api/represaliats/get/?type=fitxa&id=${slug}`;
            const data = yield fetchData(url);
            const fitxa = data[0];
            const idPersona = fitxa.id;
            const fitxaCategoria = fitxa.categoria;
            const categoriasSeleccionadas = fitxaCategoria
                .replace("{", "")
                .replace("}", "")
                .split(",");
            const contenedorCategorias = document.getElementById("categorias");
            if (!contenedorCategorias)
                return; // Asegurarse de que el contenedor existe
            categoriasSeleccionadas.forEach((categoria) => {
                const divTab = document.createElement("div");
                divTab.className = "tabInfo";
                const boton = document.createElement("button");
                boton.className = "tablinks";
                boton.innerText = categorias[+categoria] || `Categoria ${categoria}`; // Muestra un texto por defecto si no se encuentra
                const tabName = `tab${categoria}`;
                boton.dataset.tab = tabName;
                boton.onclick = (event) => openTab(event, tabName);
                divTab.appendChild(boton);
                contenedorCategorias.appendChild(divTab);
            });
            // Abre la primera pestaña por defecto
            showDefaultTab();
            // Cargar información adicional al abrir cada pestaña
            categoriasSeleccionadas.forEach((categoria) => {
                mostrarInformacion(categoria, idPersona);
            });
        }
        catch (error) {
            console.error("Error procesando la respuesta de la API:", error);
        }
    });
}
// Mostrar información basada en la categoría seleccionada
function mostrarInformacion(idCategoria, idPersona) {
    return __awaiter(this, void 0, void 0, function* () {
        const categoriaNumerica = parseInt(idCategoria);
        let urlAjax2 = "";
        if (categoriaNumerica === 1) {
            urlAjax2 = `${devDirectory}/api/afusellats/get?type=fitxa&id=${idPersona}`;
        }
        else if (categoriaNumerica === 10) {
            urlAjax2 = `${devDirectory}/api/exiliats/get?type=fitxa&id=${idPersona}`;
        }
        else {
            console.error("Categoria no válida:", categoriaNumerica);
            return;
        }
        const data = yield fetchData(urlAjax2);
        const fitxa2 = data[0];
        const divInfo = document.getElementById("informacion");
        if (!divInfo)
            return; // Verifica si el div existe
        // Limpiar contenido previo antes de agregar nuevo contenido
        divInfo.innerHTML = ''; // Opcional: limpiar contenido previo, si lo deseas
        // Aquí puedes renderizar el contenido específico en el div de cada tab
        if (categoriaNumerica === 1) {
            divInfo.innerHTML += `
      <h3>${categorias[idCategoria]}</h3>
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
        }
        else if (categoriaNumerica === 10) {
            divInfo.innerHTML += `
      <h3>${categorias[idCategoria]}</h3>
      <h5>En elaboració:</h5>
    `;
        }
    });
}
