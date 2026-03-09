<?php
require_once __DIR__ . '/config.php';

$site_name      = getSetting('site_name', 'Chika Florist');
$page_title     = $page_title ?? getSetting('meta_title_home');
$meta_desc      = $meta_desc  ?? getSetting('meta_desc_home');
$canonical      = $canonical_url ?? currentUrl();
$logo           = getSetting('logo', 'logo.jpeg');
$mainCategories = getMainCategories();
$navCities      = getActiveCities(8);
$base           = BASE_URL;
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= clean($page_title) ?></title>
<meta name="description" content="<?= clean($meta_desc) ?>">
<link rel="canonical" href="<?= clean($canonical) ?>">
<meta property="og:title" content="<?= clean($page_title) ?>">
<meta property="og:description" content="<?= clean($meta_desc) ?>">
<meta property="og:url" content="<?= clean($canonical) ?>">
<meta property="og:type" content="website">
<meta property="og:site_name" content="<?= clean($site_name) ?>">
<meta name="twitter:card" content="summary_large_image">
<script src="https://cdn.tailwindcss.com"></script>
<script>
tailwind.config = {
    theme: { extend: {
        colors: {
            warm: { 50:'#fdf8f0', 100:'#faf0dc', 200:'#f5e0b5' }
        },
        fontFamily: {
            display: ['"Playfair Display"', 'Georgia', 'serif'],
            body:    ['"Lato"', 'sans-serif'],
        }
    }}
}
</script>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&family=Lato:wght@300;400;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="<?= ASSETS_URL ?>css/main.css">
<script type="application/ld+json">
{"@context":"https://schema.org","@type":"Florist","name":"<?= clean($site_name) ?>","url":"<?= $base ?>","telephone":"+<?= getSetting('whatsapp_number') ?>","description":"<?= clean(getSetting('site_tagline')) ?>","areaServed":"Indonesia","openingHours":"Mo-Su 00:00-24:00"}
</script>
</head>
<body class="font-body bg-warm-50 text-gray-800">

<!-- NAVBAR -->
<header class="bg-white shadow-sm sticky top-0 z-50 border-b border-rose-100">
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
<div class="flex items-center justify-between h-16">

  <!-- Logo -->
  <a href="<?= $base ?>/" class="flex items-center gap-2 shrink-0">
    <img src="<?= UPLOAD_URL . $logo ?>" alt="<?= clean($site_name) ?>" class="h-10 w-auto object-contain"
         onerror="this.style.display='none';this.nextElementSibling.style.display='block'">
    <span class="font-display text-xl font-bold text-rose-700" style="display:none"><?= clean($site_name) ?></span>
  </a>

  <!-- Nav Desktop -->
  <nav class="hidden lg:flex items-center gap-6 text-sm">
    <a href="<?= $base ?>/" class="text-gray-700 hover:text-rose-600 transition-colors">Beranda</a>

    <!-- Dropdown Produk -->
    <div class="relative group">
      <button class="flex items-center gap-1 text-gray-700 hover:text-rose-600 transition-colors">
        Produk
        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
        </svg>
      </button>
      <div class="absolute top-full left-0 mt-1 w-52 bg-white rounded-xl shadow-lg border border-rose-50 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 z-50">
        <?php foreach ($mainCategories as $cat): ?>
        <a href="<?= $base ?>/<?= $cat['slug'] ?>"
           class="block px-4 py-2.5 text-sm text-gray-700 hover:bg-rose-50 hover:text-rose-600 first:rounded-t-xl last:rounded-b-xl transition-colors">
          <?= clean($cat['name']) ?>
        </a>
        <?php endforeach; ?>
      </div>
    </div>

    <!-- Dropdown Area Layanan -->
    <div class="relative group">
      <button class="flex items-center gap-1 text-gray-700 hover:text-rose-600 transition-colors">
        Area Layanan
        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
        </svg>
      </button>
      <div class="absolute top-full left-0 mt-1 w-52 bg-white rounded-xl shadow-lg border border-rose-50 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 z-50">
        <?php foreach ($navCities as $city): ?>
        <a href="<?= $base ?>/toko-bunga-<?= $city['slug'] ?>"
           class="block px-4 py-2.5 text-sm text-gray-700 hover:bg-rose-50 hover:text-rose-600 transition-colors">
          <?= clean($city['name']) ?>
        </a>
        <?php endforeach; ?>
        <a href="<?= $base ?>/area-layanan"
           class="block px-4 py-2.5 text-sm text-rose-600 font-semibold hover:bg-rose-50 rounded-b-xl border-t border-rose-50 transition-colors">
          Lihat Semua Kota →
        </a>
      </div>
    </div>

    <a href="<?= $base ?>/toko-bunga-online-24-jam-indonesia" class="text-gray-700 hover:text-rose-600 transition-colors">Layanan 24 Jam</a>
    <a href="<?= $base ?>/galeri" class="text-gray-700 hover:text-rose-600 transition-colors">Galeri</a>
  </nav>

  <!-- CTA + Hamburger -->
  <div class="flex items-center gap-3">
    <a href="<?= waLink() ?>" target="_blank" rel="noopener"
       class="hidden sm:flex items-center gap-2 bg-green-500 hover:bg-green-600 text-white text-sm font-semibold px-4 py-2 rounded-full transition-colors">
      <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
        <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z"/>
        <path d="M12 0C5.373 0 0 5.373 0 12c0 2.096.541 4.063 1.491 5.776L.057 23.04l5.43-1.424A11.947 11.947 0 0012 24c6.627 0 12-5.373 12-12S18.627 0 12 0zm0 21.818a9.818 9.818 0 01-5.015-1.371l-.36-.214-3.726.977.995-3.633-.235-.374A9.818 9.818 0 1112 21.818z"/>
      </svg>
      Pesan Sekarang
    </a>
    <button id="menuToggle" class="lg:hidden p-2 rounded-lg hover:bg-rose-50 transition-colors">
      <svg class="w-5 h-5 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
      </svg>
    </button>
  </div>

</div>
</div>

<!-- Mobile Menu -->
<div id="mobileMenu" class="hidden lg:hidden bg-white border-t border-rose-100 px-4 py-4 space-y-1">
  <a href="<?= $base ?>/" class="block py-2 text-gray-700 hover:text-rose-600 font-medium">Beranda</a>
  <p class="text-xs text-gray-400 uppercase tracking-wider mt-3 mb-1">Produk</p>
  <?php foreach ($mainCategories as $cat): ?>
  <a href="<?= $base ?>/<?= $cat['slug'] ?>" class="block py-1.5 pl-3 text-gray-600 hover:text-rose-600"><?= clean($cat['name']) ?></a>
  <?php endforeach; ?>
  <p class="text-xs text-gray-400 uppercase tracking-wider mt-3 mb-1">Area Layanan</p>
  <?php foreach ($navCities as $city): ?>
  <a href="<?= $base ?>/toko-bunga-<?= $city['slug'] ?>" class="block py-1.5 pl-3 text-gray-600 hover:text-rose-600"><?= clean($city['name']) ?></a>
  <?php endforeach; ?>
  <a href="<?= $base ?>/toko-bunga-online-24-jam-indonesia" class="block py-2 text-gray-700 hover:text-rose-600 font-medium">Layanan 24 Jam</a>
  <a href="<?= $base ?>/galeri" class="block py-2 text-gray-700 hover:text-rose-600 font-medium">Galeri</a>
  <a href="<?= waLink() ?>" target="_blank"
     class="flex items-center justify-center gap-2 mt-3 bg-green-500 text-white py-2.5 rounded-full font-semibold text-sm">
    Pesan via WhatsApp
  </a>
</div>
</header>

<!-- Breadcrumb -->
<?php if (!empty($breadcrumbs)): ?>
<nav class="bg-warm-100 border-b border-amber-100 py-2 px-4 text-sm" aria-label="Breadcrumb">
<div class="max-w-7xl mx-auto flex items-center gap-1 text-gray-500 flex-wrap">
  <?php foreach ($breadcrumbs as $i => $crumb): ?>
    <?php if ($i > 0): ?><span class="text-gray-300">/</span><?php endif; ?>
    <?php if (isset($crumb['url']) && $i < count($breadcrumbs) - 1): ?>
      <a href="<?= $base . $crumb['url'] ?>" class="hover:text-rose-600 transition-colors"><?= clean($crumb['label']) ?></a>
    <?php else: ?>
      <span class="text-gray-700 font-medium"><?= clean($crumb['label']) ?></span>
    <?php endif; ?>
  <?php endforeach; ?>
</div>
</nav>
<?php endif; ?>

<script>
document.getElementById('menuToggle').addEventListener('click', function() {
  document.getElementById('mobileMenu').classList.toggle('hidden');
});
</script>