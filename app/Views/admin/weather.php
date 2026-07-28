<section class="admin-panel settings-panel">
    <div class="panel-heading"><div><h2>Módulo del tiempo</h2><p>Controla la sección de portada y la ubicación usada cuando el visitante no comparte su posición.</p></div></div>
    <?php if ($error): ?><div class="form-error"><?= e($error) ?></div><?php endif; ?>
    <form method="post" action="<?= url('/admin/weather') ?>">
        <?= csrf_field() ?>
        <label class="check"><input type="checkbox" name="weather_enabled" value="1" <?= ($weather['weather_enabled'] ?? '0') === '1' ? 'checked' : '' ?>> Mostrar la sección del tiempo en la portada</label>
        <label>Título de la sección<input name="weather_title" maxlength="100" required value="<?= e($weather['weather_title'] ?? '') ?>"></label>
        <label>Nombre de la ubicación de respaldo<input name="weather_fallback_name" maxlength="100" required value="<?= e($weather['weather_fallback_name'] ?? '') ?>"></label>
        <div class="coordinate-fields">
            <label>Latitud<input type="number" name="weather_fallback_latitude" min="-90" max="90" step="0.0001" required value="<?= e($weather['weather_fallback_latitude'] ?? '') ?>"></label>
            <label>Longitud<input type="number" name="weather_fallback_longitude" min="-180" max="180" step="0.0001" required value="<?= e($weather['weather_fallback_longitude'] ?? '') ?>"></label>
        </div>
        <p class="hint">Si el visitante autoriza la geolocalización, el navegador mostrará el tiempo de su posición. En caso contrario se usarán estas coordenadas.</p>
        <button class="primary-button" type="submit">Guardar configuración</button>
    </form>
</section>
