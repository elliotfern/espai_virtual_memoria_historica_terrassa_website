<?php

namespace App\Infrastructure\Middleware;

use App\Application\Security\CheckSessionUseCase;

class AuthMiddleware
{
    private CheckSessionUseCase $checkSessionUseCase;

    public function __construct(CheckSessionUseCase $checkSessionUseCase)
    {
        $this->checkSessionUseCase = $checkSessionUseCase;
    }

    public function handle(): void
    {
        if (!$this->checkSessionUseCase->execute()) {
            // Redirigir si no hay sesión válida
            header('Location: /acces');
            exit;
        }
        // Si está autorizado, simplemente sigue
    }
}
