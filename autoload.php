<?php
/**
 * Debian autoloader for php-mpdf
 *
 * Registers all namespaces bundled with mPDF via PSR-4 without loading
 * vendor/autoload.php, so Composer\InstalledVersions is not overwritten
 * with mpdf-only package data.
 */

$baseDir = '/usr/share/php/mpdf';

$psr4 = [
    'Mpdf\\'                  => $baseDir . '/src',
    'Mpdf\\PsrLogAwareTrait\\' => $baseDir . '/vendor/mpdf/psr-log-aware-trait/src',
    'Mpdf\\PsrHttpMessageShim\\' => $baseDir . '/vendor/mpdf/psr-http-message-shim/src',
    'setasign\\Fpdi\\'        => $baseDir . '/vendor/setasign/fpdi/src',
    'DeepCopy\\'              => $baseDir . '/vendor/myclabs/deep-copy/src/DeepCopy',
    // Psr\Log and Psr\Http\Message are provided by php-psr-log / php-psr-http-message
];

spl_autoload_register(function (string $class) use ($psr4): void {
    foreach ($psr4 as $prefix => $dir) {
        $len = strlen($prefix);
        if (strncmp($prefix, $class, $len) !== 0) {
            continue;
        }
        $file = $dir . '/' . str_replace('\\', '/', substr($class, $len)) . '.php';
        if (file_exists($file)) {
            require $file;
            return;
        }
    }
});
