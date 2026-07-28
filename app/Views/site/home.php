<?php
$sidePosts = array_slice($posts, 0, 3);
$latestPosts = array_slice($posts, 3, 12);
$shareUrl = (isset($_SERVER['HTTPS']) ? 'https' : 'http') . '://' . ($_SERVER['HTTP_HOST'] ?? 'localhost') . url('/');
$shareTitle = 'VecinoSS — Noticias de la Provincia de San Antonio';
?>

<aside class="home-share" aria-label="Compartir la página de inicio">
    <span class="home-share-title">Compartir</span>
    <a href="https://www.facebook.com/sharer/sharer.php?u=<?= rawurlencode($shareUrl) ?>" target="_blank" rel="noopener" aria-label="Compartir en Facebook"><img src="<?= asset('images/social/facebook.svg') ?>" alt=""><b>Facebook</b></a>
    <a href="https://twitter.com/intent/tweet?url=<?= rawurlencode($shareUrl) ?>&text=<?= rawurlencode($shareTitle) ?>" target="_blank" rel="noopener" aria-label="Compartir en X"><img src="<?= asset('images/social/x.svg') ?>" alt=""><b>X</b></a>
    <a href="https://wa.me/?text=<?= rawurlencode($shareTitle . ' ' . $shareUrl) ?>" target="_blank" rel="noopener" aria-label="Compartir por WhatsApp"><img src="<?= asset('images/social/whatsapp.svg') ?>" alt=""><b>WhatsApp</b></a>
    <a href="mailto:?subject=<?= rawurlencode($shareTitle) ?>&body=<?= rawurlencode($shareTitle . "\n\n" . $shareUrl) ?>" aria-label="Compartir por correo"><img src="<?= asset('images/social/email.svg') ?>" alt=""><b>Correo</b></a>
    <button type="button" data-copy-url="<?= e($shareUrl) ?>" aria-label="Copiar enlace"><img src="<?= asset('images/social/link.svg') ?>" alt=""><b>Copiar</b></button>
</aside>

<section class="shell hero home-hero" id="inicio">
    <?php if ($featured): ?>
        <article class="lead">
            <img src="<?= e(post_image($featured['image'])) ?>" alt="">
            <div class="lead-card">
                <small><?= e($featured['category_name']) ?></small>
                <h1><a href="<?= url('/noticia/' . $featured['slug']) ?>"><?= e($featured['title']) ?></a></h1>
                <p><?= e($featured['excerpt']) ?></p>
                <a class="more" href="<?= url('/noticia/' . $featured['slug']) ?>">Leer noticia →</a>
            </div>
        </article>
    <?php endif; ?>
    <aside class="side-stories home-side-stories">
        <?php foreach ($sidePosts as $post): ?>
            <article><img src="<?= e(post_image($post['image'])) ?>" alt=""><div><small><?= e($post['category_name']) ?></small><h2><a href="<?= url('/noticia/' . $post['slug']) ?>"><?= e($post['title']) ?></a></h2><a class="more" href="<?= url('/noticia/' . $post['slug']) ?>">Ver más →</a></div></article>
        <?php endforeach; ?>
    </aside>
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

<section class="shell section" id="noticias">
    <div class="section-title"><div><small>ACTUALIDAD</small><h2>Últimas noticias</h2></div><div class="category-buttons"><?php foreach ($categories as $category): ?><a href="<?= url('/categoria/' . $category['slug']) ?>"><?= e($category['name']) ?></a><?php endforeach; ?></div></div>
    <div class="news-grid home-news-grid"><?php foreach ($latestPosts as $post) require __DIR__ . '/../partials/card.php'; ?></div>
</section>

<?php if ($videos): ?><section class="dark-section" id="tv"><div class="shell section">
    <div class="section-title"><div><small>EN PANTALLA</small><h2>VecinoSS TV</h2></div><a href="<?= url('/videos') ?>">Ver todos los videos →</a></div>
    <div class="video-grid home-video-grid">
        <?php foreach ($videos as $video): ?><article><button class="video-cover" type="button" data-video-url="<?= e($video['video_url']) ?>" data-video-title="<?= e($video['title']) ?>" aria-label="Reproducir <?= e($video['title']) ?>"><img src="<?= e(post_image($video['cover_image'])) ?>" alt="Portada de <?= e($video['title']) ?>"><span>▶</span></button><small>VECINOSS TV</small><h3><a href="<?= url('/video/'.$video['id']) ?>"><?= e($video['title']) ?></a></h3><?php if ($video['description']): ?><p><?= str_contains($video['description'], '<') ? $video['description'] : e($video['description']) ?></p><?php endif; ?><a class="more" href="<?= url('/video/'.$video['id']) ?>">Ver contenido →</a></article><?php endforeach; ?>
    </div>
</div></section>
<dialog class="video-dialog" data-video-dialog><button class="video-dialog-close" type="button" aria-label="Cerrar video" data-video-close>×</button><h2 data-video-dialog-title></h2><div class="video-player" data-video-player></div></dialog>
<?php endif; ?>

<section class="shell section home-community" id="comunidad">
    <div class="section-title"><div><small>LA VOZ DE LOS BARRIOS</small><h2>Comunidad</h2></div><a href="<?= url('/categoria/comunidad') ?>">Ver la sección →</a></div>
    <div class="community-grid">
        <article class="complaints"><small>LO ÚLTIMO EN COMUNIDAD</small><h3>Tu barrio, tu voz</h3><?php foreach ($communityPosts as $post): ?><div><b><?= e($post['category_name']) ?></b><h4><a href="<?= url('/noticia/' . $post['slug']) ?>"><?= e($post['title']) ?></a></h4><time datetime="<?= e($post['published_at']) ?>"><?= e(date_es($post['published_at'])) ?></time></div><?php endforeach; ?><?php if (!$communityPosts): ?><p>Pronto publicaremos nuevas historias de la comunidad.</p><?php endif; ?><a class="more" href="mailto:prensa@vecinoss.cl">Enviar una denuncia →</a></article>
        <article class="council"><small>SERVICIO PÚBLICO</small><h3>Información útil para vecinos</h3><p>Encuentra noticias, datos y orientación sobre los servicios de la Provincia de San Antonio.</p><ul><li>Teléfonos de emergencia</li><li>Trámites municipales</li><li>Organizaciones comunitarias</li></ul><a class="more" href="mailto:prensa@vecinoss.cl">Solicitar información →</a></article>
        <nav class="community-links" aria-label="Accesos de comunidad"><a href="<?= url('/categoria/comunidad') ?>">Organizaciones <b>→</b></a><a href="<?= url('/categoria/seguridad') ?>">Seguridad <b>→</b></a><a href="<?= url('/categoria/cultura') ?>">Cultura local <b>→</b></a><a href="mailto:prensa@vecinoss.cl">Envía tu historia <b>→</b></a></nav>
    </div>
</section>

<?php if ($businessPosts): ?><section class="business-section" id="guia"><div class="shell section">
    <div class="section-title"><div><small>DATOS Y EMPRENDIMIENTO</small><h2>Guía local</h2></div><a href="<?= url('/categoria/guia-local') ?>">Explorar guía →</a></div>
    <div class="business-grid home-business-grid"><?php foreach ($businessPosts as $post): ?><article><img src="<?= e(post_image($post['image'])) ?>" alt="<?= e($post['title']) ?>"><small><?= e($post['category_name']) ?></small><h3><a href="<?= url('/noticia/' . $post['slug']) ?>"><?= e($post['title']) ?></a></h3><time datetime="<?= e($post['published_at']) ?>"><?= e(date_es($post['published_at'])) ?></time><p><?= e($post['excerpt']) ?></p><a class="more" href="<?= url('/noticia/' . $post['slug']) ?>">Conocer más →</a></article><?php endforeach; ?></div>
    <nav class="guide-buttons" aria-label="Categorías de guía"><a href="<?= url('/categoria/restaurantes') ?>">Gastronomía <b>→</b></a><a href="<?= url('/categoria/comercios') ?>">Comercio <b>→</b></a><a href="<?= url('/categoria/turismo') ?>">Turismo <b>→</b></a><a href="<?= url('/categoria/deportes') ?>">Deportes <b>→</b></a><a href="<?= url('/categoria/servicios') ?>">Servicios <b>→</b></a></nav>
</div></section><?php endif; ?>

<section class="shell section events" id="eventos">
    <div class="section-title"><div><small>QUÉ HACER</small><h2>Agenda y eventos</h2></div><a href="<?= url('/eventos') ?>">Ver agenda completa →</a></div>
    <?php if ($eventPosts): ?><div class="event-grid home-event-grid"><?php foreach ($eventPosts as $index => $post): ?><article class="event-card<?= $index===0?' event-featured':'' ?>">
        <a class="event-image" href="<?= url('/noticia/'.$post['slug']) ?>"><img src="<?= e(post_image($post['image'])) ?>" alt=""><time class="event-date" datetime="<?= e($post['published_at']) ?>"><b><?= e(date('d',strtotime($post['published_at']))) ?></b><span><?= e(strtoupper(date('M',strtotime($post['published_at'])))) ?></span></time></a>
        <div class="event-content"><small><?= e($post['category_name']) ?></small><h3><a href="<?= url('/noticia/'.$post['slug']) ?>"><?= e($post['title']) ?></a></h3><?php if($post['excerpt']): ?><p><?= e($post['excerpt']) ?></p><?php endif; ?><div class="event-meta"><span>Provincia de San Antonio</span><a class="more" href="<?= url('/noticia/'.$post['slug']) ?>">Ver evento →</a></div></div>
    </article><?php endforeach; ?></div><?php else: ?><div class="empty">Pronto publicaremos nuevos eventos y panoramas locales.</div><?php endif; ?>
</section>
