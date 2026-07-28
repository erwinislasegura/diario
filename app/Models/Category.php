<?php
declare(strict_types=1);
namespace App\Models;
use App\Core\Database;
final class Category
{
    /** @var array<string, string> URLs antiguas que siguen circulando en enlaces y buscadores. */
    private const SLUG_ALIASES = [
        'emprendimiento' => 'emprendedores',
        'gastronomia' => 'restaurantes',
        'comercio' => 'comercios',
        'servicio' => 'servicios',
    ];

    public static function all(): array { return Database::connection()->query('SELECT c.*, parent.name parent_name, COUNT(p.id) post_count FROM categories c LEFT JOIN categories parent ON parent.id=c.parent_id LEFT JOIN posts p ON p.category_id=c.id GROUP BY c.id,parent.name ORDER BY COALESCE(parent.name,c.name),c.parent_id IS NOT NULL,c.name')->fetchAll(); }
    public static function topLevel(): array { return Database::connection()->query('SELECT c.*, COUNT(p.id) post_count FROM categories c LEFT JOIN posts p ON p.category_id=c.id WHERE c.parent_id IS NULL GROUP BY c.id ORDER BY c.id')->fetchAll(); }
    public static function childrenOf(string $parentSlug): array
    {
        $s = Database::connection()->prepare('SELECT c.* FROM categories c JOIN categories parent ON parent.id=c.parent_id WHERE parent.slug=? ORDER BY c.name');
        $s->execute([$parentSlug]);
        return $s->fetchAll();
    }
    public static function findBySlug(string $slug): ?array
    {
        $slug = self::SLUG_ALIASES[$slug] ?? $slug;
        $s = Database::connection()->prepare('SELECT * FROM categories WHERE slug=?');
        $s->execute([$slug]);
        return $s->fetch() ?: null;
    }
}
