<?php

namespace App\Application\Contract;

interface RouterInterface
{
    /**
     * Devuelve la ruta y los parámetros coincidentes.
     *
     * @param string $uri
     * @return array{routeInfo: mixed|null, params: array}
     */
    public function match(string $uri): array;
}
