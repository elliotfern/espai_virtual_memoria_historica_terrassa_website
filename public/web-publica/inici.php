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
<div class="container-fluid bg-image2">

    <div class="container px-4">
        <div class="row gx-5 gy-3">
            <div class="col">
                <div class="p-4 border bloc1">
                    <span class="titol">Cerca bàsica de <br> <span class="italic-text">represaliats i represaliades</span></span>
                    <div class="d-flex flex-column d-grid gap-3" style="margin-top:20px;margin-bottom:20px">

                        <div class="container mt-4">
                            <div class="position-relative">
                                <input id="searchInput" type="text" class="form-control mb-2" placeholder="Nom i cognoms...">
                                <div id="results" class="search-results"></div>
                            </div>


                        </div>


                        <span class="text2" style="margin-top:30px;">Si vols fer una cerca avançada, aplicant filtres per lloc de naixement,
                            sexe, afiliació sindical i/o política, entre d'altres, clica al següent botó.</span>

                        <a href="../<?php echo empty($language) ? '' : $language . '/'; ?>cerca-represaliat" class="btn btn-primary btn-custom-2 w-auto align-self-start">Cerca avançada</a>
                    </div>

                </div>
            </div>
            <div class="col">
                <div class="p-4 border bloc2">
                    <span class="titol">Explora les nostres <br>
                        <span class="italic-text">bases de dades</span></span>

                    <div class="d-flex flex-column d-grid gap-3" style="margin-top:40px;margin-bottom:20px">
                        <span class="text3">
                            Consulta les bases de dades de l'Espai Virtual de la Memòria Històrica de Terrassa, on podràs trobar el llistat complet de represaliats i fer cerques avançades.</span>

                        <a href="../<?php echo empty($language) ? '' : $language . '/'; ?>base-dades/general" class="btn btn-primary btn-custom-1">General</a>
                        <a href="../<?php echo empty($language) ? '' : $language . '/'; ?>base-dades/cost-huma" class="btn btn-primary btn-custom-1">Cost Humà de la Guerra civil</a>
                        <a href="../<?php echo empty($language) ? '' : $language . '/'; ?>base-dades/exiliats-deportats" class="btn btn-primary btn-custom-1">Exiliats i deportats</a>
                        <a href="../<?php echo empty($language) ? '' : $language . '/'; ?>base-dades/represaliats" class="btn btn-primary btn-custom-1">Represaliats de la dictadura</a>
                    </div>


                </div>
            </div>
        </div>
    </div>


</div>

<div class="container px-4 d-flex flex-column d-grid gap-2" style="margin-top:60px;margin-bottom:60px">
    <span class="titol gran lora negreta">Espai virtual de la Memòria Històrica de Terrassa</span>
    <span class="titol italic-text gran lora">Descobreix les històries, dades<br> i testimonis que conformen<br> la memòria històrica.</span>
    <span class="text1 mitja raleway">L'Espai Virtual de la Memòria Històrica de Terrassa (EVMHT) és un espai històric, documental, educatiu i d'investigació
        que recull el cost humà de la lluita per la llibertat dels terrassencs i terrassenques entre 1936 i 1983.</span>
    <a href="../<?php echo empty($language) ? '' : $language . '/'; ?>que-es-espai-virtual" class="btn btn-primary btn-custom-2 w-auto align-self-start">Coneix més sobre l'Espai Virtual</a>
</div>

<div class="container text-center my-3">
    <div class="row mx-auto my-auto justify-content-cen.carousel-control-next-icon">
        <div id="recipeCarousel" class="carousel slide " data-bs-ride="carousel">
            <div class="carousel-inner" role="listbox">
                <div class="carousel-item active gap">
                    <div class="col-md-4">
                        <img src="<?php echo IMG_DOMAIN; ?>/assets_web/foto_memoria1.jpg" class="img-fluid">
                    </div>
                </div>
                <div class="carousel-item gap">
                    <div class="col-md-4">

                        <img src="<?php echo IMG_DOMAIN; ?>/assets_web/foto_memoria2.jpg" class="img-fluid">

                    </div>
                </div>
                <div class="carousel-item gap">
                    <div class="col-md-4">

                        <img src="<?php echo IMG_DOMAIN; ?>/assets_web/foto_memoria3.jpg" class="img-fluid">

                    </div>
                </div>
                <div class="carousel-item gap">
                    <div class="col-md-4">

                        <img src="<?php echo IMG_DOMAIN; ?>/assets_web/foto_memoria4.jpg" class=" img-fluid">

                    </div>
                </div>
                <div class="carousel-item gap">
                    <div class="col-md-4">

                        <img src="<?php echo IMG_DOMAIN; ?>/assets_web/foto_memoria5.jpg" class="img-fluid">

                    </div>
                </div>
                <div class="carousel-item gap">
                    <div class="col-md-4">

                        <img src="<?php echo IMG_DOMAIN; ?>/assets_web/foto_memoria1.jpg" class="img-fluid">

                    </div>
                </div>
            </div>
            <a class="carousel-control-prev bg-transparent w-aut" href="#recipeCarousel" role="button" data-bs-slide="prev">
                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            </a>
            <a class="carousel-control-next bg-transparent w-aut" href="#recipeCarousel" role="button" data-bs-slide="next">
                <span class="carousel-control-next-icon" aria-hidden="true"></span>
            </a>
        </div>
    </div>

</div>

<hr>

<div class="container px-4 d-flex flex-column d-grid gap-2" style="margin-top:60px;margin-bottom:60px">
    <span class="titol gran lora negreta">Explora els nostres recursos</span>
    <span class="titol italic-text gran lora">Consulta i interactua amb bases de dades que inclouen fitxes<br> individuals, documents, i estudis històrics</span>
</div>

<div class="container-fluid" style="margin-bottom:60px">
    <div class="row">
        <div class="col-md-3 column-hover">
            <a href="../<?php echo empty($language) ? '' : $language . '/'; ?>base-dades/general" class="full-link">
                <h2 class="column-title lora">Base de dades<br>General</h2>
                <div class="hover-bg" style="background-image: url('https://media.memoriaterrassa.cat/assets_web/monument_caidos.jpg');"></div>
            </a>
        </div>
        <div class="col-md-3 column-hover">
            <a href="../<?php echo empty($language) ? '' : $language . '/'; ?>base-dades/cost-huma" class="full-link">
                <h2 class="column-title lora">Cost humà <br>de la Guerra civil</h2>
                <div class="hover-bg" style="background-image: url('https://media.memoriaterrassa.cat/assets_web/monument_caidos.jpg');"></div>
            </a>
        </div>
        <div class="col-md-3 column-hover">
            <a href="../<?php echo empty($language) ? '' : $language . '/'; ?>base-dades/exiliats-deportats" class="full-link">
                <h2 class="column-title lora">Exiliats i<br> deportats</h2>
                <div class="hover-bg" style="background-image: url('https://media.memoriaterrassa.cat/assets_web/monument_caidos.jpg');"></div>
            </a>

        </div>
        <div class="col-md-3 column-hover">
            <a href="../<?php echo empty($language) ? '' : $language . '/'; ?>base-dades/represaliats" class="full-link">
                <h2 class="column-title lora">Represaliats <br>de la dictadura</h2>
                <div class="hover-bg" style="background-image: url('https://media.memoriaterrassa.cat/assets_web/monument_caidos.jpg');"></div>
            </a>
        </div>
    </div>
</div>
</div>

<hr>

<div class="container px-4 d-flex flex-column d-grid gap-2" style="margin-top:0px;margin-bottom:60px">
    <span class="titol gran lora negreta">Recursos interactius</span>

    <div class="container py-5">
        <div class="row row-cols-2 g-4 text-center">
            <div class="col">
                <div class=" p-4">
                    <img src="<?php echo IMG_DOMAIN; ?>/assets_web/icon1.png" class="mb-3 w-6" alt="Imagen 1">
                    <h5 class="lora blau2">Històries personals de les represaliades <br>i represaliats</h5>
                </div>
            </div>
            <div class="col">
                <div class="p-4">
                    <img src="<?php echo IMG_DOMAIN; ?>/assets_web/icon2.png" class="mb-3 w-6" alt="Imagen 2">
                    <h5 class="lora blau2">Eines de cerca avançada<br> per explorar arxius</h5>
                </div>
            </div>
            <div class="col">
                <div class="p-4">
                    <img src="<?php echo IMG_DOMAIN; ?>/assets_web/icon3.png" class="mb-3 w-6" alt="Imagen 3">
                    <h5 class="lora blau2">Bases de dades detallades<br> segons el tipus de repressió</h5>
                </div>
            </div>
            <div class="col">
                <div class="p-4">
                    <img src="<?php echo IMG_DOMAIN; ?>/assets_web/icon4.png" class="mb-3 w-6" alt="Imagen 4">
                    <h5 class="lora blau2">Fonts documentals<br> verificades i accessibles</h5>
                </div>
            </div>
        </div>
    </div>
</div>


<div class="container-fluid" style="margin-top:60px;background-color:#EEEAD9">
    <div class="container px-4 d-flex flex-column d-grid gap-2" style="padding-top:60px;padding-bottom:60px">
        <span class="titol gran lora negreta">Equip d'investigadors i tècnics del web</span>
        <span class="titol italic-text gran lora">Espai Virtual de la Memòria Històrica de Terrassa</span>
        <span class="text1 mitja raleway"><span class="negreta">Qui hi ha darrere del projecte?</span> Aquest projecte és possible gràcies a la col·laboració d'entitats i professionals dedicats a la recuperació de la memòria històrica</span>

        <div class="container my-5">
            <span class="titol italic-text gran lora">Membres recerca històrica:</span>
            <div id="equipLlistaRoot"></div>
        </div>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        let items = document.querySelectorAll(".carousel .carousel-item");

        items.forEach((el) => {
            const minPerSlide = 3; // Número de elementos por slide
            let next = el.nextElementSibling;

            for (let i = 1; i < minPerSlide; i++) {
                if (!next) {
                    next = items[0]; // Si llega al final, vuelve al inicio
                }

                let cloneChild = next.firstElementChild.cloneNode(true);
                el.appendChild(cloneChild); // Agregar el contenido al slide actual
                next = next.nextElementSibling;
            }
        });
    });
</script>

<style>
    #results {
        font-family: Lora;
        font-size: 15px;
    }

    #results a:link,
    #results a:visited,
    #results a:hover {
        text-decoration: underline !important;
    }

    #results .avis {
        color: #d9cdbd !important;
        font-family: Lora;
    }

    .search-results {
        position: absolute;
        top: 100%;
        /* justo debajo del input */
        left: 0;
        width: 100%;
        max-height: 300px;
        overflow-y: auto;
        background: #fff;
        border: 1px solid #ccc;
        z-index: 1050;
        /* Asegúrate de que esté por encima del resto del contenido */
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        border-radius: 4px;
    }
</style>