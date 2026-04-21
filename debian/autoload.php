<?php
/**
 * Debian autoloader for php-mpdf
 *
 * Registers all namespaces bundled with mPDF via PSR-4 without loading
 * vendor/autoload.php, so Composer\InstalledVersions is not overwritten
 * with mpdf-only package data.
 */

// Load system PSR autoloaders provided by php-psr-log and php-psr-http-message
require_once '/usr/share/php/Psr/Log/autoload.php';
require_once '/usr/share/php/Psr/Http/Message/autoload.php';

// Load fpdi autoloader provided by php-setasign-fpdi
require_once '/usr/share/php/setasign/Fpdi/autoload.php';

$baseDir = '/usr/share/php/mpdf';

$psr4 = [
    'Mpdf\\'                  => $baseDir . '/src',
    'Mpdf\\PsrLogAwareTrait\\' => $baseDir . '/vendor/mpdf/psr-log-aware-trait/src',
    'Mpdf\\PsrHttpMessageShim\\' => $baseDir . '/vendor/mpdf/psr-http-message-shim/src',
    // setasign\Fpdi\ is provided by php-setasign-fpdi via its own autoloader above
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

require_once '/usr/share/php/Composer/InstalledVersions.php';

(function (): void {
    $versions = [];
    foreach (\Composer\InstalledVersions::getAllRawData() as $d) {
        $versions = array_merge($versions, $d['versions'] ?? []);
    }
    $name    = defined('APP_NAME')    ? APP_NAME    : 'unknown';
    $version = '0.0.0';
    $versions[$name] = ['pretty_version' => $version, 'version' => $version,
        'reference' => null, 'type' => 'library', 'install_path' => __DIR__,
        'aliases' => [], 'dev_requirement' => false];
    \Composer\InstalledVersions::reload([
        'root' => ['name' => $name, 'pretty_version' => $version, 'version' => $version,
            'reference' => null, 'type' => 'project', 'install_path' => __DIR__,
            'aliases' => [], 'dev' => false],
        'versions' => $versions,
    ]);
})();
