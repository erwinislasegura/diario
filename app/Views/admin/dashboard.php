<?php
$maxDaily = max(1, ...array_column($analytics['daily'], 'views'));
?>
<section class="admin-welcome">
    <div><small>RESUMEN GENERAL</small><h2>El pulso de tu medio, en un solo lugar.</h2><p>Publica contenidos y revisa el rendimiento del portal en tiempo real.</p></div>
    <a class="primary-button" href="<?= url('/admin/posts/create') ?>">＋ Publicar noticia</a>
</section>

<div class="stats editorial-stats">
    <article><span>Noticias</span><b><?= number_format($stats['posts'], 0, ',', '.') ?></b><small><?= $stats['published'] ?> publicadas</small></article>
    <article><span>Visitas totales</span><b><?= number_format($analytics['total'], 0, ',', '.') ?></b><small>Desde la activación</small></article>
    <article><span>Visitas hoy</span><b><?= number_format($analytics['today'], 0, ',', '.') ?></b><small><?= $analytics['unique_today'] ?> visitantes únicos</small></article>
    <article><span>Últimos 7 días</span><b><?= number_format($analytics['last_7_days'], 0, ',', '.') ?></b><small>Vistas acumuladas</small></article>
</div>

<div class="admin-dashboard-grid">
    <section class="admin-panel analytics-panel">
        <div class="panel-heading"><div><small>ANALÍTICA</small><h2>Visitas de los últimos 7 días</h2></div><span class="live-badge">● EN VIVO</span></div>
        <div class="analytics-bars">
            <?php foreach ($analytics['daily'] as $day): ?>
            <div><span><i style="height:<?= max(3, round(($day['views'] / $maxDaily) * 100)) ?>%"></i></span><b><?= $day['views'] ?></b><small><?= e(strtoupper(date('D', strtotime($day['date'])))) ?></small></div>
            <?php endforeach; ?>
        </div>
    </section>

    <section class="admin-panel top-pages-panel">
        <div class="panel-heading"><div><small>ÚLTIMOS 30 DÍAS</small><h2>Páginas más visitadas</h2></div></div>
        <?php if ($analytics['top_pages']): ?><ol class="top-pages"><?php foreach ($analytics['top_pages'] as $index => $page): ?>
            <li><b><?= $index + 1 ?></b><div><a href="<?= url($page['path']) ?>" target="_blank"><?= e($page['page_title'] ?: $page['path']) ?></a><small><?= e($page['page_type']) ?> · <?= number_format((int)$page['visitors'], 0, ',', '.') ?> visitantes</small></div><strong><?= number_format((int)$page['views'], 0, ',', '.') ?></strong></li>
        <?php endforeach; ?></ol><?php else: ?><div class="analytics-empty">Las estadísticas comenzarán a aparecer con las próximas visitas.</div><?php endif; ?>
    </section>
</div>

<section class="admin-panel">
    <div class="panel-heading"><div><small>CONTENIDO</small><h2>Publicaciones recientes</h2></div><a href="<?= url('/admin/posts') ?>">Administrar todas →</a></div>
    <?php require __DIR__.'/posts/table.php'; ?>
</section>
