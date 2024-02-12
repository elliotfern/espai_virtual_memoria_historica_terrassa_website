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
            <div class="col-md-6 negreta">
                <label for="nom" class="form-label">Nom:</label>
                <input type="text" class="form-control" id="nom" name="nom" value="">
            </div>

            <div class="col-md-6 negreta">
                <label for="cognoms" class="form-label">Cognoms:</label>
                <input type="text" class="form-control" id="cognoms" name="cognoms" value="">
            </div>

            <div class="col-md-6 negreta">
                <label for="data_naixement" class="form-label">Data de naixement:</label>
                <input type="text" class="form-control" id="data_naixement" name="data_naixement" value="">
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
            <div class="col-md-6 negreta">
                <label for="estat_civil" class="form-label">Estat civil:</label>
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
  <h3>Procés judicial</h3>
  <p id="copia_exp"></p>
  <p id="procediment_cat"></p>
  <p id="num_causa"></p>
  <p id="data_inici_proces"></p>
  <p id="jutge_instructor"></p>
  <p id="secretari_instructor"></p>
  <p id="jutjat"></p>
  <p id="any_inicial"></p>
  <p id="consell_guerra_data"></p>
  <p id="ciutat_consellGuerra"></p>
  <p id="president_tribunal"></p>
  <p id="defensor"></p>
  <p id="fiscal"></p>
  <p id="ponent"></p>
  <p id="tribunal_vocals"></p>
  <p id="acusacio"></p>
  <p id="acusacio_2"></p>
  <p id="testimoni_acusacio"></p>
  <p id="sentencia_data"></p>
  <p id="sentencia"></p>
  <p id="data_sentencia"></p>
  <p id="data_execucio"></p>
  <p id="ciutat_enterrament"></p>
  <p id="espai"></p>
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

<div class="row espai-superior">
    <div class="col">
        <a class="btn btn-secondary" role="button" aria-disabled="true" onclick="goBack()">Tornar enrere</a>
    </div>

    <div class="col d-flex justify-content-end align-items-center">
        <a class="btn btn-primary" role="button" aria-disabled="true">Modificar dades</a>
    </div>
</div>
</form>

<style>

.fitxa-persona {
    background-color: #A0BEF7;
    padding: 15px;
    border: solid black 1px;
}

.tab {
    margin-bottom: 25px;
}
    /* Esconde todos los divs con clase tabcontent excepto el primero */
.tabcontent {
  display: none;
}

/* Estilo de los botones de la pestaña */
.tab button {
  background-color: #f2f2f2;
  border: 1px solid #ccc;
  cursor: pointer;
  padding: 10px 20px;
  transition: background-color 0.3s;
}

/* Cambia el color de fondo del botón activo */
.tab button.active {
  background-color: #B6B6B6;
}

.tab button:hover {
  background-color: #323232;
  color: white;
}
</style>

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
        
        // 01. dades personals
        document.getElementById('nom').value = fitxa.nom;
        document.getElementById('cognoms').value = fitxa.cognoms;
        document.getElementById('data_naixement').value = fitxa.data_naixement;
        auxiliarSelect(fitxa.ciutat_naixement_id, "municipis", "ciutat_naixement", "ciutat");
        auxiliarSelect(fitxa.ciutat_residencia_id, "municipis", "ciutat_residencia", "ciutat");
        document.getElementById('adreca').value = fitxa.adreca;
        auxiliarSelect(fitxa.estudis_id, "estudis", "estudi_cat", "estudi_cat")
        
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
        document.getElementById('copia_exp').innerHTML = "Còpia expedient: " + fitxa.copia_exp;
        document.getElementById('procediment_cat').innerHTML = "Tipus de procediment: " + fitxa.procediment_cat;
        document.getElementById('num_causa').innerHTML = "Número de causa: " + fitxa.num_causa;
        document.getElementById('data_inici_proces').innerHTML = "Data inici del procés judicial: " + fitxa.data_inici_proces;
        document.getElementById('jutge_instructor').innerHTML = "Jutge instructor: " + fitxa.jutge_instructor;
        document.getElementById('secretari_instructor').innerHTML = "Secretari instructor: " + fitxa.secretari_instructor;
        document.getElementById('jutjat').innerHTML = "Jutjat: " + fitxa.jutjat;
        document.getElementById('any_inicial').innerHTML = "Any inici del procés: " + fitxa.any_inicial;
        document.getElementById('consell_guerra_data').innerHTML = "Data del consell de guerra: " + fitxa.consell_guerra_data;
        document.getElementById('ciutat_consellGuerra').innerHTML = "Ciutat del consell de guerra: " + fitxa.ciutat_consellGuerra;
        document.getElementById('president_tribunal').innerHTML = "President del tribunal: " + fitxa.president_tribunal;
        document.getElementById('defensor').innerHTML = "Advocat defensor: " + fitxa.defensor;
        document.getElementById('fiscal').innerHTML = "Fiscal: " + fitxa.fiscal;
        document.getElementById('ponent').innerHTML = "Ponent: " + fitxa.ponent;
        document.getElementById('tribunal_vocals').innerHTML = "Vocals tribunal: " + fitxa.tribunal_vocals;
        document.getElementById('acusacio').innerHTML = "Acusació: " + fitxa.acusacio;
        document.getElementById('acusacio_2').innerHTML = "Acusació 2: " + fitxa.acusacio_2;
        document.getElementById('testimoni_acusacio').innerHTML = "Testimoni acusació: " + fitxa.testimoni_acusacio;
        document.getElementById('sentencia_data').innerHTML = "Data de la sentència: " + fitxa.sentencia_data;
        document.getElementById('sentencia').innerHTML = "Sentència: " + fitxa.sentencia;
        document.getElementById('data_sentencia').innerHTML = "Data sentència: " + fitxa.data_sentencia;
        document.getElementById('data_execucio').innerHTML = "Data execució: " + fitxa.data_execucio;
        document.getElementById('espai').innerHTML = "Lloc execució: " + fitxa.espai;

        if (fitxa.ciutat_enterrament === null || fitxa.ciutat_enterrament === "NULL") {
            document.getElementById('ciutat_enterrament').innerHTML = "Ciutat enterrament: - ";
        } else {
            document.getElementById('ciutat_enterrament').innerHTML = "Ciutat enterrament: " + fitxa.ciutat_enterrament;
        }

        // 06. dades biografiques
        document.getElementById('familiars').innerHTML = "Familiars: " + fitxa.familiars;
        document.getElementById('observacions').innerHTML = "Observacions: " + fitxa.observacions;
        document.getElementById('biografia').innerHTML = "Biografia: " + fitxa.biografia;
        
        // 07. Dades bibliografiques/arxiu
        document.getElementById('ref_num_arxiu').innerHTML = "Referència arxiu: " + fitxa.ref_num_arxiu;
        document.getElementById('font_1').innerHTML = "Font 1: " + fitxa.font_1;
        document.getElementById('font_2').innerHTML = "Font 2: " + fitxa.font_2;

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

<style>
    .negreta {
        margin-top: 20px;
        font-weight: bold;
    }
    
    .espai-superior {
        margin-top: 25px;
    }
    </style>
</div>

<?php
# footer
require_once(APP_ROOT . APP_DEV . '/public/php/footer.php');