<?php
declare(strict_types=1);

namespace App\Services;

final class ImageStorage
{
    private const MAX_BYTES = 5_000_000;
    private const ALLOWED_TYPES = [
        'image/jpeg' => 'jpg',
        'image/png' => 'png',
        'image/webp' => 'webp',
    ];

    public static function fromUpload(array $file): string
    {
        if (($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
            throw new \RuntimeException('No fue posible recibir la imagen subida.');
        }
        if (!isset($file['tmp_name'], $file['size']) || !is_uploaded_file($file['tmp_name'])) {
            throw new \RuntimeException('La carga de la imagen no es válida.');
        }
        if ((int) $file['size'] > self::MAX_BYTES) {
            throw new \RuntimeException('La imagen supera el máximo permitido de 5 MB.');
        }

        return self::storeValidatedFile($file['tmp_name'], false);
    }

    public static function fromRemoteUrl(string $url): string
    {
        $url = trim($url);
        if ($url === '') {
            return '';
        }
        if (!extension_loaded('curl')) {
            throw new \RuntimeException('El servidor no tiene habilitada la descarga segura de imágenes.');
        }

        $temporary = tempnam(sys_get_temp_dir(), 'pulso-image-');
        if ($temporary === false) {
            throw new \RuntimeException('No fue posible preparar la descarga de la imagen.');
        }

        try {
            self::download($url, $temporary);
            return self::storeValidatedFile($temporary, true);
        } finally {
            if (is_file($temporary)) {
                @unlink($temporary);
            }
        }
    }

    private static function download(string $url, string $destination): void
    {
        for ($redirects = 0; $redirects <= 3; $redirects++) {
            [$host, $port, $ip] = self::validatedTarget($url);
            $handle = fopen($destination, 'wb');
            if ($handle === false) {
                throw new \RuntimeException('No fue posible guardar temporalmente la imagen.');
            }

            $bytes = 0;
            $responseHeaders = [];
            $curl = curl_init($url);
            curl_setopt_array($curl, [
                CURLOPT_FOLLOWLOCATION => false,
                CURLOPT_CONNECTTIMEOUT => 8,
                CURLOPT_TIMEOUT => 20,
                CURLOPT_USERAGENT => 'PulsoAngelino/1.0 (+https://pulsoangelino.cl)',
                CURLOPT_HTTPHEADER => ['Accept: image/jpeg,image/png,image/webp'],
                CURLOPT_PROTOCOLS => CURLPROTO_HTTP | CURLPROTO_HTTPS,
                CURLOPT_REDIR_PROTOCOLS => CURLPROTO_HTTP | CURLPROTO_HTTPS,
                CURLOPT_RESOLVE => [sprintf('%s:%d:%s', $host, $port, $ip)],
                CURLOPT_HEADERFUNCTION => static function ($curl, string $header) use (&$responseHeaders): int {
                    $length = strlen($header);
                    $parts = explode(':', $header, 2);
                    if (count($parts) === 2) {
                        $responseHeaders[strtolower(trim($parts[0]))] = trim($parts[1]);
                    }
                    return $length;
                },
                CURLOPT_WRITEFUNCTION => static function ($curl, string $chunk) use ($handle, &$bytes): int {
                    $bytes += strlen($chunk);
                    if ($bytes > self::MAX_BYTES) {
                        return 0;
                    }
                    $written = fwrite($handle, $chunk);
                    return $written === false ? 0 : $written;
                },
            ]);

            $success = curl_exec($curl);
            $status = (int) curl_getinfo($curl, CURLINFO_RESPONSE_CODE);
            $error = curl_error($curl);
            curl_close($curl);
            fclose($handle);

            if ($bytes > self::MAX_BYTES) {
                throw new \RuntimeException('La imagen externa supera el máximo permitido de 5 MB.');
            }
            if ($success === false) {
                throw new \RuntimeException('No fue posible descargar la imagen externa: ' . ($error ?: 'respuesta inválida.'));
            }
            if ($status >= 200 && $status < 300) {
                if ($bytes === 0) {
                    throw new \RuntimeException('La URL externa no devolvió una imagen.');
                }
                return;
            }
            if ($status >= 300 && $status < 400 && isset($responseHeaders['location'])) {
                $url = self::absoluteRedirect($url, $responseHeaders['location']);
                continue;
            }

            throw new \RuntimeException('La imagen externa respondió con estado HTTP ' . $status . '.');
        }

        throw new \RuntimeException('La imagen externa realizó demasiadas redirecciones.');
    }

    private static function validatedTarget(string $url): array
    {
        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            throw new \RuntimeException('La URL de imagen no es válida.');
        }

        $parts = parse_url($url);
        $scheme = strtolower((string) ($parts['scheme'] ?? ''));
        $host = strtolower((string) ($parts['host'] ?? ''));
        if (!in_array($scheme, ['http', 'https'], true) || $host === '' || isset($parts['user']) || isset($parts['pass'])) {
            throw new \RuntimeException('La imagen debe usar una URL pública http o https.');
        }

        $port = isset($parts['port']) ? (int) $parts['port'] : ($scheme === 'https' ? 443 : 80);
        if (!in_array($port, [80, 443], true)) {
            throw new \RuntimeException('La URL de imagen utiliza un puerto no permitido.');
        }

        $addresses = filter_var($host, FILTER_VALIDATE_IP) ? [$host] : (gethostbynamel($host) ?: []);
        foreach ($addresses as $address) {
            if (filter_var($address, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
                return [$host, $port, $address];
            }
        }

        throw new \RuntimeException('La URL de imagen no apunta a un servidor público permitido.');
    }

    private static function absoluteRedirect(string $base, string $location): string
    {
        if (preg_match('#^https?://#i', $location)) {
            return $location;
        }

        $parts = parse_url($base);
        $origin = ($parts['scheme'] ?? 'https') . '://' . ($parts['host'] ?? '');
        if (isset($parts['port'])) {
            $origin .= ':' . $parts['port'];
        }
        if (str_starts_with($location, '/')) {
            return $origin . $location;
        }

        $directory = rtrim(str_replace('\\', '/', dirname($parts['path'] ?? '/')), '/');
        return $origin . ($directory ? '/' . ltrim($directory, '/') : '') . '/' . $location;
    }

    private static function storeValidatedFile(string $source, bool $copy): string
    {
        $mime = (new \finfo(FILEINFO_MIME_TYPE))->file($source);
        if (!isset(self::ALLOWED_TYPES[$mime])) {
            throw new \RuntimeException('El archivo recibido no es una imagen JPG, PNG o WebP válida.');
        }
        if ((int) filesize($source) > self::MAX_BYTES) {
            throw new \RuntimeException('La imagen supera el máximo permitido de 5 MB.');
        }

        $directory = dirname(__DIR__, 2) . '/public/uploads';
        if (!is_dir($directory) && !mkdir($directory, 0755, true) && !is_dir($directory)) {
            throw new \RuntimeException('No fue posible crear el directorio de imágenes.');
        }

        $name = bin2hex(random_bytes(12)) . '.' . self::ALLOWED_TYPES[$mime];
        $destination = $directory . '/' . $name;
        $stored = $copy ? copy($source, $destination) : move_uploaded_file($source, $destination);
        if (!$stored) {
            throw new \RuntimeException('No fue posible almacenar la imagen en el servidor.');
        }

        @chmod($destination, 0644);
        return 'uploads/' . $name;
    }
}
