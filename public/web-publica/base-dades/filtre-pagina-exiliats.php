<!-- index.html -->

<style>
    #filtros-panel {
        position: sticky;
        top: 100px;
        max-height: calc(100vh - 120px);
        overflow: auto;
    }

    #tabla-resultados .fila-persona {
        padding: 10px;
        border-bottom: 1px solid #eee;
    }

    #tabla-resultados .nombre {
        font-weight: 600;
    }

    @media (max-width: 900px) {
        /* En mobile, vuelve a flujo normal (sin sticky ni scroll) */

        #filtros-panel {
            position: relative;
            width: 100%;
            max-height: none;
            height: auto;
            overflow: visible;
        }


        #resultados {
            margin-left: 0;
        }
    }

    /* Cada bloque de filtro en columna */
    #filtros .filtro-grupo {
        margin-bottom: 12px;
    }

    /* Etiquetas arriba del select */
    #filtros .filtro-grupo label {
        display: block;
        margin-bottom: 6px;
        font-weight: 600;
        font-size: 13px;
    }

    /* Choices ocupa todo el ancho del contenedor */
    #filtros .choices,
    #filtros .choices__inner {
        width: 100%;
    }

    #filtros .choices {
        display: block;
    }
</style>


<!-- Sección con container-fluid -->
<div class="container-fluid background-image-cap">

    <div class="container px-4">
        <span class="negreta gran italic-text cap">Base de dades<br>exiliats i deportats</span>

    </div>
</div>


<div class="container d-flex flex-column card-body" style="padding-top: 50px;padding-bottom:10px;">
    <span class="titol italic-text gran lora">Explora les històries, documents i testimonis</span>

    <span class="text1 mitja raleway" style="margin-top:20px">
        El llistat de les persones exiliades i deportades està format per totes les persones terrassenques (residents o nascudes a Terrassa) que van exiliar-se arran de la guerra civil i els que van ser deportats als camps de concentració de l’Alemanya nazi. El llistat inclou els familiars dels exiliats, molts dels quals tornaren poc temps després a Catalunya i Espanya.</span>

    <span class="more-text d-none text1 mitja raleway">


    </span>


    <button class="btn-toggle btn btn-primary btn-custom-2 w-auto align-self-start" style="margin-top:25px">
        llegir més
    </button>
</div>

<div class="container mt-4 text-center">
    <span class="titol italic-text gran lora">Selecciona una categoria per començar la teva recerca:</span>
    <div class="row g-3 justify-content-center" style="padding-top: 50px;padding-bottom:10px;">
        <div class="col-12 col-md-6 d-flex justify-content-center">
            <a class="btn-div" href="<?php echo $langCode2 === 'ca' ? '/' : '/' . $langCode2 . '/'; ?>base-dades/general/#filtre">
                <span class="lora mitja">General</span>
            </a>
        </div>

        <div class="col-12 col-md-6 d-flex justify-content-center">
            <a class="btn-div" href="<?php echo $langCode2 === 'ca' ? '/' : '/' . $langCode2 . '/'; ?>base-dades/cost-huma/#filtre">
                <span class="lora mitja">Cost humà <br>de la Guerra Civil</span>
            </a>
        </div>

        <div class="col-12 col-md-6 d-flex justify-content-center">
            <a class="btn-div active" href="#filtre">
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

<span id="filtre"></span>
<div class="container my-5" id="filtre">
    <div class="row g-4">
        <!-- Columna filtros -->
        <div class="col-lg-3">
            <div id="filtros-panel" class="bg-white p-3 border rounded">
                <input
                    type="text"
                    id="buscador-nom"
                    placeholder="Cerca per nom o cognoms"
                    class="form-control mb-3" />
                <h3 class="h6">Filtres</h3>
                <div id="filtros"></div>
                <div class="mt-3">
                    <button id="btn-reset" type="button" class="btn btn-outline-secondary w-100">
                        Restableix filtres
                    </button>
                </div>
            </div>
        </div>

        <!-- Columna resultados -->
        <div class="col-lg-9">
            <div id="resultados">
                <div id="exportToolbar" class="mb-2"></div>
                <h3 class="h6">Resultats</h3>
                <div id="tabla-resultados" aria-live="polite"></div>
                <div id="contador-resultados" class="text-muted mt-3"></div>
                <div id="paginacion" class="d-flex gap-2 align-items-center mt-3">
                    <button id="prevPage" class="btn btn-outline-primary btn-sm" aria-label="Anterior">
                        Anterior
                    </button>
                    <span id="pageInfo" style="min-width:120px;text-align:center"></span>
                    <button id="nextPage" class="btn btn-outline-primary btn-sm" aria-label="Següent">
                        Següent
                    </button>
                </div>
            </div>
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

<script>
    document.addEventListener("DOMContentLoaded", function() {
        document.querySelectorAll(".btn-toggle").forEach(button => {
            button.addEventListener("click", function() {
                const cardBody = this.closest(".card-body");
                const moreText = cardBody.querySelector(".more-text");

                if (moreText.classList.contains("d-none")) {
                    moreText.classList.remove("d-none");
                    this.textContent = "veure menys";
                } else {
                    moreText.classList.add("d-none");
                    this.textContent = "veure més";
                }
            });
        });
    });
</script>