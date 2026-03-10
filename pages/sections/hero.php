<!-- HERO - Kebun Bunga Elegan -->
<style>
@import url('https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,300;0,400;0,600;1,300;1,400&family=Jost:wght@300;400;500&display=swap');

:root {
  --blush:   #f2d4d7;
  --rose:    #c0485a;
  --deep:    #7a1f2e;
  --gold:    #c9a84c;
  --sage:    #7d9b76;
  --cream:   #fdf6ee;
  --petal:   #f8e8ec;
}

.hero-wrap {
  position: relative;
  min-height: 100vh;
  overflow: hidden;
  background: linear-gradient(135deg, #fdf0f3 0%, #fdf6ee 40%, #f0f4ed 100%);
  display: flex;
  align-items: center;
  font-family: 'Jost', sans-serif;
}

/* ── Decorative pattern background ── */
.hero-pattern {
  position: absolute;
  inset: 0;
  background-image:
    radial-gradient(circle at 15% 50%, rgba(192,72,90,0.07) 0%, transparent 55%),
    radial-gradient(circle at 85% 20%, rgba(125,155,118,0.08) 0%, transparent 50%),
    radial-gradient(circle at 60% 85%, rgba(201,168,76,0.06) 0%, transparent 40%);
  pointer-events: none;
}

/* ── Scattered petal dots pattern ── */
.hero-dots {
  position: absolute;
  inset: 0;
  background-image: radial-gradient(circle, rgba(192,72,90,0.12) 1px, transparent 1px);
  background-size: 36px 36px;
  mask-image: radial-gradient(ellipse 80% 80% at 50% 50%, black 30%, transparent 100%);
  pointer-events: none;
}

/* ── Floating petals ── */
.petal {
  position: absolute;
  opacity: 0;
  animation: floatPetal linear infinite;
  pointer-events: none;
}
@keyframes floatPetal {
  0%   { transform: translateY(110vh) rotate(0deg);   opacity: 0; }
  5%   { opacity: 0.6; }
  95%  { opacity: 0.4; }
  100% { transform: translateY(-10vh) rotate(720deg); opacity: 0; }
}

/* ── Diagonal divider ── */
.hero-divider {
  position: absolute;
  right: 0; top: 0; bottom: 0;
  width: 48%;
  background: linear-gradient(160deg, rgba(242,212,215,0.35) 0%, rgba(240,244,237,0.45) 100%);
  clip-path: polygon(12% 0, 100% 0, 100% 100%, 0% 100%);
  backdrop-filter: blur(2px);
  pointer-events: none;
}

/* ── Content ── */
.hero-content {
  position: relative;
  z-index: 10;
  max-width: 1280px;
  margin: 0 auto;
  padding: 6rem 2rem 4rem;
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 4rem;
  align-items: center;
  width: 100%;
}

/* ── Left text ── */
.hero-text { animation: fadeUp 1s ease both; }
@keyframes fadeUp {
  from { opacity:0; transform:translateY(32px); }
  to   { opacity:1; transform:translateY(0); }
}

.hero-badge {
  display: inline-flex;
  align-items: center;
  gap: .5rem;
  background: rgba(192,72,90,0.08);
  border: 1px solid rgba(192,72,90,0.2);
  color: var(--rose);
  font-size: .7rem;
  font-weight: 500;
  letter-spacing: .18em;
  text-transform: uppercase;
  padding: .4rem 1rem;
  border-radius: 999px;
  margin-bottom: 1.6rem;
  animation: fadeUp 1s .1s ease both;
}

.hero-h1 {
  font-family: 'Cormorant Garamond', serif;
  font-size: clamp(2.8rem, 5vw, 4.4rem);
  font-weight: 300;
  line-height: 1.1;
  color: #2a1018;
  margin-bottom: 1rem;
  animation: fadeUp 1s .2s ease both;
}
.hero-h1 em {
  font-style: italic;
  color: var(--rose);
}
.hero-h1 .line-gold {
  display: block;
  font-size: clamp(1.4rem, 2.5vw, 2rem);
  font-weight: 400;
  color: var(--gold);
  letter-spacing: .04em;
  margin-top: .3rem;
}

.hero-desc {
  font-size: 1rem;
  color: #6b4a52;
  line-height: 1.8;
  max-width: 440px;
  margin-bottom: 2.4rem;
  animation: fadeUp 1s .3s ease both;
}

.hero-ctas {
  display: flex;
  flex-wrap: wrap;
  gap: .9rem;
  margin-bottom: 2.8rem;
  animation: fadeUp 1s .4s ease both;
}

.btn-wa {
  display: inline-flex;
  align-items: center;
  gap: .6rem;
  background: linear-gradient(135deg, #25d366, #128c50);
  color: #fff;
  font-weight: 500;
  font-size: .9rem;
  padding: .85rem 1.8rem;
  border-radius: 999px;
  text-decoration: none;
  box-shadow: 0 8px 24px rgba(18,140,80,.25);
  transition: transform .2s, box-shadow .2s;
}
.btn-wa:hover { transform: translateY(-2px); box-shadow: 0 12px 32px rgba(18,140,80,.35); }

.btn-outline {
  display: inline-flex;
  align-items: center;
  gap: .5rem;
  border: 1.5px solid var(--rose);
  color: var(--rose);
  font-weight: 500;
  font-size: .9rem;
  padding: .85rem 1.8rem;
  border-radius: 999px;
  text-decoration: none;
  background: transparent;
  transition: background .2s, color .2s;
}
.btn-outline:hover { background: var(--rose); color: #fff; }

/* ── Trust badges ── */
.hero-trust {
  display: flex;
  flex-wrap: wrap;
  gap: 1.2rem;
  animation: fadeUp 1s .5s ease both;
}
.trust-item {
  display: flex;
  align-items: center;
  gap: .45rem;
  font-size: .78rem;
  color: #8a6068;
}
.trust-dot {
  width: 6px; height: 6px;
  border-radius: 50%;
  background: var(--sage);
  flex-shrink: 0;
}

/* ── Right image panel ── */
.hero-visual {
  position: relative;
  animation: fadeUp 1s .25s ease both;
}

.img-frame {
  position: relative;
  border-radius: 40% 60% 55% 45% / 45% 40% 60% 55%;
  overflow: hidden;
  aspect-ratio: 4/5;
  box-shadow:
    0 30px 80px rgba(122,31,46,.18),
    0 0 0 1px rgba(192,72,90,.1);
  animation: morphFrame 10s ease-in-out infinite;
}
@keyframes morphFrame {
  0%,100% { border-radius: 40% 60% 55% 45% / 45% 40% 60% 55%; }
  33%      { border-radius: 55% 45% 40% 60% / 60% 55% 45% 40%; }
  66%      { border-radius: 45% 55% 60% 40% / 40% 60% 55% 45%; }
}

.img-frame img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  transition: transform .6s ease;
}
.img-frame:hover img { transform: scale(1.04); }

/* Gold ring accent */
.ring-accent {
  position: absolute;
  inset: -16px;
  border-radius: inherit;
  border: 1.5px dashed rgba(201,168,76,.4);
  animation: morphFrame 10s ease-in-out infinite, spinSlow 30s linear infinite;
  pointer-events: none;
}
@keyframes spinSlow { to { transform: rotate(360deg); } }

/* Floating badge card */
.float-card {
  position: absolute;
  background: rgba(255,255,255,.92);
  backdrop-filter: blur(12px);
  border: 1px solid rgba(192,72,90,.12);
  border-radius: 16px;
  padding: .75rem 1.1rem;
  box-shadow: 0 8px 32px rgba(122,31,46,.12);
  font-family: 'Jost', sans-serif;
  white-space: nowrap;
}
.float-card-1 {
  bottom: 12%;
  left: -14%;
  animation: floatCard 4s ease-in-out infinite;
}
.float-card-2 {
  top: 8%;
  right: -10%;
  animation: floatCard 4s 1.5s ease-in-out infinite;
}
@keyframes floatCard {
  0%,100% { transform: translateY(0); }
  50%      { transform: translateY(-8px); }
}
.float-card .fc-label {
  font-size: .65rem;
  color: #a07878;
  letter-spacing: .1em;
  text-transform: uppercase;
  display: block;
  margin-bottom: .15rem;
}
.float-card .fc-value {
  font-size: .95rem;
  font-weight: 500;
  color: #2a1018;
  display: flex;
  align-items: center;
  gap: .4rem;
}

/* SVG floral corners */
.floral-tl, .floral-br {
  position: absolute;
  opacity: .18;
  pointer-events: none;
}
.floral-tl { top: -20px; left: -20px; width: 200px; transform: rotate(-15deg); }
.floral-br { bottom: -20px; right: -20px; width: 180px; transform: rotate(165deg); }

/* ── Divider ornament bottom ── */
.hero-bottom-ornament {
  position: absolute;
  bottom: 0; left: 0; right: 0;
  height: 80px;
  overflow: hidden;
  pointer-events: none;
}
.hero-bottom-ornament svg { width: 100%; height: 100%; }

/* ── Responsive ── */
@media (max-width: 900px) {
  .hero-content {
    grid-template-columns: 1fr;
    padding: 5rem 1.5rem 3rem;
    text-align: center;
  }
  .hero-desc { margin-left: auto; margin-right: auto; }
  .hero-ctas, .hero-trust { justify-content: center; }
  .hero-divider { display: none; }
  .img-frame { max-width: 340px; margin: 0 auto; }
  .float-card-1 { left: -4%; }
  .float-card-2 { right: -4%; }
  .floral-tl, .floral-br { display: none; }
}
</style>

<!-- Floating petals (JS-injected) -->
<script>
(function(){
  const colors = ['#f2d4d7','#c0485a','#e8c5cb','#7d9b76','#c9a84c'];
  const container = document.currentScript.parentElement;
  for(let i=0;i<14;i++){
    const p = document.createElementNS('http://www.w3.org/2000/svg','svg');
    const size = 8 + Math.random()*14;
    p.setAttribute('viewBox','0 0 20 20');
    p.setAttribute('width', size);
    p.setAttribute('height', size);
    p.style.cssText = `position:fixed;left:${Math.random()*100}%;pointer-events:none;z-index:1`;
    p.innerHTML = `<ellipse cx="10" cy="10" rx="5" ry="9" fill="${colors[i%colors.length]}" opacity=".8" transform="rotate(${Math.random()*360} 10 10)"/>`;
    p.classList.add('petal');
    p.style.animationDuration = (12 + Math.random()*18) + 's';
    p.style.animationDelay    = (Math.random()*20) + 's';
    document.body.appendChild(p);
  }
})();
</script>

<section class="hero-wrap">
  <div class="hero-pattern"></div>
  <div class="hero-dots"></div>
  <div class="hero-divider"></div>

  <div class="hero-content">

    <!-- LEFT: TEXT -->
    <div class="hero-text">
      <span class="hero-badge">
        <svg width="12" height="12" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2C6 2 2 6 2 12s4 10 10 10 10-4 10-10S18 2 12 2zm0 18c-4.4 0-8-3.6-8-8s3.6-8 8-8 8 3.6 8 8-3.6 8-8 8zm.5-13H11v6l5.2 3.2.8-1.3-4.5-2.7V7z"/></svg>
        Florist Online Terpercaya &bull; 24 Jam
      </span>

      <h1 class="hero-h1">
        Toko Bunga Online<br>
        <em>24 Jam Indonesia</em>
        <span class="line-gold">✦ Kirim Bunga Cepat Seluruh Kota ✦</span>
      </h1>

      <p class="hero-desc">
        Chika Florist menghadirkan keindahan kebun bunga langsung ke pintu Anda. Pesan kapan saja, bunga segar berkualitas dikirim dengan penuh kasih ke seluruh Indonesia.
      </p>

      <div class="hero-ctas">
        <a href="<?= waLink('Halo Chika Florist, saya ingin memesan bunga') ?>" target="_blank" class="btn-wa">
          <svg width="18" height="18" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z"/><path d="M12 0C5.373 0 0 5.373 0 12c0 2.096.541 4.063 1.491 5.776L.057 23.04l5.43-1.424A11.947 11.947 0 0012 24c6.627 0 12-5.373 12-12S18.627 0 12 0zm0 21.818a9.818 9.818 0 01-5.015-1.371l-.36-.214-3.726.977.995-3.633-.235-.374A9.818 9.818 0 1112 21.818z"/></svg>
          Pesan via WhatsApp
        </a>
        <a href="<?= BASE_URL ?>/toko-bunga-online-24-jam-indonesia" class="btn-outline">
          Layanan 24 Jam →
        </a>
      </div>

      <div class="hero-trust">
        <span class="trust-item"><span class="trust-dot"></span> Same Day Delivery</span>
        <span class="trust-item"><span class="trust-dot"></span> Bunga Fresh</span>
        <span class="trust-item"><span class="trust-dot"></span> 24 Jam Nonstop</span>
        <span class="trust-item"><span class="trust-dot"></span> Seluruh Indonesia</span>
      </div>
    </div>

    <!-- RIGHT: VISUAL -->
    <div class="hero-visual">

      <!-- SVG floral decoration top-left -->
      <svg class="floral-tl" viewBox="0 0 200 200" fill="none" xmlns="http://www.w3.org/2000/svg">
        <g fill="#c0485a">
          <ellipse cx="60" cy="40" rx="18" ry="30" transform="rotate(-30 60 40)"/>
          <ellipse cx="80" cy="30" rx="18" ry="30" transform="rotate(10 80 30)"/>
          <ellipse cx="40" cy="55" rx="18" ry="30" transform="rotate(-70 40 55)"/>
          <ellipse cx="70" cy="60" rx="18" ry="30" transform="rotate(50 70 60)"/>
          <circle cx="65" cy="48" r="12" fill="#f2d4d7"/>
          <circle cx="65" cy="48" r="6" fill="#c9a84c"/>
        </g>
        <g fill="#7d9b76" opacity=".7">
          <path d="M30 90 Q50 60 80 80 Q60 100 30 90Z"/>
          <path d="M10 110 Q40 85 65 100 Q45 120 10 110Z"/>
          <path d="M50 105 Q70 80 95 95 Q75 115 50 105Z"/>
        </g>
      </svg>

      <!-- SVG floral decoration bottom-right -->
      <svg class="floral-br" viewBox="0 0 200 200" fill="none" xmlns="http://www.w3.org/2000/svg">
        <g fill="#c9a84c">
          <ellipse cx="120" cy="140" rx="16" ry="26" transform="rotate(-30 120 140)"/>
          <ellipse cx="140" cy="130" rx="16" ry="26" transform="rotate(10 140 130)"/>
          <ellipse cx="100" cy="155" rx="16" ry="26" transform="rotate(-70 100 155)"/>
          <ellipse cx="130" cy="160" rx="16" ry="26" transform="rotate(50 130 160)"/>
          <circle cx="125" cy="148" r="10" fill="#fdf6ee"/>
          <circle cx="125" cy="148" r="5" fill="#c0485a"/>
        </g>
        <g fill="#7d9b76" opacity=".7">
          <path d="M90 170 Q110 145 140 160 Q120 180 90 170Z"/>
          <path d="M110 185 Q135 165 160 175 Q140 195 110 185Z"/>
        </g>
      </svg>

      <div class="img-frame">
        <div class="ring-accent"></div>
        <img src="<?= BASE_URL ?>/assets/images/1a.jpg"
             alt="Toko Bunga Online 24 Jam Indonesia – Chika Florist"
             onerror="this.parentElement.style.background='linear-gradient(135deg,#f2d4d7,#f0f4ed)';this.style.display='none'">
      </div>

      <!-- Floating card 1 -->
      <div class="float-card float-card-1">
        <span class="fc-label">Pengiriman</span>
        <span class="fc-value">
          <svg width="14" height="14" fill="#25d366" viewBox="0 0 24 24"><path d="M9 16.2L4.8 12l-1.4 1.4L9 19 21 7l-1.4-1.4L9 16.2z"/></svg>
          Same Day Delivery
        </span>
      </div>

      <!-- Floating card 2 -->
      <div class="float-card float-card-2">
        <span class="fc-label">Layanan</span>
        <span class="fc-value">
          <svg width="14" height="14" fill="#c9a84c" viewBox="0 0 24 24"><path d="M12 2C6 2 2 6 2 12s4 10 10 10 10-4 10-10S18 2 12 2zm.5 5H11v6l5.2 3.2.8-1.3-4.5-2.7V7z"/></svg>
          24 Jam Nonstop
        </span>
      </div>

    </div>
  </div>

  <!-- Bottom wave ornament -->
  <div class="hero-bottom-ornament">
    <svg viewBox="0 0 1440 80" preserveAspectRatio="none" xmlns="http://www.w3.org/2000/svg">
      <path d="M0,40 C240,80 480,0 720,40 C960,80 1200,0 1440,40 L1440,80 L0,80 Z" fill="white" opacity=".6"/>
      <path d="M0,55 C360,10 720,70 1080,30 C1260,10 1380,50 1440,55 L1440,80 L0,80 Z" fill="white" opacity=".8"/>
    </svg>
  </div>
</section>