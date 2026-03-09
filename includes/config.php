<?php
// ============================================================
// CHIKA FLORIST - KONFIGURASI UTAMA
// ============================================================
define('DB_HOST', 'localhost');
define('DB_NAME', 'chikaflorist');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');

// BASE_URL otomatis - support localhost/chikaflorist/ maupun domain langsung
define('BASE_URL',   'http://localhost/chikaflorist');
define('BASE_PATH',  dirname(__DIR__));
define('UPLOAD_DIR', BASE_PATH  . '/uploads/');
define('UPLOAD_URL', BASE_URL   . '/uploads/');
define('ASSETS_URL', BASE_URL   . '/assets/');

if (session_status() === PHP_SESSION_NONE) session_start();
date_default_timezone_set('Asia/Jakarta');
error_reporting(0);
ini_set('display_errors', 0);

function getDB() {
    static $pdo = null;
    if ($pdo === null) {
        try {
            $dsn = "mysql:host=".DB_HOST.";dbname=".DB_NAME.";charset=".DB_CHARSET;
            $pdo = new PDO($dsn, DB_USER, DB_PASS, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ]);
        } catch (PDOException $e) {
            die('Koneksi database gagal.');
        }
    }
    return $pdo;
}

function getSetting($key, $default = '') {
    static $settings = null;
    if ($settings === null) {
        try {
            $settings = getDB()->query("SELECT setting_key, setting_value FROM settings")->fetchAll(PDO::FETCH_KEY_PAIR);
        } catch (Exception $e) { return $default; }
    }
    return $settings[$key] ?? $default;
}

function makeSlug($string) {
    $string = mb_strtolower($string, 'UTF-8');
    $from = ['à','á','â','ã','ä','å','æ','ç','è','é','ê','ë','ì','í','î','ï','ð','ñ','ò','ó','ô','õ','ö','ø','ù','ú','û','ü','ý','þ','ÿ'];
    $to   = ['a','a','a','a','a','a','ae','c','e','e','e','e','i','i','i','i','d','n','o','o','o','o','o','o','u','u','u','u','y','b','y'];
    $string = str_replace($from, $to, $string);
    $string = preg_replace('/[^a-z0-9\s-]/', '', $string);
    $string = preg_replace('/[\s-]+/', '-', $string);
    return trim($string, '-');
}

function formatHarga($min, $max = 0) {
    if ($max > 0 && $max != $min)
        return 'Rp ' . number_format($min,0,',','.') . ' – Rp ' . number_format($max,0,',','.');
    return 'Rp ' . number_format($min,0,',','.');
}

function clean($input) {
    return htmlspecialchars(strip_tags(trim((string)$input)), ENT_QUOTES, 'UTF-8');
}

function redirect($url) {
    header("Location: $url"); exit();
}

function waLink($message = '') {
    $number = getSetting('whatsapp_number', '628131241986');
    $text = $message ?: getSetting('whatsapp_text', 'Halo Chika Florist, saya ingin memesan bunga');
    return 'https://wa.me/' . $number . '?text=' . urlencode($text);
}

function currentUrl() {
    $p = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    return $p . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
}

function getActiveCities($limit = 0) {
    $sql = "SELECT id, name, slug FROM cities WHERE is_active=1 ORDER BY tier ASC, sort_order ASC, name ASC";
    if ($limit > 0) $sql .= " LIMIT $limit";
    return getDB()->query($sql)->fetchAll();
}

function getAreasByCity($city_id) {
    $stmt = getDB()->prepare("SELECT id, name, slug FROM areas WHERE city_id=? AND is_active=1 ORDER BY sort_order ASC, name ASC");
    $stmt->execute([$city_id]);
    return $stmt->fetchAll();
}

function getMainCategories() {
    return getDB()->query("SELECT * FROM categories WHERE parent_id IS NULL AND is_active=1 ORDER BY sort_order ASC")->fetchAll();
}

function getSubCategories($parent_id) {
    $stmt = getDB()->prepare("SELECT * FROM categories WHERE parent_id=? AND is_active=1 ORDER BY sort_order ASC");
    $stmt->execute([$parent_id]);
    return $stmt->fetchAll();
}

function isAdminLoggedIn() {
    return isset($_SESSION['admin_id']) && !empty($_SESSION['admin_id']);
}

function requireAdminLogin() {
    if (!isAdminLoggedIn()) redirect(BASE_URL . '/admin/');
}
