<?php

echo '<div class="container">';
echo '<h2>Base de dades: Afusellats</h2>';

echo "<p><button type='button' class='btn btn-dark btn-sm' id='btnAddActor' onclick='btnFAddActor()' data-bs-toggle='modal' data-bs-target='#modalCreateActor'>Crear nou registre</button></p>";

echo "<hr>";

echo '<div class="' . TABLE_DIV_CLASS . '">';
echo '<table class="table table-striped datatable" id="afusellatsTable">
        <thead class="' . TABLE_THREAD . '">
        <tr>
            <th>Nom complet</th>
            <th>Data naixement</th>
            <th>Data execució</th>
            <th>Lloc afusellament</th>
            <th></th>
            <th></th>
        </tr>
        </thead>
        </table>
    </div>';

echo '</div>'; // Cierre de div para "container"
echo '</div>'; // Cierre de div para el contenedor principal

?>
<script>
$(document).ready(function () {
    // Agregar función de ordenación personalizada para fechas en formato dd/mm/yyyy
    jQuery.extend(jQuery.fn.dataTableExt.oSort, {
        "date-eu-pre": function (dateString) {
            var dateParts = dateString.split('/');
            return Date.parse(dateParts[2] + '-' + dateParts[1] + '-' + dateParts[0]) || 0;
        },

        "date-eu-asc": function (a, b) {
            return a - b;
        },

        "date-eu-desc": function (a, b) {
            return b - a;
        }
    });

    let server = window.location.hostname;
    let urlAjax = "https://" + server + "/api/afusellats/get/?type=llistat";
    $('#afusellatsTable').DataTable({
        "pageLength": 30, // Mostrar solo 30 resultados por página
        ajax: {
            url: urlAjax,
            type: "POST",
            dataSrc: "",
            beforeSend: function (xhr) {
                // Obtener el token del localStorage
                let token = localStorage.getItem('token');

                // Incluir el token en el encabezado de autorización
                xhr.setRequestHeader('Authorization', 'Bearer ' + token);
            },

        },
        order: [
            [0, "asc"]
        ],

        columns: [
             // Aquí está la configuración para agregar el botón en la columna deseada
            {
                targets: [0], // Indica que esta configuración se aplica a la columna número 5 (contando desde 0)
                orderable: true, // Indica que la columna no es ordenable
                render: function (data, type, row, meta) {
                    // La función de renderizado se llama para cada celda en la columna especificada.
                    // 'data' contiene el valor de la celda
                    // 'type' indica si el renderizado es para 'display', 'filter', 'sort' u 'type'
                    // 'row' contiene los datos de la fila
                    // 'meta' contiene metadatos sobre la celda, como el índice de la columna
                    return '<a href="/afusellats/fitxa/'+ row.id +'">' + row.cognoms + ', ' + row.nom + '</a>';

                },
            },

            {
                targets: [1], // Indica que esta configuración se aplica a la columna número 5 (contando desde 0)
                type: 'date-eu',
                orderable: true, // Indica que la columna es ordenable
                render: function (data, type, row, meta) {
                    // La función de renderizado se llama para cada celda en la columna especificada.
                    // 'data' contiene el valor de la celda
                    // 'type' indica si el renderizado es para 'display', 'filter', 'sort' u 'type'
                    // 'row' contiene los datos de la fila
                    // 'meta' contiene metadatos sobre la celda, como el índice de la columna
                    
                    return row.data_naixement;
                },
            },

            {
                targets: [2], // Indica que esta configuración se aplica a la columna número 5 (contando desde 0)
                type: 'date-eu',
                orderable: true, // Indica que la columna es ordenable
                render: function (data, type, row, meta) {
                    // La función de renderizado se llama para cada celda en la columna especificada.
                    // 'data' contiene el valor de la celda
                    // 'type' indica si el renderizado es para 'display', 'filter', 'sort' u 'type'
                    // 'row' contiene los datos de la fila
                    // 'meta' contiene metadatos sobre la celda, como el índice de la columna
                    
                    return row.data_execucio;
                },
            },

            {
                // lloc afusellament
                targets: [3], // Indica que esta configuración se aplica a la columna número 5 (contando desde 0)
                orderable: true, // Indica que la columna no es ordenable
                render: function (data, type, row, meta) {
                    // La función de renderizado se llama para cada celda en la columna especificada.
                    // 'data' contiene el valor de la celda
                    // 'type' indica si el renderizado es para 'display', 'filter', 'sort' u 'type'
                    // 'row' contiene los datos de la fila
                    // 'meta' contiene metadatos sobre la celda, como el índice de la columna
                    
                    return row.espai;
                },
            },

            {
                targets: [4], // Indica que esta configuración se aplica a la columna número 5 (contando desde 0)
                orderable: false, // Indica que la columna no es ordenable
                render: function (data, type, row, meta) {
                    // La función de renderizado se llama para cada celda en la columna especificada.
                    // 'data' contiene el valor de la celda
                    // 'type' indica si el renderizado es para 'display', 'filter', 'sort' u 'type'
                    // 'row' contiene los datos de la fila
                    // 'meta' contiene metadatos sobre la celda, como el índice de la columna

                    return (
                        '<button type="button" onclick="btnModificaBook('+row.id+')" id="btnModificaBook" class="btn btn-sm btn-warning">Modificar dades</button>'
                    );
                },
            },

            {
                targets: [5], // Indica que esta configuración se aplica a la columna número 5 (contando desde 0)
                orderable: false, // Indica que la columna no es ordenable
                render: function (data, type, row, meta) {
                    // La función de renderizado se llama para cada celda en la columna especificada.
                    // 'data' contiene el valor de la celda
                    // 'type' indica si el renderizado es para 'display', 'filter', 'sort' u 'type'
                    // 'row' contiene los datos de la fila
                    // 'meta' contiene metadatos sobre la celda, como el índice de la columna

                    return (
                        '<button type="button" onclick="btnDeleteBook('+row.id+')" id="btnDeleteBook" class="btn btn-sm btn-danger" >Eliminar</button>'
                    );
                },
            },
        ] // <- Se añadió la coma aquí
    });
});
</script>

<?php
# footer
require_once(APP_ROOT . APP_DEV . '/public/php/footer.php');
?>
