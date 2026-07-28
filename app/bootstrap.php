<?php
declare(strict_types=1);

session_start([
    'cookie_httponly' => true,
    'cookie_samesite' => 'Lax',
    'cookie_secure' => isset($_SERVER['HTTPS']),
]);

spl_autoload_register(function (string $class): void {
    $prefix = 'App\\';
    if (str_starts_with($class, $prefix)) {
        $file = __DIR__ . '/' . str_replace('\\', '/', substr($class, strlen($prefix))) . '.php';
        if (is_file($file)) require $file;
    }
});

function base_url(): string
{
    static $base;
    if ($base !== null) {
        return $base;
    }

    $configured = trim((string) ($_ENV['APP_BASE_PATH'] ?? getenv('APP_BASE_PATH') ?: ''));
    if ($configured !== '') {
        return $base = '/' . trim($configured, '/');
    }

    $documentRoot = realpath((string) ($_SERVER['DOCUMENT_ROOT'] ?? ''));
    $projectRoot = realpath(dirname(__DIR__));
    if ($documentRoot && $projectRoot && str_starts_with($projectRoot, $documentRoot)) {
        $relative = str_replace('\\', '/', substr($projectRoot, strlen($documentRoot)));
        return $base = rtrim('/' . trim($relative, '/'), '/');
    }

    $script = str_replace('\\', '/', (string) ($_SERVER['SCRIPT_NAME'] ?? '/index.php'));
    return $base = rtrim(dirname($script), '/.');
}

function url(string $path = ''): string
{
    return base_url() . '/' . ltrim($path, '/');
}

function asset(string $path): string { return url('/public/' . ltrim($path, '/')); }
function e(mixed $value): string { return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8'); }
function csrf_field(): string { return '<input type="hidden" name="_token" value="' . e(App\Core\Csrf::token()) . '">'; }
function post_image(?string $image): string { return $image ? (str_starts_with($image, 'http') ? $image : asset($image)) : asset('images/placeholder.svg'); }
function date_es(?string $date): string { return $date ? (new DateTime($date))->format('d.m.Y · H:i') : ''; }
