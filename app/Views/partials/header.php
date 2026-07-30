<div class="utility"><div class="shell"><span>EN VIVO</span><b><?= e(date_es(date('Y-m-d H:i:s'))) ?></b><span>Los Ángeles · Provincia de Biobío</span><span>Edición digital</span></div></div>
<header class="site-header">
  <div class="shell brandbar">
    <button class="menu-button" aria-label="Abrir menú" aria-controls="main-menu" aria-expanded="false"><span></span><span></span><span></span></button>
    <a href="<?= url('/') ?>" class="brand" aria-label="Pulso Angelino, ir al inicio"><img src="<?= url('/logo/logo.png') ?>" alt="Pulso Angelino"></a>
    <a class="mobile-search-trigger" href="<?= url('/buscar') ?>" aria-label="Buscar noticias"><svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="10.5" cy="10.5" r="6.5"></circle><path d="m15.5 15.5 5 5"></path></svg></a>
    <div class="brand-statement"><b>NOTICIAS</b><span></span><b>COMUNIDAD</b><span></span><b>REGIÓN</b><small>La información que mueve a Los Ángeles</small></div>
    <a class="send" href="mailto:prensa@pulsoangelino.cl"><span class="send-full">Envía tu noticia</span><span class="send-short">Enviar</span><b>→</b></a>
  </div>
  <nav class="main-nav" id="main-menu" aria-label="Navegación principal"><div class="shell">
    <a class="nav-home" href="<?= url('/') ?>" aria-label="Inicio"><span>Inicio</span></a>
    <?php foreach (array_slice($categories ?? App\Models\Category::topLevel(), 0, 6) as $navCategory): ?><a href="<?= category_url($navCategory) ?>"><?= e($navCategory['name']) ?></a><?php endforeach; ?>
    <form class="header-search" action="<?= url('/buscar') ?>" method="get" role="search"><label class="sr-only" for="header-search-input">Buscar noticias</label><input id="header-search-input" type="search" name="q" value="<?= e($query ?? '') ?>" placeholder="Buscar noticias" maxlength="100" required><button type="submit" aria-label="Buscar"><svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="10.8" cy="10.8" r="6.8"></circle><path d="m16 16 5 5"></path></svg></button></form>
  </div></nav>
</header>
<?php $tickerPosts = $tickerPosts ?? App\Models\Post::publishedToday(); ?>
<div class="ticker" aria-label="Últimas noticias del día"><div class="shell">
  <b>ÚLTIMO MINUTO</b>
  <div class="ticker-window"><?php if ($tickerPosts): ?><div class="ticker-track"><?php for ($copy=0;$copy<2;$copy++): ?><div class="ticker-items" <?= $copy?'aria-hidden="true"':'' ?>><?php foreach($tickerPosts as $tickerPost): ?><a href="<?= url('/noticia/'.$tickerPost['slug']) ?>"><?= e($tickerPost['title']) ?></a><?php endforeach; ?></div><?php endfor; ?></div><?php else: ?><span class="ticker-empty">Pulso Angelino informa: la actualidad local, cerca de ti.</span><?php endif; ?></div>
  <time class="ticker-clock" data-live-clock datetime="<?= date('c') ?>"><span aria-hidden="true"></span><b><?= date('H:i') ?></b></time>
</div></div>
