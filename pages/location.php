<?php
require_once __DIR__ . '/../includes/config.php';
$pdo    = getDB();
$cities = $pdo->query("SELECT * FROM cities WHERE is_active=1 ORDER BY tier ASC, sort_order ASC, name ASC")->fetchAll();

// Group by province
$byProvince = [];
foreach ($cities as $city) {
    $prov = $city['province'] ?: 'Lainnya';
    $byProvince[$prov][] = $city;
}
ksort($byProvince);

$totalCities    = count($cities);
$totalProvinces = count($byProvince);
$tierOneCities  = count(array_filter($cities, fn($c) => $c['tier'] == 1));

$page_title    = 'Area Layanan | Toko Bunga Online Seluruh Indonesia – Chika Florist';
$meta_desc     = 'Chika Florist melayani pengiriman bunga ke seluruh kota di Indonesia. Lihat daftar area layanan kami.';
$canonical_url = BASE_URL . '/area-layanan';
$breadcrumbs   = [['label'=>'Beranda','url'=>'/'],['label'=>'Area Layanan']];
require_once __DIR__ . '/../includes/header.php';
?>

<style>
@import url('https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,400;0,600;0,700;1,400;1,600&family=Jost:wght@300;400;500;600;700&display=swap');

/* ==============================
   HERO
   ============================== */
.area-hero {
  position: relative;
  min-height: 500px;
  display: flex;
  align-items: center;
  justify-content: center;
  overflow: hidden;
  background: linear-gradient(150deg, #1a0810 0%, #2a0f1a 45%, #1a1a0a 100%);
}

.area-hero-glow {
  position: absolute;
  border-radius: 50%;
  filter: blur(90px);
  pointer-events: none;
}

/* Stars */
.area-stars { position: absolute; inset: 0; pointer-events: none; overflow: hidden; }
.area-star  {
  position: absolute; border-radius: 50%; background: #fff;
  animation: aStar var(--dur,3s) var(--delay,0s) ease-in-out infinite;
}
@keyframes aStar {
  0%,100% { opacity: var(--op,.3); transform: scale(1); }
  50%      { opacity: .05; transform: scale(.5); }
}

/* Map container */
.area-map-wrap {
  position: absolute;
  inset: 0;
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 2;
  pointer-events: none;
}
.area-map-svg {
  width: 100%;
  max-width: 900px;
  height: auto;
  opacity: 0.13;
  filter: drop-shadow(0 0 20px rgba(201,168,76,0.3));
}

/* Map city dots */
.map-city-dot {
  animation: dotPulse 2.5s ease-in-out infinite;
}
@keyframes dotPulse {
  0%,100% { opacity: .7; transform: scale(1); }
  50%      { opacity: 1; transform: scale(1.4); }
}

/* Trellis */
.area-hero-trellis {
  position: absolute; inset: 0; z-index: 3;
  opacity: .04;
  background-image:
    repeating-linear-gradient(45deg,#c9a84c 0,#c9a84c 1px,transparent 0,transparent 50%),
    repeating-linear-gradient(-45deg,#c9a84c 0,#c9a84c 1px,transparent 0,transparent 50%);
  background-size: 32px 32px;
}

/* Hero content */
.area-hero-content {
  position: relative;
  z-index: 10;
  text-align: center;
  padding: 3rem 1.5rem 4.5rem;
  max-width: 680px;
  animation: aHeroUp .9s cubic-bezier(.22,.68,0,1.2) both;
}
@keyframes aHeroUp {
  from { opacity:0; transform:translateY(28px); }
  to   { opacity:1; transform:translateY(0); }
}

.area-eyebrow {
  display: inline-flex; align-items: center; gap: .45rem;
  font-family: 'Jost',sans-serif; font-size: .7rem; font-weight: 700;
  letter-spacing: .12em; text-transform: uppercase;
  color: #f9c784; background: rgba(201,168,76,.14);
  border: 1px solid rgba(201,168,76,.3);
  padding: .28rem .9rem; border-radius: 999px;
  margin-bottom: 1.1rem;
  animation: aHeroUp .7s .1s both;
}
.area-eyebrow-dot {
  width: 5px; height: 5px; border-radius: 50%; background: #f9c784;
  animation: aEyeDot 2s ease-in-out infinite;
}
@keyframes aEyeDot {
  0%,100%{transform:scale(1);opacity:1} 50%{transform:scale(1.8);opacity:.5}
}

.area-hero h1 {
  font-family: 'Cormorant Garamond', serif;
  font-size: clamp(2rem, 5.5vw, 3.6rem);
  font-weight: 700; color: #fff; line-height: 1.1;
  text-shadow: 0 2px 30px rgba(0,0,0,.5);
  margin-bottom: .8rem;
  animation: aHeroUp .7s .2s both;
}
.area-hero h1 em { font-style: italic; color: #fda4af; }

.area-hero-divider {
  width: 70px; height: 2px;
  background: linear-gradient(90deg, transparent, #c9a84c, transparent);
  margin: .8rem auto 1rem;
  animation: aHeroUp .7s .25s both;
}

.area-hero-desc {
  font-family: 'Jost', sans-serif; font-size: .9rem;
  color: rgba(255,255,255,.65); line-height: 1.7;
  max-width: 480px; margin: 0 auto 1.8rem;
  animation: aHeroUp .7s .3s both;
}

/* Stats counter row */
.area-stats {
  display: flex; align-items: stretch; justify-content: center;
  gap: 0; max-width: 420px; margin: 0 auto 1.6rem;
  background: rgba(255,255,255,.07);
  border: 1px solid rgba(255,255,255,.12);
  border-radius: 1rem; overflow: hidden;
  backdrop-filter: blur(10px);
  animation: aHeroUp .7s .35s both;
}
.area-stat {
  flex: 1; padding: .85rem .5rem; text-align: center;
  border-right: 1px solid rgba(255,255,255,.1);
}
.area-stat:last-child { border-right: none; }
.area-stat-num {
  font-family: 'Cormorant Garamond', serif;
  font-size: 1.8rem; font-weight: 700; color: #f9c784;
  line-height: 1; display: block;
}
.area-stat-label {
  font-family: 'Jost', sans-serif; font-size: .6rem; font-weight: 600;
  letter-spacing: .07em; text-transform: uppercase;
  color: rgba(255,255,255,.4); margin-top: .2rem; display: block;
}

/* Scroll hint */
.area-scroll-hint {
  display: flex; flex-direction: column; align-items: center; gap: .3rem;
  cursor: pointer; animation: aHeroUp .7s .45s both;
}
.area-scroll-hint span {
  font-family: 'Jost', sans-serif; font-size: .64rem; font-weight: 600;
  letter-spacing: .1em; text-transform: uppercase;
  color: rgba(255,255,255,.3);
}
.area-scroll-line {
  width: 1px; height: 30px;
  background: linear-gradient(to bottom, rgba(255,255,255,.3), transparent);
  animation: scrollLn 2s ease-in-out infinite;
}
@keyframes scrollLn {
  0%   {transform:scaleY(0);transform-origin:top;opacity:0}
  50%  {transform:scaleY(1);opacity:1}
  100% {transform:scaleY(0);transform-origin:bottom;opacity:0}
}

/* Wave */
.area-hero-wave {
  position: absolute; bottom:-1px; left:0; right:0; z-index:11; line-height:0;
}

/* ==============================
   BODY
   ============================== */
.area-body {
  background: linear-gradient(170deg,#fdf6ee 0%,#fff 55%,#fdf0f3 100%);
  padding: 3rem 0 4rem;
}

/* Search bar */
.area-search-wrap {
  max-width: 520px; margin: 0 auto 2.5rem;
  position: relative;
}
.area-search-icon {
  position: absolute; left: 1rem; top: 50%; transform: translateY(-50%);
  color: #9ca3af; pointer-events: none;
}
.area-search-input {
  width: 100%;
  font-family: 'Jost', sans-serif; font-size: .9rem; font-weight: 500;
  color: #1f2937;
  background: #fff;
  border: 1.5px solid #fde8b4;
  border-radius: 999px;
  padding: .85rem 1rem .85rem 2.8rem;
  outline: none;
  transition: border-color .2s, box-shadow .2s;
  box-shadow: 0 2px 12px rgba(201,168,76,.08);
}
.area-search-input:focus {
  border-color: #fca5a5;
  box-shadow: 0 0 0 3px rgba(225,29,72,.08), 0 2px 12px rgba(201,168,76,.08);
}
.area-search-input::placeholder { color: #d1d5db; }
.area-search-clear {
  position: absolute; right: .85rem; top: 50%; transform: translateY(-50%);
  width: 24px; height: 24px; border-radius: 50%;
  background: #f3f4f6; border: none; cursor: pointer;
  display: none; align-items: center; justify-content: center;
  transition: background .2s;
}
.area-search-clear:hover { background: #fca5a5; }
.area-search-clear svg { color: #6b7280; }
.area-search-clear.visible { display: flex; }

/* Search no result */
.area-no-result {
  display: none; text-align: center; padding: 2rem 1rem;
}
.area-no-result.show { display: block; }
.area-no-result p {
  font-family: 'Jost', sans-serif; font-size: .9rem; color: #9ca3af;
  margin-bottom: 1rem;
}

/* Section header */
.sec-eyebrow {
  font-family:'Jost',sans-serif;font-size:.7rem;font-weight:700;
  letter-spacing:.12em;text-transform:uppercase;color:#e11d48;margin-bottom:.35rem;
}
.sec-title {
  font-family:'Cormorant Garamond',serif;
  font-size:clamp(1.4rem,2.5vw,2rem);font-weight:700;color:#1f2937;
}
.sec-gold { width:44px;height:2px;background:linear-gradient(90deg,#c9a84c,transparent);margin:.5rem 0 0; }

/* ==============================
   ACCORDION PROVINCE
   ============================== */
.prov-list { display: flex; flex-direction: column; gap: .6rem; }

.prov-item {
  background: #fff;
  border: 1px solid #fde8b4;
  border-radius: 1rem;
  overflow: hidden;
  transition: border-color .2s, box-shadow .2s;
}
.prov-item.open {
  border-color: #fca5a5;
  box-shadow: 0 4px 20px rgba(214,90,110,.08);
}

.prov-head {
  display: flex; align-items: center; justify-content: space-between;
  padding: 1rem 1.1rem; cursor: pointer; user-select: none;
  transition: background .2s;
}
.prov-head:hover { background: #fff5f5; }
.prov-head-left { display: flex; align-items: center; gap: .75rem; }

.prov-icon {
  width: 36px; height: 36px; border-radius: 50%;
  background: linear-gradient(135deg,#fdf0f3,#fdf6ee);
  border: 1px solid #fde8b4;
  display: flex; align-items: center; justify-content: center;
  flex-shrink: 0; transition: all .25s;
}
.prov-item.open .prov-icon {
  background: linear-gradient(135deg,#ffe4e6,#fdf0f3);
  border-color: #fca5a5;
}
.prov-icon svg { color: #e11d48; }

.prov-name {
  font-family: 'Cormorant Garamond', serif;
  font-size: 1.05rem; font-weight: 700; color: #1f2937;
  transition: color .2s;
}
.prov-item.open .prov-name { color: #e11d48; }

.prov-count {
  font-family: 'Jost', sans-serif; font-size: .7rem; font-weight: 600;
  color: #9ca3af; background: #f9fafb; border: 1px solid #f3f4f6;
  padding: .15rem .55rem; border-radius: 999px;
  transition: all .2s;
}
.prov-item.open .prov-count {
  color: #e11d48; background: rgba(225,29,72,.06); border-color: rgba(225,29,72,.15);
}

.prov-chevron {
  width: 28px; height: 28px; border-radius: 50%;
  background: #f9fafb; border: 1px solid #f3f4f6;
  display: flex; align-items: center; justify-content: center;
  flex-shrink: 0; transition: all .25s;
}
.prov-item.open .prov-chevron {
  background: #e11d48; border-color: #e11d48; transform: rotate(180deg);
}
.prov-chevron svg { color: #9ca3af; transition: color .25s; }
.prov-item.open .prov-chevron svg { color: #fff; }

/* Province body */
.prov-body {
  max-height: 0; overflow: hidden;
  transition: max-height .4s cubic-bezier(.4,0,.2,1);
}
.prov-body-inner {
  padding: 0 1rem 1rem;
  border-top: 1px solid #fef3c7;
}

/* City grid inside accordion */
.city-grid {
  display: grid;
  grid-template-columns: repeat(2,1fr);
  gap: .5rem;
  margin-top: .75rem;
}
@media(min-width:480px) { .city-grid { grid-template-columns: repeat(3,1fr); } }
@media(min-width:768px) { .city-grid { grid-template-columns: repeat(4,1fr); } }

.city-card {
  display: flex; align-items: center; gap: .5rem;
  background: #fff; border: 1px solid #fde8b4;
  border-radius: .75rem; padding: .6rem .75rem;
  text-decoration: none; transition: all .2s;
  position: relative;
}
.city-card:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 14px rgba(214,90,110,.1);
  border-color: #fca5a5; background: #fff5f5;
}
.city-card-dot {
  width: 6px; height: 6px; border-radius: 50%;
  background: #fde8b4; flex-shrink: 0; transition: background .2s;
}
.city-card:hover .city-card-dot { background: #e11d48; }
.city-card-name {
  font-family: 'Jost', sans-serif; font-size: .78rem; font-weight: 600;
  color: #374151; line-height: 1.3; transition: color .2s;
  flex: 1; min-width: 0;
}
.city-card:hover .city-card-name { color: #e11d48; }

/* Tier badge */
.city-tier-badge {
  font-family: 'Jost', sans-serif; font-size: .58rem; font-weight: 700;
  letter-spacing: .05em; text-transform: uppercase;
  color: #92400e; background: linear-gradient(135deg,#fef3c7,#fde68a);
  border: 1px solid #fcd34d; padding: .12rem .45rem; border-radius: 999px;
  white-space: nowrap; flex-shrink: 0;
}

/* Hidden city (search filter) */
.city-card.hidden { display: none; }

/* Province item hidden in search */
.prov-item.all-hidden { display: none; }

/* ==============================
   NOT FOUND CTA
   ============================== */
.notfound-cta {
  background: linear-gradient(135deg,#fdf6ee,#fdf0f3);
  border: 1px solid #fde8b4; border-radius: 1rem;
  padding: 1.5rem; text-align: center;
  max-width: 480px; margin: 0 auto;
}
.notfound-cta p {
  font-family: 'Jost', sans-serif; font-size: .85rem; color: #6b7280;
  margin-bottom: 1rem; line-height: 1.6;
}
.btn-wa-sm {
  display: inline-flex; align-items: center; gap: .45rem;
  font-family: 'Jost', sans-serif; font-size: .8rem; font-weight: 700;
  color: #fff; background: #16a34a; padding: .65rem 1.4rem;
  border-radius: 999px; text-decoration: none;
  transition: all .2s; box-shadow: 0 3px 12px rgba(22,163,74,.25);
}
.btn-wa-sm:hover { background: #15803d; transform: translateY(-1px); }

/* ==============================
   CTA BOTTOM
   ============================== */
.area-cta {
  background: linear-gradient(135deg,#881337 0%,#9f1239 50%,#7f1d1d 100%);
  position: relative; overflow: hidden;
}
.area-cta-pat {
  position: absolute; inset: 0;
  background-image:
    repeating-linear-gradient(45deg,rgba(255,255,255,.025) 0,rgba(255,255,255,.025) 1px,transparent 0,transparent 50%),
    repeating-linear-gradient(-45deg,rgba(255,255,255,.025) 0,rgba(255,255,255,.025) 1px,transparent 0,transparent 50%);
  background-size: 28px 28px;
}
</style>

<!-- ==============================
     HERO — PETA INDONESIA
     ============================== -->
<section class="area-hero">

  <!-- Glow blobs -->
  <div class="area-hero-glow" style="width:500px;height:500px;background:rgba(190,24,93,.2);top:-150px;left:-100px;"></div>
  <div class="area-hero-glow" style="width:380px;height:380px;background:rgba(201,168,76,.14);bottom:-60px;right:-40px;"></div>
  <div class="area-hero-glow" style="width:300px;height:300px;background:rgba(20,83,45,.15);top:20%;left:40%;"></div>

  <!-- Stars -->
  <div class="area-stars" id="area-stars"></div>
  <div class="area-hero-trellis"></div>

  <!-- Peta Indonesia SVG abstrak -->
  <div class="area-map-wrap">
    <svg class="area-map-svg" viewBox="0 0 900 380" fill="none" xmlns="http://www.w3.org/2000/svg">
      <!-- Siluet kepulauan Indonesia — disederhanakan -->
      <!-- Sumatera -->
      <path d="M55,180 C65,155 80,140 100,135 C120,130 140,128 155,135 C170,142 178,155 180,170 C182,185 175,200 165,210 C155,220 140,225 125,222 C110,219 95,212 82,202 C69,192 60,195 55,180Z" fill="#c9a84c"/>
      <!-- Jawa -->
      <path d="M210,220 C225,212 245,208 268,210 C291,212 318,215 340,218 C362,221 375,220 385,225 C395,230 392,240 378,245 C364,250 340,250 315,248 C290,246 268,242 248,238 C228,234 215,230 210,220Z" fill="#c9a84c"/>
      <!-- Kalimantan -->
      <path d="M380,130 C395,110 415,100 440,98 C465,96 488,105 505,120 C522,135 528,155 525,175 C522,195 510,210 494,218 C478,226 458,228 440,222 C422,216 408,204 398,188 C388,172 380,155 378,140 C376,128 375,130 380,130Z" fill="#c9a84c"/>
      <!-- Sulawesi -->
      <path d="M545,145 C552,132 562,125 572,128 C582,131 585,145 582,158 C579,171 572,180 568,190 C564,200 565,210 560,215 C555,220 547,218 543,208 C539,198 540,185 543,172 C546,159 546,150 545,145Z M575,160 C585,155 598,158 605,168 C612,178 610,192 602,198 C594,204 582,202 576,193 C570,184 570,170 575,160Z" fill="#c9a84c"/>
      <!-- Bali & Lombok kecil -->
      <path d="M428,238 C432,235 437,234 441,236 C445,238 446,243 443,246 C440,249 434,249 430,246 C426,243 425,241 428,238Z" fill="#c9a84c"/>
      <!-- Papua -->
      <path d="M680,155 C695,138 715,130 738,130 C761,130 782,140 795,158 C808,176 808,198 795,214 C782,230 760,238 738,236 C716,234 698,224 688,208 C678,192 675,172 680,155Z" fill="#c9a84c"/>
      <!-- NTT -->
      <path d="M502,258 C508,252 516,250 523,253 C530,256 532,264 528,270 C524,276 515,278 508,274 C501,270 499,263 502,258Z" fill="#c9a84c"/>
      <!-- Maluku kecil -->
      <path d="M635,175 C638,170 644,168 649,171 C654,174 655,181 652,186 C649,191 642,192 638,188 C634,184 633,179 635,175Z M645,195 C647,191 652,190 656,193 C660,196 660,202 657,205 C654,208 648,208 645,205 C642,202 643,198 645,195Z" fill="#c9a84c"/>

      <!-- City dots — posisi kota-kota besar di atas peta -->
      <!-- Jakarta -->
      <circle class="map-city-dot" cx="268" cy="228" r="5" fill="#fda4af" style="animation-delay:0s"/>
      <circle cx="268" cy="228" r="10" fill="#fda4af" opacity=".15"/>
      <!-- Surabaya -->
      <circle class="map-city-dot" cx="340" cy="232" r="4" fill="#fda4af" style="animation-delay:.4s"/>
      <circle cx="340" cy="232" r="8" fill="#fda4af" opacity=".12"/>
      <!-- Medan -->
      <circle class="map-city-dot" cx="105" cy="162" r="4" fill="#f9c784" style="animation-delay:.8s"/>
      <circle cx="105" cy="162" r="8" fill="#f9c784" opacity=".12"/>
      <!-- Makassar -->
      <circle class="map-city-dot" cx="558" cy="210" r="4" fill="#f9c784" style="animation-delay:1.2s"/>
      <circle cx="558" cy="210" r="8" fill="#f9c784" opacity=".12"/>
      <!-- Bandung -->
      <circle class="map-city-dot" cx="255" cy="235" r="3" fill="#86efac" style="animation-delay:1.6s"/>
      <!-- Bali -->
      <circle class="map-city-dot" cx="436" cy="238" r="3" fill="#86efac" style="animation-delay:.6s"/>
      <!-- Manado -->
      <circle class="map-city-dot" cx="600" cy="148" r="3" fill="#f9c784" style="animation-delay:2s"/>
      <!-- Pontianak -->
      <circle class="map-city-dot" cx="415" cy="162" r="3" fill="#86efac" style="animation-delay:1s"/>
    </svg>
  </div>

  <!-- SVG bunga dekorasi -->
  <svg style="position:absolute;top:-8px;left:-8px;opacity:.07;z-index:4;pointer-events:none;" width="180" height="180" viewBox="0 0 180 180" fill="none">
    <?php foreach([0,60,120,180,240,300] as $r): ?>
    <ellipse cx="90" cy="48" rx="16" ry="34" fill="#fda4af" transform="rotate(<?=$r?> 90 90)"/>
    <?php endforeach; ?>
    <circle cx="90" cy="90" r="18" fill="#f9c784"/>
  </svg>
  <svg style="position:absolute;bottom:55px;right:-5px;opacity:.065;z-index:4;pointer-events:none;transform:scaleX(-1) rotate(15deg);" width="150" height="150" viewBox="0 0 150 150" fill="none">
    <?php foreach([0,72,144,216,288] as $r): ?>
    <ellipse cx="75" cy="38" rx="12" ry="28" fill="#c9a84c" transform="rotate(<?=$r?> 75 75)"/>
    <?php endforeach; ?>
    <circle cx="75" cy="75" r="14" fill="#fda4af"/>
  </svg>

  <!-- Content -->
  <div class="area-hero-content">
    <div class="area-eyebrow">
      <span class="area-eyebrow-dot"></span>
      Jangkauan Nasional
    </div>

    <h1>Area Layanan<br><em>Seluruh Indonesia</em></h1>

    <div class="area-hero-divider"></div>

    <p class="area-hero-desc">
      Chika Florist hadir di <?= $totalCities ?>+ kota di <?= $totalProvinces ?> provinsi Indonesia —
      siap mengirimkan bunga segar ke momen paling berharga Anda.
    </p>

    <!-- Stats -->
    <div class="area-stats">
      <div class="area-stat">
        <span class="area-stat-num" data-count="<?= $totalCities ?>" id="cnt-cities">0</span>
        <span class="area-stat-label">Kota Layanan</span>
      </div>
      <div class="area-stat">
        <span class="area-stat-num" data-count="<?= $totalProvinces ?>" id="cnt-prov">0</span>
        <span class="area-stat-label">Provinsi</span>
      </div>
      <div class="area-stat">
        <span class="area-stat-num" data-count="<?= $tierOneCities ?>" id="cnt-tier1">0</span>
        <span class="area-stat-label">Kota Utama</span>
      </div>
      <div class="area-stat">
        <span class="area-stat-num">24</span>
        <span class="area-stat-label">Jam Layanan</span>
      </div>
    </div>

    <div class="area-scroll-hint" onclick="document.getElementById('area-body').scrollIntoView({behavior:'smooth'})">
      <span>Cari Kota Anda</span>
      <div class="area-scroll-line"></div>
    </div>
  </div>

  <!-- Wave -->
  <div class="area-hero-wave">
    <svg viewBox="0 0 1440 56" preserveAspectRatio="none" style="width:100%;height:56px;display:block;">
      <path d="M0,20 C360,56 720,0 1080,28 C1260,42 1380,14 1440,22 L1440,56 L0,56 Z" fill="#fdf6ee"/>
    </svg>
  </div>
</section>

<!-- ==============================
     BODY
     ============================== -->
<div class="area-body" id="area-body">
  <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">

    <!-- Section header -->
    <div style="text-align:center;margin-bottom:2rem;">
      <p class="sec-eyebrow">Pilih Kota Anda</p>
      <h2 class="sec-title" style="margin:0 auto;">Daftar Area Layanan</h2>
      <div class="sec-gold" style="margin:.5rem auto 0;"></div>
    </div>

    <!-- Search -->
    <div class="area-search-wrap">
      <span class="area-search-icon">
        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
          <circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/>
        </svg>
      </span>
      <input type="text" class="area-search-input" id="city-search"
             placeholder="Cari kota... (contoh: Jakarta, Surabaya)"
             autocomplete="off" spellcheck="false">
      <button class="area-search-clear" id="search-clear" onclick="clearSearch()">
        <svg width="10" height="10" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M18 6L6 18M6 6l12 12"/></svg>
      </button>
    </div>

    <!-- No result state -->
    <div class="area-no-result" id="no-result">
      <div style="font-size:2.5rem;margin-bottom:.75rem;">🌸</div>
      <p id="no-result-text">Kota tidak ditemukan di daftar layanan kami saat ini.</p>
      <div class="notfound-cta">
        <p>Kota Anda belum ada? Hubungi kami — kami akan coba bantu pengiriman ke kota Anda!</p>
        <a href="<?= waLink('Halo, saya ingin tanya apakah bisa kirim bunga ke kota saya') ?>" target="_blank" class="btn-wa-sm">
          <svg width="14" height="14" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z"/><path d="M12 0C5.373 0 0 5.373 0 12c0 2.096.541 4.063 1.491 5.776L.057 23.04l5.43-1.424A11.947 11.947 0 0012 24c6.627 0 12-5.373 12-12S18.627 0 12 0zm0 21.818a9.818 9.818 0 01-5.015-1.371l-.36-.214-3.726.977.995-3.633-.235-.374A9.818 9.818 0 1112 21.818z"/></svg>
          Tanya via WhatsApp
        </a>
      </div>
    </div>

    <!-- Province Accordion -->
    <?php if (!empty($cities)): ?>
    <div class="prov-list" id="prov-list">
      <?php foreach ($byProvince as $prov => $provCities):
        $isFirst = array_key_first($byProvince) === $prov;
      ?>
      <div class="prov-item <?= $isFirst ? 'open' : '' ?>" data-prov="<?= htmlspecialchars(strtolower($prov)) ?>">

        <div class="prov-head" onclick="toggleProv(this)">
          <div class="prov-head-left">
            <div class="prov-icon">
              <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                <path d="M17.657 16.657L13.414 20.9a2 2 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                <circle cx="12" cy="11" r="3"/>
              </svg>
            </div>
            <span class="prov-name"><?= clean($prov) ?></span>
            <span class="prov-count"><?= count($provCities) ?> kota</span>
          </div>
          <div class="prov-chevron">
            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
              <path d="M6 9l6 6 6-6"/>
            </svg>
          </div>
        </div>

        <div class="prov-body" style="<?= $isFirst ? 'max-height:2000px' : '' ?>">
          <div class="prov-body-inner">
            <div class="city-grid">
              <?php foreach ($provCities as $city): ?>
              <a href="<?= BASE_URL ?>/toko-bunga-<?= $city['slug'] ?>"
                 class="city-card"
                 data-name="<?= htmlspecialchars(strtolower($city['name'])) ?>"
                 data-prov="<?= htmlspecialchars(strtolower($prov)) ?>">
                <span class="city-card-dot"></span>
                <span class="city-card-name"><?= clean($city['name']) ?></span>
                <?php if ($city['tier'] == 1): ?>
                <span class="city-tier-badge">Utama</span>
                <?php endif; ?>
              </a>
              <?php endforeach; ?>
            </div>
          </div>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
    <?php endif; ?>

    <!-- Info kota tidak ada -->
    <div style="text-align:center;margin-top:2rem;padding:1rem;">
      <p style="font-family:'Jost',sans-serif;font-size:.82rem;color:#9ca3af;">
        Kota Anda tidak ada di daftar?
        <a href="<?= waLink('Halo, saya ingin tanya apakah bisa kirim bunga ke kota saya') ?>" target="_blank"
           style="color:#e11d48;font-weight:600;text-decoration:underline;">Hubungi kami</a>
        — kami siap membantu!
      </p>
    </div>

  </div>
</div>

<!-- CTA BOTTOM -->
<section class="area-cta py-14 px-4 text-center">
  <div class="area-cta-pat"></div>
  <div style="position:absolute;width:320px;height:320px;border-radius:50%;background:rgba(244,63,94,.18);filter:blur(80px);top:-80px;left:50%;transform:translateX(-50%);pointer-events:none;"></div>
  <div style="position:relative;z-index:2;max-width:480px;margin:0 auto;">
    <svg width="32" height="32" viewBox="0 0 80 80" fill="none" style="display:inline-block;margin-bottom:.75rem;opacity:.7;">
      <?php foreach([0,60,120,180,240,300] as $r): ?>
      <ellipse cx="40" cy="22" rx="8" ry="18" fill="#fda4af" transform="rotate(<?=$r?> 40 40)"/>
      <?php endforeach; ?>
      <circle cx="40" cy="40" r="10" fill="#f9c784"/>
    </svg>
    <h2 style="font-family:'Cormorant Garamond',serif;font-size:1.8rem;font-weight:700;color:#fff;margin-bottom:.5rem;">
      Siap Kirim ke <em style="font-style:italic;color:#f9c784;">Kota Anda</em>
    </h2>
    <p style="font-family:'Jost',sans-serif;font-size:.86rem;color:rgba(255,255,255,.7);margin-bottom:1.5rem;line-height:1.65;">
      Tidak menemukan kota Anda? Hubungi kami — layanan 24 jam siap membantu.
    </p>
    <a href="<?= waLink('Halo Chika Florist, saya ingin pesan bunga. Boleh info area pengiriman?') ?>" target="_blank"
       style="display:inline-flex;align-items:center;gap:.6rem;font-family:'Jost',sans-serif;font-size:.88rem;font-weight:700;background:#fff;color:#e11d48;padding:.85rem 2rem;border-radius:999px;text-decoration:none;box-shadow:0 4px 20px rgba(0,0,0,.2);">
      <svg width="16" height="16" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z"/><path d="M12 0C5.373 0 0 5.373 0 12c0 2.096.541 4.063 1.491 5.776L.057 23.04l5.43-1.424A11.947 11.947 0 0012 24c6.627 0 12-5.373 12-12S18.627 0 12 0zm0 21.818a9.818 9.818 0 01-5.015-1.371l-.36-.214-3.726.977.995-3.633-.235-.374A9.818 9.818 0 1112 21.818z"/></svg>
      Hubungi via WhatsApp
    </a>
  </div>
</section>

<!-- ==============================
     JAVASCRIPT
     ============================== -->
<script>
// ---- Stars ----
(function(){
  var c = document.getElementById('area-stars');
  if (!c) return;
  for (var i = 0; i < 70; i++) {
    var s = document.createElement('div');
    s.className = 'area-star';
    var sz = Math.random() * 2 + .4;
    s.style.cssText = [
      'width:'+sz+'px','height:'+sz+'px',
      'top:'+(Math.random()*70)+'%',
      'left:'+(Math.random()*100)+'%',
      '--op:'+(Math.random()*.45+.1),
      '--dur:'+(Math.random()*3+2)+'s',
      '--delay:'+(-Math.random()*5)+'s'
    ].join(';');
    c.appendChild(s);
  }
})();

// ---- Animated counter ----
function animateCount(el, target, duration) {
  var start = 0, step = target / (duration / 16);
  var timer = setInterval(function(){
    start = Math.min(start + step, target);
    el.textContent = Math.floor(start);
    if (start >= target) clearInterval(timer);
  }, 16);
}
var counted = false;
var statsEl = document.querySelector('.area-stats');
if (statsEl) {
  var obs = new IntersectionObserver(function(entries){
    if (entries[0].isIntersecting && !counted) {
      counted = true;
      animateCount(document.getElementById('cnt-cities'),  <?= $totalCities ?>,  1200);
      animateCount(document.getElementById('cnt-prov'),    <?= $totalProvinces ?>, 1000);
      animateCount(document.getElementById('cnt-tier1'),   <?= $tierOneCities ?>,  900);
    }
  }, {threshold:.5});
  obs.observe(statsEl);
}

// ---- Accordion ----
function toggleProv(head) {
  var item = head.closest('.prov-item');
  var body = item.querySelector('.prov-body');
  var inner = body.querySelector('.prov-body-inner');
  var isOpen = item.classList.contains('open');

  // Tutup semua dulu
  document.querySelectorAll('.prov-item.open').forEach(function(el){
    el.classList.remove('open');
    el.querySelector('.prov-body').style.maxHeight = '0';
  });

  if (!isOpen) {
    item.classList.add('open');
    body.style.maxHeight = inner.scrollHeight + 'px';
  }
}

// ---- Real-time Search ----
var searchInput = document.getElementById('city-search');
var searchClear = document.getElementById('search-clear');
var noResult    = document.getElementById('no-result');
var noResultTxt = document.getElementById('no-result-text');
var provList    = document.getElementById('prov-list');

searchInput.addEventListener('input', function(){
  var q = this.value.trim().toLowerCase();
  searchClear.classList.toggle('visible', q.length > 0);

  if (!q) {
    // Reset semua
    document.querySelectorAll('.city-card').forEach(function(c){ c.classList.remove('hidden'); });
    document.querySelectorAll('.prov-item').forEach(function(p){ p.classList.remove('all-hidden'); });
    noResult.classList.remove('show');
    provList.style.display = '';
    return;
  }

  var totalVisible = 0;

  document.querySelectorAll('.prov-item').forEach(function(provItem){
    var cards  = provItem.querySelectorAll('.city-card');
    var prov   = provItem.dataset.prov || '';
    var visibleInProv = 0;

    cards.forEach(function(card){
      var name = card.dataset.name || '';
      var match = name.includes(q) || prov.includes(q);
      card.classList.toggle('hidden', !match);
      if (match) visibleInProv++;
    });

    if (visibleInProv === 0) {
      provItem.classList.add('all-hidden');
    } else {
      provItem.classList.remove('all-hidden');
      // Auto buka accordion yang ada hasil
      var body  = provItem.querySelector('.prov-body');
      var inner = provItem.querySelector('.prov-body-inner');
      provItem.classList.add('open');
      body.style.maxHeight = inner.scrollHeight + 'px';
      totalVisible += visibleInProv;
    }
  });

  if (totalVisible === 0) {
    noResult.classList.add('show');
    noResultTxt.textContent = '"' + searchInput.value.trim() + '" tidak ditemukan di daftar layanan kami saat ini.';
    provList.style.display = 'none';
  } else {
    noResult.classList.remove('show');
    provList.style.display = '';
  }
});

function clearSearch() {
  searchInput.value = '';
  searchInput.dispatchEvent(new Event('input'));
  searchInput.focus();
}

// ---- Init accordion pertama ----
document.addEventListener('DOMContentLoaded', function(){
  var first = document.querySelector('.prov-item.open');
  if (first) {
    var b = first.querySelector('.prov-body');
    b.style.maxHeight = b.querySelector('.prov-body-inner').scrollHeight + 'px';
  }
});
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>