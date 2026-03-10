<!-- CTA - Dark Elegant Garden Night -->
<style>
.cta-section {
  position: relative;
  padding: 7rem 0 0;
  overflow: hidden;
  background: linear-gradient(170deg, #1a0a0e 0%, #2d0f18 40%, #3d1520 70%, #1a0a0e 100%);
}

/* ── Star particles ── */
.cta-stars {
  position: absolute;
  inset: 0;
  pointer-events: none;
  z-index: 1;
}
.cta-star-dot {
  position: absolute;
  border-radius: 50%;
  background: #fff;
  animation: twinkle linear infinite;
}
@keyframes twinkle {
  0%,100% { opacity: .08; transform: scale(1); }
  50%      { opacity: .9;  transform: scale(1.4); }
}

/* ── Glow orbs ── */
.cta-orb {
  position: absolute;
  border-radius: 50%;
  filter: blur(80px);
  pointer-events: none;
  z-index: 1;
}
.cta-orb-1 {
  width: 500px; height: 500px;
  top: -150px; left: -100px;
  background: radial-gradient(circle, rgba(192,72,90,.18), transparent);
}
.cta-orb-2 {
  width: 400px; height: 400px;
  top: -80px; right: -80px;
  background: radial-gradient(circle, rgba(201,168,76,.1), transparent);
}
.cta-orb-3 {
  width: 350px; height: 350px;
  bottom: 80px; left: 30%;
  background: radial-gradient(circle, rgba(125,155,118,.08), transparent);
}

/* ── Content ── */
.cta-content {
  position: relative;
  z-index: 3;
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
  color: rgba(201,168,76,.75);
  margin-bottom: 1.5rem;
  animation: ctaFadeUp .8s .1s ease both;
}
.cta-eyebrow::before, .cta-eyebrow::after {
  content: ''; display: block;
  width: 36px; height: 1px;
  background: linear-gradient(90deg, transparent, rgba(201,168,76,.5));
}
.cta-eyebrow::after { transform: scaleX(-1); }

/* Title */
.cta-title {
  font-family: 'Cormorant Garamond', serif;
  font-size: clamp(2.4rem, 5.5vw, 4rem);
  font-weight: 300;
  color: #fff;
  line-height: 1.12;
  margin-bottom: 1rem;
  animation: ctaFadeUp .8s .2s ease both;
}
.cta-title em {
  font-style: italic;
  background: linear-gradient(135deg, #f2d4d7, #c0485a);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  background-clip: text;
}
.cta-title .cta-title-gold {
  display: block;
  font-size: clamp(1.2rem, 2.5vw, 1.8rem);
  background: linear-gradient(135deg, #e8cc80, #c9a84c);
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
  color: rgba(255,255,255,.55);
  line-height: 1.85;
  max-width: 520px;
  margin: 0 auto 2.5rem;
  animation: ctaFadeUp .8s .3s ease both;
}
.cta-desc a {
  color: rgba(242,212,215,.8);
  text-decoration: underline;
  text-underline-offset: 3px;
  transition: color .2s;
}
.cta-desc a:hover { color: #fff; }

/* ── Live clock ── */
.cta-clock-wrap {
  display: inline-flex;
  align-items: center;
  gap: 1rem;
  background: rgba(255,255,255,.05);
  border: 1px solid rgba(201,168,76,.2);
  border-radius: 999px;
  padding: .6rem 1.4rem;
  margin-bottom: 2.5rem;
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
  color: rgba(255,255,255,.5); letter-spacing: .1em; text-transform: uppercase;
}
.cta-clock-time {
  font-family: 'Cormorant Garamond', serif;
  font-size: 1.3rem; font-weight: 600;
  color: rgba(201,168,76,.9);
  letter-spacing: .06em;
  min-width: 80px; text-align: center;
}
.cta-clock-open {
  font-family: 'Jost', sans-serif; font-size: .7rem; font-weight: 600;
  color: #4ade80; letter-spacing: .08em; text-transform: uppercase;
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
  box-shadow: 0 10px 32px rgba(18,140,80,.35);
  transition: transform .25s, box-shadow .25s;
}
.cta-btn-wa:hover {
  transform: translateY(-3px);
  box-shadow: 0 16px 44px rgba(18,140,80,.5);
}

.cta-btn-outline {
  display: inline-flex; align-items: center; gap: .6rem;
  font-family: 'Jost', sans-serif; font-size: .9rem; font-weight: 500;
  border: 1.5px solid rgba(255,255,255,.25);
  color: rgba(255,255,255,.85);
  padding: 1rem 2rem;
  border-radius: 999px;
  text-decoration: none;
  transition: background .2s, border-color .2s, color .2s, transform .2s;
}
.cta-btn-outline:hover {
  background: rgba(255,255,255,.08);
  border-color: rgba(255,255,255,.5);
  color: #fff;
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
  color: rgba(255,255,255,.35); letter-spacing: .04em;
}
.cta-trust-item svg { color: rgba(201,168,76,.6); }

/* ── Animations ── */
@keyframes ctaFadeUp {
  from { opacity:0; transform: translateY(24px); }
  to   { opacity:1; transform: translateY(0); }
}

/* ══════════════════════
   GARDEN SILHOUETTE (bottom)
══════════════════════ */
.cta-garden {
  position: relative; z-index: 2;
  line-height: 0; margin-top: -2px;
}
.cta-garden svg { width: 100%; display: block; }
</style>

<section class="cta-section">

  <!-- Stars -->
  <div class="cta-stars" id="ctaStars"></div>

  <!-- Glow orbs -->
  <div class="cta-orb cta-orb-1"></div>
  <div class="cta-orb cta-orb-2"></div>
  <div class="cta-orb cta-orb-3"></div>

  <!-- Content -->
  <div class="cta-content">

    <div class="cta-eyebrow">✦ Siap Membantu Anda ✦</div>

    <h2 class="cta-title">
      Pesan Bunga Online<br>
      <em>Kapan Saja, Di Mana Saja</em>
      <span class="cta-title-gold">✦ Chika Florist — Seluruh Indonesia ✦</span>
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

  <!-- Garden silhouette bottom -->
  <div class="cta-garden">
    <svg viewBox="0 0 1440 220" preserveAspectRatio="none" xmlns="http://www.w3.org/2000/svg">
      <!-- Ground -->
      <rect x="0" y="160" width="1440" height="60" fill="#0f0608"/>

      <!-- Background trees/bushes -->
      <ellipse cx="100"  cy="155" rx="55" ry="40" fill="#0f0608"/>
      <ellipse cx="200"  cy="148" rx="45" ry="35" fill="#120709"/>
      <ellipse cx="1300" cy="150" rx="55" ry="38" fill="#0f0608"/>
      <ellipse cx="1380" cy="155" rx="40" ry="32" fill="#120709"/>

      <!-- Tall stems -->
      <rect x="148" y="100" width="4" height="65"  fill="#1a0a0e"/>
      <rect x="320" y="85"  width="3" height="80"  fill="#1a0a0e"/>
      <rect x="680" y="75"  width="4" height="90"  fill="#1a0a0e"/>
      <rect x="900" y="80"  width="3" height="85"  fill="#1a0a0e"/>
      <rect x="1100" y="90" width="4" height="75"  fill="#1a0a0e"/>
      <rect x="1260" y="88" width="3" height="77"  fill="#1a0a0e"/>

      <!-- Rose flowers on stems -->
      <!-- Rose 1 -->
      <ellipse cx="150" cy="88"  rx="22" ry="16" fill="#3d0f18"/>
      <ellipse cx="150" cy="88"  rx="15" ry="11" fill="#5a1525"/>
      <circle  cx="150" cy="88"  r="7"            fill="#7a1f2e"/>
      <circle  cx="150" cy="88"  r="3"            fill="#c0485a" opacity=".6"/>

      <!-- Rose 2 -->
      <ellipse cx="322" cy="72"  rx="25" ry="18" fill="#3d0f18"/>
      <ellipse cx="322" cy="72"  rx="17" ry="12" fill="#5a1525"/>
      <circle  cx="322" cy="72"  r="8"            fill="#7a1f2e"/>
      <circle  cx="322" cy="72"  r="3.5"          fill="#c0485a" opacity=".5"/>

      <!-- Rose 3 (center large) -->
      <ellipse cx="682" cy="60"  rx="30" ry="22" fill="#3d0f18"/>
      <ellipse cx="682" cy="60"  rx="20" ry="15" fill="#5a1525"/>
      <circle  cx="682" cy="60"  r="10"           fill="#7a1f2e"/>
      <circle  cx="682" cy="60"  r="4"            fill="#c0485a" opacity=".7"/>

      <!-- Rose 4 -->
      <ellipse cx="902" cy="66"  rx="26" ry="19" fill="#3d0f18"/>
      <ellipse cx="902" cy="66"  rx="17" ry="13" fill="#5a1525"/>
      <circle  cx="902" cy="66"  r="8.5"          fill="#7a1f2e"/>
      <circle  cx="902" cy="66"  r="3.5"          fill="#c0485a" opacity=".55"/>

      <!-- Rose 5 -->
      <ellipse cx="1102" cy="76" rx="22" ry="16" fill="#3d0f18"/>
      <ellipse cx="1102" cy="76" rx="14" ry="11" fill="#5a1525"/>
      <circle  cx="1102" cy="76" r="7"            fill="#7a1f2e"/>
      <circle  cx="1102" cy="76" r="3"            fill="#c0485a" opacity=".5"/>

      <!-- Rose 6 -->
      <ellipse cx="1262" cy="74" rx="24" ry="17" fill="#3d0f18"/>
      <ellipse cx="1262" cy="74" rx="16" ry="12" fill="#5a1525"/>
      <circle  cx="1262" cy="74" r="8"            fill="#7a1f2e"/>
      <circle  cx="1262" cy="74" r="3"            fill="#c0485a" opacity=".5"/>

      <!-- Leaves on stems -->
      <path d="M148 120 Q130 110 125 98 Q140 105 148 120Z"  fill="#1a2e18" opacity=".8"/>
      <path d="M152 125 Q170 115 174 102 Q160 110 152 125Z" fill="#1a2e18" opacity=".7"/>
      <path d="M320 115 Q302 104 298 90  Q314 100 320 115Z" fill="#1a2e18" opacity=".8"/>
      <path d="M324 118 Q342 108 346 94  Q332 104 324 118Z" fill="#1a2e18" opacity=".7"/>
      <path d="M680 110 Q662 98  658 84  Q674  95 680 110Z" fill="#1a2e18" opacity=".8"/>
      <path d="M684 112 Q702 100 706 86  Q692  97 684 112Z" fill="#1a2e18" opacity=".7"/>

      <!-- Grass blades -->
      <?php
      $grassX = [50,80,130,180,240,290,360,420,500,560,620,700,770,840,920,980,1040,1120,1180,1240,1310,1370,1420];
      foreach ($grassX as $gx):
        $h = 15 + ($gx * 7) % 25;
        $w = 2 + ($gx * 3) % 3;
      ?>
      <path d="M<?= $gx ?> 165 Q<?= $gx + rand(-6,6) ?> <?= 165-$h ?> <?= $gx + rand(-4,4) ?> <?= 165-$h-5 ?>"
            stroke="#1a2e18" stroke-width="<?= $w ?>" fill="none" opacity=".7"/>
      <?php endforeach; ?>

      <!-- Small wildflowers in grass -->
      <?php foreach ([180,420,640,860,1060,1280] as $fx): ?>
      <circle cx="<?= $fx ?>"    cy="158" r="5" fill="#2d0f18" opacity=".8"/>
      <circle cx="<?= $fx ?>"    cy="158" r="2.5" fill="#c0485a" opacity=".35"/>
      <circle cx="<?= $fx+18 ?>" cy="155" r="4" fill="#2d0f18" opacity=".7"/>
      <circle cx="<?= $fx+18 ?>" cy="155" r="2" fill="#c9a84c" opacity=".3"/>
      <?php endforeach; ?>

      <!-- Moon reflection glow on ground -->
      <ellipse cx="720" cy="162" rx="180" ry="8" fill="rgba(201,168,76,.04)"/>
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

/* ── Star particles ── */
(function(){
  var container = document.getElementById('ctaStars');
  if (!container) return;
  var section = container.parentElement;
  var W = section.offsetWidth;
  var H = section.offsetHeight || 500;

  for (var i = 0; i < 80; i++) {
    var star = document.createElement('div');
    star.className = 'cta-star-dot';
    var size = .8 + Math.random() * 2.2;
    star.style.cssText = [
      'width:'  + size + 'px',
      'height:' + size + 'px',
      'left:'   + (Math.random() * 100) + '%',
      'top:'    + (Math.random() * 75)  + '%',
      'animation-duration:' + (2 + Math.random() * 4) + 's',
      'animation-delay:'    + (Math.random() * 5)     + 's',
    ].join(';');
    container.appendChild(star);
  }
})();
</script>