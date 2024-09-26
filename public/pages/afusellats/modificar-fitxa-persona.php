<?php
$id = $params['id'];
?>

<div class="container fitxa-persona">
<h2 id="fitxaNomCognoms"></h2>


<div class="tab">
  <button class="tablinks" onclick="openTab(event, 'tab1')">Dades personals</button>
  <button class="tablinks" onclick="openTab(event, 'tab2')">Dades familiars</button>
  <button class="tablinks" onclick="openTab(event, 'tab3')">Dades laborals</button>
  <button class="tablinks" onclick="openTab(event, 'tab4')">Dades polítiques/sindicals</button>
  <button class="tablinks" onclick="openTab(event, 'tab5')">Procés judicial</button>
  <button class="tablinks" onclick="openTab(event, 'tab6')">Biografia/observacions</button>
  <button class="tablinks" onclick="openTab(event, 'tab7')">Dades bibliogràfiques</button>
</div>

<form id="personalForm">
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
                <label for="cognoms" class="form-label negreta">Cognoms:</label>
                <input type="text" class="form-control" id="cognoms" name="cognoms" value="">

                <div class="avis-form">
                * Camp obligatori
              </div>
            </div>

            <div class="col-md-6 negreta">
                <label for="data_naixement" class="form-label">Data de naixement:</label>
                <input type="text" class="form-control" id="data_naixement" name="data_naixement" value="">
            </div>

            <div class="col-md-6">
              <label for="data_execucio" class="form-label negreta">Data de defunció (execució):</label>
              <input type="text" class="form-control" id="data_execucio" name="data_execucio" value="">
            </div>

            <div class="col-md-6">
              <label for="edat" class="form-label negreta">Edat:</label>
              <input type="text" class="form-control" id="edat" name="edat" value="">
            </div>

            <div class="col-md-6 negreta">
                <label for="ciutat_naixement" class="form-label">Ciutat de naixement:</label>
                <select class="form-select" aria-label="Default select example" id="ciutat_naixement">
                </select>
            </div>

            <div class="col-md-6 negreta">
                <label for="ciutat_residencia" class="form-label">Ciutat de residència:</label>
                <select class="form-select" aria-label="Default select example" id="ciutat_residencia">
                </select>
            </div>

            <div class="col-md-6 negreta">
                <label for="adreca" class="form-label">Adreça:</label>
                <input type="text" class="form-control" id="adreca" name="adreca" value="">
            </div>

            <div class="col-md-6 negreta">
                <label for="estudi_cat" class="form-label">Estudis:</label>
                <select class="form-select" aria-label="Default select example" id="estudi_cat">
                </select>
            </div>
            
    </div>
</div>

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
</div>

<div id="tab3" class="tabcontent">
    <div class="row">
        <h3>Dades laborals</h3>

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
</div>

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
</div>

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
</div>

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
</div>

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
</div>

<div class="row espai-superior">
    <div class="col">
        <a class="btn btn-secondary" role="button" aria-disabled="true" onclick="goBack()">Tornar enrere</a>
    </div>

    <div class="col d-flex justify-content-end align-items-center">
        <a class="btn btn-primary" role="button" aria-disabled="true">Modificar dades</a>
    </div>
</div>
</form>

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
document.getElementById("tab1").style.display = "block";
document.getElementsByClassName("tablinks")[0].className += " active";
</script>

<script>
// Carregar tota la informacio des de la base de dades

document.addEventListener('DOMContentLoaded', function() {
    // Llama a la función fitxaPersonaAfusellat para cargar los datos
    fitxaPersonaAfusellat('<?php echo $id; ?>');
});


function fitxaPersonaAfusellat(slug) {
  let urlAjax = devDirectory + "/api/afusellats/get/?type=fitxa&id=" + slug;
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
    
        // DOM modifications
        document.getElementById('fitxaNomCognoms').innerHTML = "Fitxa: " + fitxa.nom + " " + fitxa.cognoms;

        // function auxiliarSelect: idAux, api, elementId (form), valorText
        
        // 01. dades personals
        document.getElementById('nom').value = fitxa.nom;
        document.getElementById('cognoms').value = fitxa.cognoms;
        document.getElementById('data_naixement').value = fitxa.data_naixement;
        auxiliarSelect(fitxa.ciutat_naixement_id, "municipis", "ciutat_naixement", "ciutat");
        auxiliarSelect(fitxa.ciutat_residencia_id, "municipis", "ciutat_residencia", "ciutat");
        document.getElementById('adreca').value = fitxa.adreca;
        auxiliarSelect(fitxa.estudis_id, "estudis", "estudi_cat", "estudi_cat")
        document.getElementById('edat').value = fitxa.edat;
        
        // 02. dades familiars:
        auxiliarSelect(fitxa.estat_civil_id, "estats", "estat_civil", "estat_cat");
        document.getElementById('esposa').value = fitxa.esposa;
        document.getElementById('fills_num').value = fitxa.fills_num;
        document.getElementById('fills_noms').value = fitxa.fills_noms;

        // 03. dades laborals:
        auxiliarSelect(fitxa.ofici_id, "oficis", "ofici_cat", "ofici_cat");
        document.getElementById('empresa').value = fitxa.empresa;

        // 04. dades politiques
        auxiliarSelect(fitxa.partit_politic_id, "partits", "partit_politic", "partit_politic");
        auxiliarSelect(fitxa.sindicat_id, "sindicats", "sindicat", "sindicat");

        // 05. dades proces judicial
        document.getElementById('copia_exp').value = fitxa.copia_exp;
        auxiliarSelect(fitxa.procediment_id, "procediments", "procediment_cat", "procediment_cat");

        document.getElementById('procediment_cat').value = fitxa.procediment_cat;
        document.getElementById('num_causa').value = fitxa.num_causa;
        document.getElementById('data_inici_proces').value = fitxa.data_inici_proces;
        document.getElementById('jutge_instructor').value = fitxa.jutge_instructor;
        document.getElementById('secretari_instructor').value = fitxa.secretari_instructor;
        auxiliarSelect(fitxa.jutjat_id, "jutjats", "jutjat", "jutjat_cat");
        document.getElementById('any_inicial').value =  fitxa.any_inicial;
        document.getElementById('consell_guerra_data').value = fitxa.consell_guerra_data;
        auxiliarSelect(fitxa.ciutat_consellGuerra_id, "municipis", "ciutat_consellGuerra", "ciutat");
        document.getElementById('president_tribunal').value = fitxa.president_tribunal;
        document.getElementById('defensor').value = fitxa.defensor;
        document.getElementById('fiscal').value = fitxa.fiscal;
        document.getElementById('ponent').value = fitxa.ponent;
        document.getElementById('tribunal_vocals').value = fitxa.tribunal_vocals;
        auxiliarSelect(fitxa.acusacio_id, "acusacions", "acusacio", "acusacio_cat");
        auxiliarSelect(fitxa.acusacio_id2, "acusacions", "acusacio_2", "acusacio_cat");
        document.getElementById('testimoni_acusacio').value = fitxa.testimoni_acusacio;
        document.getElementById('sentencia_data').value = fitxa.sentencia_data;
        auxiliarSelect(fitxa.sentencia_id, "sentencies", "sentencia", "sentencia_cat");
        document.getElementById('data_sentencia').value = fitxa.data_sentencia;
        document.getElementById('data_execucio').value = fitxa.data_execucio;
        auxiliarSelect(fitxa.sentencia_id, "sentencies", "sentencia", "sentencia_cat");
        auxiliarSelect(fitxa.espai_id, "espais", "espai", "espai_cat");
        auxiliarSelect(fitxa.ciutat_enterrament_id, "municipis", "ciutat_enterrament", "ciutat");

        // 06. dades biografiques
        document.getElementById('familiars').value = fitxa.familiars;
        document.getElementById('observacions').value = fitxa.observacions;
        document.getElementById('biografia').value =  fitxa.biografia;
        
        // 07. Dades bibliografiques/arxiu
        document.getElementById('ref_num_arxiu').value = fitxa.ref_num_arxiu;
        document.getElementById('font_1').value = fitxa.font_1;
        document.getElementById('font_2').value = fitxa.font_2;

        /* document.getElementById("authorPhoto").src = `../../public/img/library-author/${data.nameImg}.jpg`;*/
      } catch (error) {
        console.error('Error al parsear JSON:', error);  // Muestra el error de parsing
      }
    }
  })
}


// Carregar el select
function auxiliarSelect(idAux, api, elementId, valorText) {
  let urlAjax = devDirectory + "/api/auxiliars/get/?type=" + api;
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
        data.forEach(function (item) {
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
        console.error('Error al parsear JSON:', error);  // Muestra el error de parsing
      }
    }
  })
}

function goBack() {
  window.history.back();
}

</script>

</div>

<?php
# footer
require_once(APP_ROOT . APP_DEV . '/public/php/footer.php');