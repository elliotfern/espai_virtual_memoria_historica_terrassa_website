<?php

echo '<h4>Llistat complert de represaliats</h4>';
echo "<hr>";

// Añadimos el input de búsqueda
echo '<input type="text" id="searchInput" placeholder="Buscar...">';

// Añadimos el div donde se renderizará la tabla
echo '<div class="' . TABLE_DIV_CLASS . '">';
echo '<table class="table table-striped" id="represaliatsTable">
        <thead>
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
        <button id="nextPage">Siguiente</button>
    </div>';
echo '</div>';

?>
<script>
document.addEventListener("DOMContentLoaded", function () {
    let server = window.location.hostname;
    let urlAjax = "https://" + server + "/api/represaliats/get/?type=tots";
    let currentPage = 1;
    let rowsPerPage = 10; // Número de filas por página
    let totalPages = 1;
    let datos = [];

    // Función para obtener los datos con Fetch API y async/await
    async function obtenerDatos() {
        try {
            let token = localStorage.getItem('token');
            let response = await fetch(urlAjax, {
                method: 'GET',
                headers: {
                    'Authorization': 'Bearer ' + token
                }
            });

            if (!response.ok) {
                throw new Error(`Error HTTP: ${response.status}`);
            }

            datos = await response.json(); // Parseamos la respuesta a JSON
            totalPages = Math.ceil(datos.length / rowsPerPage); // Calculamos el número total de páginas
            document.getElementById('totalPages').textContent = totalPages; // Actualizamos el número total de páginas
            renderizarTabla(currentPage); // Renderizamos la tabla para la página actual
        } catch (error) {
            console.error("Error al obtener los datos: ", error.message);
        }
    }

    // Función para renderizar la tabla con paginación
    function renderizarTabla(page) {
        const tbody = document.getElementById("represaliatsBody");
        tbody.innerHTML = ''; // Limpiar el contenido actual
        let start = (page - 1) * rowsPerPage;
        let end = start + rowsPerPage;
        const datosPaginados = datos.slice(start, end); // Obtener el rango de datos para esta página

        datosPaginados.forEach(function (row) {
            const tr = document.createElement("tr");

            // Nombre completo
            const tdNombre = document.createElement("td");
            const nombreCompleto = `${row.cognom1} ${row.cognom2}, ${row.nom}`;
            tdNombre.innerHTML = `<strong><a href="/represaliats/fitxa/${row.id}">${nombreCompleto}</a></strong>`;
            tr.appendChild(tdNombre);

            // Municipio nacimiento
            const tdMunicipiNaixement = document.createElement("td");
            const municipiNaixement = `${row.ciutat ?? "Desconegut"} (${row.comarca ?? "Desconegut"}, ${row.provincia ?? "Desconegut"}, ${row.comunitat ?? "Desconegut"}, ${row.pais ?? "Desconegut"})`;
            tdMunicipiNaixement.textContent = municipiNaixement;
            tr.appendChild(tdMunicipiNaixement);

            // Municipio defunció
            const tdMunicipiDefuncio = document.createElement("td");
            const municipiDefuncio = `${row.ciutat2 ?? "Desconegut"} (${row.comarca2 ?? "Desconegut"}, ${row.provincia2 ?? "Desconegut"}, ${row.comunitat2 ?? "Desconegut"}, ${row.pais2 ?? "Desconegut"})`;
            tdMunicipiDefuncio.textContent = municipiDefuncio;
            tr.appendChild(tdMunicipiDefuncio);

            // Col·lectiu
            const tdCollectiu = document.createElement("td");
            const categorias = row.categoria ? row.categoria.replace(/[{}]/g, '').split(',').map(Number) : [];
            const collectiuTexto = categorias.map(num => {
                switch(num) {
                    case 1: return 'Afusellat';
                    case 2: return 'Deportat';
                    case 3: return 'Mort en combat';
                    case 10: return 'Exiliat';
                    default: return '';
                }
            }).filter(Boolean).join(', ');
            tdCollectiu.textContent = collectiuTexto;
            tr.appendChild(tdCollectiu);

            // Botón Modificar
            const tdModificar = document.createElement("td");
            const btnModificar = document.createElement("button");
            btnModificar.textContent = "Modificar dades";
            btnModificar.classList.add("btn", "btn-sm", "btn-warning");
            btnModificar.onclick = function () {
                window.location.href = "/afusellats/fitxa/modifica/" + row.id;
            };
            tdModificar.appendChild(btnModificar);
            tr.appendChild(tdModificar);

            // Botón Eliminar
            const tdEliminar = document.createElement("td");
            const btnEliminar = document.createElement("button");
            btnEliminar.textContent = "Eliminar";
            btnEliminar.classList.add("btn", "btn-sm", "btn-danger");
            btnEliminar.onclick = function () {
                if (confirm("¿Estás seguro de que deseas eliminar este registro?")) {
                    window.location.href = "/afusellats/eliminar/" + row.id;
                }
            };
            tdEliminar.appendChild(btnEliminar);
            tr.appendChild(tdEliminar);

            // Añadir la fila a la tabla
            tbody.appendChild(tr);
        });

        // Actualizar el estado de la paginación
        document.getElementById('currentPage').textContent = currentPage;
        document.getElementById('prevPage').disabled = currentPage === 1;
        document.getElementById('nextPage').disabled = currentPage === totalPages;
    }

    // Función para buscar en todos los datos
    function buscarEnTodosLosDatos() {
        const input = document.getElementById("searchInput").value.toLowerCase();
        const tbody = document.getElementById("represaliatsBody");
        tbody.innerHTML = ''; // Limpiar el contenido actual

        // Filtrar los datos que coincidan con la búsqueda
        const resultadosFiltrados = datos.filter(function (row) {
            const nombreCompleto = `${row.cognom1} ${row.cognom2}, ${row.nom}`.toLowerCase();
            const municipiNaixement = `${row.ciutat ?? "Desconegut"} (${row.comarca ?? "Desconegut"}, ${row.provincia ?? "Desconegut"}, ${row.comunitat ?? "Desconegut"}, ${row.pais ?? "Desconegut"})`.toLowerCase();
            const municipiDefuncio = `${row.ciutat2 ?? "Desconegut"} (${row.comarca2 ?? "Desconegut"}, ${row.provincia2 ?? "Desconegut"}, ${row.comunitat2 ?? "Desconegut"}, ${row.pais2 ?? "Desconegut"})`.toLowerCase();
            const categorias = row.categoria ? row.categoria.replace(/[{}]/g, '').split(',').map(Number) : [];
            const collectiuTexto = categorias.map(num => {
                switch(num) {
                    case 1: return 'Afusellat';
                    case 2: return 'Deportat';
                    case 3: return 'Mort en combat';
                    case 10: return 'Exiliat';
                    default: return '';
                }
            }).filter(Boolean).join(', ').toLowerCase();

            // Verifica si alguno de los campos coincide con la búsqueda
            return nombreCompleto.includes(input) || municipiNaixement.includes(input) || 
                   municipiDefuncio.includes(input) || collectiuTexto.includes(input);
        });

        // Renderiza los resultados filtrados
        resultadosFiltrados.forEach(function (row) {
            const tr = document.createElement("tr");

            // Nombre completo
            const tdNombre = document.createElement("td");
            const nombreCompleto = `${row.cognom1} ${row.cognom2}, ${row.nom}`;
            tdNombre.innerHTML = `<strong><a href="/represaliats/fitxa/${row.id}">${nombreCompleto}</a></strong>`;
            tr.appendChild(tdNombre);

            // Municipio nacimiento
            const tdMunicipiNaixement = document.createElement("td");
            const municipiNaixement = `${row.ciutat ?? "Desconegut"} (${row.comarca ?? "Desconegut"}, ${row.provincia ?? "Desconegut"}, ${row.comunitat ?? "Desconegut"}, ${row.pais ?? "Desconegut"})`;
            tdMunicipiNaixement.textContent = municipiNaixement;
            tr.appendChild(tdMunicipiNaixement);

            // Municipio defunció
            const tdMunicipiDefuncio = document.createElement("td");
            const municipiDefuncio = `${row.ciutat2 ?? "Desconegut"} (${row.comarca2 ?? "Desconegut"}, ${row.provincia2 ?? "Desconegut"}, ${row.comunitat2 ?? "Desconegut"}, ${row.pais2 ?? "Desconegut"})`;
            tdMunicipiDefuncio.textContent = municipiDefuncio;
            tr.appendChild(tdMunicipiDefuncio);

            // Col·lectiu
            const tdCollectiu = document.createElement("td");
            const categorias = row.categoria ? row.categoria.replace(/[{}]/g, '').split(',').map(Number) : [];
            const collectiuTexto = categorias.map(num => {
                switch(num) {
                    case 1: return 'Afusellat';
                    case 2: return 'Deportat';
                    case 3: return 'Mort en combat';
                    case 10: return 'Exiliat';
                    default: return '';
                }
            }).filter(Boolean).join(', ');
            tdCollectiu.textContent = collectiuTexto;
            tr.appendChild(tdCollectiu);

            // Botón Modificar
            const tdModificar = document.createElement("td");
            const btnModificar = document.createElement("button");
            btnModificar.textContent = "Modificar dades";
            btnModificar.classList.add("btn", "btn-sm", "btn-warning");
            btnModificar.onclick = function () {
                window.location.href = "/afusellats/fitxa/modifica/" + row.id;
            };
            tdModificar.appendChild(btnModificar);
            tr.appendChild(tdModificar);

            // Botón Eliminar
            const tdEliminar = document.createElement("td");
            const btnEliminar = document.createElement("button");
            btnEliminar.textContent = "Eliminar";
            btnEliminar.classList.add("btn", "btn-sm", "btn-danger");
            btnEliminar.onclick = function () {
                if (confirm("¿Estás seguro de que deseas eliminar este registro?")) {
                    window.location.href = "/afusellats/eliminar/" + row.id;
                }
            };
            tdEliminar.appendChild(btnEliminar);
            tr.appendChild(tdEliminar);

            // Añadir la fila a la tabla
            tbody.appendChild(tr);
        });

        // Actualizar el estado de la paginación (opcional si se quiere mantener la paginación después de la búsqueda)
        totalPages = Math.ceil(resultadosFiltrados.length / rowsPerPage);
        document.getElementById('totalPages').textContent = totalPages;
        document.getElementById('currentPage').textContent = currentPage;
    }

    // Función para cambiar de página
    function cambiarPagina(delta) {
        currentPage += delta;
        if (currentPage < 1) currentPage = 1;
        if (currentPage > totalPages) currentPage = totalPages;
        renderizarTabla(currentPage);
    }

    // Evento para el buscador que ejecuta la búsqueda global
    document.getElementById("searchInput").addEventListener("input", buscarEnTodosLosDatos);

    // Eventos para la paginación
    document.getElementById('prevPage').addEventListener('click', function() {
        cambiarPagina(-1);
    });
    document.getElementById('nextPage').addEventListener('click', function() {
        cambiarPagina(1);
    });

    // Llamada inicial para obtener los datos
    obtenerDatos();
});
</script>

<?php
# footer
require_once(APP_ROOT . APP_DEV . '/public/php/footer.php');
?>
