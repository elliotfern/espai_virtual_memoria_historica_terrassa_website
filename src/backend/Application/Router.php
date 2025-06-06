<?php

namespace App\Application;

use App\Application\Contract\RouterInterface;


class Router implements RouterInterface
{
    private array $routes;

    public function __construct(array $routes)
    {
        $this->routes = $routes;
    }

    /**
     * @param string $requestUri
     * @return array [ 'routeInfo' => array|null, 'params' => array ]
     */
    public function match(string $requestUri): array
    {
        $requestUri = rtrim($requestUri, '/');
        if ($requestUri === '') {
            $requestUri = '/';
        }

        foreach ($this->routes as $routePattern => $routeInfo) {
            // Convertir patrones con parámetros {param} a regex
            $pattern = preg_replace('/\{[a-zA-Z0-9_]+\}/', '([a-zA-Z0-9_-]+)', $routePattern);
            $pattern = '#^' . $pattern . '$#';

            if (preg_match($pattern, $requestUri, $matches)) {
                // Extraer parámetros (sin el primer elemento que es la ruta completa)
                $params = array_slice($matches, 1);
                return [
                    'routeInfo' => $routeInfo,
                    'params' => $params,
                ];
            }
        }

        // Ruta no encontrada
        return [
            'routeInfo' => null,
            'params' => [],
        ];
    }
}
