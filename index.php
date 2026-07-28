<?php
declare(strict_types=1);

require __DIR__ . '/app/bootstrap.php';

use App\Controllers\AdminController;
use App\Controllers\SiteController;

$requestPath = rawurldecode((string) parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));
$base = base_url();
if ($base !== '' && ($requestPath === $base || str_starts_with($requestPath, $base . '/'))) {
    $requestPath = substr($requestPath, strlen($base));
}
$requestPath = preg_replace('#^/index\.php(?:/|$)#', '/', $requestPath) ?? $requestPath;
$path = '/' . trim($requestPath, '/');
$path = $path === '//' ? '/' : $path;
$method = $_SERVER['REQUEST_METHOD'];
$site = new SiteController();
$admin = new AdminController();

if ($path === '/' && $method === 'GET') $site->home();
elseif ($path === '/admin/login' && $method === 'GET') $admin->login();
elseif ($path === '/admin/login' && $method === 'POST') $admin->authenticate();
elseif ($path === '/admin/logout' && $method === 'POST') $admin->logout();
elseif ($path === '/admin' && $method === 'GET') $admin->dashboard();
elseif ($path === '/admin/posts' && $method === 'GET') $admin->posts();
elseif ($path === '/admin/events' && $method === 'GET') $admin->events();
elseif ($path === '/admin/events/create' && $method === 'GET') $admin->createEvent();
elseif ($path === '/admin/events' && $method === 'POST') $admin->storeEvent();
elseif (preg_match('#^/admin/events/(\d+)/edit$#', $path, $m) && $method === 'GET') $admin->editEvent((int) $m[1]);
elseif (preg_match('#^/admin/events/(\d+)$#', $path, $m) && $method === 'POST' && ($_POST['_method'] ?? '') === 'DELETE') $admin->destroyEvent((int) $m[1]);
elseif (preg_match('#^/admin/events/(\d+)$#', $path, $m) && $method === 'POST') $admin->storeEvent((int) $m[1]);
elseif ($path === '/admin/weather' && $method === 'GET') $admin->weather();
elseif ($path === '/admin/weather' && $method === 'POST') $admin->saveWeather();
elseif ($path === '/admin/videos' && $method === 'GET') $admin->videos();
elseif ($path === '/admin/videos/create' && $method === 'GET') $admin->createVideo();
elseif ($path === '/admin/videos' && $method === 'POST') $admin->saveVideo();
elseif (preg_match('#^/admin/videos/(\d+)/edit$#', $path, $m) && $method === 'GET') $admin->editVideo((int) $m[1]);
elseif (preg_match('#^/admin/videos/(\d+)$#', $path, $m) && $method === 'POST' && ($_POST['_method'] ?? '') === 'DELETE') $admin->destroyVideo((int) $m[1]);
elseif (preg_match('#^/admin/videos/(\d+)$#', $path, $m) && $method === 'POST') $admin->saveVideo((int) $m[1]);
elseif ($path === '/admin/posts/create' && $method === 'GET') $admin->create();
elseif ($path === '/admin/posts' && $method === 'POST') $admin->store();
elseif (preg_match('#^/admin/posts/(\d+)/edit$#', $path, $m) && $method === 'GET') $admin->edit((int) $m[1]);
elseif (preg_match('#^/admin/posts/(\d+)$#', $path, $m) && $method === 'POST' && ($_POST['_method'] ?? '') === 'DELETE') $admin->destroy((int) $m[1]);
elseif (preg_match('#^/admin/posts/(\d+)$#', $path, $m) && $method === 'POST') $admin->store((int) $m[1]);
elseif (preg_match('#^/noticia/([a-z0-9-]+)$#', $path, $m) && $method === 'GET') $site->article($m[1]);
elseif ($path === '/videos' && $method === 'GET') $site->videos();
elseif ($path === '/eventos' && $method === 'GET') $site->events();
elseif (preg_match('#^/video/(\d+)$#', $path, $m) && $method === 'GET') $site->video((int) $m[1]);
elseif (preg_match('#^/categoria/([a-z0-9-]+)$#', $path, $m) && $method === 'GET') $site->category($m[1]);
else { http_response_code(404); $site->notFound(); }
