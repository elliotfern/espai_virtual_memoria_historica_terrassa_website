<?php
require_once APP_ROOT . '/public/intranet/includes/header.php';
?>

<div class="container" style="margin-bottom:50px;border: 1px solid gray;border-radius: 10px;padding:25px;background-color:#eaeaea">
    <div class="container">
        <h2>Gestió base de dades auxiliars</h2>
        <h4>Llistat d'imatges</h4>

        <div class="text-start">
            <p><button class="btn btn-primary" onclick="window.location.href='<?php echo APP_INTRANET . $url['auxiliars']; ?>/nova-imatge'">
                    Pujar nova imatge al web
                </button></p>
        </div>

        <div id="taulaImatges"></div>
    </div>
</div>