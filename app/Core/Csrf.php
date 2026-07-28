<?php
declare(strict_types=1);
namespace App\Core;
final class Csrf
{
    public static function token(): string { return $_SESSION['_token'] ??= bin2hex(random_bytes(32)); }
    public static function verify(): void
    {
        if (!hash_equals($_SESSION['_token'] ?? '', (string) ($_POST['_token'] ?? ''))) {
            http_response_code(419); exit('La sesión del formulario expiró. Vuelve atrás y recarga la página.');
        }
    }
}

