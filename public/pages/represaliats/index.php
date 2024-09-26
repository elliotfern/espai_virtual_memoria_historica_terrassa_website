<?php

echo '<div class="container">';
echo '<h2>Espai Virtual de la Memòria Històrica de Terrassa - EVMHT</h2>';
echo '<h4>Llistat complert de represaliats</h4>';

echo "<hr>";

echo '<div class="' . TABLE_DIV_CLASS . '">';
echo '<table class="table table-striped datatable" id="represaliatsTable">
        <thead class="' . TABLE_THREAD . '">
        <tr>
            <th>Nom complet</th>
            <th>Municipi naixement</th>
            <th>Municipi defunció</th>
            <th>Col·lectiu</th>
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
    let urlAjax = "https://" + server + "/api/represaliats/get/?type=tots";
    $('#represaliatsTable').DataTable({
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
                targets: [0],
                orderable: true,
                render: function (data, type, row, meta) {
                    // Función para convertir fecha de formato DD/MM/YYYY a YYYY-MM-DD
                    function convertirFecha(fecha) {
                        if (!fecha) return null;
                        const partes = fecha.split('/');
                        // Si la fecha ya está en un formato incorrecto, devolver null
                        if (partes.length !== 3) return null;
                        // Reorganizamos a YYYY-MM-DD
                        return `${partes[2]}-${partes[1]}-${partes[0]}`;
                    }

                    // Función para calcular la edad al morir
                    function calcularEdadAlMorir(fechaNacimiento, fechaDefuncion) {
                        const nacimiento = new Date(fechaNacimiento);
                        const defuncion = new Date(fechaDefuncion);
                        let edad = defuncion.getFullYear() - nacimiento.getFullYear();

                        const mesNacimiento = nacimiento.getMonth();
                        const diaNacimiento = nacimiento.getDate();
                        const mesDefuncion = defuncion.getMonth();
                        const diaDefuncion = defuncion.getDate();

                        if (mesDefuncion < mesNacimiento || (mesDefuncion === mesNacimiento && diaDefuncion < diaNacimiento)) {
                            edad--;
                        }

                        return edad;
                    }

                    // Convertir fechas al formato aceptado por Date
                    const fechaNacimiento = convertirFecha(row.data_naixement);
                    const fechaDefuncion = convertirFecha(row.data_defuncio);

                    // Verificamos que las fechas de nacimiento y defunción no sean nulas y sean válidas
                    let edadAlMorir = '';
                    if (fechaNacimiento && fechaDefuncion) {
                        edadAlMorir = calcularEdadAlMorir(fechaNacimiento, fechaDefuncion) + ' anys';
                    }

                    // Concatenar el nombre completo, las fechas y la edad
                    return '<a href="/afusellats/fitxa/' + row.id + '">' +
                        row.cognom1 + ' ' + row.cognom2 + ', ' + row.nom + '</a>' +
                        '<div style="font-size: 0.8em; color: gray;">' +
                        row.data_naixement + ' - ' + row.data_defuncio +
                        (edadAlMorir ? ' (' + edadAlMorir + ')' : '') + // Mostrar edad si está disponible
                        '</div>';
                },
            },

            {
                // lloc naixement
                targets: [1], // Indica que esta configuración se aplica a la columna número 5 (contando desde 0)
                orderable: true, // Indica que la columna no es ordenable
                render: function (data, type, row, meta) {
                    // La función de renderizado se llama para cada celda en la columna especificada.
                    // 'data' contiene el valor de la celda
                    // 'type' indica si el renderizado es para 'display', 'filter', 'sort' u 'type'
                    // 'row' contiene los datos de la fila
                    // 'meta' contiene metadatos sobre la celda, como el índice de la columna
                    
                    return row.ciutat + 
                    '<div style="font-size: 0.8em; color: gray;">' + 
                    row.comarca + ', ' +  row.provincia + ', ' + row.comunitat + ', ' + row.pais + 
                    '</div>';
                },
            },

            {
                // lloc afusellament
                targets: [2], // Indica que esta configuración se aplica a la columna número 5 (contando desde 0)
                orderable: true, // Indica que la columna no es ordenable
                render: function (data, type, row, meta) {
                    // La función de renderizado se llama para cada celda en la columna especificada.
                    // 'data' contiene el valor de la celda
                    // 'type' indica si el renderizado es para 'display', 'filter', 'sort' u 'type'
                    // 'row' contiene los datos de la fila
                    // 'meta' contiene metadatos sobre la celda, como el índice de la columna
                    
                    return row.ciutat2 + 
                    '<div style="font-size: 0.8em; color: gray;">' + 
                    row.comarca2 + ', ' +  row.provincia2 + ', ' + row.comunitat2 + ', ' + row.pais2 + 
                    '</div>';
                },
            },

            {
                targets: [3], 
                orderable: true, // Permitir ordenamiento
                render: function (data, type, row, meta) {
                    // Suponiendo que 'data' viene como '{1,2,3}'
                    let categorias = row.categoria;
                    if (categorias) {
                        // Eliminar las llaves y dividir en un array
                        let arrayDatos = categorias.replace(/[{}]/g, '').split(',').map(Number);
                        
                        // Mapeo de números a sus textos correspondientes
                        const textoMapeo = {
                            1: 'Afusellat',
                            2: 'Exiliat',
                            3: 'Mort en combat'
                        };

                        // Construir el resultado
                        let resultado = arrayDatos.map(num => textoMapeo[num] || '').filter(Boolean).join(', ');

                        return resultado;
                    }

                    return ''; // Devolver una cadena vacía si no hay datos
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
                        '<button type="button" onclick="btnModificaAfusellat('+row.id+')" id="btnModificaAfusellat" class="btn btn-sm btn-warning">Modificar dades</button>'
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

// BOTO MODIFICAR FITXA PERSONA
function btnModificaAfusellat(id) {
    let idAfusellat = id;
    let url = devDirectory + "/afusellats/fitxa/modifica/" + idAfusellat;

    // Redirigir al usuario a la página deseada
    window.location.href = url;
}
</script>

<?php
# footer
require_once(APP_ROOT . APP_DEV . '/public/php/footer.php');
?>
