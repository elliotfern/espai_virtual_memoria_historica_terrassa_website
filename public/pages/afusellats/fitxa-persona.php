<?php
$id = $params['id'];
?>

<script type="module">
    fitxaPersonaAfusellat('<?php echo $id; ?>')
</script>

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

<div id="tab1" class="tabcontent">
  <h3>Dades personals</h3>
  <p id="data_naixement"></p>
  <p id="data_execucio"></p>
  <p id="ciutat_naixement"></p>
  <p id="ciutat_residencia"></p>
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
</div>

<div id="tab4" class="tabcontent">
  <h3>Dades polítiques/sindicals</h3>
  <p id="partit_politic"></p>
  <p id="sindicat"></p>
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
        // 01. dades personals
        document.getElementById('fitxaNomCognoms').innerHTML = "Fitxa: " + fitxa.nom + " " + fitxa.cognoms;
        document.getElementById('data_naixement').innerHTML = "Data de naixement: " + fitxa.data_naixement;
        document.getElementById('ciutat_naixement').innerHTML = "Ciutat de naixement: " + fitxa.ciutat_naixement;
        document.getElementById('ciutat_residencia').innerHTML = "Ciutat de residència: " + fitxa.ciutat_residencia;
        document.getElementById('adreca').innerHTML = "Adreça: " + fitxa.adreca;
        document.getElementById('estudi_cat').innerHTML = "Estudis: " + fitxa.estudi_cat;
        
        // 02. dades familiars:
        document.getElementById('estat_civil').innerHTML = "Estat civil: " + fitxa.estat_civil;
        document.getElementById('esposa').innerHTML = "Esposa: " + fitxa.esposa;
        document.getElementById('fills_num').innerHTML = "Número de fills: " + fitxa.fills_num;
        document.getElementById('fills_noms').innerHTML = "Noms fills: " + fitxa.fills_noms;

        // 03. dades laborals:
        document.getElementById('ofici_cat').innerHTML = "Ofici: " + fitxa.ofici_cat;
        document.getElementById('empresa').innerHTML = "Empresa: " + fitxa.empresa;

        // 04. dades politiques
        if (fitxa.partit_politic === null || fitxa.partit_politic === "NULL") {
            document.getElementById('partit_politic').innerHTML = "Afiliació política: - ";
        } else {
            document.getElementById('partit_politic').innerHTML = "Afiliació política: " + fitxa.partit_politic;
        }

        if (fitxa.sindicat === null || fitxa.sindicat === "NULL") {
            document.getElementById('sindicat').innerHTML = "Afiliació sindical: - ";
        } else {
            document.getElementById('sindicat').innerHTML = "Afiliació sindical: " + fitxa.sindicat;
        }

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

</script>


</div>

<?php
# footer
require_once(APP_ROOT . APP_DEV . '/public/php/footer.php');