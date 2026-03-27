<?php
require_once __DIR__ . '/config.php';

$site_name      = getSetting('site_name', 'Chika Florist');
$site_tagline   = getSetting('site_tagline', '');
$page_title     = $page_title ?? getSetting('meta_title_home');
$meta_desc      = $meta_desc  ?? getSetting('meta_desc_home');
$canonical      = $canonical_url ?? currentUrl();
$logo           = getSetting('logo', 'logo.jpeg');
$base           = BASE_URL;

// ── Deteksi slug aktif ───────────────────────────────────
$uriPath     = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$basePath    = parse_url(BASE_URL, PHP_URL_PATH);
if ($basePath && strpos($uriPath, $basePath) === 0) {
    $uriPath = substr($uriPath, strlen($basePath));
}
$current_slug = strtolower(trim($uriPath, '/'));

// ── Data Navbar ──────────────────────────────────────────
$navParentCats  = getMainCategories();
$navCatChildren = [];
foreach ($navParentCats as $parent) {
    $navCatChildren[$parent['id']] = getSubCategories($parent['id']);
}

$allCities = getDB()->query(
    "SELECT id, name, slug, province, tier
     FROM cities
     WHERE is_active=1
     ORDER BY province ASC, tier ASC, sort_order ASC, name ASC"
)->fetchAll();

$navAreas = [];
foreach ($allCities as $city) {
    $navAreas[$city['id']] = getAreasByCity($city['id']);
}

$navProvinces = [];
foreach ($allCities as $city) {
    $prov = trim((string)($city['province'] ?? ''));
    if ($prov === '') $prov = 'Lainnya';
    $navProvinces[$prov][] = $city;
}
ksort($navProvinces);
$navProvincesDisplay = $navProvinces;
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
<link rel="icon" type="image/jpeg" href="<?= UPLOAD_URL . $logo ?>">
<script src="https://cdn.tailwindcss.com"></script>
<script>
tailwind.config = {
  theme: { extend: {
    colors: { warm: { 50:'#fdf8f0', 100:'#faf0dc', 200:'#f5e0b5' } },
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

<style>
/* ════════════════════════════════════════════════
   DESKTOP FLYOUT
   ════════════════════════════════════════════════ */
.nav-dropdown { position: relative; }

.nav-panel {
  position: absolute;
  top: calc(100% + 8px);
  left: 0;
  min-width: 210px;
  background: #fff;
  border: 1px solid #fce7f3;
  border-radius: 14px;
  box-shadow: 0 8px 32px rgba(190,24,93,0.08), 0 2px 8px rgba(0,0,0,0.06);
  padding: 6px 0;
  opacity: 0;
  visibility: hidden;
  transform: translateY(6px);
  transition: opacity .2s ease, transform .2s ease, visibility .2s;
  z-index: 200;
  pointer-events: none;
}
.nav-dropdown:hover .nav-panel,
.nav-dropdown:focus-within .nav-panel {
  opacity: 1; visibility: visible;
  transform: translateY(0);
  pointer-events: auto;
}

.nav-panel-item {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 8px;
  padding: 10px 16px;
  font-size: 0.875rem;
  font-weight: 600;
  color: #374151;
  text-decoration: none;
  cursor: pointer;
  transition: background .15s, color .15s;
  white-space: nowrap;
  position: relative;
  background: none;
  border: none;
  width: 100%;
  text-align: left;
}
.nav-panel-item:hover { background: #fdf2f8; color: #be185d; }
.nav-panel-item .chev-r {
  width: 13px; height: 13px;
  color: #d1d5db;
  flex-shrink: 0;
  transition: color .15s;
}
.nav-panel-item:hover .chev-r { color: #be185d; }

.nav-flyout {
  position: absolute;
  top: -6px;
  left: 100%;
  min-width: 190px;
  max-width: 230px;
  background: #fff;
  border: 1px solid #fce7f3;
  border-radius: 14px;
  box-shadow: 0 8px 32px rgba(190,24,93,0.08), 0 2px 8px rgba(0,0,0,0.06);
  padding: 6px 0;
  opacity: 0;
  visibility: hidden;
  transform: translateX(6px);
  transition: opacity .18s ease, transform .18s ease, visibility .18s;
  z-index: 201;
  pointer-events: none;
}
.nav-flyout::before {
  content: '';
  position: absolute;
  top: 0; left: -12px;
  width: 12px; height: 100%;
}
.has-flyout:hover > .nav-flyout,
.has-flyout:focus-within > .nav-flyout {
  opacity: 1; visibility: visible;
  transform: translateX(0);
  pointer-events: auto;
}
.nav-flyout .nav-flyout { z-index: 202; }

.nav-flyout.open-left {
  left: auto; right: 100%;
  transform: translateX(-6px);
}
.has-flyout:hover > .nav-flyout.open-left,
.has-flyout:focus-within > .nav-flyout.open-left {
  transform: translateX(0);
}

.nav-flyout-item {
  display: flex;
  align-items: center;
  gap: 8px;
  padding: 9px 16px;
  font-size: 0.825rem;
  color: #6b7280;
  text-decoration: none;
  transition: background .15s, color .15s;
  white-space: nowrap;
}
.nav-flyout-item::before {
  content: '';
  width: 5px; height: 5px;
  background: #f9a8d4;
  border-radius: 50%;
  flex-shrink: 0;
}
.nav-flyout-item:hover { background: #fdf2f8; color: #be185d; }

.nav-panel-divider { height: 1px; background: #fce7f3; margin: 4px 0; }

.see-all-link {
  display: flex;
  align-items: center;
  padding: 8px 16px;
  font-size: 0.8rem;
  font-weight: 700;
  color: #be185d;
  text-decoration: none;
  transition: background .15s;
  border-top: 1px dashed #fce7f3;
  margin-top: 4px;
}
.see-all-link:hover { background: #fdf2f8; }

.nav-trigger {
  display: flex;
  align-items: center;
  gap: 4px;
  font-size: 0.875rem;
  font-weight: 600;
  color: #374151;
  background: none;
  border: none;
  cursor: pointer;
  padding: 0;
  transition: color .15s;
  text-decoration: none;
}
.nav-trigger:hover, .nav-dropdown:hover .nav-trigger { color: #be185d; }
/* Active state untuk nav link */
.nav-trigger.active { color: #be185d; }
.nav-trigger svg { width: 12px; height: 12px; transition: transform .2s ease; }
.nav-dropdown:hover .nav-trigger svg { transform: rotate(180deg); }

/* ════════════════════════════════════════════════
   MOBILE DRAWER
   ════════════════════════════════════════════════ */
#mobileDrawer {
  position: fixed; inset: 0;
  z-index: 9999; visibility: hidden;
}
#mobileDrawer.open { visibility: visible; }
#drawerBackdrop {
  position: absolute; inset: 0;
  background: rgba(0,0,0,0);
  transition: background .3s ease;
}
#mobileDrawer.open #drawerBackdrop { background: rgba(0,0,0,0.45); }
#drawerPanel {
  position: absolute;
  top: 0; left: 0; bottom: 0;
  width: 310px; max-width: 88vw;
  background: #fff;
  transform: translateX(-100%);
  transition: transform .32s cubic-bezier(.4,0,.2,1);
  display: flex; flex-direction: column;
  overflow: hidden;
  box-shadow: 4px 0 28px rgba(0,0,0,0.13);
}
#mobileDrawer.open #drawerPanel { transform: translateX(0); }

.drawer-header {
  display: flex; align-items: center; justify-content: space-between;
  padding: 1rem 1.2rem;
  border-bottom: 1px solid #fce7f3;
  flex-shrink: 0;
  background: linear-gradient(135deg,#fff8f8 0%,#fdf6ee 100%);
}
.drawer-body {
  flex: 1; overflow-y: auto;
  -webkit-overflow-scrolling: touch;
  padding-bottom: 1.5rem;
}

.dacc-l1-btn {
  display: flex; align-items: center; justify-content: space-between;
  width: 100%; padding: 0.78rem 1.25rem;
  font-size: 0.925rem; font-weight: 700;
  color: #374151;
  background: none; border: none;
  cursor: pointer; text-align: left;
  transition: background .15s, color .15s;
  text-decoration: none;
}
.dacc-l1-btn:hover { background: #fdf2f8; color: #be185d; }
.dacc-l1-btn.is-open { color: #be185d; }
/* Active state untuk mobile link */
.dacc-l1-btn.active { color: #be185d; font-weight: 700; }
.dacc-l1-btn .dacc-icon { display: flex; align-items: center; gap: 8px; }
.dacc-l1-btn .dacc-chevron {
  width: 15px; height: 15px; color: #9ca3af;
  flex-shrink: 0; transition: transform .25s ease, color .15s;
}
.dacc-l1-btn.is-open .dacc-chevron { transform: rotate(180deg); color: #be185d; }

.dacc-l1-body {
  max-height: 0; overflow: hidden;
  transition: max-height .35s cubic-bezier(.4,0,.2,1);
  background: #fdfaff;
}
.dacc-l1-body.is-open { max-height: 3000px; }

.dacc-l1-link {
  display: flex; align-items: center; gap: 8px;
  padding: 0.65rem 1.25rem 0.65rem 2.25rem;
  font-size: 0.875rem; color: #6b7280;
  text-decoration: none;
  transition: background .15s, color .15s;
}
.dacc-l1-link::before {
  content: ''; width: 5px; height: 5px;
  background: #f9a8d4; border-radius: 50%; flex-shrink: 0;
}
.dacc-l1-link:hover { background: #fce7f3; color: #be185d; }

.dacc-l2-btn {
  display: flex; align-items: center; justify-content: space-between;
  width: 100%;
  padding: 0.65rem 1rem 0.65rem 1.75rem;
  font-size: 0.875rem; font-weight: 700;
  color: #4b5563;
  background: none; border: none;
  cursor: pointer; text-align: left;
  transition: background .15s, color .15s;
  border-left: 3px solid #fce7f3;
  margin-left: 1.25rem;
}
.dacc-l2-btn:hover { background: #fce7f3; color: #be185d; }
.dacc-l2-btn.is-open { color: #be185d; border-left-color: #be185d; }
.dacc-l2-btn .dacc-chevron {
  width: 13px; height: 13px; color: #d1d5db;
  flex-shrink: 0; transition: transform .2s ease, color .15s;
}
.dacc-l2-btn.is-open .dacc-chevron { transform: rotate(180deg); color: #be185d; }

.dacc-l2-body {
  max-height: 0; overflow: hidden;
  transition: max-height .3s cubic-bezier(.4,0,.2,1);
  background: #fff8fc;
  margin-left: 1.25rem;
  border-left: 3px solid #fce7f3;
}
.dacc-l2-body.is-open { max-height: 2000px; }

.dacc-l3-btn {
  display: flex; align-items: center; justify-content: space-between;
  width: 100%;
  padding: 0.55rem 1rem 0.55rem 1.5rem;
  font-size: 0.85rem; font-weight: 600;
  color: #6b7280;
  background: none; border: none;
  cursor: pointer; text-align: left;
  transition: background .15s, color .15s;
  border-left: 2px solid #fce7f3;
  margin-left: 1rem;
}
.dacc-l3-btn:hover { background: #fce7f3; color: #be185d; }
.dacc-l3-btn.is-open { color: #be185d; border-left-color: #f9a8d4; }
.dacc-l3-btn .dacc-chevron {
  width: 12px; height: 12px; color: #e5e7eb;
  flex-shrink: 0; transition: transform .18s ease, color .15s;
}
.dacc-l3-btn.is-open .dacc-chevron { transform: rotate(180deg); color: #be185d; }

.dacc-l3-body {
  max-height: 0; overflow: hidden;
  transition: max-height .28s cubic-bezier(.4,0,.2,1);
  background: #fff5f9;
  margin-left: 1rem;
  border-left: 2px solid #fce7f3;
}
.dacc-l3-body.is-open { max-height: 1000px; }

.dacc-l3-link {
  display: flex; align-items: center; gap: 6px;
  padding: 0.45rem 0.75rem 0.45rem 1.25rem;
  font-size: 0.8rem; color: #9ca3af;
  text-decoration: none;
  transition: background .15s, color .15s;
}
.dacc-l3-link::before {
  content: ''; width: 4px; height: 4px;
  background: #fca5a5; border-radius: 50%; flex-shrink: 0;
}
.dacc-l3-link:hover { background: #fce7f3; color: #be185d; }

.drawer-divider { height: 1px; background: #f3f4f6; margin: 0.35rem 1.25rem; }
.drawer-section-label {
  padding: 0.75rem 1.25rem 0.25rem;
  font-size: 0.7rem; font-weight: 700;
  letter-spacing: 0.08em; color: #d1d5db;
  text-transform: uppercase;
}
.drawer-footer {
  padding: 1rem 1.25rem;
  border-top: 1px solid #fce7f3;
  flex-shrink: 0;
}

.ham-line {
  display: block; width: 20px; height: 2px;
  background: #374151; border-radius: 2px;
  transition: transform .25s ease, opacity .2s ease;
  transform-origin: center;
}
#menuToggle.is-open .ham-line:nth-child(1) { transform: translateY(6px) rotate(45deg); }
#menuToggle.is-open .ham-line:nth-child(2) { opacity: 0; }
#menuToggle.is-open .ham-line:nth-child(3) { transform: translateY(-6px) rotate(-45deg); }
</style>

<script type="application/ld+json">
{"@context":"https://schema.org","@type":"Florist","name":"<?= clean($site_name) ?>","url":"<?= $base ?>","telephone":"+<?= getSetting('whatsapp_number') ?>","description":"<?= clean(getSetting('site_tagline')) ?>","areaServed":"Indonesia","openingHours":"Mo-Su 00:00-24:00"}
</script>
</head>
<body class="font-body bg-warm-50 text-gray-800">

<!-- ══════════════════════════════════════════
     NAVBAR
══════════════════════════════════════════ -->
<header class="bg-white shadow-sm sticky top-0 z-50 border-b border-rose-100">
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
<div class="flex items-center justify-between h-16">

  <!-- Logo -->
  <a href="<?= $base ?>/" class="flex items-center gap-3 shrink-0">
    <img src="<?= UPLOAD_URL . $logo ?>" alt="<?= clean($site_name) ?>"
         class="h-10 w-auto object-contain" onerror="this.style.display='none'">
    <div class="leading-tight">
      <p class="font-display font-bold text-rose-700 text-lg leading-none"><?= clean($site_name) ?></p>
      <?php if ($site_tagline): ?>
      <p class="text-xs text-gray-400 mt-0.5 leading-none hidden sm:block"><?= clean($site_tagline) ?></p>
      <?php endif; ?>
    </div>
  </a>

  <!-- Desktop Nav -->
  <nav class="hidden lg:flex items-center gap-7 text-sm" aria-label="Navigasi utama">

    <a href="<?= $base ?>/"
       class="nav-trigger <?= $current_slug === '' ? 'active' : '' ?>">Beranda</a>

    <!-- ── PRODUK ── -->
    <div class="nav-dropdown">
      <button class="nav-trigger <?= strpos($current_slug, 'produk') === 0 ? 'active' : '' ?>" aria-haspopup="true">
        Product
        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/>
        </svg>
      </button>
      <div class="nav-panel" role="menu">
        <?php foreach ($navParentCats as $parent):
          $children = $navCatChildren[$parent['id']] ?? [];
        ?>
          <?php if (!empty($children)): ?>
            <div class="has-flyout nav-panel-item" role="none" tabindex="0">
              <span><?= clean($parent['name']) ?></span>
              <svg class="chev-r" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/>
              </svg>
              <div class="nav-flyout" role="menu">
                <a href="<?= $base ?>/<?= $parent['slug'] ?>" class="nav-flyout-item" role="menuitem">
                  Semua <?= clean($parent['name']) ?>
                </a>
                <div class="nav-panel-divider"></div>
                <?php foreach ($children as $child): ?>
                <a href="<?= $base ?>/<?= $child['slug'] ?>" class="nav-flyout-item" role="menuitem">
                  <?= clean($child['name']) ?>
                </a>
                <?php endforeach; ?>
              </div>
            </div>
          <?php else: ?>
            <a href="<?= $base ?>/<?= $parent['slug'] ?>" class="nav-panel-item" role="menuitem">
              <?= clean($parent['name']) ?>
            </a>
          <?php endif; ?>
        <?php endforeach; ?>
      </div>
    </div>

    <!-- ── AREA LAYANAN ── -->
    <div class="nav-dropdown">
      <button class="nav-trigger <?= strpos($current_slug, 'toko-bunga-') === 0 || $current_slug === 'area-layanan' ? 'active' : '' ?>" aria-haspopup="true">
        Area Layanan
        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/>
        </svg>
      </button>
      <div class="nav-panel" role="menu">
        <?php foreach ($navProvincesDisplay as $provName => $provCities): ?>
          <div class="has-flyout nav-panel-item" role="none" tabindex="0">
            <span><?= clean($provName) ?></span>
            <svg class="chev-r" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/>
            </svg>
            <div class="nav-flyout" role="menu">
              <?php foreach ($provCities as $city):
                $areas = $navAreas[$city['id']] ?? [];
              ?>
                <?php if (!empty($areas)): ?>
                  <div class="has-flyout nav-panel-item" role="none" tabindex="0" style="font-weight:500;">
                    <span><?= clean($city['name']) ?></span>
                    <svg class="chev-r" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/>
                    </svg>
                    <div class="nav-flyout" role="menu">
                      <a href="<?= $base ?>/toko-bunga-<?= $city['slug'] ?>" class="nav-flyout-item" role="menuitem">
                        Semua Area <?= clean($city['name']) ?>
                      </a>
                      <div class="nav-panel-divider"></div>
                      <?php foreach ($areas as $area): ?>
                      <a href="<?= $base ?>/toko-bunga-<?= $area['slug'] ?>" class="nav-flyout-item" role="menuitem">
                        <?= clean($area['name']) ?>
                      </a>
                      <?php endforeach; ?>
                    </div>
                  </div>
                <?php else: ?>
                  <a href="<?= $base ?>/toko-bunga-<?= $city['slug'] ?>" class="nav-flyout-item" role="menuitem">
                    <?= clean($city['name']) ?>
                  </a>
                <?php endif; ?>
              <?php endforeach; ?>
            </div>
          </div>
        <?php endforeach; ?>
        <div class="nav-panel-divider"></div>
        <a href="<?= $base ?>/area-layanan" class="see-all-link">Lihat Semua Area →</a>
      </div>
    </div>

    <a href="<?= $base ?>/toko-bunga-online-24-jam-indonesia"
       class="nav-trigger <?= $current_slug === 'toko-bunga-online-24-jam-indonesia' ? 'active' : '' ?>">Layanan 24 Jam</a>

    <a href="<?= $base ?>/galeri"
       class="nav-trigger <?= $current_slug === 'galeri' ? 'active' : '' ?>">Galeri</a>

    <!-- ── BLOG ── -->
    <a href="<?= $base ?>/blog"
       class="nav-trigger <?= $current_slug === 'blog' || strpos($current_slug, 'blog/') === 0 ? 'active' : '' ?>">Blog</a>

  </nav>

  <!-- CTA + Hamburger -->
  <div class="flex items-center gap-3">
    <a href="<?= waLink() ?>" target="_blank" rel="noopener"
       class="hidden sm:flex items-center gap-2 bg-green-500 hover:bg-green-600 text-white text-sm font-semibold px-4 py-2 rounded-full transition-colors shadow-sm">
      <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
        <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z"/>
        <path d="M12 0C5.373 0 0 5.373 0 12c0 2.096.541 4.063 1.491 5.776L.057 23.04l5.43-1.424A11.947 11.947 0 0012 24c6.627 0 12-5.373 12-12S18.627 0 12 0zm0 21.818a9.818 9.818 0 01-5.015-1.371l-.36-.214-3.726.977.995-3.633-.235-.374A9.818 9.818 0 1112 21.818z"/>
      </svg>
      Pesan Sekarang
    </a>
    <button id="menuToggle" class="lg:hidden p-2 rounded-lg hover:bg-rose-50 transition-colors" aria-label="Buka menu">
      <span class="flex flex-col gap-1.5">
        <span class="ham-line"></span>
        <span class="ham-line"></span>
        <span class="ham-line"></span>
      </span>
    </button>
  </div>

</div>
</div>
</header>

<!-- ══════════════════════════════════════════
     MOBILE DRAWER
══════════════════════════════════════════ -->
<div id="mobileDrawer" role="dialog" aria-modal="true" aria-label="Menu navigasi">
  <div id="drawerBackdrop"></div>
  <div id="drawerPanel">

    <div class="drawer-header">
      <a href="<?= $base ?>/" class="flex items-center gap-2">
        <img src="<?= UPLOAD_URL . $logo ?>" alt="<?= clean($site_name) ?>"
             class="h-8 w-auto object-contain" onerror="this.style.display='none'">
        <span class="font-display font-bold text-rose-700 text-base"><?= clean($site_name) ?></span>
      </a>
      <button id="drawerClose" class="p-1.5 rounded-lg hover:bg-rose-100 transition-colors" aria-label="Tutup menu">
        <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
        </svg>
      </button>
    </div>

    <div class="drawer-body">

      <!-- Beranda -->
      <a href="<?= $base ?>/" class="dacc-l1-btn <?= $current_slug === '' ? 'active' : '' ?>">
        <span class="dacc-icon">
          <svg class="w-4 h-4 text-rose-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/>
            <polyline stroke-linecap="round" stroke-linejoin="round" stroke-width="2" points="9,22 9,12 15,12 15,22"/>
          </svg>
          Beranda
        </span>
      </a>

      <div class="drawer-divider"></div>
      <div class="drawer-section-label">Menu Utama</div>

      <!-- ── L1: PRODUK ── -->
      <button class="dacc-l1-btn" data-l1="layanan">
        <span class="dacc-icon">
          <svg class="w-4 h-4 text-rose-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
          </svg>
          Product
        </span>
        <svg class="dacc-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/>
        </svg>
      </button>
      <div class="dacc-l1-body" id="l1-layanan">
        <?php foreach ($navParentCats as $parent):
          $children = $navCatChildren[$parent['id']] ?? [];
          $pid = 'l2-cat-' . $parent['id'];
        ?>
          <?php if (!empty($children)): ?>
            <button class="dacc-l2-btn" data-l2="<?= $pid ?>">
              <?= clean($parent['name']) ?>
              <svg class="dacc-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/>
              </svg>
            </button>
            <div class="dacc-l2-body" id="<?= $pid ?>">
              <a href="<?= $base ?>/<?= $parent['slug'] ?>" class="dacc-l3-link" style="font-weight:600;color:#4b5563;">
                Semua <?= clean($parent['name']) ?>
              </a>
              <?php foreach ($children as $child): ?>
              <a href="<?= $base ?>/<?= $child['slug'] ?>" class="dacc-l3-link"><?= clean($child['name']) ?></a>
              <?php endforeach; ?>
            </div>
          <?php else: ?>
            <a href="<?= $base ?>/<?= $parent['slug'] ?>" class="dacc-l1-link"><?= clean($parent['name']) ?></a>
          <?php endif; ?>
        <?php endforeach; ?>
      </div>

      <!-- ── L1: AREA LAYANAN ── -->
      <button class="dacc-l1-btn" data-l1="area">
        <span class="dacc-icon">
          <svg class="w-4 h-4 text-rose-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
          </svg>
          Area Layanan
        </span>
        <svg class="dacc-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/>
        </svg>
      </button>
      <div class="dacc-l1-body" id="l1-area">
        <?php foreach ($navProvinces as $provName => $provCities):
          $provKey = 'l2-prov-' . preg_replace('/[^a-z0-9]/', '-', strtolower($provName));
        ?>
          <button class="dacc-l2-btn" data-l2="<?= $provKey ?>">
            <?= clean($provName) ?>
            <svg class="dacc-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/>
            </svg>
          </button>
          <div class="dacc-l2-body" id="<?= $provKey ?>">
            <?php foreach ($provCities as $city):
              $areas   = $navAreas[$city['id']] ?? [];
              $cityKey = 'l3-city-' . $city['id'];
            ?>
              <?php if (!empty($areas)): ?>
                <button class="dacc-l3-btn" data-l3="<?= $cityKey ?>">
                  <?= clean($city['name']) ?>
                  <svg class="dacc-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/>
                  </svg>
                </button>
                <div class="dacc-l3-body" id="<?= $cityKey ?>">
                  <a href="<?= $base ?>/toko-bunga-<?= $city['slug'] ?>" class="dacc-l3-link" style="font-weight:600;color:#6b7280;">
                    Semua Area <?= clean($city['name']) ?>
                  </a>
                  <?php foreach ($areas as $area): ?>
                  <a href="<?= $base ?>/toko-bunga-<?= $area['slug'] ?>" class="dacc-l3-link"><?= clean($area['name']) ?></a>
                  <?php endforeach; ?>
                </div>
              <?php else: ?>
                <a href="<?= $base ?>/toko-bunga-<?= $city['slug'] ?>" class="dacc-l3-link" style="font-weight:500;">
                  <?= clean($city['name']) ?>
                </a>
              <?php endif; ?>
            <?php endforeach; ?>
          </div>
        <?php endforeach; ?>
        <a href="<?= $base ?>/area-layanan" class="dacc-l1-link" style="color:#be185d;font-weight:700;">
          Lihat Semua Area →
        </a>
      </div>

      <div class="drawer-divider"></div>
      <div class="drawer-section-label">Lainnya</div>

      <a href="<?= $base ?>/toko-bunga-online-24-jam-indonesia"
         class="dacc-l1-btn <?= $current_slug === 'toko-bunga-online-24-jam-indonesia' ? 'active' : '' ?>"
         style="font-weight:600;">
        <span class="dacc-icon">
          <svg class="w-4 h-4 text-rose-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <circle cx="12" cy="12" r="10" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"/>
            <polyline stroke-linecap="round" stroke-linejoin="round" stroke-width="2" points="12,6 12,12 16,14"/>
          </svg>
          Layanan 24 Jam
        </span>
      </a>

      <a href="<?= $base ?>/galeri"
         class="dacc-l1-btn <?= $current_slug === 'galeri' ? 'active' : '' ?>"
         style="font-weight:600;">
        <span class="dacc-icon">
          <svg class="w-4 h-4 text-rose-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <rect x="3" y="3" width="18" height="18" rx="2" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"/>
            <circle cx="8.5" cy="8.5" r="1.5" stroke-width="2"/>
            <polyline stroke-linecap="round" stroke-linejoin="round" stroke-width="2" points="21,15 16,10 5,21"/>
          </svg>
          Galeri
        </span>
      </a>

      <!-- ── BLOG ── -->
      <a href="<?= $base ?>/blog"
         class="dacc-l1-btn <?= $current_slug === 'blog' || strpos($current_slug, 'blog/') === 0 ? 'active' : '' ?>"
         style="font-weight:600;">
        <span class="dacc-icon">
          <svg class="w-4 h-4 text-rose-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10l6 6v10a2 2 0 01-2 2z"/>
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M13 4v6h6M9 14h6M9 17h4"/>
          </svg>
          Blog
        </span>
      </a>

    </div><!-- /drawer-body -->

    <div class="drawer-footer">
      <a href="<?= waLink() ?>" target="_blank" rel="noopener"
         class="flex items-center justify-center gap-2 w-full bg-green-500 hover:bg-green-600 text-white font-semibold text-sm py-3 rounded-xl transition-colors shadow-sm">
        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
          <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z"/>
          <path d="M12 0C5.373 0 0 5.373 0 12c0 2.096.541 4.063 1.491 5.776L.057 23.04l5.43-1.424A11.947 11.947 0 0012 24c6.627 0 12-5.373 12-12S18.627 0 12 0zm0 21.818a9.818 9.818 0 01-5.015-1.371l-.36-.214-3.726.977.995-3.633-.235-.374A9.818 9.818 0 1112 21.818z"/>
        </svg>
        Pesan via WhatsApp
      </a>
    </div>

  </div>
</div>

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
(function () {
  'use strict';

  /* ── Drawer ─────────────────────────────── */
  var toggle   = document.getElementById('menuToggle');
  var drawer   = document.getElementById('mobileDrawer');
  var backdrop = document.getElementById('drawerBackdrop');
  var closeBtn = document.getElementById('drawerClose');

  function openDrawer()  {
    drawer.classList.add('open');
    toggle.classList.add('is-open');
    document.body.style.overflow = 'hidden';
    toggle.setAttribute('aria-label','Tutup menu');
  }
  function closeDrawer() {
    drawer.classList.remove('open');
    toggle.classList.remove('is-open');
    document.body.style.overflow = '';
    toggle.setAttribute('aria-label','Buka menu');
  }
  toggle.addEventListener('click', function() {
    drawer.classList.contains('open') ? closeDrawer() : openDrawer();
  });
  backdrop.addEventListener('click', closeDrawer);
  closeBtn.addEventListener('click', closeDrawer);
  document.addEventListener('keydown', function(e) { if (e.key==='Escape') closeDrawer(); });

  /* ── Accordion L1 ─────────────────────── */
  document.querySelectorAll('.dacc-l1-btn[data-l1]').forEach(function(btn) {
    btn.addEventListener('click', function() {
      var key  = btn.getAttribute('data-l1');
      var body = document.getElementById('l1-' + key);
      if (!body) return;
      var isOpen = body.classList.contains('is-open');

      document.querySelectorAll('.dacc-l1-body.is-open').forEach(function(el){ el.classList.remove('is-open'); });
      document.querySelectorAll('.dacc-l1-btn.is-open').forEach(function(el){ el.classList.remove('is-open'); });
      document.querySelectorAll('.dacc-l2-body.is-open').forEach(function(el){ el.classList.remove('is-open'); });
      document.querySelectorAll('.dacc-l2-btn.is-open').forEach(function(el){ el.classList.remove('is-open'); });
      document.querySelectorAll('.dacc-l3-body.is-open').forEach(function(el){ el.classList.remove('is-open'); });
      document.querySelectorAll('.dacc-l3-btn.is-open').forEach(function(el){ el.classList.remove('is-open'); });

      if (!isOpen) { body.classList.add('is-open'); btn.classList.add('is-open'); }
    });
  });

  /* ── Accordion L2 ─────────────────────── */
  document.querySelectorAll('.dacc-l2-btn[data-l2]').forEach(function(btn) {
    btn.addEventListener('click', function(e) {
      e.stopPropagation();
      var key  = btn.getAttribute('data-l2');
      var body = document.getElementById(key);
      if (!body) return;
      var isOpen = body.classList.contains('is-open');

      var parentL1 = btn.closest('.dacc-l1-body');
      if (parentL1) {
        parentL1.querySelectorAll('.dacc-l2-body.is-open').forEach(function(el){ el.classList.remove('is-open'); });
        parentL1.querySelectorAll('.dacc-l2-btn.is-open').forEach(function(el){ el.classList.remove('is-open'); });
        parentL1.querySelectorAll('.dacc-l3-body.is-open').forEach(function(el){ el.classList.remove('is-open'); });
        parentL1.querySelectorAll('.dacc-l3-btn.is-open').forEach(function(el){ el.classList.remove('is-open'); });
      }
      if (!isOpen) { body.classList.add('is-open'); btn.classList.add('is-open'); }
    });
  });

  /* ── Accordion L3 ─────────────────────── */
  document.querySelectorAll('.dacc-l3-btn[data-l3]').forEach(function(btn) {
    btn.addEventListener('click', function(e) {
      e.stopPropagation();
      var key  = btn.getAttribute('data-l3');
      var body = document.getElementById(key);
      if (!body) return;
      var isOpen = body.classList.contains('is-open');

      var parentL2 = btn.closest('.dacc-l2-body');
      if (parentL2) {
        parentL2.querySelectorAll('.dacc-l3-body.is-open').forEach(function(el){ el.classList.remove('is-open'); });
        parentL2.querySelectorAll('.dacc-l3-btn.is-open').forEach(function(el){ el.classList.remove('is-open'); });
      }
      if (!isOpen) { body.classList.add('is-open'); btn.classList.add('is-open'); }
    });
  });

  /* ── Desktop flyout overflow guard ─────── */
  document.querySelectorAll('.nav-flyout').forEach(function(flyout) {
    var parent = flyout.closest('.has-flyout');
    if (!parent) return;
    parent.addEventListener('mouseenter', function() {
      flyout.classList.remove('open-left');
      setTimeout(function() {
        var rect = flyout.getBoundingClientRect();
        if (rect.right > window.innerWidth - 16) {
          flyout.classList.add('open-left');
        }
      }, 10);
    });
  });

  /* ── Keyboard flyout (Enter/Space) ─────── */
  document.querySelectorAll('.has-flyout[tabindex]').forEach(function(item) {
    item.addEventListener('keydown', function(e) {
      if (e.key === 'Enter' || e.key === ' ') {
        e.preventDefault();
        var flyout = item.querySelector(':scope > .nav-flyout');
        if (!flyout) return;
        var visible = getComputedStyle(flyout).opacity === '1';
        flyout.style.opacity    = visible ? '0' : '1';
        flyout.style.visibility = visible ? 'hidden' : 'visible';
        flyout.style.transform  = visible ? 'translateX(6px)' : 'translateX(0)';
        flyout.style.pointerEvents = visible ? 'none' : 'auto';
      }
    });
  });

})();
</script>