<?php

/**
 * @param string $viewName
 * @param array{
 *   viewPath?: string,
 *   variables?: array,
 *   options?: array{header_footer?: bool, header_menu_footer?: bool, header_private?: bool}
 * } $data
 * @param Language|null $language
 * @return string
 */

namespace App\Application;

use App\Application\Contract\ViewRendererInterface;
use App\Domain\Common\ValueObject\Language;

class ViewRenderer implements ViewRendererInterface
{
    private string $basePath;

    public function __construct(string $basePath)
    {
        $this->basePath = rtrim($basePath, '/');
    }

    public function render(string $viewName, array $data = [], ?Language $language = null): string
    {
        // Extraemos las variables del array de datos
        $variables = $data['variables'] ?? [];
        $options   = $data['options'] ?? [];
        $viewPath  = $data['viewPath'] ?? $viewName; // por si lo pasas separado

        ob_start();

        extract($variables);

        $headerFooter = $options['header_footer'] ?? false;
        $headerMenuFooter = $options['header_menu_footer'] ?? false;

        if ($headerFooter) {
            include $this->basePath . '/public/includes/header.php';
        } elseif ($headerMenuFooter) {
            include $this->basePath . '/public/includes/header.php';
            include $this->basePath . '/public/includes/header-menu.php';
            if (!empty($options['header_private'])) {
                include $this->basePath . '/public/includes/header-private.php';
            }
        }

        include $this->basePath . '/' . $viewPath;

        if ($headerFooter || $headerMenuFooter) {
            include $this->basePath . '/public/includes/footer.php';
            include $this->basePath . '/public/includes/footer-end.php';
        }

        return ob_get_clean();
    }
}
