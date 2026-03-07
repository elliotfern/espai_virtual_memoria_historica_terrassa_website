<?php
require_once APP_ROOT . '/public/intranet/includes/header.php';
?>

<div class="container" style="margin-bottom:50px;border: 1px solid gray;border-radius: 10px;padding:25px;background-color:#eaeaea">
    <div class="container">
        <div class="row">
            <h2>Gestió base de dades auxiliars</h2>
            <h4>Estudis: llistat de tipus d'estudi</h4>
            <?php if (isUserAdmin()) : ?>

                <p><button onclick="window.location.href='<?php echo APP_INTRANET . $url['auxiliars']; ?>/estudis/nou-territori'" class="btn btn-success">Nou tipus</button></p>

                <div id="taulaTipus"></div>

            <?php endif; ?>
        </div>
    </div>
</div>