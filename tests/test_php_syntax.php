<?php
declare(strict_types=1);

$dirs = [
    __DIR__ . '/../app',
    __DIR__ . '/../config',
    __DIR__ . '/../routes',
    __DIR__ . '/../public',
    __DIR__ . '/../resources',
];

$errors = [];
$total = 0;

foreach ($dirs as $dir) {
    if (!is_dir($dir)) continue;
    $it = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir));
    foreach ($it as $file) {
        if ($file->isFile() && $file->getExtension() === 'php') {
            $total++;
            $cmd = 'php -l ' . escapeshellarg($file->getPathname()) . ' 2>&1';
            exec($cmd, $out, $ret);
            if ($ret !== 0) {
                $errors[$file->getPathname()] = implode("\n", $out);
            }
            $out = [];
        }
    }
}

echo "Total PHP files checked: $total\n";
if (empty($errors)) {
    echo "SUCCESS: No syntax errors found in any PHP file!\n";
} else {
    echo "ERRORS FOUND: " . count($errors) . "\n";
    foreach ($errors as $path => $err) {
        echo "File: $path\nError:\n$err\n" . str_repeat('-', 40) . "\n";
    }
}
