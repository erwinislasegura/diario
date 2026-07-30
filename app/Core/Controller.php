<?php
declare(strict_types=1);

namespace App\Core;

abstract class Controller
{
    protected function render(string $view, array $data = [], string $layout = 'site'): void
    {
        if ($layout === 'admin') {
            header('Cache-Control: no-store, no-cache, must-revalidate, private');
            header('Pragma: no-cache');
            header('Expires: 0');
            header('Vary: Cookie');
        }

        extract($data, EXTR_SKIP);
        $viewFile = dirname(__DIR__) . '/Views/' . $view . '.php';
        if (!is_file($viewFile)) {
            throw new \RuntimeException('Vista no encontrada.');
        }
        ob_start();
        require $viewFile;
        $content = (string) ob_get_clean();
        require dirname(__DIR__) . '/Views/layouts/' . $layout . '.php';
    }

    protected function redirect(string $path): never
    {
        header('Location: ' . url($path));
        exit;
    }
}

