<?php require_once APP_ROOT . '/public/intranet/includes/header.php';

echo '<h4>Llistat complert de represaliats</h4>';
echo "<hr>";

// Añadimos el input de búsqueda
echo '<input type="text" id="searchInput" placeholder="Cercar...">';

// Añadimos el div donde se renderizará la tabla
echo '<div class="table-responsive" style="margin-top:30px">';
echo '<table class="table table-striped table-hover" id="represaliatsTable">
       <thead class="table-dark">
        <tr>
            <th>Nom complet</th>
            <th>Municipi naixement</th>
            <th>Municipi defunció</th>
            <th>Col·lectiu</th>
            <th>Modificar</th>
            <th>Eliminar</th>
        </tr>
        </thead>
        <tbody id="represaliatsBody">
        <!-- Aquí se insertarán las filas de la tabla dinámicamente -->
        </tbody>
    </table>';

// Paginación
echo '<div id="pagination">
        <button id="prevPage" disabled>Anterior</button>
        <span id="currentPage">1</span> de <span id="totalPages">1</span>
        <button id="nextPage">Següent</button>
    </div>';
echo '</div>';
