<?php require_once APP_ROOT . '/public/intranet/includes/header.php'; ?>

<div class="container" style="padding:25px;margin-top:25px;margin-bottom:50px;">

    <h3 style="margin-bottom:25px">Registre horari</h3>

    <div class="text-start">
        <p><button class="btn btn-primary" onclick="window.location.href='<?php echo APP_INTRANET . $urlIntranet['registre_horari']; ?>/nou-registre'">
                Afegir nou registre
            </button></p>

        <?php if ($isAdmin): ?>
            <p><button class="btn btn-primary" onclick="window.location.href='<?php echo APP_INTRANET . $urlIntranet['registre_horari']; ?>/taula-registre'">
                    Llistat complert
                </button></p>
        <?php endif; ?>
    </div>

    <div id="taulaHores"></div>
</div>