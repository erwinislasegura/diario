<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\{ApiAuth,ApiException};
use App\Services\AutomationPublicationService;

final class ApiController
{
    public function storeNews(): never
    {
        $requestId = null;
        try {
            $config = ApiAuth::requireBearer();
            if (!str_contains(strtolower((string) ($_SERVER['CONTENT_TYPE'] ?? '')), 'application/json')) {
                throw new ApiException('El contenido debe enviarse como application/json.', 415, 'unsupported_media_type');
            }
            if ((int) ($_SERVER['CONTENT_LENGTH'] ?? 0) > 1_000_000) {
                throw new ApiException('La solicitud supera el máximo de 1 MB.', 413, 'payload_too_large');
            }
            $payload = json_decode((string) file_get_contents('php://input'), true, 64, JSON_THROW_ON_ERROR);
            if (!is_array($payload)) {
                throw new ApiException('El cuerpo JSON no es válido.', 400, 'invalid_json');
            }
            $key = trim((string) ($_SERVER['HTTP_IDEMPOTENCY_KEY'] ?? ''));
            if (!preg_match('/^[A-Za-z0-9._:-]{8,128}$/', $key)) {
                throw new ApiException('Debes enviar Idempotency-Key (8 a 128 caracteres).', 400, 'missing_idempotency_key');
            }
            $result = (new AutomationPublicationService())->publish($payload, $key, (int) ($config['user_id'] ?? 1));
            $requestId = $result['request_id'] ?? null;
            $postPath = url('/noticia/' . $result['slug']);
            $appUrl = rtrim((string) (getenv('APP_URL') ?: ''), '/');
            $this->json([
                'ok' => true,
                'replayed' => $result['replayed'],
                'request_id' => $requestId,
                'post' => [
                    'id' => $result['post_id'],
                    'slug' => $result['slug'],
                    'url' => $appUrl !== '' ? $appUrl . $postPath : $postPath,
                ],
            ], $result['replayed'] ? 200 : 201);
        } catch (\JsonException) {
            $this->json(['ok' => false, 'error' => ['code' => 'invalid_json', 'message' => 'El cuerpo JSON no es válido.']], 400);
        } catch (ApiException $error) {
            $this->json(['ok' => false, 'error' => ['code' => $error->errorCode, 'message' => $error->getMessage()]], $error->status);
        } catch (\Throwable $error) {
            error_log('Automation API error: ' . $error->getMessage());
            $this->json(['ok' => false, 'request_id' => $requestId, 'error' => ['code' => 'internal_error', 'message' => 'No fue posible publicar la noticia.']], 500);
        }
    }

    private function json(array $payload, int $status): never
    {
        http_response_code($status);
        header('Content-Type: application/json; charset=utf-8');
        header('Cache-Control: no-store');
        header('X-Content-Type-Options: nosniff');
        echo json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        exit;
    }
}
