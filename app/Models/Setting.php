<?php
declare(strict_types=1);

namespace App\Models;

use App\Core\Database;

final class Setting
{
    private const DEFAULTS = [
        'weather_enabled' => '1',
        'weather_title' => 'El tiempo en tu comuna',
        'weather_fallback_name' => 'San Antonio',
        'weather_fallback_latitude' => '-33.5933',
        'weather_fallback_longitude' => '-71.6217',
    ];

    public static function weather(): array
    {
        self::ensureTable();
        $rows = Database::connection()->query("SELECT setting_key, setting_value FROM settings WHERE setting_key LIKE 'weather_%'")->fetchAll();
        $values = array_column($rows, 'setting_value', 'setting_key');
        return array_merge(self::DEFAULTS, $values);
    }

    public static function saveWeather(array $values): void
    {
        self::ensureTable();
        $statement = Database::connection()->prepare('INSERT INTO settings (setting_key, setting_value) VALUES (:key, :value) ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value)');
        foreach (self::DEFAULTS as $key => $default) {
            $statement->execute(['key' => $key, 'value' => (string) ($values[$key] ?? $default)]);
        }
    }

    private static function ensureTable(): void
    {
        Database::connection()->exec('CREATE TABLE IF NOT EXISTS settings (setting_key VARCHAR(100) PRIMARY KEY, setting_value TEXT NOT NULL, updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP) ENGINE=InnoDB');
    }
}
