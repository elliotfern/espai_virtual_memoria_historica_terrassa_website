<?php
require_once APP_ROOT . '/public/intranet/includes/header.php';

$id = $routeParams[0];
?>


<div class="container" style="margin-bottom:50px">
  <h2 id="fitxaNomCognoms"></h2>


  <div class="tab">
    <button class="tablinks" onclick="openTab(event, 'tab8')">Categoria repressió</button>
    <button class="tablinks" onclick="openTab(event, 'tab1')">Dades personals</button>
    <button class="tablinks" onclick="openTab(event, 'tab2')">Dades familiars</button>
    <button class="tablinks" onclick="openTab(event, 'tab3')">Dades acadèmiques i laborals</button>
    <button class="tablinks" onclick="openTab(event, 'tab4')">Dades polítiques i sindicals</button>
    <button class="tablinks" onclick="openTab(event, 'tab5')">Biografia</button>
    <button class="tablinks" onclick="openTab(event, 'tab6')">Fonts documentals</button>
    <button class="tablinks" onclick="openTab(event, 'tab7')">Altres dades</button>
  </div>

  <form id="personalForm">

    <div id="tab8" class="tabcontent">
      <div class="row">
        <h3>Categoria repressió</h3>

        <div class="container">
          <div class="row">

            <div class="col-md-12" style="margin-top:20px;margin-bottom:20px">
              <h6><strong>Represaliats 1939/1979:</strong></h6>

              <div id="categoria" class="d-flex flex-wrap">
                <div class="form-check me-3">
                  <input class="form-check-input" type="checkbox" id="categoria6" name="categoria" value="categoria6">
                  <label class="form-check-label" for="categoria6">
                    Processat/Empresonat
                  </label>
                </div>

                <div class="form-check me-3">
                  <input class="form-check-input" type="checkbox" id="categoria1" name="categoria" value="categoria1">
                  <label class="form-check-label" for="categoria1">
                    Afusellat
                  </label>
                </div>

                <div class="form-check me-3">
                  <input class="form-check-input" type="checkbox" id="categoria7" name="categoria" value="categoria7">
                  <label class="form-check-label" for="categoria7">
                    Depurat
                  </label>
                </div>

              </div>
            </div> <!-- Fi bloc repressio 1939-79 -->

            <div class="col-md-12" style="margin-bottom:20px">
              <h6><strong>Exili:</strong></h6>

              <div id="categoria" class="d-flex flex-wrap">
                <div class="form-check me-3">
                  <input class="form-check-input" type="checkbox" id="categoria10" name="categoria" value="categoria10">
                  <label class="form-check-label" for="categoria10">
                    Exiliat
                  </label>
                </div>

                <div class="form-check me-3">
                  <input class="form-check-input" type="checkbox" id="categoria2" name="categoria" value="categoria2">
                  <label class="form-check-label" for="categoria2">
                    Deportat
                  </label>
                </div>

              </div>
            </div> <!-- Fi bloc exili -->

            <div class="col-md-12" style="margin-top:20px">
              <h6><strong>Cost humà de la guerra:</strong></h6>

              <div id="categoria" class="d-flex flex-wrap">
                <div class="form-check me-3">
                  <input class="form-check-input" type="checkbox" id="categoria3" name="categoria" value="categoria3">
                  <label class="form-check-label" for="categoria3">
                    Mort o desaparegut al front
                  </label>
                </div>

                <div class="form-check me-3">
                  <input class="form-check-input" type="checkbox" id="categoria4" name="categoria" value="categoria4">
                  <label class="form-check-label" for="categoria4">
                    Mort civil
                  </label>
                </div>

                <div class="form-check me-3">
                  <input class="form-check-input" type="checkbox" id="categoria5" name="categoria" value="categoria5">
                  <label class="form-check-label" for="categoria5">
                    Represàlia republicana
                  </label>
                </div>

              </div>
            </div> <!-- Fi bloc cost huma -->

          </div> <!-- Fi bloc row -->
        </div> <!-- Fi bloc container -->

      </div>
    </div> <!-- Fi tab8 categoria repressio -->

    <div id="tab1" class="tabcontent">
      <div class="row">
        <h3>Dades personals</h3>

        <div class="col-md-6">
          <label for="nom" class="form-label negreta">Nom:</label>
          <input type="text" class="form-control" id="nom" name="nom" value="">

          <div class="avis-form">
            * Camp obligatori
          </div>
        </div>

        <div class="col-md-6">
          <label for="cognom1" class="form-label negreta">Primer cognom:</label>
          <input type="text" class="form-control" id="cognom1" name="cognom1" value="">

          <div class="avis-form">
            * Camp obligatori
          </div>
        </div>

        <div class="col-md-6">
          <label for="cognom2" class="form-label negreta">Segon cognom:</label>
          <input type="text" class="form-control" id="cognom2" name="cognom3" value="">

          <div class="avis-form">
            * Camp obligatori
          </div>
        </div>

        <div class="mb-3">
          <label for="sexe" class="form-label negreta">Gènere:</label>
          <select class="form-select" id="sexe" name="sexe">
            <option selected disabled>Selecciona una opció:</option>
            <option value="1">Home</option>
            <option value="2">Dona</option>
          </select>
        </div>

        <div class="col-md-6 negreta">
          <label for="data_naixement" class="form-label">Data de naixement:</label>
          <input type="text" class="form-control" id="data_naixement" name="data_naixement" value="">
        </div>

        <div class="col-md-6">
          <label for="data_defuncio" class="form-label negreta">Data de defunció:</label>
          <input type="text" class="form-control" id="data_defuncio" name="data_defuncio" value="">
        </div>

        <div class="col-md-6 negreta">
          <label for="ciutat_naixement" class="form-label">Ciutat de naixement:</label>
          <select class="form-select" id="ciutat_naixement" value="">
          </select>
        </div>

        <div class="col-md-6 negreta">
          <label for="ciutat_defuncio" class="form-label">Ciutat de defuncio:</label>
          <select class="form-select" id="ciutat_defuncio" value="">
          </select>
        </div>

        <div class="col-md-6 negreta">
          <label for="ciutat_residencia" class="form-label">Ciutat de residència:</label>
          <select class="form-select" id="ciutat_residencia" value="">
          </select>
        </div>

        <div class="col-md-6 negreta">
          <label for="adreca" class="form-label">Adreça residència:</label>
          <input type="text" class="form-control" id="adreca" name="adreca" value="">
        </div>

        <div class="col-md-6 negreta">
          <label for="tipologia_lloc_defuncio" class="form-label">Tipologia lloc de defunció:</label>
          <select class="form-select" id="tipologia_lloc_defuncio" value="">
          </select>
        </div>

        <div class="col-md-6 negreta">
          <label for="causa_defuncio" class="form-label">Causa de la defunció:</label>
          <select class="form-select" id="causa_defuncio" value="">
          </select>
        </div>

      </div>
    </div> <!-- Fi tab1 -->

    <div id="tab2" class="tabcontent">
      <div class="row">
        <h3>Dades familiars</h3>
        <div class="col-md-6">
          <label for="estat_civil" class="form-label negreta">Estat civil:</label>
          <select class="form-select" aria-label="Default select example" id="estat_civil">
          </select>
        </div>

        <div class="col-md-6 negreta">
          <label for="esposa" class="form-label">Esposa:</label>
          <input type="text" class="form-control" id="esposa" name="esposa" value="">
        </div>

        <div class="col-md-6 negreta">
          <label for="fills_num" class="form-label">Número de fills:</label>
          <input type="text" class="form-control" id="fills_num" name="fills_num" value="">
        </div>

        <div class="col-md-6 negreta">
          <label for="fills_noms" class="form-label">Noms dels fills:</label>
          <input type="text" class="form-control" id="fills_noms" name="fills_noms" value="">
        </div>
      </div>
    </div> <!-- Fi tab2 -->

    <div id="tab3" class="tabcontent">
      <div class="row">
        <h3>Dades laborals i acadèmiques</h3>

        <div class="col-md-6 negreta">
          <label for="estudi_cat" class="form-label">Estudis:</label>
          <select class="form-select" aria-label="Default select example" id="estudi_cat">
          </select>
        </div>

        <div class="col-md-6 negreta">
          <label for="ofici_cat" class="form-label">Ofici:</label>
          <select class="form-select" aria-label="Default select example" id="ofici_cat">
          </select>
        </div>

        <div class="col-md-6 negreta">
          <label for="empresa" class="form-label">Empresa:</label>
          <input type="text" class="form-control" id="empresa" name="empresa" value="">
        </div>
      </div>
    </div> <!-- Fi tab3 -->

    <div id="tab4" class="tabcontent">
      <div class="row">
        <h3>Dades polítiques/sindicals</h3>
        <div class="col-md-6 negreta">
          <label for="partit_politic" class="form-label">Partit polític:</label>
          <select class="form-select" aria-label="Default select example" id="partit_politic">
          </select>
        </div>

        <div class="col-md-6 negreta">
          <label for="sindicat" class="form-label">Sindicat:</label>
          <select class="form-select" aria-label="Default select example" id="sindicat">
          </select>
        </div>

      </div>
    </div> <!-- Fi tab4 -->

    <div id="tab5" class="tabcontent">
      <div class="row">
        <h3>Procés judicial</h3>

        <div class="col-md-6">
          <label for="copia_exp" class="form-label negreta">Còpia expedient:</label>
          <input type="text" class="form-control" id="copia_exp" name="copia_exp" value="">
        </div>

        <div class="col-md-6">
          <label for="procediment_cat" class="form-label negreta">Tipus de procediment:</label>
          <select class="form-select" aria-label="Default select example" id="procediment_cat">
          </select>
        </div>

        <div class="col-md-6">
          <label for="num_causa" class="form-label negreta">Número de causa:</label>
          <input type="text" class="form-control" id="num_causa" name="num_causa" value="">
        </div>

        <div class="col-md-6">
          <label for="data_inici_proces" class="form-label negreta">Data inici del procés judicial:</label>
          <input type="text" class="form-control" id="data_inici_proces" name="data_inici_proces" value="">
        </div>

        <div class="col-md-6">
          <label for="jutjat" class="form-label negreta">Jutjat:</label>
          <select class="form-select" aria-label="Default select example" id="jutjat">
          </select>
        </div>

        <div class="col-md-6">
          <label for="jutge_instructor" class="form-label negreta">Jutge instructor:</label>
          <input type="text" class="form-control" id="jutge_instructor" name="jutge_instructor" value="">
        </div>

        <div class="col-md-6">
          <label for="secretari_instructor" class="form-label negreta">Secretari instructor:</label>
          <input type="text" class="form-control" id="secretari_instructor" name="secretari_instructor" value="">
        </div>

        <div class="col-md-6">
          <label for="any_inicial" class="form-label negreta">Any inici procés:</label>
          <input type="text" class="form-control" id="any_inicial" name="any_inicial" value="">
        </div>

        <div class="col-md-6">
          <label for="consell_guerra_data" class="form-label negreta">Data Consell de guerra:</label>
          <input type="text" class="form-control" id="consell_guerra_data" name="consell_guerra_data" value="">
        </div>

        <div class="col-md-6">
          <label for="ciutat_consellGuerra" class="form-label negreta">Ciutat Consell de guerra:</label>
          <select class="form-select" aria-label="Default select example" id="ciutat_consellGuerra">
          </select>
        </div>

        <div class="col-md-6">
          <label for="president_tribunal" class="form-label negreta">President Tribunal:</label>
          <input type="text" class="form-control" id="president_tribunal" name="president_tribunal" value="">
        </div>

        <div class="col-md-6">
          <label for="defensor" class="form-label negreta">Defensor:</label>
          <input type="text" class="form-control" id="defensor" name="defensor" value="">
        </div>

        <div class="col-md-6">
          <label for="fiscal" class="form-label negreta">Fiscal:</label>
          <input type="text" class="form-control" id="fiscal" name="fiscal" value="">
        </div>

        <div class="col-md-6">
          <label for="ponent" class="form-label negreta">Ponent:</label>
          <input type="text" class="form-control" id="ponent" name="ponent" value="">
        </div>

        <div class="col-md-6">
          <label for="tribunal_vocals" class="form-label negreta">Vocals del tribunal:</label>
          <input type="text" class="form-control" id="tribunal_vocals" name="tribunal_vocals" value="">
        </div>

        <div class="col-md-6">
          <label for="acusacio" class="form-label negreta">Acusació 1:</label>
          <select class="form-select" aria-label="Default select example" id="acusacio">
          </select>
        </div>

        <div class="col-md-6">
          <label for="acusacio_2" class="form-label negreta">Acusació 2:</label>
          <select class="form-select" aria-label="Default select example" id="acusacio_2">
          </select>
        </div>

        <div class="col-md-6">
          <label for="testimoni_acusacio" class="form-label negreta">Testimoni acusació:</label>
          <input type="text" class="form-control" id="testimoni_acusacio" name="testimoni_acusacio" value="">
        </div>

        <div class="col-md-6">
          <label for="sentencia_data" class="form-label negreta">Data sentència:</label>
          <input type="text" class="form-control" id="sentencia_data" name="sentencia_data" value="">
        </div>

        <div class="col-md-6">
          <label for="sentencia" class="form-label negreta">Sentència:</label>
          <select class="form-select" aria-label="Default select example" id="sentencia">
          </select>
        </div>

        <div class="col-md-6">
          <label for="data_sentencia" class="form-label negreta">Any sentència:</label>
          <input type="text" class="form-control" id="data_sentencia" name="data_sentencia" value="">
        </div>

        <div class="col-md-6">
          <label for="ciutat_enterrament" class="form-label negreta">Ciutat enterrament:</label>
          <select class="form-select" aria-label="Default select example" id="ciutat_enterrament">
          </select>
        </div>

        <div class="col-md-6">
          <label for="espai" class="form-label negreta">Lloc d'excecució:</label>
          <select class="form-select" aria-label="Default select example" id="espai">
          </select>
        </div>

      </div>
    </div> <!-- Fi tab5 -->

    <div id="tab6" class="tabcontent">
      <div class="row">
        <h3>Biografia/observacions</h3>
        <div class="col-md-6">
          <label for="observacions" class="form-label negreta">Observacions:</label>
          <input type="text" class="form-control" id="observacions" name="observacions" value="">
        </div>

        <div class="col-md-6">
          <label for="familiars" class="form-label negreta">Familiars:</label>
          <input type="text" class="form-control" id="familiars" name="familiars" value="">
        </div>

        <div class="col-md-12">
          <label for="biografia" class="form-label negreta">Biografia:</label>
          <textarea class="form-control" id="biografia" name="biografia" rows="20"></textarea>
        </div>

      </div>
    </div> <!-- Fi tab6 -->

    <div id="tab7" class="tabcontent">
      <div class="row">
        <h3>Dades bibliografiques i d'arxiu</h3>

        <div class="col-md-6">
          <label for="ref_num_arxiu" class="form-label negreta">Referència número arxiu:</label>
          <input type="text" class="form-control" id="ref_num_arxiu" name="ref_num_arxiu" value="">
        </div>

        <div class="col-md-6">
          <label for="font_1" class="form-label negreta">Font 1:</label>
          <input type="text" class="form-control" id="font_1" name="font_1" value="">
        </div>

        <div class="col-md-6">
          <label for="font_2" class="form-label negreta">Font 2:</label>
          <input type="text" class="form-control" id="font_2" name="font_2" value="">
        </div>

      </div>
    </div> <!-- Fi tab8 -->


    <div class="row espai-superior">
      <div class="col">
        <a class="btn btn-secondary" role="button" aria-disabled="true" onclick="goBack()">Tornar enrere</a>
      </div>

      <div class="col d-flex justify-content-end align-items-center">
        <a class="btn btn-primary" role="button" aria-disabled="true">Modificar dades</a>
      </div>
    </div>
  </form> <!-- Fi Form -->

  <script>
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
    document.getElementById("tab8").style.display = "block";
    document.getElementsByClassName("tablinks")[0].className += " active";
  </script>

  <script>
    // Carregar tota la informacio des de la base de dades

    document.addEventListener('DOMContentLoaded', function() {
      // Llama a la función fitxaPersonaAfusellat para cargar los datos
      fitxaPersonaAfusellat('<?php echo $id; ?>');
    });


    async function fitxaPersonaAfusellat(slug) {
      const devDirectory = `https://${window.location.hostname}`;

      let urlAjax = devDirectory + "/api/represaliats/get/?type=fitxa&id=" + slug;

      // Obtener el token del localStorage
      let token = localStorage.getItem('token');

      // Configurar las opciones de la solicitud
      const options = {
        method: 'GET',
        headers: {
          'Authorization': 'Bearer ' + token,
          'Content-Type': 'application/json'
        }
      };

      try {
        // Hacer la solicitud fetch y esperar la respuesta
        const response = await fetch(urlAjax, options);

        // Verificar si la respuesta es correcta
        if (!response.ok) {
          throw new Error('Error en la solicitud');
        }

        // Parsear los datos JSON
        const fitxa = await response.json();


        // DOM modifications
        document.getElementById('fitxaNomCognoms').innerHTML = "Modificació de la fitxa: " + fitxa[0].nom + " " + fitxa[0].cognom1 + " " + fitxa[0].cognom2;

        // function auxiliarSelect: idAux, api, elementId (form), valorText

        // 08. Categoria repressio

        // Procesa los datos para obtener los valores como un array
        const selectedValues = fitxa[0].categoria.replace(/{|}/g, '').split(',');

        // Selecciona automáticamente los checkboxes correspondientes
        selectedValues.forEach(value => {
          const checkbox = document.querySelector(`input[type="checkbox"][value="categoria${value}"]`);
          if (checkbox) {
            checkbox.checked = true; // Marca el checkbox
          }
        });

        // 01. dades personals
        document.getElementById('nom').value = fitxa[0].nom;
        document.getElementById('cognom1').value = fitxa[0].cognom1;
        document.getElementById('cognom2').value = fitxa[0].cognom2;

        // Selecciona el elemento <select> del DOM
        const selectElement = document.getElementById("sexe");

        // Asigna el valor del select según fitxa[0].sexe
        if (fitxa[0].sexe) {
          selectElement.value = fitxa[0].sexe; // Cambia el valor seleccionado automáticamente
        }

        document.getElementById('data_naixement').value = fitxa[0].data_naixement;
        document.getElementById('data_defuncio').value = fitxa[0].data_defuncio;

        auxiliarSelect(fitxa[0].ciutat_naixement_id, "municipis", "ciutat_naixement", "ciutat");
        auxiliarSelect(fitxa[0].ciutat_defuncio_id, "municipis", "ciutat_defuncio", "ciutat");
        auxiliarSelect(fitxa[0].ciutat_residencia_id, "municipis", "ciutat_residencia", "ciutat");
        document.getElementById('adreca').value = fitxa[0].adreca;

        auxiliarSelect(fitxa[0].tipologia_lloc_defuncio_id, "tipologia_espais", "tipologia_lloc_defuncio", "tipologia_espai_ca");

        auxiliarSelect(fitxa[0].causa_defuncio_id, "causa_defuncio", "causa_defuncio", "causa_defuncio_ca");

        // 02. dades familiars:
        auxiliarSelect(fitxa.estat_civil_id, "estats", "estat_civil", "estat_cat");
        document.getElementById('esposa').value = fitxa.esposa;
        document.getElementById('fills_num').value = fitxa.fills_num;
        document.getElementById('fills_noms').value = fitxa.fills_noms;

        // 03. dades laborals i academiques:
        auxiliarSelect(fitxa.ofici_id, "oficis", "ofici_cat", "ofici_cat");
        document.getElementById('empresa').value = fitxa.empresa;
        auxiliarSelect(fitxa[0].estudis_id, "estudis", "estudi_cat", "estudi_cat")

        // 04. dades politiques
        auxiliarSelect(fitxa.partit_politic_id, "partits", "partit_politic", "partit_politic");
        auxiliarSelect(fitxa.sindicat_id, "sindicats", "sindicat", "sindicat");

        // 05. dades proces judicial

        // 06. dades biografiques
        document.getElementById('familiars').value = fitxa.familiars;
        document.getElementById('observacions').value = fitxa.observacions;
        document.getElementById('biografia').value = fitxa.biografia;

        // 07. Dades bibliografiques/arxiu
        document.getElementById('ref_num_arxiu').value = fitxa.ref_num_arxiu;
        document.getElementById('font_1').value = fitxa.font_1;
        document.getElementById('font_2').value = fitxa.font_2;

        /* document.getElementById("authorPhoto").src = `../../public/img/library-author/${data.nameImg}.jpg`;*/
      } catch (error) {
        console.error('Error al parsear JSON:', error); // Muestra el error de parsing
      }
    }


    // Carregar el select
    async function auxiliarSelect(idAux, api, elementId, valorText) {

      const devDirectory = `https://${window.location.hostname}`;
      let urlAjax = devDirectory + "/api/auxiliars/get/?type=" + api;

      // Obtener el token del localStorage
      let token = localStorage.getItem('token');

      // Configurar las opciones de la solicitud
      const options = {
        method: 'GET',
        headers: {
          'Authorization': 'Bearer ' + token,
          'Content-Type': 'application/json'
        }
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
        selectElement.innerHTML = "";

        // Agregar una opción predeterminada "Selecciona una opción"
        var defaultOption = document.createElement("option");
        defaultOption.text = "Selecciona una opció:";
        defaultOption.value = ""; // Valor vacío
        selectElement.appendChild(defaultOption);

        // Iterar sobre los datos obtenidos de la API
        data.forEach(function(item) {
          // Crear una opción y agregarla al select
          // console.log(item.ciutat)
          var option = document.createElement("option");
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
  </script>

</div>