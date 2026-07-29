<?php
declare(strict_types=1);

namespace App\Models;

use App\Core\Database;

final class Video
{
    public const COMMUNES = ['Los Ángeles','Cabrero','Yumbel','Nacimiento','Mulchén','Santa Bárbara'];
    public const FORMATS = ['Entrevistas','Reportajes','Crónicas','Transmisiones en vivo'];
    public static function published(int $limit = 6): array
    {
        self::ensureTable();
        $statement = Database::connection()->prepare("SELECT * FROM videos WHERE status='published' AND published_at<=CURRENT_TIMESTAMP ORDER BY published_at DESC LIMIT ?");
        $statement->bindValue(1, $limit, \PDO::PARAM_INT);
        $statement->execute();
        return $statement->fetchAll();
    }

    public static function allAdmin(): array
    {
        self::ensureTable();
        return Database::connection()->query('SELECT * FROM videos ORDER BY created_at DESC')->fetchAll();
    }

    public static function find(int $id): ?array
    {
        self::ensureTable();
        $statement = Database::connection()->prepare('SELECT * FROM videos WHERE id=?');
        $statement->execute([$id]);
        return $statement->fetch() ?: null;
    }

    public static function findPublished(int $id): ?array
    {
        self::ensureTable();
        $statement = Database::connection()->prepare("SELECT * FROM videos WHERE id=? AND status='published' AND published_at<=CURRENT_TIMESTAMP LIMIT 1");
        $statement->execute([$id]);
        return $statement->fetch() ?: null;
    }

    public static function save(array $data, ?int $id = null): void
    {
        self::ensureTable();
        $values = [$data['title'], $data['description'], $data['commune'], $data['format'], $data['cover_image'], $data['video_url'], $data['status'], $data['published_at']];
        if ($id) {
            $values[] = $id;
            $sql = 'UPDATE videos SET title=?,description=?,commune=?,format=?,cover_image=?,video_url=?,status=?,published_at=? WHERE id=?';
        } else {
            $sql = 'INSERT INTO videos(title,description,commune,format,cover_image,video_url,status,published_at) VALUES(?,?,?,?,?,?,?,?)';
        }
        Database::connection()->prepare($sql)->execute($values);
    }

    public static function delete(int $id): void
    {
        self::ensureTable();
        Database::connection()->prepare('DELETE FROM videos WHERE id=?')->execute([$id]);
    }

    private static function ensureTable(): void
    {
        Database::connection()->exec("CREATE TABLE IF NOT EXISTS videos (id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY, title VARCHAR(180) NOT NULL, description LONGTEXT, commune VARCHAR(40) NOT NULL DEFAULT 'Los Ángeles', format VARCHAR(40) NOT NULL DEFAULT 'Reportajes', cover_image VARCHAR(500), video_url VARCHAR(1000) NOT NULL, status ENUM('draft','published') NOT NULL DEFAULT 'draft', published_at DATETIME NOT NULL, created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP, updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP, INDEX idx_videos_public (status, published_at)) ENGINE=InnoDB");
    }
}
