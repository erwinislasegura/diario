<?php
declare(strict_types=1);

namespace App\Core;

use PDO;

final class Database
{
    private static ?PDO $connection = null;

    public static function connection(): PDO
    {
        if (self::$connection === null) {
            $file = dirname(__DIR__, 2) . '/config/database.php';
            if (!is_file($file)) {
                throw new \RuntimeException('Copia config/database.example.php como config/database.php y configura MySQL.');
            }
            $config = require $file;
            self::$connection = new PDO($config['dsn'], $config['username'], $config['password'], [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ]);
        }
        return self::$connection;
    }
}

