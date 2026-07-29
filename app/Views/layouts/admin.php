<?php
$adminPath = (string) parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH);
$isActive = static fn(string $path): string => str_contains($adminPath, $path) ? ' class="active"' : '';
$icon = static function (string $name): string {
    $paths = [
        'home' => '<path d="M3 11.5 12 4l9 7.5M5.5 10v10h13V10M9.5 20v-6h5v6"/>',
        'news' => '<path d="M5 4h14v16H5zM8 8h8M8 12h8M8 16h5"/>',
        'plus' => '<path d="M12 5v14M5 12h14"/>',
        'calendar' => '<rect x="4" y="5" width="16" height="15"/><path d="M8 3v4M16 3v4M4 9h16"/>',
        'video' => '<rect x="3" y="5" width="18" height="14"/><path d="m10 9 5 3-5 3z"/>',
        'weather' => '<circle cx="12" cy="12" r="4"/><path d="M12 2v2M12 20v2M2 12h2M20 12h2M5 5l1.5 1.5M17.5 17.5 19 19M19 5l-1.5 1.5M6.5 17.5 5 19"/>',
        'external' => '<path d="M14 4h6v6M20 4l-9 9"/><path d="M18 13v7H4V6h7"/>',
        'logout' => '<path d="M10 5H4v14h6M14 8l4 4-4 4M18 12H8"/>',
    ];
    return '<svg viewBox="0 0 24 24" aria-hidden="true">' . ($paths[$name] ?? '') . '</svg>';
};
?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title><?= e($title) ?> | Pulso Angelino Admin</title>
    <link rel="stylesheet" href="<?= asset('css/app.css') ?>">
    <link rel="stylesheet" href="<?= asset('css/admin-pro.css') ?>">
</head>
<body class="admin-body">
<aside class="admin-sidebar">
    <div class="admin-brand">
        <a href="<?= url('/admin') ?>"><img src="<?= url('/logo/logo.png') ?>" alt="Pulso Angelino"></a>
        <span>Centro editorial</span>
    </div>
    <nav aria-label="Administración">
        <small>GENERAL</small>
        <a href="<?= url('/admin') ?>"<?= $adminPath === url('/admin') ? ' class="active"' : '' ?>><?= $icon('home') ?><span>Resumen</span></a>
        <small>CONTENIDO</small>
        <a href="<?= url('/admin/posts') ?>"<?= $isActive('/admin/posts') ?>><?= $icon('news') ?><span>Noticias</span></a>
        <a href="<?= url('/admin/posts/create') ?>"><?= $icon('plus') ?><span>Nueva noticia</span></a>
        <a href="<?= url('/admin/events') ?>"<?= $isActive('/admin/events') ?>><?= $icon('calendar') ?><span>Agenda y eventos</span></a>
        <a href="<?= url('/admin/videos') ?>"<?= $isActive('/admin/videos') ?>><?= $icon('video') ?><span>Pulso Angelino TV</span></a>
        <small>CONFIGURACIÓN</small>
        <a href="<?= url('/admin/weather') ?>"<?= $isActive('/admin/weather') ?>><?= $icon('weather') ?><span>Tiempo local</span></a>
        <a href="<?= url('/') ?>" target="_blank" rel="noopener"><?= $icon('external') ?><span>Ver sitio público</span></a>
    </nav>
    <form action="<?= url('/admin/logout') ?>" method="post"><?= csrf_field() ?><button><?= $icon('logout') ?><span>Cerrar sesión</span></button></form>
</aside>
<main class="admin-main">
    <header class="admin-top">
        <div><small>PULSO ANGELINO</small><h1><?= e($title) ?></h1></div>
        <div class="admin-user"><span><?= e(substr(App\Core\Auth::user()['name'] ?? 'E', 0, 1)) ?></span><div><small>SESIÓN ACTIVA</small><b><?= e(App\Core\Auth::user()['name'] ?? '') ?></b></div></div>
    </header>
    <?php if ($flash = $_SESSION['flash'] ?? null): unset($_SESSION['flash']); ?><div class="flash"><?= e($flash) ?></div><?php endif; ?>
    <?= $content ?>
</main>
</body>
</html>
