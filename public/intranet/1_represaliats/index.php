<?php require_once APP_ROOT . '/public/intranet/includes/header.php'; ?>

<div class="container">
    <h2>Llistat de represaliats (1939-1979):</h2>
    <ul>
        <li>Processats / Empresonats</li>
        <li>Afusellats</li>
        <li>Depurats</li>
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
                    <th>Estat fitxa</th>
                    <th>Modificar</th>
                    <th>Eliminar</th>
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
</div>