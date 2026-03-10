<?php
require_once APP_ROOT . '/public/intranet/includes/header.php';
?>

<div class="container" style="margin-bottom:50px;border: 1px solid gray;border-radius: 10px;padding:25px;background-color:#eaeaea">
    <div class="container">
        <div class="row">
            <h2>Gestió base de dades auxiliars</h2>
            <h4>Antecedents: llistat d'antecedents Espai Virtual</h4>
            <?php if (isUserAdmin()) : ?>

                <p><button onclick="window.location.href='<?php echo APP_INTRANET . $url['auxiliars']; ?>/espai-virtual/nou-antecedent'" class="btn btn-success">Nou antecedent</button></p>

                <div id="taulaAntecedents"></div>

            <?php endif; ?>
        </div>
    </div>
</div>