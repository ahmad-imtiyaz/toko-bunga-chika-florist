<?php
require_once __DIR__ . '/../includes/config.php';
$pdo = getDB();

// Ambil semua kategori aktif
$categories = $pdo->query("
    SELECT c.*, COUNT(p.id) as product_count
    FROM categories c
    LEFT JOIN products p ON p.category_id = c.id AND p.is_active = 1
    WHERE c.is_active = 1
    GROUP BY c.id
    ORDER BY c.parent_id ASC, c.sort_order ASC, c.name ASC
")->fetchAll();

// Filter kategori aktif
$filterCat = $_GET['kategori'] ?? '';

// Ambil semua produk
$sql = "SELECT p.*, c.name as cat_name, c.slug as cat_slug
        FROM products p
        JOIN categories c ON p.category_id = c.id
        WHERE p.is_active = 1";
$params = [];
if ($filterCat) {
    $sql .= " AND c.slug = ?";
    $params[] = $filterCat;
}
$sql .= " ORDER BY p.is_featured DESC, p.sort_order ASC, p.name ASC";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$products = $stmt->fetchAll();

$totalProducts = count($products);

$page_title    = 'Semua Produk | Toko Bunga Online 24 Jam – Chika Florist';
$meta_desc     = 'Jelajahi koleksi lengkap bunga Chika Florist. Buket, standing flower, bunga papan, dan berbagai produk bunga segar berkualitas.';
$canonical_url = BASE_URL . '/produk';
$breadcrumbs   = [['label'=>'Beranda','url'=>'/'],['label'=>'Semua Produk']];
require_once __DIR__ . '/../includes/header.php';
?>

<style>
@import url('https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,400;0,600;0,700;1,400;1,600;1,700&family=Jost:wght@300;400;500;600;700&display=swap');

/* ==============================
   HERO
   ============================== */
.katalog-hero {
  position: relative;
  padding: 4rem 1.5rem 5.5rem;
  overflow: hidden;
  background: linear-gradient(150deg, #fdf6ee 0%, #fdf0f3 50%, #f0f4ed 100%);
  text-align: center;
}

/* Trellis */
.katalog-hero::before {
  content: '';
  position: absolute; inset: 0;
  opacity: .03;
  background-image:
    repeating-linear-gradient(45deg,#7a1f2e 0,#7a1f2e 1px,transparent 0,transparent 50%),
    repeating-linear-gradient(-45deg,#7a1f2e 0,#7a1f2e 1px,transparent 0,transparent 50%);
  background-size: 28px 28px;
  pointer-events: none;
}

/* Glow blobs */
.katalog-glow {
  position: absolute; border-radius: 50%; filter: blur(80px); pointer-events: none;
}

/* SVG ornamen bunga */
.katalog-flower-ornament {
  position: absolute; pointer-events: none;
  animation: kFlowerFloat 5s ease-in-out infinite;
}
@keyframes kFlowerFloat {
  0%,100% { transform: translateY(0) rotate(0deg); }
  50%      { transform: translateY(-10px) rotate(4deg); }
}

.katalog-eyebrow {
  display: inline-flex; align-items: center; gap: .5rem;
  font-family: 'Jost', sans-serif; font-size: .7rem; font-weight: 700;
  letter-spacing: .14em; text-transform: uppercase;
  color: #c0485a;
  margin-bottom: 1rem;
  position: relative; z-index: 2;
  animation: kUp .7s ease both;
}
.katalog-eyebrow::before,.katalog-eyebrow::after {
  content:''; width:32px; height:1px; background:#c0485a; opacity:.35;
}

.katalog-hero h1 {
  font-family: 'Cormorant Garamond', serif;
  font-size: clamp(2rem, 5vw, 3.2rem);
  font-weight: 700; color: #2a1018; line-height: 1.15;
  margin-bottom: .6rem;
  position: relative; z-index: 2;
  animation: kUp .7s .1s ease both;
}
.katalog-hero h1 em { font-style: italic; color: #c0485a; }

.katalog-hero-divider {
  width: 60px; height: 2px;
  background: linear-gradient(90deg, transparent, #c9a84c, transparent);
  margin: .8rem auto 1rem;
  position: relative; z-index: 2;
  animation: kUp .7s .15s ease both;
}

.katalog-hero-desc {
  font-family: 'Jost', sans-serif; font-size: .88rem; color: #9a7070;
  line-height: 1.7; max-width: 480px; margin: 0 auto 0;
  position: relative; z-index: 2;
  animation: kUp .7s .2s ease both;
}

@keyframes kUp {
  from { opacity:0; transform:translateY(20px); }
  to   { opacity:1; transform:translateY(0); }
}

/* Wave */
.katalog-wave {
  position: absolute; bottom:-1px; left:0; right:0; z-index:3; line-height:0;
}

/* ==============================
   BODY
   ============================== */
.katalog-body {
  background: linear-gradient(170deg, #fdf6ee 0%, #fff 50%, #fdf0f3 100%);
  padding: 2.5rem 0 4rem;
}

/* ==============================
   FILTER BAR
   ============================== */
.katalog-filter-wrap {
  max-width: 1100px; margin: 0 auto 2rem;
  padding: 0 1.5rem;
}

.katalog-filter-label {
  font-family: 'Jost', sans-serif; font-size: .68rem; font-weight: 700;
  letter-spacing: .1em; text-transform: uppercase; color: #9ca3af;
  margin-bottom: .65rem;
  display: flex; align-items: center; gap: .5rem;
}
.katalog-filter-label::after {
  content:''; flex:1; height:1px;
  background: linear-gradient(90deg,#fde8b4,transparent);
}

.katalog-filter-scroll {
  display: flex; gap: .5rem; flex-wrap: wrap;
}

.kf-chip {
  display: inline-flex; align-items: center; gap: .4rem;
  font-family: 'Jost', sans-serif; font-size: .75rem; font-weight: 600;
  padding: .45rem 1rem; border-radius: 999px;
  border: 1.5px solid #fde8b4; background: #fff;
  color: #6b7280; text-decoration: none;
  transition: all .2s; white-space: nowrap; cursor: pointer;
}
.kf-chip:hover { border-color: #fca5a5; color: #e11d48; background: #fff5f5; }
.kf-chip.active {
  background: #e11d48; border-color: #e11d48; color: #fff;
  box-shadow: 0 3px 12px rgba(225,29,72,.25);
}
.kf-chip-count {
  font-size: .65rem; font-weight: 700;
  background: rgba(255,255,255,.25); border-radius: 999px;
  padding: .05rem .4rem;
}
.kf-chip.active .kf-chip-count { background: rgba(255,255,255,.3); }
.kf-chip:not(.active) .kf-chip-count { background: #f3f4f6; color: #9ca3af; }

/* ==============================
   RESULT INFO
   ============================== */
.katalog-result-info {
  max-width: 1100px; margin: 0 auto 1.2rem;
  padding: 0 1.5rem;
  display: flex; align-items: center; justify-content: space-between; gap: 1rem;
  flex-wrap: wrap;
}
.katalog-result-count {
  font-family: 'Jost', sans-serif; font-size: .8rem; color: #9ca3af;
}
.katalog-result-count strong {
  font-weight: 700; color: #1f2937;
}

/* ==============================
   PRODUCT GRID
   ============================== */
.katalog-grid {
  max-width: 1100px; margin: 0 auto;
  padding: 0 1.5rem;
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  gap: 1.1rem;
}
@media(min-width:540px)  { .katalog-grid { grid-template-columns: repeat(3,1fr); } }
@media(min-width:900px)  { .katalog-grid { grid-template-columns: repeat(4,1fr); } }
@media(min-width:1100px) { .katalog-grid { grid-template-columns: repeat(5,1fr); } }

/* ==============================
   PRODUCT CARD
   ============================== */
.k-card {
  background: #fff;
  border: 1px solid #fde8b4;
  border-radius: 1.1rem;
  overflow: hidden;
  text-decoration: none;
  display: flex; flex-direction: column;
  transition: all .32s cubic-bezier(.22,.68,0,1.2);
  position: relative;
  animation: kCardIn .5s ease both;
}
.k-card:hover {
  transform: translateY(-6px);
  box-shadow: 0 16px 40px rgba(122,31,46,.12);
  border-color: #fca5a5;
}

@keyframes kCardIn {
  from { opacity:0; transform:translateY(20px); }
  to   { opacity:1; transform:translateY(0); }
}
.k-card:nth-child(1)  { animation-delay:.04s }
.k-card:nth-child(2)  { animation-delay:.08s }
.k-card:nth-child(3)  { animation-delay:.12s }
.k-card:nth-child(4)  { animation-delay:.16s }
.k-card:nth-child(5)  { animation-delay:.20s }
.k-card:nth-child(6)  { animation-delay:.24s }
.k-card:nth-child(7)  { animation-delay:.28s }
.k-card:nth-child(8)  { animation-delay:.32s }
.k-card:nth-child(n+9){ animation-delay:.36s }

/* Badges */
.k-card-badge-featured {
  position:absolute; top:8px; left:8px; z-index:5;
  font-family:'Jost',sans-serif; font-size:.58rem; font-weight:700;
  letter-spacing:.06em; text-transform:uppercase;
  color:#78350f;
  background: linear-gradient(135deg,#fef3c7,#fde68a);
  border:1px solid #fcd34d; padding:.2rem .55rem; border-radius:999px;
  box-shadow:0 2px 6px rgba(245,158,11,.2);
}

.k-card-badge-cat {
  position:absolute; top:8px; right:0; z-index:5;
  font-family:'Jost',sans-serif; font-size:.56rem; font-weight:600;
  letter-spacing:.04em; text-transform:uppercase;
  color:#fff; background:rgba(192,72,90,.88);
  padding:.18rem .55rem .18rem .45rem;
  border-radius:4px 0 0 4px;
}

/* Image */
.k-card-img {
  height: 165px; overflow:hidden;
  background: linear-gradient(135deg,#fdf0f3,#fdf6ee);
  position: relative;
}
.k-card-img img {
  width:100%; height:100%; object-fit:cover;
  transition: transform .4s cubic-bezier(.22,.68,0,1.2);
}
.k-card:hover .k-card-img img { transform: scale(1.08); }

/* Hover overlay */
.k-card-overlay {
  position:absolute; inset:0;
  background: linear-gradient(180deg, transparent 40%, rgba(42,16,24,.6) 100%);
  opacity:0; transition:opacity .3s;
  display:flex; align-items:flex-end; justify-content:center; padding-bottom:10px;
}
.k-card:hover .k-card-overlay { opacity:1; }
.k-card-overlay-btn {
  font-family:'Jost',sans-serif; font-size:.7rem; font-weight:700;
  color:#fff; background:rgba(225,29,72,.9);
  border:1px solid rgba(255,255,255,.3);
  padding:.35rem .9rem; border-radius:999px;
  transform:translateY(6px); transition:transform .25s;
  white-space:nowrap;
}
.k-card:hover .k-card-overlay-btn { transform:translateY(0); }

/* Card body */
.k-card-body { padding:.75rem .85rem .9rem; flex:1; display:flex; flex-direction:column; gap:.2rem; }
.k-card-cat {
  font-family:'Jost',sans-serif; font-size:.6rem; font-weight:700;
  letter-spacing:.07em; text-transform:uppercase; color:#c9a84c;
}
.k-card-name {
  font-family:'Cormorant Garamond',serif;
  font-size:.98rem; font-weight:700; color:#2a1018; line-height:1.3;
  flex:1;
  display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;
  transition:color .2s;
}
.k-card:hover .k-card-name { color:#c0485a; }
.k-card-price {
  font-family:'Cormorant Garamond',serif;
  font-size:1rem; font-weight:700; color:#c0485a; font-style:italic;
  margin-top:.1rem;
}
.k-card-wa {
  display:flex; align-items:center; justify-content:center; gap:.35rem;
  font-family:'Jost',sans-serif; font-size:.7rem; font-weight:700;
  color:#15803d; background:rgba(22,163,74,.06);
  border:1px solid rgba(22,163,74,.2); border-radius:999px;
  padding:.4rem .7rem; margin-top:.5rem;
  text-decoration:none; transition:all .2s;
}
.k-card-wa:hover { background:#16a34a; color:#fff; border-color:#16a34a; }

/* ==============================
   EMPTY STATE
   ============================== */
.katalog-empty {
  max-width:400px; margin:3rem auto; text-align:center;
  padding:2.5rem 1.5rem;
  background:#fff; border:1px solid #fde8b4; border-radius:1.25rem;
}
.katalog-empty-icon { font-size:3rem; margin-bottom:.75rem; }
.katalog-empty h3 {
  font-family:'Cormorant Garamond',serif;
  font-size:1.4rem; font-weight:700; color:#2a1018; margin-bottom:.4rem;
}
.katalog-empty p {
  font-family:'Jost',sans-serif; font-size:.82rem; color:#9ca3af; line-height:1.6;
}

/* ==============================
   CTA
   ============================== */
.katalog-cta {
  background: linear-gradient(135deg,#881337 0%,#9f1239 50%,#7f1d1d 100%);
  position:relative; overflow:hidden;
}
.katalog-cta-pat {
  position:absolute; inset:0;
  background-image:
    repeating-linear-gradient(45deg,rgba(255,255,255,.025) 0,rgba(255,255,255,.025) 1px,transparent 0,transparent 50%),
    repeating-linear-gradient(-45deg,rgba(255,255,255,.025) 0,rgba(255,255,255,.025) 1px,transparent 0,transparent 50%);
  background-size:28px 28px;
}
</style>

<!-- ==============================
     HERO
     ============================== -->
<section class="katalog-hero">

  <!-- Glow blobs -->
  <div class="katalog-glow" style="width:400px;height:400px;background:rgba(192,72,90,.08);top:-100px;left:-80px;"></div>
  <div class="katalog-glow" style="width:350px;height:350px;background:rgba(201,168,76,.07);bottom:-60px;right:-60px;"></div>
  <div class="katalog-glow" style="width:280px;height:280px;background:rgba(125,155,118,.06);top:30%;left:50%;transform:translateX(-50%);"></div>

  <!-- Ornamen bunga kiri -->
  <svg class="katalog-flower-ornament" style="top:-10px;left:-5px;width:160px;opacity:.1;animation-delay:0s;" viewBox="0 0 160 200" fill="none">
    <?php foreach([0,60,120,180,240,300] as $r): ?>
    <ellipse cx="80" cy="32" rx="13" ry="28" fill="#c0485a" transform="rotate(<?=$r?> 80 75)"/>
    <?php endforeach; ?>
    <circle cx="80" cy="75" r="16" fill="#f9c784"/>
    <line x1="80" y1="95" x2="80" y2="200" stroke="#6b7c5c" stroke-width="2.5"/>
    <ellipse cx="58" cy="145" rx="22" ry="11" fill="#6b7c5c" opacity=".6" transform="rotate(-30 58 145)"/>
    <ellipse cx="102" cy="168" rx="20" ry="10" fill="#6b7c5c" opacity=".6" transform="rotate(25 102 168)"/>
  </svg>

  <!-- Ornamen bunga kanan -->
  <svg class="katalog-flower-ornament" style="top:-5px;right:-8px;width:140px;opacity:.09;animation-delay:1.5s;transform:scaleX(-1);" viewBox="0 0 160 200" fill="none">
    <?php foreach([0,72,144,216,288] as $r): ?>
    <ellipse cx="80" cy="30" rx="11" ry="25" fill="#c9a84c" transform="rotate(<?=$r?> 80 72)"/>
    <?php endforeach; ?>
    <circle cx="80" cy="72" r="14" fill="#fda4af"/>
    <line x1="80" y1="90" x2="80" y2="200" stroke="#6b7c5c" stroke-width="2"/>
    <ellipse cx="60" cy="140" rx="18" ry="9" fill="#6b7c5c" opacity=".5" transform="rotate(-25 60 140)"/>
  </svg>

  <div class="katalog-eyebrow">✿ Koleksi Lengkap ✿</div>

  <h1>Katalog <em>Bunga</em> Chika Florist</h1>

  <div class="katalog-hero-divider"></div>

  <p class="katalog-hero-desc">
    <?= $totalProducts ?>+ produk bunga segar — buket, standing flower, bunga papan & lebih banyak lagi.
    Dikirim 24 jam ke seluruh Indonesia.
  </p>

  <!-- Wave bottom -->
  <div class="katalog-wave">
    <svg viewBox="0 0 1440 52" preserveAspectRatio="none" style="width:100%;height:52px;display:block;">
      <path d="M0,18 C360,52 720,0 1080,26 C1260,40 1380,12 1440,20 L1440,52 L0,52 Z" fill="#fdf6ee"/>
    </svg>
  </div>
</section>

<!-- ==============================
     BODY
     ============================== -->
<div class="katalog-body">

  <!-- Filter -->
  <div class="katalog-filter-wrap">
    <div class="katalog-filter-label">
      <svg width="11" height="11" fill="none" stroke="#c9a84c" stroke-width="2" viewBox="0 0 24 24"><path d="M3 6h18M7 12h10M11 18h2"/></svg>
      Filter Kategori
    </div>
    <div class="katalog-filter-scroll">
      <!-- Semua -->
      <a href="<?= BASE_URL ?>/produk" class="kf-chip <?= !$filterCat ? 'active' : '' ?>">
        Semua Produk
        <span class="kf-chip-count"><?= $pdo->query("SELECT COUNT(*) FROM products WHERE is_active=1")->fetchColumn() ?></span>
      </a>
      <?php foreach ($categories as $cat): ?>
      <?php if ($cat['product_count'] < 1) continue; ?>
      <a href="<?= BASE_URL ?>/produk?kategori=<?= $cat['slug'] ?>"
         class="kf-chip <?= $filterCat === $cat['slug'] ? 'active' : '' ?>">
        <?= clean($cat['name']) ?>
        <span class="kf-chip-count"><?= $cat['product_count'] ?></span>
      </a>
      <?php endforeach; ?>
    </div>
  </div>

  <!-- Result info -->
  <div class="katalog-result-info">
    <p class="katalog-result-count">
      Menampilkan <strong><?= $totalProducts ?></strong> produk
      <?php if ($filterCat): ?>
      — kategori <strong><?= clean(array_values(array_filter($categories, fn($c)=>$c['slug']===$filterCat))[0]['name'] ?? $filterCat) ?></strong>
      <?php endif; ?>
    </p>
    <?php if ($filterCat): ?>
    <a href="<?= BASE_URL ?>/produk" style="font-family:'Jost',sans-serif;font-size:.75rem;color:#e11d48;text-decoration:none;font-weight:600;">
      × Hapus filter
    </a>
    <?php endif; ?>
  </div>

  <!-- Grid produk -->
  <?php if (!empty($products)): ?>
  <div class="katalog-grid" id="katalog-grid">
    <?php foreach ($products as $prod): ?>
    <a href="<?= BASE_URL ?>/produk/<?= $prod['slug'] ?>" class="k-card">

      <?php if ($prod['is_featured']): ?>
      <div class="k-card-badge-featured">✦ Terlaris</div>
      <?php endif; ?>
      <div class="k-card-badge-cat"><?= clean($prod['cat_name']) ?></div>

      <div class="k-card-img">
        <img src="<?= UPLOAD_URL . ($prod['image'] ?? '') ?>"
             alt="<?= clean($prod['name']) ?> – Chika Florist"
             loading="lazy"
             onerror="this.style.display='none'">
        <div class="k-card-overlay">
          <div class="k-card-overlay-btn">Lihat Detail →</div>
        </div>
      </div>

      <div class="k-card-body">
        <span class="k-card-cat"><?= clean($prod['cat_name']) ?></span>
        <span class="k-card-name"><?= clean($prod['name']) ?></span>
        <span class="k-card-price"><?= formatHarga($prod['price_min'], $prod['price_max']) ?></span>
        <a href="<?= waLink('Halo Chika Florist, saya ingin pesan ' . $prod['name']) ?>"
           target="_blank"
           class="k-card-wa"
           onclick="event.stopPropagation()">
          <svg width="11" height="11" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z"/><path d="M12 0C5.373 0 0 5.373 0 12c0 2.096.541 4.063 1.491 5.776L.057 23.04l5.43-1.424A11.947 11.947 0 0012 24c6.627 0 12-5.373 12-12S18.627 0 12 0zm0 21.818a9.818 9.818 0 01-5.015-1.371l-.36-.214-3.726.977.995-3.633-.235-.374A9.818 9.818 0 1112 21.818z"/></svg>
          Pesan Cepat
        </a>
      </div>
    </a>
    <?php endforeach; ?>
  </div>

  <?php else: ?>
  <div class="katalog-empty">
    <div class="katalog-empty-icon">🌸</div>
    <h3>Produk Tidak Ditemukan</h3>
    <p>Belum ada produk di kategori ini.<br>Coba pilih kategori lain atau lihat semua produk.</p>
    <a href="<?= BASE_URL ?>/produk" style="display:inline-block;margin-top:1rem;font-family:'Jost',sans-serif;font-size:.8rem;font-weight:700;color:#e11d48;text-decoration:underline;">
      Lihat Semua Produk
    </a>
  </div>
  <?php endif; ?>

</div>

<!-- CTA -->
<section class="katalog-cta py-12 px-4 text-center">
  <div class="katalog-cta-pat"></div>
  <div style="position:absolute;width:300px;height:300px;border-radius:50%;background:rgba(244,63,94,.18);filter:blur(80px);top:-80px;left:50%;transform:translateX(-50%);pointer-events:none;"></div>
  <div style="position:relative;z-index:2;max-width:460px;margin:0 auto;">
    <svg width="28" height="28" viewBox="0 0 80 80" fill="none" style="display:inline-block;margin-bottom:.75rem;opacity:.7;">
      <?php foreach([0,60,120,180,240,300] as $r): ?>
      <ellipse cx="40" cy="22" rx="8" ry="18" fill="#fda4af" transform="rotate(<?=$r?> 40 40)"/>
      <?php endforeach; ?>
      <circle cx="40" cy="40" r="10" fill="#f9c784"/>
    </svg>
    <h2 style="font-family:'Cormorant Garamond',serif;font-size:1.8rem;font-weight:700;color:#fff;margin-bottom:.5rem;">
      Tidak Menemukan yang <em style="font-style:italic;color:#f9c784;">Tepat?</em>
    </h2>
    <p style="font-family:'Jost',sans-serif;font-size:.86rem;color:rgba(255,255,255,.7);margin-bottom:1.5rem;line-height:1.65;">
      Konsultasikan kebutuhan bunga Anda — kami siap membantu membuat desain custom 24 jam.
    </p>
    <a href="<?= waLink('Halo Chika Florist, saya ingin konsultasi bunga custom') ?>" target="_blank"
       style="display:inline-flex;align-items:center;gap:.6rem;font-family:'Jost',sans-serif;font-size:.88rem;font-weight:700;background:#fff;color:#e11d48;padding:.85rem 2rem;border-radius:999px;text-decoration:none;box-shadow:0 4px 20px rgba(0,0,0,.2);">
      <svg width="16" height="16" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z"/><path d="M12 0C5.373 0 0 5.373 0 12c0 2.096.541 4.063 1.491 5.776L.057 23.04l5.43-1.424A11.947 11.947 0 0012 24c6.627 0 12-5.373 12-12S18.627 0 12 0zm0 21.818a9.818 9.818 0 01-5.015-1.371l-.36-.214-3.726.977.995-3.633-.235-.374A9.818 9.818 0 1112 21.818z"/></svg>
      Konsultasi via WhatsApp
    </a>
  </div>
</section>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>