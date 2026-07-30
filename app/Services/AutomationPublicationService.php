<?php
declare(strict_types=1);

namespace App\Services;

use App\Core\{ApiException,Database};
use App\Models\{Category,Post};
use DateTimeImmutable;
final class AutomationPublicationService
{
    public function publish(array $payload, string $idempotencyKey, int $userId): array
    {
        $data = $this->validate($payload);
        $db = Database::connection();
        $existingRequest = $this->findRequest($idempotencyKey);
        if ($existingRequest && $existingRequest['status'] === 'published') {
            return ['replayed' => true, 'post_id' => (int) $existingRequest['post_id'], 'slug' => $existingRequest['slug']];
        }
        if ($existingRequest && $existingRequest['status'] !== 'failed') {
            throw new ApiException('Esta solicitud ya está siendo procesada.', 409, 'idempotency_conflict');
        }
        if ($duplicate = Post::findBySourceUrl($data['source_url'])) {
            throw new ApiException('La fuente ya fue publicada como "' . $duplicate['title'] . '".', 409, 'duplicate_source');
        }
        $category = Category::findBySlug($data['category_slug']);
        if (!$category) {
            throw new ApiException('La categoría indicada no existe.', 422, 'invalid_category');
        }

        $requestId = $this->uuid();
        $existingRequest
            ? $this->retryRequest($idempotencyKey, $requestId, $data['source_url'])
            : $this->createRequest($idempotencyKey, $requestId, $data['source_url']);
        try {
            $image = ImageStorage::fromRemoteUrl($data['image_url']);
            $slug = $this->uniqueSlug($data['slug'] ?: $data['title']);
            $db->beginTransaction();
            $postId = Post::save([
                'category_id' => (int) $category['id'],
                'user_id' => $userId,
                'title' => $data['title'],
                'slug' => $slug,
                'excerpt' => $data['excerpt'],
                'body' => $data['body'],
                'tags' => implode(',', $data['tags']),
                'image' => $image,
                'source_name' => $data['source_name'],
                'source_url' => $data['source_url'],
                'status' => $data['status'],
                'featured' => $data['featured'] ? 1 : 0,
                'published_at' => $data['published_at'],
            ]);
            $statement = $db->prepare("UPDATE automation_publications SET post_id=?,status='published',http_status=201,completed_at=CURRENT_TIMESTAMP WHERE idempotency_key=?");
            $statement->execute([$postId, $idempotencyKey]);
            $db->commit();
            return ['replayed' => false, 'post_id' => $postId, 'slug' => $slug, 'request_id' => $requestId];
        } catch (\Throwable $error) {
            if ($db->inTransaction()) {
                $db->rollBack();
            }
            $this->failRequest($idempotencyKey, $error);
            throw $error;
        }
    }

    private function validate(array $payload): array
    {
        $title = trim((string) ($payload['title'] ?? ''));
        $body = $this->sanitizeHtml(trim((string) ($payload['body'] ?? '')));
        $excerpt = trim((string) ($payload['excerpt'] ?? ''));
        $urgent = filter_var($payload['urgent'] ?? false, FILTER_VALIDATE_BOOL);
        $minimumWords = $urgent ? 180 : 220;
        $wordCount = count(preg_split('/\s+/u', trim(strip_tags($body))) ?: []);
        if (mb_strlen($title) < 10 || mb_strlen($title) > 180) {
            throw new ApiException('El título debe tener entre 10 y 180 caracteres.', 422, 'invalid_title');
        }
        if ($wordCount < $minimumWords) {
            throw new ApiException("El contenido debe tener al menos {$minimumWords} palabras.", 422, 'content_too_short');
        }
        if (mb_strlen($excerpt) > 350) {
            throw new ApiException('La bajada no puede superar 350 caracteres.', 422, 'invalid_excerpt');
        }
        $sourceUrl = $this->canonicalSourceUrl($this->publicUrl($payload['source_url'] ?? '', 'fuente'));
        $imageUrl = $this->publicUrl($payload['image_url'] ?? '', 'imagen');
        $sourceName = trim((string) ($payload['source_name'] ?? ''));
        if ($sourceName === '' || mb_strlen($sourceName) > 180) {
            throw new ApiException('El nombre de la fuente es obligatorio y admite hasta 180 caracteres.', 422, 'invalid_source');
        }
        $categorySlug = trim((string) ($payload['category_slug'] ?? ''));
        if (!preg_match('/^[a-z0-9-]{2,100}$/', $categorySlug)) {
            throw new ApiException('Debes indicar una categoría válida.', 422, 'invalid_category');
        }
        $status = in_array($payload['status'] ?? 'published', ['draft', 'published'], true) ? $payload['status'] : 'published';
        try {
            $publishedAt = isset($payload['published_at']) ? new DateTimeImmutable((string) $payload['published_at']) : new DateTimeImmutable();
        } catch (\Throwable) {
            throw new ApiException('La fecha de publicación no es válida.', 422, 'invalid_published_at');
        }
        $tags = $payload['tags'] ?? [];
        if (is_string($tags)) {
            $tags = explode(',', $tags);
        }
        if (!is_array($tags)) {
            throw new ApiException('Las etiquetas deben enviarse como arreglo o texto separado por comas.', 422, 'invalid_tags');
        }
        $tags = array_slice(array_values(array_unique(array_filter(array_map(static fn($tag) => mb_substr(trim((string) $tag), 0, 80), $tags)))), 0, 20);
        return [
            'title' => $title,
            'slug' => trim((string) ($payload['slug'] ?? '')),
            'excerpt' => $excerpt,
            'body' => $body,
            'category_slug' => $categorySlug,
            'tags' => $tags,
            'image_url' => $imageUrl,
            'source_name' => $sourceName,
            'source_url' => $sourceUrl,
            'status' => $status,
            'featured' => filter_var($payload['featured'] ?? false, FILTER_VALIDATE_BOOL),
            'published_at' => $publishedAt->format('Y-m-d H:i:s'),
        ];
    }

    private function publicUrl(mixed $value, string $label): string
    {
        $url = trim((string) $value);
        $parts = parse_url($url);
        if (!filter_var($url, FILTER_VALIDATE_URL) || !in_array(strtolower((string) ($parts['scheme'] ?? '')), ['http', 'https'], true) || isset($parts['user']) || isset($parts['pass']) || strlen($url) > 1000) {
            throw new ApiException("La URL de {$label} no es válida.", 422, 'invalid_' . $label . '_url');
        }
        return $url;
    }

    private function canonicalSourceUrl(string $url): string
    {
        $parts = parse_url($url);
        if ($parts === false) {
            return $url;
        }
        $query = [];
        parse_str((string) ($parts['query'] ?? ''), $query);
        foreach (array_keys($query) as $key) {
            if (str_starts_with(strtolower((string) $key), 'utm_') || in_array(strtolower((string) $key), ['fbclid', 'gclid', 'mc_cid', 'mc_eid'], true)) {
                unset($query[$key]);
            }
        }
        $canonical = strtolower((string) $parts['scheme']) . '://' . strtolower((string) $parts['host']);
        if (isset($parts['port'])) {
            $canonical .= ':' . $parts['port'];
        }
        $canonical .= $parts['path'] ?? '/';
        if ($query) {
            $canonical .= '?' . http_build_query($query);
        }
        return rtrim($canonical, '/');
    }

    private function sanitizeHtml(string $html): string
    {
        $html = strip_tags($html, '<p><br><strong><b><em><i><u><h2><h3><ul><ol><li><blockquote><a>');
        $html = preg_replace('/\s+on[a-z]+\s*=\s*(["\']).*?\1/iu', '', $html) ?? $html;
        $html = preg_replace('/<(?!a\b)([a-z0-9]+)\s+[^>]*>/iu', '<$1>', $html) ?? $html;
        return trim((string) preg_replace_callback('/<a\s+([^>]*)>/iu', static function (array $match): string {
            preg_match('/href\s*=\s*(["\'])(.*?)\1/iu', $match[1], $href);
            $url = html_entity_decode($href[2] ?? '');
            return preg_match('#^https?://#i', $url) ? '<a href="' . htmlspecialchars($url, ENT_QUOTES, 'UTF-8') . '" target="_blank" rel="noopener">' : '<a>';
        }, $html));
    }

    private function uniqueSlug(string $text): string
    {
        $ascii = iconv('UTF-8', 'ASCII//TRANSLIT', $text) ?: $text;
        $base = trim((string) preg_replace('/[^a-z0-9]+/', '-', strtolower($ascii)), '-');
        $base = mb_substr($base ?: 'noticia', 0, 190);
        $slug = $base;
        for ($suffix = 2; Post::slugExists($slug); $suffix++) {
            $slug = mb_substr($base, 0, 190 - strlen((string) $suffix)) . '-' . $suffix;
        }
        return $slug;
    }

    private function findRequest(string $key): ?array
    {
        $statement = Database::connection()->prepare('SELECT a.*,p.slug FROM automation_publications a LEFT JOIN posts p ON p.id=a.post_id WHERE a.idempotency_key=? LIMIT 1');
        $statement->execute([$key]);
        return $statement->fetch() ?: null;
    }

    private function createRequest(string $key, string $requestId, string $sourceUrl): void
    {
        Database::connection()->prepare('INSERT INTO automation_publications(idempotency_key,request_id,source_url) VALUES(?,?,?)')->execute([$key, $requestId, $sourceUrl]);
    }

    private function retryRequest(string $key, string $requestId, string $sourceUrl): void
    {
        $statement = Database::connection()->prepare("UPDATE automation_publications SET request_id=?,post_id=NULL,source_url=?,status='processing',http_status=NULL,error_message=NULL,completed_at=NULL WHERE idempotency_key=? AND status='failed'");
        $statement->execute([$requestId, $sourceUrl, $key]);
    }

    private function failRequest(string $key, \Throwable $error): void
    {
        $statement = Database::connection()->prepare("UPDATE automation_publications SET status='failed',http_status=500,error_message=?,completed_at=CURRENT_TIMESTAMP WHERE idempotency_key=?");
        $statement->execute([mb_substr($error->getMessage(), 0, 500), $key]);
    }

    private function uuid(): string
    {
        $bytes = random_bytes(16);
        $bytes[6] = chr((ord($bytes[6]) & 0x0f) | 0x40);
        $bytes[8] = chr((ord($bytes[8]) & 0x3f) | 0x80);
        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($bytes), 4));
    }
}
