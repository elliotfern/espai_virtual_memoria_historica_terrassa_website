import {
  devDirectory,
  categorias,
  convertirFecha,
  calcularEdadAlMorir,
} from "../../config.js";
import { fetchData } from "../../services/api/api.js";
const dayjs = window.dayjs;

export function initButtons(id: string): void {
  const contenedorBotones = document.getElementById("botons1");
  if (!contenedorBotones) return; // Asegurarse de que el contenedor de botones existe

  const buttons = [
    { label: "Dades personals", category: "tab1" },
    { label: "Dades familiars", category: "tab2" },
    { label: "Dades laborals", category: "tab3" },
    { label: "Dades polítiques i sindicals", category: "tab4" },
    { label: "Altres dades", category: "tab5" },
    { label: "Biografia/observacions", category: "tab6" },
    { label: "Dades bibliogràfiques", category: "tab7" },
  ];

  buttons.forEach((button) => {
    const btn = document.createElement("button");
    btn.className = "tablinks";
    btn.innerText = button.label;
    btn.dataset.tab = button.category;

    btn.onclick = () => {
      // Eliminar la clase 'active' de todos los botones
      const allButtons = contenedorBotones.getElementsByClassName("tablinks");
      Array.from(allButtons).forEach((b) => b.classList.remove("active"));

      // Agregar la clase 'active' al botón actual
      btn.classList.add("active");

      // Mostrar información correspondiente
      mostrarInformacion(button.category, id); // Pasar el ID de la persona
    };

    contenedorBotones.appendChild(btn);
  });

  // Cargar automáticamente el tab1 y el div de info al iniciar
  mostrarInformacion("tab1", id);

  // Generar botones de categorías dinámicamente
  generarBotonesCategoria(id);
}

// Función para generar los botones según la categoría obtenida de la API
async function generarBotonesCategoria(idPersona: string): Promise<void> {
  const url = `${devDirectory}/api/represaliats/get/?type=fitxa&id=${idPersona}`; // URL para obtener la información de la ficha

  try {
    const data = await fetchData(url); // Llamada a la API para obtener la ficha
    const fitxa = data[0];
    const categoriasNumericas = fitxa.categoria.replace(/[{}]/g, "").split(","); // Obtener las categorías en formato de array

    const contenedorCategorias = document.getElementById("botons2");
    if (!contenedorCategorias) return;

    // Iterar sobre las categorías numéricas y crear botones dinámicamente
    categoriasNumericas.forEach((catNum: string) => {
      const catTitle = categorias[catNum]; // Obtener el título de la categoría desde la constante

      if (catTitle) {
        // Solo crear botón si la categoría tiene un título definido
        const btn = document.createElement("button");
        btn.className = "tablinks";
        btn.innerText = catTitle;
        btn.dataset.tab = `categoria${catNum}`;

        // Asignar la función que mostrará información al hacer clic en el botón
        btn.onclick = () => {
          const divInfo = document.getElementById("fitxa-categoria");
          if (!divInfo) return; // Verifica si el div existe

          // Si el contenido ya está visible, ocultarlo y eliminar la clase 'active'
          if (
            divInfo.style.display === "block" &&
            divInfo.dataset.categoria === String(catNum)
          ) {
            divInfo.style.display = "none";
            btn.classList.remove("active");
          } else {
            // Limpiar el contenido previo y actualizar el dataset
            divInfo.innerHTML = "";
            divInfo.dataset.categoria = String(catNum);

            // Eliminar la clase 'active' de todos los botones
            const allButtons =
              contenedorCategorias.getElementsByClassName("tablinks");
            Array.from(allButtons).forEach((b) => b.classList.remove("active"));

            // Agregar la clase 'active' al botón actual
            btn.classList.add("active");

            // Mostrar información de la categoría
            mostrarCategoria(catNum, idPersona);
            divInfo.style.display = "block"; // Asegúrate de mostrar el div
          }
        };

        // Agregar el botón al contenedor de categorías
        contenedorCategorias.appendChild(btn);
      }
    });
  } catch (error) {
    console.error("Error al generar botones de categoría:", error);
  }
}

// Función para mostrar la información de la categoría (lógica aún no definida completamente)
async function mostrarCategoria(
  categoriaNumerica: string,
  idPersona: string
): Promise<void> {
  const divInfo = document.getElementById("fitxa-categoria");
  if (!divInfo) return; // Verifica si el div existe

  // Si el contenido ya está visible, lo ocultamos
  if (
    divInfo.style.display === "block" &&
    divInfo.dataset.categoria === String(categoriaNumerica)
  ) {
    divInfo.style.display = "none";
    return;
  }

  // Limpiar contenido previo y actualizar el dataset
  divInfo.innerHTML = "";
  divInfo.dataset.categoria = String(categoriaNumerica);

  let urlAjax2 = "";

  // Definir la URL de la API dependiendo de la categoría
  if (parseInt(categoriaNumerica) === 1) {
    urlAjax2 = `${devDirectory}/api/afusellats/get?type=fitxa&id=${idPersona}`;
  } else if (parseInt(categoriaNumerica) === 10) {
    urlAjax2 = `${devDirectory}/api/exiliats/get?type=fitxa&id=${idPersona}`;
  } else {
    console.error("Categoria no válida:", categoriaNumerica);
    return;
  }

  try {
    // Hacer la llamada a la API y esperar la respuesta
    const data = await fetchData(urlAjax2);
    const fitxa2 = data[0];

    const divInfo = document.getElementById("fitxa-categoria");
    if (!divInfo) return;

    // Limpiar contenido previo
    divInfo.innerHTML = "";

    // Mostrar el div en caso de estar oculto
    divInfo.style.display = "block";

    // Mostrar la información dependiendo de la categoría
    if (parseInt(categoriaNumerica) === 1) {
      divInfo.innerHTML += `
        <h3>${categorias[categoriaNumerica]}</h3>
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
    } else if (parseInt(categoriaNumerica) === 10) {
      divInfo.innerHTML += `
        <h3>${categorias[categoriaNumerica]}</h3>
        <h5>En elaboració:</h5>
      `;
    }
  } catch (error) {
    console.error("Error al obtener la información de la categoría:", error);
  }
}

// Función para mostrar la información según el tab
async function mostrarInformacion(
  tab: string,
  idPersona: string
): Promise<void> {
  const url = `${devDirectory}/api/represaliats/get/?type=fitxa&id=${idPersona}`; // URL para obtener la información
  try {
    const data = await fetchData(url);
    const fitxa = data[0]; // Suponiendo que la respuesta es un array

    const divInfo = document.getElementById("fitxa");
    const divAdditionalInfo = document.getElementById("info");
    if (!divInfo || !divAdditionalInfo) return;

    // Limpiar el contenido anterior de fitxa
    divInfo.innerHTML = "";

    const sexeText =
      parseInt(fitxa.sexe, 10) === 1
        ? "Home"
        : parseInt(fitxa.sexe, 10) === 2
        ? "Dona"
        : "desconegut";

    const fechaNacimiento = convertirFecha(fitxa.data_naixement);
    const fechaDefuncion = convertirFecha(fitxa.data_defuncio);

    let edatAlMorir = "";
    if (fechaNacimiento && fechaDefuncion) {
      const edat = calcularEdadAlMorir(fechaNacimiento, fechaDefuncion);
      if (edat !== null) {
        edatAlMorir = `${edat} anys`;
      }
    }

    const carrecText =
      fitxa.carrec_cat === null ? "Desconegut" : fitxa.carrec_cat;
    const partitPolitic =
      fitxa.partit_politic === null ? "Desconegut" : fitxa.partit_politic;
    const sindicat = fitxa.sindicat === null ? "Desconegut" : fitxa.sindicat;

    // Dependiendo del tab, generar el contenido
    switch (tab) {
      case "tab1":
        divInfo.innerHTML = `
        <h3>Dades personals</h3>
        <p><span class='negreta'>Nom complet:</span> ${fitxa.nom} ${fitxa.cognom1} ${fitxa.cognom2}</p>
        <p><span class='negreta'>Sexe:</span> ${sexeText}</p>
        <p><span class='negreta'>Data de naixement:</span> ${fitxa.data_naixement}</p>
        <p><span class='negreta'>Data de defunció:</span> ${fitxa.data_defuncio}</p>
        <p><span class='negreta'>Edat:</span> ${edatAlMorir}</p>
        <p><span class='negreta'>Ciutat de naixement:</span> ${fitxa.ciutat_naixement} (${fitxa.comarca_naixement}, ${fitxa.provincia_naixement}, ${fitxa.comunitat_naixement}, ${fitxa.pais_naixement})</p>
        <p><span class='negreta'>Lloc de residència:</span> ${fitxa.adreca}, ${fitxa.ciutat_residencia} (${fitxa.comarca_residencia}, ${fitxa.provincia_residencia} ${fitxa.comunitat_residencia}, ${fitxa.pais_residencia})</p>
        <p><span class='negreta'>Ciutat de defunció:</span> ${fitxa.ciutat_defuncio} (${fitxa.comarca_defuncio}, ${fitxa.provincia_defuncio}, ${fitxa.comunitat_defuncio}, ${fitxa.pais_defuncio})</p>
        <p><span class='negreta'>Estudis:</span> ${fitxa.estudi_cat}</p>
        `;
        break;
      case "tab2":
        divInfo.innerHTML = `
        <h3>Dades familiars</h3>
        <p><span class='negreta'>Estat civil:</span> ${fitxa.estat_civil}</p>
        <p><span class='negreta'>Esposa:</span> ${fitxa.esposa}</p>
        <p><span class='negreta'>Número de fills:</span> ${fitxa.fills_num}</p>
        <p><span class='negreta'>Noms fills:</span> ${fitxa.fills_noms}</p>
        `;
        break;
      case "tab3":
        divInfo.innerHTML = `
        <h3>Dades laborals</h3>
        <p><span class='negreta'>Ofici:</span> ${fitxa.ofici_cat}</p>
        <p><span class='negreta'>Empresa:</span> ${fitxa.empresa}</p>
        <p><span class='negreta'>Càrrec:</span> ${carrecText}</p>
        <p><span class='negreta'>Sector econòmic:</span> ${fitxa.sector_cat}</p>
        <p><span class='negreta'>Sub-sector econòmic:</span> ${fitxa.sub_sector_cat}</p>
        `;
        break;
      case "tab4":
        divInfo.innerHTML = `
          <h3>Dades polítiques i sindicals</h3>
          <p><span class='negreta'>Afiliació política:</span> ${partitPolitic}</p>
          <p><span class='negreta'>Afiliació sindical:</span> ${sindicat}</p>
          `;
        break;
      case "tab5":
        divInfo.innerHTML = `
        <h3>Dades</h3>
        `;

        break;
      case "tab6":
        divInfo.innerHTML = `
        <h3>Biografia</h3>
        <p><span class='negreta'>Observacions:</span> ${fitxa.observacions}</p>
        <p><span class='negreta'>Biografia:</span> ${fitxa.biografia}</p>
        `;

        break;
      case "tab7":
        divInfo.innerHTML = `
        <h3>Fonts documentals</h3>
        <p><span class='negreta'>Referència arxiu:</span> ${fitxa.ref_num_arxiu}</p>
        <p><span class='negreta'>Font 1:</span> ${fitxa.font_1}</p>
        <p><span class='negreta'>Font 2:</span> ${fitxa.font_2}</p>
        `;

        break;
      default:
        divInfo.innerHTML = `<p>No hay información disponible para esta categoría.</p>`;
    }

    const dataCreacio = dayjs(fitxa.data_creacio).format("DD-MM-YYYY");
    const dataActualitzacio = dayjs(fitxa.data_actualitzacio).format(
      "DD-MM-YYYY"
    );

    // Aquí puedes mantener el contenido de divAdditionalInfo si es necesario
    divAdditionalInfo.innerHTML = `
        <h4><strong>Fitxa represaliat:</strong> ${fitxa.nom} ${fitxa.cognom1} ${fitxa.cognom2}</h4>
        <span class='negreta'>Fitxa creada per: </span> ${fitxa.autorNom} (${fitxa.biografia_cat})<br>
        <span class='negreta'>Data de creació: </span>${dataCreacio}<br>
        <span class='negreta'>Darrera actualització: </span> ${dataActualitzacio}
      `; // No se limpia el contenido
  } catch (error) {
    console.error("Error al obtener la información:", error);
  }
}
