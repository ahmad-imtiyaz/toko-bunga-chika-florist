<!-- CTA - Bright Elegant Floral Garden -->
<style>
@import url('https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,300;0,400;0,600;1,300;1,400&family=Jost:wght@300;400;500;600&display=swap');

.cta-section {
  position: relative;
  padding: 7rem 0 0;
  overflow: hidden;
  background: #faf7f4;
}

/* ── Background image layer ── */
.cta-bg-image {
  position: absolute;
  inset: 0;
  background-image: url('<?= BASE_URL ?>/assets/images/pwutih.jpeg');
  background-size: cover;
  background-position: center top;
  background-repeat: no-repeat;
  z-index: 0;
  opacity: 0.22;
}

/* ── Soft gradient overlay ── */
.cta-bg-overlay {
  position: absolute;
  inset: 0;
  z-index: 1;
  background:
    radial-gradient(ellipse 80% 60% at 50% 0%, rgba(255,250,245,0.55) 0%, transparent 70%),
    linear-gradient(180deg,
      rgba(253,248,242,0.82) 0%,
      rgba(255,252,248,0.70) 40%,
      rgba(250,244,238,0.85) 80%,
      rgba(247,240,233,0.98) 100%
    );
}

/* ── Floral corner decorations ── */
.cta-floral-tl,
.cta-floral-tr,
.cta-floral-bl,
.cta-floral-br {
  position: absolute;
  z-index: 2;
  pointer-events: none;
  background-image: url('<?= BASE_URL ?>/assets/images/pwutih.jpeg');
  background-size: cover;
  border-radius: 50%;
  opacity: 0.18;
  filter: saturate(0.6) brightness(1.1);
}
.cta-floral-tl {
  width: 340px; height: 340px;
  top: -80px; left: -80px;
  background-position: center;
  opacity: 0.20;
}
.cta-floral-tr {
  width: 260px; height: 260px;
  top: -50px; right: -50px;
  background-position: center;
  opacity: 0.15;
  filter: saturate(0.5) brightness(1.2);
}

/* ── Petal particles ── */
.cta-petals {
  position: absolute;
  inset: 0;
  pointer-events: none;
  z-index: 2;
  overflow: hidden;
}
.cta-petal {
  position: absolute;
  border-radius: 50% 0 50% 0;
  animation: petalFall linear infinite;
  opacity: 0;
}
@keyframes petalFall {
  0%   { opacity: 0; transform: translateY(-20px) rotate(0deg); }
  10%  { opacity: 0.6; }
  90%  { opacity: 0.3; }
  100% { opacity: 0; transform: translateY(100vh) rotate(360deg) translateX(40px); }
}

/* ── Deco lines ── */
.cta-deco-line {
  position: absolute;
  z-index: 2;
  pointer-events: none;
}
.cta-deco-line-left {
  left: 5%;
  top: 10%;
  width: 1px;
  height: 200px;
  background: linear-gradient(180deg, transparent, rgba(180,140,130,0.25), transparent);
}
.cta-deco-line-right {
  right: 5%;
  top: 15%;
  width: 1px;
  height: 160px;
  background: linear-gradient(180deg, transparent, rgba(180,140,130,0.2), transparent);
}

/* ── Glow orbs (soft warm) ── */
.cta-orb {
  position: absolute;
  border-radius: 50%;
  filter: blur(70px);
  pointer-events: none;
  z-index: 2;
}
.cta-orb-1 {
  width: 450px; height: 450px;
  top: -100px; left: -80px;
  background: radial-gradient(circle, rgba(255,220,210,.30), transparent);
}
.cta-orb-2 {
  width: 380px; height: 380px;
  top: -60px; right: -60px;
  background: radial-gradient(circle, rgba(220,235,215,.25), transparent);
}
.cta-orb-3 {
  width: 320px; height: 320px;
  bottom: 100px; left: 35%;
  background: radial-gradient(circle, rgba(255,240,230,.20), transparent);
}

/* ── Content ── */
.cta-content {
  position: relative;
  z-index: 5;
  max-width: 820px;
  margin: 0 auto;
  padding: 0 2rem 5rem;
  text-align: center;
}

/* Eyebrow */
.cta-eyebrow {
  display: inline-flex; align-items: center; gap: .7rem;
  font-family: 'Jost', sans-serif; font-size: .68rem; font-weight: 500;
  letter-spacing: .22em; text-transform: uppercase;
  color: rgba(160,100,90,.75);
  margin-bottom: 1.5rem;
  animation: ctaFadeUp .8s .1s ease both;
}
.cta-eyebrow::before, .cta-eyebrow::after {
  content: ''; display: block;
  width: 36px; height: 1px;
  background: linear-gradient(90deg, transparent, rgba(180,120,110,.45));
}
.cta-eyebrow::after { transform: scaleX(-1); }

/* Title */
.cta-title {
  font-family: 'Cormorant Garamond', serif;
  font-size: clamp(2.4rem, 5.5vw, 4rem);
  font-weight: 300;
  color: #3a2420;
  line-height: 1.12;
  margin-bottom: 1rem;
  animation: ctaFadeUp .8s .2s ease both;
}
.cta-title em {
  font-style: italic;
  background: linear-gradient(135deg, #c0485a, #e8778a);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  background-clip: text;
}
.cta-title .cta-title-green {
  display: block;
  font-size: clamp(1.2rem, 2.5vw, 1.8rem);
  background: linear-gradient(135deg, #5a8a60, #7db87f);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  background-clip: text;
  letter-spacing: .06em;
  margin-top: .3rem;
}

/* Description */
.cta-desc {
  font-family: 'Jost', sans-serif;
  font-size: .95rem;
  color: rgba(60,35,30,.6);
  line-height: 1.85;
  max-width: 520px;
  margin: 0 auto 2.5rem;
  animation: ctaFadeUp .8s .3s ease both;
}
.cta-desc a {
  color: rgba(180,80,90,.85);
  text-decoration: underline;
  text-underline-offset: 3px;
  transition: color .2s;
}
.cta-desc a:hover { color: #c0485a; }

/* ── Live clock ── */
.cta-clock-wrap {
  display: inline-flex;
  align-items: center;
  gap: 1rem;
  background: rgba(255,255,255,.65);
  backdrop-filter: blur(12px);
  border: 1px solid rgba(180,140,130,.25);
  border-radius: 999px;
  padding: .6rem 1.4rem;
  margin-bottom: 2.5rem;
  box-shadow: 0 4px 24px rgba(180,120,110,.10);
  animation: ctaFadeUp .8s .35s ease both;
}
.cta-clock-dot {
  width: 8px; height: 8px; border-radius: 50%;
  background: #4ade80;
  box-shadow: 0 0 8px #4ade80;
  animation: pulseDot 1.5s ease-in-out infinite;
}
@keyframes pulseDot {
  0%,100% { box-shadow: 0 0 4px #4ade80; }
  50%      { box-shadow: 0 0 14px #4ade80, 0 0 24px rgba(74,222,128,.4); }
}
.cta-clock-label {
  font-family: 'Jost', sans-serif; font-size: .72rem;
  color: rgba(60,35,30,.5); letter-spacing: .1em; text-transform: uppercase;
}
.cta-clock-time {
  font-family: 'Cormorant Garamond', serif;
  font-size: 1.3rem; font-weight: 600;
  color: #7a4f3a;
  letter-spacing: .06em;
  min-width: 80px; text-align: center;
}
.cta-clock-open {
  font-family: 'Jost', sans-serif; font-size: .7rem; font-weight: 600;
  color: #3a9e5a; letter-spacing: .08em; text-transform: uppercase;
}

/* ── Buttons ── */
.cta-buttons {
  display: flex;
  flex-wrap: wrap;
  gap: 1rem;
  justify-content: center;
  animation: ctaFadeUp .8s .45s ease both;
}

.cta-btn-wa {
  display: inline-flex; align-items: center; gap: .6rem;
  font-family: 'Jost', sans-serif; font-size: .9rem; font-weight: 600;
  background: linear-gradient(135deg, #25d366, #128c50);
  color: #fff;
  padding: 1rem 2.2rem;
  border-radius: 999px;
  text-decoration: none;
  box-shadow: 0 10px 32px rgba(18,140,80,.25);
  transition: transform .25s, box-shadow .25s;
}
.cta-btn-wa:hover {
  transform: translateY(-3px);
  box-shadow: 0 16px 44px rgba(18,140,80,.40);
}

.cta-btn-outline {
  display: inline-flex; align-items: center; gap: .6rem;
  font-family: 'Jost', sans-serif; font-size: .9rem; font-weight: 500;
  border: 1.5px solid rgba(180,100,90,.35);
  color: rgba(80,40,35,.8);
  background: rgba(255,255,255,.50);
  backdrop-filter: blur(8px);
  padding: 1rem 2rem;
  border-radius: 999px;
  text-decoration: none;
  transition: background .2s, border-color .2s, color .2s, transform .2s;
}
.cta-btn-outline:hover {
  background: rgba(255,255,255,.80);
  border-color: rgba(192,72,90,.55);
  color: #c0485a;
  transform: translateY(-2px);
}

/* Trust badges */
.cta-trust {
  display: flex; flex-wrap: wrap; justify-content: center; gap: 1.4rem;
  margin-top: 2.5rem;
  animation: ctaFadeUp .8s .55s ease both;
}
.cta-trust-item {
  display: flex; align-items: center; gap: .4rem;
  font-family: 'Jost', sans-serif; font-size: .72rem;
  color: rgba(60,35,30,.5); letter-spacing: .04em;
}
.cta-trust-item svg { color: rgba(160,80,90,.6); }

/* ── Animations ── */
@keyframes ctaFadeUp {
  from { opacity:0; transform: translateY(24px); }
  to   { opacity:1; transform: translateY(0); }
}

/* ══════════════════════
   GARDEN SILHOUETTE (bottom) - BRIGHT VERSION
══════════════════════ */
.cta-garden {
  position: relative; z-index: 4;
  line-height: 0; margin-top: -2px;
}
.cta-garden svg { width: 100%; display: block; }

/* ── Decorative divider ── */
.cta-floral-divider {
  position: relative;
  z-index: 5;
  text-align: center;
  margin-bottom: 1.8rem;
  animation: ctaFadeUp .8s .15s ease both;
}
.cta-floral-divider svg {
  width: 180px;
  opacity: 0.45;
}
</style>

<section class="cta-section">

  <!-- Background image -->
  <div class="cta-bg-image"></div>

  <!-- Gradient overlay -->
  <div class="cta-bg-overlay"></div>

  <!-- Floral corner accents -->
  <div class="cta-floral-tl"></div>
  <div class="cta-floral-tr"></div>

  <!-- Deco lines -->
  <div class="cta-deco-line cta-deco-line-left"></div>
  <div class="cta-deco-line cta-deco-line-right"></div>

  <!-- Glow orbs -->
  <div class="cta-orb cta-orb-1"></div>
  <div class="cta-orb cta-orb-2"></div>
  <div class="cta-orb cta-orb-3"></div>

  <!-- Falling petals -->
  <div class="cta-petals" id="ctaPetals"></div>

  <!-- Content -->
  <div class="cta-content">

    <!-- Floral SVG divider -->
    <div class="cta-floral-divider">
      <svg viewBox="0 0 200 40" xmlns="http://www.w3.org/2000/svg">
        <!-- Left branch -->
        <path d="M10 20 Q40 8 80 18" stroke="#a06060" stroke-width="1" fill="none"/>
        <circle cx="30" cy="13" r="4" fill="#e8a0a0" opacity=".6"/>
        <circle cx="50" cy="10" r="3" fill="#c8d8b0" opacity=".7"/>
        <circle cx="65" cy="14" r="3.5" fill="#e8a0a0" opacity=".5"/>
        <path d="M28 13 Q24 8 20 11" stroke="#7a9a70" stroke-width="1" fill="none"/>
        <path d="M50 10 Q46 5 43 7" stroke="#7a9a70" stroke-width="1" fill="none"/>
        <!-- Center rose -->
        <circle cx="100" cy="18" r="7" fill="#e8a0a8" opacity=".7"/>
        <circle cx="100" cy="18" r="4.5" fill="#d47880" opacity=".8"/>
        <circle cx="100" cy="18" r="2" fill="#b05060" opacity=".9"/>
        <!-- Right branch (mirror) -->
        <path d="M190 20 Q160 8 120 18" stroke="#a06060" stroke-width="1" fill="none"/>
        <circle cx="170" cy="13" r="4" fill="#e8a0a0" opacity=".6"/>
        <circle cx="150" cy="10" r="3" fill="#c8d8b0" opacity=".7"/>
        <circle cx="135" cy="14" r="3.5" fill="#e8a0a0" opacity=".5"/>
        <path d="M172 13 Q176 8 180 11" stroke="#7a9a70" stroke-width="1" fill="none"/>
        <path d="M150 10 Q154 5 157 7" stroke="#7a9a70" stroke-width="1" fill="none"/>
      </svg>
    </div>

    <div class="cta-eyebrow">✦ Siap Membantu Anda ✦</div>

    <h2 class="cta-title">
      Pesan Bunga Online<br>
      <em>Kapan Saja, Di Mana Saja</em>
      <span class="cta-title-green">✦ Chika Florist — Seluruh Indonesia ✦</span>
    </h2>

    <p class="cta-desc">
      Dengan layanan <a href="<?= BASE_URL ?>/toko-bunga-online-24-jam-indonesia">toko bunga online 24 jam Indonesia</a> dari Chika Florist, setiap momen spesial Anda akan selalu hadir dengan keindahan bunga terbaik.
    </p>

    <!-- Live clock -->
    <div class="cta-clock-wrap">
      <div class="cta-clock-dot"></div>
      <span class="cta-clock-label">Waktu Sekarang</span>
      <span class="cta-clock-time" id="ctaClock">--:--:--</span>
      <span class="cta-clock-open">● Buka 24 Jam</span>
    </div>

    <!-- Buttons -->
    <div class="cta-buttons">
      <a href="<?= waLink() ?>" target="_blank" class="cta-btn-wa">
        <svg width="18" height="18" fill="currentColor" viewBox="0 0 24 24">
          <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z"/>
          <path d="M12 0C5.373 0 0 5.373 0 12c0 2.096.541 4.063 1.491 5.776L.057 23.04l5.43-1.424A11.947 11.947 0 0012 24c6.627 0 12-5.373 12-12S18.627 0 12 0zm0 21.818a9.818 9.818 0 01-5.015-1.371l-.36-.214-3.726.977.995-3.633-.235-.374A9.818 9.818 0 1112 21.818z"/>
        </svg>
        Hubungi Kami via WhatsApp
      </a>
      <a href="<?= BASE_URL ?>/toko-bunga-online-24-jam-indonesia" class="cta-btn-outline">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
          <circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/>
        </svg>
        Layanan 24 Jam
      </a>
    </div>

    <!-- Trust badges -->
    <div class="cta-trust">
      <?php foreach ([
        ['Same Day Delivery','M5 17H3a2 2 0 01-2-2V5a2 2 0 012-2h11a2 2 0 012 2v3M9 11h14v10H9z'],
        ['Bunga Fresh','M12 22V12M12 12C12 12 7 9 7 5a5 5 0 0110 0c0 4-5 7-5 7z'],
        ['Admin Responsif','M21 15a2 2 0 01-2 2H7l-4 4V5a2 2 0 012-2h14a2 2 0 012 2z'],
        ['Seluruh Indonesia','M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7z'],
      ] as $tb): ?>
      <div class="cta-trust-item">
        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
          <path d="<?= $tb[1] ?>"/>
        </svg>
        <?= $tb[0] ?>
      </div>
      <?php endforeach; ?>
    </div>

  </div>

  <!-- Garden silhouette bottom - BRIGHT VERSION -->
  <div class="cta-garden">
    <svg viewBox="0 0 1440 220" preserveAspectRatio="none" xmlns="http://www.w3.org/2000/svg">
      <!-- Ground - warm cream -->
      <rect x="0" y="160" width="1440" height="60" fill="#f0ebe4"/>

      <!-- Background bushes - soft sage/warm green -->
      <ellipse cx="100"  cy="155" rx="55" ry="40" fill="#d8e8d0"/>
      <ellipse cx="200"  cy="148" rx="45" ry="35" fill="#cde0c5"/>
      <ellipse cx="1300" cy="150" rx="55" ry="38" fill="#d8e8d0"/>
      <ellipse cx="1380" cy="155" rx="40" ry="32" fill="#cde0c5"/>

      <!-- Tall stems - warm olive -->
      <rect x="148" y="100" width="4" height="65"  fill="#8aaa78"/>
      <rect x="320" y="85"  width="3" height="80"  fill="#8aaa78"/>
      <rect x="680" y="75"  width="4" height="90"  fill="#8aaa78"/>
      <rect x="900" y="80"  width="3" height="85"  fill="#8aaa78"/>
      <rect x="1100" y="90" width="4" height="75"  fill="#8aaa78"/>
      <rect x="1260" y="88" width="3" height="77"  fill="#8aaa78"/>

      <!-- Rose flowers - bright white/cream/blush -->
      <!-- Rose 1 -->
      <ellipse cx="150" cy="88"  rx="22" ry="16" fill="#f5e8e8"/>
      <ellipse cx="150" cy="88"  rx="15" ry="11" fill="#f0d8d8"/>
      <circle  cx="150" cy="88"  r="7"            fill="#e8c0c0"/>
      <circle  cx="150" cy="88"  r="3"            fill="#d48898" opacity=".8"/>

      <!-- Rose 2 -->
      <ellipse cx="322" cy="72"  rx="25" ry="18" fill="#f8f2ec"/>
      <ellipse cx="322" cy="72"  rx="17" ry="12" fill="#f2e4dc"/>
      <circle  cx="322" cy="72"  r="8"            fill="#e8d0c0"/>
      <circle  cx="322" cy="72"  r="3.5"          fill="#d4a890" opacity=".8"/>

      <!-- Rose 3 (center large) - bright white -->
      <ellipse cx="682" cy="60"  rx="30" ry="22" fill="#fff8f5"/>
      <ellipse cx="682" cy="60"  rx="20" ry="15" fill="#f8eeea"/>
      <circle  cx="682" cy="60"  r="10"           fill="#f0dcd8"/>
      <circle  cx="682" cy="60"  r="4"            fill="#d4a0a8" opacity=".9"/>

      <!-- Rose 4 - blush pink -->
      <ellipse cx="902" cy="66"  rx="26" ry="19" fill="#fceef0"/>
      <ellipse cx="902" cy="66"  rx="17" ry="13" fill="#f8e0e4"/>
      <circle  cx="902" cy="66"  r="8.5"          fill="#ecc0c8"/>
      <circle  cx="902" cy="66"  r="3.5"          fill="#d09098" opacity=".9"/>

      <!-- Rose 5 -->
      <ellipse cx="1102" cy="76" rx="22" ry="16" fill="#f5eef0"/>
      <ellipse cx="1102" cy="76" rx="14" ry="11" fill="#eddde0"/>
      <circle  cx="1102" cy="76" r="7"            fill="#e0c0c8"/>
      <circle  cx="1102" cy="76" r="3"            fill="#c89098" opacity=".8"/>

      <!-- Rose 6 -->
      <ellipse cx="1262" cy="74" rx="24" ry="17" fill="#f8f0ec"/>
      <ellipse cx="1262" cy="74" rx="16" ry="12" fill="#f0e4dc"/>
      <circle  cx="1262" cy="74" r="8"            fill="#e4ccc0"/>
      <circle  cx="1262" cy="74" r="3"            fill="#c8a090" opacity=".8"/>

      <!-- Baby's breath clusters -->
      <?php foreach ([250, 450, 580, 780, 1000, 1180] as $bx): ?>
      <circle cx="<?= $bx ?>"    cy="<?= 100 + ($bx*3)%30 ?>" r="3" fill="#e8e8e0" opacity=".9"/>
      <circle cx="<?= $bx+8 ?>"  cy="<?= 95  + ($bx*2)%25 ?>" r="2" fill="#f0f0e8" opacity=".8"/>
      <circle cx="<?= $bx+14 ?>" cy="<?= 102 + ($bx*4)%20 ?>" r="2.5" fill="#e8e8e0" opacity=".7"/>
      <circle cx="<?= $bx+4 ?>"  cy="<?= 88  + ($bx*3)%22 ?>" r="1.8" fill="#f8f8f0" opacity=".9"/>
      <?php endforeach; ?>

      <!-- Leaves on stems - bright sage -->
      <path d="M148 120 Q130 110 125 98 Q140 105 148 120Z"  fill="#8aaa78" opacity=".9"/>
      <path d="M152 125 Q170 115 174 102 Q160 110 152 125Z" fill="#9aba88" opacity=".8"/>
      <path d="M320 115 Q302 104 298 90  Q314 100 320 115Z" fill="#8aaa78" opacity=".9"/>
      <path d="M324 118 Q342 108 346 94  Q332 104 324 118Z" fill="#9aba88" opacity=".8"/>
      <path d="M680 110 Q662 98  658 84  Q674  95 680 110Z" fill="#8aaa78" opacity=".9"/>
      <path d="M684 112 Q702 100 706 86  Q692  97 684 112Z" fill="#9aba88" opacity=".8"/>

      <!-- Grass blades - fresh green -->
      <?php
      $grassX = [50,80,130,180,240,290,360,420,500,560,620,700,770,840,920,980,1040,1120,1180,1240,1310,1370,1420];
      foreach ($grassX as $gx):
        $h = 15 + ($gx * 7) % 25;
        $w = 2 + ($gx * 3) % 3;
      ?>
      <path d="M<?= $gx ?> 165 Q<?= $gx + rand(-6,6) ?> <?= 165-$h ?> <?= $gx + rand(-4,4) ?> <?= 165-$h-5 ?>"
            stroke="#88b070" stroke-width="<?= $w ?>" fill="none" opacity=".7"/>
      <?php endforeach; ?>

      <!-- Small wildflowers - bright -->
      <?php foreach ([180,420,640,860,1060,1280] as $fx): ?>
      <circle cx="<?= $fx ?>"    cy="158" r="5" fill="#fce8e0" opacity=".9"/>
      <circle cx="<?= $fx ?>"    cy="158" r="2.5" fill="#e8a0b0" opacity=".9"/>
      <circle cx="<?= $fx+18 ?>" cy="155" r="4" fill="#fce8c8" opacity=".8"/>
      <circle cx="<?= $fx+18 ?>" cy="155" r="2" fill="#e8c070" opacity=".8"/>
      <?php endforeach; ?>

      <!-- Soft light reflection on ground -->
      <ellipse cx="720" cy="162" rx="200" ry="10" fill="rgba(255,240,220,.25)"/>
    </svg>
  </div>

</section>

<script>
/* ── Live Clock ── */
(function(){
  var el = document.getElementById('ctaClock');
  if (!el) return;
  function tick() {
    var now = new Date();
    var h = String(now.getHours()).padStart(2,'0');
    var m = String(now.getMinutes()).padStart(2,'0');
    var s = String(now.getSeconds()).padStart(2,'0');
    el.textContent = h + ':' + m + ':' + s;
  }
  tick();
  setInterval(tick, 1000);
})();

/* ── Falling Petals ── */
(function(){
  var container = document.getElementById('ctaPetals');
  if (!container) return;

  var colors = [
    'rgba(240,200,200,.55)',
    'rgba(255,220,220,.50)',
    'rgba(245,235,225,.60)',
    'rgba(220,235,215,.45)',
    'rgba(250,210,215,.55)',
  ];

  for (var i = 0; i < 18; i++) {
    var petal = document.createElement('div');
    petal.className = 'cta-petal';
    var size = 6 + Math.random() * 10;
    var color = colors[Math.floor(Math.random() * colors.length)];
    var duration = 8 + Math.random() * 10;
    var delay = Math.random() * 12;
    petal.style.cssText = [
      'width:'  + size + 'px',
      'height:' + (size * 0.6) + 'px',
      'left:'   + (Math.random() * 100) + '%',
      'top: -20px',
      'background:' + color,
      'animation-duration:' + duration + 's',
      'animation-delay:' + delay + 's',
      'transform: rotate(' + (Math.random() * 360) + 'deg)',
    ].join(';');
    container.appendChild(petal);
  }
})();
</script>