<?php
declare(strict_types=1);

namespace App\Models;

use App\Core\Database;
use Throwable;

final class Analytics
{
    private static bool $ready = false;

    public static function record(string $path, string $type, ?int $contentId, string $title): void
    {
        if (str_starts_with($path, '/admin') || self::isBot()) return;

        try {
            self::ensureSchema();
            $ip = (string) ($_SERVER['REMOTE_ADDR'] ?? 'unknown');
            $agent = substr((string) ($_SERVER['HTTP_USER_AGENT'] ?? ''), 0, 500);
            $visitorHash = hash('sha256', $ip . '|' . $agent . '|' . date('Y-m-d'));
            $sessionHash = hash('sha256', session_id() . '|' . $agent);
            $referrer = substr((string) ($_SERVER['HTTP_REFERER'] ?? ''), 0, 500);
            $stmt = Database::connection()->prepare(
                'INSERT INTO page_views(path,page_type,content_id,page_title,visitor_hash,session_hash,referrer,user_agent)
                 VALUES(?,?,?,?,?,?,?,?)'
            );
            $stmt->execute([
                substr($path, 0, 255),
                substr($type, 0, 30),
                $contentId,
                substr($title, 0, 200),
                $visitorHash,
                $sessionHash,
                $referrer,
                $agent,
            ]);
        } catch (Throwable) {
            // Las métricas nunca deben impedir que el sitio público cargue.
        }
    }

    public static function dashboard(): array
    {
        $empty = [
            'total' => 0,
            'today' => 0,
            'unique_today' => 0,
            'last_7_days' => 0,
            'top_pages' => [],
            'daily' => [],
        ];

        try {
            self::ensureSchema();
            $db = Database::connection();
            $summary = $db->query(
                "SELECT
                    COUNT(*) total,
                    SUM(viewed_at >= CURRENT_DATE) today,
                    COUNT(DISTINCT CASE WHEN viewed_at >= CURRENT_DATE THEN visitor_hash END) unique_today,
                    SUM(viewed_at >= DATE_SUB(CURRENT_DATE, INTERVAL 6 DAY)) last_7_days
                 FROM page_views"
            )->fetch() ?: [];
            $topPages = $db->query(
                "SELECT path, MAX(page_title) page_title, MAX(page_type) page_type, COUNT(*) views,
                        COUNT(DISTINCT visitor_hash) visitors
                 FROM page_views
                 WHERE viewed_at >= DATE_SUB(CURRENT_DATE, INTERVAL 30 DAY)
                 GROUP BY path
                 ORDER BY views DESC
                 LIMIT 8"
            )->fetchAll();
            $dailyRows = $db->query(
                "SELECT DATE(viewed_at) view_date, COUNT(*) views
                 FROM page_views
                 WHERE viewed_at >= DATE_SUB(CURRENT_DATE, INTERVAL 6 DAY)
                 GROUP BY DATE(viewed_at)
                 ORDER BY view_date"
            )->fetchAll();
            $dailyMap = array_column($dailyRows, 'views', 'view_date');
            $daily = [];
            for ($daysAgo = 6; $daysAgo >= 0; $daysAgo--) {
                $date = date('Y-m-d', strtotime("-{$daysAgo} days"));
                $daily[] = ['date' => $date, 'views' => (int) ($dailyMap[$date] ?? 0)];
            }

            return [
                'total' => (int) ($summary['total'] ?? 0),
                'today' => (int) ($summary['today'] ?? 0),
                'unique_today' => (int) ($summary['unique_today'] ?? 0),
                'last_7_days' => (int) ($summary['last_7_days'] ?? 0),
                'top_pages' => $topPages,
                'daily' => $daily,
            ];
        } catch (Throwable) {
            return $empty;
        }
    }

    private static function ensureSchema(): void
    {
        if (self::$ready) return;
        Database::connection()->exec(
            "CREATE TABLE IF NOT EXISTS page_views (
                id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                path VARCHAR(255) NOT NULL,
                page_type VARCHAR(30) NOT NULL,
                content_id BIGINT UNSIGNED NULL,
                page_title VARCHAR(200) NOT NULL,
                visitor_hash CHAR(64) NOT NULL,
                session_hash CHAR(64) NOT NULL,
                referrer VARCHAR(500) NOT NULL DEFAULT '',
                user_agent VARCHAR(500) NOT NULL DEFAULT '',
                viewed_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
                INDEX idx_views_date (viewed_at),
                INDEX idx_views_path_date (path, viewed_at),
                INDEX idx_views_content (page_type, content_id)
            ) ENGINE=InnoDB"
        );
        self::$ready = true;
    }

    private static function isBot(): bool
    {
        return (bool) preg_match(
            '/bot|crawler|spider|slurp|preview|facebookexternalhit|whatsapp/i',
            (string) ($_SERVER['HTTP_USER_AGENT'] ?? '')
        );
    }
}
