<?php require_once APP_ROOT . '/public/intranet/includes/header.php'; ?>

<div class="container">
    <?php if ($isAdmin || $isAutor || $isUserCostHuma): ?>
        <h2>Cost humà de la Guerra Civil (1936-1939):</h2>
        <ul>
            <li><a href="<?php echo APP_SERVER . APP_INTRANET . $urlIntranet['base_dades']; ?>/cost-huma/llistat-morts-al-front">Llistat de desapareguts i morts al front</a></li>
            <li><a href="<?php echo APP_SERVER . APP_INTRANET . $urlIntranet['base_dades']; ?>/cost-huma/llistat-morts-civils">Llistat de morts civils</a></li>
            <li><a href="<?php echo APP_SERVER . APP_INTRANET . $urlIntranet['base_dades']; ?>/cost-huma/llistat-represalia-republicana">Llistat de la represàlia republicana</a></li>
            <li><a href="<?php echo APP_SERVER . APP_INTRANET . $urlIntranet['base_dades']; ?>/general/llistat-revisio">Llistat casos revisió</a></li>
        </ul>
        <hr>

        <div id="taulaCostHuma"></div>

    <?php else: ?>
        <p><strong>L'accés a aquesta pàgina està restringit només a usuaris administratius.</strong></p>
    <?php endif; ?>
</div>