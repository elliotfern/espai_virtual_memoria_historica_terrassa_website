<?php require_once APP_ROOT . '/public/intranet/includes/header.php'; ?>

<div class="container" style="margin-bottom:55px">
    <?php if ($isAdmin || $isAutor): ?>
        <h2>Llistat de represaliats (1939-1979):</h2>
        <ul>
            <li><a href="<?php echo APP_SERVER . APP_INTRANET . $urlIntranet['base_dades']; ?>/represaliats/llistat-processats">Llistat de detinguts / processats</a></li>
            <li><a href="<?php echo APP_SERVER . APP_INTRANET . $urlIntranet['base_dades']; ?>/represaliats/llistat-afusellats">Llistat d'afusellats</a></li>
            <li><a href="<?php echo APP_SERVER . APP_INTRANET . $urlIntranet['base_dades']; ?>/represaliats/llistat-preso-model">Llistat detinguts Presó Model</a></li>
            <li>Depurats</li>
            <li>Detinguts Guàrdia Urbana Terrassa</li>
            <li>Detinguts Comitè Solidaritat</li>
            <li>Responsabilitats polítiques</li>
            <li>Tribunal Orden Público</li>
            <li><a href="<?php echo APP_SERVER . APP_INTRANET . $urlIntranet['base_dades']; ?>/represaliats/llistat-pendents">Represaliats pendents de classificar (llistat Ajuntament)</a></li>
            <li><a href="<?php echo APP_SERVER . APP_INTRANET . $urlIntranet['base_dades']; ?>/general/llistat-revisio">Llistat casos revisió</a></li>
        </ul>

        <hr>

        <div id="taulaRepresaliats"></div>

    <?php else: ?>
        <p><strong>L'accés a aquesta pàgina està restringit només a usuaris administratius.</strong></p>
    <?php endif; ?>
</div>