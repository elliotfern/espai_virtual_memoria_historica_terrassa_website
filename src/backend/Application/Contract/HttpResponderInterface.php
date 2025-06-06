<?php

namespace App\Application\Contract;

use App\Domain\Common\ValueObject\Language;

interface HttpResponderInterface
{
    public function respondToApiRoute($routeInfo, array $params): void;

    public function respondToPrivateRoute($routeInfo, array $params): void;

    public function respondToPublicRoute($routeInfo, array $params, Language $language): void;
}
