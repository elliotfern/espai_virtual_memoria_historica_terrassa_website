<?php
require_once APP_ROOT . '/public/intranet/includes/header.php';
?>

<div class="container" style="margin-bottom:50px;border: 1px solid gray;border-radius: 10px;padding:25px;background-color:#eaeaea">
    <div class="container">
        <div class="row">
            <h2>Gestió base de dades auxiliars</h2>
            <h4>Llistat de motius de detenció/empresonament</h4>
            <?php if (isUserAdmin()) : ?>

                <p><button onclick="window.location.href='<?php echo APP_INTRANET . $url['auxiliars']; ?>/nou-motiu-detencio'" class="btn btn-success">Nou motiu detenció/empresonament</button></p>

                <div id="taulaMotiusDetencio"></div>

            <?php endif; ?>
        </div>
    </div>
</div>