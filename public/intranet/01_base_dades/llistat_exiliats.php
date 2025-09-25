<?php require_once APP_ROOT . '/public/intranet/includes/header.php'; ?>

<div class="container" style="margin-bottom:55px">
    <?php if ($isAdmin || $isAutor || $isUserExili): ?>
        <h2>Llistat d'exiliats i deportats:</h2>
        <ul>
            <li><a href="<?php echo APP_SERVER . APP_INTRANET . $urlIntranet['base_dades']; ?>/exiliats-deportats/llistat-exiliats">Llistat exiliats</a></li>
            <li><a href="<?php echo APP_SERVER . APP_INTRANET . $urlIntranet['base_dades']; ?>/exiliats-deportats/llistat-deportats">Llistat deportats</a></li>
            <li><a href="<?php echo APP_SERVER . APP_INTRANET . $urlIntranet['base_dades']; ?>/general/llistat-revisio">Llistat casos revisió</a></li>
        </ul>
        <hr>

        <div id="taulaExiliats"></div>

    <?php else: ?>
        <p><strong>L'accés a aquesta pàgina està restringit només a usuaris administratius.</strong></p>
    <?php endif; ?>
</div>