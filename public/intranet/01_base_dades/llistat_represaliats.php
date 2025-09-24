<?php require_once APP_ROOT . '/public/intranet/includes/header.php'; ?>

<div class="container">
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

        <input type="text" id="searchInput" placeholder="Cercar...">

        <div id="botonsFiltres" class="mb-3 d-flex gap-3" style="margin-top:25px;margin-bottom:25px"></div>

        <div class="table-responsive" style="margin-top:30px">
            <table class="table table-striped table-hover" id="represaliatsTable">
                <thead class="table-dark">
                    <tr>
                        <th>Nom complet</th>
                        <th>Municipi naixement</th>
                        <th>Municipi defunció</th>
                        <th>Col·lectiu</th>
                        <th>Origen dades</th>
                        <th>Estat fitxa</th>
                        <th>Visibilitat</th>
                        <th>Modificar</th>
                        <?php if ($isAdmin): ?>
                            <th>Eliminar</th>
                        <?php endif; ?>
                    </tr>
                </thead>
                <tbody id="represaliatsBody">
                    <!-- Aquí se insertarán las filas de la tabla dinámicamente -->
                </tbody>
            </table>
            <div id="pagination" style="margin-bottom:50px">
                <button id="prevPage" disabled>Anterior</button>
                <span id="currentPage">1</span> de <span id="totalPages">1</span>
                <button id="nextPage">Següent</button>
            </div>
        </div>
    <?php else: ?>
        <p><strong>L'accés a aquesta pàgina està restringit només a usuaris administratius.</strong></p>
    <?php endif; ?>
</div>