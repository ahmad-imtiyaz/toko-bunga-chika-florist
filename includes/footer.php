<?php
$site_name   = getSetting('site_name', 'Chika Florist');
$footer_text = getSetting('footer_text', 'Toko bunga online terpercaya, melayani pengiriman ke seluruh Indonesia 24 jam nonstop.');
$logo        = getSetting('logo', 'logo.jpeg');
$allCities   = getActiveCities();
$mainCats    = getMainCategories();
?>

<style>
/* ══════════════════════════════
   FOOTER — Taman Bunga Theme
══════════════════════════════ */
.cf-footer {
  position: relative;
  overflow: hidden;
  background: linear-gradient(175deg, #1c0c12 0%, #2a1018 45%, #1a1a0e 100%);
  font-family: 'Jost', sans-serif;
}

/* Decorative atmosphere blobs */
.cf-footer-blob {
  position: absolute;
  border-radius: 50%;
  pointer-events: none;
  filter: blur(90px);
  z-index: 0;
}
.cf-footer-blob-1 {
  width: 500px; height: 500px;
  top: -150px; left: -100px;
  background: radial-gradient(circle, rgba(192,72,90,.12), transparent);
}
.cf-footer-blob-2 {
  width: 400px; height: 400px;
  bottom: 0; right: -80px;
  background: radial-gradient(circle, rgba(125,155,118,.1), transparent);
}
.cf-footer-blob-3 {
  width: 300px; height: 300px;
  top: 40%; left: 50%;
  background: radial-gradient(circle, rgba(201,168,76,.07), transparent);
}

/* ── Trellis pattern overlay ── */
.cf-footer-trellis {
  position: absolute;
  inset: 0;
  opacity: .025;
  background-image:
    repeating-linear-gradient(45deg,  #c0485a 0, #c0485a 1px, transparent 0, transparent 50%),
    repeating-linear-gradient(-45deg, #c0485a 0, #c0485a 1px, transparent 0, transparent 50%);
  background-size: 28px 28px;
  pointer-events: none;
  z-index: 0;
}

/* ── Top floral SVG divider ── */
.cf-footer-top-divider {
  position: relative;
  z-index: 1;
  line-height: 0;
  margin-bottom: -2px;
}
.cf-footer-top-divider svg { width: 100%; display: block; }

/* ── SEO city strip ── */
.cf-footer-cities {
  position: relative;
  z-index: 2;
  border-bottom: 1px solid rgba(255,255,255,.06);
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
  color: rgba(201,168,76,.5);
  margin-bottom: .8rem;
  display: flex;
  align-items: center;
  gap: .5rem;
}
.cf-footer-cities-label::after {
  content: '';
  flex: 1;
  height: 1px;
  background: linear-gradient(90deg, rgba(201,168,76,.2), transparent);
}
.cf-footer-city-links {
  display: flex;
  flex-wrap: wrap;
  gap: .3rem .5rem;
  align-items: center;
}
.cf-footer-city-links a {
  font-size: .72rem;
  color: rgba(255,255,255,.3);
  text-decoration: none;
  transition: color .2s;
  white-space: nowrap;
}
.cf-footer-city-links a:hover { color: rgba(192,72,90,.8); }
.cf-footer-city-sep {
  color: rgba(255,255,255,.1);
  font-size: .6rem;
  line-height: 1;
}

/* ── Main footer grid ── */
.cf-footer-main {
  position: relative;
  z-index: 2;
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
  filter: brightness(1.1);
}
.cf-footer-logo-fallback {
  font-family: 'Cormorant Garamond', serif;
  font-size: 1.6rem;
  font-weight: 600;
  color: #fff;
  letter-spacing: .02em;
}

/* Gold ornament line */
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
  background: linear-gradient(90deg, rgba(201,168,76,.5), transparent);
}
.cf-footer-brand-ornament span {
  color: rgba(201,168,76,.6);
  font-size: .75rem;
  letter-spacing: .15em;
  white-space: nowrap;
}

.cf-footer-tagline {
  font-size: .82rem;
  color: rgba(255,255,255,.38);
  line-height: 1.75;
  margin-bottom: 1.6rem;
  max-width: 260px;
}

/* Social icons — WA only */
.cf-footer-socials {
  display: flex;
  gap: .6rem;
  margin-bottom: 1.8rem;
}
.cf-footer-social-btn {
  width: 36px; height: 36px;
  border-radius: 10px;
  border: 1px solid rgba(255,255,255,.08);
  background: rgba(255,255,255,.04);
  color: rgba(255,255,255,.4);
  display: flex;
  align-items: center;
  justify-content: center;
  text-decoration: none;
  transition: background .2s, border-color .2s, color .2s, transform .2s;
}
.cf-footer-social-btn.wa:hover {
  background: rgba(37,211,102,.2);
  border-color: rgba(37,211,102,.4);
  color: rgba(255,255,255,.9);
  transform: translateY(-2px);
}

/* Jam operasional badge */
.cf-footer-badge {
  display: inline-flex;
  align-items: center;
  gap: .5rem;
  background: rgba(255,255,255,.04);
  border: 1px solid rgba(201,168,76,.15);
  border-radius: 999px;
  padding: .45rem 1rem;
  font-size: .68rem;
  color: rgba(255,255,255,.45);
  letter-spacing: .06em;
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
  color: rgba(255,255,255,.85);
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
  background: linear-gradient(90deg, rgba(192,72,90,.3), transparent);
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
  color: rgba(255,255,255,.33);
  text-decoration: none;
  display: inline-flex;
  align-items: center;
  gap: .4rem;
  transition: color .2s, gap .2s;
}
.cf-footer-nav li a::before {
  content: '✦';
  font-size: .5rem;
  color: rgba(201,168,76,.3);
  transition: color .2s;
  flex-shrink: 0;
}
.cf-footer-nav li a:hover {
  color: rgba(255,255,255,.75);
  gap: .6rem;
}
.cf-footer-nav li a:hover::before {
  color: rgba(192,72,90,.7);
}

/* More cities link */
.cf-footer-more-cities {
  display: inline-flex;
  align-items: center;
  gap: .35rem;
  font-size: .7rem;
  color: rgba(192,72,90,.6) !important;
  margin-top: .4rem;
}
.cf-footer-more-cities:hover { color: rgba(192,72,90,.9) !important; }

/* ── Floral divider mid-footer ── */
.cf-footer-divider {
  position: relative;
  z-index: 2;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 1.2rem;
  padding: 0 2rem;
  margin: 0 auto 0;
  max-width: 1280px;
}
.cf-footer-divider::before,
.cf-footer-divider::after {
  content: '';
  flex: 1;
  height: 1px;
  background: linear-gradient(90deg, transparent, rgba(255,255,255,.08), transparent);
}
.cf-footer-divider-icon {
  color: rgba(201,168,76,.3);
  font-size: .8rem;
  letter-spacing: .2em;
  flex-shrink: 0;
}

/* ── Footer bottom ── */
.cf-footer-bottom {
  position: relative;
  z-index: 2;
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
  color: rgba(255,255,255,.2);
  letter-spacing: .04em;
}
.cf-footer-seo-link {
  font-size: .7rem;
  color: rgba(192,72,90,.4);
  text-decoration: none;
  transition: color .2s;
  letter-spacing: .03em;
}
.cf-footer-seo-link:hover { color: rgba(192,72,90,.7); }

/* ── Bottom garden silhouette ── */
.cf-footer-garden {
  position: relative;
  z-index: 1;
  line-height: 0;
  opacity: .15;
  pointer-events: none;
}
.cf-footer-garden svg { width: 100%; display: block; }


/* ══════════════════════════════
   STICKY WA BUTTON
   — desktop & mobile always show "Pesan Sekarang"
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
    0 8px 24px rgba(18,140,80,.4),
    0 2px 8px rgba(0,0,0,.2);
  transition: transform .25s cubic-bezier(.22,1,.36,1), box-shadow .25s;
  letter-spacing: .02em;
}
.cf-wa-sticky:hover {
  transform: translateY(-3px) scale(1.04);
  box-shadow:
    0 16px 36px rgba(18,140,80,.5),
    0 4px 12px rgba(0,0,0,.2);
}
.cf-wa-sticky:active {
  transform: scale(.97);
}

/* Pulse ring */
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

.cf-wa-sticky-icon {
  width: 22px; height: 22px;
  flex-shrink: 0;
  position: relative;
  z-index: 1;
}
.cf-wa-sticky-label {
  position: relative;
  z-index: 1;
  white-space: nowrap;
}

/* Mobile: slightly smaller but still shows label */
@media (max-width: 480px) {
  .cf-wa-sticky {
    bottom: 1rem;
    right: 1rem;
    font-size: .78rem;
    padding: .7rem 1.1rem .7rem .85rem;
    gap: .5rem;
  }
  .cf-wa-sticky-icon { width: 18px; height: 18px; }
}
</style>

<!-- ══ Top Wave Transition from CTA ══ -->
<div class="cf-footer-top-divider">
  <svg viewBox="0 0 1440 60" preserveAspectRatio="none" xmlns="http://www.w3.org/2000/svg">
    <path d="M0,40 C240,0 480,60 720,30 C960,0 1200,50 1440,40 L1440,60 L0,60 Z" fill="#1c0c12"/>
  </svg>
</div>

<footer class="cf-footer">
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
          <svg width="15" height="15" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z"/><path d="M12 0C5.373 0 0 5.373 0 12c0 2.096.541 4.063 1.491 5.776L.057 23.04l5.43-1.424A11.947 11.947 0 0012 24c6.627 0 12-5.373 12-12S18.627 0 12 0zm0 21.818a9.818 9.818 0 01-5.015-1.371l-.36-.214-3.726.977.995-3.633-.235-.374A9.818 9.818 0 1112 21.818z"/></svg>
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
  <div style="padding: 0 2rem; position: relative; z-index: 2;">
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

  <!-- Garden silhouette decoration -->
  <div class="cf-footer-garden">
    <svg viewBox="0 0 1440 80" preserveAspectRatio="none" xmlns="http://www.w3.org/2000/svg">
      <!-- Stems -->
      <rect x="80"   y="30" width="2" height="50" fill="#c0485a" opacity=".5"/>
      <rect x="240"  y="20" width="2" height="60" fill="#c0485a" opacity=".4"/>
      <rect x="440"  y="15" width="3" height="65" fill="#c0485a" opacity=".5"/>
      <rect x="700"  y="10" width="3" height="70" fill="#c0485a" opacity=".6"/>
      <rect x="920"  y="18" width="2" height="62" fill="#c0485a" opacity=".45"/>
      <rect x="1140" y="22" width="2" height="58" fill="#c0485a" opacity=".4"/>
      <rect x="1340" y="28" width="2" height="52" fill="#c0485a" opacity=".5"/>
      <!-- Rose heads -->
      <ellipse cx="81"   cy="28" rx="12" ry="8"  fill="#c0485a" opacity=".4"/>
      <ellipse cx="241"  cy="18" rx="14" ry="9"  fill="#c0485a" opacity=".35"/>
      <ellipse cx="441"  cy="13" rx="16" ry="10" fill="#c0485a" opacity=".45"/>
      <ellipse cx="701"  cy="8"  rx="18" ry="11" fill="#c0485a" opacity=".5"/>
      <ellipse cx="921"  cy="16" rx="14" ry="9"  fill="#c0485a" opacity=".4"/>
      <ellipse cx="1141" cy="20" rx="12" ry="8"  fill="#c0485a" opacity=".35"/>
      <ellipse cx="1341" cy="26" rx="12" ry="8"  fill="#c0485a" opacity=".4"/>
      <!-- Leaves -->
      <path d="M78 45 Q65 38 62 28 Q74 34 78 45Z" fill="#7d9b76" opacity=".4"/>
      <path d="M84 48 Q96 41 98 30 Q87 37 84 48Z" fill="#7d9b76" opacity=".35"/>
      <path d="M438 40 Q425 32 422 22 Q434 28 438 40Z" fill="#7d9b76" opacity=".4"/>
      <path d="M445 42 Q456 34 459 24 Q448 30 445 42Z" fill="#7d9b76" opacity=".35"/>
      <!-- Ground -->
      <rect x="0" y="75" width="1440" height="5" fill="#1a1a0e" opacity=".5"/>
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