<?php

function sanitizeHtml($html)
{
    static $purifier = null;

    if ($purifier === null) {
        $config = HTMLPurifier_Config::createDefault();

        // Configura las etiquetas y atributos permitidos
        $config->set('HTML.Allowed', 'p,br,b,strong,i,em,u,ul,ol,li,a[href],blockquote');

        // Elimina estilos inline peligrosos
        $config->set('CSS.AllowedProperties', []);
        $config->set('HTML.SafeIframe', false);

        // Opcional: codificación y entidades
        $config->set('Core.Encoding', 'UTF-8');
        $config->set('HTML.Doctype', 'HTML 4.01 Transitional');

        $purifier = new HTMLPurifier($config);
    }

    return $purifier->purify($html);
}
