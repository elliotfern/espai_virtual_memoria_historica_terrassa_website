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
                                <button class="btn btn-primary btn-custom-2 w-auto align-self-start">Cercar</button>
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
                        <img src="<?php echo APP_WEB; ?>/public/img/foto_memoria1.jpg" class="img-fluid">
                    </div>
                </div>
                <div class="carousel-item gap">
                    <div class="col-md-4">

                        <img src="<?php echo APP_WEB; ?>/public/img/foto_memoria2.jpg" class="img-fluid">

                    </div>
                </div>
                <div class="carousel-item gap">
                    <div class="col-md-4">

                        <img src="<?php echo APP_WEB; ?>/public/img/foto_memoria3.jpg" class="img-fluid">

                    </div>
                </div>
                <div class="carousel-item gap">
                    <div class="col-md-4">

                        <img src="<?php echo APP_WEB; ?>/public/img/foto_memoria4.jpg" class=" img-fluid">

                    </div>
                </div>
                <div class="carousel-item gap">
                    <div class="col-md-4">

                        <img src="<?php echo APP_WEB; ?>/public/img/foto_memoria5.jpg" class="img-fluid">

                    </div>
                </div>
                <div class="carousel-item gap">
                    <div class="col-md-4">

                        <img src="<?php echo APP_WEB; ?>/public/img/foto_memoria1.jpg" class="img-fluid">

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
                <div class="hover-bg" style="background-image: url('https://memoriaterrassa.cat/src/frontend/assets/monument_caidos.jpg');"></div>
            </a>
        </div>
        <div class="col-md-3 column-hover">
            <a href="../<?php echo empty($language) ? '' : $language . '/'; ?>base-dades/cost-huma" class="full-link">
                <h2 class="column-title lora">Cost humà <br>de la Guerra civil</h2>
                <div class="hover-bg" style="background-image: url('https://memoriaterrassa.cat/src/frontend/assets/monument_caidos.jpg');"></div>
            </a>
        </div>
        <div class="col-md-3 column-hover">
            <a href="../<?php echo empty($language) ? '' : $language . '/'; ?>base-dades/exiliats-deportats" class="full-link">
                <h2 class="column-title lora">Exiliats i<br> deportats</h2>
                <div class="hover-bg" style="background-image: url('https://memoriaterrassa.cat/src/frontend/assets/monument_caidos.jpg');"></div>
            </a>

        </div>
        <div class="col-md-3 column-hover">
            <a href="../<?php echo empty($language) ? '' : $language . '/'; ?>base-dades/represaliats" class="full-link">
                <h2 class="column-title lora">Represaliats <br>de la dictadura</h2>
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
                    <h5 class="lora blau2">Històries personals de les represaliades <br>i represaliats</h5>
                </div>
            </div>
            <div class="col">
                <div class="p-4">
                    <img src="<?php echo APP_WEB; ?>/public/img/icon2.png" class="mb-3 w-6" alt="Imagen 2">
                    <h5 class="lora blau2">Eines de cerca avançada<br> per explorar arxius</h5>
                </div>
            </div>
            <div class="col">
                <div class="p-4">
                    <img src="<?php echo APP_WEB; ?>/public/img/icon3.png" class="mb-3 w-6" alt="Imagen 3">
                    <h5 class="lora blau2">Bases de dades detallades<br> segons el tipus de repressió</h5>
                </div>
            </div>
            <div class="col">
                <div class="p-4">
                    <img src="<?php echo APP_WEB; ?>/public/img/icon4.png" class="mb-3 w-6" alt="Imagen 4">
                    <h5 class="lora blau2">Fonts documentals<br> verificades i accessibles</h5>
                </div>
            </div>
        </div>
    </div>
</div>


<div class="container-fluid" style="margin-top:60px;background-color:#EEEAD9">
    <div class="container px-4 d-flex flex-column d-grid gap-2" style="padding-top:60px;padding-bottom:60px">
        <span class="titol gran lora negreta">Equip d'investigadors</span>
        <span class="titol italic-text gran lora">Espai Virtual de la Memòria Històrica de Terrassa</span>
        <span class="text1 mitja raleway"><span class="negreta">Qui hi ha darrere del projecte?</span> Aquest projecte és possible gràcies a la col·laboració d'entitats i professionals dedicats a la recuperació de la memòria històrica</span>

        <div class="container my-5">
            <div class="row mt-4 gy-4 gx-4">
                <!-- Primera fila -->
                <div class="col-md-6 d-flex align-items-center px-3">
                    <div class="col-md-6 d-flex flex-column g-3">
                        <h3 class="fw-bold lora gran blau1">Manuel Màrquez <br>Berrocal</h3>
                        <span class="marro1 lora italic-text">Historiador i<br> responsable del projecte</span>
                        <a href="../<?php echo empty($language) ? '' : $language . '/'; ?>equip/manel-marquez" class="btn btn-primary btn-custom-2 w-auto align-self-start" style="margin-top:15px">Veure biografia</a>
                    </div>
                    <div class="col-md-4 d-flex align-items-center">
                        <div class="col-md-10">
                            <img src="<?php echo APP_WEB; ?>/public/img/manel_marquez.png" class="rounded-circle img-petita" alt="Foto">
                        </div>
                        <div class="col-md-2">
                            <img src="<?php echo APP_WEB; ?>/public/img/vector.png" class="img-s" alt="Foto">
                        </div>
                    </div>
                </div>

                <!-- segona fila -->
                <div class="col-md-6 d-flex align-items-center px-3 border-start">
                    <div class="col-md-6 d-flex flex-column g-3">
                        <h3 class="fw-bold lora gran blau1">Juan Antonio <br>Olivares Abad</h3>
                        <span class="marro1 lora italic-text">Historiador i<br> divulgador.</span>
                        <a href="../<?php echo empty($language) ? '' : $language . '/'; ?>equip/juan-antonio-olivares" class="btn btn-primary btn-custom-2 w-auto align-self-start" style="margin-top:15px">Veure biografia</a>
                    </div>
                    <div class="col-md-4 d-flex align-items-center">
                        <div class="col-md-10">
                            <img src="<?php echo APP_WEB; ?>/public/img/jose_antonio_olivares2.jpg" class="rounded-circle img-petita" alt="Foto">
                        </div>
                        <div class="col-md-2">
                            <img src="<?php echo APP_WEB; ?>/public/img/vector.png" class="img-s" alt="Foto">
                        </div>
                    </div>
                </div>
            </div>

            <div class="row mt-4 gy-4 gx-4 border-top">
                <!-- tercera fila -->
                <div class="col-md-6 d-flex align-items-center px-3">
                    <div class="col-md-6 d-flex flex-column g-3">
                        <h3 class="fw-bold lora gran blau1">Josep Lluís<br>Lacueva Moreno</h3>
                        <span class="marro1 lora italic-text">Historiador i<br> autor de diversos llibres d'història de Terrassa.</span>
                        <a href="../<?php echo empty($language) ? '' : $language . '/'; ?>equip/josep-lluis-lacueva" class="btn btn-primary btn-custom-2 w-auto align-self-start" style="margin-top:15px">Veure biografia</a>
                    </div>
                    <div class="col-md-4 d-flex align-items-center">
                        <div class="col-md-10">
                            <img src="<?php echo APP_WEB; ?>/public/img/josep_lluis_lacueva.jpg" class="rounded-circle img-petita" alt="Foto">
                        </div>
                        <div class="col-md-2">
                            <img src="<?php echo APP_WEB; ?>/public/img/vector.png" class="img-s" alt="Foto">
                        </div>
                    </div>
                </div>


                <!-- quarta fila -->
                <div class="col-md-6 d-flex align-items-center px-3  border-start">
                    <div class="col-md-6 d-flex flex-column g-3">
                        <h3 class="fw-bold lora gran blau1">Elliot Fernández <br>Hernández</h3>
                        <span class="marro1 lora italic-text">Historiador i<br> responsable tècnic del web.</span>
                        <a href="../<?php echo empty($language) ? '' : $language . '/'; ?>equip/elliot-fernandez" class="btn btn-primary btn-custom-2 w-auto align-self-start" style="margin-top:15px">Veure biografia</a>
                    </div>
                    <div class="col-md-4 d-flex align-items-center">
                        <div class="col-md-10">
                            <img src="<?php echo APP_WEB; ?>/public/img/elliot_fernandez2.jpg" class="rounded-circle img-petita" alt="Foto">
                        </div>
                        <div class="col-md-2">
                            <img src="<?php echo APP_WEB; ?>/public/img/vector.png" class="img-s" alt="Foto">
                        </div>
                    </div>
                </div>


            </div>
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