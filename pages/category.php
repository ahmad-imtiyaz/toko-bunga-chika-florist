<?php
require_once __DIR__ . '/../includes/config.php';
$slug = $_GET['slug'] ?? '';
$pdo  = getDB();
$stmt = $pdo->prepare("SELECT * FROM categories WHERE slug=? AND is_active=1");
$stmt->execute([$slug]);
$category = $stmt->fetch();
if (!$category) { http_response_code(404); require __DIR__ . '/404.php'; exit(); }

$subCats = getSubCategories($category['id']);
$parent  = null;
if ($category['parent_id']) {
    $s2 = $pdo->prepare("SELECT * FROM categories WHERE id=?");
    $s2->execute([$category['parent_id']]);
    $parent = $s2->fetch();
}

$cat_ids = [$category['id']];
foreach ($subCats as $sc) $cat_ids[] = $sc['id'];
$placeholders = implode(',', array_fill(0, count($cat_ids), '?'));
$s3 = $pdo->prepare("SELECT p.*,c.name as cat_name,c.slug as cat_slug FROM products p JOIN categories c ON p.category_id=c.id WHERE p.category_id IN ($placeholders) AND p.is_active=1 ORDER BY p.is_featured DESC, p.sort_order ASC");
$s3->execute($cat_ids);
$products = $s3->fetchAll();
$cities   = getActiveCities(12);

$nama          = clean($category['name']);
$page_title    = $category['meta_title'] ?: "{$nama} 24 Jam | Florist Online – Chika Florist";
$meta_desc     = $category['meta_desc'] ?: "Pesan {$nama} online 24 jam. Tersedia berbagai pilihan desain elegan dengan pengiriman cepat ke seluruh Indonesia.";
$canonical_url = BASE_URL . '/' . $category['slug'];

$breadcrumbs = [['label'=>'Beranda','url'=>'/']];
if ($parent) $breadcrumbs[] = ['label'=>$parent['name'],'url'=>'/'.$parent['slug']];
$breadcrumbs[] = ['label'=>$nama];
require_once __DIR__ . '/../includes/header.php';
?>

<!-- ==============================
     HERO SECTION
     ============================== -->
<style>
@import url('https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,400;0,600;0,700;1,400;1,600&family=Jost:wght@300;400;500;600&display=swap');

.cat-hero {
  position: relative;
  height: 400px;
  overflow: hidden;
  display: flex;
  align-items: center;
  justify-content: center;
}

/* Background foto dari DB */
.cat-hero-bg {
  position: absolute;
  inset: 0;
  background-size: cover;
  background-position: center;
  background-repeat: no-repeat;
  transform: scale(1.05);
  transition: transform 8s ease;
}
.cat-hero:hover .cat-hero-bg { transform: scale(1); }

/* Fallback gradient kalau foto kosong */
.cat-hero-bg-fallback {
  position: absolute;
  inset: 0;
  background: linear-gradient(135deg,
    #f0f4ed 0%,
    #fdf6ee 30%,
    #fdf0f3 60%,
    #f3e8f0 100%
  );
}

/* Overlay berlapis — terang di tengah atas, gelap di bawah */
.cat-hero-overlay {
  position: absolute;
  inset: 0;
  background:
    radial-gradient(ellipse 80% 60% at 50% 30%, rgba(0,0,0,0.08) 0%, rgba(0,0,0,0.52) 100%),
    linear-gradient(to bottom, rgba(0,0,0,0.1) 0%, rgba(0,0,0,0.55) 100%);
}

/* Overlay untuk fallback (lebih terang, warna berbeda) */
.cat-hero-overlay-light {
  position: absolute;
  inset: 0;
  background:
    radial-gradient(ellipse 70% 70% at 50% 40%, rgba(253,240,243,0.0) 0%, rgba(245,228,233,0.5) 100%);
}

/* Trellis pattern */
.cat-hero-pattern {
  position: absolute;
  inset: 0;
  opacity: 0.06;
  background-image:
    repeating-linear-gradient(45deg, #c9a84c 0px, #c9a84c 1px, transparent 0px, transparent 50%),
    repeating-linear-gradient(-45deg, #c9a84c 0px, #c9a84c 1px, transparent 0px, transparent 50%);
  background-size: 28px 28px;
}

/* Konten tengah */
.cat-hero-content {
  position: relative;
  z-index: 10;
  text-align: center;
  padding: 0 1.5rem;
  max-width: 680px;
  width: 100%;
  animation: heroFadeUp 0.8s cubic-bezier(.22,.68,0,1.2) both;
}
@keyframes heroFadeUp {
  from { opacity: 0; transform: translateY(28px); }
  to   { opacity: 1; transform: translateY(0); }
}

/* Breadcrumb */
.cat-breadcrumb {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 0.4rem;
  margin-bottom: 1rem;
  animation: heroFadeUp 0.7s 0.1s both;
}
.cat-breadcrumb a,
.cat-breadcrumb span {
  font-family: 'Jost', sans-serif;
  font-size: 0.72rem;
  font-weight: 500;
  letter-spacing: 0.08em;
  text-transform: uppercase;
  color: rgba(255,255,255,0.75);
  text-decoration: none;
  transition: color 0.2s;
}
.cat-breadcrumb a:hover { color: #fff; }
.cat-breadcrumb .sep { color: rgba(255,255,255,0.35); font-size: 0.6rem; }
.cat-breadcrumb .current { color: #f9c784; }

/* Heading */
.cat-hero h1 {
  font-family: 'Cormorant Garamond', serif;
  font-size: clamp(2rem, 5vw, 3.2rem);
  font-weight: 700;
  color: #fff;
  line-height: 1.15;
  margin-bottom: 0.75rem;
  text-shadow: 0 2px 20px rgba(0,0,0,0.3);
  animation: heroFadeUp 0.7s 0.2s both;
}
.cat-hero h1 em {
  font-style: italic;
  color: #f9c784;
}

/* Gold divider */
.cat-hero-divider {
  width: 60px;
  height: 2px;
  background: linear-gradient(90deg, transparent, #c9a84c, transparent);
  margin: 0.75rem auto;
  animation: heroFadeUp 0.7s 0.3s both;
}

/* Description */
.cat-hero-desc {
  font-family: 'Jost', sans-serif;
  font-size: 0.92rem;
  color: rgba(255,255,255,0.82);
  line-height: 1.65;
  max-width: 520px;
  margin: 0 auto 1.25rem;
  animation: heroFadeUp 0.7s 0.35s both;
}

/* Badges row */
.cat-hero-badges {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 0.6rem;
  flex-wrap: wrap;
  margin-bottom: 1.4rem;
  animation: heroFadeUp 0.7s 0.4s both;
}
.cat-badge {
  display: inline-flex;
  align-items: center;
  gap: 0.35rem;
  font-family: 'Jost', sans-serif;
  font-size: 0.72rem;
  font-weight: 600;
  letter-spacing: 0.04em;
  padding: 0.3rem 0.85rem;
  border-radius: 999px;
  border: 1px solid rgba(255,255,255,0.25);
  backdrop-filter: blur(8px);
  background: rgba(255,255,255,0.12);
  color: #fff;
}
.cat-badge.gold {
  background: rgba(201,168,76,0.25);
  border-color: rgba(201,168,76,0.5);
  color: #f9c784;
}
.cat-badge-dot {
  width: 6px; height: 6px;
  border-radius: 50%;
  background: #f9c784;
  flex-shrink: 0;
}

/* Scroll button */
.cat-scroll-btn {
  display: inline-flex;
  align-items: center;
  gap: 0.5rem;
  font-family: 'Jost', sans-serif;
  font-size: 0.8rem;
  font-weight: 600;
  letter-spacing: 0.06em;
  text-transform: uppercase;
  color: rgba(255,255,255,0.9);
  background: rgba(255,255,255,0.15);
  border: 1px solid rgba(255,255,255,0.3);
  backdrop-filter: blur(10px);
  padding: 0.6rem 1.4rem;
  border-radius: 999px;
  cursor: pointer;
  transition: all 0.25s;
  text-decoration: none;
  animation: heroFadeUp 0.7s 0.5s both;
}
.cat-scroll-btn:hover {
  background: rgba(255,255,255,0.25);
  color: #fff;
  transform: translateY(-2px);
}
.cat-scroll-btn svg {
  animation: bounceDown 1.5s ease-in-out infinite;
}
@keyframes bounceDown {
  0%,100% { transform: translateY(0); }
  50%      { transform: translateY(4px); }
}

/* SVG bunga dekoratif pojok */
.cat-hero-flower-tl,
.cat-hero-flower-tr,
.cat-hero-flower-br,
.cat-hero-flower-bl {
  position: absolute;
  z-index: 5;
  pointer-events: none;
  opacity: 0.22;
}
.cat-hero-flower-tl { top: -10px; left: -10px; transform: rotate(-20deg); }
.cat-hero-flower-tr { top: -5px;  right: -5px;  transform: rotate(30deg) scaleX(-1); }
.cat-hero-flower-bl { bottom: 20px; left: 0;   transform: rotate(10deg); }
.cat-hero-flower-br { bottom: 10px; right: 0;  transform: rotate(-15deg) scaleX(-1); }

/* Wave bawah */
.cat-hero-wave {
  position: absolute;
  bottom: -1px;
  left: 0; right: 0;
  z-index: 8;
  line-height: 0;
}

/* ==============================
   SECTION KONTEN
   ============================== */
.cat-content {
  background: linear-gradient(170deg, #fdf6ee 0%, #fff 50%, #fdf0f3 100%);
  min-height: 60vh;
}

/* Sub kategori */
.subcat-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(160px, 1fr));
  gap: 1rem;
}
.subcat-card {
  background: #fff;
  border-radius: 1rem;
  overflow: hidden;
  border: 1px solid #fde8b4;
  text-decoration: none;
  transition: all 0.25s;
  display: block;
}
.subcat-card:hover {
  transform: translateY(-4px);
  box-shadow: 0 8px 24px rgba(214,90,110,0.12);
  border-color: #fca5a5;
}
.subcat-img {
  height: 100px;
  overflow: hidden;
  background: linear-gradient(135deg,#fdf0f3,#fdf6ee);
}
.subcat-img img {
  width: 100%; height: 100%;
  object-fit: cover;
  transition: transform 0.35s;
}
.subcat-card:hover .subcat-img img { transform: scale(1.07); }
.subcat-label {
  font-family: 'Jost', sans-serif;
  font-size: 0.82rem;
  font-weight: 600;
  color: #374151;
  padding: 0.6rem 0.75rem;
  text-align: center;
  transition: color 0.2s;
}
.subcat-card:hover .subcat-label { color: #e11d48; }

/* Section heading */
.section-eyebrow {
  font-family: 'Jost', sans-serif;
  font-size: 0.72rem;
  font-weight: 600;
  letter-spacing: 0.12em;
  text-transform: uppercase;
  color: #e11d48;
  margin-bottom: 0.4rem;
}
.section-title {
  font-family: 'Cormorant Garamond', serif;
  font-size: clamp(1.5rem, 3vw, 2.1rem);
  font-weight: 700;
  color: #1f2937;
  margin-bottom: 0.3rem;
}
.section-gold-line {
  width: 50px; height: 2px;
  background: linear-gradient(90deg, #c9a84c, transparent);
  margin: 0.5rem 0 1.5rem;
}

/* Product grid */
.prod-grid-cat {
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  gap: 1rem;
}
@media(min-width:640px)  { .prod-grid-cat { grid-template-columns: repeat(3,1fr); gap: 1.25rem; } }
@media(min-width:1024px) { .prod-grid-cat { grid-template-columns: repeat(4,1fr); gap: 1.5rem; } }

.pcard {
  background: #fff;
  border-radius: 1rem;
  overflow: hidden;
  border: 1px solid #fde8b4;
  transition: all 0.3s cubic-bezier(.22,.68,0,1.2);
  display: flex;
  flex-direction: column;
}
.pcard:hover {
  transform: translateY(-5px);
  box-shadow: 0 12px 32px rgba(214,90,110,0.13);
  border-color: #fca5a5;
}
.pcard-img {
  position: relative;
  height: 180px;
  overflow: hidden;
  background: linear-gradient(135deg,#fdf0f3,#fdf6ee);
  flex-shrink: 0;
}
@media(min-width:640px) { .pcard-img { height: 200px; } }
.pcard-img img {
  width: 100%; height: 100%;
  object-fit: cover;
  transition: transform 0.45s ease;
}
.pcard:hover .pcard-img img { transform: scale(1.07); }

/* Overlay pesan cepat */
.pcard-overlay {
  position: absolute;
  inset: 0;
  background: rgba(17,5,8,0.55);
  display: flex;
  align-items: center;
  justify-content: center;
  opacity: 0;
  transition: opacity 0.3s;
}
.pcard:hover .pcard-overlay { opacity: 1; }
.pcard-overlay-btn {
  font-family: 'Jost', sans-serif;
  font-size: 0.75rem;
  font-weight: 700;
  letter-spacing: 0.06em;
  text-transform: uppercase;
  color: #fff;
  background: #e11d48;
  border: none;
  padding: 0.55rem 1.3rem;
  border-radius: 999px;
  text-decoration: none;
  transform: translateY(10px);
  transition: transform 0.3s;
  display: flex;
  align-items: center;
  gap: 0.4rem;
}
.pcard:hover .pcard-overlay-btn { transform: translateY(0); }

/* Badge terlaris */
.pcard-badge {
  position: absolute;
  top: 0.6rem; left: 0.6rem;
  font-family: 'Jost', sans-serif;
  font-size: 0.65rem;
  font-weight: 700;
  letter-spacing: 0.05em;
  text-transform: uppercase;
  color: #78350f;
  background: linear-gradient(135deg,#fbbf24,#f59e0b);
  padding: 0.22rem 0.6rem;
  border-radius: 999px;
  box-shadow: 0 2px 8px rgba(245,158,11,0.35);
}

.pcard-body { padding: 0.9rem 1rem 1rem; flex: 1; display: flex; flex-direction: column; }
.pcard-cat {
  font-family: 'Jost', sans-serif;
  font-size: 0.68rem;
  font-weight: 600;
  letter-spacing: 0.06em;
  text-transform: uppercase;
  color: #e11d48;
  margin-bottom: 0.3rem;
}
.pcard-name {
  font-family: 'Jost', sans-serif;
  font-size: 0.85rem;
  font-weight: 600;
  color: #1f2937;
  line-height: 1.4;
  margin-bottom: 0.3rem;
}
.pcard-desc {
  font-family: 'Jost', sans-serif;
  font-size: 0.75rem;
  color: #9ca3af;
  line-height: 1.5;
  margin-bottom: 0.5rem;
  display: -webkit-box;
  -webkit-line-clamp: 2;
  line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
  flex: 1;
}
.pcard-price {
  font-family: 'Cormorant Garamond', serif;
  font-size: 1.05rem;
  font-weight: 700;
  color: #e11d48;
  margin-bottom: 0.7rem;
}
.pcard-btn {
  display: block;
  width: 100%;
  text-align: center;
  font-family: 'Jost', sans-serif;
  font-size: 0.75rem;
  font-weight: 700;
  letter-spacing: 0.04em;
  padding: 0.55rem;
  border-radius: 0.6rem;
  background: #f0fdf4;
  color: #16a34a;
  border: 1px solid #bbf7d0;
  text-decoration: none;
  transition: all 0.2s;
}
.pcard-btn:hover {
  background: #16a34a;
  color: #fff;
  border-color: #16a34a;
}

/* Empty state */
.empty-state {
  text-align: center;
  padding: 4rem 1rem;
  color: #9ca3af;
}
.empty-state svg { margin: 0 auto 1rem; opacity: 0.3; }

/* SEO content */
.seo-content {
  font-family: 'Jost', sans-serif;
  color: #4b5563;
  font-size: 0.88rem;
  line-height: 1.8;
}
.seo-content h2 {
  font-family: 'Cormorant Garamond', serif;
  font-size: 1.2rem;
  font-weight: 700;
  color: #1f2937;
  margin: 1.5rem 0 0.5rem;
  border-left: 3px solid #c9a84c;
  padding-left: 0.75rem;
}
.seo-content ul { list-style: none; padding: 0; display: flex; flex-wrap: wrap; gap: 0.4rem; }
.seo-content ul li::before { content: '✦ '; color: #e11d48; font-size: 0.65rem; }
.seo-content a { color: #e11d48; text-decoration: underline; }

/* City strip */
.city-strip {
  background: linear-gradient(135deg, #fdf6ee, #fdf0f3);
  border: 1px solid #fde8b4;
  border-radius: 1rem;
  padding: 1.25rem;
}
.city-strip-label {
  font-family: 'Jost', sans-serif;
  font-size: 0.68rem;
  font-weight: 700;
  letter-spacing: 0.1em;
  text-transform: uppercase;
  color: #9ca3af;
  margin-bottom: 0.75rem;
  display: flex;
  align-items: center;
  gap: 0.5rem;
}
.city-strip-label::after {
  content: '';
  flex: 1;
  height: 1px;
  background: linear-gradient(90deg, #fde8b4, transparent);
}
.internal-links { display: flex; flex-wrap: wrap; gap: 0.4rem; }
.internal-links a {
  font-family: 'Jost', sans-serif;
  font-size: 0.72rem;
  font-weight: 500;
  color: #6b7280;
  background: #fff;
  border: 1px solid #fde8b4;
  border-radius: 999px;
  padding: 0.25rem 0.7rem;
  text-decoration: none;
  transition: all 0.2s;
}
.internal-links a:hover {
  color: #e11d48;
  border-color: #fca5a5;
  background: #fff5f5;
}

/* CTA bottom */
.cat-cta {
  background: linear-gradient(135deg, #881337 0%, #9f1239 50%, #7f1d1d 100%);
  position: relative;
  overflow: hidden;
}
.cat-cta-pattern {
  position: absolute; inset: 0;
  background-image:
    repeating-linear-gradient(45deg, rgba(255,255,255,0.03) 0, rgba(255,255,255,0.03) 1px, transparent 0, transparent 50%),
    repeating-linear-gradient(-45deg, rgba(255,255,255,0.03) 0, rgba(255,255,255,0.03) 1px, transparent 0, transparent 50%);
  background-size: 28px 28px;
}
.cat-cta-glow {
  position: absolute;
  border-radius: 50%;
  filter: blur(60px);
  pointer-events: none;
}
</style>

<!-- HERO -->
<section class="cat-hero" id="cat-hero">

  <?php if (!empty($category['image'])): ?>
  <!-- Foto dari DB -->
  <div class="cat-hero-bg" style="background-image:url('<?= UPLOAD_URL . $category['image'] ?>')"></div>
  <div class="cat-hero-overlay"></div>
  <?php else: ?>
  <!-- Fallback gradient -->
  <div class="cat-hero-bg-fallback"></div>
  <div class="cat-hero-overlay-light"></div>
  <?php endif; ?>

  <!-- Pattern trellis -->
  <div class="cat-hero-pattern"></div>

  <!-- SVG Bunga TL -->
  <svg class="cat-hero-flower-tl" width="180" height="180" viewBox="0 0 180 180" fill="none">
    <g opacity="1">
      <?php foreach([[0,'#e11d48'],[60,'#c9a84c'],[120,'#e11d48'],[180,'#c9a84c'],[240,'#e11d48'],[300,'#c9a84c']] as [$rot,$clr]): ?>
      <ellipse cx="90" cy="60" rx="18" ry="36" fill="<?= $clr ?>" opacity="0.7" transform="rotate(<?= $rot ?> 90 90)"/>
      <?php endforeach; ?>
      <circle cx="90" cy="90" r="18" fill="#f9c784"/>
    </g>
    <line x1="90" y1="130" x2="90" y2="180" stroke="#6b7c5c" stroke-width="3"/>
    <ellipse cx="72" cy="158" rx="14" ry="8" fill="#6b7c5c" opacity="0.6" transform="rotate(-30 72 158)"/>
    <ellipse cx="108" cy="150" rx="14" ry="8" fill="#6b7c5c" opacity="0.6" transform="rotate(30 108 150)"/>
  </svg>

  <!-- SVG Bunga TR -->
  <svg class="cat-hero-flower-tr" width="150" height="150" viewBox="0 0 150 150" fill="none">
    <?php foreach([[0,'#fca5a5'],[72,'#c9a84c'],[144,'#fca5a5'],[216,'#c9a84c'],[288,'#fca5a5']] as [$rot,$clr]): ?>
    <ellipse cx="75" cy="48" rx="14" ry="30" fill="<?= $clr ?>" opacity="0.6" transform="rotate(<?= $rot ?> 75 75)"/>
    <?php endforeach; ?>
    <circle cx="75" cy="75" r="14" fill="#f9c784" opacity="0.9"/>
  </svg>

  <!-- SVG Bunga BR -->
  <svg class="cat-hero-flower-br" width="200" height="140" viewBox="0 0 200 140" fill="none">
    <line x1="100" y1="0" x2="100" y2="140" stroke="#6b7c5c" stroke-width="3"/>
    <ellipse cx="78" cy="50" rx="16" ry="9" fill="#6b7c5c" opacity="0.5" transform="rotate(-35 78 50)"/>
    <ellipse cx="122" cy="80" rx="16" ry="9" fill="#6b7c5c" opacity="0.5" transform="rotate(35 122 80)"/>
    <?php foreach([[0,'#fda4af'],[60,'#c9a84c'],[120,'#fda4af'],[180,'#c9a84c'],[240,'#fda4af'],[300,'#c9a84c']] as [$rot,$clr]): ?>
    <ellipse cx="100" cy="18" rx="13" ry="22" fill="<?= $clr ?>" opacity="0.65" transform="rotate(<?= $rot ?> 100 40)"/>
    <?php endforeach; ?>
    <circle cx="100" cy="40" r="12" fill="#f9c784"/>
  </svg>

  <!-- Konten tengah -->
  <div class="cat-hero-content">

    <!-- Breadcrumb -->
    <nav class="cat-breadcrumb">
      <a href="<?= BASE_URL ?>/">Beranda</a>
      <span class="sep">❯</span>
      <?php if ($parent): ?>
        <a href="<?= BASE_URL ?>/<?= $parent['slug'] ?>"><?= clean($parent['name']) ?></a>
        <span class="sep">❯</span>
      <?php endif; ?>
      <span class="current"><?= $nama ?></span>
    </nav>

    <!-- Judul -->
    <h1><?= $nama ?></h1>

    <!-- Divider emas -->
    <div class="cat-hero-divider"></div>

    <!-- Deskripsi -->
    <?php if (!empty($category['description'])): ?>
    <p class="cat-hero-desc"><?= clean(substr($category['description'], 0, 150)) ?><?= strlen($category['description']) > 150 ? '…' : '' ?></p>
    <?php else: ?>
    <p class="cat-hero-desc">Koleksi <?= $nama ?> berkualitas tinggi dari Chika Florist — dibuat dari bunga segar pilihan untuk setiap momen spesial Anda.</p>
    <?php endif; ?>

    <!-- Badges -->
    <div class="cat-hero-badges">
      <span class="cat-badge gold">
        <span class="cat-badge-dot"></span>
        <?= count($products) ?> Produk Tersedia
      </span>
      <span class="cat-badge">
        <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
        Layanan 24 Jam
      </span>
      <span class="cat-badge">
        <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
        Same Day Delivery
      </span>
    </div>

    <!-- Scroll btn -->
    <a href="#produk" class="cat-scroll-btn" onclick="document.getElementById('produk').scrollIntoView({behavior:'smooth'});return false;">
      Lihat Produk
      <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
        <path d="M12 5v14M5 12l7 7 7-7"/>
      </svg>
    </a>

  </div>

  <!-- Wave bawah -->
  <div class="cat-hero-wave">
    <svg viewBox="0 0 1440 60" fill="none" xmlns="http://www.w3.org/2000/svg" preserveAspectRatio="none" style="width:100%;height:60px;display:block;">
      <path d="M0,40 C240,0 480,60 720,30 C960,0 1200,50 1440,20 L1440,60 L0,60 Z"
            fill="<?= !empty($category['image']) ? '#fdf6ee' : '#fdf6ee' ?>"/>
    </svg>
  </div>
</section>

<!-- ==============================
     KONTEN UTAMA
     ============================== -->
<div class="cat-content" id="produk">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 lg:py-14">

    <!-- Sub Kategori -->
    <?php if (!empty($subCats)): ?>
    <div class="mb-12">
      <p class="section-eyebrow">Jelajahi</p>
      <h2 class="section-title">Jenis <?= $nama ?></h2>
      <div class="section-gold-line"></div>
      <div class="subcat-grid">
        <?php foreach ($subCats as $sc): ?>
        <a href="<?= BASE_URL ?>/<?= $sc['slug'] ?>" class="subcat-card">
          <div class="subcat-img">
            <img src="<?= UPLOAD_URL.($sc['image']??'') ?>" alt="<?= clean($sc['name']) ?>" onerror="this.style.display='none'">
          </div>
          <div class="subcat-label"><?= clean($sc['name']) ?></div>
        </a>
        <?php endforeach; ?>
      </div>
    </div>
    <?php endif; ?>

    <!-- Grid Produk -->
    <?php if (!empty($products)): ?>
    <div class="mb-8">
      <p class="section-eyebrow">Koleksi</p>
      <h2 class="section-title"><?= count($products) ?> Produk <?= $nama ?></h2>
      <div class="section-gold-line"></div>
    </div>
    <div class="prod-grid-cat">
      <?php foreach ($products as $prod): ?>
      <div class="pcard">
        <div class="pcard-img">
          <img src="<?= UPLOAD_URL.($prod['image']??'') ?>" alt="<?= clean($prod['name']) ?>" onerror="this.style.display='none'">
          <?php if ($prod['is_featured']): ?>
          <span class="pcard-badge">✦ Terlaris</span>
          <?php endif; ?>
          <div class="pcard-overlay">
            <a href="<?= waLink('Halo, saya ingin pesan '.$prod['name']) ?>" target="_blank" class="pcard-overlay-btn">
              <svg width="12" height="12" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z"/><path d="M12 0C5.373 0 0 5.373 0 12c0 2.096.541 4.063 1.491 5.776L.057 23.04l5.43-1.424A11.947 11.947 0 0012 24c6.627 0 12-5.373 12-12S18.627 0 12 0zm0 21.818a9.818 9.818 0 01-5.015-1.371l-.36-.214-3.726.977.995-3.633-.235-.374A9.818 9.818 0 1112 21.818z"/></svg>
              Pesan Cepat
            </a>
          </div>
        </div>
        <div class="pcard-body">
          <p class="pcard-cat"><?= clean($prod['cat_name']) ?></p>
          <h3 class="pcard-name"><?= clean($prod['name']) ?></h3>
          <?php if (!empty($prod['short_desc'])): ?>
          <p class="pcard-desc"><?= clean($prod['short_desc']) ?></p>
          <?php endif; ?>
          <p class="pcard-price"><?= formatHarga($prod['price_min'],$prod['price_max']) ?></p>
          <a href="<?= waLink('Halo, saya ingin pesan '.$prod['name']) ?>" target="_blank" class="pcard-btn">
            Pesan Sekarang
          </a>
        </div>
      </div>
      <?php endforeach; ?>
    </div>

    <?php else: ?>
    <div class="empty-state">
      <svg width="64" height="64" fill="none" stroke="#d1d5db" stroke-width="1.5" viewBox="0 0 24 24">
        <path d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
      </svg>
      <p style="font-family:'Jost',sans-serif;font-size:0.9rem;">Belum ada produk di kategori ini.</p>
    </div>
    <?php endif; ?>

    <!-- SEO Content -->
    <div class="seo-content mt-14 max-w-4xl">
      <h2>Tentang <?= $nama ?> Chika Florist</h2>
      <p>Chika Florist menyediakan berbagai pilihan <?= $nama ?> berkualitas untuk kebutuhan acara Anda. Setiap produk dibuat dari bunga segar pilihan oleh tim florist profesional kami.</p>
      <h2><?= $nama ?> untuk Berbagai Acara</h2>
      <ul>
        <?php foreach(['Wedding & Lamaran','Duka Cita & Belasungkawa','Ulang Tahun','Wisuda','Grand Opening','Anniversary','Corporate Event'] as $acara): ?>
        <li><?= $acara ?></li>
        <?php endforeach; ?>
      </ul>
      <h2>Cara Pesan <?= $nama ?></h2>
      <p>Pilih produk <?= $nama ?> yang Anda inginkan, lalu hubungi admin kami via WhatsApp. Kirim detail ucapan dan alamat pengiriman — pesanan akan segera diproses dan dikirim.</p>
      <p>Tersedia melalui layanan <a href="<?= BASE_URL ?>/">toko bunga online 24 jam Indonesia</a> Chika Florist.</p>
    </div>

    <!-- City strip SEO -->
    <?php if (!empty($cities)): ?>
    <div class="city-strip mt-8">
      <div class="city-strip-label">
        <svg width="12" height="12" fill="none" stroke="#c9a84c" stroke-width="2" viewBox="0 0 24 24"><path d="M17.657 16.657L13.414 20.9a2 2 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><circle cx="12" cy="11" r="3"/></svg>
        <?= $nama ?> Tersedia di
      </div>
      <div class="internal-links">
        <?php foreach ($cities as $city): ?>
        <a href="<?= BASE_URL ?>/toko-bunga-<?= $city['slug'] ?>"><?= $nama ?> <?= clean($city['name']) ?></a>
        <?php endforeach; ?>
      </div>
    </div>
    <?php endif; ?>

  </div>
</div>

<!-- CTA Bottom -->
<section class="cat-cta py-14 px-4 text-center">
  <div class="cat-cta-pattern"></div>
  <div class="cat-cta-glow" style="width:300px;height:300px;background:rgba(244,63,94,0.2);top:-100px;left:50%;transform:translateX(-50%);"></div>
  <div style="position:relative;z-index:2;max-width:520px;margin:0 auto;">
    <!-- SVG bunga mini -->
    <div style="margin-bottom:1rem;">
      <svg width="40" height="40" viewBox="0 0 80 80" fill="none" style="display:inline-block;opacity:0.7;">
        <?php foreach([0,60,120,180,240,300] as $r): ?>
        <ellipse cx="40" cy="22" rx="8" ry="18" fill="#fda4af" opacity="0.8" transform="rotate(<?= $r ?> 40 40)"/>
        <?php endforeach; ?>
        <circle cx="40" cy="40" r="10" fill="#f9c784"/>
      </svg>
    </div>
    <h2 style="font-family:'Cormorant Garamond',serif;font-size:1.8rem;font-weight:700;color:#fff;margin-bottom:0.5rem;">
      Pesan <em style="font-style:italic;color:#f9c784;"><?= $nama ?></em> Sekarang
    </h2>
    <p style="font-family:'Jost',sans-serif;font-size:0.88rem;color:rgba(255,255,255,0.75);margin-bottom:1.5rem;line-height:1.6;">
      Konsultasi gratis dengan admin kami — kami siap membantu Anda memilih rangkaian yang tepat.
    </p>
    <a href="<?= waLink("Halo, saya ingin pesan {$nama}") ?>" target="_blank"
       style="display:inline-flex;align-items:center;gap:0.6rem;font-family:'Jost',sans-serif;font-size:0.88rem;font-weight:700;letter-spacing:0.04em;background:#fff;color:#e11d48;padding:0.85rem 2rem;border-radius:999px;text-decoration:none;box-shadow:0 4px 20px rgba(0,0,0,0.2);transition:all 0.2s;">
      <svg width="16" height="16" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z"/><path d="M12 0C5.373 0 0 5.373 0 12c0 2.096.541 4.063 1.491 5.776L.057 23.04l5.43-1.424A11.947 11.947 0 0012 24c6.627 0 12-5.373 12-12S18.627 0 12 0zm0 21.818a9.818 9.818 0 01-5.015-1.371l-.36-.214-3.726.977.995-3.633-.235-.374A9.818 9.818 0 1112 21.818z"/></svg>
      Hubungi via WhatsApp
    </a>
  </div>
</section>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>