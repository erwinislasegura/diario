<?php
declare(strict_types=1);

namespace App\Core;

final class ApiAuth
{
    public static function requireBearer(): array
    {
        $file = dirname(__DIR__, 2) . '/config/automation.php';
        $config = is_file($file) ? require $file : [
            'token_hash' => getenv('PULSO_AUTOMATION_TOKEN_HASH') ?: '',
            'user_id' => (int) (getenv('PULSO_AUTOMATION_USER_ID') ?: 1),
        ];
        $expected = strtolower(trim((string) ($config['token_hash'] ?? '')));
        $header = trim((string) ($_SERVER['HTTP_AUTHORIZATION'] ?? ''));
        if ($expected === '' || !preg_match('/^Bearer\s+(\S+)$/i', $header, $match) || !hash_equals($expected, hash('sha256', $match[1]))) {
            throw new ApiException('Credenciales de automatización inválidas.', 401, 'unauthorized');
        }
        return $config;
    }
}
