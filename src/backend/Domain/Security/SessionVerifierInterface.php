<?php

namespace App\Domain\Security;

interface SessionVerifierInterface
{
    public function isSessionValid(): bool;
}
