<?php

namespace App\Application\Security;

use App\Domain\Security\SessionVerifierInterface;

class CheckSessionUseCase
{
    public function __construct(private SessionVerifierInterface $verifier) {}

    public function execute(): bool
    {
        return $this->verifier->isSessionValid();
    }
}
