# Pulso Angelino

Portal de noticias regional desarrollado en PHP.

## API privada de publicación

El endpoint `POST /api/v1/noticias` permite publicar sin una sesión del panel. Antes de usarlo:

1. Ejecuta `database/migrate_automation_api.sql`.
2. Copia `config/automation.example.php` como `config/automation.php`.
3. Configura el hash SHA-256 de una clave aleatoria larga y el ID del usuario editorial.

La solicitud requiere `Authorization: Bearer ...`, `Idempotency-Key` y JSON con `title`, `excerpt`,
`body`, `category_slug`, `tags`, `image_url`, `source_name` y `source_url`. La misma clave de
idempotencia puede reenviarse con seguridad: la API devolverá la publicación ya creada.

Ejemplo:

```bash
curl -X POST https://pulsoangelino.cl/api/v1/noticias \
  -H "Authorization: Bearer CLAVE_PRIVADA" \
  -H "Idempotency-Key: ejecucion-20260730-noticia-01" \
  -H "Content-Type: application/json" \
  --data '{
    "title": "Título de la noticia",
    "excerpt": "Resumen de hasta 350 caracteres.",
    "body": "<p>Contenido original de al menos 220 palabras.</p>",
    "category_slug": "actualidad-comunal",
    "tags": ["Los Ángeles", "Biobío"],
    "image_url": "https://medio.cl/imagen.jpg",
    "source_name": "Nombre del medio",
    "source_url": "https://medio.cl/noticia-original",
    "status": "published"
  }'
```

Las alertas urgentes pueden usar `"urgent": true` y un mínimo de 180 palabras. Configura
`APP_URL=https://pulsoangelino.cl` para que la respuesta entregue un enlace absoluto.

## Apache

El proyecto incluye un archivo `.htaccess` que dirige las URL amigables a
`index.php`. Para instalaciones en una subcarpeta, por ejemplo
`http://localhost/diario`, Apache debe tener habilitado `mod_rewrite` y permitir
sobrescrituras mediante `AllowOverride All`.
