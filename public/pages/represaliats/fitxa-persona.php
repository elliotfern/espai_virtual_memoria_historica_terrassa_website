<?php
$id = $params['id'];
?>

<script type="module">
    fitxaPersonaAfusellat('<?php echo $id; ?>')
</script>

<h4 id="fitxaNomCognoms"></h4>
<div id="info"> </div>

<hr>

<h6><strong>Col·lecció:</strong></h6>
<div class="tab" id="categorias"></div>

<hr>

<h6><strong>Dades vitals:</strong></h6>

<div class="tab">
  <button class="tablinks" onclick="openTab(event, 'tab1')">Dades personals</button>
  <button class="tablinks" onclick="openTab(event, 'tab2')">Dades familiars</button>
  <button class="tablinks" onclick="openTab(event, 'tab3')">Dades laborals</button>
  <button class="tablinks" onclick="openTab(event, 'tab4')">Dades polítiques/sindicals</button>
  <button class="tablinks" onclick="openTab(event, 'tab6')">Biografia/observacions</button>
  <button class="tablinks" onclick="openTab(event, 'tab7')">Dades bibliogràfiques</button>
</div>

<div class="fitxa-persona">

<div id="tab1" class="tabcontent">
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
</div>

<div id="tab2" class="tabcontent">
  <h3>Dades familiars</h3>
  <p id="estat_civil"></p>
  <p id="esposa"></p>
  <p id="fills_num"></p>
  <p id="fills_noms"></p>
</div>

<div id="tab3" class="tabcontent">
  <h3>Dades laborals</h3>
  <p id="ofici_cat"></p>
  <p id="empresa"></p>
  <p id="carrec"></p>
  <p id="sector"></p>
  <p id="subsector"></p>
</div>

<div id="tab4" class="tabcontent">
  <h3>Dades polítiques/sindicals</h3>
  <p id="partit_politic"></p>
  <p id="sindicat"></p>
</div>

<div id="tab6" class="tabcontent">
  <h3>Biografia/observacions</h3>
  <p id="observacions"></p>
  <p id="familiars"></p>
  <p id="biografia"></p>
</div>

<div id="tab7" class="tabcontent">
  <h3>Dades bibliografiques i d'arxiu</h3>
  <p id="ref_num_arxiu"></p>
  <p id="font_1"></p>
  <p id="font_2"></p>
</div>

<button type="button" onclick="btnModificaAfusellat('<?php echo $id; ?>')" id="btnModificaAfusellat" class="btn btn-sm btn-warning">Modificar dades</button>

</div>

<div id="informacion" class="fitxa-persona" style="margin-top:50px;display:none">

</div>

</div>

<script>

  // BOTO MODIFICAR FITXA PERSONA
function btnModificaAfusellat(id) {
    let idAfusellat = id;
    let url = devDirectory + "/afusellats/fitxa/modifica/" + idAfusellat;

    // Redirigir al usuario a la página deseada
    window.location.href = url;
}

    function openTab(evt, tabName) {
  // Obtén todos los elementos con la clase tabcontent y ocúltalos
  var tabcontent = document.getElementsByClassName("tabcontent");
  for (var i = 0; i < tabcontent.length; i++) {
    tabcontent[i].style.display = "none";
  }

  // Obtén todos los elementos con la clase tablinks y quítales la clase "active"
  var tablinks = document.getElementsByClassName("tablinks");
  for (var i = 0; i < tablinks.length; i++) {
    tablinks[i].className = tablinks[i].className.replace(" active", "");
  }

  // Muestra el div actual y agrega la clase "active" al botón que abrió la pestaña
  document.getElementById(tabName).style.display = "block";
  evt.currentTarget.className += " active";
}

// Mostrar la primera pestaña por defecto
document.getElementById("tab1").style.display = "block";
document.getElementsByClassName("tablinks")[0].className += " active";


// Carregar tota la informacio des de la base de dades
function fitxaPersonaAfusellat(slug) {

  const categorias = {
    1: "Afusellat",
    2: "Exiliat",
    3: "Categoria 3",
    4: "Categoria 4",
    5: "Categoria 5",
    6: "Categoria 6"
  };


  let urlAjax = devDirectory + "/api/represaliats/get/?type=fitxa&id=" + slug;
  $.ajax({
    url: urlAjax,
    method: "GET",
    dataType: "json",
    beforeSend: function (xhr) {
      // Obtener el token del localStorage
      let token = localStorage.getItem('token');

      // Incluir el token en el encabezado de autorización
      xhr.setRequestHeader('Authorization', 'Bearer ' + token);
    },

    success: function (data) {
      try {
        let fitxa = data[0];
        let idPersona = fitxa.id;
        let fitxaCategoria = fitxa.categoria;

        // aqui comença la programacio de les diferents categories
            let categoriasSeleccionadas = fitxaCategoria.replace("{", "").replace("}", "").split(",");

            const contenedorCategorias = document.getElementById('categorias');

            // Función para manejar la apertura de los tabs.
            function openTab(evt, idCategoria) {
              // Ocultar el contenido anterior.
              const tabContent = document.getElementById('informacion');
              tabContent.innerHTML = ''; // Limpiamos el contenido previo.

              // Llamar a la API con el ID de la categoría.
              mostrarInformacion(idCategoria, +idPersona);

              // Remover clase "active" de todos los botones.
              const tablinks = document.getElementsByClassName("tablinks");
              for (let i = 0; i < tablinks.length; i++) {
                tablinks[i].className = tablinks[i].className.replace(" active", "");
              }

              // Añadir la clase "active" al botón clicado.
              evt.currentTarget.className += " active";
            }

            // Recorremos las categorías seleccionadas y generamos los botones.
            categoriasSeleccionadas.forEach(categoria => {
              // Creamos el div con la clase "tab" y el botón con la clase "tablinks".
              let divTab = document.createElement("div");
              divTab.className = "tab";

              let boton = document.createElement("button");
              boton.className = "tablinks";
              boton.innerText = categorias[categoria]; // Asignamos el nombre de la categoría.
              boton.onclick = (event) => openTab(event, categoria); // Asignamos el evento de clic.

              divTab.appendChild(boton); // Añadir el botón al div "tab".
              contenedorCategorias.appendChild(divTab); // Añadir el div con el botón al contenedor.
            });

            // Función para hacer la llamada Ajax y mostrar información (ya mostrada arriba).
            function mostrarInformacion(idCategoria, idPersona) {
              let categoriaNumerica = parseInt(idCategoria);
              let urlAjax2;

              if (categoriaNumerica === 1) {
                let devDirectory = "/api/afusellats/get";
                urlAjax2 = devDirectory + "?type=fitxa&id=" + idPersona;
              }
              
              $.ajax({
                url: urlAjax2,
                method: "GET",
                dataType: "json",
                beforeSend: function (xhr) {
                  let token = localStorage.getItem('token');
                  xhr.setRequestHeader('Authorization', 'Bearer ' + token);
                },
                success: function (data) {
                  try {
                    const fitxa2 = data[0];

                    // 01. Afusellats - dades proces judicial
                    const divInfo = document.getElementById('informacion');
                    divInfo.style.display = 'block';
                    divInfo.innerHTML = `
                      <h3>${categorias[idCategoria]}</h3>
                      <h3>Procés judicial:</h3>
                      <p id="copia_exp"></p>
                      <p id="procediment_cat"><span class='negreta'>Tipus de procediment:</span> ${fitxa2.procediment_cat}</p>
                      <p id="num_causa"><span class='negreta'>Número de causa:</span>  ${fitxa2.num_causa}</p>
                      <p id="data_inici_proces"><span class='negreta'>Data inici del procés judicial:</span>  ${fitxa2.data_inici_proces}</p>
                      <p id="jutge_instructor"><span class='negreta'>Jutge instructor:</span>  ${fitxa2.jutge_instructor}</p>
                      <p id="secretari_instructor"><span class='negreta'>Secretari instructor:</span>  ${fitxa2.secretari_instructor}</p>
                      <p id="jutjat"><span class='negreta'>Jutjat:</span>  ${fitxa2.jutjat}</p>
                      <p id="any_inicial"><span class='negreta'>Any inici del procés:</span>  ${fitxa2.any_inicial}</p>
                      <p id="consell_guerra_data"><span class='negreta'>Data del consell de guerra:</span>  ${fitxa2.consell_guerra_data}</p>
                      <p id="ciutat_consellGuerra"><span class='negreta'>Ciutat del consell de guerra:</span>  ${fitxa2.ciutat_consellGuerra}</p>
                      <p id="president_tribunal"><span class='negreta'>President del tribunal:</span>  ${fitxa2.president_tribunal}</p>
                      <p id="defensor"><span class='negreta'>Advocat defensor:</span>  ${fitxa2.defensor}</p>
                      <p id="fiscal"><span class='negreta'>Fiscal:</span>  ${fitxa2.fiscal}</p>
                      <p id="ponent"><span class='negreta'>Ponent:</span>  ${fitxa2.ponent}</p>
                      <p id="tribunal_vocals"><span class='negreta'>Vocals tribunal:</span>  ${fitxa2.tribunal_vocals}</p>
                      <p id="acusacio"><span class='negreta'>Acusació:</span>  ${fitxa2.acusacio}</p>
                      <p id="acusacio_2"><span class='negreta'>Acusació 2:</span>  ${fitxa2.acusacio_2}</p>
                      <p id="testimoni_acusacio"><span class='negreta'>Testimoni acusació:</span>  ${fitxa2.testimoni_acusacio}</p>
                      <p id="sentencia_data"><span class='negreta'>Data de la sentència:</span>  ${fitxa2.sentencia_data}</p>
                      <p id="sentencia"><span class='negreta'>Sentència:</span>  ${fitxa2.sentencia}</p>
                      <p id="data_sentencia"><span class='negreta'>Data sentència:</span>  ${fitxa2.data_sentencia}</p>
                      <p id="data_execucio"><span class='negreta'>Data de defunció (execució):</span>  ${fitxa2.data_execucio}</p>
                      <p id="ciutat_enterrament"></p>
                      <p id="espai"><span class='negreta'>Lloc execució:</span>  ${fitxa2.espai}</p>
                      `;

                  } catch (error) {
                    console.error("Error procesando la respuesta de la API:", error);
                  }
                },
                error: function (jqXHR, textStatus, errorThrown) {
                  console.error('Error en la llamada a la API:', textStatus, errorThrown);
                }
              });
            }
      
      let dataCreacio = dayjs(fitxa.data_creacio).format('DD-MM-YYYY');
      let dataActualitzacio = dayjs(fitxa.data_actualitzacio).format('DD-MM-YYYY');

      const divInfo2 = document.getElementById('info');
      divInfo2.innerHTML = `
                      <span class='negreta'>Fitxa creada per: </span> ${fitxa.autorNom} (${fitxa.biografia_cat})<br>
                      <span class='negreta'>Data de creació: </span>${dataCreacio}<br>
                      <span class='negreta'>Darrera actualització: </span> ${dataActualitzacio}
                      `;
    
        // DOM modifications
        // 01. dades personals
        document.getElementById('fitxaNomCognoms').innerHTML = "<h4><strong>Fitxa represaliat:</strong> " + fitxa.nom + " " + fitxa.cognom1 + " " + fitxa.cognom2 + "</h4>";
        document.getElementById('nomComplet').innerHTML = "<span class='negreta'>Nom complet:</span> " + fitxa.nom + " " + fitxa.cognom1 + " " + fitxa.cognom2;
        let sexeText = (parseInt(fitxa.sexe, 10) === 1) ? "Home" : (parseInt(fitxa.sexe, 10) === 2) ? "Dona" : "desconegut";
          document.getElementById('sexe').innerHTML = "<span class='negreta'>Sexe:</span> " + sexeText;
        document.getElementById('data_naixement').innerHTML = "<span class='negreta'>Data de naixement:</span> " + fitxa.data_naixement;
        document.getElementById('data_defuncio').innerHTML = "<span class='negreta'>Data de defunció:</span> " + fitxa.data_defuncio;

        
        document.getElementById('edat').innerHTML = "<span class='negreta'>Edat:</span> " + fitxa.edat + " anys";
        document.getElementById('ciutat_naixement').innerHTML = "<span class='negreta'>Ciutat de naixement:</span> " + fitxa.ciutat_naixement + " (" + fitxa.comarca_naixement + ", " + fitxa.provincia_naixement + ", " + fitxa.comunitat_naixement + ", " + fitxa.pais_naixement + ")";
        document.getElementById('ciutat_residencia').innerHTML = "<span class='negreta'>Lloc de residència:</span> " + fitxa.adreca + ", " + fitxa.ciutat_residencia + " (" + fitxa.comarca_residencia + ", " + fitxa.provincia_residencia + ", " + fitxa.comunitat_residencia + ", " + fitxa.pais_residencia + ")";
        document.getElementById('ciutat_defuncio').innerHTML = "<span class='negreta'>Ciutat de defunció:</span> " + fitxa.ciutat_defuncio + " (" + fitxa.comarca_defuncio + ", " + fitxa.provincia_defuncio + ", " + fitxa.comunitat_defuncio + ", " + fitxa.pais_defuncio + ")";
        document.getElementById('estudi_cat').innerHTML = "<span class='negreta'>Estudis:</span> " + fitxa.estudi_cat;
        
        // 02. dades familiars:
        document.getElementById('estat_civil').innerHTML = "<span class='negreta'>Estat civil:</span> " + fitxa.estat_civil;
        document.getElementById('esposa').innerHTML = "<span class='negreta'>Esposa:</span> " + fitxa.esposa;
        document.getElementById('fills_num').innerHTML = "<span class='negreta'>Número de fills:</span> " + fitxa.fills_num;
        document.getElementById('fills_noms').innerHTML = "<span class='negreta'>Noms fills:</span> " + fitxa.fills_noms;

        // 03. dades laborals:
        document.getElementById('ofici_cat').innerHTML = "<span class='negreta'>Ofici:</span> " + fitxa.ofici_cat;
        document.getElementById('empresa').innerHTML = "<span class='negreta'>Empresa:</span> " + fitxa.empresa;
        let carrecText = fitxa.carrec_cat === null ? "Desconegut" : fitxa.carrec_cat;
          document.getElementById('carrec').innerHTML = "<span class='negreta'>Càrrec:</span> " + carrecText;
        document.getElementById('sector').innerHTML = "<span class='negreta'>Sector econòmic:</span> " + fitxa.sector_cat;
        document.getElementById('subsector').innerHTML = "<span class='negreta'>Sub-sector econòmic:</span> " + fitxa.sub_sector_cat;

        // 04. dades politiques
        if (fitxa.partit_politic === null || fitxa.partit_politic === "NULL") {
            document.getElementById('partit_politic').innerHTML = "<span class='negreta'>Afiliació política:</span> - ";
        } else {
            document.getElementById('partit_politic').innerHTML = "<span class='negreta'>Afiliació política:</span> " + fitxa.partit_politic;
        }

        if (fitxa.sindicat === null || fitxa.sindicat === "NULL") {
            document.getElementById('sindicat').innerHTML = "<span class='negreta'>Afiliació sindical:</span> - ";
        } else {
            document.getElementById('sindicat').innerHTML = "<span class='negreta'>Afiliació sindical:</span> " + fitxa.sindicat;
        }

        // 06. dades biografiques
        document.getElementById('familiars').innerHTML = "<span class='negreta'>Familiars:</span> " + fitxa.familiars;
        document.getElementById('observacions').innerHTML = "<span class='negreta'>Observacions:</span> " + fitxa.observacions;
        document.getElementById('biografia').innerHTML = "<span class='negreta'>Biografia:</span> " + fitxa.biografia;
        
        // 07. Dades bibliografiques/arxiu
        /*
        document.getElementById('ref_num_arxiu').innerHTML = "<span class='negreta'>Referència arxiu:</span> " + fitxa.ref_num_arxiu;
        document.getElementById('font_1').innerHTML = "<span class='negreta'>Font 1:</span> " + fitxa.font_1;
        document.getElementById('font_2').innerHTML = "<span class='negreta'>Font 2:</span> " + fitxa.font_2;
        */
       
        /* document.getElementById("authorPhoto").src = `../../public/img/library-author/${data.nameImg}.jpg`;*/
      } catch (error) {
        console.error('Error al parsear JSON:', error);  // Muestra el error de parsing
      }
    }
  })
}

</script>




<?php
# footer
require_once(APP_ROOT . APP_DEV . '/public/php/footer.php');