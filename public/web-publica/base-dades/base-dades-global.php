<?php

// Obtener la ruta de la URL (sin el dominio)
$requestUri = parse_url($requestUri, PHP_URL_PATH);

// Normalizar la ruta eliminando las barras finales
$requestUri = rtrim($requestUri, '/');
// Detectar el idioma desde la URL (primer segmento después del dominio)
preg_match('#^/(fr|en|es|pt|it)#', $requestUri, $matches);
$language = $matches[1] ?? '';

// Obtener traducciones generales
$translate = $translations['benvinguda'] ?? [];
?>

<!-- Sección con container-fluid -->
<div class="container-fluid background-image-cap">

    <div class="container px-4">
        <span class="negreta gran italic-text cap">Base de dades<br>general</span>

    </div>
</div>


<div class="container d-flex flex-column" style="padding-top: 50px;padding-bottom:10px;">
    <span class="titol italic-text gran lora">Explora les històries, documents i testimonis de les persones víctimes de la Guerra Civil i represaliades pel franquisme</span>

    <span class="text1 mitja raleway" style="margin-top:20px">
        Aquesta base de dades agrupa tota la informació estructurada sobre les persones víctimes de la Guerra civil i represaliades per la repressió franquista. Pots explorar-la per categories o utilitzar la cerca avançada.</span>
</div>

<div class="container mt-4 text-center">
    <span class="titol italic-text gran lora">Selecciona una categoria per començar la teva recerca:</span>
    <div class="row g-3 justify-content-center" style="padding-top: 50px;padding-bottom:10px;">
        <div class="col-12 col-md-6 d-flex justify-content-center">
            <a class="btn-div active" href="#filtre">
                <span class="lora mitja">General</span>
            </a>
        </div>

        <div class="col-12 col-md-6 d-flex justify-content-center">
            <a class="btn-div" href="<?php echo $langCode2 === 'ca' ? '/' : '/' . $langCode2 . '/'; ?>base-dades/cost-huma/#filtre">
                <span class="lora mitja">Cost humà <br>de la Guerra Civil</span>
            </a>
        </div>

        <div class="col-12 col-md-6 d-flex justify-content-center">
            <a class="btn-div" href="<?php echo $langCode2 === 'ca' ? '/' : '/' . $langCode2 . '/'; ?>base-dades/exiliats-deportats/#filtre">
                <span class="lora mitja">Exiliats<br>i deportats</span>
            </a>
        </div>

        <div class="col-12 col-md-6 d-flex justify-content-center">
            <a class="btn-div" href="<?php echo $langCode2 === 'ca' ? '/' : '/' . $langCode2 . '/'; ?>base-dades/represaliats/#filtre">
                <span class="lora mitja">Represaliats <br>de la dictadura</span>
            </a>
        </div>
    </div>
</div>


<div class="container" id="filtre" style="margin-top: 50px;margin-bottom:50px;">

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


<style>
    .active {
        background-color: #C2AF96 !important;
        color: #133B7C !important;
    }

    .btn-div {
        width: 70%;
        padding: 20px;
        background-color: #133B7C;
        color: #C2AF96;
        border-radius: 8px;
        text-align: center;
        cursor: pointer;
        transition: background 0.3s ease-in-out;
        display: flex;
        align-items: center;
        justify-content: center;
        text-align: center;
        text-decoration: none !important;
    }

    .btn-div:hover {
        background-color: #C2AF96;
        color: #133B7C;
    }
</style>

</div>