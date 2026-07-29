<?php
$topStories = array_slice($posts, 0, 2);
$briefStories = array_slice($posts, 2, 4);
$latestPosts = array_slice($posts, 6, 16);
?>

<section class="newsroom shell" id="inicio">
    <div class="newsroom-label"><span>Portada</span><b>Información de Los Ángeles y la Provincia de Biobío</b><time datetime="<?= date('c') ?>"><?= e(date_es(date('Y-m-d H:i:s'))) ?></time></div>
    <div class="newsroom-grid">
        <?php if ($featured): ?>
        <article class="cover-story">
            <a class="cover-story-media" href="<?= url('/noticia/' . $featured['slug']) ?>"><img src="<?= e(post_image($featured['image'])) ?>" alt="<?= e($featured['title']) ?>"></a>
            <div class="cover-story-copy">
                <small><?= e($featured['category_name']) ?> · NOTICIA PRINCIPAL</small>
                <h1><a href="<?= url('/noticia/' . $featured['slug']) ?>"><?= e($featured['title']) ?></a></h1>
                <p><?= e($featured['excerpt']) ?></p>
                <a class="more" href="<?= url('/noticia/' . $featured['slug']) ?>">Leer noticia completa <span>→</span></a>
            </div>
        </article>
        <?php endif; ?>

        <div class="top-stories">
            <?php foreach ($topStories as $post): ?>
            <article>
                <a href="<?= url('/noticia/' . $post['slug']) ?>"><img src="<?= e(post_image($post['image'])) ?>" alt="<?= e($post['title']) ?>"></a>
                <div><small><?= e($post['category_name']) ?></small><h2><a href="<?= url('/noticia/' . $post['slug']) ?>"><?= e($post['title']) ?></a></h2><time datetime="<?= e($post['published_at']) ?>"><?= e(date_es($post['published_at'])) ?></time></div>
            </article>
            <?php endforeach; ?>
        </div>

        <aside class="briefing" aria-label="Más noticias destacadas">
            <header><span></span><div><small>EL PULSO DEL DÍA</small><h2>En desarrollo</h2></div></header>
            <ol>
                <?php foreach ($briefStories as $index => $post): ?>
                <li><b><?= str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT) ?></b><div><small><?= e($post['category_name']) ?></small><h3><a href="<?= url('/noticia/' . $post['slug']) ?>"><?= e($post['title']) ?></a></h3></div></li>
                <?php endforeach; ?>
            </ol>
            <a href="<?= category_url('noticias') ?>">Revisar todas las noticias →</a>
        </aside>
    </div>
</section>

<?php if (($weather['weather_enabled'] ?? '0') === '1'): ?>
<section class="weather-section" aria-labelledby="weather-title">
    <div class="shell weather-widget" data-weather-widget data-latitude="<?= e($weather['weather_fallback_latitude']) ?>" data-longitude="<?= e($weather['weather_fallback_longitude']) ?>" data-fallback-name="<?= e($weather['weather_fallback_name']) ?>">
        <div class="weather-heading"><small>PRONÓSTICO LOCAL</small><h2 id="weather-title"><?= e($weather['weather_title']) ?></h2><p data-weather-location><?= e($weather['weather_fallback_name']) ?></p></div>
        <div class="weather-current" aria-live="polite"><span class="weather-icon" data-weather-icon>◌</span><div><b data-weather-temperature>--°</b><span data-weather-description>Consultando el tiempo…</span></div></div>
        <dl class="weather-details"><div><dt>Sensación</dt><dd data-weather-apparent>--°</dd></div><div><dt>Humedad</dt><dd data-weather-humidity>--%</dd></div><div><dt>Viento</dt><dd data-weather-wind>-- km/h</dd></div></dl>
        <button class="weather-location-button" type="button" data-weather-locate>⌖ Usar mi ubicación</button>
    </div>
</section>
<?php endif; ?>

<section class="shell section latest-section" id="noticias">
    <div class="editorial-heading"><div><small>ACTUALIDAD REGIONAL</small><h2>Lo último</h2></div><a href="<?= category_url('noticias') ?>">Todas las noticias <span>→</span></a></div>
    <nav class="section-tabs" aria-label="Secciones principales">
        <?php foreach ($categories as $category): ?><a href="<?= category_url($category) ?>"><?= e($category['name']) ?></a><?php endforeach; ?>
    </nav>
    <div class="latest-layout">
        <div class="latest-grid"><?php foreach ($latestPosts as $post) require __DIR__ . '/../partials/card.php'; ?></div>
        <aside class="participate-panel">
            <span class="pulse-mark">⌁</span><small>PARTICIPACIÓN CIUDADANA</small><h3>¿Hay algo que debemos saber?</h3><p>Comparte una noticia, denuncia, actividad o historia de tu sector con nuestro equipo editorial.</p><a href="mailto:prensa@pulsoangelino.cl">Contactar a prensa →</a>
        </aside>
    </div>
</section>

<?php if ($videos): ?>
<section class="media-band" id="tv"><div class="shell section">
    <div class="editorial-heading light"><div><small>PULSO ANGELINO TV</small><h2>La región en pantalla</h2></div><a href="<?= url('/videos') ?>">Ir al canal <span>→</span></a></div>
    <div class="media-feature">
        <?php foreach (array_slice($videos, 0, 3) as $index => $video): ?>
        <article class="<?= $index === 0 ? 'media-lead' : '' ?>">
            <button class="video-cover" type="button" data-video-url="<?= e($video['video_url']) ?>" data-video-title="<?= e($video['title']) ?>" aria-label="Reproducir <?= e($video['title']) ?>"><img src="<?= e(post_image($video['cover_image'])) ?>" alt="<?= e($video['title']) ?>"><span>▶</span></button>
            <div><small><?= e($video['commune']) ?> · <?= e($video['format']) ?></small><h3><a href="<?= url('/video/' . $video['id']) ?>"><?= e($video['title']) ?></a></h3><a class="more" href="<?= url('/video/' . $video['id']) ?>">Ver video →</a></div>
        </article>
        <?php endforeach; ?>
    </div>
</div></section>
<dialog class="video-dialog" data-video-dialog><button class="video-dialog-close" type="button" aria-label="Cerrar video" data-video-close>×</button><h2 data-video-dialog-title></h2><div class="video-player" data-video-player></div></dialog>
<?php endif; ?>

<section class="shell section local-dashboard">
    <div class="community-column">
        <div class="editorial-heading"><div><small>COMUNIDAD</small><h2>La voz de los barrios</h2></div><a href="<?= category_url('comunidad') ?>">Ver comunidad →</a></div>
        <div class="community-feed">
            <?php foreach (array_slice($communityPosts, 0, 4) as $post): ?>
            <article><a href="<?= url('/noticia/' . $post['slug']) ?>"><img src="<?= e(post_image($post['image'])) ?>" alt="<?= e($post['title']) ?>"></a><div><small><?= e($post['category_name']) ?></small><h3><a href="<?= url('/noticia/' . $post['slug']) ?>"><?= e($post['title']) ?></a></h3><time datetime="<?= e($post['published_at']) ?>"><?= e(date_es($post['published_at'])) ?></time></div></article>
            <?php endforeach; ?>
        </div>
    </div>

    <aside class="agenda-column">
        <div class="editorial-heading"><div><small>AGENDA</small><h2>Próximos eventos</h2></div><a href="<?= url('/eventos') ?>">Ver todos →</a></div>
        <?php if ($eventPosts): ?><div class="agenda-list"><?php foreach (array_slice($eventPosts, 0, 4) as $post): ?><article><time datetime="<?= e($post['published_at']) ?>"><b><?= e(date('d', strtotime($post['published_at']))) ?></b><span><?= e(strtoupper(date('M', strtotime($post['published_at'])))) ?></span></time><div><small><?= e($post['category_name']) ?></small><h3><a href="<?= url('/noticia/' . $post['slug']) ?>"><?= e($post['title']) ?></a></h3></div></article><?php endforeach; ?></div><?php else: ?><div class="empty">La agenda se está actualizando.</div><?php endif; ?>
    </aside>
</section>

<?php if ($businessPosts): ?>
<section class="regional-guide"><div class="shell section">
    <div class="editorial-heading"><div><small>ECONOMÍA LOCAL</small><h2>Emprendedores y guía regional</h2></div><a href="<?= category_url('guia-local') ?>">Explorar guía →</a></div>
    <div class="guide-editorial"><?php foreach (array_slice($businessPosts, 0, 4) as $post): ?><article><a href="<?= url('/noticia/' . $post['slug']) ?>"><img src="<?= e(post_image($post['image'])) ?>" alt="<?= e($post['title']) ?>"></a><small><?= e($post['category_name']) ?></small><h3><a href="<?= url('/noticia/' . $post['slug']) ?>"><?= e($post['title']) ?></a></h3><p><?= e($post['excerpt']) ?></p></article><?php endforeach; ?></div>
</div></section>
<?php endif; ?>
