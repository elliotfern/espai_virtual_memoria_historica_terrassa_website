<?php
require_once APP_ROOT . '/public/intranet/includes/header.php';
?>

<div class="container" style="margin-bottom:50px;border: 1px solid gray;border-radius: 10px;padding:25px;background-color:#eaeaea">
    <div class="container">
        <h2>Gestió base de dades auxiliars</h2>
        <h4>Llistat de causes de defunció</h4>

        <div class="text-start">
            <p><button class="btn btn-primary" onclick="window.location.href='<?php echo APP_INTRANET . $url['auxiliars']; ?>/nova-causa-mort'">
                    Crear nova causa de mort
                </button></p>
        </div>

        <div id="taulaLlistatCausesDefuncio"></div>
    </div>
</div>