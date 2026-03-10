<?php
$site_name   = getSetting('site_name', 'Chika Florist');
$footer_text = getSetting('footer_text', 'Toko bunga online terpercaya, melayani pengiriman ke seluruh Indonesia 24 jam nonstop.');
$logo        = getSetting('logo', 'logo.jpeg');
$allCities   = getActiveCities();
$mainCats    = getMainCategories();
?>

<style>
/* ══════════════════════════════
   FOOTER — Bright Floral Garden
══════════════════════════════ */
.cf-footer {
  position: relative;
  overflow: hidden;
  background: #faf7f4;
  font-family: 'Jost', sans-serif;
}

/* ── Background image layer (same as CTA) ── */
.cf-footer-bg-image {
  position: absolute;
  inset: 0;
  background-image: url('<?= BASE_URL ?>/assets/images/pwutih.jpeg');
  background-size: cover;
  background-position: center bottom;
  background-repeat: no-repeat;
  z-index: 0;
  opacity: 0.10;
}

/* ── Soft gradient overlay ── */
.cf-footer-bg-overlay {
  position: absolute;
  inset: 0;
  z-index: 1;
  background:
    radial-gradient(ellipse 70% 50% at 80% 10%, rgba(255,240,235,0.60) 0%, transparent 70%),
    linear-gradient(180deg,
      rgba(253,248,242,0.88) 0%,
      rgba(250,245,240,0.82) 50%,
      rgba(247,242,236,0.92) 100%
    );
}

/* Decorative atmosphere blobs */
.cf-footer-blob {
  position: absolute;
  border-radius: 50%;
  pointer-events: none;
  filter: blur(90px);
  z-index: 1;
}
.cf-footer-blob-1 {
  width: 500px; height: 500px;
  top: -150px; left: -100px;
  background: radial-gradient(circle, rgba(240,180,180,.18), transparent);
}
.cf-footer-blob-2 {
  width: 400px; height: 400px;
  bottom: 0; right: -80px;
  background: radial-gradient(circle, rgba(160,200,150,.14), transparent);
}
.cf-footer-blob-3 {
  width: 300px; height: 300px;
  top: 40%; left: 50%;
  background: radial-gradient(circle, rgba(255,220,200,.12), transparent);
}

/* ── Trellis pattern overlay ── */
.cf-footer-trellis {
  position: absolute;
  inset: 0;
  opacity: .018;
  background-image:
    repeating-linear-gradient(45deg,  #c0485a 0, #c0485a 1px, transparent 0, transparent 50%),
    repeating-linear-gradient(-45deg, #c0485a 0, #c0485a 1px, transparent 0, transparent 50%);
  background-size: 28px 28px;
  pointer-events: none;
  z-index: 1;
}

/* ── Top wave transition from CTA (light → light) ── */
.cf-footer-top-divider {
  position: relative;
  z-index: 2;
  line-height: 0;
  margin-bottom: -2px;
}
.cf-footer-top-divider svg { width: 100%; display: block; }

/* ── SEO city strip ── */
.cf-footer-cities {
  position: relative;
  z-index: 3;
  border-bottom: 1px solid rgba(160,100,90,.10);
  padding: 1.8rem 2rem;
}
.cf-footer-cities-inner {
  max-width: 1280px;
  margin: 0 auto;
}
.cf-footer-cities-label {
  font-size: .62rem;
  font-weight: 600;
  letter-spacing: .2em;
  text-transform: uppercase;
  color: rgba(140,80,70,.55);
  margin-bottom: .8rem;
  display: flex;
  align-items: center;
  gap: .5rem;
}
.cf-footer-cities-label::after {
  content: '';
  flex: 1;
  height: 1px;
  background: linear-gradient(90deg, rgba(180,120,110,.25), transparent);
}
.cf-footer-city-links {
  display: flex;
  flex-wrap: wrap;
  gap: .3rem .5rem;
  align-items: center;
}
.cf-footer-city-links a {
  font-size: .72rem;
  color: rgba(80,45,40,.40);
  text-decoration: none;
  transition: color .2s;
  white-space: nowrap;
}
.cf-footer-city-links a:hover { color: rgba(192,72,90,.85); }
.cf-footer-city-sep {
  color: rgba(160,100,90,.2);
  font-size: .6rem;
  line-height: 1;
}

/* ── Main footer grid ── */
.cf-footer-main {
  position: relative;
  z-index: 3;
  max-width: 1280px;
  margin: 0 auto;
  padding: 4rem 2rem 3rem;
  display: grid;
  grid-template-columns: 1.6fr 1fr 1fr 1fr;
  gap: 3rem;
}
@media (max-width: 1024px) {
  .cf-footer-main { grid-template-columns: 1fr 1fr; gap: 2.5rem; }
}
@media (max-width: 600px) {
  .cf-footer-main { grid-template-columns: 1fr; gap: 2rem; padding: 3rem 1.5rem 2rem; }
}

/* Brand column */
.cf-footer-logo-wrap {
  display: inline-block;
  margin-bottom: 1.4rem;
}
.cf-footer-logo-wrap img {
  height: 52px;
  width: auto;
  object-fit: contain;
  opacity: .92;
}
.cf-footer-logo-fallback {
  font-family: 'Cormorant Garamond', serif;
  font-size: 1.6rem;
  font-weight: 600;
  color: #3a2420;
  letter-spacing: .02em;
}

/* Rose ornament line */
.cf-footer-brand-ornament {
  display: flex;
  align-items: center;
  gap: .6rem;
  margin-bottom: 1.1rem;
}
.cf-footer-brand-ornament::before,
.cf-footer-brand-ornament::after {
  content: '';
  flex: 1;
  height: 1px;
  background: linear-gradient(90deg, rgba(192,72,90,.30), transparent);
}
.cf-footer-brand-ornament span {
  color: rgba(160,80,90,.65);
  font-size: .75rem;
  letter-spacing: .15em;
  white-space: nowrap;
}

.cf-footer-tagline {
  font-size: .82rem;
  color: rgba(60,35,30,.50);
  line-height: 1.75;
  margin-bottom: 1.6rem;
  max-width: 260px;
}

/* Social icons */
.cf-footer-socials {
  display: flex;
  gap: .6rem;
  margin-bottom: 1.8rem;
}
.cf-footer-social-btn {
  width: 36px; height: 36px;
  border-radius: 10px;
  border: 1px solid rgba(160,100,90,.18);
  background: rgba(255,255,255,.65);
  backdrop-filter: blur(8px);
  color: rgba(100,55,50,.45);
  display: flex;
  align-items: center;
  justify-content: center;
  text-decoration: none;
  transition: background .2s, border-color .2s, color .2s, transform .2s;
  box-shadow: 0 2px 8px rgba(160,100,90,.08);
}
.cf-footer-social-btn.wa:hover {
  background: rgba(37,211,102,.12);
  border-color: rgba(37,211,102,.40);
  color: #128c50;
  transform: translateY(-2px);
}

/* Jam operasional badge */
.cf-footer-badge {
  display: inline-flex;
  align-items: center;
  gap: .5rem;
  background: rgba(255,255,255,.70);
  backdrop-filter: blur(10px);
  border: 1px solid rgba(160,100,90,.18);
  border-radius: 999px;
  padding: .45rem 1rem;
  font-size: .68rem;
  color: rgba(80,45,40,.55);
  letter-spacing: .06em;
  box-shadow: 0 2px 10px rgba(160,100,90,.08);
}
.cf-footer-badge-dot {
  width: 6px; height: 6px;
  border-radius: 50%;
  background: #4ade80;
  box-shadow: 0 0 6px #4ade80;
  animation: cfPulseDot 1.8s ease-in-out infinite;
  flex-shrink: 0;
}
@keyframes cfPulseDot {
  0%,100% { box-shadow: 0 0 4px #4ade80; }
  50%      { box-shadow: 0 0 12px #4ade80, 0 0 20px rgba(74,222,128,.4); }
}

/* ── Nav columns ── */
.cf-footer-col-title {
  font-family: 'Cormorant Garamond', serif;
  font-size: .88rem;
  font-weight: 600;
  color: rgba(58,36,32,.80);
  letter-spacing: .12em;
  text-transform: uppercase;
  margin-bottom: 1.2rem;
  display: flex;
  align-items: center;
  gap: .5rem;
}
.cf-footer-col-title::after {
  content: '';
  flex: 1;
  height: 1px;
  background: linear-gradient(90deg, rgba(192,72,90,.25), transparent);
}

.cf-footer-nav {
  list-style: none;
  padding: 0; margin: 0;
  display: flex;
  flex-direction: column;
  gap: .55rem;
}
.cf-footer-nav li a {
  font-size: .78rem;
  color: rgba(60,35,30,.45);
  text-decoration: none;
  display: inline-flex;
  align-items: center;
  gap: .4rem;
  transition: color .2s, gap .2s;
}
.cf-footer-nav li a::before {
  content: '✦';
  font-size: .5rem;
  color: rgba(192,72,90,.30);
  transition: color .2s;
  flex-shrink: 0;
}
.cf-footer-nav li a:hover {
  color: rgba(192,72,90,.85);
  gap: .6rem;
}
.cf-footer-nav li a:hover::before {
  color: rgba(192,72,90,.70);
}

/* More cities link */
.cf-footer-more-cities {
  display: inline-flex;
  align-items: center;
  gap: .35rem;
  font-size: .7rem;
  color: rgba(192,72,90,.55) !important;
  margin-top: .4rem;
}
.cf-footer-more-cities:hover { color: rgba(192,72,90,.9) !important; }

/* ── Divider ornament ── */
.cf-footer-divider {
  position: relative;
  z-index: 3;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 1.2rem;
  padding: 0 2rem;
  margin: 0 auto;
  max-width: 1280px;
}
.cf-footer-divider::before,
.cf-footer-divider::after {
  content: '';
  flex: 1;
  height: 1px;
  background: linear-gradient(90deg, transparent, rgba(160,100,90,.15), transparent);
}
.cf-footer-divider-icon {
  color: rgba(160,80,90,.35);
  font-size: .8rem;
  letter-spacing: .2em;
  flex-shrink: 0;
}

/* ── Footer bottom ── */
.cf-footer-bottom {
  position: relative;
  z-index: 3;
  padding: 1.4rem 2rem;
  max-width: 1280px;
  margin: 0 auto;
  display: flex;
  flex-wrap: wrap;
  align-items: center;
  justify-content: space-between;
  gap: .8rem;
}
.cf-footer-copy {
  font-size: .7rem;
  color: rgba(60,35,30,.35);
  letter-spacing: .04em;
}
.cf-footer-seo-link {
  font-size: .7rem;
  color: rgba(192,72,90,.45);
  text-decoration: none;
  transition: color .2s;
  letter-spacing: .03em;
}
.cf-footer-seo-link:hover { color: rgba(192,72,90,.80); }

/* ── Bottom garden silhouette — BRIGHT ── */
.cf-footer-garden {
  position: relative;
  z-index: 2;
  line-height: 0;
  pointer-events: none;
}
.cf-footer-garden svg { width: 100%; display: block; }


/* ══════════════════════════════
   STICKY WA BUTTON
══════════════════════════════ */
.cf-wa-sticky {
  position: fixed;
  bottom: 1.5rem;
  right: 1.5rem;
  z-index: 9999;
  display: flex;
  align-items: center;
  gap: .6rem;
  background: linear-gradient(135deg, #25d366, #1da851);
  color: #fff;
  font-family: 'Jost', sans-serif;
  font-size: .85rem;
  font-weight: 600;
  padding: .8rem 1.4rem .8rem 1rem;
  border-radius: 999px;
  text-decoration: none;
  box-shadow:
    0 8px 24px rgba(18,140,80,.35),
    0 2px 8px rgba(0,0,0,.15);
  transition: transform .25s cubic-bezier(.22,1,.36,1), box-shadow .25s;
  letter-spacing: .02em;
}
.cf-wa-sticky:hover {
  transform: translateY(-3px) scale(1.04);
  box-shadow:
    0 16px 36px rgba(18,140,80,.45),
    0 4px 12px rgba(0,0,0,.15);
}
.cf-wa-sticky:active { transform: scale(.97); }
.cf-wa-sticky::before {
  content: '';
  position: absolute;
  inset: -4px;
  border-radius: 999px;
  border: 2px solid rgba(37,211,102,.4);
  animation: cfWaRing 2s ease-out infinite;
}
@keyframes cfWaRing {
  0%   { transform: scale(1); opacity: .8; }
  100% { transform: scale(1.18); opacity: 0; }
}
.cf-wa-sticky-icon { width: 22px; height: 22px; flex-shrink: 0; position: relative; z-index: 1; }
.cf-wa-sticky-label { position: relative; z-index: 1; white-space: nowrap; }
@media (max-width: 480px) {
  .cf-wa-sticky { bottom: 1rem; right: 1rem; font-size: .78rem; padding: .7rem 1.1rem .7rem .85rem; gap: .5rem; }
  .cf-wa-sticky-icon { width: 18px; height: 18px; }
}
</style>

<!-- ══ Top Wave Transition — light to light ══ -->
<div class="cf-footer-top-divider">
  <svg viewBox="0 0 1440 60" preserveAspectRatio="none" xmlns="http://www.w3.org/2000/svg">
    <!-- soft warm wave separating CTA garden from footer -->
    <path d="M0,30 C240,55 480,5 720,28 C960,50 1200,10 1440,30 L1440,60 L0,60 Z"
          fill="#f3ede6" opacity=".8"/>
    <path d="M0,40 C240,10 480,55 720,35 C960,15 1200,50 1440,38 L1440,60 L0,60 Z"
          fill="#faf7f4"/>
  </svg>
</div>

<footer class="cf-footer">

  <!-- Background layers -->
  <div class="cf-footer-bg-image"></div>
  <div class="cf-footer-bg-overlay"></div>

  <!-- Atmosphere -->
  <div class="cf-footer-blob cf-footer-blob-1"></div>
  <div class="cf-footer-blob cf-footer-blob-2"></div>
  <div class="cf-footer-blob cf-footer-blob-3"></div>
  <div class="cf-footer-trellis"></div>

  <!-- ── SEO City Strip ── -->
  <?php if (!empty($allCities)): ?>
  <div class="cf-footer-cities">
    <div class="cf-footer-cities-inner">
      <div class="cf-footer-cities-label">✦ Area Layanan Kami</div>
      <div class="cf-footer-city-links">
        <?php foreach ($allCities as $i => $city): ?>
        <a href="<?= BASE_URL ?>/toko-bunga-<?= $city['slug'] ?>">Toko Bunga <?= clean($city['name']) ?></a>
        <?php if ($i < count($allCities) - 1): ?>
        <span class="cf-footer-city-sep">·</span>
        <?php endif; ?>
        <?php endforeach; ?>
      </div>
    </div>
  </div>
  <?php endif; ?>

  <!-- ── Main Grid ── -->
  <div class="cf-footer-main">

    <!-- Brand -->
    <div class="cf-footer-brand">
      <a href="<?= BASE_URL ?>/" class="cf-footer-logo-wrap">
        <img src="<?= UPLOAD_URL . $logo ?>"
             alt="<?= clean($site_name) ?>"
             onerror="this.style.display='none'; this.nextElementSibling.style.display='block'">
        <span class="cf-footer-logo-fallback" style="display:none"><?= clean($site_name) ?></span>
      </a>

      <div class="cf-footer-brand-ornament">
        <span>✦ Chika Florist ✦</span>
      </div>

      <p class="cf-footer-tagline"><?= clean($footer_text) ?></p>

      <!-- Social — WA only -->
      <div class="cf-footer-socials">
        <a href="<?= waLink() ?>" target="_blank" rel="noopener" class="cf-footer-social-btn wa" title="WhatsApp">
          <svg width="15" height="15" fill="currentColor" viewBox="0 0 24 24">
            <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z"/>
            <path d="M12 0C5.373 0 0 5.373 0 12c0 2.096.541 4.063 1.491 5.776L.057 23.04l5.43-1.424A11.947 11.947 0 0012 24c6.627 0 12-5.373 12-12S18.627 0 12 0zm0 21.818a9.818 9.818 0 01-5.015-1.371l-.36-.214-3.726.977.995-3.633-.235-.374A9.818 9.818 0 1112 21.818z"/>
          </svg>
        </a>
      </div>

      <!-- Live badge -->
      <div class="cf-footer-badge">
        <span class="cf-footer-badge-dot"></span>
        Buka 24 Jam Nonstop
      </div>
    </div>

    <!-- Produk -->
    <div>
      <div class="cf-footer-col-title">Koleksi Bunga</div>
      <ul class="cf-footer-nav">
        <?php foreach ($mainCats as $cat): ?>
        <li><a href="<?= BASE_URL ?>/<?= $cat['slug'] ?>"><?= clean($cat['name']) ?></a></li>
        <?php endforeach; ?>
      </ul>
    </div>

    <!-- Layanan -->
    <div>
      <div class="cf-footer-col-title">Layanan</div>
      <ul class="cf-footer-nav">
        <li><a href="<?= BASE_URL ?>/toko-bunga-online-24-jam-indonesia">Toko Bunga Online 24 Jam</a></li>
        <li><a href="<?= BASE_URL ?>/kirim-bunga-hari-ini">Kirim Bunga Hari Ini</a></li>
        <li><a href="<?= BASE_URL ?>/florist-terdekat">Florist Terdekat</a></li>
        <li><a href="<?= BASE_URL ?>/pesan-bunga-online">Pesan Bunga Online</a></li>
        <li><a href="<?= BASE_URL ?>/area-layanan">Semua Area Layanan</a></li>
      </ul>
    </div>

    <!-- Kota Populer -->
    <div>
      <div class="cf-footer-col-title">Kota Populer</div>
      <ul class="cf-footer-nav">
        <?php foreach (array_slice($allCities, 0, 8) as $city): ?>
        <li><a href="<?= BASE_URL ?>/toko-bunga-<?= $city['slug'] ?>">Toko Bunga <?= clean($city['name']) ?></a></li>
        <?php endforeach; ?>
        <?php if (count($allCities) > 8): ?>
        <li>
          <a href="<?= BASE_URL ?>/area-layanan" class="cf-footer-more-cities">
            + <?= count($allCities) - 8 ?> kota lainnya →
          </a>
        </li>
        <?php endif; ?>
      </ul>
    </div>

  </div><!-- /.cf-footer-main -->

  <!-- Divider ornament -->
  <div style="padding: 0 2rem; position: relative; z-index: 3;">
    <div class="cf-footer-divider">
      <span class="cf-footer-divider-icon">✦ &nbsp; ✿ &nbsp; ✦</span>
    </div>
  </div>

  <!-- Bottom bar -->
  <div class="cf-footer-bottom">
    <p class="cf-footer-copy">
      © <?= date('Y') ?> <?= clean($site_name) ?>. All rights reserved.
    </p>
    <a href="<?= BASE_URL ?>/toko-bunga-online-24-jam-indonesia" class="cf-footer-seo-link">
      Toko Bunga Online 24 Jam Indonesia
    </a>
  </div>

  <!-- ── Garden silhouette decoration — BRIGHT ── -->
  <div class="cf-footer-garden">
    <svg viewBox="0 0 1440 80" preserveAspectRatio="none" xmlns="http://www.w3.org/2000/svg">
      <!-- Soft ground -->
      <rect x="0" y="68" width="1440" height="12" fill="#ede6de"/>

      <!-- Stems — warm sage -->
      <rect x="80"   y="30" width="2" height="50" fill="#8aaa78" opacity=".6"/>
      <rect x="240"  y="20" width="2" height="60" fill="#8aaa78" opacity=".55"/>
      <rect x="440"  y="15" width="3" height="65" fill="#8aaa78" opacity=".6"/>
      <rect x="700"  y="10" width="3" height="70" fill="#8aaa78" opacity=".65"/>
      <rect x="920"  y="18" width="2" height="62" fill="#8aaa78" opacity=".55"/>
      <rect x="1140" y="22" width="2" height="58" fill="#8aaa78" opacity=".5"/>
      <rect x="1340" y="28" width="2" height="52" fill="#8aaa78" opacity=".55"/>

      <!-- Rose heads — cream/blush -->
      <ellipse cx="81"   cy="28" rx="12" ry="8"  fill="#f0d8d8" opacity=".9"/>
      <ellipse cx="81"   cy="28" rx="7"  ry="5"  fill="#e0b8b8" opacity=".85"/>
      <circle  cx="81"   cy="28" r="3"            fill="#c89098" opacity=".7"/>

      <ellipse cx="241"  cy="18" rx="14" ry="9"  fill="#f8f0ec" opacity=".9"/>
      <ellipse cx="241"  cy="18" rx="9"  ry="6"  fill="#ecd8cc" opacity=".85"/>
      <circle  cx="241"  cy="18" r="3.5"          fill="#c8a890" opacity=".7"/>

      <ellipse cx="441"  cy="13" rx="16" ry="10" fill="#fff8f5" opacity=".95"/>
      <ellipse cx="441"  cy="13" rx="10" ry="7"  fill="#f0dcd8" opacity=".9"/>
      <circle  cx="441"  cy="13" r="4"            fill="#d4a0a8" opacity=".8"/>

      <ellipse cx="701"  cy="8"  rx="18" ry="11" fill="#fff8f5" opacity=".95"/>
      <ellipse cx="701"  cy="8"  rx="11" ry="7"  fill="#f5e8e5" opacity=".92"/>
      <circle  cx="701"  cy="8"  r="4.5"          fill="#e0b8c0" opacity=".85"/>

      <ellipse cx="921"  cy="16" rx="14" ry="9"  fill="#fce8ec" opacity=".9"/>
      <ellipse cx="921"  cy="16" rx="9"  ry="6"  fill="#f0d0d5" opacity=".85"/>
      <circle  cx="921"  cy="16" r="3.5"          fill="#d09098" opacity=".75"/>

      <ellipse cx="1141" cy="20" rx="12" ry="8"  fill="#f5eef0" opacity=".9"/>
      <ellipse cx="1141" cy="20" rx="7"  ry="5"  fill="#e8dce0" opacity=".85"/>
      <circle  cx="1141" cy="20" r="3"            fill="#c8a0a8" opacity=".7"/>

      <ellipse cx="1341" cy="26" rx="12" ry="8"  fill="#f8f0ec" opacity=".9"/>
      <ellipse cx="1341" cy="26" rx="7"  ry="5"  fill="#ece0d8" opacity=".85"/>
      <circle  cx="1341" cy="26" r="3"            fill="#c8a890" opacity=".7"/>

      <!-- Baby's breath clusters -->
      <circle cx="160"  cy="42" r="2.5" fill="#e8e8e0" opacity=".8"/>
      <circle cx="168"  cy="38" r="2"   fill="#f0f0e8" opacity=".75"/>
      <circle cx="175"  cy="44" r="2.2" fill="#e8e8e0" opacity=".7"/>
      <circle cx="560"  cy="35" r="2.5" fill="#e8e8e0" opacity=".8"/>
      <circle cx="568"  cy="31" r="2"   fill="#f0f0e8" opacity=".75"/>
      <circle cx="840"  cy="30" r="2.5" fill="#e8e8e0" opacity=".8"/>
      <circle cx="848"  cy="26" r="2"   fill="#f0f0e8" opacity=".75"/>
      <circle cx="1050" cy="38" r="2.5" fill="#e8e8e0" opacity=".75"/>
      <circle cx="1058" cy="34" r="2"   fill="#f0f0e8" opacity=".70"/>

      <!-- Leaves — bright sage -->
      <path d="M78  45 Q65  38 62  28 Q74  34 78  45Z" fill="#8aaa78" opacity=".8"/>
      <path d="M84  48 Q96  41 98  30 Q87  37 84  48Z" fill="#9aba88" opacity=".7"/>
      <path d="M438 40 Q425 32 422 22 Q434 28 438 40Z" fill="#8aaa78" opacity=".8"/>
      <path d="M445 42 Q456 34 459 24 Q448 30 445 42Z" fill="#9aba88" opacity=".7"/>
      <path d="M698 38 Q685 30 682 18 Q694 26 698 38Z" fill="#8aaa78" opacity=".8"/>
      <path d="M705 40 Q716 32 719 22 Q708 28 705 40Z" fill="#9aba88" opacity=".7"/>

      <!-- Grass blades — fresh green -->
      <path d="M50  68 Q46  52 48  46" stroke="#88b070" stroke-width="1.5" fill="none" opacity=".6"/>
      <path d="M130 68 Q126 50 129 44" stroke="#88b070" stroke-width="2"   fill="none" opacity=".6"/>
      <path d="M310 68 Q308 53 312 47" stroke="#88b070" stroke-width="1.5" fill="none" opacity=".6"/>
      <path d="M520 68 Q518 51 521 45" stroke="#88b070" stroke-width="2"   fill="none" opacity=".6"/>
      <path d="M800 68 Q797 50 801 44" stroke="#88b070" stroke-width="2"   fill="none" opacity=".6"/>
      <path d="M1010 68 Q1008 52 1011 46" stroke="#88b070" stroke-width="1.5" fill="none" opacity=".6"/>
      <path d="M1220 68 Q1218 51 1221 45" stroke="#88b070" stroke-width="2"   fill="none" opacity=".6"/>
      <path d="M1400 68 Q1398 53 1401 47" stroke="#88b070" stroke-width="1.5" fill="none" opacity=".6"/>

      <!-- Small wildflowers -->
      <circle cx="200"  cy="64" r="4"   fill="#fce8e0" opacity=".9"/>
      <circle cx="200"  cy="64" r="2"   fill="#e8a0b0" opacity=".9"/>
      <circle cx="218"  cy="62" r="3.5" fill="#fce8c8" opacity=".8"/>
      <circle cx="218"  cy="62" r="1.8" fill="#d4b070" opacity=".8"/>
      <circle cx="640"  cy="63" r="4"   fill="#fce8e0" opacity=".9"/>
      <circle cx="640"  cy="63" r="2"   fill="#e8a0b0" opacity=".9"/>
      <circle cx="658"  cy="61" r="3.5" fill="#fce8c8" opacity=".8"/>
      <circle cx="658"  cy="61" r="1.8" fill="#d4b070" opacity=".8"/>
      <circle cx="1100" cy="63" r="4"   fill="#fce8e0" opacity=".9"/>
      <circle cx="1100" cy="63" r="2"   fill="#e8a0b0" opacity=".85"/>
      <circle cx="1118" cy="61" r="3.5" fill="#fce8c8" opacity=".8"/>
      <circle cx="1118" cy="61" r="1.8" fill="#d4b070" opacity=".75"/>

      <!-- Soft light on ground -->
      <ellipse cx="720" cy="70" rx="220" ry="8" fill="rgba(255,235,215,.20)"/>
    </svg>
  </div>

</footer>

<!-- ══ Sticky WhatsApp Button ══ -->
<a href="<?= waLink('Halo Chika Florist, saya ingin memesan bunga') ?>"
   target="_blank"
   rel="noopener"
   class="cf-wa-sticky"
   aria-label="Pesan via WhatsApp">
  <svg class="cf-wa-sticky-icon" fill="currentColor" viewBox="0 0 24 24">
    <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z"/>
    <path d="M12 0C5.373 0 0 5.373 0 12c0 2.096.541 4.063 1.491 5.776L.057 23.04l5.43-1.424A11.947 11.947 0 0012 24c6.627 0 12-5.373 12-12S18.627 0 12 0zm0 21.818a9.818 9.818 0 01-5.015-1.371l-.36-.214-3.726.977.995-3.633-.235-.374A9.818 9.818 0 1112 21.818z"/>
  </svg>
  <span class="cf-wa-sticky-label">Pesan Sekarang</span>
</a>

</body>
</html>