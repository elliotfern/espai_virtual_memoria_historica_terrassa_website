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
  <p id="edat"></p>
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

<button type="button" onclick="btnModificaAfusellat('<?php echo $id; ?>')" id="btnModificaAfusellat" class="btn btn-sm btn-warning">Modificar dades</button>

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
        document.getElementById('data_naixement').innerHTML = "<span class='negreta'>Data de naixement:</span> " + fitxa.data_naixement;
        document.getElementById('edat').innerHTML = "<span class='negreta'>Edat:</span> " + fitxa.edat + " anys";
        document.getElementById('ciutat_naixement').innerHTML = "<span class='negreta'>Ciutat de naixement:</span> " + fitxa.ciutat_naixement;
        document.getElementById('ciutat_residencia').innerHTML = "<span class='negreta'>Ciutat de residència:</span> " + fitxa.ciutat_residencia;
        document.getElementById('adreca').innerHTML = "<span class='negreta'>Adreça:</span> " + fitxa.adreca;
        document.getElementById('estudi_cat').innerHTML = "<span class='negreta'>Estudis:</span> " + fitxa.estudi_cat;
        
        // 02. dades familiars:
        document.getElementById('estat_civil').innerHTML = "<span class='negreta'>Estat civil:</span> " + fitxa.estat_civil;
        document.getElementById('esposa').innerHTML = "<span class='negreta'>Esposa:</span> " + fitxa.esposa;
        document.getElementById('fills_num').innerHTML = "<span class='negreta'>Número de fills:</span> " + fitxa.fills_num;
        document.getElementById('fills_noms').innerHTML = "<span class='negreta'>Noms fills:</span> " + fitxa.fills_noms;

        // 03. dades laborals:
        document.getElementById('ofici_cat').innerHTML = "<span class='negreta'>Ofici:</span> " + fitxa.ofici_cat;
        document.getElementById('empresa').innerHTML = "<span class='negreta'>Empresa:</span> " + fitxa.empresa;

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

        // 05. dades proces judicial
        document.getElementById('copia_exp').innerHTML = "<span class='negreta'>Còpia expedient:</span> " + fitxa.copia_exp;
        document.getElementById('procediment_cat').innerHTML = "<span class='negreta'>Tipus de procediment:</span> " + fitxa.procediment_cat;
        document.getElementById('num_causa').innerHTML = "<span class='negreta'>Número de causa:</span> " + fitxa.num_causa;
        document.getElementById('data_inici_proces').innerHTML = "<span class='negreta'>Data inici del procés judicial:</span> " + fitxa.data_inici_proces;
        document.getElementById('jutge_instructor').innerHTML = "<span class='negreta'>Jutge instructor:</span> " + fitxa.jutge_instructor;
        document.getElementById('secretari_instructor').innerHTML = "<span class='negreta'>Secretari instructor:</span> " + fitxa.secretari_instructor;
        document.getElementById('jutjat').innerHTML = "<span class='negreta'>Jutjat:</span> " + fitxa.jutjat;
        document.getElementById('any_inicial').innerHTML = "<span class='negreta'>Any inici del procés:</span> " + fitxa.any_inicial;
        document.getElementById('consell_guerra_data').innerHTML = "<span class='negreta'>Data del consell de guerra:</span> " + fitxa.consell_guerra_data;
        document.getElementById('ciutat_consellGuerra').innerHTML = "<span class='negreta'>Ciutat del consell de guerra:</span> " + fitxa.ciutat_consellGuerra;
        document.getElementById('president_tribunal').innerHTML = "<span class='negreta'>President del tribunal:</span> " + fitxa.president_tribunal;
        document.getElementById('defensor').innerHTML = "<span class='negreta'>Advocat defensor:</span> " + fitxa.defensor;
        document.getElementById('fiscal').innerHTML = "<span class='negreta'>Fiscal:</span> " + fitxa.fiscal;
        document.getElementById('ponent').innerHTML = "<span class='negreta'>Ponent:</span> " + fitxa.ponent;
        document.getElementById('tribunal_vocals').innerHTML = "<span class='negreta'>Vocals tribunal:</span> " + fitxa.tribunal_vocals;
        document.getElementById('acusacio').innerHTML = "<span class='negreta'>Acusació:</span> " + fitxa.acusacio;
        document.getElementById('acusacio_2').innerHTML = "<span class='negreta'>Acusació 2:</span> " + fitxa.acusacio_2;
        document.getElementById('testimoni_acusacio').innerHTML = "<span class='negreta'>Testimoni acusació:</span> " + fitxa.testimoni_acusacio;
        document.getElementById('sentencia_data').innerHTML = "<span class='negreta'>Data de la sentència:</span> " + fitxa.sentencia_data;
        document.getElementById('sentencia').innerHTML = "<span class='negreta'>Sentència:</span> " + fitxa.sentencia;
        document.getElementById('data_sentencia').innerHTML = "<span class='negreta'>Data sentència:</span> " + fitxa.data_sentencia;
        document.getElementById('data_execucio').innerHTML = "<span class='negreta'>Data de defunció (execució):</span> " + fitxa.data_execucio;
        document.getElementById('espai').innerHTML = "<span class='negreta'>Lloc execució:</span> " + fitxa.espai;

        if (fitxa.ciutat_enterrament === null || fitxa.ciutat_enterrament === "NULL") {
            document.getElementById('ciutat_enterrament').innerHTML = "<span class='negreta'>Ciutat enterrament:</span> - ";
        } else {
            document.getElementById('ciutat_enterrament').innerHTML = "<span class='negreta'>Ciutat enterrament:</span> " + fitxa.ciutat_enterrament;
        }

        // 06. dades biografiques
        document.getElementById('familiars').innerHTML = "<span class='negreta'>Familiars:</span> " + fitxa.familiars;
        document.getElementById('observacions').innerHTML = "<span class='negreta'>Observacions:</span> " + fitxa.observacions;
        document.getElementById('biografia').innerHTML = "<span class='negreta'>Biografia:</span> " + fitxa.biografia;
        
        // 07. Dades bibliografiques/arxiu
        document.getElementById('ref_num_arxiu').innerHTML = "<span class='negreta'>Referència arxiu:</span> " + fitxa.ref_num_arxiu;
        document.getElementById('font_1').innerHTML = "<span class='negreta'>Font 1:</span> " + fitxa.font_1;
        document.getElementById('font_2').innerHTML = "<span class='negreta'>Font 2:</span> " + fitxa.font_2;

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