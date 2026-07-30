<section class="search-hero">
  <div class="shell">
    <nav class="breadcrumbs breadcrumbs-dark" aria-label="Migas de pan"><a href="<?= url('/') ?>">Inicio</a><span>›</span><span>Buscar</span></nav>
    <small>HEMEROTECA DIGITAL</small>
    <h1>Busca en Pulso Angelino</h1>
    <form class="search-page-form" action="<?= url('/buscar') ?>" method="get" role="search">
      <label for="search-page-input">¿Qué noticia estás buscando?</label>
      <div><input id="search-page-input" type="search" name="q" value="<?= e($query) ?>" placeholder="Ej. Los Ángeles, comunidad, deportes…" maxlength="100" required autofocus><button type="submit">Buscar <span>→</span></button></div>
    </form>
  </div>
</section>
<section class="shell section search-results" aria-live="polite">
  <?php if ($query !== ''): ?>
    <header class="search-results-heading"><div><small>RESULTADOS</small><h2><?= count($posts) ?> <?= count($posts) === 1 ? 'noticia encontrada' : 'noticias encontradas' ?></h2></div><p>Para <strong>“<?= e($query) ?>”</strong></p></header>
    <?php if ($posts): ?><div class="news-grid category-news-grid search-news-grid"><?php foreach ($posts as $post): ?><?php require __DIR__.'/../partials/card.php'; ?><?php endforeach; ?></div>
    <?php else: ?><div class="search-empty"><span aria-hidden="true">⌕</span><div><h2>No encontramos coincidencias</h2><p>Prueba con términos más generales, revisa la ortografía o explora nuestras secciones.</p><a href="<?= url('/') ?>">Ir a la portada →</a></div></div><?php endif; ?>
  <?php else: ?>
    <div class="search-welcome"><b>Noticias locales, a un clic</b><p>Escribe una palabra o frase para buscar por título, contenido, categoría o etiqueta.</p></div>
  <?php endif; ?>
</section>
