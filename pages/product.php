<?php
require_once __DIR__ . '/../includes/config.php';

$slug = $_GET['slug'] ?? '';
$pdo  = getDB();

$stmt = $pdo->prepare("SELECT p.*, c.name as cat_name, c.slug as cat_slug, c.parent_id,
    (SELECT name FROM categories WHERE id=c.parent_id) as parent_cat_name,
    (SELECT slug FROM categories WHERE id=c.parent_id) as parent_cat_slug
    FROM products p JOIN categories c ON p.category_id=c.id
    WHERE p.slug=? AND p.is_active=1");
$stmt->execute([$slug]);
$product = $stmt->fetch();

if (!$product) {
    header("HTTP/1.0 404 Not Found");
    require_once __DIR__ . '/404.php';
    exit();
}

$related = $pdo->prepare("SELECT p.*, c.name as cat_name FROM products p JOIN categories c ON p.category_id=c.id
    WHERE p.category_id=? AND p.id!=? AND p.is_active=1 ORDER BY p.is_featured DESC LIMIT 8");
$related->execute([$product['category_id'], $product['id']]);
$related = $related->fetchAll();

$cities = getActiveCities(12);

$nama          = clean($product['name']);
$page_title    = $product['meta_title'] ?: "{$nama} 24 Jam | Florist Online – Chika Florist";
$meta_desc     = $product['meta_desc'] ?: "Pesan {$nama} online dengan layanan 24 jam. Tersedia berbagai ukuran dan desain, pengiriman cepat ke seluruh Indonesia.";
$canonical_url = BASE_URL . '/produk/' . $product['slug'];

$breadcrumbs = [['label'=>'Beranda','url'=>'/']];
if ($product['parent_id'] && $product['parent_cat_slug'])
    $breadcrumbs[] = ['label'=>$product['parent_cat_name'],'url'=>'/'.$product['parent_cat_slug']];
$breadcrumbs[] = ['label'=>$product['cat_name'],'url'=>'/'.$product['cat_slug']];
$breadcrumbs[] = ['label'=>$nama];

require_once __DIR__ . '/../includes/header.php';
?>

<style>
@import url('https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,400;0,600;0,700;1,400;1,600;1,700&family=Jost:wght@300;400;500;600;700&display=swap');

/* ==============================
   PAGE WRAPPER
   ============================== */
.prod-page {
  background: linear-gradient(170deg, #fdf6ee 0%, #fff 50%, #fdf0f3 100%);
  min-height: 80vh;
  padding: 2rem 0 4rem;
}

/* ==============================
   BREADCRUMB
   ============================== */
.prod-breadcrumb {
  display: flex; align-items: center; gap: .4rem; flex-wrap: wrap;
  margin-bottom: 1.5rem;
}
.prod-breadcrumb a, .prod-breadcrumb span {
  font-family: 'Jost', sans-serif; font-size: .7rem; font-weight: 600;
  letter-spacing: .07em; text-transform: uppercase;
  color: #9ca3af; text-decoration: none; transition: color .2s;
}
.prod-breadcrumb a:hover { color: #e11d48; }
.prod-breadcrumb .sep   { color: #e5e7eb; }
.prod-breadcrumb .cur   { color: #e11d48; }

/* ==============================
   MAIN LAYOUT — sticky left / scroll right
   ============================== */
.prod-layout {
  display: grid;
  grid-template-columns: 1fr;
  gap: 2rem;
  align-items: start;
}
@media(min-width: 768px) {
  .prod-layout {
    grid-template-columns: 1fr 1fr;
    gap: 3rem;
  }
}

/* ==============================
   PHOTO COLUMN — STICKY
   ============================== */
.prod-photo-col {
  position: relative;
}
@media(min-width: 768px) {
  .prod-photo-col {
    position: sticky;
    top: 5rem; /* bawah header */
  }
}

/* Linen texture background */
.prod-photo-stage {
  position: relative;
  padding: 2.5rem;
  border-radius: 2rem;
  overflow: visible;
  background:
    repeating-linear-gradient(
      0deg, rgba(201,168,76,.04) 0, rgba(201,168,76,.04) 1px, transparent 1px, transparent 28px
    ),
    repeating-linear-gradient(
      90deg, rgba(201,168,76,.04) 0, rgba(201,168,76,.04) 1px, transparent 1px, transparent 28px
    ),
    linear-gradient(145deg, #fdf6ee 0%, #fdf0f3 100%);
  border: 1px solid rgba(201,168,76,.2);
  box-shadow:
    0 1px 0 rgba(255,255,255,.9) inset,
    0 20px 60px rgba(201,168,76,.1),
    0 4px 20px rgba(214,90,110,.06);
}

/* Ornamen bunga TL */
.prod-flower-tl {
  position: absolute; top: -18px; left: -14px;
  z-index: 5; pointer-events: none;
  animation: flowerFloat 4s ease-in-out infinite;
}
/* Ornamen bunga BR */
.prod-flower-br {
  position: absolute; bottom: -12px; right: -12px;
  z-index: 5; pointer-events: none;
  animation: flowerFloat 4s 1.5s ease-in-out infinite;
}
/* Ornamen daun TR */
.prod-leaf-tr {
  position: absolute; top: 10px; right: -16px;
  z-index: 5; pointer-events: none;
  animation: flowerFloat 5s .8s ease-in-out infinite;
}
@keyframes flowerFloat {
  0%,100% { transform: translateY(0) rotate(0deg); }
  50%      { transform: translateY(-8px) rotate(3deg); }
}

/* Blob clip path */
.prod-blob-wrap {
  position: relative;
  width: 100%;
  aspect-ratio: 1 / 1.05;
  overflow: hidden;
}
.prod-blob-clip {
  width: 100%; height: 100%;
  clip-path: path('M 50,5 C 70,2 90,15 95,35 C 100,55 95,75 82,87 C 69,99 48,102 30,96 C 12,90 2,72 2,52 C 2,32 12,15 28,8 C 37,4 44,6 50,5 Z');
  overflow: hidden;
  background: linear-gradient(135deg, #fdf0f3, #fdf6ee);
  /* Scale clip to percentage */
  transform: scale(1);
}
/* Normalize clip path to percentage by using viewBox-like approach */
.prod-blob-img-inner {
  position: absolute;
  inset: 0;
  clip-path: ellipse(48% 50% at 50% 50%);
  /* fallback rounded if clip-path not full supported */
  border-radius: 60% 40% 55% 45% / 50% 45% 55% 50%;
  overflow: hidden;
  background: linear-gradient(135deg, #fdf0f3, #fdf6ee);
  transition: border-radius 8s ease;
  animation: blobMorph 8s ease-in-out infinite;
}
@keyframes blobMorph {
  0%,100% { border-radius: 60% 40% 55% 45% / 50% 45% 55% 50%; }
  25%      { border-radius: 45% 55% 40% 60% / 55% 40% 60% 45%; }
  50%      { border-radius: 55% 45% 60% 40% / 40% 60% 45% 55%; }
  75%      { border-radius: 40% 60% 45% 55% / 60% 45% 55% 40%; }
}
.prod-blob-img-inner img {
  width: 100%; height: 100%;
  object-fit: cover;
  transition: transform .5s ease;
}
.prod-photo-stage:hover .prod-blob-img-inner img { transform: scale(1.05); }

/* Fallback SVG kalau foto kosong */
.prod-blob-fallback {
  width: 100%; height: 100%;
  display: flex; align-items: center; justify-content: center;
  background: linear-gradient(135deg, #fdf0f3, #fdf6ee);
}

/* Featured badge */
.prod-featured-badge {
  position: absolute;
  top: 1rem; left: 1rem;
  z-index: 10;
  font-family: 'Jost', sans-serif;
  font-size: .65rem; font-weight: 700; letter-spacing: .06em; text-transform: uppercase;
  color: #78350f;
  background: linear-gradient(135deg, #fef3c7, #fde68a);
  border: 1px solid #fcd34d;
  padding: .25rem .7rem; border-radius: 999px;
  box-shadow: 0 2px 8px rgba(245,158,11,.25);
}

/* Gold frame ring */
.prod-ring {
  position: absolute; inset: 0;
  border-radius: 60% 40% 55% 45% / 50% 45% 55% 50%;
  border: 1.5px dashed rgba(201,168,76,.35);
  animation: ringMorph 8s ease-in-out infinite, ringRotate 20s linear infinite;
  pointer-events: none;
}
@keyframes ringMorph {
  0%,100% { border-radius: 60% 40% 55% 45% / 50% 45% 55% 50%; }
  25%      { border-radius: 45% 55% 40% 60% / 55% 40% 60% 45%; }
  50%      { border-radius: 55% 45% 60% 40% / 40% 60% 45% 55%; }
  75%      { border-radius: 40% 60% 45% 55% / 60% 45% 55% 40%; }
}
@keyframes ringRotate {
  from { transform: rotate(0deg); }
  to   { transform: rotate(360deg); }
}

/* ==============================
   INFO COLUMN
   ============================== */
.prod-info-col {
  display: flex; flex-direction: column; gap: 0;
}

/* Breadcrumb cat */
.prod-cat-path {
  display: flex; align-items: center; gap: .4rem;
  margin-bottom: .9rem; flex-wrap: wrap;
}
.prod-cat-path a {
  font-family: 'Jost', sans-serif; font-size: .68rem; font-weight: 700;
  letter-spacing: .07em; text-transform: uppercase;
  color: #e11d48; text-decoration: none;
  background: rgba(225,29,72,.07); border: 1px solid rgba(225,29,72,.15);
  padding: .18rem .6rem; border-radius: 999px;
  transition: all .2s;
}
.prod-cat-path a:hover { background: rgba(225,29,72,.12); }
.prod-cat-path .sep { color: #e5e7eb; font-size: .65rem; }

/* Product name */
.prod-name {
  font-family: 'Cormorant Garamond', serif;
  font-size: clamp(1.7rem, 4vw, 2.6rem);
  font-weight: 700; color: #1f2937; line-height: 1.15;
  margin-bottom: .5rem;
}

/* Gold underline */
.prod-name-underline {
  width: 52px; height: 2px;
  background: linear-gradient(90deg, #c9a84c, transparent);
  margin-bottom: 1rem;
}

/* Short desc */
.prod-short-desc {
  font-family: 'Jost', sans-serif; font-size: .88rem;
  color: #6b7280; line-height: 1.75;
  margin-bottom: 1.25rem;
}

/* Price box */
.prod-price-box {
  background: linear-gradient(135deg, #fdf0f3 0%, #fdf6ee 100%);
  border: 1px solid rgba(225,29,72,.15);
  border-radius: 1rem; padding: 1rem 1.25rem;
  margin-bottom: 1.4rem;
  display: inline-flex; flex-direction: column; gap: .15rem;
  position: relative; overflow: hidden;
}
.prod-price-box::before {
  content: '';
  position: absolute; top: 0; left: 0;
  width: 3px; height: 100%;
  background: linear-gradient(to bottom, #e11d48, #fda4af);
}
.prod-price-label {
  font-family: 'Jost', sans-serif; font-size: .65rem; font-weight: 700;
  letter-spacing: .1em; text-transform: uppercase; color: #9ca3af;
}
.prod-price-num {
  font-family: 'Cormorant Garamond', serif;
  font-size: 2rem; font-weight: 700; color: #e11d48; line-height: 1;
  font-style: italic;
}
.prod-price-note {
  font-family: 'Jost', sans-serif; font-size: .68rem; color: #9ca3af;
}

/* CTA Buttons */
.prod-btn-wa {
  display: flex; align-items: center; justify-content: center; gap: .55rem;
  font-family: 'Jost', sans-serif; font-size: .9rem; font-weight: 700;
  color: #fff; background: #16a34a;
  padding: .9rem 1.5rem; border-radius: 999px;
  text-decoration: none; transition: all .25s;
  box-shadow: 0 4px 16px rgba(22,163,74,.3);
  margin-bottom: .65rem;
}
.prod-btn-wa:hover { background: #15803d; transform: translateY(-2px); box-shadow: 0 6px 20px rgba(22,163,74,.35); }

.prod-btn-cat {
  display: flex; align-items: center; justify-content: center; gap: .45rem;
  font-family: 'Jost', sans-serif; font-size: .82rem; font-weight: 600;
  color: #92400e; background: rgba(254,243,199,.6);
  border: 1px solid #fde68a; padding: .75rem 1.5rem; border-radius: 999px;
  text-decoration: none; transition: all .2s;
  margin-bottom: 1.4rem;
}
.prod-btn-cat:hover { background: #fef3c7; border-color: #fcd34d; transform: translateY(-1px); }

/* Trust cards */
.prod-trust-grid {
  display: grid; grid-template-columns: repeat(2,1fr); gap: .5rem;
  margin-bottom: 1.4rem;
}
.prod-trust-card {
  display: flex; align-items: center; gap: .55rem;
  background: #fff; border: 1px solid #fde8b4;
  border-radius: .75rem; padding: .6rem .75rem;
  transition: all .2s;
}
.prod-trust-card:hover { border-color: #fca5a5; background: #fff5f5; transform: translateY(-1px); }
.prod-trust-icon {
  width: 32px; height: 32px; border-radius: .5rem;
  background: linear-gradient(135deg, #fdf0f3, #fdf6ee);
  border: 1px solid #fde8b4;
  display: flex; align-items: center; justify-content: center; flex-shrink: 0;
}
.prod-trust-icon svg { color: #e11d48; }
.prod-trust-text { display: flex; flex-direction: column; }
.prod-trust-title {
  font-family: 'Jost', sans-serif; font-size: .72rem; font-weight: 700; color: #1f2937;
}
.prod-trust-sub {
  font-family: 'Jost', sans-serif; font-size: .62rem; color: #9ca3af;
}

/* ==============================
   ACCORDION DESKRIPSI
   ============================== */
.prod-acc-wrap { display: flex; flex-direction: column; gap: .5rem; margin-top: 2rem; }
.prod-acc-item {
  background: #fff; border: 1px solid #fde8b4; border-radius: .875rem; overflow: hidden;
  transition: border-color .2s;
}
.prod-acc-item.open { border-color: #fca5a5; }
.prod-acc-head {
  display: flex; align-items: center; justify-content: space-between; gap: .75rem;
  padding: .9rem 1rem; cursor: pointer; user-select: none;
}
.prod-acc-head-left { display: flex; align-items: center; gap: .65rem; }
.prod-acc-icon-wrap {
  width: 30px; height: 30px; border-radius: .5rem;
  background: linear-gradient(135deg,#fdf0f3,#fdf6ee);
  border: 1px solid #fde8b4;
  display: flex; align-items: center; justify-content: center; flex-shrink: 0;
  transition: all .25s;
}
.prod-acc-item.open .prod-acc-icon-wrap { background: linear-gradient(135deg,#ffe4e6,#fdf0f3); border-color: #fca5a5; }
.prod-acc-icon-wrap svg { color: #e11d48; }
.prod-acc-title {
  font-family: 'Jost', sans-serif; font-size: .85rem; font-weight: 700; color: #1f2937;
}
.prod-acc-chevron {
  width: 26px; height: 26px; border-radius: 50%;
  background: #f9fafb; border: 1px solid #f3f4f6;
  display: flex; align-items: center; justify-content: center;
  flex-shrink: 0; transition: all .25s;
}
.prod-acc-item.open .prod-acc-chevron { background: #e11d48; border-color: #e11d48; transform: rotate(180deg); }
.prod-acc-chevron svg { color: #9ca3af; transition: color .25s; }
.prod-acc-item.open .prod-acc-chevron svg { color: #fff; }
.prod-acc-body { max-height: 0; overflow: hidden; transition: max-height .38s cubic-bezier(.4,0,.2,1); }
.prod-acc-body-inner {
  padding: 0 1rem 1rem 1rem;
  font-family: 'Jost', sans-serif; font-size: .82rem;
  color: #6b7280; line-height: 1.8;
  border-top: 1px solid #fef3c7;
  padding-top: .75rem;
}
.prod-acc-body-inner a { color: #e11d48; text-decoration: underline; }

/* ==============================
   CITY STRIP — VISUAL
   ============================== */
.prod-city-section { margin-top: 2.5rem; }
.prod-city-label {
  font-family: 'Jost', sans-serif; font-size: .68rem; font-weight: 700;
  letter-spacing: .1em; text-transform: uppercase; color: #9ca3af;
  margin-bottom: .75rem;
  display: flex; align-items: center; gap: .5rem;
}
.prod-city-label::after {
  content: ''; flex: 1; height: 1px;
  background: linear-gradient(90deg, #fde8b4, transparent);
}
.prod-city-grid {
  display: flex; flex-wrap: wrap; gap: .4rem;
}
.prod-city-chip {
  display: inline-flex; align-items: center; gap: .35rem;
  font-family: 'Jost', sans-serif; font-size: .72rem; font-weight: 500; color: #6b7280;
  background: #fff; border: 1px solid #fde8b4; border-radius: 999px;
  padding: .28rem .75rem; text-decoration: none; transition: all .2s;
}
.prod-city-chip:hover { color: #e11d48; border-color: #fca5a5; background: #fff5f5; transform: translateY(-1px); }
.prod-city-chip-dot { width: 5px; height: 5px; border-radius: 50%; background: #c9a84c; flex-shrink: 0; }

/* ==============================
   SEO PROSE
   ============================== */
.prod-seo {
  font-family: 'Jost', sans-serif; font-size: .84rem; color: #6b7280; line-height: 1.8;
  background: #fff; border: 1px solid #fde8b4; border-radius: 1rem;
  padding: 1.25rem 1.4rem; margin-top: 2rem;
}
.prod-seo h2 {
  font-family: 'Cormorant Garamond', serif; font-size: 1.15rem; font-weight: 700; color: #1f2937;
  margin: 1.1rem 0 .4rem; border-left: 3px solid #c9a84c; padding-left: .7rem;
}
.prod-seo h2:first-child { margin-top: 0; }
.prod-seo a { color: #e11d48; text-decoration: underline; }

/* ==============================
   RELATED — HORIZONTAL SCROLL
   ============================== */
.related-section { margin-top: 3rem; }
.related-header {
  display: flex; align-items: flex-end; justify-content: space-between;
  margin-bottom: 1.1rem; gap: 1rem;
}
.related-title {
  font-family: 'Cormorant Garamond', serif;
  font-size: 1.5rem; font-weight: 700; color: #1f2937;
}
.related-title em { font-style: italic; color: #e11d48; }
.related-see-all {
  font-family: 'Jost', sans-serif; font-size: .75rem; font-weight: 600;
  color: #e11d48; text-decoration: none; white-space: nowrap;
  transition: opacity .2s;
}
.related-see-all:hover { opacity: .7; }

/* Horizontal scroll container */
.related-scroll {
  overflow-x: auto;
  scroll-snap-type: x mandatory;
  scroll-padding: 0 1rem;
  -webkit-overflow-scrolling: touch;
  scrollbar-width: none;
  padding-bottom: .5rem;
  /* Fade edges */
  mask-image: linear-gradient(to right, transparent 0%, black 3%, black 92%, transparent 100%);
  -webkit-mask-image: linear-gradient(to right, transparent 0%, black 3%, black 92%, transparent 100%);
}
.related-scroll::-webkit-scrollbar { display: none; }
.related-track {
  display: flex; gap: .85rem;
  width: max-content;
  padding: .25rem .5rem .5rem;
}

/* Related card */
.rel-card {
  flex: 0 0 170px;
  scroll-snap-align: start;
  background: #fff; border: 1px solid #fde8b4;
  border-radius: 1rem; overflow: hidden;
  text-decoration: none;
  transition: all .28s cubic-bezier(.22,.68,0,1.2);
  display: flex; flex-direction: column;
}
@media(min-width:640px) { .rel-card { flex: 0 0 200px; } }
.rel-card:hover {
  transform: translateY(-5px);
  box-shadow: 0 10px 28px rgba(214,90,110,.13);
  border-color: #fca5a5;
}
.rel-card-img {
  height: 140px; overflow: hidden;
  background: linear-gradient(135deg,#fdf0f3,#fdf6ee);
  position: relative;
}
.rel-card-img img { width:100%;height:100%;object-fit:cover;transition:transform .4s; }
.rel-card:hover .rel-card-img img { transform: scale(1.07); }
.rel-card-body { padding: .7rem .8rem .8rem; flex:1; display:flex; flex-direction:column; gap:.2rem; }
.rel-card-cat {
  font-family:'Jost',sans-serif;font-size:.62rem;font-weight:700;
  letter-spacing:.06em;text-transform:uppercase;color:#e11d48;
}
.rel-card-name {
  font-family:'Jost',sans-serif;font-size:.78rem;font-weight:600;color:#1f2937;
  line-height:1.35;flex:1;
  display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;
}
.rel-card-price {
  font-family:'Cormorant Garamond',serif;font-size:.95rem;font-weight:700;color:#e11d48;
  font-style:italic;
}

/* ==============================
   CTA
   ============================== */
.prod-cta {
  background: linear-gradient(135deg, #881337 0%, #9f1239 50%, #7f1d1d 100%);
  position: relative; overflow: hidden;
}
.prod-cta-pat {
  position:absolute;inset:0;
  background-image:
    repeating-linear-gradient(45deg,rgba(255,255,255,.025) 0,rgba(255,255,255,.025) 1px,transparent 0,transparent 50%),
    repeating-linear-gradient(-45deg,rgba(255,255,255,.025) 0,rgba(255,255,255,.025) 1px,transparent 0,transparent 50%);
  background-size:28px 28px;
}
</style>

<div class="prod-page">
  <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">

    <!-- Breadcrumb -->
    <nav class="prod-breadcrumb">
      <a href="<?= BASE_URL ?>/">Beranda</a>
      <?php if ($product['parent_id'] && $product['parent_cat_slug']): ?>
      <span class="sep">❯</span>
      <a href="<?= BASE_URL ?>/<?= $product['parent_cat_slug'] ?>"><?= clean($product['parent_cat_name']) ?></a>
      <?php endif; ?>
      <span class="sep">❯</span>
      <a href="<?= BASE_URL ?>/<?= $product['cat_slug'] ?>"><?= clean($product['cat_name']) ?></a>
      <span class="sep">❯</span>
      <span class="cur"><?= $nama ?></span>
    </nav>

    <!-- Main Layout -->
    <div class="prod-layout">

      <!-- ===== FOTO COLUMN (sticky) ===== -->
      <div class="prod-photo-col">
        <div class="prod-photo-stage">

          <!-- Ornamen bunga TL -->
          <svg class="prod-flower-tl" width="80" height="90" viewBox="0 0 80 90" fill="none">
            <?php foreach([0,60,120,180,240,300] as $r): ?>
            <ellipse cx="40" cy="18" rx="7" ry="16" fill="#fda4af" opacity=".8" transform="rotate(<?=$r?> 40 38)"/>
            <?php endforeach; ?>
            <circle cx="40" cy="38" r="9" fill="#f9c784"/>
            <line x1="40" y1="50" x2="40" y2="90" stroke="#6b7c5c" stroke-width="2.5"/>
            <ellipse cx="28" cy="72" rx="11" ry="6" fill="#6b7c5c" opacity=".55" transform="rotate(-30 28 72)"/>
            <ellipse cx="52" cy="80" rx="11" ry="6" fill="#6b7c5c" opacity=".55" transform="rotate(30 52 80)"/>
          </svg>

          <!-- Ornamen daun TR -->
          <svg class="prod-leaf-tr" width="55" height="70" viewBox="0 0 55 70" fill="none">
            <path d="M27,5 C42,10 52,28 48,48 C44,62 32,68 20,62 C8,56 4,40 8,24 C12,10 20,2 27,5Z" fill="#86efac" opacity=".5"/>
            <path d="M27,5 C27,5 25,35 15,55" stroke="#4a7c59" stroke-width="1.5" opacity=".6"/>
          </svg>

          <!-- Foto blob -->
          <div class="prod-blob-wrap">
            <?php if ($product['is_featured']): ?>
            <div class="prod-featured-badge">✦ Terlaris</div>
            <?php endif; ?>

            <div class="prod-blob-img-inner">
              <?php if (!empty($product['image'])): ?>
              <img src="<?= UPLOAD_URL . $product['image'] ?>"
                   alt="<?= $nama ?> Chika Florist"
                   onerror="this.parentElement.classList.add('prod-blob-fallback');this.style.display='none'">
              <?php else: ?>
              <!-- Fallback SVG bunga -->
              <div class="prod-blob-fallback" style="width:100%;height:100%;display:flex;align-items:center;justify-content:center;">
                <svg width="120" height="120" viewBox="0 0 160 160" fill="none" opacity=".3">
                  <?php foreach([0,60,120,180,240,300] as $r): ?>
                  <ellipse cx="80" cy="32" rx="16" ry="36" fill="#e11d48" transform="rotate(<?=$r?> 80 80)"/>
                  <?php endforeach; ?>
                  <circle cx="80" cy="80" r="20" fill="#f9c784"/>
                </svg>
              </div>
              <?php endif; ?>
            </div>
            <!-- Gold ring -->
            <div class="prod-ring"></div>
          </div>

          <!-- Ornamen bunga BR -->
          <svg class="prod-flower-br" width="70" height="75" viewBox="0 0 70 75" fill="none">
            <?php foreach([0,72,144,216,288] as $r): ?>
            <ellipse cx="35" cy="14" rx="6" ry="14" fill="#c9a84c" opacity=".7" transform="rotate(<?=$r?> 35 35)"/>
            <?php endforeach; ?>
            <circle cx="35" cy="35" r="8" fill="#fda4af"/>
            <line x1="35" y1="48" x2="35" y2="75" stroke="#6b7c5c" stroke-width="2"/>
          </svg>

        </div>
      </div>

      <!-- ===== INFO COLUMN ===== -->
      <div class="prod-info-col">

        <!-- Cat path -->
        <div class="prod-cat-path">
          <?php if ($product['parent_id'] && $product['parent_cat_slug']): ?>
          <a href="<?= BASE_URL ?>/<?= $product['parent_cat_slug'] ?>"><?= clean($product['parent_cat_name']) ?></a>
          <span class="sep">›</span>
          <?php endif; ?>
          <a href="<?= BASE_URL ?>/<?= $product['cat_slug'] ?>"><?= clean($product['cat_name']) ?></a>
        </div>

        <!-- Nama -->
        <h1 class="prod-name"><?= $nama ?></h1>
        <div class="prod-name-underline"></div>

        <!-- Short desc -->
        <?php if ($product['short_desc']): ?>
        <p class="prod-short-desc"><?= clean($product['short_desc']) ?></p>
        <?php endif; ?>

        <!-- Price box -->
        <div class="prod-price-box">
          <span class="prod-price-label">Harga Mulai</span>
          <span class="prod-price-num"><?= formatHarga($product['price_min'], $product['price_max']) ?></span>
          <span class="prod-price-note">*Belum termasuk ongkos kirim</span>
        </div>

        <!-- CTA -->
        <a href="<?= waLink("Halo Chika Florist, saya ingin pesan {$nama}") ?>" target="_blank" class="prod-btn-wa">
          <svg width="18" height="18" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z"/><path d="M12 0C5.373 0 0 5.373 0 12c0 2.096.541 4.063 1.491 5.776L.057 23.04l5.43-1.424A11.947 11.947 0 0012 24c6.627 0 12-5.373 12-12S18.627 0 12 0zm0 21.818a9.818 9.818 0 01-5.015-1.371l-.36-.214-3.726.977.995-3.633-.235-.374A9.818 9.818 0 1112 21.818z"/></svg>
          Pesan via WhatsApp
        </a>
        <a href="<?= BASE_URL ?>/<?= $product['cat_slug'] ?>" class="prod-btn-cat">
          <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M4 6h16M4 12h16M4 18h7"/></svg>
          Lihat Semua <?= clean($product['cat_name']) ?>
        </a>

        <!-- Trust cards -->
        <div class="prod-trust-grid">
          <?php
          $trusts = [
            ['icon'=>'<circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/>','title'=>'24 Jam Nonstop','sub'=>'Pesan kapan saja'],
            ['icon'=>'<path d="M12 22s-8-4.5-8-11.8A8 8 0 0112 2a8 8 0 018 8.2c0 7.3-8 11.8-8 11.8z"/><circle cx="12" cy="10" r="3"/>','title'=>'Same Day Delivery','sub'=>'Kirim hari ini'],
            ['icon'=>'<path d="M12 22s-8-4.5-8-11.8A8 8 0 0112 2a8 8 0 018 8.2c0 7.3-8 11.8-8 11.8z"/>','title'=>'Bunga Fresh','sub'=>'Kualitas terjaga'],
            ['icon'=>'<path d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>','title'=>'Harga Transparan','sub'=>'Tanpa biaya tersembunyi'],
          ];
          foreach ($trusts as $t): ?>
          <div class="prod-trust-card">
            <div class="prod-trust-icon">
              <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><?= $t['icon'] ?></svg>
            </div>
            <div class="prod-trust-text">
              <span class="prod-trust-title"><?= $t['title'] ?></span>
              <span class="prod-trust-sub"><?= $t['sub'] ?></span>
            </div>
          </div>
          <?php endforeach; ?>
        </div>

        <!-- Accordion deskripsi -->
        <div class="prod-acc-wrap">
          <?php
          $accItems = [];
          if ($product['description'])
            $accItems[] = ['icon'=>'<path d="M9 12h6M9 16h6M9 8h6M5 3h14a2 2 0 012 2v14a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2z"/>','title'=>'Deskripsi Produk','body'=>nl2br(clean($product['description']))];

          $accItems[] = ['icon'=>'<path d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>',
            'title'=>'Cara Pemesanan',
            'body'=>'<ol style="list-style:decimal;padding-left:1.2rem;display:flex;flex-direction:column;gap:.4rem;">
              <li>Klik tombol <strong>Pesan via WhatsApp</strong> di atas</li>
              <li>Ceritakan kebutuhan dan acara Anda</li>
              <li>Kirim alamat & waktu pengiriman</li>
              <li>Konfirmasi pesanan & bunga segera dikirim</li>
            </ol>'];

          $accItems[] = ['icon'=>'<path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>',
            'title'=>'Informasi Produk',
            'body'=>'<p>Chika Florist menyediakan <strong>'.clean($nama).'</strong> berkualitas tinggi untuk berbagai kebutuhan acara. Setiap produk dibuat dengan bunga segar pilihan oleh tim florist profesional.</p>
            <p style="margin-top:.6rem;">Tersedia melalui layanan <a href="'.BASE_URL.'/toko-bunga-online-24-jam-indonesia">toko bunga online 24 jam Indonesia</a> Chika Florist.</p>'];

          foreach ($accItems as $ai => $acc): ?>
          <div class="prod-acc-item <?= $ai===0?'open':'' ?>" data-acc>
            <div class="prod-acc-head" onclick="toggleProdAcc(this)">
              <div class="prod-acc-head-left">
                <div class="prod-acc-icon-wrap">
                  <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><?= $acc['icon'] ?></svg>
                </div>
                <span class="prod-acc-title"><?= $acc['title'] ?></span>
              </div>
              <div class="prod-acc-chevron">
                <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M6 9l6 6 6-6"/></svg>
              </div>
            </div>
            <div class="prod-acc-body" style="<?= $ai===0?'max-height:600px':'' ?>">
              <div class="prod-acc-body-inner"><?= $acc['body'] ?></div>
            </div>
          </div>
          <?php endforeach; ?>
        </div>

        <!-- City strip -->
        <?php if (!empty($cities)): ?>
        <div class="prod-city-section">
          <div class="prod-city-label">
            <svg width="11" height="11" fill="none" stroke="#c9a84c" stroke-width="2" viewBox="0 0 24 24"><path d="M17.657 16.657L13.414 20.9a2 2 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><circle cx="12" cy="11" r="3"/></svg>
            <?= $nama ?> Tersedia di
          </div>
          <div class="prod-city-grid">
            <?php foreach ($cities as $city): ?>
            <a href="<?= BASE_URL ?>/toko-bunga-<?= $city['slug'] ?>" class="prod-city-chip">
              <span class="prod-city-chip-dot"></span>
              <?= clean($city['name']) ?>
            </a>
            <?php endforeach; ?>
            <a href="<?= BASE_URL ?>/" class="prod-city-chip" style="border-color:#fca5a5;color:#e11d48;">
              <span class="prod-city-chip-dot" style="background:#e11d48;"></span>
              + Seluruh Indonesia
            </a>
          </div>
        </div>
        <?php endif; ?>

      </div>
    </div><!-- end .prod-layout -->

    <!-- SEO Section -->
    <div class="prod-seo">
      <h2>Tentang <?= $nama ?></h2>
      <p>Chika Florist menyediakan <?= $nama ?> berkualitas tinggi untuk berbagai kebutuhan acara. Setiap produk dibuat dari bunga segar pilihan oleh tim florist profesional kami.</p>
      <h2><?= $nama ?> Tersedia di Seluruh Indonesia</h2>
      <p>Produk ini dapat dipesan melalui layanan <a href="<?= BASE_URL ?>/toko-bunga-online-24-jam-indonesia">toko bunga online 24 jam Indonesia</a> Chika Florist. Kami melayani pengiriman ke berbagai kota di seluruh Indonesia.</p>
    </div>

    <!-- Related Products — Horizontal Scroll -->
    <?php if (!empty($related)): ?>
    <div class="related-section">
      <div class="related-header">
        <h2 class="related-title">Produk <em><?= clean($product['cat_name']) ?></em> Lainnya</h2>
        <a href="<?= BASE_URL ?>/<?= $product['cat_slug'] ?>" class="related-see-all">Lihat Semua →</a>
      </div>
      <div class="related-scroll">
        <div class="related-track">
          <?php foreach ($related as $r): ?>
          <a href="<?= BASE_URL ?>/produk/<?= $r['slug'] ?>" class="rel-card">
            <div class="rel-card-img">
              <img src="<?= UPLOAD_URL.($r['image']??'') ?>" alt="<?= clean($r['name']) ?>" onerror="this.style.display='none'">
            </div>
            <div class="rel-card-body">
              <span class="rel-card-cat"><?= clean($r['cat_name']) ?></span>
              <span class="rel-card-name"><?= clean($r['name']) ?></span>
              <span class="rel-card-price"><?= formatHarga($r['price_min'],$r['price_max']) ?></span>
            </div>
          </a>
          <?php endforeach; ?>
        </div>
      </div>
    </div>
    <?php endif; ?>

  </div>
</div>

<!-- CTA -->
<section class="prod-cta py-12 px-4 text-center">
  <div class="prod-cta-pat"></div>
  <div style="position:absolute;width:300px;height:300px;border-radius:50%;background:rgba(244,63,94,.18);filter:blur(80px);top:-80px;left:50%;transform:translateX(-50%);pointer-events:none;"></div>
  <div style="position:relative;z-index:2;max-width:460px;margin:0 auto;">
    <svg width="30" height="30" viewBox="0 0 80 80" fill="none" style="display:inline-block;margin-bottom:.75rem;opacity:.7;">
      <?php foreach([0,60,120,180,240,300] as $r): ?>
      <ellipse cx="40" cy="22" rx="8" ry="18" fill="#fda4af" transform="rotate(<?=$r?> 40 40)"/>
      <?php endforeach; ?>
      <circle cx="40" cy="40" r="10" fill="#f9c784"/>
    </svg>
    <h2 style="font-family:'Cormorant Garamond',serif;font-size:1.8rem;font-weight:700;color:#fff;margin-bottom:.5rem;">
      Pesan <em style="font-style:italic;color:#f9c784;"><?= $nama ?></em> Sekarang
    </h2>
    <p style="font-family:'Jost',sans-serif;font-size:.86rem;color:rgba(255,255,255,.7);margin-bottom:1.5rem;line-height:1.65;">
      Admin kami siap membantu konsultasi 24 jam — bunga segar langsung dikirim.
    </p>
    <a href="<?= waLink("Halo, saya ingin pesan {$nama}") ?>" target="_blank"
       style="display:inline-flex;align-items:center;gap:.6rem;font-family:'Jost',sans-serif;font-size:.88rem;font-weight:700;background:#fff;color:#e11d48;padding:.85rem 2rem;border-radius:999px;text-decoration:none;box-shadow:0 4px 20px rgba(0,0,0,.2);">
      <svg width="16" height="16" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z"/><path d="M12 0C5.373 0 0 5.373 0 12c0 2.096.541 4.063 1.491 5.776L.057 23.04l5.43-1.424A11.947 11.947 0 0012 24c6.627 0 12-5.373 12-12S18.627 0 12 0zm0 21.818a9.818 9.818 0 01-5.015-1.371l-.36-.214-3.726.977.995-3.633-.235-.374A9.818 9.818 0 1112 21.818z"/></svg>
      Pesan via WhatsApp
    </a>
  </div>
</section>

<script>
// Accordion
function toggleProdAcc(head) {
  var item  = head.closest('[data-acc]');
  var body  = item.querySelector('.prod-acc-body');
  var inner = body.querySelector('.prod-acc-body-inner');
  var isOpen = item.classList.contains('open');
  document.querySelectorAll('[data-acc].open').forEach(function(el){
    el.classList.remove('open');
    el.querySelector('.prod-acc-body').style.maxHeight = '0';
  });
  if (!isOpen) {
    item.classList.add('open');
    body.style.maxHeight = inner.scrollHeight + 'px';
  }
}
// Init first
document.addEventListener('DOMContentLoaded', function(){
  var first = document.querySelector('[data-acc]');
  if (first) {
    var b = first.querySelector('.prod-acc-body');
    b.style.maxHeight = b.querySelector('.prod-acc-body-inner').scrollHeight + 'px';
  }
});
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>