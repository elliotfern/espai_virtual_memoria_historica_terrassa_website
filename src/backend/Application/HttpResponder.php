<?php

namespace App\Application;

use App\Infrastructure\Middleware\AuthMiddleware;
use App\Domain\Common\ValueObject\Language;
use App\Application\Services\TranslationService;
use App\Application\Contract\HttpResponderInterface;

class HttpResponder implements HttpResponderInterface
{
    private ViewRenderer $viewRenderer;
    private TranslationService $translationService;
    private AuthMiddleware $authMiddleware;

    public function __construct(ViewRenderer $viewRenderer, TranslationService $translationService, AuthMiddleware $authMiddleware)
    {
        $this->viewRenderer = $viewRenderer;
        $this->translationService = $translationService;
        $this->authMiddleware = $authMiddleware;
    }

    public function respondToPublicRoute($routeInfo, array $params, Language $language): void
    {
        $translations = $this->translationService->getAll();

        if ($routeInfo === null) {
            http_response_code(404);
            echo $translations['page_not_found'] ?? '404 - Página no encontrada';
            return;
        }

        $variables = [
            'language' => $language,
            'translations' => $translations,
        ];

        $options = [
            'header_footer' => $routeInfo['header_footer'] ?? false,
            'header_menu_footer' => $routeInfo['header_menu_footer'] ?? false,
        ];

        $data = [
            'variables' => $variables,
            'options' => $options,
            'viewPath' => $routeInfo['view'],
        ];

        echo $this->viewRenderer->render($routeInfo['view'], $data);
    }

    public function respondToPrivateRoute($routeInfo, array $params): void
    {
        if ($routeInfo === null) {
            http_response_code(404);
            echo 'page_not_found' ?? '404 - Página no encontrada';
            return;
        }

        $this->authMiddleware->handle();

        $variables = $params;
        $options = [
            'header_footer' => $routeInfo['header_footer'] ?? false,
            'header_menu_footer' => $routeInfo['header_menu_footer'] ?? false,
        ];

        $data = [
            'variables' => $variables,
            'options' => $options,
            'viewPath' => $routeInfo['view'],
        ];

        echo $this->viewRenderer->render($routeInfo['view'], $data);
    }

    public function respondToApiRoute($routeInfo, array $params): void
    {
        header('Content-Type: application/json; charset=utf-8');

        if ($routeInfo === null) {
            http_response_code(404);
            echo json_encode(['error' => 'API endpoint not found']);
            return;
        }

        $needsAuth = $routeInfo['needs_auth'] ?? false;
        if ($needsAuth) {
            $this->authMiddleware->handle();
        }

        $variables = $params;

        $viewPath = __DIR__ . '/../../../' . $routeInfo['view'];

        if (file_exists($viewPath)) {
            extract($variables);
            include $viewPath;
        } else {
            http_response_code(404);
            echo json_encode(['error' => 'API handler not found']);
        }
    }
}
