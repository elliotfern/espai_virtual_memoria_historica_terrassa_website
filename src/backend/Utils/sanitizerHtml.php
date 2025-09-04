<?php

function sanitizeTrixHtml(?string $html): ?string
{
    if ($html === null) return null;

    // Cache de HTMLPurifier
    $cacheDir = __DIR__ . '/../var/cache/htmlpurifier';
    if (!is_dir($cacheDir)) {
        @mkdir($cacheDir, 0775, true);
        if (!is_dir($cacheDir)) {
            $cacheDir = sys_get_temp_dir();
        }
    }

    $config = \HTMLPurifier_Config::createDefault();
    $config->set('Core.Encoding', 'UTF-8');
    $config->set('Cache.SerializerPath', $cacheDir);

    // Mantener párrafos/separadores de Trix
    $config->set('AutoFormat.RemoveEmpty', false);
    $config->set('AutoFormat.RemoveEmpty.RemoveNbsp', false);

    // Whitelist mínima (negrita, cursiva, listas y párrafos/saltos)
    $config->set('HTML.Allowed', 'p,div,br,ul,ol,li,strong,em,b,i');

    // Sin estilos ni atributos
    $config->set('CSS.AllowedProperties', []);
    $config->set('Attr.AllowedClasses', []);
    $config->set('Attr.EnableID', false);

    $purifier = new \HTMLPurifier($config);
    $clean = trim($purifier->purify($html));

    return $clean === '' ? null : $clean;
}
