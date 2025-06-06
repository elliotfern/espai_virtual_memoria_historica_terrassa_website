<?php

namespace App\Application;

use App\Domain\Common\ValueObject\Language;
use App\Application\Contract\RouterInterface;
use App\Application\Contract\ViewRendererInterface;
use App\Application\Contract\HttpResponderInterface;

class FrontController
{
    private string $requestUri;
    private RouterInterface $apiRouter;
    private RouterInterface $privateRouter;
    private RouterInterface $publicRouter;
    private Language $language;
    private ViewRendererInterface $viewRenderer;
    private HttpResponderInterface $responder;

    public function __construct(
        string $requestUri,
        RouterInterface $apiRouter,
        RouterInterface $privateRouter,
        RouterInterface $publicRouter,
        ViewRendererInterface $viewRenderer,
        HttpResponderInterface $responder
    ) {
        $this->requestUri = '/' . trim($requestUri, '/');
        $this->language = $this->detectLanguageFromUri($this->requestUri);
        $this->viewRenderer = $viewRenderer;

        // Quitar el prefijo idioma de la ruta para el router
        $this->requestUri = preg_replace('#^/' . $this->language->value() . '(?=/|$)#', '', $this->requestUri);
        if ($this->requestUri === '') {
            $this->requestUri = '/';
        }

        $this->apiRouter = $apiRouter;
        $this->privateRouter = $privateRouter;
        $this->publicRouter = $publicRouter;
        $this->responder = $responder;
    }

    public function handleRequest(): void
    {
        $requestUri = $this->requestUri;

        if (str_starts_with($requestUri, '/api')) {
            $match = $this->apiRouter->match($requestUri);
            $this->responder->respondToApiRoute($match['routeInfo'] ?? null, $match['params'] ?? []);
        } elseif (str_starts_with($requestUri, '/gestio')) {
            $match = $this->privateRouter->match($requestUri);
            $this->responder->respondToPrivateRoute($match['routeInfo'] ?? null, $match['params'] ?? []);
        } else {
            $match = $this->publicRouter->match($requestUri);
            $this->responder->respondToPublicRoute($match['routeInfo'] ?? null, $match['params'] ?? [], $this->language);
        }
    }

    public function language(): Language
    {
        return $this->language;
    }

    private function detectLanguageFromUri(string $uri): Language
    {
        if (preg_match('#^/(es|fr|en|ca|it|pt)(/|$)#', $uri, $matches)) {
            return new Language($matches[1]);
        }
        return Language::default();
    }
}
