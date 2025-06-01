<?php
require_once APP_ROOT . '/public/intranet/includes/header.php';

$id = $routeParams[0];
?>

<div class="container" style="margin-bottom:50px;border: 1px solid gray;border-radius: 10px;padding:25px;background-color:#eaeaea">

  <div class="alert alert-success" role="alert" id="okMessage" style="display:none">
    <h4 class="alert-heading"><strong>Modificació correcte!</strong></h4>
    <div id="okText"></div>
  </div>

  <div class="alert alert-danger" role="alert" id="errMessage" style="display:none">
    <h4 class="alert-heading"><strong>Error en les dades!</strong></h4>
    <div id="errText"></div>
  </div>

  <h2 id="fitxaNomCognoms"></h2>


  <div class="tab">
    <button class="tablinks" data-tab="tab1">Categoria repressió</button>
    <button class="tablinks" data-tab="tab2">Dades personals</button>
    <button class="tablinks" data-tab="tab3">Dades familiars</button>
    <button class="tablinks" data-tab="tab4">Dades acadèmiques i laborals</button>
    <button class="tablinks" data-tab="tab5">Dades polítiques i sindicals</button>
    <button class="tablinks" data-tab="tab6">Biografia</button>
    <button class="tablinks" data-tab="tab7">Fonts documentals</button>
    <button class="tablinks" data-tab="tab8">Altres dades</button>
  </div>

  <form id="personalForm">
    <div id="tab1" class="tabcontent">
      <div class="row">
        <h3>Categoria repressió</h3>

        <div class="container">
          <div class="row">

            <div class="avis-form">
              * Has d'escollir almenys 1 categoria.
            </div>

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

                <div class="form-check me-3">
                  <input class="form-check-input" type="checkbox" id="categoria11" name="categoria" value="categoria11">
                  <label class="form-check-label" for="categoria11">
                    Represaliat pendent classificació (llista Ajuntament)
                  </label>
                </div>

                <div class="form-check me-3">
                  <input class="form-check-input" type="checkbox" id="categoria12" name="categoria" value="categoria12">
                  <label class="form-check-label" for="categoria12">
                    Detinguts Presó Model
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
                    Mort o desaparegut en combat
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

            <div class="col-md-12" style="margin-top:20px">
              <h6><strong>Modificació dades repressió:</strong></h6>
              <div id="btnRepressio" class="d-flex flex-wrap"></div>
            </div>

          </div> <!-- Fi bloc row -->
        </div> <!-- Fi bloc container -->

      </div>
    </div> <!-- Fi tab8 categoria repressio -->

    <div id="tab2" class="tabcontent">
      <h3 class="mb-4">Dades personals</h3>
      <div class="row g-4">

        <div class="col-md-4 mb-4">
          <label for="nom" class="form-label negreta">Nom:</label>
          <input type="text" class="form-control" id="nom" name="nom" value="">

          <div class="avis-form">
            * Camp obligatori
          </div>
        </div>

        <div class="col-md-4 mb-4">
          <label for="cognom1" class="form-label negreta">Primer cognom:</label>
          <input type="text" class="form-control" id="cognom1" name="cognom1" value="">

          <div class="avis-form">
            * Camp obligatori
          </div>
        </div>

        <div class="col-md-4 mb-4">
          <label for="cognom2" class="form-label negreta">Segon cognom:</label>
          <input type="text" class="form-control" id="cognom2" name="cognom2" value="">

          <div class="avis-form">
            * Camp obligatori
          </div>
        </div>

        <div class="col-md-4 mb-4">
          <label for="sexe" class="form-label negreta">Gènere:</label>
          <select class="form-select" id="sexe" name="sexe">
            <option selected disabled>Selecciona una opció:</option>
            <option value="1">Home</option>
            <option value="2">Dona</option>
          </select>
        </div>

        <div class="col-md-4 mb-4">
          <label for="data_naixement" class="form-label negreta">Data de naixement:</label>
          <input type="text" class="form-control" id="data_naixement" name="data_naixement" value="">
        </div>

        <div class="col-md-4 mb-4">
          <label for="data_defuncio" class="form-label negreta">Data de defunció:</label>
          <input type="text" class="form-control" id="data_defuncio" name="data_defuncio" value="">
        </div>

        <div class="col-md-4 mb-4">
          <label for="ciutat_naixement" class="form-label negreta">Ciutat de naixement:</label>
          <select class="form-select" name="municipi_naixement" id="municipi_naixement" value="">
          </select>
          <div class="mt-2">
            <a href="<?php echo APP_WEB . APP_INTRANET . $url['auxiliars'] ?>/nou-municipi/" target="_blank" class="btn btn-secondary btn-sm" id="afegirMunicipi1">Afegir municipi</a>
            <button id="refreshButton1" class="btn btn-primary btn-sm">Actualitzar llistat</button>
          </div>

        </div>

        <div class="col-md-4 mb-4">
          <label for="ciutat_defuncio" class="form-label negreta">Lloc de defuncio (ciutat o país):</label>
          <select class="form-select" name="municipi_defuncio" id="municipi_defuncio" value="">
          </select>

          <div class="mt-2">
            <a href="<?php echo APP_WEB . APP_INTRANET . $url['auxiliars'] ?>/nou-municipi" target="_blank" class="btn btn-secondary btn-sm" id="afegirMunicipi2">Afegir municipi</a>
            <button id="refreshButton2" class="btn btn-primary btn-sm">Actualitzar llistat</button>
          </div>
        </div>

        <div class="col-md-4 mb-4">
          <label for="ciutat_residencia" class="form-label negreta">Ciutat de residència abans de la guerra:</label>
          <select class="form-select" name="municipi_residencia" id="municipi_residencia" value="">
          </select>

          <div class="mt-2">
            <a href="<?php echo APP_WEB . APP_INTRANET . $url['auxiliars'] ?>/nou-municipi" target="_blank" class="btn btn-secondary btn-sm" id="afegirMunicipi3">Afegir municipi</a>
            <button id="refreshButton3" class="btn btn-primary btn-sm">Actualitzar llistat</button>
          </div>

        </div>

        <div class="col-md-4 mb-4">
          <label for="adreca" class="form-label negreta">Adreça residència:</label>
          <input type="text" class="form-control" id="adreca" name="adreca" value="">
        </div>

        <div class="col-md-4 mb-4">
          <label for="tipologia_lloc_defuncio" class="form-label negreta">Tipologia lloc de defunció:</label>
          <select class="form-select" id="tipologia_lloc_defuncio" value="" name="tipologia_lloc_defuncio">
          </select>
          <div class="mt-2">
            <a href="<?php echo APP_WEB . APP_INTRANET . $url['auxiliars'] ?>/nova-tipologia-espai" target="_blank" class="btn btn-secondary btn-sm" id="afegirMunicipi4">Afegir tipologia espai</a>
            <button id="refreshButton4" class="btn btn-primary btn-sm">Actualitzar llistat</button>
          </div>

        </div>

        <div class="col-md-4 mb-4">
          <label for="causa_defuncio" class="form-label negreta">Causa de la defunció:</label>
          <select class="form-select" id="causa_defuncio" value="" name="causa_defuncio">
          </select>
          <div class="mt-2">
            <a href="<?php echo APP_WEB . APP_INTRANET . $url['auxiliars'] ?>/nova-causa-mort" target="_blank" class="btn btn-secondary btn-sm" id="afegirMunicipi5">Afegir causa de mort</a>
            <button id="refreshButton5" class="btn btn-primary btn-sm">Actualitzar llistat</button>
          </div>
        </div>

      </div>
    </div> <!-- Fi tab1 -->

    <div id="tab3" class="tabcontent">
      <div class="row">
        <h3>Dades familiars</h3>
        <div class="col-md-4">
          <label for="estat_civil" class="form-label negreta">Estat civil:</label>
          <select class="form-select" id="estat_civil" name="estat_civil" value="">
          </select>
        </div>

        <hr style="margin-top:25px">

        <h4>Establir relacions de parantiu</h4>
        <div id="botonsFamiliars" style="margin-top:5px;margin-bottom:5px"></div>
        <div id="quadreFamiliars"></div>

      </div>
    </div> <!-- Fi tab2 -->

    <div id="tab4" class="tabcontent">
      <div class="row">
        <h3>Dades laborals i acadèmiques</h3>

        <div class="col-md-4">
          <label for="estudis" class="form-label negreta">Estudis:</label>
          <select class="form-select" id="estudis" value="" name="estudis">
          </select>
        </div>

        <div class="col-md-4">
          <label for="ofici" class="form-label negreta">Ofici:</label>
          <select class="form-select" id="ofici" value="" name="ofici">
          </select>
          <div class="mt-2">
            <a href="<?php echo APP_WEB . APP_INTRANET . $url['auxiliars'] ?>/nou-ofici" target="_blank" class="btn btn-secondary btn-sm" id="afegirMunicipi1">Afegir ofici</a>
            <button id="refreshButtonOfici" class="btn btn-primary btn-sm">Actualitzar llistat</button>
          </div>
        </div>

        <div class="col-md-4">
          <label for="empresa" class="form-label negreta">Empresa:</label>
          <input type="text" class="form-control" id="empresa" name="empresa" value="">
        </div>

        <div class="col-md-4">
          <label for="carrec_empresa" class="form-label negreta">Càrrec empresa:</label>
          <select class="form-select" id="carrec_empresa" value="" name="carrec_empresa">
          </select>

          <div class="mt-2">
            <a href="<?php echo APP_WEB . APP_INTRANET . $url['auxiliars'] ?>/nou-carrec-empresa" target="_blank" class="btn btn-secondary btn-sm" id="afegirMunicipi6">Afegir càrrec empresa</a>
            <button id="refreshButtonCarrec" class="btn btn-primary btn-sm">Actualitzar llistat</button>
          </div>
        </div>

        <div class="col-md-4">
          <label for="sector" class="form-label negreta">Sector econòmic:</label>
          <select class="form-select" id="sector" value="" name="sector">
          </select>
        </div>

        <div class="col-md-4">
          <label for="sub_sector" class="form-label negreta">Sub-sector econòmic:</label>
          <select class="form-select" id="sub_sector" value="" name="sub_sector">
          </select>
          <div class="mt-2">
            <a href="<?php echo APP_WEB . APP_INTRANET . $url['auxiliars'] ?>/nou-sub-sector-economic" target="_blank" class="btn btn-secondary btn-sm" id="afegirMunicipi6">Afegir sub-sector econòmic</a>
            <button id="refreshButtonSubSector" class="btn btn-primary btn-sm">Actualitzar llistat</button>
          </div>
        </div>

      </div>
    </div> <!-- Fi tab3 -->

    <div id="tab5" class="tabcontent">
      <div class="row">
        <h3>Dades polítiques/sindicals</h3>

        <div class="container">
          <div class="row">
            <div class="col-md-12" style="margin-top:20px;margin-bottom:20px">
              <h6><strong>Filiació política:</strong></h6>

              <div id="partit_politic" class="d-flex flex-wrap">

              </div>
            </div>
          </div>

          <div class="mt-2">
            <a href="<?php echo APP_WEB . APP_INTRANET . $url['auxiliars'] ?>/nou-partit-politic" target="_blank" class="btn btn-secondary btn-sm" id="afegirMunicipi7">Afegir partit polític</a>
            <button id="refreshButtonPartits" class="btn btn-primary btn-sm">Actualitzar llistat</button>
          </div>
        </div>

        <div class="container">
          <div class="row">
            <div class="col-md-12" style="margin-top:20px;margin-bottom:20px">
              <h6><strong>Filiació sindical:</strong></h6>

              <div id="sindicat" class="d-flex flex-wrap">

              </div>
            </div>
          </div>

          <div class="mt-2">
            <a href="<?php echo APP_WEB . APP_INTRANET . $url['auxiliars'] ?>/nou-sindicat" target="_blank" class="btn btn-secondary btn-sm" id="afegirMunicipi8">Afegir sindicat</a>
            <button id="refreshButtonSindicats" class="btn btn-primary btn-sm">Actualitzar llistat</button>
          </div>

        </div>

        <div class="col-md-12">
          <label for="activitat_durant_guerra" class="form-label negreta">Activitat política/sindical durant la guerra (especificar en format text, opcional):</label>
          <textarea class="form-control" id="activitat_durant_guerra" name="activitat_durant_guerra" value="" rows="3"></textarea>
        </div>

      </div>
    </div> <!-- Fi tab4 -->

    <div id="tab6" class="tabcontent">
      <div class="row">
        <h3>Biografia</h3>
        <hr style="margin-top:25px">
        <h4>Introduir / modificar biografia del represaliat</h4>

        <div class="col-md-4">
          <a href="https://memoriaterrassa.cat/gestio/tots/fitxa/biografia/fitxa/<?php echo $id; ?>" target="_blank" class="btn btn-success">Afegir/modificar biografia</a>
        </div>

      </div>
    </div> <!-- Fi tab5 -->

    <div id="tab7" class="tabcontent">
      <div class="row">
        <h3>Fonts documentals</h3>

        <hr style="margin-top:25px">
        <h4>Modificar llistat de fonts documentals (bibliografia i arxius)</h4>

        <div class="col-md-4">
          <a href="https://memoriaterrassa.cat/gestio/tots/fitxa/fonts-documentals/fitxa/<?php echo $id; ?>" target="_blank" class="btn btn-success">Afegir/veure fonts</a>
        </div>


      </div>
    </div> <!-- Fi tab6 -->

    <div id="tab8" class="tabcontent">
      <div class="row">
        <h3>Altres dades</h3>

        <div class="col-md-12">
          <label for="observacions" class="form-label negreta">Observacions:</label>
          <textarea class="form-control" id="observacions" name="observacions" value="" rows="3"></textarea>
        </div>

        <div class="col-md-4">
          <label for="autor" class="form-label negreta">Autor fitxa:</label>
          <select class="form-select" id="autor" value="" name="autor">
          </select>
        </div>

        <div class="col-md-4">
          <label for="data_creacio" class="form-label negreta">Data de creació de la fitxa:</label>
          <div id="data_creacio"></div>
        </div>

        <div class="col-md-4">
          <label for="data_actualitzacio" class="form-label negreta">Data d'actualització:</label>
          <div id="data_actualitzacio"></div>
        </div>

        <hr style="margin-top:25px">

        <div class="form-group">
          <label for="completat" class="form-label negreta">Estat de la fitxa:</label><br>

          <!-- Botón de opción "Sí" -->
          <div class="custom-control custom-radio custom-control-inline">
            <input type="radio" id="completat_si" name="completat" value="2" class="custom-control-input">
            <label class="custom-control-label" for="completat_si">Completada</label>
          </div>

          <!-- Botón de opción "No" -->
          <div class="custom-control custom-radio custom-control-inline">
            <input type="radio" id="completat_no" name="completat" value="1" class="custom-control-input">
            <label class="custom-control-label" for="completat_no">No completada</label>
          </div>
        </div>

      </div>
    </div> <!-- Fi tab7 -->

    <input type="hidden" name="id" id="id" value="<?php echo $id; ?>">

    <div class="row espai-superior" style="border-top: 1px solid black;padding-top:25px">
      <div class="col">
        <a class="btn btn-secondary" role="button" aria-disabled="true" onclick="goBack()">Cancel·lar els canvis</a>
      </div>

      <div class="col d-flex justify-content-end align-items-center">
        <a class="btn btn-primary" role="button" aria-disabled="true" id="btnModificarDadesPersonals" onclick="enviarFormulario(event)">Modificar dades</a>
      </div>
    </div>
  </form> <!-- Fi Form -->
</div>

<script>
  // Función para manejar el envío del formulario
  async function enviarFormulario(event) {
    event.preventDefault(); // Prevenir que el formulario se envíe por defecto
    // Obtener el formulario
    const form = document.getElementById("personalForm");

    // Crear un objeto para almacenar los datos del formulario
    const formData = {};
    new FormData(form).forEach((value, key) => {
      formData[key] = value; // Agregar cada campo al objeto formData
    });

    // Obtener todos los checkboxes seleccionados de la categoría
    const selectedCategories = [];
    document.querySelectorAll('input[name="categoria"]:checked').forEach((checkbox) => {
      selectedCategories.push(checkbox.value.replace('categoria', ''));
    });

    // Convertir el array de categorías seleccionadas al formato {1,2,3}
    formData['categoria'] = `{${selectedCategories.join(',')}}`;

    // Obtener todos los checkboxes seleccionados del partit
    const selectedPartits = [];
    document.querySelectorAll('input[name="partido"]:checked').forEach((checkbox) => {
      selectedPartits.push(checkbox.value.replace('partido', ''));
    });

    // Convertir el array de categorías seleccionadas al formato {1,2,3}
    formData['filiacio_politica'] = `{${selectedPartits.join(',')}}`;

    // Obtener todos los checkboxes seleccionados del sindicat
    const selectedSindicats = [];
    document.querySelectorAll('input[name="sindicat"]:checked').forEach((checkbox) => {
      selectedSindicats.push(checkbox.value.replace('sindicat', ''));
    });

    // Convertir el array de categorías seleccionadas al formato {1,2,3}
    formData['filiacio_sindical'] = `{${selectedSindicats.join(',')}}`;

    // Obtener el user_id de localStorage
    const userId = localStorage.getItem('user_id');
    if (userId) {
      formData['userId'] = userId;
    }

    // Convertir los datos del formulario a JSON
    const jsonData = JSON.stringify(formData);
    const devDirectory = `https://${window.location.hostname}`;
    let urlAjax = devDirectory + "/api/dades_personals/put";

    try {
      // Hacer la solicitud con fetch y await
      const response = await fetch(urlAjax, {
        method: "PUT",
        headers: {
          "Content-Type": "application/json", // Indicar que se envía JSON
        },
        body: jsonData, // Enviar los datos en formato JSON
      });

      // Procesar la respuesta como texto o JSON
      const data = await response.json();

      // Verificar si la solicitud fue exitosa
      if (response.ok) {
        // Verificar si el status es success
        if (data.status === "success") {
          // Cambiar el display del div con id 'OkMessage' a 'block'
          const okMessageDiv = document.getElementById("okMessage");
          const errMessageDiv = document.getElementById("errMessage");
          const okTextDiv = document.getElementById("okText");

          if (okMessageDiv && okTextDiv && errMessageDiv) {
            okMessageDiv.style.display = "block";
            errMessageDiv.style.display = "none";
            okTextDiv.textContent = data.message || "Les dades s'han actualitzat correctament!";
          }
        } else {
          // Si el status no es success, manejar el error aquí
          const errMessageDiv = document.getElementById("errMessage");
          const okMessageDiv = document.getElementById("okMessage");
          const errTextDiv = document.getElementById("errText");
          if (errMessageDiv && errTextDiv && okMessageDiv) {
            errMessageDiv.style.display = "block";
            okMessageDiv.style.display = "none";
            errTextDiv.innerHTML = data.errors.join('<br>') || "S'ha produit un error a la base de dades.";
          }
        }
      } else {
        // Manejar errores de respuesta del servidor
        const errMessageDiv = document.getElementById("errMessage");
        const errTextDiv = document.getElementById("errText");
        if (errMessageDiv && errTextDiv) {
          errMessageDiv.style.display = "block";
          errTextDiv.innerHTML = data.errors.join('<br>') || "S'ha produit un error a la base de dades.";
        }
      }
    } catch (error) {
      // Manejar errores de red
      const errMessageDiv = document.getElementById("errMessage");
      const errTextDiv = document.getElementById("errText");
      if (errMessageDiv && errTextDiv) {
        errMessageDiv.style.display = "block";
        errTextDiv.innerHTML = error.join('<br>') || "S'ha produit un error a la xarxa.";
      }
      console.error("Error:", error);
    }

  }
</script>