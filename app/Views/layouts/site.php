<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1,viewport-fit=cover">
    <title><?= e($title) ?> | Pulso Angelino</title>
    <meta name="description" content="<?= e($metaDescription ?? 'Noticias locales, actualidad y comunidad de la Provincia de Biobío.') ?>">
    <meta name="theme-color" content="#082b68">
    <link rel="stylesheet" href="<?= asset('css/app.css') ?>">
    <link rel="stylesheet" href="<?= asset('css/pro.css') ?>">
</head>
<body>
<a class="skip-link" href="#contenido">Saltar al contenido</a>
<?php require dirname(__DIR__) . '/partials/header.php'; ?>
<main id="contenido"><?= $content ?></main>
<?php require dirname(__DIR__) . '/partials/footer.php'; ?>
<script src="<?= asset('js/app.js') ?>" defer></script>
</body></html>
