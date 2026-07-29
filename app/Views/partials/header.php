<div class="utility"><div class="shell"><span><?= e(date_es(date('Y-m-d H:i:s'))) ?> · Provincia de Biobío</span><span>Edición digital</span></div></div>
<header class="site-header">
  <div class="shell brandbar"><button class="menu-button" aria-label="Abrir menú" aria-controls="main-menu" aria-expanded="false"><span></span><span></span><span></span></button><a href="<?= url('/') ?>" class="brand" aria-label="Pulso Angelino, ir al inicio"><img src="<?= url('/logo/logo.png') ?>" alt="Pulso Angelino"></a><a class="send" href="mailto:prensa@pulsoangelino.cl"><span class="send-full">Envía tu noticia</span><span class="send-short">Enviar</span><b>→</b></a></div>
  <nav class="main-nav" id="main-menu" aria-label="Navegación principal"><div class="shell">
    <a href="<?= url('/') ?>">Inicio</a>
    <?php foreach (array_slice($categories ?? App\Models\Category::topLevel(), 0, 6) as $navCategory): ?><a href="<?= category_url($navCategory) ?>"><?= e($navCategory['name']) ?></a><?php endforeach; ?>
  </div></nav>
</header>
<?php $tickerPosts = $tickerPosts ?? App\Models\Post::publishedToday(); ?>
<div class="ticker" aria-label="Últimas noticias del día"><div class="shell">
  <b>AHORA</b>
  <div class="ticker-window"><?php if ($tickerPosts): ?><div class="ticker-track"><?php for ($copy=0;$copy<2;$copy++): ?><div class="ticker-items" <?= $copy?'aria-hidden="true"':'' ?>><?php foreach($tickerPosts as $tickerPost): ?><a href="<?= url('/noticia/'.$tickerPost['slug']) ?>"><?= e($tickerPost['title']) ?></a><?php endforeach; ?></div><?php endfor; ?></div><?php else: ?><span class="ticker-empty">Pulso Angelino informa: la actualidad local, cerca de ti.</span><?php endif; ?></div>
  <time class="ticker-clock" data-live-clock datetime="<?= date('c') ?>"><span aria-hidden="true"></span><b><?= date('H:i') ?></b></time>
</div></div>
