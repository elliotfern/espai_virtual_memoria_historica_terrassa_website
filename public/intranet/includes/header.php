<div class="container text-center">
    <div class="row">

        <div class="col-12 col-md-12 d-flex flex-column flex-md-row justify-content-md-between gap-3">

            <?php if ($isAdmin || $isAutor): ?>
                <a href="<?php echo APP_WEB . APP_INTRANET . $urlIntranet['base_dades']; ?>/general" class="btn btn-success menuBtn w-100 w-md-auto" role="button" aria-disabled="false">Tots</a>
            <?php endif; ?>

            <?php if ($isAdmin || $isAutor || $isUserRepresaliats): ?>
                <a href="<?php echo APP_WEB . APP_INTRANET . $urlIntranet['base_dades']; ?>/represaliats" class="btn btn-success menuBtn w-100 w-md-auto" role="button" aria-disabled="false">Represaliats 1939-79</a>
            <?php endif; ?>

            <?php if ($isAdmin || $isAutor || $isUserExili): ?>
                <a href="<?php echo APP_WEB . APP_INTRANET . $urlIntranet['base_dades']; ?>/exiliats-deportats" class="btn btn-success menuBtn w-100 w-md-auto" role="button" aria-disabled="false">Exiliats i deportats</a>
            <?php endif; ?>

            <?php if ($isAdmin || $isAutor || $isUserCostHuma): ?>
                <a href="<?php echo APP_WEB . APP_INTRANET . $urlIntranet['base_dades']; ?>/cost-huma" class="btn btn-success menuBtn w-100 w-md-auto" role="button" aria-disabled="false">Cost humà de la guerra</a>
            <?php endif; ?>

        </div>
    </div>
</div>

<div class="container text-center" style="margin-top:10px;margin-bottom:20px">
    <div class="row">
        <div class="col-12 col-md-12 d-flex flex-column flex-md-row justify-content-md-between gap-3">
            <?php if ($isAdmin || $isAutor): ?>
                <a href="<?php echo APP_WEB . APP_INTRANET . $urlIntranet['base_dades']; ?>/nova-fitxa" class="btn btn-secondary menuBtn w-100 w-md-auto" role="button" aria-disabled="false">Creació nova fitxa</a>
            <?php endif; ?>

            <?php if ($isAdmin || $isAutor || $isLogged) : ?>
                <a href="<?php echo APP_WEB . APP_INTRANET . $urlIntranet['auxiliars']; ?>" class="btn btn-secondary menuBtn w-100 w-md-auto" role="button" aria-disabled="false">Taules auxiliars</a>
            <?php endif; ?>

            <?php if ($isAdmin || $isAutor) : ?>
                <a href="<?php echo APP_WEB . APP_INTRANET . $urlIntranet['cronologia']; ?>" class="btn btn-secondary menuBtn w-100 w-md-auto" role="button" aria-disabled="false">Cronologia</a>

                <a href="<?php echo APP_WEB . APP_INTRANET; ?>/registre-canvis" class="btn btn-secondary menuBtn w-100 w-md-auto" role="button" aria-disabled="false">Registre canvis</a>
            <?php endif; ?>
        </div>
    </div>
</div>

<hr>