<?php require_once APP_ROOT . '/public/intranet/includes/header.php'; ?>

<div class="container" style="margin-bottom:50px;border: 1px solid gray;border-radius: 10px;padding:25px;background-color:#eaeaea">
    <h2>Gestió de bases de dades auxiliars:</h2>

    <ul>
        <li><a href="<?php echo APP_SERVER . APP_INTRANET . $url['auxiliars']; ?>/llistat-municipis">Taula llistat de municipis</a></li>
        <li><a href="<?php echo APP_SERVER . APP_INTRANET . $url['auxiliars']; ?>/llistat-partits-politics">Taula llistat de partits polítics</a></li>
        <li><a href="<?php echo APP_SERVER . APP_INTRANET . $url['auxiliars']; ?>/llistat-sindicats">Taula llistat de sindicats</a></li>
    </ul>
    <div id="isAdminButton" style="display: none;margin-top:25px">
        <?php if (isUserAdmin()) : ?>
            <p><strong>Només visible per usuaris administratius:</strong></p>
            <ul>
                <li><a href="<?php echo APP_SERVER; ?>/gestio/auxiliars/llistat-usuaris">Taula usuaris registrats</a></li>
                <li><a href="<?php echo APP_SERVER; ?>/gestio/auxiliars/llistat-imatges">Taula llistat imatges</a></li>
            </ul>
        <?php endif; ?>
    </div>
</div>