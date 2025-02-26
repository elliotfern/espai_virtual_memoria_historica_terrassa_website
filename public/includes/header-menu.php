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


function getLanguageFromUrl()
{
    $uri = $_SERVER['REQUEST_URI']; // Obtiene la URL después del dominio
    $segments = explode('/', trim($uri, '/')); // Divide la URL en partes

    // Verifica si el primer segmento es un idioma válido
    $validLanguages = ['en', 'es', 'ca', 'fr', 'it', 'pt']; // Idiomas disponibles

    if (!empty($segments[0]) && in_array($segments[0], $validLanguages)) {
        return $segments[0]; // Retorna el idioma detectado
    }

    return 'ca'; // Idioma por defecto si no se detecta
}

// Uso
$langCode2 = getLanguageFromUrl();
?>

<div class="container-fluid full-screen fonsColorPrimerHeader header1">
    <div class="container">
        <div class="row align-items-center g-3 g-md-0 m-0 p-0">

            <!-- Bloque de idiomas -->
            <div class="col-4 col-md-6 d-flex justify-content-center justify-content-md-start order-1 order-md-1">

                <!-- Dropdown solo en móviles -->
                <div class="dropdown d-md-none">
                    <button class="btn btn-primary btn-custom-3 w-auto align-self-start dropdown-toggle" type="button" data-bs-toggle="dropdown">
                        Idioma
                    </button>
                    <ul class="dropdown-menu">
                        <?php foreach ($languages as $langCode => $langName): ?>
                            <li>
                                <a class="dropdown-item" href="<?php echo $langCode === 'ca' ? $baseUri : '/' . $langCode . $baseUri ?>">
                                    <?php echo $langName; ?>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>

                <!-- Lista normal en desktop -->
                <ul class="list-unstyled d-none d-md-flex flex-row gap-3 m-0 p-0">
                    <?php foreach ($languages as $langCode => $langName): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo $langCode === 'ca' ? $baseUri : '/' . $langCode . $baseUri ?>">
                                <?php echo $langName; ?>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>

            </div>

            <!-- Bloque de redes sociales -->
            <div class="col-8 col-md-6 d-flex justify-content-center justify-content-md-end order-1 order-md-2 mt-2 mt-md-0">
                <ul class="list-unstyled d-flex flex-row gap-3 m-0 p-0">
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
            <div class="col-8 col-md-4 d-flex justify-content-center justify-content-md-start">
                <a class="nav-link" href="<?php echo $langCode2 === 'ca' ? '/inici' : '/' . $langCode2 . '/inici'; ?>">
                    <img src="<?php echo APP_WEB; ?>/public/img/logo_web.png" alt="Logo" class="logoPetit">
                </a>
            </div>

            <!-- Menú Responsive -->
            <div class="col-4 col-md-8 d-flex justify-content-end">
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
                                        <li><a class="dropdown-item" href="<?php echo $langCode2 === 'ca' ? '/' : '/' . $langCode2 . '/'; ?>base-dades/general"> <?php echo $translate['general']; ?></a></li>
                                        <li><a class="dropdown-item" href="<?php echo $langCode2 === 'ca' ? '/' : '/' . $langCode2 . '/'; ?>base-dades/cost-huma"> <?php echo $translate['cost-huma']; ?></a></li>
                                        <li><a class="dropdown-item" href="<?php echo $langCode2 === 'ca' ? '/' : '/' . $langCode2 . '/'; ?>base-dades/exiliats-deportats"> <?php echo $translate['exiliats']; ?></a></li>
                                        <li><a class="dropdown-item" href="<?php echo $langCode2 === 'ca' ? '/' : '/' . $langCode2 . '/'; ?>base-dades/represaliats"> <?php echo $translate['represaliats']; ?></a></li>
                                    </ul>
                                </li>
                                <li class="nav-item"><a class="nav-link" href="<?php echo $langCode2 === 'ca' ? '/' : '/' . $langCode2 . '/'; ?>documents-estudis"> <?php echo $translate['estudis']; ?></a></li>
                                <li class="nav-item"><a class="nav-link" href="<?php echo $langCode2 === 'ca' ? '/' : '/' . $langCode2 . '/'; ?>cronologia"> <?php echo $translate['cronologia']; ?></a></li>
                                <li class="nav-item"><a class="nav-link" href="<?php echo $langCode2 === 'ca' ? '/' : '/' . $langCode2 . '/'; ?>fonts-documentals"> <?php echo $translate['documents']; ?></a></li>
                                <li class="nav-item"><a class="nav-link" href="<?php echo $langCode2 === 'ca' ? '/' : '/' . $langCode2 . '/'; ?>que-es-espai-virtual"> <?php echo $translate['espai-virtual']; ?></a></li>
                                <li class="nav-item"><a class="nav-link" href="<?php echo $langCode2 === 'ca' ? '/' : '/' . $langCode2 . '/'; ?>links"> <?php echo $translate['links']; ?></a></li>
                                <li class="nav-item"><a class="nav-link" href="<?php echo $langCode2 === 'ca' ? '/' : '/' . $langCode2 . '/'; ?>contacte"> <?php echo $translate['contacta']; ?></a></li>
                            </ul>
                        </div>
                    </div>
                </nav>
            </div>
        </div>
    </div>
</div>