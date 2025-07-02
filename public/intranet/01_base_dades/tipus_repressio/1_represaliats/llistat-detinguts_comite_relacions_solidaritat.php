<?php
$idPersona = $routeParams[1];
?>

<div class="container" style="margin-bottom:50px;border: 1px solid gray;border-radius: 10px;padding:25px;background-color:#eaeaea">

    <div class="container">
        <h2>Tipus de repressió: Detinguts Comitè de Relacions de Solidaritat (1939-1940)</h2>
        <h4 id="fitxaNomCognoms">Fitxa:</a></h4>

        <p><button onclick="window.location.href='<?php echo APP_INTRANET . $urlIntranet['base_dades']; ?>/empresonaments-comite-relacions-solidaritat/nou-empresonament/<?php echo $idPersona ?>'" class="btn btn-success">Nou registre detingut Comitè Relacions Solidaritat</button></p>

        <div id="taulaLlistatDetencionsComiteRelacionsSolidaritat"></div>

    </div>

</div>