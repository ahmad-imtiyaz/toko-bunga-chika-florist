<?php
require_once __DIR__ . '/../includes/config.php';

// SEO Meta
$page_title    = getSetting('meta_title_home');
$meta_desc     = getSetting('meta_desc_home');
$canonical_url = BASE_URL . '/';

// Data
$pdo          = getDB();
$featured     = $pdo->query("SELECT p.*,c.name as cat_name,c.slug as cat_slug FROM products p JOIN categories c ON p.category_id=c.id WHERE p.is_featured=1 AND p.is_active=1 ORDER BY p.sort_order ASC LIMIT 8")->fetchAll();
$testimonials = $pdo->query("SELECT * FROM testimonials WHERE is_active=1 ORDER BY sort_order ASC LIMIT 6")->fetchAll();
$faqs         = $pdo->query("SELECT * FROM faqs WHERE is_active=1 ORDER BY sort_order ASC LIMIT 6")->fetchAll();
$cities       = getActiveCities(20);
$categories   = getMainCategories();

require_once __DIR__ . '/../includes/header.php';

require __DIR__ . '/sections/hero.php';
require __DIR__ . '/sections/kategori.php';
require __DIR__ . '/sections/produk-unggulan.php';
require __DIR__ . '/sections/keunggulan.php';
require __DIR__ . '/sections/area.php';
require __DIR__ . '/sections/layanan.php';
require __DIR__ . '/sections/cara-pesan.php';
require __DIR__ . '/sections/testimoni.php';
require __DIR__ . '/sections/faq.php';
require __DIR__ . '/sections/cta.php';

require_once __DIR__ . '/../includes/footer.php';