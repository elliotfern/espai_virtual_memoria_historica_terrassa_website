<?php

$isAdmin = isUserAdmin();
$isAutor = isUserAutor();
?>

<div class="container text-center">
    <div class="row">

        <div class="col-12 col-md-12 d-flex flex-column flex-md-row justify-content-md-between gap-3">

            <?php if ($isAdmin || $isAutor): ?>
                <a href="<?php echo APP_WEB . APP_INTRANET; ?>/base-dades/general" class="btn btn-success menuBtn w-100 w-md-auto" role="button" aria-disabled="false">Tots</a>

                <a href="<?php echo APP_WEB . APP_INTRANET; ?>/base-dades/represaliats" class="btn btn-success menuBtn w-100 w-md-auto" role="button" aria-disabled="false">Represaliats 1939-79</a>
            <?php endif; ?>

            <a href="<?php echo APP_WEB . APP_INTRANET; ?>/base-dades/exiliats-deportats" class="btn btn-success menuBtn w-100 w-md-auto" role="button" aria-disabled="false">Exiliats</a>

            <a href="<?php echo APP_WEB . APP_INTRANET; ?>/base-dades/cost-huma" class="btn btn-success menuBtn w-100 w-md-auto" role="button" aria-disabled="false">Cost humà de la guerra</a>
        </div>
    </div>
</div>

<div class="container text-center" style="margin-top:10px;margin-bottom:20px">
    <div class="row">
        <div class="col-12 col-md-12 d-flex flex-column flex-md-row justify-content-md-between gap-3">
            <a href="<?php echo APP_WEB . APP_INTRANET; ?>/tots/fitxa-nova" class="btn btn-secondary menuBtn w-100 w-md-auto" role="button" aria-disabled="false">Creació nova fitxa</a>

            <?php if ($isAdmin || $isAutor): ?>
                <a href="<?php echo APP_WEB . APP_INTRANET; ?>/cronologia" class="btn btn-secondary menuBtn w-100 w-md-auto" role="button" aria-disabled="false">Cronologia</a>

                <a href="<?php echo APP_WEB . APP_INTRANET; ?>/auxiliars" class="btn btn-secondary menuBtn w-100 w-md-auto" role="button" aria-disabled="false">Taules auxiliars</a>

                <a href="<?php echo APP_WEB . APP_INTRANET; ?>/registre-canvis" class="btn btn-secondary menuBtn w-100 w-md-auto" role="button" aria-disabled="false">Registre canvis</a>
            <?php endif; ?>
        </div>
    </div>
</div>

<hr>