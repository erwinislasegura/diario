document.querySelector('.menu-button')?.addEventListener('click', event => {
  const nav = document.querySelector('.main-nav');
  const open = nav.classList.toggle('open');
  event.currentTarget.setAttribute('aria-expanded', String(open));
  event.currentTarget.setAttribute('aria-label', open ? 'Cerrar menú' : 'Abrir menú');
  document.body.classList.toggle('menu-open', open);
});

document.querySelectorAll('.main-nav a').forEach(link => link.addEventListener('click', () => {
  const nav = document.querySelector('.main-nav');
  const button = document.querySelector('.menu-button');
  nav?.classList.remove('open');
  document.body.classList.remove('menu-open');
  button?.setAttribute('aria-expanded', 'false');
  button?.setAttribute('aria-label', 'Abrir menú');
}));

document.addEventListener('keydown', event => {
  if (event.key !== 'Escape') return;
  const nav = document.querySelector('.main-nav');
  if (!nav?.classList.contains('open')) return;
  nav.classList.remove('open');
  document.body.classList.remove('menu-open');
  const button = document.querySelector('.menu-button');
  button?.setAttribute('aria-expanded', 'false');
  button?.setAttribute('aria-label', 'Abrir menú');
  button?.focus();
});

const liveClock = document.querySelector('[data-live-clock]');
if (liveClock) {
  const clockValue = liveClock.querySelector('b');
  const formatter = new Intl.DateTimeFormat('es-CL', { hour: '2-digit', minute: '2-digit', second: '2-digit', hour12: false, timeZone: 'America/Santiago' });
  const updateClock = () => {
    const now = new Date();
    liveClock.dateTime = now.toISOString();
    clockValue.textContent = formatter.format(now);
  };
  updateClock();
  setInterval(updateClock, 1000);
}

const readingProgress = document.querySelector('[data-reading-progress]');
const story = document.querySelector('.story');
if (readingProgress && story) {
  let ticking = false;
  const updateReadingProgress = () => {
    const bounds = story.getBoundingClientRect();
    const available = Math.max(1, story.offsetHeight - window.innerHeight);
    const travelled = Math.min(available, Math.max(0, -bounds.top));
    readingProgress.style.width = `${(travelled / available) * 100}%`;
    ticking = false;
  };
  const requestProgressUpdate = () => {
    if (ticking) return;
    ticking = true;
    requestAnimationFrame(updateReadingProgress);
  };
  updateReadingProgress();
  addEventListener('scroll', requestProgressUpdate, { passive: true });
  addEventListener('resize', requestProgressUpdate, { passive: true });
}

const weatherWidget = document.querySelector('[data-weather-widget]');
if (weatherWidget) {
  const weatherCodes = { 0: ['Despejado', '☀'], 1: ['Mayormente despejado', '🌤'], 2: ['Parcialmente nublado', '⛅'], 3: ['Nublado', '☁'], 45: ['Niebla', '🌫'], 48: ['Niebla', '🌫'], 51: ['Llovizna', '🌦'], 53: ['Llovizna', '🌦'], 55: ['Llovizna intensa', '🌧'], 61: ['Lluvia', '🌧'], 63: ['Lluvia', '🌧'], 65: ['Lluvia intensa', '🌧'], 80: ['Chubascos', '🌦'], 81: ['Chubascos', '🌦'], 82: ['Chubascos fuertes', '⛈'], 95: ['Tormenta', '⛈'] };
  const showWeather = async (latitude, longitude, locationName) => {
    try {
      const endpoint = new URL('https://api.open-meteo.com/v1/forecast');
      endpoint.search = new URLSearchParams({ latitude, longitude, current: 'temperature_2m,relative_humidity_2m,apparent_temperature,weather_code,wind_speed_10m', timezone: 'auto' });
      const response = await fetch(endpoint);
      if (!response.ok) throw new Error('Weather request failed');
      const { current } = await response.json();
      const condition = weatherCodes[current.weather_code] || ['Condiciones variables', '◌'];
      weatherWidget.querySelector('[data-weather-location]').textContent = locationName;
      weatherWidget.querySelector('[data-weather-temperature]').textContent = `${Math.round(current.temperature_2m)}°`;
      weatherWidget.querySelector('[data-weather-description]').textContent = condition[0];
      weatherWidget.querySelector('[data-weather-icon]').textContent = condition[1];
      weatherWidget.querySelector('[data-weather-apparent]').textContent = `${Math.round(current.apparent_temperature)}°`;
      weatherWidget.querySelector('[data-weather-humidity]').textContent = `${current.relative_humidity_2m}%`;
      weatherWidget.querySelector('[data-weather-wind]').textContent = `${Math.round(current.wind_speed_10m)} km/h`;
    } catch (error) { weatherWidget.querySelector('[data-weather-description]').textContent = 'No fue posible actualizar el tiempo'; }
  };
  const fallback = () => showWeather(weatherWidget.dataset.latitude, weatherWidget.dataset.longitude, weatherWidget.dataset.fallbackName);
  const showVisitorWeather = async ({ latitude, longitude }) => {
    const coordinates = `${Number(latitude).toFixed(2)}, ${Number(longitude).toFixed(2)}`;
    await showWeather(latitude, longitude, `Ubicación actual (${coordinates})`);
    try {
      const endpoint = new URL('https://api.bigdatacloud.net/data/reverse-geocode-client');
      endpoint.search = new URLSearchParams({ latitude, longitude, localityLanguage: 'es' });
      const response = await fetch(endpoint);
      if (!response.ok) throw new Error('Reverse geocoding request failed');
      const place = await response.json();
      const locality = place.locality || place.city || place.localityInfo?.administrative?.[3]?.name;
      const region = place.principalSubdivision;
      const locationName = [locality, region].filter((value, index, values) => value && values.indexOf(value) === index).join(', ');
      if (locationName) weatherWidget.querySelector('[data-weather-location]').textContent = locationName;
    } catch (error) {
      // Las coordenadas visibles siguen identificando el lugar si el geocodificador no responde.
    }
  };
  const locateVisitor = () => {
    if (!navigator.geolocation) return fallback();
    navigator.geolocation.getCurrentPosition(position => showVisitorWeather(position.coords), fallback, { enableHighAccuracy: false, timeout: 8000, maximumAge: 600000 });
  };
  weatherWidget.querySelector('[data-weather-locate]').addEventListener('click', locateVisitor);
  locateVisitor();
}

document.querySelectorAll('[data-copy-url]').forEach(copyButton => copyButton.addEventListener('click', async event => {
  const button = event.currentTarget;
  try {
    await navigator.clipboard.writeText(button.dataset.copyUrl);
    const label = button.querySelector('b');
    const originalLabel = label.textContent;
    label.textContent = '¡Copiado!';
    setTimeout(() => { label.textContent = originalLabel; }, 1800);
  } catch (error) { window.prompt('Copia este enlace:', button.dataset.copyUrl); }
}));

const embedUrl = (value, autoplay = false) => {
  try {
    const url = new URL(value);
    const host = url.hostname.replace(/^www\./, '');
    const play = autoplay ? '?autoplay=1' : '';
    let id = '';
    if (host === 'youtu.be') id = url.pathname.split('/')[1] || '';
    if (host === 'youtube.com' || host.endsWith('.youtube.com')) id = url.searchParams.get('v') || url.pathname.match(/^\/(?:shorts|embed)\/([^/]+)/)?.[1] || '';
    if (id && /^[a-zA-Z0-9_-]{6,}$/.test(id)) return `https://www.youtube-nocookie.com/embed/${id}${play}`;
    if (host === 'vimeo.com' || host.endsWith('.vimeo.com')) {
      id = url.pathname.match(/\/(?:video\/)?(\d+)/)?.[1] || '';
      if (id) return `https://player.vimeo.com/video/${id}${play}`;
    }
    if (host === 'dai.ly') id = url.pathname.split('/')[1] || '';
    if (host === 'dailymotion.com' || host.endsWith('.dailymotion.com')) id = url.pathname.match(/\/video\/([^_/?]+)/)?.[1] || '';
    if (id && /^[a-zA-Z0-9]+$/.test(id)) return `https://www.dailymotion.com/embed/video/${id}${play}`;
  } catch (error) { return null; }
  return null;
};

const createVideoMedia = (source, title, autoplay = false) => {
  const embedded = embedUrl(source, autoplay);
  const directVideo = /\.(?:mp4|webm|ogg)(?:[?#].*)?$/i.test(source);
  if (!embedded && !directVideo) return null;
  const media = document.createElement(embedded ? 'iframe' : 'video');
  media.src = embedded || source;
  media.title = title;
  if (embedded) {
    media.allow = 'autoplay; fullscreen; picture-in-picture';
    media.allowFullscreen = true;
  } else {
    media.controls = true;
    media.autoplay = autoplay;
  }
  return media;
};

document.querySelectorAll('[data-video-inline]').forEach(player => {
  const media = createVideoMedia(player.dataset.videoUrl, player.dataset.videoTitle);
  if (media) player.replaceChildren(media);
});

const videoFilters = document.querySelector('[data-video-filters]');
if (videoFilters) {
  const cards = [...document.querySelectorAll('[data-video-card]')];
  const emptyState = document.querySelector('[data-video-empty]');
  videoFilters.querySelectorAll('[data-filter]').forEach(button => button.addEventListener('click', () => {
    const filter = button.dataset.filter;
    let visible = 0;
    videoFilters.querySelectorAll('[data-filter]').forEach(item => item.classList.toggle('active', item === button));
    cards.forEach(card => {
      const show = filter === 'all' || card.dataset.format === filter;
      card.hidden = !show;
      if (show) visible += 1;
    });
    if (emptyState) emptyState.hidden = visible !== 0;
  }));
}

const videoDialog = document.querySelector('[data-video-dialog]');
if (videoDialog) {
  const player = videoDialog.querySelector('[data-video-player]');
  const playVideo = button => {
    const source = button.dataset.videoUrl;
    const media = createVideoMedia(source, button.dataset.videoTitle, true);
    if (!media) {
      window.open(source, '_blank', 'noopener,noreferrer');
      return;
    }
    player.replaceChildren();
    player.append(media);
    videoDialog.querySelector('[data-video-dialog-title]').textContent = button.dataset.videoTitle;
    videoDialog.showModal();
  };
  document.querySelectorAll('[data-video-url]').forEach(button => button.addEventListener('click', () => playVideo(button)));
  const closeVideo = () => { videoDialog.close(); player.replaceChildren(); };
  videoDialog.querySelector('[data-video-close]').addEventListener('click', closeVideo);
  videoDialog.addEventListener('click', event => { if (event.target === videoDialog) closeVideo(); });
  videoDialog.addEventListener('close', () => player.replaceChildren());
}

document.querySelectorAll('[data-rich-editor]').forEach(editor => {
  const canvas = editor.querySelector('.rich-canvas');
  const source = editor.querySelector('.rich-source');
  const sync = () => { source.value = canvas.innerHTML; };
  editor.querySelectorAll('[data-command]').forEach(button => button.addEventListener('click', () => {
    canvas.focus();
    let value = button.dataset.value || null;
    if (button.dataset.command === 'createLink') value = window.prompt('Dirección del enlace (https://…)');
    if (button.dataset.command !== 'createLink' || value) document.execCommand(button.dataset.command, false, value);
    sync();
  }));
  canvas.addEventListener('input', sync);
  editor.closest('form')?.addEventListener('submit', sync);
});
