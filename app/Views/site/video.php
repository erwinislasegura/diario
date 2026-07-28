<?php $shareUrl = (isset($_SERVER['HTTPS']) ? 'https' : 'http') . '://' . ($_SERVER['HTTP_HOST'] ?? 'localhost') . url('/video/' . $video['id']); ?>
<article class="video-story">
    <div class="video-glow" aria-hidden="true"></div>
    <div class="shell">
        <nav class="breadcrumbs breadcrumbs-dark" aria-label="Migas de pan"><a href="<?= url('/') ?>">Inicio</a><span>›</span><a href="<?= url('/videos') ?>">VecinoSS TV</a><span>›</span><span><?= e($video['format']) ?></span></nav>
        <header class="video-story-heading">
            <div><small>VECINOSS TV · <?= e($video['format']) ?></small><h1><?= e($video['title']) ?></h1></div>
            <div class="video-meta"><span><?= e($video['commune']) ?></span><time datetime="<?= e($video['published_at']) ?>"><?= e(date_es($video['published_at'])) ?></time></div>
        </header>
        <div class="video-stage">
            <div class="video-stage-bar"><span><i></i> REPRODUCIENDO EN VECINOSS TV</span><b><?= e($video['commune']) ?></b></div>
            <div class="video-player video-player-inline" data-video-inline data-video-url="<?= e($video['video_url']) ?>" data-video-title="<?= e($video['title']) ?>"><a class="primary-button" href="<?= e($video['video_url']) ?>" target="_blank" rel="noopener">Abrir video en su plataforma ↗</a></div>
        </div>
    </div>
</article>
<section class="video-after shell">
    <div class="video-description"><div class="video-description-title"><small>SOBRE ESTE VIDEO</small><span><?= e($video['format']) ?></span></div><?php if ($video['description']): ?><div><?= str_contains($video['description'], '<') ? $video['description'] : nl2br(e($video['description'])) ?></div><?php else: ?><p>Contenido audiovisual de VecinoSS TV.</p><?php endif; ?><a class="video-channel-link" href="<?= url('/videos') ?>">Explorar el canal completo <b>→</b></a></div>
    <aside class="video-actions"><b>Comparte esta historia</b><p>Ayuda a que la información local llegue a más vecinos.</p><div class="share-bar compact-share" aria-label="Compartir este video"><a href="https://www.facebook.com/sharer/sharer.php?u=<?= rawurlencode($shareUrl) ?>" target="_blank" rel="noopener" aria-label="Facebook"><img src="<?= asset('images/social/facebook.svg') ?>" alt=""></a><a href="https://twitter.com/intent/tweet?url=<?= rawurlencode($shareUrl) ?>&text=<?= rawurlencode($video['title']) ?>" target="_blank" rel="noopener" aria-label="X"><img src="<?= asset('images/social/x.svg') ?>" alt=""></a><a href="https://wa.me/?text=<?= rawurlencode($video['title'].' '.$shareUrl) ?>" target="_blank" rel="noopener" aria-label="WhatsApp"><img src="<?= asset('images/social/whatsapp.svg') ?>" alt=""></a><button type="button" data-copy-url="<?= e($shareUrl) ?>" aria-label="Copiar enlace"><img src="<?= asset('images/social/link.svg') ?>" alt=""><b>Copiar</b></button></div></aside>
</section>
<?php if ($related): ?><section class="dark-section video-related"><div class="shell section"><div class="section-title"><div><small>CONTINÚA VIENDO</small><h2>Más de VecinoSS TV</h2></div><a href="<?= url('/videos') ?>">Ver todos →</a></div><div class="video-grid"><?php foreach ($related as $item): ?><article><a class="video-cover" href="<?= url('/video/'.$item['id']) ?>"><img src="<?= e(post_image($item['cover_image'])) ?>" alt="<?= e($item['title']) ?>" loading="lazy"><span>▶</span></a><small><?= e($item['commune']) ?> · <?= e($item['format']) ?></small><h3><a href="<?= url('/video/'.$item['id']) ?>"><?= e($item['title']) ?></a></h3><a class="more" href="<?= url('/video/'.$item['id']) ?>">Ver video →</a></article><?php endforeach; ?></div></div></section><?php endif; ?>
