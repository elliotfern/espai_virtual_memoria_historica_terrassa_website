<?php

namespace App\Application;

use App\Infrastructure\Middleware\AuthMiddleware;

class FrontController
{
    private string $requestUri;
    private string $language = 'ca';
    private Router $apiRouter;
    private Router $privateRouter;
    private Router $publicRouter;
    private AuthMiddleware $authMiddleware;

    public function __construct(
        string $requestUri,
        ?string $lang,
        Router $apiRouter,
        Router $privateRouter,
        Router $publicRouter,
        AuthMiddleware $authMiddleware
    ) {
        $this->requestUri = '/' . trim($requestUri, '/');
        $this->language = $this->extractLanguageFromUri($this->requestUri) ?? 'ca';
        $this->language = $this->extractLanguageFromUri($this->requestUri) ?? 'ca';

        // Quitar el prefijo idioma de la ruta para el router
        if ($this->language !== null) {
            $this->requestUri = preg_replace('#^/' . $this->language . '(?=/|$)#', '', $this->requestUri);
            if ($this->requestUri === '') {
                $this->requestUri = '/';
            }
        }
        $this->apiRouter = $apiRouter;
        $this->privateRouter = $privateRouter;
        $this->publicRouter = $publicRouter;
        $this->authMiddleware = $authMiddleware;
    }

    public function handleRequest()
    {
        $requestUri = $this->requestUri;

        if (str_starts_with($requestUri, '/api')) {
            $match = $this->apiRouter->match($requestUri);
            $this->handleApiRoute($match);
        } elseif (str_starts_with($requestUri, '/gestio')) {
            $this->authMiddleware->handle();
            $match = $this->privateRouter->match($requestUri);
            $this->handlePrivateRoute($match);
        } else {
            $match = $this->publicRouter->match($requestUri);
            $this->handlePublicRoute($match);
        }
    }

    private function handleApiRoute(array $match): void
    {
        header('Content-Type: application/json; charset=utf-8');

        if ($match['routeInfo'] === null) {
            http_response_code(404);
            echo json_encode(['error' => 'API endpoint not found']);
            return;
        }

        $routeInfo = $match['routeInfo'];
        $routeParams = $match['params'] ?? [];

        // Verificar autenticación si la ruta la requiere
        $needsAuth = $routeInfo['needs_auth'] ?? false;
        if ($needsAuth) {
            // Usamos directamente el middleware para no repetir lógica
            $this->authMiddleware->handle();
        }

        $viewPath = __DIR__ . '/../../../' . $routeInfo['view'];

        if (file_exists($viewPath)) {
            extract($routeParams);
            include $viewPath;
        } else {
            http_response_code(404);
            echo json_encode(['error' => 'API handler not found']);
        }
    }

    private function handlePrivateRoute(array $match): void
    {
        $translationsFile = __DIR__ . "/../locales/{$this->language}.php";
        $translations = file_exists($translationsFile) ? require $translationsFile : require __DIR__ . '/../locales/ca.php';

        if ($match['routeInfo'] === null) {
            http_response_code(404);
            echo $translations['page_not_found'] ?? '404 - Página no encontrada';
            return;
        }

        $routeInfo = $match['routeInfo'];

        $includeHeaderFooter = $routeInfo['header_footer'] ?? false;
        $includeHeaderMenuFooter = $routeInfo['header_menu_footer'] ?? false;

        if ($includeHeaderFooter) {
            include __DIR__ . '/../../../public/includes/header.php';
        } elseif ($includeHeaderMenuFooter) {
            include __DIR__ . '/../../../public/includes/header.php';
            include __DIR__ . '/../../../public/includes/header-menu.php';
            include __DIR__ . '/../../../public/includes/header-private.php';
        }

        include __DIR__ . '/../../../' . $routeInfo['view'];

        if ($includeHeaderFooter || $includeHeaderMenuFooter) {
            include __DIR__ . '/../../../public/includes/footer.php';
            include __DIR__ . '/../../../public/includes/footer-end.php';
        }
    }

    private function handlePublicRoute(array $match): void
    {
        $translationsFile = __DIR__ . "/../locales/{$this->language}.php";
        $translations = file_exists($translationsFile) ? require $translationsFile : require __DIR__ . '/../locales/ca.php';

        if ($match['routeInfo'] === null) {
            http_response_code(404);
            echo $translations['page_not_found'] ?? '404 - Página no encontrada';
            return;
        }

        $routeInfo = $match['routeInfo'];

        $headerFooter = $routeInfo['header_footer'] ?? false;
        $headerMenuFooter = $routeInfo['header_menu_footer'] ?? false;

        // Pasar variables a las vistas
        $language = $this->language;         // para <html lang="...">
        $translations = $translations;       // para textos traducidos

        if ($headerFooter) {
            include __DIR__ . '/../../../public/includes/header.php';
        } elseif ($headerMenuFooter) {
            include __DIR__ . '/../../../public/includes/header.php';
            include __DIR__ . '/../../../public/includes/header-menu.php';
        }

        include __DIR__ . '/../../../' . $routeInfo['view'];

        if ($headerFooter || $headerMenuFooter) {
            include __DIR__ . '/../../../public/includes/footer.php';
            include __DIR__ . '/../../../public/includes/footer-end.php';
        }
    }

    private function extractLanguageFromUri(string $uri): ?string
    {
        if (preg_match('#^/(es|fr|en|ca|it|pt)(/|$)#', $uri, $matches)) {
            return $matches[1];
        }
        return null;
    }
}
