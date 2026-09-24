<?php
declare(strict_types=1);

namespace App\Core;

class Controller
{
    public function render(string $view, array $data = [], string $layout = 'customer'): string
    {
        extract($data, EXTR_SKIP);

        $viewFile = dirname(__DIR__, 2) . "/resources/views/{$view}.php";
        if (!file_exists($viewFile)) {
            throw new \RuntimeException("View file not found: {$viewFile}");
        }

        ob_start();
        require $viewFile;
        $content = ob_get_clean();

        $layoutFile = dirname(__DIR__, 2) . "/resources/views/layouts/{$layout}.php";
        if (!file_exists($layoutFile)) {
            return (string)$content;
        }

        ob_start();
        require $layoutFile;
        return (string)ob_get_clean();
    }

    public function json(array $data, int $statusCode = 200): void
    {
        Response::json($data, $statusCode);
    }
}
