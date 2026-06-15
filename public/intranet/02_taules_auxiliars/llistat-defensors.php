<div class="container" style="margin-bottom:50px;border: 1px solid gray;border-radius: 10px;padding:25px;background-color:#eaeaea">
    <div class="container">
        <div class="row">
            <h2>Gestió base de dades auxiliars</h2>
            <h4>Llistat Defensors (Consells de guerra)</h4>
            <?php if (isUserAdmin()) : ?>

                <p><button onclick="window.location.href='<?php echo APP_INTRANET . $url['auxiliars']; ?>/nou-defensor'" class="btn btn-success">Nou defensor</button></p>

                <div id="tabla1"></div>

            <?php endif; ?>
        </div>
    </div>
</div>