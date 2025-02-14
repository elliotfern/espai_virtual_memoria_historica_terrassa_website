<?php

// Función para generar rutas específicas por idioma
function generateLanguageRoutes(array $base_routes, bool $use_languages = true): array
{

    $languages = ['es', 'fr', 'en', 'ca', 'it', 'pt']; // Idiomas soportados
    $default_language = 'ca'; // Idioma por defecto
    $routes = [];

    // Si no quieres rutas por idioma, solo usa las rutas base sin prefijo
    if (!$use_languages) {
        return $base_routes;
    }

    // Genera las rutas para cada idioma
    foreach ($languages as $lang) {
        foreach ($base_routes as $path => $view) {
            // Se crean las rutas con el prefijo de idioma (por ejemplo, /fr/, /en/, /ca/)
            if ($lang === $default_language) {
                // La ruta raíz para el idioma por defecto se mantiene como está
                $routes[$path] = [
                    'view' => $view,
                    'needs_session' => false,
                    'header_footer' => false,
                    'header_menu_footer' => true
                ];
            } else {
                // Las rutas para otros idiomas tendrán el prefijo de idioma (ej. /fr/, /en/)
                $routes["/{$lang}{$path}"] = [
                    'view' => $view,
                    'needs_session' => false,
                    'header_footer' => false,
                    'header_menu_footer' => true
                ];
            }
        }
    }

    return $routes;
}
