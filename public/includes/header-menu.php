<?php

// Obtener traducciones generales
$translate = $translations['header'] ?? [];

// Obtener la ruta de la URL (sin el dominio)
$requestUri = parse_url($requestUri, PHP_URL_PATH);

// Normalizar la ruta eliminando las barras finales
$requestUri = rtrim($requestUri, '/');
// Detectar el idioma desde la URL (primer segmento después del dominio)
preg_match('#^/(fr|en|es|pt|it)#', $requestUri, $matches);
$language = $matches[1] ?? '';

// Obtener el idioma actual desde la cookie
$currentLanguage = $language;  // Si no está establecido, por defecto 'es'

// Idiomas disponibles
$languages = [
    'ca' => $translate['catala'],
    'es' => $translate['espanyol'],
    'en' => $translate['angles'],
    'fr' => $translate['frances'],
    'it' => $translate['italia'],
    'pt' => $translate['portugues'],
];

// Obtener la URL actual sin el idioma (comenzando desde el primer segmento después de la raíz)
$currentUri = $_SERVER['REQUEST_URI'];

// Eliminar el idioma actual de la URL (por ejemplo, de '/es', '/fr', '/en', '/ca')
$baseUri = preg_replace('#^/(fr|en|es|it|pt)/#', '/', $currentUri);

$base_url = ($currentLanguage === 'ca') ? '/' : "/$currentLanguage/";

// Si el idioma actual es español, no agregar el prefijo '/ca'
if ($currentLanguage === 'ca') {
    // Si el idioma es español, la URL debe ser simplemente '/inici' o cualquier página sin el prefijo '/ca'
    $baseUri = preg_replace('#^/ca/#', '/', $currentUri);
}

?>

<div class="container-fluid full-screen fonsColorPrimerHeader header1">
    <div class="container py-2">
        <div class="row">
            <div class="col-6">
                <ul class="d-flex list-unstyled gap-3 m-0 p-0">
                    <?php foreach ($languages as $langCode => $langName): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo $langCode === 'ca' ? $baseUri : '/' . $langCode . $baseUri ?>">
                                <?php echo $langName; ?>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>

            <div class="col-6 float-end">
                <ul class="d-flex list-unstyled gap-3 m-0 p-0 float-end">

                    <li class="nav-item">
                        <a class="nav-link" href="https://bsky.app/profile/terrassamemoria.bsky.social" target="_blank">
                            <img src="<?php echo APP_WEB; ?>/public/img/bluesky.png" alt="Bluesky" class="logoXarxes">
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link" href="https://x.com/terrassaMemoria" target="_blank">
                            <img src="<?php echo APP_WEB; ?>/public/img/x2.png" alt="X" class="logoXarxes">
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link" href="https://bsky.app/profile/terrassamemoria.bsky.social" target="_blank">
                            <img src="<?php echo APP_WEB; ?>/public/img/mastodon.png" alt="Mastodon" class="logoXarxes">
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link" href="" target="_blank">
                            <img src="<?php echo APP_WEB; ?>/public/img/instagram2.png" alt="Instagram" class="logoXarxes">
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link" href="" target="_blank">
                            <img src="<?php echo APP_WEB; ?>/public/img/linkedin2.png" alt="Linkedin" class="logoXarxes">
                        </a>
                    </li>



                </ul>
            </div>

        </div>
    </div>
</div>

<div class="container-fluid full-screen fonsColorSegonHeader header2">
    <div class="container py-3">
        <div class="row">
            <!-- Logo -->
            <div class="col-4">
                <a href="../<?php echo empty($language) ? '' : $language . '/'; ?>inici">
                    <img src="<?php echo APP_WEB; ?>/public/img/logo_web.png" alt="Logo" class="logoPetit">
                </a>
            </div>

            <!-- Menú Responsive -->
            <!-- Menú Responsive -->
            <div class="col-8 d-flex justify-content-end">
                <nav class="navbar navbar-expand-lg w-100">
                    <button class="navbar-toggler ms-auto" type="button" data-bs-toggle="offcanvas" data-bs-target="#menuNav" aria-controls="menuNav" aria-expanded="false" aria-label="Toggle navigation">
                        <span class="navbar-toggler-icon"></span>
                    </button>

                    <!-- Menú Offcanvas -->
                    <div class="offcanvas offcanvas-end" tabindex="-1" id="menuNav">
                        <div class="offcanvas-header">
                            <h5 class="offcanvas-title">Menú</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
                        </div>
                        <div class="offcanvas-body">
                            <ul class="navbar-nav w-100 d-flex justify-content-between">
                                <li class="nav-item dropdown">
                                    <a class="nav-link dropdown-toggle" href="#" id="submenuEstudis" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                        <?php echo $translate['base-dades']; ?>
                                    </a>
                                    <ul class="dropdown-menu" aria-labelledby="submenuEstudis">
                                        <li><a class="dropdown-item" href="../<?php echo empty($language) ? '' : $language . '/'; ?>base-dades/general"> <?php echo $translate['general']; ?></a></li>
                                        <li><a class="dropdown-item" href="../<?php echo empty($language) ? '' : $language . '/'; ?>base-dades/cost-huma"> <?php echo $translate['cost-huma']; ?></a></li>
                                        <li><a class="dropdown-item" href="../<?php echo empty($language) ? '' : $language . '/'; ?>base-dades/exiliats-deportats"> <?php echo $translate['exiliats']; ?></a></li>
                                        <li><a class="dropdown-item" href="../<?php echo empty($language) ? '' : $language . '/'; ?>base-dades/represaliats"> <?php echo $translate['represaliats']; ?></a></li>
                                    </ul>
                                </li>
                                <li class="nav-item"><a class="nav-link" href="../<?php echo empty($language) ? '' : $language . '/'; ?>documents-estudis"> <?php echo $translate['estudis']; ?></a></li>
                                <li class="nav-item"><a class="nav-link" href="../<?php echo empty($language) ? '' : $language . '/'; ?>fonts-documentals"> <?php echo $translate['documents']; ?></a></li>
                                <li class="nav-item"><a class="nav-link" href="../<?php echo empty($language) ? '' : $language . '/'; ?>que-es-espai-virtual"> <?php echo $translate['espai-virtual']; ?></a></li>
                                <li class="nav-item"><a class="nav-link" href="../<?php echo empty($language) ? '' : $language . '/'; ?>contacta"> <?php echo $translate['contacta']; ?></a></li>
                            </ul>
                        </div>
                    </div>
                </nav>
            </div>
        </div>
    </div>
</div>

<style>
    .fonsColorPrimerHeader {
        background-color: #B39B7C;
    }

    .fonsColorSegonHeader {
        background-color: #c5c3c0b2;
    }

    .header1 {
        padding-top: 5px;
        padding-bottom: 5px;
    }

    .header2 {
        padding-top: 30px;
        padding-bottom: 30px;
    }

    .navbar-nav .nav-link {
        color: #133B7C !important;
        font-family: 'Raleway';
        font-size: 18px;
        text-transform: none;
        font-weight: 500;
        text-decoration: none;
        transition: color 0.3s ease-in-out, text-decoration 0.3s ease-in-out;
    }

    .navbar-nav .nav-link:hover {
        color: #B39B7C !important;
        /* Cambia el color al pasar el ratón */
        text-decoration: underline;
        /* Subrayado al pasar el mouse */
    }

    .logoXarxes {
        width: 100%;
        max-width: 25px;
        height: auto;
    }

    @media (max-width: 777px) {
        .logoPetit {
            width: 100%;
            max-width: 200px;
            height: auto;
        }

        .logosFooter {
            width: 100%;
            max-width: 200px;
            height: auto;
        }

        .logosFooter2 {
            width: 100%;
            max-width: 90px;
            height: auto;
        }
    }

    @media (min-width: 992px) {
        .logoPetit {
            width: 347px !important;
            max-width: 347px !important;
            height: auto;
        }

        .logosFooter {
            width: 100%;
            max-width: 200px;
            height: auto;
        }

        .logosFooter2 {
            width: 100%;
            max-width: 90px;
            height: auto;
        }
    }
</style>