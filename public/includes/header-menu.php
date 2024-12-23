<div class="container-fluid full-screen fonsColor">
    <div class="container" style="padding-top: 20px;padding-bottom: 20px;">
        <div class="row">
            <div class="col-2">
                <a href="<?php echo APP_WEB; ?>/inici"> <img src="<?php echo APP_WEB; ?>/public/img/logo-petit.png" alt="Logo" class="logoPetit"></a>
            </div>
            <div class="col-10 d-flex justify-content-end align-items-end">
                <nav class="menu">
                    <ul class="menu-list d-flex mb-0">
                        <li class="menu-item"><a href="<?php echo APP_WEB; ?>/inici">Inici</a></li>
                        <li class="menu-item"><a href="<?php echo APP_WEB; ?>/represaliats">Represaliats 1939/79</a></li>
                        <li class="menu-item"><a href="<?php echo APP_WEB; ?>/exiliats">Exili</a></li>
                        <li class="menu-item"><a href="<?php echo APP_WEB; ?>/cost-huma">Cost humà de la guerra</a></li>
                    </ul>
                </nav>
            </div>
        </div>
    </div>
</div>

<style>
    .fonsColor {
        background-color: #C2AF96B2;
    }

    .logoPetit {
        /* Asegura que el logo esté encima de la capa azul */
        width: 200px;
        /* Ajusta el tamaño del logo */
        height: auto;
        /* Mantiene la proporción del logo */
    }

    .menu-list {
        list-style: none;
        /* Elimina los puntos de la lista */
        padding: 0;
        margin: 0;
    }

    .menu-item {
        margin-right: 20px;
        /* Espaciado entre los elementos */
    }

    .menu-item:last-child {
        margin-right: 0;
        /* Sin espaciado para el último elemento */
    }

    .menu-item a {
        text-decoration: none;
        /* Sin subrayado en los enlaces */
        color: #000;
        /* Color de los enlaces (puedes ajustarlo) */
        font-weight: bold;
        /* Negrita opcional */
    }

    .menu-item a:hover {
        color: #007bff;
        /* Cambia el color al pasar el mouse */
    }
</style>