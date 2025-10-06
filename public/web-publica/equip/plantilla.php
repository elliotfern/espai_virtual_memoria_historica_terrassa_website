<!-- Sección con container-fluid -->
<div id="equipRoot">
    <div class="container-fluid background-image-cap">
        <div id="equipContainer" class="container px-4">
            <span id="nom" class="negreta gran italic-text cap"></span>
            <p><span id="bio_curta" class="italic-text cap" style="font-size: 30px!important;"></span></p>
        </div>
    </div>

    <div class="container mt-4" style="margin-top: 50px;margin-bottom:50px;">
        <div class="row">
            <div class="col-md-8 raleway">
                <!-- Texto a la izquierda -->
                <span id="bio" data-render="html"></span>
            </div>

            <!-- Imagen a la derecha -->
            <div class="col-md-4 text-center">
                <img id="urlImatge" src="" class="img-fluid" alt="Usuari">
            </div>

        </div>
    </div>
</div>

<!-- El bloque 404 se crea dinámicamente si hace falta -->
<div id="error404" hidden></div>