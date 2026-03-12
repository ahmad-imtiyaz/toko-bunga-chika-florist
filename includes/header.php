<?php
require_once __DIR__ . '/config.php';

$site_name      = getSetting('site_name', 'Chika Florist');
$site_tagline   = getSetting('site_tagline', '');
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
<link rel="icon" type="image/jpeg" href="<?= UPLOAD_URL . $logo ?>">
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

<style>
/* ── DRAWER ─────────────────────────────────── */
#mobileDrawer {
  position: fixed;
  inset: 0;
  z-index: 9999;
  visibility: hidden;
}
#mobileDrawer.open {
  visibility: visible;
}

/* Backdrop */
#drawerBackdrop {
  position: absolute;
  inset: 0;
  background: rgba(0,0,0,0);
  transition: background .3s ease;
}
#mobileDrawer.open #drawerBackdrop {
  background: rgba(0,0,0,0.45);
}

/* Panel */
#drawerPanel {
  position: absolute;
  top: 0; left: 0; bottom: 0;
  width: 300px;
  max-width: 85vw;
  background: #fff;
  transform: translateX(-100%);
  transition: transform .32s cubic-bezier(.4,0,.2,1);
  display: flex;
  flex-direction: column;
  overflow: hidden;
  box-shadow: 4px 0 24px rgba(0,0,0,0.12);
}
#mobileDrawer.open #drawerPanel {
  transform: translateX(0);
}

/* Drawer header */
.drawer-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 1rem 1.2rem;
  border-bottom: 1px solid #fce7f3;
  flex-shrink: 0;
  background: linear-gradient(135deg, #fff8f8 0%, #fdf6ee 100%);
}

/* Drawer scroll area */
.drawer-body {
  flex: 1;
  overflow-y: auto;
  padding: 0.75rem 0 1.5rem;
  -webkit-overflow-scrolling: touch;
}

/* Nav item */
.drawer-item {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 0.75rem 1.25rem;
  font-size: 0.925rem;
  font-weight: 600;
  color: #374151;
  cursor: pointer;
  transition: background .15s, color .15s;
  border: none;
  background: none;
  width: 100%;
  text-align: left;
  text-decoration: none;
}
.drawer-item:hover {
  background: #fdf2f8;
  color: #be185d;
}
.drawer-item.active {
  color: #be185d;
}

/* Chevron icon */
.drawer-chevron {
  width: 16px; height: 16px;
  transition: transform .25s ease;
  flex-shrink: 0;
  color: #9ca3af;
}
.drawer-item.active .drawer-chevron {
  transform: rotate(180deg);
  color: #be185d;
}

/* Submenu */
.drawer-sub {
  max-height: 0;
  overflow: hidden;
  transition: max-height .35s cubic-bezier(.4,0,.2,1);
  background: #fdf8ff;
  border-left: 3px solid #fce7f3;
  margin: 0 1.25rem 0 1.5rem;
  border-radius: 0 8px 8px 0;
}
.drawer-sub.open {
  max-height: 500px;
}
.drawer-sub a {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  padding: 0.6rem 1rem;
  font-size: 0.85rem;
  color: #6b7280;
  text-decoration: none;
  transition: color .15s, background .15s;
  border-radius: 6px;
  margin: 2px 4px;
}
.drawer-sub a:hover {
  color: #be185d;
  background: #fce7f3;
}
.drawer-sub a::before {
  content: '';
  width: 5px; height: 5px;
  background: #f9a8d4;
  border-radius: 50%;
  flex-shrink: 0;
}

/* Divider */
.drawer-divider {
  height: 1px;
  background: #f3f4f6;
  margin: 0.5rem 1.25rem;
}

/* Drawer footer WA button */
.drawer-footer {
  padding: 1rem 1.25rem;
  border-top: 1px solid #fce7f3;
  flex-shrink: 0;
}

/* Hamburger → X animation */
.ham-line {
  display: block;
  width: 20px; height: 2px;
  background: #374151;
  border-radius: 2px;
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

<!-- ══════════ NAVBAR ══════════ -->
<header class="bg-white shadow-sm sticky top-0 z-50 border-b border-rose-100">
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
<div class="flex items-center justify-between h-16">

  <!-- Logo -->
  <a href="<?= $base ?>/" class="flex items-center gap-3 shrink-0">
    <img src="<?= UPLOAD_URL . $logo ?>" alt="<?= clean($site_name) ?>"
         class="h-10 w-auto object-contain" id="navLogo"
         onerror="this.style.display='none'">
    <div class="leading-tight">
      <p class="font-display font-bold text-rose-700 text-lg leading-none"><?= clean($site_name) ?></p>
      <?php if ($site_tagline): ?>
      <p class="text-xs text-gray-400 mt-0.5 leading-none hidden sm:block"><?= clean($site_tagline) ?></p>
      <?php endif; ?>
    </div>
  </a>

  <!-- Nav Desktop -->
  <nav class="hidden lg:flex items-center gap-6 text-sm">
    <a href="<?= $base ?>/" class="text-gray-700 hover:text-rose-600 transition-colors font-medium">Beranda</a>

    <!-- Dropdown Produk -->
    <div class="relative group">
      <button class="flex items-center gap-1 text-gray-700 hover:text-rose-600 transition-colors font-medium">
        Produk
        <svg class="w-3 h-3 transition-transform group-hover:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
        </svg>
      </button>
      <div class="absolute top-full left-0 mt-2 w-52 bg-white rounded-xl shadow-lg border border-rose-50 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 z-50 py-1">
        <?php foreach ($mainCategories as $cat): ?>
        <a href="<?= $base ?>/<?= $cat['slug'] ?>"
           class="flex items-center gap-2 px-4 py-2.5 text-sm text-gray-700 hover:bg-rose-50 hover:text-rose-600 transition-colors">
          <span class="w-1.5 h-1.5 rounded-full bg-rose-300 flex-shrink-0"></span>
          <?= clean($cat['name']) ?>
        </a>
        <?php endforeach; ?>
      </div>
    </div>

    <!-- Dropdown Area Layanan -->
    <div class="relative group">
      <button class="flex items-center gap-1 text-gray-700 hover:text-rose-600 transition-colors font-medium">
        Area Layanan
        <svg class="w-3 h-3 transition-transform group-hover:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
        </svg>
      </button>
      <div class="absolute top-full left-0 mt-2 w-56 bg-white rounded-xl shadow-lg border border-rose-50 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 z-50 py-1">
        <?php foreach ($navCities as $city): ?>
        <a href="<?= $base ?>/toko-bunga-<?= $city['slug'] ?>"
           class="flex items-center gap-2 px-4 py-2.5 text-sm text-gray-700 hover:bg-rose-50 hover:text-rose-600 transition-colors">
          <svg class="w-3 h-3 text-rose-300 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
          </svg>
          <?= clean($city['name']) ?>
        </a>
        <?php endforeach; ?>
        <div class="border-t border-rose-50 mt-1 pt-1">
          <a href="<?= $base ?>/area-layanan"
             class="flex items-center gap-2 px-4 py-2.5 text-sm text-rose-600 font-semibold hover:bg-rose-50 transition-colors rounded-b-xl">
            Lihat Semua Kota →
          </a>
        </div>
      </div>
    </div>

    <a href="<?= $base ?>/toko-bunga-online-24-jam-indonesia" class="text-gray-700 hover:text-rose-600 transition-colors font-medium">Layanan 24 Jam</a>
    <a href="<?= $base ?>/galeri" class="text-gray-700 hover:text-rose-600 transition-colors font-medium">Galeri</a>
  </nav>

  <!-- CTA Desktop + Hamburger -->
  <div class="flex items-center gap-3">
    <a href="<?= waLink() ?>" target="_blank" rel="noopener"
       class="hidden sm:flex items-center gap-2 bg-green-500 hover:bg-green-600 text-white text-sm font-semibold px-4 py-2 rounded-full transition-colors shadow-sm">
      <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
        <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z"/>
        <path d="M12 0C5.373 0 0 5.373 0 12c0 2.096.541 4.063 1.491 5.776L.057 23.04l5.43-1.424A11.947 11.947 0 0012 24c6.627 0 12-5.373 12-12S18.627 0 12 0zm0 21.818a9.818 9.818 0 01-5.015-1.371l-.36-.214-3.726.977.995-3.633-.235-.374A9.818 9.818 0 1112 21.818z"/>
      </svg>
      Pesan Sekarang
    </a>

    <!-- Hamburger button -->
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

<!-- ══════════ MOBILE DRAWER ══════════ -->
<div id="mobileDrawer" role="dialog" aria-modal="true" aria-label="Menu navigasi">

  <!-- Backdrop -->
  <div id="drawerBackdrop"></div>

  <!-- Panel -->
  <div id="drawerPanel">

    <!-- Header drawer -->
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

    <!-- Body scrollable -->
    <div class="drawer-body">

      <!-- Beranda -->
      <a href="<?= $base ?>/" class="drawer-item">
        <span class="flex items-center gap-2.5">
          <svg class="w-4 h-4 text-rose-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/>
            <polyline stroke-linecap="round" stroke-linejoin="round" stroke-width="2" points="9,22 9,12 15,12 15,22"/>
          </svg>
          Beranda
        </span>
      </a>

      <div class="drawer-divider"></div>

      <!-- Accordion: Produk -->
      <button class="drawer-item" id="accordionProdukBtn" aria-expanded="false">
        <span class="flex items-center gap-2.5">
          <svg class="w-4 h-4 text-rose-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
          </svg>
          Produk
        </span>
        <svg class="drawer-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
        </svg>
      </button>
      <div class="drawer-sub" id="accordionProdukMenu">
        <?php foreach ($mainCategories as $cat): ?>
        <a href="<?= $base ?>/<?= $cat['slug'] ?>"><?= clean($cat['name']) ?></a>
        <?php endforeach; ?>
      </div>

      <!-- Accordion: Area Layanan -->
      <button class="drawer-item" id="accordionKotaBtn" aria-expanded="false">
        <span class="flex items-center gap-2.5">
          <svg class="w-4 h-4 text-rose-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
          </svg>
          Area Layanan
        </span>
        <svg class="drawer-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
        </svg>
      </button>
      <div class="drawer-sub" id="accordionKotaMenu">
        <?php foreach ($navCities as $city): ?>
        <a href="<?= $base ?>/toko-bunga-<?= $city['slug'] ?>"><?= clean($city['name']) ?></a>
        <?php endforeach; ?>
        <a href="<?= $base ?>/area-layanan" style="color:#be185d;font-weight:600;">Lihat Semua Kota →</a>
      </div>

      <div class="drawer-divider"></div>

      <!-- Link biasa -->
      <a href="<?= $base ?>/toko-bunga-online-24-jam-indonesia" class="drawer-item">
        <span class="flex items-center gap-2.5">
          <svg class="w-4 h-4 text-rose-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <circle cx="12" cy="12" r="10" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"/>
            <polyline stroke-linecap="round" stroke-linejoin="round" stroke-width="2" points="12,6 12,12 16,14"/>
          </svg>
          Layanan 24 Jam
        </span>
      </a>

      <a href="<?= $base ?>/galeri" class="drawer-item">
        <span class="flex items-center gap-2.5">
          <svg class="w-4 h-4 text-rose-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <rect x="3" y="3" width="18" height="18" rx="2" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"/>
            <circle cx="8.5" cy="8.5" r="1.5" stroke-width="2"/>
            <polyline stroke-linecap="round" stroke-linejoin="round" stroke-width="2" points="21,15 16,10 5,21"/>
          </svg>
          Galeri
        </span>
      </a>

    </div><!-- /.drawer-body -->

    <!-- Footer drawer — tombol WA -->
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

  </div><!-- /#drawerPanel -->
</div><!-- /#mobileDrawer -->

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
  const toggle    = document.getElementById('menuToggle');
  const drawer    = document.getElementById('mobileDrawer');
  const backdrop  = document.getElementById('drawerBackdrop');
  const closeBtn  = document.getElementById('drawerClose');

  // ── Buka / tutup drawer ──────────────────────
  function openDrawer() {
    drawer.classList.add('open');
    toggle.classList.add('is-open');
    document.body.style.overflow = 'hidden';
    toggle.setAttribute('aria-label', 'Tutup menu');
  }
  function closeDrawer() {
    drawer.classList.remove('open');
    toggle.classList.remove('is-open');
    document.body.style.overflow = '';
    toggle.setAttribute('aria-label', 'Buka menu');
  }

  toggle.addEventListener('click', function () {
    drawer.classList.contains('open') ? closeDrawer() : openDrawer();
  });
  backdrop.addEventListener('click', closeDrawer);
  closeBtn.addEventListener('click', closeDrawer);

  // Tutup dengan Esc
  document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape') closeDrawer();
  });

  // ── Accordion helper ─────────────────────────
  function initAccordion(btnId, menuId) {
    const btn  = document.getElementById(btnId);
    const menu = document.getElementById(menuId);
    if (!btn || !menu) return;

    btn.addEventListener('click', function () {
      const isOpen = menu.classList.contains('open');

      // Tutup semua accordion lain dulu
      document.querySelectorAll('.drawer-sub.open').forEach(function (el) {
        el.classList.remove('open');
      });
      document.querySelectorAll('.drawer-item.active').forEach(function (el) {
        el.classList.remove('active');
        el.setAttribute('aria-expanded', 'false');
      });

      // Toggle yang diklik
      if (!isOpen) {
        menu.classList.add('open');
        btn.classList.add('active');
        btn.setAttribute('aria-expanded', 'true');
      }
    });
  }

  initAccordion('accordionProdukBtn', 'accordionProdukMenu');
  initAccordion('accordionKotaBtn',   'accordionKotaMenu');

})();
</script>