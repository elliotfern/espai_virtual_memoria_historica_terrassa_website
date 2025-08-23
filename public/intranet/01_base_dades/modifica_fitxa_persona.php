<?php
require_once APP_ROOT . '/public/intranet/includes/header.php';

// Obtener la URL completa
$url2 = $_SERVER['REQUEST_URI'];

// Dividir la URL en partes usando '/' como delimitador
$urlParts = explode('/', $url2);

// Obtener la parte deseada (en este caso, la cuarta parte)
$pagina = $urlParts[3] ?? '';

$modificaBtn = "";

if ($pagina === "modifica-fitxa") {
  $modificaBtn = 1;
} else {
  $modificaBtn = 2;
}
?>

<div class="container" style="margin-bottom:50px;border: 1px solid gray;border-radius: 10px;padding:25px;background-color:#eaeaea">

  <div class="alert alert-success" role="alert" id="okMessage" style="display:none">
    <div id="okText"></div>
  </div>

  <div class="alert alert-danger" role="alert" id="errMessage" style="display:none">

    <div id="errText"></div>
  </div>

  <?php if ($modificaBtn === 1) { ?>
    <h2 id="fitxaNomCognoms"></h2>
  <?php } else { ?>
    <h2>Creació de fitxa repressaliat</h2>

    <?php if (!$isAdmin): ?>

      <p>omés poden crear noves fitxes els usuaris administradors.</p>

    <?php endif; ?>

  <?php } ?>

  <div class="tab">
    <button class="tablinks" data-tab="tab1">Categoria repressió</button>
    <button class="tablinks" data-tab="tab2">Dades personals</button>
    <button class="tablinks" data-tab="tab3">Dades familiars</button>
    <button class="tablinks" data-tab="tab4">Dades acadèmiques i laborals</button>
    <button class="tablinks" data-tab="tab5">Dades polítiques i sindicals</button>
    <button class="tablinks" data-tab="tab6">Biografia</button>
    <button class="tablinks" data-tab="tab7">Fonts documentals</button>
    <button class="tablinks" data-tab="tab10">Imatge perfil</button>
    <button class="tablinks" data-tab="tab11">Multimèdia</button>
    <button class="tablinks" data-tab="tab8">Altres dades</button>
    <button class="tablinks" data-tab="tab9">Registre canvis</button>
  </div>

  <form id="formFitxaRepressaliat">
    <input type="hidden" name="id" id="id" value="">
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
                  <input class="form-check-input" type="checkbox" id="categoria1" name="categoria" value="categoria1">
                  <label class="form-check-label" for="categoria1">
                    Afusellat
                  </label>
                </div>

                <div class="form-check me-3">
                  <input class="form-check-input" type="checkbox" id="categoria6" name="categoria" value="categoria6">
                  <label class="form-check-label" for="categoria6">
                    Processat/Empresonat
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
                    Empresonat Presó Model
                  </label>
                </div>

                <div class="form-check me-3">
                  <input class="form-check-input" type="checkbox" id="categoria13" name="categoria" value="categoria13">
                  <label class="form-check-label" for="categoria13">
                    Detingut Guàrdia Urbana
                  </label>
                </div>

                <div class="form-check me-3">
                  <input class="form-check-input" type="checkbox" id="categoria14" name="categoria" value="categoria14">
                  <label class="form-check-label" for="categoria14">
                    Detingut Comitè de Solidaritat (1970-1977)
                  </label>
                </div>

                <div class="form-check me-3">
                  <input class="form-check-input" type="checkbox" id="categoria15" name="categoria" value="categoria15">
                  <label class="form-check-label" for="categoria15">
                    Expedient Responsabilitats polítiques
                  </label>
                </div>

                <div class="form-check me-3">
                  <input class="form-check-input" type="checkbox" id="categoria16" name="categoria" value="categoria16">
                  <label class="form-check-label" for="categoria16">
                    Empresonat dipòsit municipal Sant Llàtzer (1951-1975)
                  </label>
                </div>

                <div class="form-check me-3">
                  <input class="form-check-input" type="checkbox" id="categoria17" name="categoria" value="categoria17">
                  <label class="form-check-label" for="categoria17">
                    Tribunal Orden Público
                  </label>
                </div>

                <div class="form-check me-3">
                  <input class="form-check-input" type="checkbox" id="categoria18" name="categoria" value="categoria18">
                  <label class="form-check-label" for="categoria18">
                    Comitè Relacions de Solidaritat (1939-1940)
                  </label>
                </div>

                <div class="form-check me-3">
                  <input class="form-check-input" type="checkbox" id="categoria19" name="categoria" value="categoria19">
                  <label class="form-check-label" for="categoria19">
                    Detingut Camps de treball
                  </label>
                </div>


                <div class="form-check me-3">
                  <input class="form-check-input" type="checkbox" id="categoria20" name="categoria" value="categoria20">
                  <label class="form-check-label" for="categoria20">
                    Detingut Batalló de presos
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

            <div class="col-md-12" style="margin-bottom:20px;margin-top:20px">
              <h6><strong>Dones:</strong></h6>

              <div id="categoria" class="d-flex flex-wrap">
                <div class="form-check me-3">
                  <input class="form-check-input" type="checkbox" id="categoria8" name="categoria" value="categoria8">
                  <label class="form-check-label" for="categoria8">
                    Dona
                  </label>
                </div>

              </div>
            </div> <!-- Fi bloc dones -->

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
        </div>

        <div class="col-md-4 mb-4">
          <label for="sexe" class="form-label negreta">Gènere:</label>
          <select class="form-select" id="sexe" name="sexe">
            <option selected disabled>Selecciona una opció:</option>
            <option value="1">Home</option>
            <option value="2">Dona</option>
          </select>
          <div class="avis-form">
            * Camp obligatori
          </div>
        </div>

        <div class="col-md-4 mb-4">
          <label for="data_naixement" class="form-label negreta">Data de naixement:</label>
          <input type="text" class="form-control" id="data_naixement" name="data_naixement" value="">
          <div class="avis-form">
            * Format vàlid de la data: dia/mes/any. Deixar-ho en blanc si la data és desconeguda
          </div>
        </div>

        <div class="col-md-4 mb-4">
          <label for="data_defuncio" class="form-label negreta">Data de defunció:</label>
          <input type="text" class="form-control" id="data_defuncio" name="data_defuncio" value="">
          <div class="avis-form">
            * Format vàlid de la data: dia/mes/any. Deixar-ho en blanc si la data és desconeguda
          </div>
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

        <hr>

        <h5>Dades residència (abans de la guerra pels exiliats/deportats o combatents i durant la dictadura per la resta)</h5>

        <div class="col-md-4 mb-4">
          <label for="ciutat_residencia" class="form-label negreta">Ciutat de residència:</label>
          <select class="form-select" name="municipi_residencia" id="municipi_residencia" value="">
          </select>
          <div class="avis-form">
            * Camp obligatori
          </div>

          <div class="mt-2">
            <a href="<?php echo APP_WEB . APP_INTRANET . $url['auxiliars'] ?>/nou-municipi" target="_blank" class="btn btn-secondary btn-sm" id="afegirMunicipi3">Afegir municipi</a>
            <button id="refreshButton3" class="btn btn-primary btn-sm">Actualitzar llistat</button>
          </div>

        </div>

        <div class="col-md-2 mb-4">
          <label for="tipus_via" class="form-label negreta">Tipus de via</label>
          <select class="form-select" id="tipus_via" value="" name="tipus_via">
          </select>
        </div>

        <div class="col-md-4 mb-4">
          <label for="adreca" class="form-label negreta">Nom actual de la via de residència:</label>
          <input type="text" class="form-control" id="adreca" name="adreca" value="">
        </div>

        <div class="col-md-1 mb-4">
          <label for="adreca_num" class="form-label negreta">Número:</label>
          <input type="number" class="form-control" id="adreca_num" name="adreca_num" value="">
        </div>

        <div class="col-md-4 mb-4">
          <label for="adreca_antic" class="form-label negreta">Nom antic de la via de residència (tal com apareix als registres històrics):</label>
          <input type="text" class="form-control" id="adreca_antic" name="adreca_antic" value="">
        </div>

        <?php if ($isAdmin): ?>
          <div class="col-md-4 mb-4" id="geolocalitzacioBtn">
          </div>

        <?php endif; ?>

        <hr>

        <div class="col-md-4 mb-4">
          <label for="tipologia_lloc_defuncio" class="form-label negreta">Tipologia lloc de defunció:</label>
          <select class="form-select" id="tipologia_lloc_defuncio" value="" name="tipologia_lloc_defuncio">
          </select>
          <div class="avis-form">
            * Camp obligatori.
          </div>
          <div class="mt-2">
            <a href="<?php echo APP_WEB . APP_INTRANET . $url['auxiliars'] ?>/nova-tipologia-espai" target="_blank" class="btn btn-secondary btn-sm" id="afegirMunicipi4">Afegir tipologia espai</a>
            <button id="refreshButton4" class="btn btn-primary btn-sm">Actualitzar llistat</button>
          </div>

        </div>

        <div class="col-md-4 mb-4">
          <label for="causa_defuncio" class="form-label negreta">Causa de la defunció:</label>
          <select class="form-select" id="causa_defuncio" value="" name="causa_defuncio">
          </select>
          <div class="avis-form">
            * Camp obligatori. Només els usuaris amb permisos d'administració poden crear nous ítems.
          </div>

          <?php if ($isAdmin): ?>
            <div class="mt-2">
              <a href="<?php echo APP_WEB . APP_INTRANET . $url['auxiliars'] ?>/nova-causa-mort" target="_blank" class="btn btn-secondary btn-sm" id="afegirMunicipi5">Afegir causa de mort</a>
              <button id="refreshButton5" class="btn btn-primary btn-sm">Actualitzar llistat</button>
            </div>

          <?php endif; ?>
        </div>

        <div class="col-md-4 mb-4">
          <label for="causa_defuncio_detalls" class="form-label negreta">Detalls causa de la defunció:</label>
          <select class="form-select" id="causa_defuncio_detalls" value="" name="causa_defuncio_detalls">
          </select>
        </div>

      </div>
    </div> <!-- Fi tab1 -->

    <div id="tab3" class="tabcontent">
      <div class="row g-3">
        <h3>Dades familiars</h3>
        <div class="col-md-4 mb-4">
          <label for="estat_civil" class="form-label negreta">Estat civil:</label>
          <select class="form-select" id="estat_civil" name="estat_civil" value="">
          </select>
          <div class="avis-form">
            * Camp obligatori
          </div>
        </div>

        <hr style="margin-top:25px">

        <h4>Establir relacions de parentiu</h4>
        <div id="avisFamiliars" style="margin-top:5px;margin-bottom:5px;display:none"></div>
        <div id="botonsFamiliars" style="margin-top:5px;margin-bottom:5px"></div>
        <div id="quadreFamiliars"></div>

      </div>
    </div> <!-- Fi tab2 -->

    <div id="tab4" class="tabcontent">
      <div class="row g-4">
        <h3>Dades laborals i acadèmiques</h3>

        <div class="col-md-4 mb-4">
          <label for="estudis" class="form-label negreta">Estudis:</label>
          <select class="form-select" id="estudis" value="" name="estudis">
          </select>
          <div class="avis-form">
            * Camp obligatori (en cas d'absència d'informació marqueu "Nivell d'estudis desconegut")
          </div>
        </div>

        <div class="col-md-4 mb-4">
          <label for="ofici" class="form-label negreta">Ofici:</label>
          <select class="form-select" id="ofici" value="" name="ofici">
          </select>
          <div class="avis-form">
            * Camp obligatori (en cas d'absència d'informació marqueu "Desconegut")
          </div>
          <div class="mt-2">
            <a href="<?php echo APP_WEB . APP_INTRANET . $url['auxiliars'] ?>/nou-ofici" target="_blank" class="btn btn-secondary btn-sm" id="afegirMunicipi1">Afegir ofici</a>
            <button id="refreshButtonOfici" class="btn btn-primary btn-sm">Actualitzar llistat</button>
          </div>
        </div>

        <div class="col-md-4 mb-4">
          <label for="empresa" class="form-label negreta">Empresa / Organisme públic:</label>
          <select class="form-select" id="empresa" value="" name="empresa">
          </select>
          <div class="avis" style="font-size:14px">
            * En cas d'absència d'informació marqueu "Desconeguda".
          </div>
          <div class="mt-2">
            <a href="<?php echo APP_WEB . APP_INTRANET . $url['auxiliars'] ?>/nova-empresa" target="_blank" class="btn btn-secondary btn-sm" id="afegirEmpresa">Afegir empresa</a>
            <button id="refreshButtonEmpresa" class="btn btn-primary btn-sm">Actualitzar llistat</button>
          </div>
        </div>

        <div class="col-md-4 mb-4">
          <label for="carrec_empresa" class="form-label negreta">Càrrec empresa:</label>
          <select class="form-select" id="carrec_empresa" value="" name="carrec_empresa">
          </select>
          <div class="avis" style="font-size:14px">
            * En cas d'absència d'informació marqueu "Càrrec desconegut"
          </div>

          <div class="mt-2">
            <a href="<?php echo APP_WEB . APP_INTRANET . $url['auxiliars'] ?>/nou-carrec-empresa" target="_blank" class="btn btn-secondary btn-sm" id="afegirMunicipi6">Afegir càrrec empresa</a>
            <button id="refreshButtonCarrec" class="btn btn-primary btn-sm">Actualitzar llistat</button>
          </div>
        </div>

        <div class="col-md-4 mb-4">
          <label for="sector" class="form-label negreta">Sector econòmic:</label>
          <select class="form-select" id="sector" value="" name="sector">
          </select>
          <div class="avis" style="font-size:14px">
            * En cas d'absència d'informació marqueu "Desconegut"
          </div>
        </div>

        <div class="col-md-4 mb-4">
          <label for="sub_sector" class="form-label negreta">Sub-sector econòmic:</label>
          <select class="form-select" id="sub_sector" value="" name="sub_sector">
          </select>
          <div class="avis" style="font-size:14px">
            * En cas d'absència d'informació marqueu "Desconegut"
          </div>
          <div class="mt-2">
            <a href="<?php echo APP_WEB . APP_INTRANET . $url['auxiliars'] ?>/nou-sub-sector-economic" target="_blank" class="btn btn-secondary btn-sm" id="afegirMunicipi6">Afegir sub-sector econòmic</a>
            <button id="refreshButtonSubSector" class="btn btn-primary btn-sm">Actualitzar llistat</button>
          </div>
        </div>

      </div>
    </div> <!-- Fi tab3 -->

    <div id="tab5" class="tabcontent">
      <div class="row g-4">
        <h3>Dades polítiques/sindicals</h3>

        <div class="container" style="margin-top:20px;margin-bottom:40px">
          <div class="row g-4">
            <div class="col-md-12">
              <h6><strong>Filiació política:</strong></h6>
              <div id="partit_politic" class="d-flex flex-wrap"> </div>
            </div>

            <div class="avis-form">
              * Camp obligatori (en cas d'absència d'informació marqueu "Filiació desconeguda")
            </div>

            <div class="mt-2">
              <a href="<?php echo APP_WEB . APP_INTRANET . $url['auxiliars'] ?>/nou-partit-politic" target="_blank" class="btn btn-secondary btn-sm" id="afegirMunicipi7">Afegir partit polític</a>
              <button id="refreshButtonPartits" class="btn btn-primary btn-sm">Actualitzar llistat</button>
            </div>
          </div>
        </div>

        <div class="container" style="margin-top:10px;margin-bottom:40px">
          <div class="row g-4">
            <div class="col-md-12">
              <h6><strong>Filiació sindical:</strong></h6>
              <div id="sindicat" class="d-flex flex-wrap"></div>
            </div>
            <div class="avis-form">
              * Camp obligatori (en cas d'absència d'informació marqueu "Filiació desconeguda")
            </div>

            <div class="mt-2">
              <a href="<?php echo APP_WEB . APP_INTRANET . $url['auxiliars'] ?>/nou-sindicat" target="_blank" class="btn btn-secondary btn-sm" id="afegirMunicipi8">Afegir sindicat</a>
              <button id="refreshButtonSindicats" class="btn btn-primary btn-sm">Actualitzar llistat</button>
            </div>
          </div>
        </div>

        <div class="col-md-12">
          <label for="activitat_durant_guerra" class="form-label negreta">Activitat política/sindical durant la guerra civil i la dictadura:</label>
          <textarea class="form-control" id="activitat_durant_guerra" name="activitat_durant_guerra" value="" rows="3"></textarea>
          <div class="avis" style="font-size:14px">
            * Camp opcional
          </div>
        </div>

      </div>
    </div> <!-- Fi tab4 -->

    <div id="tab6" class="tabcontent">
      <div class="row">
        <h3>Biografia</h3>

        <div id="avisBiografia" style="margin-top:5px;margin-bottom:5px;display:none"></div>
        <div id="botonsBiografies" style="margin-top:5px;margin-bottom:5px"></div>
        <div id="quadreBiografies"></div>

      </div>
    </div> <!-- Fi tab5 -->

    <div id="tab7" class="tabcontent">
      <div class="row">
        <h3>Fonts documentals</h3>
        <div id="avisFonts" style="margin-top:5px;margin-bottom:5px;display:none"></div>

        <div id="quadreFonts1" style="display:none">
          <div id="botonsFonts1" style="margin-top:5px;margin-bottom:5px"></div>
          <div id="quadreFontsBibliografia"></div>
        </div>

        <div id="quadreFonts2" style="display:none">
          <div id="botonsFonts2" style="margin-top:5px;margin-bottom:5px"></div>
          <div id="quadreFontsArxius"></div>
        </div>
      </div>
    </div> <!-- Fi tab6 -->

    <div id="tab8" class="tabcontent">
      <div class="row g-3">
        <h3>Altres dades</h3>

        <?php if ($isAdmin): ?>

          <div class="col-md-4 mb-4">
            <label for="slug" class="form-label negreta">Slug URL:</label>
            <input type="text" class="form-control" id="slug" name="slug" value="">
          </div>

        <?php else: ?>
          <div class="col-md-4 mb-4">
            <label for="slug2" class="form-label negreta">Slug URL:</label>
            <input type="text" class="form-control" id="slug2" name="slug2" value="" readonly>

            <div class="avis-form">
              * Camp obligatori (només els usuaris administradors poden canviar aquest valor)
            </div>

          </div>

        <?php endif; ?>

        <div class="col-md-12">
          <label for="observacions" class="form-label negreta">Observacions (aquest text apareix al web ):</label>
          <textarea class="form-control" id="observacions" name="observacions" value="" rows="3"></textarea>
        </div>

        <div class="col-md-4 mb-4">
          <label for="autor" class="form-label negreta">Autor principal fitxa:</label>
          <select class="form-select" id="autor" value="" name="autor">
          </select>
          <div class="avis-form">
            * Camp obligatori
          </div>
        </div>

        <div class="col-md-4 mb-4">
          <label for="autor2" class="form-label negreta">Co-autor 2:</label>
          <select class="form-select" id="autor2" value="" name="autor2">
          </select>
        </div>

        <div class="col-md-4 mb-4">
          <label for="autor3" class="form-label negreta">Co-autor 3:</label>
          <select class="form-select" id="autor3" value="" name="autor3">
          </select>
        </div>

        <div class="col-md-4 mb-4">
          <label for="colab1" class="form-label negreta">Col·laborador fitxa / introducció dades:</label>
          <select class="form-select" id="colab1" value="" name="colab1">
          </select>
        </div>

        <div class="col-md-4 mb-4">
          <label for="data_creacio" class="form-label negreta">Data de creació de la fitxa:</label>
          <div id="data_creacio"></div>
        </div>

        <div class="col-md-4 mb-4">
          <label for="data_actualitzacio" class="form-label negreta">Data d'actualització:</label>
          <div id="data_actualitzacio"></div>
        </div>

        <hr style="margin-top:25px">

        <div class="col-md-12">
          <label for="observacions_internes" class="form-label negreta">Notes internes fitxa (aquest text NO apareix al web ):</label>
          <textarea class="form-control" id="observacions_internes" name="observacions_internes" value="" rows="4"></textarea>
        </div>

        <div class="form-group">
          <label for="completat" class="form-label negreta">Estat de la fitxa:</label><br>

          <!-- Botón de opción "Sí" -->
          <div class="custom-control custom-radio custom-control-inline">
            <input type="radio" id="completat_si" name="completat" value="2" class="custom-control-input">
            <label class="custom-control-label" for="completat_si">Completada</label>
          </div>

          <!-- Botón de opción "No" -->
          <div class="custom-control custom-radio custom-control-inline">
            <input type="radio" id="completat_pendent" name="completat" value="3" class="custom-control-input">
            <label class="custom-control-label" for="completat_pendent">Revisió pendent</label>
          </div>

          <!-- Botón de opción "No" -->
          <div class="custom-control custom-radio custom-control-inline">
            <input type="radio" id="completat_no" name="completat" value="1" class="custom-control-input">
            <label class="custom-control-label" for="completat_no">No completada</label>
          </div>
        </div>

        <div class="form-group">
          <label for="visibilitat" class="form-label negreta">Visibilitat de la fitxa:</label><br>

          <!-- Botón de opción "Sí" -->
          <div class="custom-control custom-radio custom-control-inline">
            <input type="radio" id="visibilitat_si" name="visibilitat" value="2" class="custom-control-input">
            <label class="custom-control-label" for="visibilitat_si">Visible</label>
          </div>

          <!-- Botón de opción "No" -->
          <div class="custom-control custom-radio custom-control-inline">
            <input type="radio" id="visibilitat_no" name="visibilitat" value="1" class="custom-control-input">
            <label class="custom-control-label" for="visibilitat_no">No visible</label>
          </div>
        </div>

      </div>
    </div> <!-- Fi tab7 -->

    <div id="tab9" class="tabcontent">
      <div class="row g-3">
        <h3>Registre edició fitxa</h3>

        <div id="quadreEdicions"></div>

      </div>
    </div> <!-- Fi tab9 -->

    <div id="tab10" class="tabcontent">
      <div class="row g-3">
        <h3>Imatge fitxa represaliat</h3>

        <div id="imatgePerfil"></div>
        <input type="hidden" id="imatgePerfilHidden" name="imatgePerfil" value="">

      </div>
    </div> <!-- Fi tab10 -->

    <div class="row espai-superior" style="border-top: 1px solid black;padding-top:25px">
      <div class="col">
        <a class="btn btn-secondary" role="button" aria-disabled="true" onclick="goBack()">Cancel·lar els canvis</a>
      </div>

      <div class="col d-flex justify-content-end align-items-center">

        <?php
        if ($modificaBtn === 1) {
          echo '<button type="submit" form="formFitxaRepressaliat" class="btn btn-primary" id="btnModificarFitxa">
          Modificar dades
        </button>';
        } else {

          if ($isAdmin):
            echo '<button type="submit" form="formFitxaRepressaliat" class="btn btn-primary" id="btnEnviaFitxa">
              Crea nova fitxa
            </button>';
          endif;
        }
        ?>
      </div>
    </div>
  </form> <!-- Fi Form -->
</div>


<div class="container" id="quadreGrupsRepressio" style="display:none">
  <h2>Modificació dades del grup de repressió</h2>
  <div class="d-flex flex-wrap" id="btnActualitzarRepressio"></div>
  <div class="d-flex flex-wrap" id="btnRepressio"></div>
</div>