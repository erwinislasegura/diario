<?php
$shareUrl = (isset($_SERVER['HTTPS']) ? 'https' : 'http') . '://' . ($_SERVER['HTTP_HOST'] ?? 'localhost') . url('/noticia/' . $post['slug']);
$isEvent = ($post['parent_category_name'] ?? '') === 'Eventos' || $post['category_slug'] === 'eventos';
$plainBody = trim(strip_tags($post['body']));
$readingMinutes = max(1, (int) ceil(str_word_count($plainBody) / 210));
?>
<article class="story<?= $isEvent ? ' event-story' : '' ?>">
    <div class="reading-progress" aria-hidden="true"><span data-reading-progress></span></div>
    <div class="story-head shell">
        <nav class="breadcrumbs" aria-label="Migas de pan"><a href="<?= url('/') ?>">Inicio</a><span>›</span><a href="<?= $isEvent ? url('/eventos') : url('/categoria/'.$post['category_slug']) ?>"><?= e($isEvent ? 'Agenda y eventos' : $post['category_name']) ?></a></nav>
        <div class="story-heading">
            <div>
                <div class="story-kicker"><small><?= e($isEvent ? 'AGENDA LOCAL · '.$post['category_name'] : $post['category_name']) ?></small><span><?= $readingMinutes ?> min de lectura</span></div>
                <h1><?= e($post['title']) ?></h1>
                <?php if ($post['excerpt']): ?><p class="standfirst"><?= e($post['excerpt']) ?></p><?php endif; ?>
                <div class="byline">
                    <span>Por <b><?= e($post['author_name']) ?></b></span>
                    <time datetime="<?= e($post['published_at']) ?>"><?= e(date_es($post['published_at'])) ?></time>
                </div>
            </div>
            <?php if ($isEvent): ?><aside class="event-fact"><span>Fecha</span><b><?= e(date('d', strtotime($post['published_at']))) ?></b><strong><?= e(date_es($post['published_at'])) ?></strong><a href="mailto:prensa@vecinoss.cl?subject=Consulta sobre <?= rawurlencode($post['title']) ?>">Consultar evento →</a></aside><?php endif; ?>
        </div>
    </div>

    <div class="story-media shell">
        <img class="article-image" src="<?= e(post_image($post['image'])) ?>" alt="<?= e($post['title']) ?>">
        <div class="story-media-shade" aria-hidden="true"></div>
        <span class="story-media-brand">V<span>SS</span></span>
        <?php if ($isEvent): ?><span class="story-media-label">Provincia de San Antonio</span><?php endif; ?>
    </div>

    <div class="story-content shell">
        <aside class="story-share" aria-label="Compartir esta publicación">
            <span>Compartir</span>
            <a href="https://www.facebook.com/sharer/sharer.php?u=<?= rawurlencode($shareUrl) ?>" target="_blank" rel="noopener" aria-label="Compartir en Facebook"><img src="<?= asset('images/social/facebook.svg') ?>" alt=""></a>
            <a href="https://twitter.com/intent/tweet?url=<?= rawurlencode($shareUrl) ?>&text=<?= rawurlencode($post['title']) ?>" target="_blank" rel="noopener" aria-label="Compartir en X"><img src="<?= asset('images/social/x.svg') ?>" alt=""></a>
            <a href="https://wa.me/?text=<?= rawurlencode($post['title'].' '.$shareUrl) ?>" target="_blank" rel="noopener" aria-label="Compartir por WhatsApp"><img src="<?= asset('images/social/whatsapp.svg') ?>" alt=""></a>
            <button type="button" data-copy-url="<?= e($shareUrl) ?>" aria-label="Copiar enlace"><img src="<?= asset('images/social/link.svg') ?>" alt=""><b>Copiar</b></button>
        </aside>
        <div class="article-body">
            <?= str_contains($post['body'], '<') ? $post['body'] : nl2br(e($post['body'])) ?>
            <?php if (!empty($post['tag_names'])): ?><div class="story-tags" aria-label="Temas"><?php foreach (explode(', ', $post['tag_names']) as $tag): ?><span>#<?= e($tag) ?></span><?php endforeach; ?></div><?php endif; ?>
            <div class="story-end"><span></span><b>VecinoSS</b><p>Información local, cercana y útil para nuestra comunidad.</p></div>
        </div>
        <aside class="story-summary">
            <small><?= $isEvent ? 'INFORMACIÓN' : 'EN BREVE' ?></small>
            <p><?= e($post['excerpt'] ?: 'La información más importante de nuestra provincia, explicada de forma clara y cercana.') ?></p>
            <dl>
                <div><dt>Sección</dt><dd><?= e($post['category_name']) ?></dd></div>
                <div><dt>Publicado</dt><dd><?= e(date_es($post['published_at'])) ?></dd></div>
                <div><dt>Lectura</dt><dd><?= $readingMinutes ?> min</dd></div>
            </dl>
            <?php if ($isEvent): ?><a href="mailto:prensa@vecinoss.cl?subject=Consulta sobre <?= rawurlencode($post['title']) ?>">Consultar actividad →</a><?php else: ?><a href="mailto:prensa@vecinoss.cl?subject=Información relacionada con <?= rawurlencode($post['title']) ?>">Aportar información →</a><?php endif; ?>
        </aside>
    </div>
</article>

<?php if ($related): ?><section class="shell section related-section"><div class="section-title"><div><small><?= $isEvent ? 'MÁS PANORAMAS' : 'CONTINÚA LEYENDO' ?></small><h2><?= $isEvent ? 'Otros eventos' : 'Noticias relacionadas' ?></h2></div><a href="<?= $isEvent ? url('/eventos') : url('/categoria/'.$post['category_slug']) ?>">Ver todo →</a></div><div class="news-grid"><?php foreach($related as $relatedPost){if((int)$relatedPost['id']===(int)$post['id'])continue;$post=$relatedPost;require __DIR__.'/../partials/card.php';} ?></div></section><?php endif; ?>
