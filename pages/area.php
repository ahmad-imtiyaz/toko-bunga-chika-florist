<?php
require_once __DIR__ . '/../includes/config.php';
$slug = $_GET['slug'] ?? '';
$pdo  = getDB();
$stmt = $pdo->prepare("SELECT a.id, a.city_id, a.name, a.slug, a.description,
a.landmarks, a.nearby_areas, a.content,
a.meta_title, a.meta_desc, a.sort_order, a.is_active,
c.name as city_name, c.slug as city_slug
FROM areas a JOIN cities c ON a.city_id=c.id
WHERE a.slug=? AND a.is_active=1");
$stmt->execute([$slug]);
$area = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$area) { http_response_code(404); require __DIR__ . '/404.php'; exit(); }

$prodCats    = getMainCategories();
$contentHtml = $area['content'] ?? '';           // ✅ bukan $city
$landmarkNotes = $area['landmarks'] ?? '';

// Nearby areas dari kolom nearby_areas (CSV slug)
$nearby = [];
if (!empty($area['nearby_areas'])) {
    $naSlugs = array_filter(array_map('trim', explode(',', $area['nearby_areas'])));
    if (!empty($naSlugs)) {
        $in  = implode(',', array_fill(0, count($naSlugs), '?'));
        $naQ = $pdo->prepare("SELECT name, slug FROM areas WHERE slug IN ($in) AND is_active=1");
        $naQ->execute($naSlugs);
        $nearby = $naQ->fetchAll();
    }
}

$featured = $pdo->query("SELECT p.*,c.name as cat_name FROM products p JOIN categories c ON p.category_id=c.id WHERE p.is_featured=1 AND p.is_active=1 ORDER BY RAND() LIMIT 4")->fetchAll();
$nama          = clean($area['name']);
$kota          = clean($area['city_name']);
$page_title    = $area['meta_title'] ?: "Toko Bunga {$nama} {$kota} 24 Jam | Florist Terdekat – Chika Florist";
$meta_desc     = $area['meta_desc'] ?: "Toko bunga {$nama} {$kota} melayani bunga papan, buket bunga & standing flower. Florist online 24 jam dengan pengiriman cepat.";
$canonical_url = BASE_URL . '/toko-bunga-' . $area['slug'];
$breadcrumbs   = [
    ['label'=>'Beranda','url'=>'/'],
    ['label'=>'Toko Bunga '.$kota,'url'=>'/toko-bunga-'.$area['city_slug']],
    ['label'=>'Toko Bunga '.$nama]
];
require_once __DIR__ . '/../includes/header.php';
?>

<style>
@import url('https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,400;0,600;0,700;1,400;1,600&family=Jost:wght@300;400;500;600&display=swap');

/* =====================
   HERO
   ===================== */
.area-hero {
  position: relative;
  min-height: 420px;
  display: flex;
  align-items: center;
  justify-content: center;
  overflow: hidden;
  background: linear-gradient(145deg, #fdf0f3 0%, #fdf6ee 40%, #f0f4ed 100%);
}

/* Grid pattern */
.area-hero-grid {
  position: absolute;
  inset: 0;
  background-image:
    linear-gradient(rgba(201,168,76,0.12) 1px, transparent 1px),
    linear-gradient(90deg, rgba(201,168,76,0.12) 1px, transparent 1px);
  background-size: 48px 48px;
}

/* Diagonal stripe overlay */
.area-hero-stripe {
  position: absolute;
  inset: 0;
  background-image: repeating-linear-gradient(
    -45deg,
    transparent,
    transparent 18px,
    rgba(253,240,243,0.45) 18px,
    rgba(253,240,243,0.45) 36px
  );
}

/* Glow blobs */
.area-hero-blob {
  position: absolute;
  border-radius: 50%;
  filter: blur(80px);
  pointer-events: none;
}

/* Pin container */
.area-pin-wrap {
  position: absolute;
  right: 8%;
  top: 50%;
  transform: translateY(-50%);
  display: flex;
  flex-direction: column;
  align-items: center;
  z-index: 4;
  animation: pinFloat 3s ease-in-out infinite;
}
@keyframes pinFloat {
  0%,100% { transform: translateY(-50%) translateY(0); }
  50%      { transform: translateY(-50%) translateY(-12px); }
}
@media(max-width:768px) { .area-pin-wrap { display: none; } }

/* Ripple rings di bawah pin */
.pin-ripple {
  position: absolute;
  bottom: -8px;
  left: 50%;
  transform: translateX(-50%);
  width: 60px;
  height: 20px;
}
.pin-ripple-ring {
  position: absolute;
  left: 50%;
  top: 50%;
  transform: translate(-50%,-50%);
  border-radius: 50%;
  border: 2px solid rgba(225,29,72,0.25);
  animation: rippleOut 2.4s ease-out infinite;
}
.pin-ripple-ring:nth-child(2) { animation-delay: 0.8s; }
.pin-ripple-ring:nth-child(3) { animation-delay: 1.6s; }
@keyframes rippleOut {
  0%   { width:0; height:0; opacity:.8; }
  100% { width:100px; height:36px; opacity:0; }
}

/* Hero content */
.area-hero-content {
  position: relative;
  z-index: 10;
  max-width: 600px;
  padding: 3rem 1.5rem;
  animation: areaFadeUp 0.8s cubic-bezier(.22,.68,0,1.2) both;
}
@media(min-width:769px) { .area-hero-content { margin-left: 6%; margin-right: auto; } }
@keyframes areaFadeUp {
  from { opacity:0; transform:translateY(24px); }
  to   { opacity:1; transform:translateY(0); }
}

/* Breadcrumb */
.area-breadcrumb {
  display: flex;
  align-items: center;
  gap: 0.4rem;
  margin-bottom: 1rem;
  flex-wrap: wrap;
  animation: areaFadeUp 0.7s 0.1s both;
}
.area-breadcrumb a, .area-breadcrumb span {
  font-family: 'Jost', sans-serif;
  font-size: 0.7rem;
  font-weight: 600;
  letter-spacing: 0.08em;
  text-transform: uppercase;
  color: #9ca3af;
  text-decoration: none;
  transition: color .2s;
}
.area-breadcrumb a:hover { color: #e11d48; }
.area-breadcrumb .sep { color: #d1d5db; }
.area-breadcrumb .cur { color: #e11d48; }

/* Eyebrow */
.area-eyebrow {
  display: inline-flex;
  align-items: center;
  gap: 0.45rem;
  font-family: 'Jost', sans-serif;
  font-size: 0.72rem;
  font-weight: 700;
  letter-spacing: 0.1em;
  text-transform: uppercase;
  color: #e11d48;
  background: rgba(225,29,72,0.08);
  border: 1px solid rgba(225,29,72,0.18);
  padding: 0.28rem 0.85rem;
  border-radius: 999px;
  margin-bottom: 1rem;
  animation: areaFadeUp 0.7s 0.15s both;
}
.area-eyebrow-dot {
  width: 6px; height: 6px;
  border-radius: 50%;
  background: #e11d48;
  animation: pulseDot 1.8s ease-in-out infinite;
}
@keyframes pulseDot {
  0%,100% { transform: scale(1); opacity:1; }
  50%      { transform: scale(1.6); opacity:.6; }
}

.area-hero h1 {
  font-family: 'Cormorant Garamond', serif;
  font-size: clamp(1.7rem, 4vw, 2.8rem);
  font-weight: 700;
  color: #1f2937;
  line-height: 1.2;
  margin-bottom: 0.8rem;
  text-shadow: 0 1px 0 rgba(255,255,255,0.8);
  animation: areaFadeUp 0.7s 0.2s both;
}
.area-hero h1 em { font-style: italic; color: #e11d48; }

/* Gold line */
.area-gold-line {
  width: 56px; height: 2px;
  background: linear-gradient(90deg, #c9a84c, transparent);
  margin: 0.7rem 0 1rem;
  animation: areaFadeUp 0.7s 0.25s both;
}

.area-hero-desc {
  font-family: 'Jost', sans-serif;
  font-size: 0.9rem;
  color: #6b7280;
  line-height: 1.7;
  margin-bottom: 1.4rem;
  animation: areaFadeUp 0.7s 0.3s both;
}

/* Info chips */
.area-chips {
  display: flex;
  flex-wrap: wrap;
  gap: 0.5rem;
  margin-bottom: 1.5rem;
  animation: areaFadeUp 0.7s 0.35s both;
}
.area-chip {
  display: inline-flex;
  align-items: center;
  gap: 0.35rem;
  font-family: 'Jost', sans-serif;
  font-size: 0.72rem;
  font-weight: 600;
  color: #374151;
  background: rgba(255,255,255,0.85);
  border: 1px solid #fde8b4;
  padding: 0.28rem 0.75rem;
  border-radius: 999px;
  backdrop-filter: blur(4px);
}
.area-chip svg { color: #e11d48; flex-shrink: 0; }

/* CTA buttons */
.area-hero-btns {
  display: flex;
  gap: 0.75rem;
  flex-wrap: wrap;
  animation: areaFadeUp 0.7s 0.4s both;
}
.btn-wa {
  display: inline-flex;
  align-items: center;
  gap: 0.5rem;
  font-family: 'Jost', sans-serif;
  font-size: 0.85rem;
  font-weight: 700;
  color: #fff;
  background: #16a34a;
  padding: 0.75rem 1.5rem;
  border-radius: 999px;
  text-decoration: none;
  transition: all .25s;
  box-shadow: 0 4px 16px rgba(22,163,74,0.3);
}
.btn-wa:hover { background: #15803d; transform: translateY(-2px); box-shadow: 0 6px 20px rgba(22,163,74,0.35); }
.btn-scroll {
  display: inline-flex;
  align-items: center;
  gap: 0.4rem;
  font-family: 'Jost', sans-serif;
  font-size: 0.82rem;
  font-weight: 600;
  color: #6b7280;
  background: rgba(255,255,255,0.9);
  border: 1px solid #e5e7eb;
  padding: 0.75rem 1.25rem;
  border-radius: 999px;
  text-decoration: none;
  transition: all .2s;
  cursor: pointer;
}
.btn-scroll:hover { color: #e11d48; border-color: #fca5a5; background:#fff; }
.btn-scroll svg { animation: bounceDown 1.6s ease-in-out infinite; }
@keyframes bounceDown {
  0%,100% { transform: translateY(0); }
  50%      { transform: translateY(4px); }
}

/* SVG Bunga dekorasi */
.area-deco-flower {
  position: absolute;
  pointer-events: none;
  z-index: 3;
  opacity: 0.13;
}

/* Wave bawah */
.area-hero-wave {
  position: absolute;
  bottom: -1px; left: 0; right: 0;
  z-index: 8;
  line-height: 0;
}

/* =====================
   BODY
   ===================== */
.area-body {
  background: linear-gradient(170deg, #fdf6ee 0%, #fff 60%, #fdf0f3 100%);
}

/* Section header */
.sec-eyebrow {
  font-family: 'Jost', sans-serif;
  font-size: 0.7rem;
  font-weight: 700;
  letter-spacing: 0.12em;
  text-transform: uppercase;
  color: #e11d48;
  margin-bottom: 0.35rem;
}
.sec-title {
  font-family: 'Cormorant Garamond', serif;
  font-size: clamp(1.4rem, 2.5vw, 1.9rem);
  font-weight: 700;
  color: #1f2937;
}
.sec-gold {
  width: 44px; height: 2px;
  background: linear-gradient(90deg, #c9a84c, transparent);
  margin: 0.5rem 0 0;
}

/* =====================
   KEUNGGULAN CARDS
   ===================== */
.unggulan-grid {
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  gap: 0.85rem;
}
@media(min-width:640px) { .unggulan-grid { grid-template-columns: repeat(3,1fr); } }

.unggulan-card {
  background: #fff;
  border: 1px solid #fde8b4;
  border-radius: 1rem;
  padding: 1.1rem;
  transition: all .25s cubic-bezier(.22,.68,0,1.2);
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
}
.unggulan-card:hover {
  transform: translateY(-4px);
  box-shadow: 0 8px 24px rgba(214,90,110,0.1);
  border-color: #fca5a5;
}
.unggulan-icon {
  width: 40px; height: 40px;
  border-radius: 10px;
  background: linear-gradient(135deg, #fdf0f3, #fdf6ee);
  border: 1px solid #fde8b4;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: all .25s;
}
.unggulan-card:hover .unggulan-icon {
  background: linear-gradient(135deg, #ffe4e6, #fdf0f3);
  border-color: #fca5a5;
}
.unggulan-icon svg { color: #e11d48; }
.unggulan-title {
  font-family: 'Jost', sans-serif;
  font-size: 0.82rem;
  font-weight: 700;
  color: #1f2937;
}
.unggulan-desc {
  font-family: 'Jost', sans-serif;
  font-size: 0.75rem;
  color: #9ca3af;
  line-height: 1.5;
}

/* =====================
   CARA PESAN STEPS
   ===================== */
.steps-wrap {
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  gap: 1rem;
  position: relative;
}
@media(min-width:640px) { .steps-wrap { grid-template-columns: repeat(4,1fr); } }

.step-card {
  background: #fff;
  border: 1px solid #fde8b4;
  border-radius: 1rem;
  padding: 1.1rem 0.9rem 1rem;
  text-align: center;
  position: relative;
  transition: all .25s;
}
.step-card:hover {
  transform: translateY(-4px);
  box-shadow: 0 8px 24px rgba(214,90,110,0.1);
  border-color: #fca5a5;
}
.step-num {
  font-family: 'Cormorant Garamond', serif;
  font-size: 2.2rem;
  font-weight: 700;
  color: rgba(225,29,72,0.08);
  position: absolute;
  top: 0.3rem; right: 0.6rem;
  line-height: 1;
  pointer-events: none;
}
.step-icon-wrap {
  width: 44px; height: 44px;
  border-radius: 50%;
  background: linear-gradient(135deg, #ffe4e6, #fdf0f3);
  border: 2px solid #fca5a5;
  display: flex;
  align-items: center;
  justify-content: center;
  margin: 0 auto 0.65rem;
  transition: all .25s;
}
.step-card:hover .step-icon-wrap {
  background: linear-gradient(135deg, #e11d48, #be185d);
  border-color: #e11d48;
}
.step-card:hover .step-icon-wrap svg { color: #fff; }
.step-icon-wrap svg { color: #e11d48; transition: color .25s; }
.step-title {
  font-family: 'Jost', sans-serif;
  font-size: 0.8rem;
  font-weight: 700;
  color: #1f2937;
  margin-bottom: 0.3rem;
}
.step-desc {
  font-family: 'Jost', sans-serif;
  font-size: 0.72rem;
  color: #9ca3af;
  line-height: 1.5;
}

/* =====================
   LANDMARKS CHIPS
   ===================== */
.landmark-chips {
  display: flex;
  flex-wrap: wrap;
  gap: 0.5rem;
}
.landmark-chip {
  display: inline-flex;
  align-items: center;
  gap: 0.4rem;
  font-family: 'Jost', sans-serif;
  font-size: 0.75rem;
  font-weight: 500;
  color: #374151;
  background: #fff;
  border: 1px solid #fde8b4;
  padding: 0.35rem 0.85rem;
  border-radius: 999px;
  transition: all .2s;
}
.landmark-chip:hover {
  color: #e11d48;
  border-color: #fca5a5;
  background: #fff5f5;
}
.landmark-chip-dot {
  width: 6px; height: 6px;
  border-radius: 50%;
  background: #c9a84c;
  flex-shrink: 0;
}

/* =====================
   ACCORDION / FAQ
   ===================== */
.acc-item {
  background: #fff;
  border: 1px solid #fde8b4;
  border-radius: 0.875rem;
  overflow: hidden;
  transition: border-color .2s;
}
.acc-item.open { border-color: #fca5a5; }
.acc-head {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 0.75rem;
  padding: 1rem 1.1rem;
  cursor: pointer;
  user-select: none;
}
.acc-head-left {
  display: flex;
  align-items: center;
  gap: 0.7rem;
}
.acc-num {
  font-family: 'Cormorant Garamond', serif;
  font-size: 1rem;
  font-weight: 700;
  color: #e11d48;
  min-width: 1.5rem;
  opacity: 0.5;
}
.acc-item.open .acc-num { opacity: 1; }
.acc-q {
  font-family: 'Jost', sans-serif;
  font-size: 0.85rem;
  font-weight: 600;
  color: #1f2937;
}
.acc-icon {
  width: 28px; height: 28px;
  border-radius: 50%;
  background: #fdf0f3;
  border: 1px solid #fde8b4;
  display: flex; align-items: center; justify-content: center;
  flex-shrink: 0;
  transition: all .25s;
}
.acc-item.open .acc-icon {
  background: #e11d48;
  border-color: #e11d48;
  transform: rotate(45deg);
}
.acc-icon svg { color: #e11d48; transition: color .25s; }
.acc-item.open .acc-icon svg { color: #fff; }
.acc-body {
  max-height: 0;
  overflow: hidden;
  transition: max-height 0.38s cubic-bezier(.4,0,.2,1);
}
.acc-body-inner {
  padding: 0 1.1rem 1rem 3.1rem;
  font-family: 'Jost', sans-serif;
  font-size: 0.82rem;
  color: #6b7280;
  line-height: 1.7;
  border-top: 1px solid #fef3c7;
}

/* =====================
   NEARBY CARDS
   ===================== */
.nearby-grid {
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  gap: 0.75rem;
}
@media(min-width:480px) { .nearby-grid { grid-template-columns: repeat(3,1fr); } }
@media(min-width:768px) { .nearby-grid { grid-template-columns: repeat(4,1fr); } }

.nearby-card {
  display: block;
  background: #fff;
  border: 1px solid #fde8b4;
  border-radius: 0.875rem;
  padding: 0.85rem 0.9rem;
  text-decoration: none;
  transition: all .25s cubic-bezier(.22,.68,0,1.2);
  display: flex;
  align-items: center;
  gap: 0.55rem;
}
.nearby-card:hover {
  transform: translateY(-3px);
  box-shadow: 0 6px 18px rgba(214,90,110,0.1);
  border-color: #fca5a5;
  background: #fff5f5;
}
.nearby-card-pin {
  width: 30px; height: 30px;
  border-radius: 50%;
  background: linear-gradient(135deg, #fdf0f3, #fdf6ee);
  border: 1px solid #fde8b4;
  display: flex; align-items: center; justify-content: center;
  flex-shrink: 0;
  transition: all .25s;
}
.nearby-card:hover .nearby-card-pin {
  background: linear-gradient(135deg, #ffe4e6, #fdf0f3);
  border-color: #fca5a5;
}
.nearby-card-pin svg { color: #e11d48; }
.nearby-card-name {
  font-family: 'Jost', sans-serif;
  font-size: 0.78rem;
  font-weight: 600;
  color: #374151;
  line-height: 1.35;
  transition: color .2s;
}
.nearby-card:hover .nearby-card-name { color: #e11d48; }

/* =====================
   PRODUK MINI CARDS
   ===================== */
.mini-prod-grid {
  display: grid;
  grid-template-columns: repeat(2,1fr);
  gap: 0.75rem;
}
.mini-prod-card {
  background: #fff;
  border: 1px solid #fde8b4;
  border-radius: 0.875rem;
  overflow: hidden;
  text-decoration: none;
  transition: all .25s;
  display: block;
}
.mini-prod-card:hover {
  transform: translateY(-3px);
  box-shadow: 0 6px 18px rgba(214,90,110,0.1);
  border-color: #fca5a5;
}
.mini-prod-img {
  height: 90px;
  overflow: hidden;
  background: linear-gradient(135deg,#fdf0f3,#fdf6ee);
}
.mini-prod-img img {
  width:100%; height:100%;
  object-fit:cover;
  transition: transform .35s;
}
.mini-prod-card:hover .mini-prod-img img { transform: scale(1.07); }
.mini-prod-body { padding: 0.55rem 0.65rem; }
.mini-prod-name {
  font-family: 'Jost', sans-serif;
  font-size: 0.72rem;
  font-weight: 600;
  color: #374151;
  line-height: 1.35;
  margin-bottom: 0.2rem;
  display: -webkit-box;
  -webkit-line-clamp: 2;
  line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}
.mini-prod-price {
  font-family: 'Cormorant Garamond', serif;
  font-size: 0.9rem;
  font-weight: 700;
  color: #e11d48;
}

/* =====================
   PRODUK KATEGORI LIST
   ===================== */
.prodcat-list {
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
}
.prodcat-item {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  background: #fff;
  border: 1px solid #fde8b4;
  border-radius: 0.75rem;
  padding: 0.75rem 1rem;
  text-decoration: none;
  transition: all .2s;
}
.prodcat-item:hover {
  border-color: #fca5a5;
  background: #fff5f5;
  transform: translateX(4px);
}
.prodcat-dot {
  width: 8px; height: 8px;
  border-radius: 50%;
  background: #c9a84c;
  flex-shrink: 0;
}
.prodcat-name {
  font-family: 'Jost', sans-serif;
  font-size: 0.8rem;
  font-weight: 600;
  color: #374151;
  flex: 1;
  transition: color .2s;
}
.prodcat-item:hover .prodcat-name { color: #e11d48; }
.prodcat-arrow {
  color: #d1d5db;
  transition: color .2s, transform .2s;
}
.prodcat-item:hover .prodcat-arrow { color: #e11d48; transform: translateX(3px); }

/* =====================
   SEO TEXT
   ===================== */
.seo-prose {
  font-family: 'Jost', sans-serif;
  font-size: 0.85rem;
  color: #6b7280;
  line-height: 1.8;
}
.seo-prose a { color: #e11d48; text-decoration: underline; }

/* City strip */
.city-strip {
  background: linear-gradient(135deg, #fdf6ee, #fdf0f3);
  border: 1px solid #fde8b4;
  border-radius: 1rem;
  padding: 1.1rem;
}
.city-strip-label {
  font-family: 'Jost', sans-serif;
  font-size: 0.68rem;
  font-weight: 700;
  letter-spacing: 0.1em;
  text-transform: uppercase;
  color: #9ca3af;
  margin-bottom: 0.7rem;
  display: flex; align-items: center; gap: 0.5rem;
}
.city-strip-label::after {
  content: '';
  flex: 1; height: 1px;
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
  transition: all .2s;
}
.internal-links a:hover { color: #e11d48; border-color: #fca5a5; background: #fff5f5; }

/* =====================
   CTA BOTTOM
   ===================== */
.area-cta {
  background: linear-gradient(135deg, #881337 0%, #9f1239 50%, #7f1d1d 100%);
  position: relative;
  overflow: hidden;
}
.area-cta-pat {
  position: absolute; inset: 0;
  background-image:
    repeating-linear-gradient(45deg, rgba(255,255,255,0.03) 0, rgba(255,255,255,0.03) 1px, transparent 0, transparent 50%),
    repeating-linear-gradient(-45deg, rgba(255,255,255,0.03) 0, rgba(255,255,255,0.03) 1px, transparent 0, transparent 50%);
  background-size: 28px 28px;
}
/* ── LOC CONTENT SEO ── */
.loc-content h1 { font-family:'Cormorant Garamond',Georgia,serif; font-size:1.7rem; font-weight:700; color:#1f2937; margin-bottom:.9rem; margin-top:1.4rem; line-height:1.2; }
.loc-content h2 { font-family:'Cormorant Garamond',Georgia,serif; font-size:1.35rem; font-weight:700; color:#1f2937; margin-bottom:.7rem; margin-top:1.2rem; line-height:1.3; }
.loc-content h3 { font-family:'Cormorant Garamond',Georgia,serif; font-size:1.1rem; font-weight:600; color:#e11d48; margin-bottom:.45rem; margin-top:.9rem; }
.loc-content p  { margin-bottom:.75rem; color:#6b7280; font-size:.9rem; line-height:1.8; }
.loc-content ul { list-style:disc; padding-left:1.5rem; margin-bottom:.75rem; }
.loc-content ol { list-style:decimal; padding-left:1.5rem; margin-bottom:.75rem; }
.loc-content li { margin-bottom:.25rem; color:#6b7280; font-size:.88rem; line-height:1.7; }
.loc-content strong { color:#1f2937; font-weight:700; }
.loc-content em { color:#e11d48; font-style:italic; }
.loc-content a  { color:#e11d48; text-decoration:underline; transition:color .2s; }
.loc-content a:hover { color:#be185d; }
</style>

<!-- ==============================
     HERO
     ============================== -->
<section class="area-hero" id="area-hero">

  <!-- Grid pattern -->
  <div class="area-hero-grid"></div>
  <div class="area-hero-stripe"></div>

  <!-- Glow blobs -->
  <div class="area-hero-blob" style="width:360px;height:360px;background:rgba(253,164,175,0.25);top:-80px;left:-60px;"></div>
  <div class="area-hero-blob" style="width:280px;height:280px;background:rgba(201,168,76,0.15);bottom:-60px;right:30%;"></div>
  <div class="area-hero-blob" style="width:200px;height:200px;background:rgba(187,247,208,0.2);top:20%;right:20%;"></div>

  <!-- SVG Bunga dekorasi TL -->
  <svg class="area-deco-flower" style="top:-20px;left:-20px;width:200px;height:200px;" viewBox="0 0 200 200" fill="none">
    <?php foreach([0,60,120,180,240,300] as $r): ?>
    <ellipse cx="100" cy="55" rx="20" ry="42" fill="#e11d48" transform="rotate(<?= $r ?> 100 100)"/>
    <?php endforeach; ?>
    <circle cx="100" cy="100" r="22" fill="#c9a84c"/>
    <line x1="100" y1="148" x2="100" y2="200" stroke="#6b7c5c" stroke-width="4"/>
    <ellipse cx="78" cy="178" rx="16" ry="9" fill="#6b7c5c" transform="rotate(-30 78 178)"/>
  </svg>

  <!-- SVG Bunga BR -->
  <svg class="area-deco-flower" style="bottom:-15px;left:22%;width:160px;height:120px;opacity:0.09;" viewBox="0 0 160 120" fill="none">
    <?php foreach([0,72,144,216,288] as $r): ?>
    <ellipse cx="80" cy="35" rx="13" ry="28" fill="#c9a84c" transform="rotate(<?= $r ?> 80 60)"/>
    <?php endforeach; ?>
    <circle cx="80" cy="60" r="14" fill="#f9c784"/>
  </svg>

  <!-- PIN LOKASI SVG (desktop only) -->
  <div class="area-pin-wrap">
    <!-- Ripple rings -->
    <div class="pin-ripple">
      <div class="pin-ripple-ring"></div>
      <div class="pin-ripple-ring"></div>
      <div class="pin-ripple-ring"></div>
    </div>
    <!-- Pin SVG -->
    <svg width="80" height="110" viewBox="0 0 80 110" fill="none">
      <!-- Shadow -->
      <ellipse cx="40" cy="107" rx="18" ry="5" fill="rgba(0,0,0,0.1)"/>
      <!-- Pin body -->
      <path d="M40 4C23.43 4 10 17.43 10 34c0 20 30 68 30 68s30-48 30-68C70 17.43 56.57 4 40 4z"
            fill="url(#pinGrad)" filter="url(#pinShadow)"/>
      <defs>
        <linearGradient id="pinGrad" x1="10" y1="4" x2="70" y2="70" gradientUnits="userSpaceOnUse">
          <stop offset="0%" stop-color="#fb7185"/>
          <stop offset="100%" stop-color="#be185d"/>
        </linearGradient>
        <filter id="pinShadow" x="-20%" y="-20%" width="140%" height="140%">
          <feDropShadow dx="0" dy="4" stdDeviation="4" flood-color="rgba(190,24,93,0.35)"/>
        </filter>
      </defs>
      <!-- Inner circle -->
      <circle cx="40" cy="33" r="14" fill="white" opacity="0.95"/>
      <!-- Flower mini di dalam pin -->
      <?php foreach([0,72,144,216,288] as $r): ?>
      <ellipse cx="40" cy="25" rx="4" ry="8" fill="#e11d48" opacity="0.8" transform="rotate(<?= $r ?> 40 33)"/>
      <?php endforeach; ?>
      <circle cx="40" cy="33" r="5" fill="#c9a84c"/>
    </svg>

    <!-- Label nama area -->
    <div style="margin-top:0.5rem;text-align:center;">
      <div style="background:white;border:1px solid #fde8b4;border-radius:0.75rem;padding:0.5rem 1rem;box-shadow:0 4px 16px rgba(0,0,0,0.08);">
        <p style="font-family:'Jost',sans-serif;font-size:0.7rem;font-weight:700;letter-spacing:.06em;text-transform:uppercase;color:#9ca3af;margin-bottom:2px;">Melayani</p>
        <p style="font-family:'Cormorant Garamond',serif;font-size:1.1rem;font-weight:700;color:#1f2937;white-space:nowrap;"><?= $nama ?></p>
        <p style="font-family:'Jost',sans-serif;font-size:0.7rem;color:#e11d48;font-weight:600;"><?= $kota ?></p>
      </div>
    </div>
  </div>

  <!-- Konten hero kiri -->
  <div class="area-hero-content">

    <nav class="area-breadcrumb">
      <a href="<?= BASE_URL ?>/">Beranda</a>
      <span class="sep">❯</span>
      <a href="<?= BASE_URL ?>/toko-bunga-<?= $area['city_slug'] ?>">Toko Bunga <?= $kota ?></a>
      <span class="sep">❯</span>
      <span class="cur">Toko Bunga <?= $nama ?></span>
    </nav>

    <div class="area-eyebrow">
      <span class="area-eyebrow-dot"></span>
      Florist Online 24 Jam
    </div>

    <h1>
      Toko Bunga<br>
      <em><?= $nama ?></em> <?= $kota ?>
    </h1>

    <div class="area-gold-line"></div>

    <p class="area-hero-desc">
      Chika Florist melayani pemesanan & pengiriman bunga di wilayah <?= $nama ?>, <?= $kota ?> —
      kapan saja, dengan bunga segar berkualitas dan pengiriman cepat.
    </p>

    <div class="area-chips">
      <span class="area-chip">
        <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
        Layanan 24 Jam
      </span>
      <span class="area-chip">
        <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
        Same Day Delivery
      </span>
      <span class="area-chip">
        <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M17.657 16.657L13.414 20.9a2 2 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><circle cx="12" cy="11" r="3"/></svg>
        Area <?= $nama ?>
      </span>
      <span class="area-chip">
        <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 22s-8-4.5-8-11.8A8 8 0 0112 2a8 8 0 018 8.2c0 7.3-8 11.8-8 11.8z"/></svg>
        Bunga Fresh
      </span>
    </div>

    <div class="area-hero-btns">
      <a href="<?= waLink("Halo Chika Florist, saya ingin pesan bunga di {$nama} {$kota}") ?>" target="_blank" class="btn-wa">
        <svg width="16" height="16" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z"/><path d="M12 0C5.373 0 0 5.373 0 12c0 2.096.541 4.063 1.491 5.776L.057 23.04l5.43-1.424A11.947 11.947 0 0012 24c6.627 0 12-5.373 12-12S18.627 0 12 0zm0 21.818a9.818 9.818 0 01-5.015-1.371l-.36-.214-3.726.977.995-3.633-.235-.374A9.818 9.818 0 1112 21.818z"/></svg>
        Pesan Bunga di <?= $nama ?>
      </a>
      <a href="#konten" class="btn-scroll" onclick="document.getElementById('konten').scrollIntoView({behavior:'smooth'});return false;">
        Selengkapnya
        <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M12 5v14M5 12l7 7 7-7"/></svg>
      </a>
    </div>

  </div>

  <!-- Wave bawah -->
  <div class="area-hero-wave">
    <svg viewBox="0 0 1440 56" preserveAspectRatio="none" style="width:100%;height:56px;display:block;">
      <path d="M0,28 C360,56 720,0 1080,28 C1260,42 1380,14 1440,28 L1440,56 L0,56 Z" fill="#fdf6ee"/>
    </svg>
  </div>
</section>

<!-- ==============================
     BODY
     ============================== -->
<div class="area-body" id="konten">
  <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-12 lg:py-16">

    <!-- KEUNGGULAN -->
    <div class="mb-12">
      <p class="sec-eyebrow">Kenapa Kami</p>
      <h2 class="sec-title">Keunggulan Layanan di <?= $nama ?></h2>
      <div class="sec-gold"></div>
      <div class="unggulan-grid mt-5">
        <?php
        $unggulan = [
          ['icon'=>'<path d="M12 2a10 10 0 100 20A10 10 0 0012 2zm0 18a8 8 0 110-16 8 8 0 010 16zm.5-13H11v6l5.25 3.15.75-1.23-4.5-2.67V7z"/>','title'=>'Layanan 24 Jam','desc'=>'Pesan kapan saja, termasuk tengah malam dan hari libur nasional.'],
          ['icon'=>'<path d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/>','title'=>'Same Day Delivery','desc'=>'Pengiriman di hari yang sama untuk area '.$nama.' dan sekitarnya.'],
          ['icon'=>'<path d="M12 22s-8-4.5-8-11.8A8 8 0 0112 2a8 8 0 018 8.2c0 7.3-8 11.8-8 11.8z"/><circle cx="12" cy="10" r="3"/>','title'=>'Area '.$nama,'desc'=>'Kami familiar dengan seluruh wilayah '.$nama.' untuk pengiriman tepat sasaran.'],
          ['icon'=>'<path d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>','title'=>'Admin Responsif','desc'=>'Tim kami siap membalas pesan WhatsApp dengan cepat dan ramah.'],
          ['icon'=>'<path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>','title'=>'Bunga Fresh','desc'=>'Setiap rangkaian dibuat dari bunga segar pilihan dengan kualitas terjaga.'],
          ['icon'=>'<path d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>','title'=>'Harga Transparan','desc'=>'Tidak ada biaya tersembunyi. Harga diinformasikan sejak awal konsultasi.'],
        ];
        foreach ($unggulan as $u): ?>
        <div class="unggulan-card">
          <div class="unggulan-icon">
            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><?= $u['icon'] ?></svg>
          </div>
          <div class="unggulan-title"><?= $u['title'] ?></div>
          <div class="unggulan-desc"><?= $u['desc'] ?></div>
        </div>
        <?php endforeach; ?>
      </div>
    </div>

   <!-- LANDMARKS -->
  <?php if (!empty($landmarkNotes)): ?>
  <div class="mb-12">
    <p class="sec-eyebrow">Jangkauan Area</p>
    <h2 class="sec-title">Area Pengiriman di <?= $nama ?></h2>
    <div class="sec-gold"></div>
    <p class="seo-prose mt-3 mb-4">Pengiriman bunga mencakup seluruh wilayah berikut di sekitar <?= $nama ?>:</p>
    <div class="landmark-chips">
      <?php
      $lmarks = array_filter(array_map('trim', explode(',', str_replace(["\n",';'], ',', $landmarkNotes))));
      foreach ($lmarks as $lm): if (empty($lm)) continue; ?>
      <span class="landmark-chip">
        <span class="landmark-chip-dot"></span>
        <?= clean($lm) ?>
      </span>
      <?php endforeach; ?>
    </div>
  </div>
  <?php endif; ?>

  <!-- KONTEN SEO -->
  <?php if (!empty($contentHtml)): ?>
  <div class="mb-12">
    <p class="sec-eyebrow">Tentang Kami</p>
    <h2 class="sec-title">Toko Bunga <?= $nama ?> <?= $kota ?> Terpercaya</h2>
    <div class="sec-gold"></div>
    <div class="mt-5 rounded-2xl p-6 sm:p-8"
         style="background:#fff;border:1px solid #fde8b4;box-shadow:0 4px 20px rgba(201,168,76,0.08);">
      <div class="loc-content">
        <?= $contentHtml ?>
      </div>
    </div>
  </div>
  <?php endif; ?>


    <!-- CARA PESAN -->
    <div class="mb-12">
      <p class="sec-eyebrow">Panduan</p>
      <h2 class="sec-title">Cara Pesan Bunga di <?= $nama ?></h2>
      <div class="sec-gold"></div>
      <div class="steps-wrap mt-5">
        <?php
        $steps = [
          ['icon'=>'<path d="M4 6h16M4 12h16M4 18h7"/>','title'=>'Pilih Produk','desc'=>'Pilih rangkaian bunga sesuai acara & budget di website kami.'],
          ['icon'=>'<path d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>','title'=>'Hubungi WhatsApp','desc'=>'Hubungi admin Chika Florist via WhatsApp — siap 24 jam.'],
          ['icon'=>'<path d="M17.657 16.657L13.414 20.9a2 2 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><circle cx="12" cy="11" r="3"/>','title'=>'Kirim Alamat','desc'=>'Kirim ucapan & alamat lengkap di area '.$nama.'.'],
          ['icon'=>'<path d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/>','title'=>'Bunga Dikirim','desc'=>'Pesanan diproses & bunga dikirim cepat ke alamat Anda.'],
        ];
        foreach ($steps as $i => $s): ?>
        <div class="step-card">
          <span class="step-num">0<?= $i+1 ?></span>
          <div class="step-icon-wrap">
            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><?= $s['icon'] ?></svg>
          </div>
          <div class="step-title"><?= $s['title'] ?></div>
          <div class="step-desc"><?= $s['desc'] ?></div>
        </div>
        <?php endforeach; ?>
      </div>
      <div class="mt-6 text-center">
        <a href="<?= waLink("Halo, saya ingin pesan bunga di {$nama} {$kota}") ?>" target="_blank" class="btn-wa" style="display:inline-flex;">
          <svg width="16" height="16" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z"/><path d="M12 0C5.373 0 0 5.373 0 12c0 2.096.541 4.063 1.491 5.776L.057 23.04l5.43-1.424A11.947 11.947 0 0012 24c6.627 0 12-5.373 12-12S18.627 0 12 0zm0 21.818a9.818 9.818 0 01-5.015-1.371l-.36-.214-3.726.977.995-3.633-.235-.374A9.818 9.818 0 1112 21.818z"/></svg>
          Pesan Sekarang via WhatsApp
        </a>
      </div>
    </div>

    <!-- ACCORDION FAQ + SEO -->
    <div class="mb-12">
      <p class="sec-eyebrow">Info Lengkap</p>
      <h2 class="sec-title">Layanan Bunga di <?= $nama ?></h2>
      <div class="sec-gold"></div>
      <div class="space-y-2 mt-5" id="acc-wrap">
        <?php
        $faqs = [
          ['q'=>'Layanan apa saja yang tersedia di '.$nama.'?',
           'a'=>'Chika Florist menyediakan berbagai rangkaian bunga untuk kebutuhan wedding, ulang tahun, duka cita, wisuda, grand opening, anniversary, dan corporate event. Semua tersedia dengan layanan florist online 24 jam.'],
          ['q'=>'Apakah bisa kirim bunga hari yang sama di '.$nama.'?',
           'a'=>'Ya! Tersedia layanan same day delivery untuk area '.$nama.' dan sekitarnya. Hubungi admin untuk konfirmasi ketersediaan jadwal pengiriman.'],
          ['q'=>'Apakah bisa pesan bunga tengah malam?',
           'a'=>'Bisa. Layanan florist online kami tersedia 24 jam penuh — termasuk malam hari, weekend, dan hari libur nasional.'],
          ['q'=>'Bagaimana cara menambahkan ucapan di karangan bunga?',
           'a'=>'Cukup informasikan teks ucapan yang diinginkan saat menghubungi admin via WhatsApp. Kami akan menyiapkan kartu ucapan sesuai permintaan.'],
          ['q'=>'Apakah ada minimum pemesanan di '.$nama.'?',
           'a'=>'Tidak ada minimum pemesanan khusus. Kami melayani semua ukuran pesanan dari buket sederhana hingga standing flower besar untuk acara.'],
        ];
        // Tambah FAQ dari DB kalau ada
        if (!empty($area['description'])) {
            $faqs[] = ['q'=>'Tentang Layanan Chika Florist di '.$nama, 'a'=>clean($area['description'])];
        }
        foreach ($faqs as $fi => $faq): ?>
        <div class="acc-item <?= $fi===0?'open':'' ?>" data-acc>
          <div class="acc-head" onclick="toggleAcc(this)">
            <div class="acc-head-left">
              <span class="acc-num">0<?= $fi+1 ?></span>
              <span class="acc-q"><?= $faq['q'] ?></span>
            </div>
            <div class="acc-icon">
              <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M12 5v14M5 12h14"/></svg>
            </div>
          </div>
          <div class="acc-body" style="<?= $fi===0?'max-height:300px':'' ?>">
            <div class="acc-body-inner"><?= $faq['a'] ?></div>
          </div>
        </div>
        <?php endforeach; ?>
      </div>
    </div>

    <!-- PRODUK POPULER -->
    <?php if (!empty($featured)): ?>
    <div class="mb-12">
      <p class="sec-eyebrow">Terlaris</p>
      <h2 class="sec-title">Produk Populer di <?= $nama ?></h2>
      <div class="sec-gold"></div>
      <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mt-5">
        <?php foreach ($featured as $prod): ?>
        <a href="<?= waLink('Halo, saya ingin pesan '.$prod['name'].' di '.$nama) ?>" target="_blank" class="mini-prod-card">
          <div class="mini-prod-img">
            <img src="<?= UPLOAD_URL.($prod['image']??'') ?>" alt="<?= clean($prod['name']) ?> <?= $nama ?>" onerror="this.style.display='none'">
          </div>
          <div class="mini-prod-body">
            <div class="mini-prod-name"><?= clean($prod['name']) ?></div>
            <div class="mini-prod-price"><?= formatHarga($prod['price_min'],$prod['price_max']) ?></div>
          </div>
        </a>
        <?php endforeach; ?>
      </div>
    </div>
    <?php endif; ?>

    <!-- KATEGORI PRODUK LIST -->
    <div class="mb-12">
      <p class="sec-eyebrow">Pilihan Bunga</p>
      <h2 class="sec-title">Karangan Bunga di <?= $nama ?></h2>
      <div class="sec-gold"></div>
      <div class="prodcat-list mt-4">
        <?php foreach ($prodCats as $cat): ?>
        <a href="<?= BASE_URL ?>/<?= $cat['slug'] ?>" class="prodcat-item">
          <span class="prodcat-dot"></span>
          <span class="prodcat-name"><?= clean($cat['name']) ?> <?= $nama ?></span>
          <svg class="prodcat-arrow" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9 18l6-6-6-6"/></svg>
        </a>
        <?php endforeach; ?>
        <a href="<?= BASE_URL ?>/toko-bunga-online-24-jam-indonesia" class="prodcat-item">
          <span class="prodcat-dot" style="background:#e11d48;"></span>
          <span class="prodcat-name" style="color:#e11d48;">Toko Bunga Online 24 Jam Indonesia →</span>
        </a>
      </div>
    </div>

    <!-- AREA SEKITAR -->
    <?php if (!empty($nearby)): ?>
    <div class="mb-12">
      <p class="sec-eyebrow">Wilayah Sekitar</p>
      <h2 class="sec-title">Area Lain di <?= $kota ?></h2>
      <div class="sec-gold"></div>
      <div class="nearby-grid mt-5">
        <?php foreach ($nearby as $n): ?>
        <a href="<?= BASE_URL ?>/toko-bunga-<?= $n['slug'] ?>" class="nearby-card">
          <div class="nearby-card-pin">
            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M17.657 16.657L13.414 20.9a2 2 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><circle cx="12" cy="11" r="3"/></svg>
          </div>
          <span class="nearby-card-name">Toko Bunga <?= clean($n['name']) ?></span>
        </a>
        <?php endforeach; ?>
        <a href="<?= BASE_URL ?>/toko-bunga-<?= $area['city_slug'] ?>" class="nearby-card" style="border-color:#fca5a5;background:#fff5f5;">
          <div class="nearby-card-pin" style="background:linear-gradient(135deg,#ffe4e6,#fdf0f3);border-color:#fca5a5;">
            <svg width="14" height="14" fill="none" stroke="#e11d48" stroke-width="2" viewBox="0 0 24 24"><path d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
          </div>
          <span class="nearby-card-name" style="color:#e11d48;">Semua Area <?= $kota ?></span>
        </a>
      </div>
    </div>
    <?php endif; ?>

    <!-- SEO STRIP -->
    <div class="city-strip mb-4">
      <div class="city-strip-label">
        <svg width="11" height="11" fill="none" stroke="#c9a84c" stroke-width="2" viewBox="0 0 24 24"><path d="M17.657 16.657L13.414 20.9a2 2 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><circle cx="12" cy="11" r="3"/></svg>
        Produk Populer di <?= $nama ?>
      </div>
      <div class="internal-links">
        <?php foreach ($prodCats as $cat): ?>
        <a href="<?= BASE_URL ?>/<?= $cat['slug'] ?>">pesan <?= clean($cat['name']) ?> di <?= $nama ?></a>
        <?php endforeach; ?>
        <a href="<?= BASE_URL ?>/toko-bunga-online-24-jam-indonesia">toko bunga online 24 jam</a>
      </div>
    </div>

  </div>
</div>

<!-- CTA BOTTOM -->
<section class="area-cta py-14 px-4 text-center">
  <div class="area-cta-pat"></div>
  <div style="position:absolute;width:320px;height:320px;border-radius:50%;background:rgba(244,63,94,0.18);filter:blur(80px);top:-80px;left:50%;transform:translateX(-50%);pointer-events:none;"></div>
  <div style="position:relative;z-index:2;max-width:500px;margin:0 auto;">
    <svg width="36" height="36" viewBox="0 0 80 80" fill="none" style="display:inline-block;margin-bottom:0.75rem;opacity:.7;">
      <?php foreach([0,60,120,180,240,300] as $r): ?>
      <ellipse cx="40" cy="22" rx="8" ry="18" fill="#fda4af" transform="rotate(<?= $r ?> 40 40)"/>
      <?php endforeach; ?>
      <circle cx="40" cy="40" r="10" fill="#f9c784"/>
    </svg>
    <h2 style="font-family:'Cormorant Garamond',serif;font-size:1.8rem;font-weight:700;color:#fff;margin-bottom:0.5rem;">
      Pesan Bunga di <em style="font-style:italic;color:#f9c784;"><?= $nama ?></em>
    </h2>
    <p style="font-family:'Jost',sans-serif;font-size:0.85rem;color:rgba(255,255,255,0.72);margin-bottom:1.5rem;line-height:1.65;">
      Hubungi kami kapan saja — layanan florist online 24 jam siap membantu.
    </p>
    <a href="<?= waLink("Halo, saya ingin pesan bunga di {$nama} {$kota}") ?>" target="_blank"
       style="display:inline-flex;align-items:center;gap:0.6rem;font-family:'Jost',sans-serif;font-size:0.88rem;font-weight:700;background:#fff;color:#e11d48;padding:0.85rem 2rem;border-radius:999px;text-decoration:none;box-shadow:0 4px 20px rgba(0,0,0,0.2);transition:all .2s;">
      <svg width="16" height="16" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z"/><path d="M12 0C5.373 0 0 5.373 0 12c0 2.096.541 4.063 1.491 5.776L.057 23.04l5.43-1.424A11.947 11.947 0 0012 24c6.627 0 12-5.373 12-12S18.627 0 12 0zm0 21.818a9.818 9.818 0 01-5.015-1.371l-.36-.214-3.726.977.995-3.633-.235-.374A9.818 9.818 0 1112 21.818z"/></svg>
      Hubungi via WhatsApp
    </a>
  </div>
</section>

<script>
function toggleAcc(head) {
  var item  = head.closest('[data-acc]');
  var body  = item.querySelector('.acc-body');
  var inner = body.querySelector('.acc-body-inner');
  var isOpen = item.classList.contains('open');

  // tutup semua
  document.querySelectorAll('[data-acc].open').forEach(function(el){
    el.classList.remove('open');
    el.querySelector('.acc-body').style.maxHeight = '0';
  });

  if (!isOpen) {
    item.classList.add('open');
    body.style.maxHeight = inner.scrollHeight + 'px';
  }
}
// Init: buka item pertama
document.addEventListener('DOMContentLoaded', function(){
  var first = document.querySelector('[data-acc]');
  if (first) {
    var b = first.querySelector('.acc-body');
    b.style.maxHeight = b.querySelector('.acc-body-inner').scrollHeight + 'px';
  }
});
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>