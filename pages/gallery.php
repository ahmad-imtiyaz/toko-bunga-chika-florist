<?php
require_once __DIR__ . '/../includes/config.php';
$pdo   = getDB();
$items = $pdo->query("SELECT * FROM gallery WHERE is_active=1 ORDER BY sort_order ASC")->fetchAll();
$page_title    = 'Galeri Bunga | Chika Florist – Toko Bunga Online 24 Jam';
$meta_desc     = 'Galeri karya Chika Florist – bunga papan, buket bunga, standing flower & karangan bunga custom.';
$canonical_url = BASE_URL . '/galeri';
$breadcrumbs   = [['label'=>'Beranda','url'=>'/'],['label'=>'Galeri']];
require_once __DIR__ . '/../includes/header.php';

// Ambil 5 foto pertama untuk collage hero
$heroItems  = array_slice($items, 0, 5);
$galleryItems = $items; // semua untuk masonry
?>

<style>
@import url('https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,400;0,600;0,700;1,400;1,600;1,700&family=Jost:wght@300;400;500;600;700&display=swap');

/* ==============================
   HERO — COLLAGE MOODBOARD
   ============================== */
.gal-hero {
  position: relative;
  min-height: 480px;
  display: flex;
  align-items: center;
  justify-content: center;
  overflow: hidden;
  background: linear-gradient(145deg, #1a0810 0%, #2d1020 50%, #1c1a0c 100%);
}

/* Atmospheric glow */
.gal-hero-glow {
  position: absolute;
  border-radius: 50%;
  filter: blur(80px);
  pointer-events: none;
}

/* Collage grid */
.gal-collage {
  position: absolute;
  inset: 0;
  display: grid;
  grid-template-columns: 1fr 1fr 1.2fr 1fr 1fr;
  grid-template-rows: 1fr 1fr;
  gap: 4px;
  opacity: 0.38;
}
.gal-collage-cell {
  overflow: hidden;
  position: relative;
}
.gal-collage-cell:nth-child(1) { grid-row: 1 / 3; }
.gal-collage-cell:nth-child(3) { grid-row: 1 / 3; }
.gal-collage-cell:nth-child(5) { grid-row: 1 / 3; }
.gal-collage-cell img {
  width: 100%; height: 100%;
  object-fit: cover;
  filter: saturate(0.7) brightness(0.8);
  transition: transform 12s ease;
  transform: scale(1.08);
}
.gal-collage-cell:hover img { transform: scale(1); }

/* Dark vignette overlay */
.gal-collage-overlay {
  position: absolute;
  inset: 0;
  background:
    radial-gradient(ellipse 70% 70% at 50% 50%, rgba(26,8,16,0.2) 0%, rgba(26,8,16,0.75) 100%),
    linear-gradient(to bottom, rgba(26,8,16,0.5) 0%, rgba(26,8,16,0.3) 40%, rgba(26,8,16,0.6) 100%);
  z-index: 2;
}

/* Trellis pattern */
.gal-hero-trellis {
  position: absolute;
  inset: 0;
  z-index: 3;
  opacity: 0.05;
  background-image:
    repeating-linear-gradient(45deg, #c9a84c 0, #c9a84c 1px, transparent 0, transparent 50%),
    repeating-linear-gradient(-45deg, #c9a84c 0, #c9a84c 1px, transparent 0, transparent 50%);
  background-size: 32px 32px;
}

/* Hero content */
.gal-hero-content {
  position: relative;
  z-index: 10;
  text-align: center;
  padding: 3rem 1.5rem;
  max-width: 640px;
  animation: galFadeUp .9s cubic-bezier(.22,.68,0,1.2) both;
}
@keyframes galFadeUp {
  from { opacity:0; transform:translateY(28px); }
  to   { opacity:1; transform:translateY(0); }
}

.gal-eyebrow {
  display: inline-flex;
  align-items: center;
  gap: .45rem;
  font-family: 'Jost', sans-serif;
  font-size: .7rem;
  font-weight: 700;
  letter-spacing: .12em;
  text-transform: uppercase;
  color: #f9c784;
  background: rgba(201,168,76,.15);
  border: 1px solid rgba(201,168,76,.3);
  padding: .28rem .9rem;
  border-radius: 999px;
  margin-bottom: 1.1rem;
  animation: galFadeUp .7s .1s both;
}
.gal-eyebrow-dot {
  width: 5px; height: 5px;
  border-radius: 50%;
  background: #f9c784;
  animation: eyebrowPulse 2s ease-in-out infinite;
}
@keyframes eyebrowPulse {
  0%,100% { transform:scale(1); opacity:1; }
  50%      { transform:scale(1.8); opacity:.5; }
}

.gal-hero h1 {
  font-family: 'Cormorant Garamond', serif;
  font-size: clamp(2.2rem, 6vw, 4rem);
  font-weight: 700;
  color: #fff;
  line-height: 1.1;
  margin-bottom: .8rem;
  text-shadow: 0 2px 30px rgba(0,0,0,.5);
  animation: galFadeUp .7s .2s both;
}
.gal-hero h1 em { font-style: italic; color: #fda4af; }

.gal-hero-divider {
  width: 70px; height: 2px;
  background: linear-gradient(90deg, transparent, #c9a84c, transparent);
  margin: .8rem auto 1rem;
  animation: galFadeUp .7s .25s both;
}

.gal-hero-desc {
  font-family: 'Jost', sans-serif;
  font-size: .9rem;
  color: rgba(255,255,255,.65);
  line-height: 1.7;
  max-width: 460px;
  margin: 0 auto 1.4rem;
  animation: galFadeUp .7s .3s both;
}

/* Count badge */
.gal-count-badge {
  display: inline-flex;
  align-items: center;
  gap: .5rem;
  font-family: 'Jost', sans-serif;
  font-size: .8rem;
  font-weight: 600;
  color: rgba(255,255,255,.8);
  background: rgba(255,255,255,.1);
  border: 1px solid rgba(255,255,255,.15);
  backdrop-filter: blur(8px);
  padding: .45rem 1rem;
  border-radius: 999px;
  animation: galFadeUp .7s .35s both;
}

/* Scroll hint */
.gal-scroll-hint {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: .3rem;
  margin-top: 1.5rem;
  animation: galFadeUp .7s .45s both;
  cursor: pointer;
}
.gal-scroll-hint span {
  font-family: 'Jost', sans-serif;
  font-size: .65rem;
  font-weight: 600;
  letter-spacing: .1em;
  text-transform: uppercase;
  color: rgba(255,255,255,.35);
}
.gal-scroll-line {
  width: 1px; height: 32px;
  background: linear-gradient(to bottom, rgba(255,255,255,.3), transparent);
  animation: scrollLine 2s ease-in-out infinite;
}
@keyframes scrollLine {
  0%   { transform: scaleY(0); transform-origin: top; opacity:0; }
  50%  { transform: scaleY(1); opacity:1; }
  100% { transform: scaleY(0); transform-origin: bottom; opacity:0; }
}

/* Wave */
.gal-hero-wave {
  position: absolute;
  bottom: -1px; left:0; right:0;
  z-index: 11; line-height:0;
}

/* ==============================
   MASONRY GALLERY
   ============================== */
.gal-body {
  background: linear-gradient(170deg, #fdf6ee 0%, #fff 55%, #fdf0f3 100%);
  padding: 3rem 0 4rem;
}

.gal-masonry {
  columns: 2;
  column-gap: .75rem;
  max-width: 1280px;
  margin: 0 auto;
  padding: 0 1rem;
}
@media(min-width:640px)  { .gal-masonry { columns: 3; column-gap: 1rem; padding: 0 1.5rem; } }
@media(min-width:1024px) { .gal-masonry { columns: 4; column-gap: 1.1rem; padding: 0 2rem; } }

/* Gallery item */
.gal-item {
  break-inside: avoid;
  margin-bottom: .75rem;
  position: relative;
  border-radius: 1rem;
  overflow: hidden;
  cursor: pointer;
  background: linear-gradient(135deg, #fdf0f3, #fdf6ee);
  border: 1px solid #fde8b4;
  transition: transform .3s cubic-bezier(.22,.68,0,1.2), box-shadow .3s, border-color .3s;

  /* Lazy load — initially hidden */
  opacity: 0;
  transform: translateY(20px);
}
@media(min-width:640px) { .gal-item { margin-bottom: 1rem; } }

.gal-item.visible {
  opacity: 1;
  transform: translateY(0);
  transition: opacity .5s ease, transform .5s cubic-bezier(.22,.68,0,1.2), box-shadow .3s, border-color .3s;
}
.gal-item:hover {
  transform: translateY(-5px) scale(1.01);
  box-shadow: 0 16px 40px rgba(214,90,110,.18);
  border-color: #fca5a5;
}

.gal-item img {
  width: 100%;
  height: auto;
  display: block;
  transition: transform .5s ease;
}
.gal-item:hover img { transform: scale(1.04); }

/* Hover overlay */
.gal-overlay {
  position: absolute;
  inset: 0;
  background: linear-gradient(
    to top,
    rgba(17,5,8,0.82) 0%,
    rgba(17,5,8,0.35) 45%,
    rgba(17,5,8,0) 70%
  );
  opacity: 0;
  transition: opacity .3s ease;
  display: flex;
  flex-direction: column;
  justify-content: flex-end;
  padding: 1rem;
}
.gal-item:hover .gal-overlay { opacity: 1; }

.gal-overlay-title {
  font-family: 'Cormorant Garamond', serif;
  font-size: 1rem;
  font-weight: 700;
  color: #fff;
  line-height: 1.3;
  transform: translateY(8px);
  transition: transform .3s ease;
  margin-bottom: .4rem;
}
.gal-item:hover .gal-overlay-title { transform: translateY(0); }

.gal-overlay-btn {
  display: inline-flex;
  align-items: center;
  gap: .4rem;
  font-family: 'Jost', sans-serif;
  font-size: .72rem;
  font-weight: 700;
  letter-spacing: .04em;
  color: #fff;
  background: #16a34a;
  padding: .4rem .9rem;
  border-radius: 999px;
  text-decoration: none;
  width: fit-content;
  transform: translateY(10px);
  opacity: 0;
  transition: transform .3s .05s ease, opacity .3s .05s ease;
  border: none;
}
.gal-item:hover .gal-overlay-btn {
  transform: translateY(0);
  opacity: 1;
}
.gal-overlay-btn:hover { background: #15803d; }

/* Zoom icon on hover */
.gal-zoom-icon {
  position: absolute;
  top: .6rem;
  right: .6rem;
  width: 30px; height: 30px;
  border-radius: 50%;
  background: rgba(255,255,255,.15);
  backdrop-filter: blur(8px);
  border: 1px solid rgba(255,255,255,.25);
  display: flex; align-items: center; justify-content: center;
  opacity: 0;
  transform: scale(.8);
  transition: opacity .25s, transform .25s;
  z-index: 3;
}
.gal-item:hover .gal-zoom-icon { opacity: 1; transform: scale(1); }
.gal-zoom-icon svg { color: #fff; }

/* ==============================
   LIGHTBOX
   ============================== */
.lb-backdrop {
  position: fixed;
  inset: 0;
  background: rgba(15,5,8,.95);
  z-index: 9999;
  display: flex;
  align-items: center;
  justify-content: center;
  opacity: 0;
  pointer-events: none;
  transition: opacity .3s ease;
  backdrop-filter: blur(6px);
}
.lb-backdrop.open {
  opacity: 1;
  pointer-events: all;
}

.lb-inner {
  position: relative;
  max-width: 90vw;
  max-height: 90vh;
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: .75rem;
  animation: lbZoomIn .3s cubic-bezier(.22,.68,0,1.2) both;
}
@keyframes lbZoomIn {
  from { transform: scale(.92); opacity:0; }
  to   { transform: scale(1);   opacity:1; }
}

.lb-img-wrap {
  position: relative;
  border-radius: 1rem;
  overflow: hidden;
  box-shadow: 0 30px 80px rgba(0,0,0,.6);
  border: 1px solid rgba(255,255,255,.08);
}
.lb-img-wrap img {
  max-width: 88vw;
  max-height: 75vh;
  width: auto;
  height: auto;
  display: block;
  object-fit: contain;
}

/* LB info bar */
.lb-info {
  display: flex;
  align-items: center;
  justify-content: space-between;
  width: 100%;
  gap: 1rem;
}
.lb-title {
  font-family: 'Cormorant Garamond', serif;
  font-size: 1.1rem;
  font-weight: 600;
  color: #fff;
  font-style: italic;
}
.lb-cat {
  font-family: 'Jost', sans-serif;
  font-size: .7rem;
  font-weight: 700;
  letter-spacing: .08em;
  text-transform: uppercase;
  color: #f9c784;
  background: rgba(201,168,76,.15);
  border: 1px solid rgba(201,168,76,.3);
  padding: .2rem .6rem;
  border-radius: 999px;
}
.lb-wa-btn {
  display: inline-flex;
  align-items: center;
  gap: .4rem;
  font-family: 'Jost', sans-serif;
  font-size: .75rem;
  font-weight: 700;
  color: #fff;
  background: #16a34a;
  padding: .5rem 1rem;
  border-radius: 999px;
  text-decoration: none;
  flex-shrink: 0;
  transition: background .2s;
}
.lb-wa-btn:hover { background: #15803d; }

/* Close button */
.lb-close {
  position: absolute;
  top: -14px; right: -14px;
  width: 36px; height: 36px;
  border-radius: 50%;
  background: rgba(255,255,255,.12);
  border: 1px solid rgba(255,255,255,.2);
  backdrop-filter: blur(8px);
  display: flex; align-items: center; justify-content: center;
  cursor: pointer;
  transition: background .2s, transform .2s;
  z-index: 2;
}
.lb-close:hover { background: #e11d48; transform: rotate(90deg); }
.lb-close svg { color: #fff; }

/* Prev / Next */
.lb-prev, .lb-next {
  position: fixed;
  top: 50%;
  transform: translateY(-50%);
  width: 44px; height: 44px;
  border-radius: 50%;
  background: rgba(255,255,255,.1);
  border: 1px solid rgba(255,255,255,.18);
  backdrop-filter: blur(8px);
  display: flex; align-items: center; justify-content: center;
  cursor: pointer;
  transition: background .2s, transform .2s;
  z-index: 10000;
}
.lb-prev { left: 1rem; }
.lb-next { right: 1rem; }
.lb-prev:hover, .lb-next:hover { background: rgba(225,29,72,.7); transform: translateY(-50%) scale(1.08); }
.lb-prev svg, .lb-next svg { color: #fff; }

/* Counter */
.lb-counter {
  font-family: 'Jost', sans-serif;
  font-size: .7rem;
  font-weight: 600;
  color: rgba(255,255,255,.4);
  letter-spacing: .06em;
  text-align: center;
}

/* ==============================
   EMPTY STATE
   ============================== */
.gal-empty {
  text-align: center;
  padding: 6rem 1rem;
  color: #9ca3af;
}

/* ==============================
   CTA
   ============================== */
.gal-cta {
  background: linear-gradient(135deg, #881337 0%, #9f1239 50%, #7f1d1d 100%);
  position: relative; overflow: hidden;
}
.gal-cta-pat {
  position: absolute; inset: 0;
  background-image:
    repeating-linear-gradient(45deg, rgba(255,255,255,.025) 0, rgba(255,255,255,.025) 1px, transparent 0, transparent 50%),
    repeating-linear-gradient(-45deg, rgba(255,255,255,.025) 0, rgba(255,255,255,.025) 1px, transparent 0, transparent 50%);
  background-size: 28px 28px;
}
</style>

<!-- ==============================
     HERO — COLLAGE MOODBOARD
     ============================== -->
<section class="gal-hero">

  <!-- Glow -->
  <div class="gal-hero-glow" style="width:400px;height:400px;background:rgba(190,24,93,.2);top:-100px;left:-80px;"></div>
  <div class="gal-hero-glow" style="width:300px;height:300px;background:rgba(201,168,76,.13);bottom:60px;right:5%;"></div>

  <!-- Collage -->
  <?php if (!empty($heroItems)): ?>
  <div class="gal-collage">
    <?php
    // Isi sel collage — 5 foto, kalau kurang loop
    $collageCells = 6;
    for ($ci = 0; $ci < $collageCells; $ci++):
      $photo = $heroItems[$ci % count($heroItems)];
    ?>
    <div class="gal-collage-cell">
      <img src="<?= UPLOAD_URL . $photo['image'] ?>" alt="<?= clean($photo['alt_text'] ?: $photo['title'] ?: 'Galeri Chika Florist') ?>" loading="lazy">
    </div>
    <?php endfor; ?>
  </div>
  <?php endif; ?>

  <div class="gal-collage-overlay"></div>
  <div class="gal-hero-trellis"></div>

  <!-- SVG bunga dekorasi -->
  <svg style="position:absolute;top:-10px;left:-10px;opacity:.08;z-index:4;pointer-events:none;" width="200" height="200" viewBox="0 0 200 200" fill="none">
    <?php foreach([0,60,120,180,240,300] as $r): ?>
    <ellipse cx="100" cy="55" rx="18" ry="38" fill="#fda4af" transform="rotate(<?=$r?> 100 100)"/>
    <?php endforeach; ?>
    <circle cx="100" cy="100" r="20" fill="#f9c784"/>
  </svg>
  <svg style="position:absolute;bottom:50px;right:-10px;opacity:.07;z-index:4;pointer-events:none;transform:rotate(20deg) scaleX(-1);" width="170" height="170" viewBox="0 0 170 170" fill="none">
    <?php foreach([0,72,144,216,288] as $r): ?>
    <ellipse cx="85" cy="44" rx="14" ry="32" fill="#c9a84c" transform="rotate(<?=$r?> 85 85)"/>
    <?php endforeach; ?>
    <circle cx="85" cy="85" r="16" fill="#fda4af"/>
  </svg>

  <!-- Content -->
  <div class="gal-hero-content">
    <div class="gal-eyebrow">
      <span class="gal-eyebrow-dot"></span>
      Karya Terbaik Kami
    </div>

    <h1>Galeri <em>Bunga</em><br>Chika Florist</h1>

    <div class="gal-hero-divider"></div>

    <p class="gal-hero-desc">
      Setiap rangkaian adalah cerita — dibuat dengan cinta dari bunga segar pilihan untuk momen paling berharga Anda.
    </p>

    <div class="gal-count-badge">
      <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="3" width="18" height="18" rx="2"/><path d="M3 9h18M9 21V9"/></svg>
      <?= count($items) ?> Karya dalam Galeri
    </div>

    <div class="gal-scroll-hint" onclick="document.getElementById('galeri').scrollIntoView({behavior:'smooth'})">
      <span>Lihat Galeri</span>
      <div class="gal-scroll-line"></div>
    </div>
  </div>

  <!-- Wave -->
  <div class="gal-hero-wave">
    <svg viewBox="0 0 1440 56" preserveAspectRatio="none" style="width:100%;height:56px;display:block;">
      <path d="M0,28 C360,56 720,0 1080,28 C1260,42 1380,14 1440,28 L1440,56 L0,56 Z" fill="#fdf6ee"/>
    </svg>
  </div>
</section>

<!-- ==============================
     MASONRY GALLERY
     ============================== -->
<div class="gal-body" id="galeri">

  <?php if (!empty($galleryItems)): ?>
  <div class="gal-masonry" id="gal-masonry">
    <?php foreach ($galleryItems as $idx => $item):
      $waMsg = urlencode('Halo Chika Florist, saya tertarik dengan ' . ($item['title'] ?: 'karangan bunga') . ' yang ada di galeri. Boleh info lebih lanjut?');
      $waLink = 'https://wa.me/' . preg_replace('/[^0-9]/', '', getSetting('whatsapp_number','6281234567890')) . '?text=' . $waMsg;
    ?>
    <div class="gal-item"
         data-idx="<?= $idx ?>"
         data-img="<?= UPLOAD_URL . $item['image'] ?>"
         data-title="<?= htmlspecialchars($item['title'] ?? '', ENT_QUOTES) ?>"
         data-cat="<?= htmlspecialchars($item['category'] ?? '', ENT_QUOTES) ?>"
         data-wa="<?= $waLink ?>"
         onclick="openLightbox(<?= $idx ?>)">

    

      <!-- Zoom icon -->
      <div class="gal-zoom-icon">
        <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
          <path d="M15 3h6m0 0v6m0-6l-7 7M9 21H3m0 0v-6m0 6l7-7"/>
        </svg>
      </div>

      <!-- Image -->
      <img src="<?= UPLOAD_URL . $item['image'] ?>"
           alt="<?= clean($item['alt_text'] ?: $item['title'] ?: 'Galeri Chika Florist') ?>"
           loading="lazy"
           onerror="this.parentElement.style.display='none'">

      <!-- Hover overlay -->
      <div class="gal-overlay">
        <?php if (!empty($item['title'])): ?>
        <div class="gal-overlay-title"><?= clean($item['title']) ?></div>
        <?php endif; ?>
        <a href="<?= $waLink ?>" target="_blank" class="gal-overlay-btn" onclick="event.stopPropagation()">
          <svg width="12" height="12" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z"/><path d="M12 0C5.373 0 0 5.373 0 12c0 2.096.541 4.063 1.491 5.776L.057 23.04l5.43-1.424A11.947 11.947 0 0012 24c6.627 0 12-5.373 12-12S18.627 0 12 0zm0 21.818a9.818 9.818 0 01-5.015-1.371l-.36-.214-3.726.977.995-3.633-.235-.374A9.818 9.818 0 1112 21.818z"/></svg>
          Pesan Serupa
        </a>
      </div>
    </div>
    <?php endforeach; ?>
  </div>

  <!-- CTA bawah masonry -->
  <div style="text-align:center;margin-top:2.5rem;padding:0 1rem;">
    <p style="font-family:'Jost',sans-serif;font-size:.85rem;color:#9ca3af;margin-bottom:1rem;">Tertarik dengan karya kami? Hubungi kami untuk memesan.</p>
    <a href="<?= waLink('Halo Chika Florist, saya melihat galeri dan ingin memesan bunga. Boleh info produk dan harga?') ?>" target="_blank"
       style="display:inline-flex;align-items:center;gap:.5rem;font-family:'Jost',sans-serif;font-size:.85rem;font-weight:700;color:#fff;background:#16a34a;padding:.8rem 1.8rem;border-radius:999px;text-decoration:none;box-shadow:0 4px 16px rgba(22,163,74,.3);transition:all .25s;">
      <svg width="16" height="16" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z"/><path d="M12 0C5.373 0 0 5.373 0 12c0 2.096.541 4.063 1.491 5.776L.057 23.04l5.43-1.424A11.947 11.947 0 0012 24c6.627 0 12-5.373 12-12S18.627 0 12 0zm0 21.818a9.818 9.818 0 01-5.015-1.371l-.36-.214-3.726.977.995-3.633-.235-.374A9.818 9.818 0 1112 21.818z"/></svg>
      Pesan Bunga Sekarang
    </a>
  </div>

  <?php else: ?>
  <div class="gal-empty">
    <svg width="64" height="64" fill="none" stroke="#d1d5db" stroke-width="1.5" viewBox="0 0 24 24" style="margin:0 auto 1rem;display:block;">
      <rect x="3" y="3" width="18" height="18" rx="2"/><path d="M3 9h18M9 21V9"/>
    </svg>
    <p style="font-family:'Jost',sans-serif;font-size:.9rem;">Belum ada foto di galeri.</p>
  </div>
  <?php endif; ?>

</div>

<!-- CTA BOTTOM -->
<section class="gal-cta py-12 px-4 text-center">
  <div class="gal-cta-pat"></div>
  <div style="position:absolute;width:300px;height:300px;border-radius:50%;background:rgba(244,63,94,.18);filter:blur(80px);top:-80px;left:50%;transform:translateX(-50%);pointer-events:none;"></div>
  <div style="position:relative;z-index:2;max-width:480px;margin:0 auto;">
    <svg width="32" height="32" viewBox="0 0 80 80" fill="none" style="display:inline-block;margin-bottom:.75rem;opacity:.7;">
      <?php foreach([0,60,120,180,240,300] as $r): ?>
      <ellipse cx="40" cy="22" rx="8" ry="18" fill="#fda4af" transform="rotate(<?=$r?> 40 40)"/>
      <?php endforeach; ?>
      <circle cx="40" cy="40" r="10" fill="#f9c784"/>
    </svg>
    <h2 style="font-family:'Cormorant Garamond',serif;font-size:1.8rem;font-weight:700;color:#fff;margin-bottom:.5rem;">
      Wujudkan <em style="font-style:italic;color:#f9c784;">Momen Spesial</em> Anda
    </h2>
    <p style="font-family:'Jost',sans-serif;font-size:.86rem;color:rgba(255,255,255,.7);margin-bottom:1.5rem;line-height:1.65;">
      Konsultasikan kebutuhan bunga Anda — kami siap membantu 24 jam.
    </p>
    <a href="<?= waLink('Halo Chika Florist, saya melihat galeri dan ingin konsultasi pemesanan bunga.') ?>" target="_blank"
       style="display:inline-flex;align-items:center;gap:.6rem;font-family:'Jost',sans-serif;font-size:.88rem;font-weight:700;background:#fff;color:#e11d48;padding:.85rem 2rem;border-radius:999px;text-decoration:none;box-shadow:0 4px 20px rgba(0,0,0,.2);">
      <svg width="16" height="16" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z"/><path d="M12 0C5.373 0 0 5.373 0 12c0 2.096.541 4.063 1.491 5.776L.057 23.04l5.43-1.424A11.947 11.947 0 0012 24c6.627 0 12-5.373 12-12S18.627 0 12 0zm0 21.818a9.818 9.818 0 01-5.015-1.371l-.36-.214-3.726.977.995-3.633-.235-.374A9.818 9.818 0 1112 21.818z"/></svg>
      Konsultasi via WhatsApp
    </a>
  </div>
</section>

<!-- ==============================
     LIGHTBOX
     ============================== -->
<div class="lb-backdrop" id="lb-backdrop" onclick="closeLightboxOnBackdrop(event)">
  <button class="lb-prev" onclick="lbNav(-1)">
    <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M15 18l-6-6 6-6"/></svg>
  </button>
  <button class="lb-next" onclick="lbNav(1)">
    <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M9 18l6-6-6-6"/></svg>
  </button>

  <div class="lb-inner" id="lb-inner">
    <div class="lb-img-wrap">
      <button class="lb-close" onclick="closeLightbox()">
        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M18 6L6 18M6 6l12 12"/></svg>
      </button>
      <img id="lb-img" src="" alt="">
    </div>
    <div class="lb-info">
      <div style="display:flex;align-items:center;gap:.6rem;flex:1;min-width:0;">
        <span class="lb-title" id="lb-title"></span>
        <span class="lb-cat" id="lb-cat" style="display:none;"></span>
      </div>
      <a id="lb-wa" href="#" target="_blank" class="lb-wa-btn">
        <svg width="13" height="13" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z"/><path d="M12 0C5.373 0 0 5.373 0 12c0 2.096.541 4.063 1.491 5.776L.057 23.04l5.43-1.424A11.947 11.947 0 0012 24c6.627 0 12-5.373 12-12S18.627 0 12 0zm0 21.818a9.818 9.818 0 01-5.015-1.371l-.36-.214-3.726.977.995-3.633-.235-.374A9.818 9.818 0 1112 21.818z"/></svg>
        Pesan Serupa
      </a>
    </div>
    <div class="lb-counter" id="lb-counter"></div>
  </div>
</div>

<!-- ==============================
     JAVASCRIPT
     ============================== -->
<script>
// ---- DATA dari PHP ----
var galData = <?= json_encode(array_map(function($item) use ($items) {
  $waMsg = urlencode('Halo Chika Florist, saya tertarik dengan ' . ($item['title'] ?: 'karangan bunga') . ' yang ada di galeri. Boleh info lebih lanjut?');
  $waNum = preg_replace('/[^0-9]/', '', getSetting('whatsapp_number','6281234567890'));
  return [
    'img'   => UPLOAD_URL . $item['image'],
    'title' => $item['title'] ?? '',
    'cat'   => $item['category'] ?? '',
    'wa'    => 'https://wa.me/' . $waNum . '?text=' . $waMsg,
  ];
}, $items)) ?>;

var lbCurrent = 0;
var lbTotal   = galData.length;

// ---- LIGHTBOX ----
function openLightbox(idx) {
  lbCurrent = idx;
  renderLb();
  document.getElementById('lb-backdrop').classList.add('open');
  document.body.style.overflow = 'hidden';
}
function closeLightbox() {
  document.getElementById('lb-backdrop').classList.remove('open');
  document.body.style.overflow = '';
}
function closeLightboxOnBackdrop(e) {
  if (e.target === document.getElementById('lb-backdrop')) closeLightbox();
}
function lbNav(dir) {
  lbCurrent = (lbCurrent + dir + lbTotal) % lbTotal;
  renderLb();
}
function renderLb() {
  var d = galData[lbCurrent];
  var img = document.getElementById('lb-img');
  img.style.opacity = '0';
  img.src = d.img;
  img.alt = d.title || 'Galeri Chika Florist';
  img.onload = function(){ img.style.transition='opacity .25s'; img.style.opacity='1'; };

  var titleEl = document.getElementById('lb-title');
  titleEl.textContent = d.title || '';
  titleEl.style.display = d.title ? '' : 'none';

  var catEl = document.getElementById('lb-cat');
  catEl.textContent = d.cat || '';
  catEl.style.display = d.cat ? '' : 'none';

  document.getElementById('lb-wa').href = d.wa;
  document.getElementById('lb-counter').textContent = (lbCurrent + 1) + ' / ' + lbTotal;
}

// Keyboard nav
document.addEventListener('keydown', function(e) {
  var bd = document.getElementById('lb-backdrop');
  if (!bd.classList.contains('open')) return;
  if (e.key === 'ArrowRight') lbNav(1);
  if (e.key === 'ArrowLeft')  lbNav(-1);
  if (e.key === 'Escape')     closeLightbox();
});

// Touch swipe lightbox
(function(){
  var bd = document.getElementById('lb-backdrop');
  var startX = 0;
  bd.addEventListener('touchstart', function(e){ startX = e.touches[0].clientX; }, {passive:true});
  bd.addEventListener('touchend', function(e){
    var diff = startX - e.changedTouches[0].clientX;
    if (Math.abs(diff) > 50) lbNav(diff > 0 ? 1 : -1);
  });
})();

// ---- LAZY LOAD ----
(function(){
  var items = document.querySelectorAll('.gal-item');
  if (!items.length) return;

  var obs = new IntersectionObserver(function(entries){
    entries.forEach(function(entry, i){
      if (entry.isIntersecting) {
        var el = entry.target;
        var delay = (parseInt(el.dataset.idx) % 4) * 80;
        setTimeout(function(){ el.classList.add('visible'); }, delay);
        obs.unobserve(el);
      }
    });
  }, { threshold: 0.1, rootMargin: '0px 0px -40px 0px' });

  items.forEach(function(el){ obs.observe(el); });
})();
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>