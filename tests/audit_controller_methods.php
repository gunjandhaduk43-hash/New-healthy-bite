<?php
declare(strict_types=1);

define('HB_ROOT', dirname(__DIR__));

spl_autoload_register(function (string $class) {
    $prefix = 'App\\';
    $baseDir = HB_ROOT . '/app/';
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) return;
    $relativeClass = substr($class, $len);
    $file = $baseDir . str_replace('\\', '/', $relativeClass) . '.php';
    if (file_exists($file)) {
        require_once $file;
    }
});

require_once HB_ROOT . '/config/constants.php';
require_once HB_ROOT . '/app/Helpers/url.php';
require_once HB_ROOT . '/app/Helpers/format.php';
require_once HB_ROOT . '/app/Helpers/security.php';
require_once HB_ROOT . '/app/Helpers/food.php';

\App\Core\Env::load(HB_ROOT . '/.env');

echo "=== 1. AUDITING ALL REGISTERED ROUTES & CONTROLLER METHODS ===\n";

$router = new \App\Core\Router();
require HB_ROOT . '/routes/web.php';
require HB_ROOT . '/routes/api.php';

$reflector = new ReflectionClass($router);
$prop = $reflector->getProperty('routes');
$prop->setAccessible(true);
$routes = $prop->getValue($router);

$errors = [];
$totalRoutes = 0;

foreach ($routes as $route) {
    $totalRoutes++;
    $method = $route['method'];
    $path = $route['path'];
    $handler = $route['handler'];

    if (is_array($handler)) {
        [$class, $action] = $handler;
        if (!class_exists($class)) {
            $errors[] = "Route [{$method}] {$path} -> Class '{$class}' does not exist!";
        } elseif (!method_exists($class, $action)) {
            $errors[] = "Route [{$method}] {$path} -> Method '{$class}::{$action}' does not exist!";
        } else {
            $rMethod = new ReflectionMethod($class, $action);
            if (!$rMethod->isPublic()) {
                $errors[] = "Route [{$method}] {$path} -> Method '{$class}::{$action}' is not public!";
            }
        }
    } elseif (!is_callable($handler)) {
        $errors[] = "Route [{$method}] {$path} -> Handler is neither [Class, method] nor callable!";
    }
}

echo "Total registered routes scanned: {$totalRoutes}\n";
if (empty($errors)) {
    echo "✓ All controller classes and methods for all registered routes exist and are public!\n\n";
} else {
    echo "ERRORS FOUND:\n" . implode("\n", $errors) . "\n\n";
}

echo "=== 2. AUDITING REPOSITORY METHOD CALLS IN CONTROLLERS ===\n";
// Let's scan all PHP files in app/Controllers
$controllerFiles = glob(HB_ROOT . '/app/Controllers/**/*.php');
$controllerFiles = array_merge($controllerFiles, glob(HB_ROOT . '/app/Controllers/*.php'));

$callRegex = '/->([a-zA-Z0-9_]+)\s*\(/';

foreach ($controllerFiles as $cFile) {
    $code = file_get_contents($cFile);
    // Find instantiations like $this->orderRepo = new OrderRepository();
    preg_match_all('/\$this->([a-zA-Z0-9_]+)\s*=\s*new\s+([a-zA-Z0-9_\\\\]+)/', $code, $instantiations, PREG_SET_ORDER);
    $propToClass = [];
    foreach ($instantiations as $inst) {
        $propName = $inst[1];
        $className = $inst[2];
        if (!str_contains($className, '\\')) {
            // resolve with uses in file
            preg_match('/use\s+([^;]+' . preg_quote($className, '/') . ');/', $code, $useMatch);
            if ($useMatch) {
                $className = $useMatch[1];
            } else {
                $className = 'App\\Repositories\\' . $className;
            }
        }
        $propToClass[$propName] = $className;
    }

    // Now find calls like $this->orderRepo->someMethod(
    foreach ($propToClass as $propName => $className) {
        if (class_exists($className)) {
            preg_match_all('/\$this->' . preg_quote($propName, '/') . '->([a-zA-Z0-9_]+)\s*\(/', $code, $calls);
            foreach ($calls[1] as $calledMethod) {
                if (!method_exists($className, $calledMethod)) {
                    $errors[] = "In " . basename($cFile) . ": \$this->{$propName}->{$calledMethod}() does NOT exist on {$className}!";
                }
            }
        }
    }
}

if (empty($errors)) {
    echo "✓ All repository method calls in controllers exist!\n";
} else {
    echo "METHOD ERRORS FOUND:\n" . implode("\n", $errors) . "\n";
}
