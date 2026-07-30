<?php
declare(strict_types=1);

/*
 * Copia este archivo como config/automation.php en producción.
 * Genera el hash con:
 * php -r "echo hash('sha256', 'TU_CLAVE_LARGA_Y_ALEATORIA'), PHP_EOL;"
 */
return [
    'token_hash' => getenv('PULSO_AUTOMATION_TOKEN_HASH') ?: '',
    'user_id' => (int) (getenv('PULSO_AUTOMATION_USER_ID') ?: 1),
];
