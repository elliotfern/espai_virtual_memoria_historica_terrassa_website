<?php

namespace App\Application\Contract;

use App\Domain\Common\ValueObject\Language;

interface ViewRendererInterface
{
    public function render(string $viewName, array $data = [], ?Language $language = null): string;
}
