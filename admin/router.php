<?php
// Admin router - support subfolder (localhost/chikaflorist/admin)
require_once __DIR__ . '/../includes/config.php';

$basePath = parse_url(BASE_URL, PHP_URL_PATH);
$uriPath  = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
if ($basePath && strpos($uriPath, $basePath) === 0) {
    $uriPath = substr($uriPath, strlen($basePath));
}
$uriPath = strtolower(trim($uriPath, '/'));
// uriPath sekarang = "admin" atau "admin/dashboard" dll
$page = preg_replace('#^admin/?#', '', $uriPath);
$page = $page ?: 'index';
$page = makeSlug($page);

// Map ke file
$allowed = ['dashboard','produk','kategori','kota','area','layanan','testimoni','galeri','faq','pengaturan'];

if ($page === '' || $page === 'index') {
    require __DIR__ . '/index.php'; exit();
}

if (in_array($page, $allowed)) {
    $file = __DIR__ . '/pages/' . $page . '.php';
    if (file_exists($file)) { require $file; exit(); }
}

// fallback
require __DIR__ . '/pages/dashboard.php';
