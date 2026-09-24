<?php
declare(strict_types=1);

// Built-in web server: serve existing static files directly
if (php_sapi_name() === 'cli-server') {
    $urlPath = parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH);
    if ($urlPath !== '/' && is_file(__DIR__ . $urlPath)) {
        return false;
    }
}

define('HB_ROOT', dirname(__DIR__));

// 1. PSR-4 Fallback Autoloader
spl_autoload_register(function (string $class) {
    $prefix = 'App\\';
    $baseDir = HB_ROOT . '/app/';

    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }

    $relativeClass = substr($class, $len);
    $file = $baseDir . str_replace('\\', '/', $relativeClass) . '.php';

    if (file_exists($file)) {
        require_once $file;
    }
});

// 2. Load Helpers & Constants
require_once HB_ROOT . '/config/constants.php';
require_once HB_ROOT . '/app/Helpers/url.php';
require_once HB_ROOT . '/app/Helpers/format.php';
require_once HB_ROOT . '/app/Helpers/security.php';
require_once HB_ROOT . '/app/Helpers/food.php';

// 3. Load Environment
\App\Core\Env::load(HB_ROOT . '/.env');

// 4. Initialize Core Application
$app = new \App\Core\App();
$router = $app->getRouter();

// 5. Register Routes
require_once HB_ROOT . '/routes/web.php';
require_once HB_ROOT . '/routes/api.php';

// 6. Run Application Lifecycle
$app->run();
