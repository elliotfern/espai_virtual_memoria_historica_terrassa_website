<?php
$idPersona = $routeParams[1];
?>

<div class="container" style="margin-bottom:50px;border: 1px solid gray;border-radius: 10px;padding:25px;background-color:#eaeaea">

    <div class="container">
        <h2>Tipus de repressió: Empresonat Presó Model</h2>
        <h4 id="fitxaNomCognoms">Fitxa:</a></h4>

        <p><button onclick="window.location.href='<?php echo APP_INTRANET . $urlIntranet['base_dades']; ?>/empresonaments-preso-model/nou-empresonament/<?php echo $idPersona ?>'" class="btn btn-success">Nou registre empresonament Presó Model</button></p>

        <div id="taulaLlistatDetencionsPresoModel"></div>

    </div>

</div>