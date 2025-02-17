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

                        <div class="container mt-4 ">
                            <div class="d-flex flex-column align-items-start d-grid gap-3">
                                <span class="text2">Utilitza el cercador per trobar informació sobre una persona introduint el seu nom i cognoms</span>
                                <input type="text" class="form-control mb-2" placeholder="Nom i cognoms...">
                                <button class="btn btn-primary w-auto align-self-start">Cercar</button>
                            </div>
                        </div>

                        <span class="text2" style="margin-top:30px;">Si vols fer una recerca avançada, aplicant filtres per lloc de naixement,
                            sexe, afiliació sindical i/o política, entre d'altres, clica al següent botó.</span>

                        <a href="../<?php echo empty($language) ? '' : $language . '/'; ?>recerca-represaliat" class="btn btn-primary btn-custom-2 w-auto align-self-start">Recerca avançada</a>
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
                        <a href="../<?php echo empty($language) ? '' : $language . '/'; ?>" class="btn btn-primary btn-custom-1">Cost Humà de la Guerra civil</a>
                        <a href="../<?php echo empty($language) ? '' : $language . '/'; ?>" class="btn btn-primary btn-custom-1">Exiliats i deportats</a>
                        <a href="../<?php echo empty($language) ? '' : $language . '/'; ?>" class="btn btn-primary btn-custom-1">Represaliats de la dictadura</a>
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
                        <img src="https://placehold.co/600x400/EEE/31343C" class="img-fluid">
                    </div>
                </div>
                <div class="carousel-item gap">
                    <div class="col-md-4">

                        <img src="https://placehold.co/600x400/EEE/31343C" class="img-fluid">

                    </div>
                </div>
                <div class="carousel-item gap">
                    <div class="col-md-4">

                        <img src="https://placehold.co/600x400/EEE/31343C" class="img-fluid">

                    </div>
                </div>
                <div class="carousel-item gap">
                    <div class="col-md-4">

                        <img src="https://placehold.co/600x400/EEE/31343C" class=" img-fluid">

                    </div>
                </div>
                <div class="carousel-item gap">
                    <div class="col-md-4">

                        <img src="https://placehold.co/600x400/EEE/31343C" class="img-fluid">

                    </div>
                </div>
                <div class="carousel-item gap">
                    <div class="col-md-4">

                        <img src="https://placehold.co/600x400/EEE/31343C" class="img-fluid">

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
                <h2 class="column-title">Base de dades<br>General</h2>
                <div class="hover-bg" style="background-image: url('https://memoriaterrassa.cat/src/frontend/assets/monument_caidos.jpg');"></div>
            </a>
        </div>
        <div class="col-md-3 column-hover">
            <a href="../<?php echo empty($language) ? '' : $language . '/'; ?>base-dades/cost-huma" class="full-link">
                <h2 class="column-title">Cost humà <br>de la Guerra civil</h2>
                <div class="hover-bg" style="background-image: url('https://memoriaterrassa.cat/src/frontend/assets/monument_caidos.jpg');"></div>
            </a>
        </div>
        <div class="col-md-3 column-hover">
            <a href="../<?php echo empty($language) ? '' : $language . '/'; ?>base-dades/exiliats-deportats" class="full-link">
                <h2 class="column-title">Exiliats i<br> deportats</h2>
                <div class="hover-bg" style="background-image: url('https://memoriaterrassa.cat/src/frontend/assets/monument_caidos.jpg');"></div>
            </a>

        </div>
        <div class="col-md-3 column-hover">
            <a href="../<?php echo empty($language) ? '' : $language . '/'; ?>base-dades/represaliats" class="full-link">
                <h2 class="column-title">Represaliats <br>de la dictadura</h2>
                <div class="hover-bg" style="background-image: url('https://memoriaterrassa.cat/src/frontend/assets/monument_caidos.jpg');"></div>
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
                    <img src="<?php echo APP_WEB; ?>/public/img/icon1.png" class="mb-3 w-6" alt="Imagen 1">
                    <h5 class="fw-bold">Històries personals de les represaliades i represaliats</h5>
                </div>
            </div>
            <div class="col">
                <div class="p-4">
                    <img src="<?php echo APP_WEB; ?>/public/img/icon2.png" class="mb-3 w-6" alt="Imagen 2">
                    <h5 class="fw-bold">Eines de cerca avançada per explorar arxius</h5>
                </div>
            </div>
            <div class="col">
                <div class="p-4">
                    <img src="<?php echo APP_WEB; ?>/public/img/icon3.png" class="mb-3 w-6" alt="Imagen 3">
                    <h5 class="fw-bold">Bases de dades detallades segons el tipus de repressió</h5>
                </div>
            </div>
            <div class="col">
                <div class="p-4">
                    <img src="<?php echo APP_WEB; ?>/public/img/icon4.png" class="mb-3 w-6" alt="Imagen 4">
                    <h5 class="fw-bold">Fonts documentals verificades i accessibles</h5>
                </div>
            </div>
        </div>
    </div>
</div>


<div class="container-fluid" style="margin-top:60px;background-color:#EEEAD9">
    <div class="container px-4 d-flex flex-column d-grid gap-2" style="padding-top:60px;padding-bottom:60px">
        <span class="titol gran lora negreta">Equip d'investigadors</span>
        <span class="titol italic-text gran lora">Espai Virtual de la Memòria Històrica de Terrassa</span>
        <span class="text1 mitja raleway">Qui hi ha darrere del projecte? Aquest projecte és possible gràcies a la col·laboració d'entitats i professionals dedicats a la recuperació de la memòria històrica</span>

        <div class="container my-5">
            <div class="row mt-4 gy-4 gx-4">
                <!-- Primera fila -->
                <div class="col-md-6 d-flex align-items-center px-3">
                    <div>
                        <h3 class="fw-bold">Manuel Màrquez Berrocal</h3>
                        <p>Historiador i responsable del projecte</p>
                        <a href="#" class="btn btn-primary">Veure biografia</a>
                    </div>
                    <img src="<?php echo APP_WEB; ?>/public/img/manel_marquez.png" class="rounded-circle img-petita" alt="Foto">
                </div>
                <div class="col-md-6 d-flex align-items-center px-3 border-start">
                    <div>
                        <h3 class="fw-bold">José Antonio Olivares Abad</h3>
                        <p>Historiador i responsable del projecte</p>
                        <a href="#" class="btn btn-primary">Veure biografia</a>
                    </div>
                    <img src="<?php echo APP_WEB; ?>/public/img/jose_antonio_olivares.jpg" class="rounded-circle img-petita" alt="Foto">
                </div>
            </div>

            <div class="row mt-4 gy-4 gx-4 border-top">
                <!-- Segunda fila -->
                <div class=" col-md-6 d-flex align-items-center">
                    <div>
                        <h3 class="fw-bold">José Luís Lacueva Moreno</h3>
                        <p>Historiador i responsable del projecte</p>
                        <a href="#" class="btn btn-primary">Veure biografia</a>
                    </div>
                    <img src="<?php echo APP_WEB; ?>/public/img/icon3.png" class="rounded-circle img-petita" alt="Foto">
                </div>
                <div class="col-md-6 d-flex align-items-center px-3 border-start">
                    <div>
                        <h3 class="fw-bold">Elliot Fernandez Hernandez</h3>
                        <p>Historiador i responsable del projecte</p>
                        <a href="#" class="btn btn-primary">Veure biografia</a>
                    </div>
                    <img src="<?php echo APP_WEB; ?>/public/img/icon3.png" class="rounded-circle img-petita" alt="Foto">
                </div>
            </div>
        </div>

    </div>
</div>

<style>
    .img-petita {
        width: 130px;
    }

    /* Asegurar que el enlace cubra todo el div */
    .full-link {

        text-decoration: none !important;

    }

    /* Evita que el texto cambie de color al hacer hover */
    .full-link:hover {
        text-decoration: none;
    }

    /* Estilo base de las columnas */
    .column-hover {
        background-color: #B39B7C;
        height: 460px;
        /* Altura de las columnas */
        display: flex;
        justify-content: center;
        align-items: center;
        position: relative;
        cursor: pointer;
        overflow: hidden;
    }

    /* La imagen de fondo (inicialmente oculta) */
    .hover-bg {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-size: cover;
        background-position: center;
        opacity: 0;
        /* 🔹 Ocultamos la imagen */
        transition: opacity 0.3s ease-in-out;
    }

    /* Al hacer hover, la imagen aparece suavemente */
    .column-hover:hover .hover-bg {
        opacity: 1;
    }


    /* Estilo del título */
    .column-title {
        color: white;
        font-size: 24px;
        font-weight: bold;
        text-align: center;
        background-color: rgba(0, 0, 0, 0.3);
        padding: 10px 20px;
        border-radius: 5px;
        position: relative;
        z-index: 2;
        transition: all 0.3s ease-in-out;
    }

    /* Convertir el título en un botón al hacer hover */
    .column-hover:hover .column-title {
        background-color: white;
        color: black;
        padding: 12px 24px;
        border: 2px solid black;
        border-radius: 20px;
    }
</style>

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
    .carousel-control-next-icon {
        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16' fill='%23000'%3e%3cpath d='M4.646 1.646a.5.5 0 0 1 .708 0l6 6a.5.5 0 0 1 0 .708l-6 6a.5.5 0 0 1-.708-.708L10.293 8 4.646 2.354a.5.5 0 0 1 0-.708z'/%3e%3c/svg%3e");
    }

    .carousel-control-prev-icon {
        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16' fill='%23000'%3e%3cpath d='M11.354 1.646a.5.5 0 0 0-.708 0l-6 6a.5.5 0 0 0 0 .708l6 6a.5.5 0 0 0 .708-.708L5.707 8l5.647-5.646a.5.5 0 0 0 0-.708z'/%3e%3c/svg%3e");
    }

    .carousel-control-next {
        right: -100px;
    }

    .carousel-control-prev {
        left: -100px;
    }




    @media (max-width: 767px) {
        .carousel-inner .carousel-item>div {
            display: none;
            /* Ajusta el margen entre columnas */

        }

        .carousel-inner .carousel-item>div:first-child {
            display: block;
        }
    }

    .carousel-inner .carousel-item.active,
    .carousel-inner .carousel-item-next,
    .carousel-inner .carousel-item-prev {
        display: flex;
    }

    /* medium and up screens */
    @media (min-width: 768px) {

        .carousel-inner .carousel-item-end.active,
        .carousel-inner .carousel-item-next {
            transform: translateX(25%);
        }

        .carousel-inner .carousel-item-start.active,
        .carousel-inner .carousel-item-prev {
            transform: translateX(-25%);
        }
    }

    .carousel-inner .carousel-item-end,
    .carousel-inner .carousel-item-start {
        transform: translateX(0);
    }
</style>