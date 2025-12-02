<?php
$idPersona = $routeParams[1];
?>

<div class="container" style="margin-bottom:50px;border: 1px solid gray;border-radius: 10px;padding:25px;background-color:#eaeaea">

    <div class="container">
        <h2>Tipus de repressió: Detingut / Consell de Guerra</h2>
        <h4 id="fitxaNomCognoms">Fitxa:</a></h4>

        <p><button onclick="window.location.href='<?php echo APP_INTRANET . $urlIntranet['base_dades']; ?>/detinguts-consell-guerra/nou-detingut-consell-guerra/<?php echo $idPersona ?>'" class="btn btn-success">Nou registre detenció / Consell de Guerra</button></p>

        <div id="taulaLlistatConsellGuerra"></div>

    </div>

</div>