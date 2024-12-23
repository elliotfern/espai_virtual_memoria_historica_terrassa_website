<?php require_once APP_ROOT . '/public/intranet/includes/header.php';

$id = $routeParams[0];
?>

<div class="container">
    <div id="info"> </div>

    <hr>

    <h6><strong>Dades vitals:</strong></h6>
    <div class="tab" id="botons1"></div>
    <div id="fitxa" class="fitxa-persona"> </div>

    <hr>

    <h6><strong>Tipus de repressió:</strong></h6>
    <div class="tab" id="botons2"></div>
    <div id="fitxa-categoria" class="fitxa-persona" style="margin-top:50px;margin-bottom:50px;display:none"> </div>

</div>