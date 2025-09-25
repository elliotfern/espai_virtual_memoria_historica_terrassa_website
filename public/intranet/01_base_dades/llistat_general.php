<?php require_once APP_ROOT . '/public/intranet/includes/header.php'; ?>

<div class="container">
    <?php if ($isAdmin || $isAutor): ?>
        <h2>Llistat complert de víctimes i represaliats</h2>

        <?php if ($isAdmin): ?>
            <ul>
                <li><a href="<?php echo APP_SERVER . APP_INTRANET . $urlIntranet['base_dades']; ?>/general/llistat-duplicats">Llistat duplicats</a></li>
                <li><a href="<?php echo APP_SERVER . APP_INTRANET . $urlIntranet['base_dades']; ?>/general/llistat-revisio">Llistat casos revisió</a></li>
                <li><a href="<?php echo APP_SERVER . APP_INTRANET . $urlIntranet['base_dades']; ?>/general/llistat-revisio-geolocalitzacio">Llistat casos revisió geolocalitzacio</a></li>
                <li><a href="<?php echo APP_SERVER . APP_INTRANET . $urlIntranet['base_dades']; ?>/general/quadre-general">Llistat Quadre general de víctimes</a></li>
            </ul>
            <hr>
        <?php endif; ?>
        <div id="TaulaGeneral"></div>

    <?php else: ?>
        <p><strong>L'accés a aquesta pàgina està restringit només a usuaris administratius.</strong></p>
    <?php endif; ?>
</div>