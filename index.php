<?php
require_once __DIR__ . '/includes/config.php';

// Deteksi subfolder otomatis
$basePath = parse_url(BASE_URL, PHP_URL_PATH);
$uriPath  = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
if ($basePath && strpos($uriPath, $basePath) === 0) {
    $uriPath = substr($uriPath, strlen($basePath));
}
$request = strtolower(trim($uriPath, '/'));

// Redirect jika ada .php di URL
if (substr($request, -4) === '.php') {
    header('Location: ' . rtrim(BASE_URL, '/') . '/' . substr($request, 0, -4));
    exit();
}

$pdo = getDB();

// ── Homepage ──────────────────────────────────────────────
if (empty($request) || $request === 'index') {
    require __DIR__ . '/pages/home.php'; exit();
}

// ── Admin ─────────────────────────────────────────────────
if (strpos($request, 'admin') === 0) {
    http_response_code(404); require __DIR__ . '/pages/404.php'; exit();
}

// ── Area Layanan ──────────────────────────────────────────
if ($request === 'area-layanan') {
    require __DIR__ . '/pages/location.php'; exit();
}

// ── Galeri ───────────────────────────────────────────────
if ($request === 'galeri') {
    require __DIR__ . '/pages/gallery.php'; exit();
}

// ── Produk ───────────────────────────────────────────────
if ($request === 'produk') {
    require __DIR__ . '/pages/products.php'; exit();
}

// ── Detail Produk: /produk/[slug] ─────────────────────────
if (strpos($request, 'produk/') === 0) {
    $prodSlug = substr($request, 7);
    $chk = $pdo->prepare("SELECT id FROM products WHERE slug=? AND is_active=1");
    $chk->execute([$prodSlug]);
    if ($chk->fetch()) {
        $_GET['slug'] = $prodSlug;
        require __DIR__ . '/pages/product.php'; exit();
    }
}

// ── Blog list: /blog ──────────────────────────────────────
if ($request === 'blog') {
    require __DIR__ . '/pages/blog.php'; exit();
}

// ── Blog detail: /blog/[slug] ─────────────────────────────
if (preg_match('#^blog/([a-z0-9-]+)$#', $request, $m)) {
    $stmt = $pdo->prepare("
        SELECT b.*, bc.name AS cat_name, bc.slug AS cat_slug
        FROM blogs b
        LEFT JOIN blog_categories bc ON b.blog_category_id = bc.id
        WHERE b.slug = ? AND b.status = 'active'
        LIMIT 1
    ");
    $stmt->execute([$m[1]]);
    $blog = $stmt->fetch();
    if ($blog) {
        require __DIR__ . '/pages/blog-detail.php';
    } else {
        http_response_code(404);
        require __DIR__ . '/pages/404.php';
    }
    exit();
}

// ── Halaman Layanan Statis ────────────────────────────────
$svc = $pdo->prepare("SELECT id FROM service_pages WHERE slug=? AND is_active=1");
$svc->execute([$request]);
if ($svc->fetch()) {
    $_GET['slug'] = $request;
    require __DIR__ . '/pages/service.php'; exit();
}

// ── Kategori Produk ───────────────────────────────────────
$cat = $pdo->prepare("SELECT id FROM categories WHERE slug=? AND is_active=1");
$cat->execute([$request]);
if ($cat->fetch()) {
    $_GET['slug'] = $request;
    require __DIR__ . '/pages/category.php'; exit();
}

// ── Toko Bunga [kota / area] ──────────────────────────────
if (strpos($request, 'toko-bunga-') === 0) {
    $loc = substr($request, strlen('toko-bunga-'));

    $chkCity = $pdo->prepare("SELECT id FROM cities WHERE slug=? AND is_active=1");
    $chkCity->execute([$loc]);
    if ($chkCity->fetch()) {
        $_GET['slug'] = $loc;
        require __DIR__ . '/pages/city.php'; exit();
    }

    $chkArea = $pdo->prepare("SELECT id FROM areas WHERE slug=? AND is_active=1");
    $chkArea->execute([$loc]);
    if ($chkArea->fetch()) {
        $_GET['slug'] = $loc;
        require __DIR__ . '/pages/area.php'; exit();
    }
}

// ── 404 ───────────────────────────────────────────────────
http_response_code(404);
require __DIR__ . '/pages/404.php';