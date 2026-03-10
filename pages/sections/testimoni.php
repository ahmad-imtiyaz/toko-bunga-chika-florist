<!-- TESTIMONI - Carousel Center Focus (Fixed) -->
<style>
.testi-section {
  position: relative;
  padding: 6rem 0;
  overflow: hidden;
  background: linear-gradient(160deg, #fdf6ee 0%, #fff 50%, #f0f4ed 100%);
}
.testi-bg {
  position: absolute; inset: 0; pointer-events: none;
  background-image:
    radial-gradient(circle at 20% 50%, rgba(192,72,90,.05) 0%, transparent 50%),
    radial-gradient(circle at 80% 50%, rgba(125,155,118,.05) 0%, transparent 50%);
}
.testi-watermark {
  position: absolute; top: 2rem; left: 50%;
  transform: translateX(-50%);
  font-family: 'Cormorant Garamond', serif;
  font-size: 18rem; line-height: 1;
  color: rgba(192,72,90,.035);
  pointer-events: none; user-select: none; z-index: 0;
}

/* ── Header ── */
.testi-header {
  position: relative; z-index: 2;
  text-align: center; margin-bottom: 3.5rem; padding: 0 1.5rem;
}
.testi-eyebrow {
  display: inline-flex; align-items: center; gap: .6rem;
  font-family: 'Jost', sans-serif; font-size: .7rem; font-weight: 500;
  letter-spacing: .18em; text-transform: uppercase;
  color: var(--gold, #c9a84c); margin-bottom: 1rem;
}
.testi-eyebrow::before, .testi-eyebrow::after {
  content: ''; display: block; width: 32px; height: 1px;
  background: var(--gold, #c9a84c); opacity: .5;
}
.testi-title {
  font-family: 'Cormorant Garamond', serif;
  font-size: clamp(2rem, 4vw, 3rem); font-weight: 300;
  color: #2a1018; line-height: 1.15; margin-bottom: .5rem;
}
.testi-title em { font-style: italic; color: var(--rose, #c0485a); }
.testi-subtitle { font-family: 'Jost', sans-serif; font-size: .9rem; color: #9a7070; }

/* ── Outer: clips but shows spoilers via padding trick ── */
.testi-outer {
  position: relative; z-index: 2;
  overflow: hidden;
  padding: 2rem 0 1.5rem;
}

/* Fade edge masks */
.testi-outer::before,
.testi-outer::after {
  content: ''; position: absolute; top: 0; bottom: 0;
  width: 100px; z-index: 4; pointer-events: none;
}
.testi-outer::before { left:  0; background: linear-gradient(to right, #fdf8f2 0%, transparent 100%); }
.testi-outer::after  { right: 0; background: linear-gradient(to left,  #fdf8f2 0%, transparent 100%); }

/* Track */
.testi-track {
  display: flex;
  gap: 1.5rem;
  transition: transform .6s cubic-bezier(.22,1,.36,1);
  will-change: transform;
}

/* ── Card ── */
.testi-card {
  flex: 0 0 500px;
  background: rgba(255,255,255,.88);
  backdrop-filter: blur(12px);
  border: 1px solid rgba(192,72,90,.08);
  border-radius: 28px;
  padding: 2.2rem 2.2rem 1.8rem;
  box-shadow: 0 4px 20px rgba(122,31,46,.06);
  transition:
    transform .6s cubic-bezier(.22,1,.36,1),
    box-shadow .6s ease,
    opacity .6s ease,
    border-color .3s;
  opacity: .35;
  transform: scale(.84);
  position: relative;
  overflow: hidden;
  cursor: pointer;
}
.testi-card.active {
  opacity: 1;
  transform: scale(1);
  box-shadow: 0 28px 70px rgba(122,31,46,.16);
  border-color: rgba(192,72,90,.2);
  cursor: default;
}

.testi-card-blob {
  position: absolute; bottom: -40px; left: -40px;
  width: 160px; height: 160px; border-radius: 50%;
  background: radial-gradient(circle, rgba(192,72,90,.07), transparent);
  pointer-events: none;
}
.testi-quote-icon {
  position: absolute; top: 1.4rem; right: 1.6rem; opacity: .07;
}

/* Stars */
.testi-stars { display: flex; gap: .2rem; margin-bottom: 1rem; }
.testi-star       { color: #c9a84c; font-size: 1rem; line-height: 1; }
.testi-star.empty { color: rgba(201,168,76,.2); }

/* Quote */
.testi-quote {
  font-family: 'Cormorant Garamond', serif;
  font-size: 1.15rem; font-style: italic; font-weight: 400;
  color: #3a2028; line-height: 1.75; margin-bottom: 1.6rem;
  position: relative; z-index: 1;
}
.testi-quote::before {
  content: '\201C'; font-size: 2.8rem; color: rgba(192,72,90,.15);
  line-height: 0; vertical-align: -.45rem; margin-right: .12rem;
  font-family: 'Cormorant Garamond', serif;
}

/* Divider */
.testi-divider {
  height: 1px;
  background: linear-gradient(90deg, transparent, rgba(192,72,90,.14), transparent);
  margin-bottom: 1.3rem;
}

/* Avatar row */
.testi-avatar-row { display: flex; align-items: center; gap: 1rem; }
.testi-avatar {
  width: 48px; height: 48px; border-radius: 50%;
  display: flex; align-items: center; justify-content: center;
  font-family: 'Cormorant Garamond', serif; font-size: 1.2rem; font-weight: 600;
  color: #fff; flex-shrink: 0;
  box-shadow: 0 4px 14px rgba(122,31,46,.2);
  border: 2.5px solid rgba(255,255,255,.85);
}
.testi-card:nth-child(1) .testi-avatar { background: linear-gradient(135deg, #c0485a, #e8899a); }
.testi-card:nth-child(2) .testi-avatar { background: linear-gradient(135deg, #7d9b76, #a8c4a0); }
.testi-card:nth-child(3) .testi-avatar { background: linear-gradient(135deg, #c9a84c, #e8cc80); }
.testi-card:nth-child(4) .testi-avatar { background: linear-gradient(135deg, #8b5cf6, #c4b5fd); }
.testi-card:nth-child(5) .testi-avatar { background: linear-gradient(135deg, #0ea5e9, #7dd3fc); }
.testi-card:nth-child(6) .testi-avatar { background: linear-gradient(135deg, #f97316, #fbbf24); }

.testi-name {
  font-family: 'Cormorant Garamond', serif; font-size: 1rem;
  font-weight: 600; color: #2a1018;
}
.testi-city {
  font-family: 'Jost', sans-serif; font-size: .72rem; color: #9a7070; margin-top: .1rem;
}
.testi-verified {
  margin-left: auto; display: flex; align-items: center; gap: .3rem;
  font-family: 'Jost', sans-serif; font-size: .65rem;
  color: var(--sage, #7d9b76); font-weight: 500; white-space: nowrap;
}

/* ── Controls ── */
.testi-controls {
  position: relative; z-index: 2;
  display: flex; align-items: center; justify-content: center;
  gap: 1.5rem; margin-top: 2rem; flex-wrap: wrap;
}
.testi-arrow {
  width: 44px; height: 44px; border-radius: 50%;
  border: 1.5px solid rgba(192,72,90,.2);
  background: rgba(255,255,255,.85); backdrop-filter: blur(8px);
  color: var(--rose, #c0485a);
  display: flex; align-items: center; justify-content: center;
  cursor: pointer;
  transition: background .2s, border-color .2s, color .2s, transform .2s;
}
.testi-arrow:hover {
  background: var(--rose, #c0485a); border-color: var(--rose, #c0485a);
  color: #fff; transform: scale(1.08);
}
.testi-arrow:disabled { opacity: .3; cursor: default; transform: none !important; }
.testi-dots { display: flex; gap: .5rem; align-items: center; }
.testi-dot {
  width: 7px; height: 7px; border-radius: 50%;
  background: rgba(192,72,90,.2); border: none; padding: 0;
  cursor: pointer; transition: background .2s, width .25s;
}
.testi-dot.active {
  background: var(--rose, #c0485a); width: 22px; border-radius: 4px;
}

/* ── Mobile ── */
@media (max-width: 640px) {
  .testi-card { flex: 0 0 76vw; }
  .testi-quote { font-size: 1rem; }
  .testi-outer::before, .testi-outer::after { width: 40px; }
}
</style>

<?php if (!empty($testimonials)): ?>
<section class="testi-section">
  <div class="testi-bg"></div>
  <div class="testi-watermark">"</div>

  <div class="testi-header">
    <div class="testi-eyebrow">✦ Cerita Mereka ✦</div>
    <h2 class="testi-title">Apa Kata <em>Pelanggan Kami</em></h2>
    <p class="testi-subtitle">Ribuan momen spesial — dipercayakan kepada Chika Florist</p>
  </div>

  <div class="testi-outer">
    <div class="testi-track" id="testiTrack">
      <?php foreach ($testimonials as $t): ?>
      <div class="testi-card">
        <div class="testi-card-blob"></div>
        <svg class="testi-quote-icon" width="60" height="45" viewBox="0 0 60 45" fill="none">
          <path d="M0 45V27C0 12.1 9.4 3.8 28.2 0L30 4.5C21.5 6.8 17 11.7 16.3 19H25V45H0ZM35 45V27C35 12.1 44.4 3.8 63.2 0L65 4.5C56.5 6.8 52 11.7 51.3 19H60V45H35Z" fill="#c0485a"/>
        </svg>
        <div class="testi-stars">
          <?php for ($i=1; $i<=5; $i++): ?>
          <span class="testi-star <?= $i<=$t['rating']?'':'empty' ?>">★</span>
          <?php endfor; ?>
        </div>
        <p class="testi-quote"><?= clean($t['content']) ?></p>
        <div class="testi-divider"></div>
        <div class="testi-avatar-row">
          <div class="testi-avatar"><?= mb_strtoupper(mb_substr($t['customer_name'],0,1)) ?></div>
          <div>
            <div class="testi-name"><?= clean($t['customer_name']) ?></div>
            <?php if (!empty($t['customer_city'])): ?>
            <div class="testi-city">📍 <?= clean($t['customer_city']) ?></div>
            <?php endif; ?>
          </div>
          <div class="testi-verified">
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round">
              <path d="M22 11.08V12a10 10 0 11-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/>
            </svg>
            Terverifikasi
          </div>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>

  <div class="testi-controls">
    <button class="testi-arrow" id="testiPrev">
      <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M15 18l-6-6 6-6"/></svg>
    </button>
    <div class="testi-dots">
      <?php foreach ($testimonials as $i => $_): ?>
      <button class="testi-dot <?= $i===0?'active':'' ?>" data-idx="<?= $i ?>"></button>
      <?php endforeach; ?>
    </div>
    <button class="testi-arrow" id="testiNext">
      <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M9 18l6-6-6-6"/></svg>
    </button>
  </div>
</section>

<script>
(function(){
  var outer   = document.querySelector('.testi-outer');
  var track   = document.getElementById('testiTrack');
  var dots    = document.querySelectorAll('.testi-dot');
  var btnPrev = document.getElementById('testiPrev');
  var btnNext = document.getElementById('testiNext');
  if (!track || !outer) return;

  var cards  = Array.from(track.querySelectorAll('.testi-card'));
  var total  = cards.length;
  var curr   = 0;
  var timer;

  function setup() {
    /* Inject padding so first & last card can reach center */
    var outerW = outer.offsetWidth;
    var cardW  = cards[0].offsetWidth;
    var pad    = Math.max(0, (outerW - cardW) / 2);
    track.style.paddingLeft  = pad + 'px';
    track.style.paddingRight = pad + 'px';
    goTo(curr, false); /* reposition without animation */
  }

  function goTo(idx, animate) {
    if (idx < 0)      idx = total - 1;
    if (idx >= total) idx = 0;
    curr = idx;

    var gap    = parseFloat(getComputedStyle(track).gap) || 24;
    var cardW  = cards[0].offsetWidth;
    var outerW = outer.offsetWidth;
    var pad    = Math.max(0, (outerW - cardW) / 2);

    /* Distance from track start (including padding) to center of active card */
    var offset = pad + curr * (cardW + gap) - (outerW - cardW) / 2;

    if (animate === false) {
      track.style.transition = 'none';
      track.style.transform  = 'translateX(' + (-offset) + 'px)';
      track.offsetHeight; /* force reflow */
      track.style.transition = '';
    } else {
      track.style.transform = 'translateX(' + (-offset) + 'px)';
    }

    cards.forEach(function(c, i){ c.classList.toggle('active', i === curr); });
    dots.forEach(function(d, i){ d.classList.toggle('active', i === curr); });
  }

  function startAuto() {
    clearInterval(timer);
    timer = setInterval(function(){ goTo(curr + 1); }, 4500);
  }

  btnPrev && btnPrev.addEventListener('click', function(){ goTo(curr-1); startAuto(); });
  btnNext && btnNext.addEventListener('click', function(){ goTo(curr+1); startAuto(); });
  dots.forEach(function(d){
    d.addEventListener('click', function(){ goTo(parseInt(this.dataset.idx)); startAuto(); });
  });

  /* Tap inactive card to navigate */
  cards.forEach(function(c, i){
    c.addEventListener('click', function(){ if(i!==curr){ goTo(i); startAuto(); } });
  });

  /* Touch swipe */
  var sx = 0;
  outer.addEventListener('touchstart', function(e){ sx = e.touches[0].clientX; }, {passive:true});
  outer.addEventListener('touchend',   function(e){
    var dx = sx - e.changedTouches[0].clientX;
    if (Math.abs(dx) > 40){ goTo(dx > 0 ? curr+1 : curr-1); startAuto(); }
  }, {passive:true});

  /* Pause on hover */
  outer.addEventListener('mouseenter', function(){ clearInterval(timer); });
  outer.addEventListener('mouseleave', startAuto);

  /* Init */
  setup();
  startAuto();

  var rTimer;
  window.addEventListener('resize', function(){
    clearTimeout(rTimer);
    rTimer = setTimeout(setup, 120);
  });
  window.addEventListener('load', setup);
})();
</script>
<?php endif; ?>