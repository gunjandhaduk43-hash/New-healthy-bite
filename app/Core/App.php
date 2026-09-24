<?php
declare(strict_types=1);

namespace App\Core;

class App
{
    private Router $router;

    public function __construct()
    {
        $this->router = new Router();
        $this->registerErrorHandling();
        Session::start();
    }

    public function getRouter(): Router
    {
        return $this->router;
    }

    public function run(): void
    {
        $this->router->dispatch();
    }

    private function registerErrorHandling(): void
    {
        error_reporting(E_ALL);

        set_error_handler(function (int $level, string $message, string $file, int $line) {
            if (!(error_reporting() & $level)) {
                return false;
            }
            throw new \ErrorException($message, 0, $level, $file, $line);
        });

        set_exception_handler(function (\Throwable $e) {
            $logMessage = sprintf(
                "[%s] Exception: %s in %s:%d\nStack Trace:\n%s\n",
                date('Y-m-d H:i:s'),
                $e->getMessage(),
                $e->getFile(),
                $e->getLine(),
                $e->getTraceAsString()
            );

            $logDir = dirname(__DIR__, 2) . '/storage/logs';
            if (is_dir($logDir) && is_writable($logDir)) {
                file_put_contents($logDir . '/app.log', $logMessage, FILE_APPEND);
            }

            $isDebug = (($_ENV['APP_ENV'] ?? 'development') === 'development');

            if (Request::isJson() || str_starts_with(Request::getPath(), '/api/')) {
                Response::json([
                    'error'   => 'Internal Server Error',
                    'message' => $isDebug ? $e->getMessage() : 'An unexpected error occurred.',
                ], 500);
            } else {
                Response::setStatusCode(500);
                $controller = new Controller();
                echo $controller->render('customer/errors/500', [
                    'exception' => $isDebug ? $e : null
                ], 'minimal');
            }
            exit;
        });
    }
}
