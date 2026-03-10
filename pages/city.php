<?php
require_once __DIR__ . '/../includes/config.php';
$slug = $_GET['slug'] ?? '';
$pdo  = getDB();
$stmt = $pdo->prepare("SELECT * FROM cities WHERE slug=? AND is_active=1");
$stmt->execute([$slug]);
$city = $stmt->fetch();
if (!$city) { http_response_code(404); require __DIR__ . '/404.php'; exit(); }

$areas     = getAreasByCity($city['id']);
$prodCats  = getMainCategories();
$nearby    = $pdo->query("SELECT name,slug FROM cities WHERE is_active=1 AND id!={$city['id']} ORDER BY tier ASC LIMIT 8")->fetchAll();
$featured  = $pdo->query("SELECT p.*,c.name as cat_name FROM products p JOIN categories c ON p.category_id=c.id WHERE p.is_featured=1 AND p.is_active=1 ORDER BY RAND() LIMIT 4")->fetchAll();

$kota          = clean($city['name']);
$provinsi      = clean($city['province'] ?? '');
$tierLabel     = ($city['tier'] == 1) ? 'Kota Utama' : (($city['tier'] == 2) ? 'Kota Besar' : 'Kota Layanan');
$page_title    = $city['meta_title'] ?: "Toko Bunga {$kota} 24 Jam | Florist & Kirim Bunga Cepat – Chika Florist";
$meta_desc     = $city['meta_desc'] ?: "Toko bunga {$kota} melayani bunga papan, buket bunga & standing flower. Layanan florist online 24 jam dengan pengiriman cepat.";
$canonical_url = BASE_URL . '/toko-bunga-' . $city['slug'];
$breadcrumbs   = [['label'=>'Beranda','url'=>'/'],['label'=>'Toko Bunga '.$kota]];
require_once __DIR__ . '/../includes/header.php';
?>

<style>
@import url('https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,400;0,600;0,700;1,400;1,600&family=Jost:wght@300;400;500;600;700&display=swap');

/* ==============================
   CSS VARIABLES — BRIGHT FLORAL
   ============================== */
:root {
  --rose:      #c0485a;
  --rose-soft: #e8a0a8;
  --sage:      #6a9e70;
  --sage-soft: #9aba88;
  --cream:     #faf7f4;
  --warm:      #3a2420;
  --gold:      #c9a84c;
  --blush:     #fce8ec;
}

/* ==============================
   HERO — BRIGHT FLORAL
   ============================== */
.city-hero {
  position: relative;
  min-height: 560px;
  display: flex;
  flex-direction: column;
  justify-content: flex-end;
  overflow: hidden;
  background: var(--cream);
}

/* Background image — pwutih.jpeg */
.city-hero-bgimg {
  position: absolute;
  inset: 0;
  background-image: url('<?= BASE_URL ?>/assets/images/pwutih.jpeg');
  background-size: cover;
  background-position: center 30%;
  background-repeat: no-repeat;
  z-index: 0;
  opacity: 0.18;
}

/* Gradient overlay — light, airy */
.city-hero-overlay {
  position: absolute;
  inset: 0;
  z-index: 1;
  background:
    radial-gradient(ellipse 90% 70% at 50% 0%,   rgba(255,252,248,0.72) 0%, transparent 65%),
    radial-gradient(ellipse 60% 50% at 15% 50%,  rgba(255,230,235,0.35) 0%, transparent 60%),
    radial-gradient(ellipse 50% 60% at 85% 40%,  rgba(210,235,205,0.28) 0%, transparent 60%),
    linear-gradient(180deg,
      rgba(253,248,242,0.80) 0%,
      rgba(252,246,240,0.65) 40%,
      rgba(250,243,236,0.88) 75%,
      rgba(248,241,234,0.98) 100%
    );
}

/* Floral corner accents */
.city-hero-corner {
  position: absolute;
  pointer-events: none;
  z-index: 2;
  background-image: url('<?= BASE_URL ?>/assets/images/pwutih.jpeg');
  background-size: cover;
  border-radius: 50%;
  filter: saturate(0.7) brightness(1.05);
}
.city-hero-corner-tl {
  width: 380px; height: 380px;
  top: -120px; left: -100px;
  background-position: center;
  opacity: 0.20;
}
.city-hero-corner-tr {
  width: 280px; height: 280px;
  top: -70px; right: -70px;
  background-position: center;
  opacity: 0.14;
}
.city-hero-corner-br {
  width: 240px; height: 240px;
  bottom: 60px; right: -40px;
  background-position: bottom center;
  opacity: 0.12;
}

/* Atmospheric soft glow orbs */
.city-hero-atm {
  position: absolute;
  inset: 0;
  pointer-events: none;
  z-index: 2;
}
.city-glow {
  position: absolute;
  border-radius: 50%;
  filter: blur(90px);
}

/* Floating petals */
.city-petals {
  position: absolute;
  inset: 0;
  pointer-events: none;
  z-index: 3;
  overflow: hidden;
}
.city-petal {
  position: absolute;
  border-radius: 50% 0 50% 0;
  animation: cityPetalFall linear infinite;
  opacity: 0;
}
@keyframes cityPetalFall {
  0%   { opacity:0; transform: translateY(-20px) rotate(0deg) translateX(0); }
  8%   { opacity: 0.55; }
  92%  { opacity: 0.25; }
  100% { opacity:0; transform: translateY(110vh) rotate(420deg) translateX(30px); }
}

/* City silhouette SVG — now lighter */
.city-silhouette {
  position: absolute;
  bottom: 0;
  left: 0; right: 0;
  z-index: 4;
  pointer-events: none;
  line-height: 0;
}

/* Garden overlay */
.city-garden {
  position: absolute;
  bottom: 0;
  left: 0; right: 0;
  z-index: 5;
  pointer-events: none;
  line-height: 0;
}

/* ── Hero Content ── */
.city-hero-content {
  position: relative;
  z-index: 10;
  max-width: 720px;
  margin: 0 auto;
  padding: 3.5rem 1.5rem 5.5rem;
  text-align: center;
  animation: cityFadeUp .9s cubic-bezier(.22,.68,0,1.2) both;
}
@keyframes cityFadeUp {
  from { opacity:0; transform:translateY(32px); }
  to   { opacity:1; transform:translateY(0); }
}

/* Breadcrumb */
.city-breadcrumb {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: .4rem;
  margin-bottom: 1.2rem;
  flex-wrap: wrap;
  animation: cityFadeUp .7s .1s both;
}
.city-breadcrumb a, .city-breadcrumb span {
  font-family: 'Jost', sans-serif;
  font-size: .68rem;
  font-weight: 600;
  letter-spacing: .09em;
  text-transform: uppercase;
  color: rgba(58,36,32,.40);
  text-decoration: none;
  transition: color .2s;
}
.city-breadcrumb a:hover { color: var(--rose); }
.city-breadcrumb .sep    { color: rgba(58,36,32,.2); }
.city-breadcrumb .cur    { color: var(--rose); }

/* Province badge */
.city-prov-badge {
  display: inline-flex;
  align-items: center;
  gap: .4rem;
  font-family: 'Jost', sans-serif;
  font-size: .68rem;
  font-weight: 700;
  letter-spacing: .1em;
  text-transform: uppercase;
  color: #7a4f3a;
  background: rgba(255,255,255,.65);
  backdrop-filter: blur(10px);
  border: 1px solid rgba(192,72,90,.25);
  padding: .28rem .85rem;
  border-radius: 999px;
  margin-bottom: 1rem;
  box-shadow: 0 2px 12px rgba(192,72,90,.08);
  animation: cityFadeUp .7s .15s both;
}

/* Tier pill */
.city-tier {
  display: inline-block;
  font-family: 'Jost', sans-serif;
  font-size: .63rem;
  font-weight: 700;
  letter-spacing: .07em;
  text-transform: uppercase;
  color: #fff;
  background: rgba(192,72,90,.75);
  padding: .15rem .6rem;
  border-radius: 999px;
  margin-left: .35rem;
  vertical-align: middle;
}

/* H1 */
.city-hero h1 {
  font-family: 'Cormorant Garamond', serif;
  font-size: clamp(2rem, 6vw, 3.8rem);
  font-weight: 700;
  color: var(--warm);
  line-height: 1.1;
  margin-bottom: .8rem;
  animation: cityFadeUp .7s .2s both;
}
.city-hero h1 em {
  font-style: italic;
  background: linear-gradient(135deg, #c0485a, #e8778a);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  background-clip: text;
}

/* Rose divider */
.city-divider {
  width: 70px; height: 2px;
  background: linear-gradient(90deg, transparent, var(--rose), transparent);
  margin: .8rem auto 1rem;
  animation: cityFadeUp .7s .25s both;
}

/* SVG floral ornament */
.city-hero-ornament {
  margin: 0 auto .8rem;
  animation: cityFadeUp .7s .18s both;
}

.city-hero-desc {
  font-family: 'Jost', sans-serif;
  font-size: .92rem;
  color: rgba(58,36,32,.60);
  line-height: 1.75;
  max-width: 520px;
  margin: 0 auto 1.5rem;
  animation: cityFadeUp .7s .3s both;
}

/* Stats row */
.city-stats {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 0;
  background: rgba(255,255,255,.70);
  backdrop-filter: blur(14px);
  border: 1px solid rgba(192,72,90,.15);
  border-radius: 1rem;
  overflow: hidden;
  box-shadow: 0 4px 24px rgba(192,72,90,.08), 0 1px 4px rgba(0,0,0,.04);
  animation: cityFadeUp .7s .35s both;
  max-width: 420px;
  margin: 0 auto 1.6rem;
}
.city-stat {
  flex: 1;
  padding: .85rem .5rem;
  text-align: center;
  border-right: 1px solid rgba(192,72,90,.10);
}
.city-stat:last-child { border-right: none; }
.city-stat-num {
  font-family: 'Cormorant Garamond', serif;
  font-size: 1.6rem;
  font-weight: 700;
  color: var(--rose);
  line-height: 1;
  display: block;
}
.city-stat-label {
  font-family: 'Jost', sans-serif;
  font-size: .60rem;
  font-weight: 600;
  letter-spacing: .07em;
  text-transform: uppercase;
  color: rgba(58,36,32,.40);
  margin-top: .2rem;
  display: block;
}

/* CTA Buttons */
.city-hero-btns {
  display: flex;
  gap: .75rem;
  justify-content: center;
  flex-wrap: wrap;
  animation: cityFadeUp .7s .4s both;
}
.btn-wa-light {
  display: inline-flex;
  align-items: center;
  gap: .5rem;
  font-family: 'Jost', sans-serif;
  font-size: .85rem;
  font-weight: 700;
  color: #fff;
  background: linear-gradient(135deg, #25d366, #128c50);
  padding: .85rem 1.8rem;
  border-radius: 999px;
  text-decoration: none;
  box-shadow: 0 6px 22px rgba(18,140,80,.30);
  transition: all .25s;
}
.btn-wa-light:hover {
  background: linear-gradient(135deg, #22c55e, #15803d);
  transform: translateY(-2px);
  box-shadow: 0 10px 30px rgba(18,140,80,.40);
}
.btn-scroll-light {
  display: inline-flex;
  align-items: center;
  gap: .4rem;
  font-family: 'Jost', sans-serif;
  font-size: .82rem;
  font-weight: 600;
  color: rgba(58,36,32,.75);
  background: rgba(255,255,255,.65);
  backdrop-filter: blur(10px);
  border: 1.5px solid rgba(192,72,90,.25);
  padding: .85rem 1.5rem;
  border-radius: 999px;
  text-decoration: none;
  transition: all .2s;
  cursor: pointer;
  box-shadow: 0 2px 10px rgba(192,72,90,.06);
}
.btn-scroll-light:hover {
  background: rgba(255,255,255,.90);
  border-color: rgba(192,72,90,.45);
  color: var(--rose);
  transform: translateY(-2px);
}
.btn-scroll-light svg { animation: bounceD 1.6s ease-in-out infinite; }
@keyframes bounceD { 0%,100%{transform:translateY(0)} 50%{transform:translateY(4px)} }

/* Wave bawah hero */
.city-hero-wave {
  position: absolute;
  bottom: -1px; left: 0; right: 0;
  z-index: 11;
  line-height: 0;
}

/* ==============================
   BODY (unchanged layout, refreshed colors)
   ============================== */
.city-body {
  background: linear-gradient(170deg, #fdf6ee 0%, #fff 55%, #fdf0f3 100%);
}

.sec-eyebrow {
  font-family: 'Jost', sans-serif;
  font-size: .7rem; font-weight: 700;
  letter-spacing: .12em; text-transform: uppercase;
  color: var(--rose); margin-bottom: .35rem;
}
.sec-title {
  font-family: 'Cormorant Garamond', serif;
  font-size: clamp(1.4rem,2.5vw,2rem);
  font-weight: 700; color: #1f2937;
}
.sec-gold {
  width: 44px; height: 2px;
  background: linear-gradient(90deg, #c9a84c, transparent);
  margin: .5rem 0 0;
}

/* ── Area Cards ── */
.masonry-areas {
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  gap: .85rem;
}
@media(min-width: 640px) { .masonry-areas { grid-template-columns: repeat(3, 1fr); gap: 1rem; } }
@media(min-width: 1024px) { .masonry-areas { grid-template-columns: repeat(4, 1fr); gap: 1.1rem; } }

.area-mcard {
  display: block;
  background: #fff;
  border: 1px solid #fde8b4;
  border-radius: .875rem;
  overflow: hidden;
  text-decoration: none;
  transition: all .25s;
}
.area-mcard:hover {
  transform: translateY(-5px);
  box-shadow: 0 10px 28px rgba(192,72,90,.13);
  border-color: #fca5a5;
}
.area-mcard-img {
  height: 110px;
  background: linear-gradient(135deg, #fdf0f3, #fdf6ee);
  overflow: hidden;
  position: relative;
}
.area-mcard-img-inner {
  width: 100%; height: 100%;
  background: linear-gradient(135deg, #fdf0f3 0%, #f0f4ed 100%);
  display: flex;
  align-items: center;
  justify-content: center;
}
.area-mcard-flower { opacity: .15; }
.area-mcard-body { padding: .75rem .85rem .8rem; }
.area-mcard-name {
  font-family: 'Jost', sans-serif;
  font-size: .82rem;
  font-weight: 700;
  color: #1f2937;
  margin-bottom: .25rem;
  transition: color .2s;
  display: flex; align-items: center; gap: .4rem;
}
.area-mcard:hover .area-mcard-name { color: var(--rose); }
.area-mcard-pin {
  width: 14px; height: 14px; color: var(--rose);
  flex-shrink: 0; opacity: 0;
  transition: opacity .2s, transform .2s;
  transform: translateX(-4px);
}
.area-mcard:hover .area-mcard-pin { opacity: 1; transform: translateX(0); }
.area-mcard-sub { font-family: 'Jost', sans-serif; font-size: .7rem; color: #9ca3af; font-weight: 500; }

/* Landmark chips */
.lm-chips { display: flex; flex-wrap: wrap; gap: .5rem; }
.lm-chip {
  display: inline-flex; align-items: center; gap: .4rem;
  font-family: 'Jost', sans-serif;
  font-size: .75rem; font-weight: 500; color: #374151;
  background: #fff; border: 1px solid #fde8b4;
  padding: .35rem .85rem; border-radius: 999px;
  transition: all .2s;
}
.lm-chip:hover { color: var(--rose); border-color: #fca5a5; background: #fff5f5; }
.lm-dot { width:6px; height:6px; border-radius:50%; background:#c9a84c; flex-shrink:0; }

/* Category list */
.prodcat-list { display: flex; flex-direction: column; gap: .5rem; }
.prodcat-item {
  display: flex; align-items: center; gap: .75rem;
  background: #fff; border: 1px solid #fde8b4;
  border-radius: .75rem; padding: .75rem 1rem;
  text-decoration: none; transition: all .2s;
}
.prodcat-item:hover { border-color: #fca5a5; background: #fff5f5; transform: translateX(4px); }
.prodcat-dot { width:8px; height:8px; border-radius:50%; background:#c9a84c; flex-shrink:0; }
.prodcat-name { font-family:'Jost',sans-serif; font-size:.8rem; font-weight:600; color:#374151; flex:1; transition:color .2s; }
.prodcat-item:hover .prodcat-name { color: var(--rose); }
.prodcat-arr { color:#d1d5db; transition:color .2s,transform .2s; }
.prodcat-item:hover .prodcat-arr { color: var(--rose); transform:translateX(3px); }

/* Mini product grid */
.mini-prod-grid { display:grid; grid-template-columns:repeat(2,1fr); gap:.75rem; }
@media(min-width:640px){ .mini-prod-grid{ grid-template-columns:repeat(4,1fr); } }
.mini-prod-card {
  background:#fff; border:1px solid #fde8b4;
  border-radius:.875rem; overflow:hidden;
  text-decoration:none; transition:all .25s; display:block;
}
.mini-prod-card:hover { transform:translateY(-3px); box-shadow:0 6px 18px rgba(192,72,90,.10); border-color:#fca5a5; }
.mini-prod-img { height:100px; overflow:hidden; background:linear-gradient(135deg,#fdf0f3,#fdf6ee); }
.mini-prod-img img { width:100%;height:100%;object-fit:cover;transition:transform .35s; }
.mini-prod-card:hover .mini-prod-img img { transform:scale(1.07); }
.mini-prod-body { padding:.55rem .65rem; }
.mini-prod-name { font-family:'Jost',sans-serif;font-size:.72rem;font-weight:600;color:#374151;line-height:1.35;margin-bottom:.2rem;display:-webkit-box;-webkit-line-clamp:2;line-clamp:2;-webkit-box-orient:vertical;overflow:hidden; }
.mini-prod-price { font-family:'Cormorant Garamond',serif;font-size:.95rem;font-weight:700;color:var(--rose); }

/* Nearby cities */
.nearby-city-grid { display:grid; grid-template-columns:repeat(2,1fr); gap:.75rem; }
@media(min-width:480px){ .nearby-city-grid{ grid-template-columns:repeat(3,1fr); } }
@media(min-width:768px){ .nearby-city-grid{ grid-template-columns:repeat(4,1fr); } }
.nearby-city-card {
  display:flex; align-items:center; gap:.6rem;
  background:#fff; border:1px solid #fde8b4;
  border-radius:.875rem; padding:.8rem .9rem;
  text-decoration:none; transition:all .25s;
}
.nearby-city-card:hover { transform:translateY(-3px); box-shadow:0 6px 18px rgba(192,72,90,.10); border-color:#fca5a5; background:#fff5f5; }
.nearby-city-icon {
  width:32px;height:32px;border-radius:50%;
  background:linear-gradient(135deg,#fdf0f3,#fdf6ee);
  border:1px solid #fde8b4;
  display:flex;align-items:center;justify-content:center;flex-shrink:0;transition:all .25s;
}
.nearby-city-card:hover .nearby-city-icon { background:linear-gradient(135deg,#ffe4e6,#fdf0f3);border-color:#fca5a5; }
.nearby-city-icon svg { color:var(--rose); }
.nearby-city-name { font-family:'Jost',sans-serif;font-size:.78rem;font-weight:600;color:#374151;line-height:1.35;transition:color .2s; }
.nearby-city-card:hover .nearby-city-name { color:var(--rose); }

/* SEO strip */
.seo-prose { font-family:'Jost',sans-serif;font-size:.85rem;color:#6b7280;line-height:1.8; }
.seo-prose a { color:var(--rose);text-decoration:underline; }
.city-strip { background:linear-gradient(135deg,#fdf6ee,#fdf0f3);border:1px solid #fde8b4;border-radius:1rem;padding:1.1rem; }
.city-strip-label { font-family:'Jost',sans-serif;font-size:.68rem;font-weight:700;letter-spacing:.1em;text-transform:uppercase;color:#9ca3af;margin-bottom:.7rem;display:flex;align-items:center;gap:.5rem; }
.city-strip-label::after { content:'';flex:1;height:1px;background:linear-gradient(90deg,#fde8b4,transparent); }
.internal-links { display:flex;flex-wrap:wrap;gap:.4rem; }
.internal-links a { font-family:'Jost',sans-serif;font-size:.72rem;font-weight:500;color:#6b7280;background:#fff;border:1px solid #fde8b4;border-radius:999px;padding:.25rem .7rem;text-decoration:none;transition:all .2s; }
.internal-links a:hover { color:var(--rose);border-color:#fca5a5;background:#fff5f5; }

/* CTA bottom — BRIGHT floral version */
.city-cta {
  position: relative;
  overflow: hidden;
  background: #faf7f4;
}
.city-cta-bgimg {
  position: absolute;
  inset: 0;
  background-image: url('<?= BASE_URL ?>/assets/images/pwutih.jpeg');
  background-size: cover;
  background-position: center;
  opacity: 0.14;
  z-index: 0;
}
.city-cta-overlay {
  position: absolute;
  inset: 0;
  z-index: 1;
  background: linear-gradient(160deg,
    rgba(253,248,242,0.88) 0%,
    rgba(252,243,246,0.82) 50%,
    rgba(253,248,242,0.92) 100%
  );
}
.city-cta-orb {
  position: absolute;
  border-radius: 50%;
  filter: blur(80px);
  pointer-events: none;
  z-index: 2;
}
</style>

<!-- ==============================
     HERO — BRIGHT FLORAL
     ============================== -->
<section class="city-hero">

  <!-- Background image -->
  <div class="city-hero-bgimg"></div>
  <!-- Gradient overlay -->
  <div class="city-hero-overlay"></div>

  <!-- Floral corner accents -->
  <div class="city-hero-corner city-hero-corner-tl"></div>
  <div class="city-hero-corner city-hero-corner-tr"></div>
  <div class="city-hero-corner city-hero-corner-br"></div>

  <!-- Soft warm glow orbs -->
  <div class="city-hero-atm">
    <div class="city-glow" style="width:480px;height:480px;background:rgba(240,160,170,0.16);top:-130px;left:-80px;z-index:2;"></div>
    <div class="city-glow" style="width:380px;height:380px;background:rgba(160,200,150,0.12);top:-60px;right:-50px;z-index:2;"></div>
    <div class="city-glow" style="width:320px;height:320px;background:rgba(255,220,200,0.14);bottom:100px;left:30%;z-index:2;"></div>
    <div class="city-glow" style="width:240px;height:240px;background:rgba(250,220,230,0.18);bottom:80px;right:8%;z-index:2;"></div>
  </div>

  <!-- Falling petals -->
  <div class="city-petals" id="city-petals"></div>

  <!-- City silhouette — LIGHT version -->
  <div class="city-silhouette">
    <svg viewBox="0 0 1440 200" preserveAspectRatio="xMidYMax slice" style="width:100%;height:200px;display:block;">
      <defs>
        <linearGradient id="skyGradLight" x1="0" y1="0" x2="0" y2="1">
          <stop offset="0%" stop-color="#e8ddd6" stop-opacity="0.45"/>
          <stop offset="100%" stop-color="#ddd0c8" stop-opacity="0.80"/>
        </linearGradient>
      </defs>
      <!-- Skyline buildings — soft warm silhouette -->
      <path d="
        M0,200 L0,140 L30,140 L30,100 L50,100 L50,80 L70,80 L70,100 L90,100 L90,120 L120,120 L120,70 L130,70 L130,50 L140,50 L140,70 L150,70 L150,120
        L180,120 L180,90 L200,90 L200,60 L210,60 L210,40 L220,40 L220,60 L230,60 L230,90 L260,90
        L260,110 L290,110 L290,75 L300,75 L300,55 L310,55 L310,75 L320,75 L320,110
        L350,110 L350,85 L380,85 L380,65 L395,65 L395,45 L400,42 L405,45 L405,65 L420,65 L420,85 L450,85
        L450,100 L480,100 L480,72 L492,72 L492,52 L500,48 L508,52 L508,72 L520,72 L520,100
        L550,100 L550,80 L570,80 L570,55 L580,55 L580,80 L600,80 L600,95
        L630,95 L630,65 L645,65 L645,42 L655,40 L660,38 L665,40 L675,42 L675,65 L690,65 L690,95
        L720,95 L720,110 L750,110 L750,80 L762,80 L762,58 L770,52 L778,58 L778,80 L790,80 L790,110
        L820,110 L820,88 L840,88 L840,65 L852,65 L852,45 L860,42 L868,45 L868,65 L880,65 L880,88 L910,88
        L910,100 L940,100 L940,78 L955,78 L955,58 L965,55 L975,58 L975,78 L990,78 L990,100
        L1020,100 L1020,82 L1040,82 L1040,60 L1050,60 L1050,82 L1070,82
        L1070,95 L1100,95 L1100,70 L1115,70 L1115,48 L1122,44 L1130,48 L1130,70 L1145,70 L1145,95
        L1175,95 L1175,108 L1200,108 L1200,85 L1215,85 L1215,62 L1225,58 L1235,62 L1235,85 L1250,85 L1250,108
        L1280,108 L1280,90 L1300,90 L1300,68 L1310,68 L1310,90 L1330,90
        L1330,115 L1360,115 L1360,88 L1375,88 L1375,68 L1385,65 L1395,68 L1395,88 L1410,88 L1410,115
        L1440,115 L1440,200 Z
      " fill="url(#skyGradLight)"/>
      <!-- Window lights — warm peachy glow -->
      <?php
      $wins = [[140,56,4,4],[220,46,4,4],[300,62,3,3],[400,50,4,4],[500,55,3,3],[580,62,3,3],[660,44,4,4],[770,58,3,3],[860,48,4,4],[965,60,3,3],[1122,50,3,3],[1225,64,3,3],[1385,70,4,4]];
      foreach($wins as $w): ?>
      <rect x="<?=$w[0]-$w[2]/2?>" y="<?=$w[1]?>" width="<?=$w[2]?>" height="<?=$w[3]?>" fill="#e8a070" opacity="0.50" rx="1"/>
      <?php endforeach; ?>
    </svg>
  </div>

  <!-- Garden layer — bright flowers -->
  <div class="city-garden">
    <svg viewBox="0 0 1440 120" preserveAspectRatio="xMidYMax slice" style="width:100%;height:120px;display:block;">
      <defs>
        <linearGradient id="gardenFloorLight" x1="0" y1="0" x2="0" y2="1">
          <stop offset="0%" stop-color="#faf7f4" stop-opacity="0"/>
          <stop offset="100%" stop-color="#faf7f4" stop-opacity="1"/>
        </linearGradient>
      </defs>
      <!-- Grass & ground -->
      <path d="M0,90 C200,75 400,95 600,82 C800,68 1000,88 1200,78 C1320,72 1400,82 1440,80 L1440,120 L0,120 Z"
            fill="#e8e0d8" opacity="0.55"/>
      <!-- Stems — sage green -->
      <?php
      $stems = [60,140,240,340,480,580,680,800,920,1040,1160,1280,1380];
      foreach($stems as $i=>$x):
        $h = 40 + ($i%3)*20;
        // Alternating blush/sage/cream flower colors
        $colors = ['#e8a0a8','#d4b88c','#b8d4a8','#e8a0a8','#d4c09c'];
        $fc = $colors[$i % count($colors)];
      ?>
      <line x1="<?=$x?>" y1="<?=115-$h?>" x2="<?=$x?>" y2="115"
            stroke="#8aaa78" stroke-width="2" opacity="0.70"/>
      <!-- Petals — soft bright -->
      <?php foreach([0,72,144,216,288] as $r): ?>
      <ellipse cx="<?=$x?>" cy="<?=110-$h?>" rx="5" ry="10"
        fill="<?=$fc?>" opacity="0.65"
        transform="rotate(<?=$r?> <?=$x?> <?=110-$h?>)"/>
      <?php endforeach; ?>
      <!-- Flower center -->
      <circle cx="<?=$x?>" cy="<?=110-$h?>" r="4" fill="#f5e0c0" opacity="0.85"/>
      <!-- Leaves -->
      <ellipse cx="<?=$x-10?>" cy="<?=112-$h/2?>" rx="8" ry="4"
               fill="#8aaa78" opacity="0.55"
               transform="rotate(-35 <?=$x-10?> <?=112-$h/2?>)"/>
      <?php endforeach; ?>
      <!-- Baby's breath clusters -->
      <?php foreach([100,280,460,640,820,1000,1200] as $bx): ?>
      <circle cx="<?=$bx?>"    cy="<?=80+($bx*3)%20?>" r="2.5" fill="#e8e8e0" opacity=".75"/>
      <circle cx="<?=$bx+8?>"  cy="<?=75+($bx*2)%18?>" r="2"   fill="#f0f0e8" opacity=".70"/>
      <circle cx="<?=$bx+14?>" cy="<?=82+($bx*4)%15?>" r="2"   fill="#e8e8e0" opacity=".65"/>
      <?php endforeach; ?>
      <!-- Floor gradient -->
      <rect x="0" y="88" width="1440" height="32" fill="url(#gardenFloorLight)"/>
    </svg>
  </div>

  <!-- Content -->
  <div class="city-hero-content">

    <nav class="city-breadcrumb">
      <a href="<?= BASE_URL ?>/">Beranda</a>
      <span class="sep">❯</span>
      <?php if ($provinsi): ?>
        <span style="color:rgba(58,36,32,.35)"><?= $provinsi ?></span>
        <span class="sep">❯</span>
      <?php endif; ?>
      <span class="cur">Toko Bunga <?= $kota ?></span>
    </nav>

    <?php if ($provinsi): ?>
    <div class="city-prov-badge">
      <svg width="11" height="11" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
        <path d="M17.657 16.657L13.414 20.9a2 2 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
        <circle cx="12" cy="11" r="3"/>
      </svg>
      <?= $provinsi ?> <span class="city-tier"><?= $tierLabel ?></span>
    </div>
    <?php endif; ?>

    <!-- Floral SVG ornament -->
    <div class="city-hero-ornament">
      <svg width="160" height="32" viewBox="0 0 200 40" xmlns="http://www.w3.org/2000/svg">
        <path d="M10 20 Q40 8 80 18" stroke="#b08880" stroke-width="1" fill="none"/>
        <circle cx="30" cy="13" r="4" fill="#e8a0a0" opacity=".55"/>
        <circle cx="50" cy="10" r="3" fill="#c8d8b0" opacity=".65"/>
        <circle cx="65" cy="14" r="3.5" fill="#e8a0a0" opacity=".50"/>
        <path d="M28 13 Q24 8 20 11" stroke="#7a9a70" stroke-width="1" fill="none"/>
        <path d="M50 10 Q46 5 43 7" stroke="#7a9a70" stroke-width="1" fill="none"/>
        <!-- Center rose -->
        <circle cx="100" cy="18" r="7" fill="#e8a0a8" opacity=".65"/>
        <circle cx="100" cy="18" r="4.5" fill="#d47880" opacity=".75"/>
        <circle cx="100" cy="18" r="2" fill="#b05060" opacity=".85"/>
        <!-- Right branch mirror -->
        <path d="M190 20 Q160 8 120 18" stroke="#b08880" stroke-width="1" fill="none"/>
        <circle cx="170" cy="13" r="4" fill="#e8a0a0" opacity=".55"/>
        <circle cx="150" cy="10" r="3" fill="#c8d8b0" opacity=".65"/>
        <circle cx="135" cy="14" r="3.5" fill="#e8a0a0" opacity=".50"/>
        <path d="M172 13 Q176 8 180 11" stroke="#7a9a70" stroke-width="1" fill="none"/>
        <path d="M150 10 Q154 5 157 7" stroke="#7a9a70" stroke-width="1" fill="none"/>
      </svg>
    </div>

    <h1>Toko Bunga<br><em><?= $kota ?></em></h1>

    <div class="city-divider"></div>

    <p class="city-hero-desc">
      Chika Florist melayani pemesanan &amp; pengiriman bunga di seluruh wilayah <?= $kota ?> —
      24 jam nonstop, bunga segar, pengiriman cepat ke setiap kecamatan.
    </p>

    <!-- Stats -->
    <div class="city-stats">
      <div class="city-stat">
        <span class="city-stat-num"><?= count($areas) ?>+</span>
        <span class="city-stat-label">Area Layanan</span>
      </div>
      <div class="city-stat">
        <span class="city-stat-num">24</span>
        <span class="city-stat-label">Jam Buka</span>
      </div>
      <div class="city-stat">
        <span class="city-stat-num"><?= count($prodCats) ?>+</span>
        <span class="city-stat-label">Jenis Bunga</span>
      </div>
      <div class="city-stat">
        <span class="city-stat-num">⚡</span>
        <span class="city-stat-label">Same Day</span>
      </div>
    </div>

    <div class="city-hero-btns">
      <a href="<?= waLink("Halo Chika Florist, saya ingin pesan bunga di {$kota}") ?>"
         target="_blank" class="btn-wa-light">
        <svg width="16" height="16" fill="currentColor" viewBox="0 0 24 24">
          <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z"/>
          <path d="M12 0C5.373 0 0 5.373 0 12c0 2.096.541 4.063 1.491 5.776L.057 23.04l5.43-1.424A11.947 11.947 0 0012 24c6.627 0 12-5.373 12-12S18.627 0 12 0zm0 21.818a9.818 9.818 0 01-5.015-1.371l-.36-.214-3.726.977.995-3.633-.235-.374A9.818 9.818 0 1112 21.818z"/>
        </svg>
        Pesan Bunga di <?= $kota ?>
      </a>
      <a href="#areas" class="btn-scroll-light"
         onclick="document.getElementById('areas').scrollIntoView({behavior:'smooth'});return false;">
        Lihat Area Layanan
        <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
          <path d="M12 5v14M5 12l7 7 7-7"/>
        </svg>
      </a>
    </div>

  </div>

  <!-- Wave bawah — smooth ke body -->
  <div class="city-hero-wave">
    <svg viewBox="0 0 1440 60" preserveAspectRatio="none" style="width:100%;height:60px;display:block;">
      <path d="M0,20 C360,60 720,0 1080,30 C1260,45 1380,15 1440,25 L1440,60 L0,60 Z" fill="#fdf6ee"/>
    </svg>
  </div>

</section>

<!-- ==============================
     BODY
     ============================== -->
<div class="city-body" id="areas">
  <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-12 lg:py-16">

    <!-- MASONRY AREAS -->
    <?php if (!empty($areas)): ?>
    <div class="mb-14">
      <p class="sec-eyebrow">Jangkauan Layanan</p>
      <h2 class="sec-title">Area Pengiriman di <?= $kota ?></h2>
      <div class="sec-gold"></div>
      <p class="seo-prose mt-2 mb-6">Chika Florist melayani pengiriman bunga ke <strong><?= count($areas) ?> area</strong> di seluruh <?= $kota ?> dan sekitarnya.</p>

      <div class="masonry-areas">
        <?php foreach ($areas as $i => $ar):
          $flowerColors = ['#fda4af','#c9a84c','#86efac','#fda4af','#c9a84c'];
          $fc = $flowerColors[$i % 5];
        ?>
        <a href="<?= BASE_URL ?>/toko-bunga-<?= $ar['slug'] ?>" class="area-mcard">
          <div class="area-mcard-img">
            <div class="area-mcard-img-inner">
              <svg class="area-mcard-flower" width="60" height="60" viewBox="0 0 80 80" fill="none">
                <?php foreach([0,60,120,180,240,300] as $r): ?>
                <ellipse cx="40" cy="22" rx="7" ry="16" fill="<?= $fc ?>" transform="rotate(<?= $r ?> 40 40)"/>
                <?php endforeach; ?>
                <circle cx="40" cy="40" r="9" fill="#f9c784"/>
              </svg>
            </div>
          </div>
          <div class="area-mcard-body">
            <div class="area-mcard-name">
              <svg class="area-mcard-pin" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path d="M17.657 16.657L13.414 20.9a2 2 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                <circle cx="12" cy="11" r="3"/>
              </svg>
              <?= clean($ar['name']) ?>
            </div>
            <div class="area-mcard-sub">Toko Bunga <?= clean($ar['name']) ?> →</div>
          </div>
        </a>
        <?php endforeach; ?>
      </div>
    </div>
    <?php endif; ?>

    <!-- LANDMARK NOTES -->
    <?php if (!empty($city['landmark_notes'])): ?>
    <div class="mb-14">
      <p class="sec-eyebrow">Jangkauan Pengiriman</p>
      <h2 class="sec-title">Cakupan Area di <?= $kota ?></h2>
      <div class="sec-gold"></div>
      <p class="seo-prose mt-2 mb-4">Pengiriman bunga mencakup berbagai titik strategis di <?= $kota ?>:</p>
      <div class="lm-chips">
        <?php
        $lmarks = array_filter(array_map('trim', explode(',', str_replace(["\n",';'], ',', $city['landmark_notes']))));
        foreach ($lmarks as $lm): if(empty($lm)) continue; ?>
        <span class="lm-chip"><span class="lm-dot"></span><?= clean($lm) ?></span>
        <?php endforeach; ?>
      </div>
    </div>
    <?php endif; ?>

    <!-- KATEGORI PRODUK -->
    <div class="mb-14">
      <p class="sec-eyebrow">Pilihan Bunga</p>
      <h2 class="sec-title">Karangan Bunga di <?= $kota ?></h2>
      <div class="sec-gold"></div>
      <div class="prodcat-list mt-5">
        <?php foreach ($prodCats as $cat): ?>
        <a href="<?= BASE_URL ?>/<?= $cat['slug'] ?>" class="prodcat-item">
          <span class="prodcat-dot"></span>
          <span class="prodcat-name"><?= clean($cat['name']) ?> <?= $kota ?></span>
          <svg class="prodcat-arr" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9 18l6-6-6-6"/></svg>
        </a>
        <?php endforeach; ?>
        <a href="<?= BASE_URL ?>/toko-bunga-online-24-jam-indonesia" class="prodcat-item">
          <span class="prodcat-dot" style="background:var(--rose);"></span>
          <span class="prodcat-name" style="color:var(--rose);">Toko Bunga Online 24 Jam Indonesia →</span>
        </a>
      </div>
    </div>

    <!-- PRODUK POPULER -->
    <?php if (!empty($featured)): ?>
    <div class="mb-14">
      <p class="sec-eyebrow">Terlaris</p>
      <h2 class="sec-title">Produk Populer di <?= $kota ?></h2>
      <div class="sec-gold"></div>
      <div class="mini-prod-grid mt-5">
        <?php foreach ($featured as $prod): ?>
        <a href="<?= waLink('Halo, saya ingin pesan '.$prod['name'].' di '.$kota) ?>" target="_blank" class="mini-prod-card">
          <div class="mini-prod-img">
            <img src="<?= UPLOAD_URL.($prod['image']??'') ?>" alt="<?= clean($prod['name']) ?> <?= $kota ?>" onerror="this.style.display='none'">
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

    <!-- NEARBY CITIES -->
    <?php if (!empty($nearby)): ?>
    <div class="mb-14">
      <p class="sec-eyebrow">Kota Lainnya</p>
      <h2 class="sec-title">Florist Online Kota Lain</h2>
      <div class="sec-gold"></div>
      <div class="nearby-city-grid mt-5">
        <?php foreach ($nearby as $nc): ?>
        <a href="<?= BASE_URL ?>/toko-bunga-<?= $nc['slug'] ?>" class="nearby-city-card">
          <div class="nearby-city-icon">
            <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
              <path d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
            </svg>
          </div>
          <span class="nearby-city-name">Toko Bunga <?= clean($nc['name']) ?></span>
        </a>
        <?php endforeach; ?>
      </div>
    </div>
    <?php endif; ?>

    <!-- SEO STRIP -->
    <div class="city-strip mb-4">
      <div class="city-strip-label">
        <svg width="11" height="11" fill="none" stroke="#c9a84c" stroke-width="2" viewBox="0 0 24 24">
          <path d="M17.657 16.657L13.414 20.9a2 2 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
          <circle cx="12" cy="11" r="3"/>
        </svg>
        Produk Tersedia di <?= $kota ?>
      </div>
      <div class="internal-links">
        <?php foreach ($prodCats as $cat): ?>
        <a href="<?= BASE_URL ?>/<?= $cat['slug'] ?>"><?= clean($cat['name']) ?> <?= $kota ?></a>
        <?php endforeach; ?>
        <a href="<?= BASE_URL ?>/toko-bunga-online-24-jam-indonesia">toko bunga online 24 jam</a>
        <a href="<?= BASE_URL ?>/">florist online 24 jam Indonesia</a>
      </div>
    </div>

  </div>
</div>

<!-- ==============================
     CTA BOTTOM — BRIGHT FLORAL
     ============================== -->
<section class="city-cta py-14 px-4 text-center">
  <div class="city-cta-bgimg"></div>
  <div class="city-cta-overlay"></div>
  <!-- Soft orbs -->
  <div class="city-cta-orb" style="width:380px;height:380px;background:rgba(240,160,170,0.18);top:-100px;left:50%;transform:translateX(-50%);"></div>
  <div class="city-cta-orb" style="width:250px;height:250px;background:rgba(160,200,150,0.12);bottom:-60px;right:10%;"></div>

  <div style="position:relative;z-index:5;max-width:500px;margin:0 auto;">

    <!-- Floral ornament -->
    <svg width="48" height="48" viewBox="0 0 80 80" fill="none"
         style="display:inline-block;margin-bottom:.85rem;">
      <?php foreach([0,60,120,180,240,300] as $r): ?>
      <ellipse cx="40" cy="22" rx="8" ry="18" fill="#e8a0a8" opacity=".60"
               transform="rotate(<?= $r ?> 40 40)"/>
      <?php endforeach; ?>
      <circle cx="40" cy="40" r="10" fill="#f0d0b0" opacity=".9"/>
      <circle cx="40" cy="40" r="5"  fill="#d49888" opacity=".7"/>
    </svg>

    <h2 style="font-family:'Cormorant Garamond',serif;font-size:1.9rem;font-weight:700;
               color:#3a2420;margin-bottom:.5rem;line-height:1.15;">
      Pesan Bunga di <em style="font-style:italic;color:#c0485a;"><?= $kota ?></em>
    </h2>
    <p style="font-family:'Jost',sans-serif;font-size:.86rem;color:rgba(58,36,32,.58);
              margin-bottom:1.8rem;line-height:1.7;max-width:380px;margin-left:auto;margin-right:auto;">
      Layanan florist online 24 jam — pesan kapan saja, bunga dikirim cepat ke seluruh area <?= $kota ?>.
    </p>

    <!-- Floral divider line -->
    <div style="display:flex;align-items:center;gap:.8rem;max-width:280px;margin:0 auto 1.8rem;">
      <div style="flex:1;height:1px;background:linear-gradient(90deg,transparent,rgba(192,72,90,.25));"></div>
      <span style="color:rgba(192,72,90,.45);font-size:.75rem;">✿</span>
      <div style="flex:1;height:1px;background:linear-gradient(90deg,rgba(192,72,90,.25),transparent);"></div>
    </div>

    <a href="<?= waLink("Halo, saya ingin pesan bunga di {$kota}") ?>" target="_blank"
       style="display:inline-flex;align-items:center;gap:.65rem;
              font-family:'Jost',sans-serif;font-size:.88rem;font-weight:700;
              background:linear-gradient(135deg,#25d366,#128c50);
              color:#fff;padding:.9rem 2.2rem;border-radius:999px;
              text-decoration:none;
              box-shadow:0 8px 28px rgba(18,140,80,.30);
              transition:all .25s;">
      <svg width="17" height="17" fill="currentColor" viewBox="0 0 24 24">
        <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z"/>
        <path d="M12 0C5.373 0 0 5.373 0 12c0 2.096.541 4.063 1.491 5.776L.057 23.04l5.43-1.424A11.947 11.947 0 0012 24c6.627 0 12-5.373 12-12S18.627 0 12 0zm0 21.818a9.818 9.818 0 01-5.015-1.371l-.36-.214-3.726.977.995-3.633-.235-.374A9.818 9.818 0 1112 21.818z"/>
      </svg>
      Hubungi via WhatsApp
    </a>
  </div>
</section>

<!-- JS — Falling Petals (no stars, bright mode) -->
<script>
(function(){
  var c = document.getElementById('city-petals');
  if (!c) return;
  var colors = [
    'rgba(240,190,195,.55)',
    'rgba(255,215,220,.50)',
    'rgba(245,235,220,.55)',
    'rgba(210,230,205,.45)',
    'rgba(250,205,215,.55)',
    'rgba(240,220,200,.50)',
  ];
  for (var i = 0; i < 22; i++) {
    var p = document.createElement('div');
    p.className = 'city-petal';
    var sz  = 6 + Math.random() * 11;
    var col = colors[Math.floor(Math.random() * colors.length)];
    var dur = 9 + Math.random() * 10;
    var del = Math.random() * 14;
    p.style.cssText = [
      'width:'  + sz + 'px',
      'height:' + (sz * 0.58) + 'px',
      'left:'   + (Math.random() * 100) + '%',
      'top:-20px',
      'background:' + col,
      'animation-duration:' + dur + 's',
      'animation-delay:' + del + 's',
      'transform:rotate(' + (Math.random() * 360) + 'deg)',
    ].join(';');
    c.appendChild(p);
  }
})();
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>