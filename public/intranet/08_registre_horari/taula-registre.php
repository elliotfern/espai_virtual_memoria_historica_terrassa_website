<?php

require_once APP_ROOT . '/public/intranet/includes/header.php';
?>

<div class="container" style="margin-bottom:50px;">
    <h3 style="margin-bottom:25px">Registre horari usuaris</h3>

    <div class="row g-2 align-items-end mb-3">
        <div class="col-auto">
            <label class="form-label">Mes</label>
            <input type="month" id="filtreMes" class="form-control">
        </div>
    </div>

    <div id="taulaRegistreHorari"></div>

</div>