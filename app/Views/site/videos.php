<section class="media-hero"><div class="shell"><div><small>CANAL DIGITAL LOCAL</small><h1>Pulso Angelino <em>TV</em></h1><p>Historias, entrevistas y actualidad en video desde las seis comunas de la Provincia de Biobío.</p></div><span aria-hidden="true">▶</span></div></section>
<section class="shell section media-catalog">
    <div class="catalog-tools">
        <div><small>EXPLORA EL CANAL</small><h2>Todos los videos</h2></div>
        <div class="video-filters" data-video-filters>
            <button class="active" type="button" data-filter="all">Todos</button>
            <?php foreach ($formats as $format): ?><button type="button" data-filter="<?= e(mb_strtolower($format)) ?>"><?= e($format) ?></button><?php endforeach; ?>
        </div>
    </div>
    <?php if ($videos): ?><div class="video-catalog-grid"><?php foreach ($videos as $index=>$item): ?><article class="<?= $index===0?'video-catalog-lead':'' ?>" data-video-card data-format="<?= e(mb_strtolower($item['format'])) ?>">
        <a class="video-cover" href="<?= url('/video/'.$item['id']) ?>"><img src="<?= e(post_image($item['cover_image'])) ?>" alt="<?= e($item['title']) ?>" <?= $index ? 'loading="lazy"' : '' ?>><span>▶</span><time datetime="<?= e($item['published_at']) ?>"><?= e(date_es($item['published_at'])) ?></time></a>
        <div><small><?= e($item['commune']) ?> · <?= e($item['format']) ?></small><h2><a href="<?= url('/video/'.$item['id']) ?>"><?= e($item['title']) ?></a></h2><?php if($item['description']): ?><p><?= e(strip_tags($item['description'])) ?></p><?php endif; ?><a class="more" href="<?= url('/video/'.$item['id']) ?>">Reproducir video →</a></div>
    </article><?php endforeach; ?></div><div class="empty video-empty" hidden data-video-empty>No hay videos en este formato por ahora.</div><?php else: ?><div class="empty"><b>Próximamente</b><p>Estamos preparando nuevos contenidos para Pulso Angelino TV.</p></div><?php endif; ?>
</section>
