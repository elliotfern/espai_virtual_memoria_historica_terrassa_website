<!-- Sección con container-fluid -->
<div class="container-fluid background-image-cap" style="height:280px;display: flex; align-items: center;">

    <div class="container px-4">
        <span class="titol negreta gran italic-text cap">Base de dades<br>represaliats i represaliades de la dictadura franquista</span>

    </div>
</div>

<style>
    .cap {
        font-size: 45px;
        color: #b39b7c;
    }
</style>
<div class="container" style="margin-top: 50px;margin-bottom:50px;">

    <input type="text" id="searchInput" placeholder="Cercar...">

    <div class="table-responsive" style="margin-top:30px">
        <table class="table table-striped table-hover" id="represaliatsTable">
            <thead class="table-dark">
                <tr>
                    <th>Nom complet</th>
                    <th>Dades naixement</th>
                    <th>Dades defunció</th>
                    <th>Col·lectiu represaliat</th>
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