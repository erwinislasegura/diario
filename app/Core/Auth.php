<?php
declare(strict_types=1);

namespace App\Core;

final class Auth
{
    public static function check(): bool { return isset($_SESSION['user_id']); }
    public static function user(): ?array { return self::check() ? ['id' => $_SESSION['user_id'], 'name' => $_SESSION['user_name']] : null; }
    public static function requireLogin(): void { if (!self::check()) { header('Location: ' . url('/admin/login')); exit; } }
    public static function attempt(string $email, string $password): bool
    {
        $stmt = Database::connection()->prepare('SELECT id, name, password FROM users WHERE email = ? AND active = 1 LIMIT 1');
        $stmt->execute([$email]);
        $user = $stmt->fetch();
        if (!$user || !password_verify($password, $user['password'])) return false;
        session_regenerate_id(true);
        $_SESSION['user_id'] = (int) $user['id'];
        $_SESSION['user_name'] = $user['name'];
        return true;
    }
    public static function logout(): void { $_SESSION = []; session_destroy(); }
}

