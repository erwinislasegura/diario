<?php
declare(strict_types=1);

namespace App\Core;

final class ApiException extends \RuntimeException
{
    public function __construct(string $message, public readonly int $status = 400, public readonly string $errorCode = 'invalid_request')
    {
        parent::__construct($message);
    }
}
