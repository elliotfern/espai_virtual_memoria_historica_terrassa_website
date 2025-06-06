<?php

namespace App\Infrastructure\Security;

class SessionManager
{
    private CheckSessionUseCase $checkSessionUseCase;

    public function __construct(CheckSessionUseCase $checkSessionUseCase)
    {
        $this->checkSessionUseCase = $checkSessionUseCase;
    }

    public function verificarSesion(): bool
    {
        return $this->checkSessionUseCase->execute();
    }
}
