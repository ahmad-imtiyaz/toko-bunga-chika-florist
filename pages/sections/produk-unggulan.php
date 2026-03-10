<!-- PRODUK UNGGULAN - Taman Bunga Elegan -->
<style>
.prod-section {
  position: relative;
  padding: 5rem 0 6rem;
  overflow: hidden;
  background: linear-gradient(170deg, #f0f4ed 0%, #fdf6ee 35%, #fdf0f3 100%);
}

/* Layered background atmosphere */
.prod-bg {
  position: absolute;
  inset: 0;
  pointer-events: none;
}
.prod-bg::before {
  content: '';
  position: absolute;
  top: -80px; right: -80px;
  width: 500px; height: 500px;
  border-radius: 50%;
  background: radial-gradient(circle, rgba(192,72,90,.07) 0%, transparent 70%);
}
.prod-bg::after {
  content: '';
  position: absolute;
  bottom: -60px; left: -60px;
  width: 400px; height: 400px;
  border-radius: 50%;
  background: radial-gradient(circle, rgba(125,155,118,.08) 0%, transparent 70%);
}

/* Trellis pattern */
.prod-trellis {
  position: absolute;
  inset: 0;
  opacity: .025;
  background-image:
    repeating-linear-gradient(45deg, #7a1f2e 0, #7a1f2e 1px, transparent 0, transparent 50%),
    repeating-linear-gradient(-45deg, #7a1f2e 0, #7a1f2e 1px, transparent 0, transparent 50%);
  background-size: 28px 28px;
  pointer-events: none;
}

/* Floating SVG vines */
.prod-vine {
  position: absolute;
  opacity: .12;
  pointer-events: none;
}
.prod-vine-tl { top: 0; left: 0; width: 260px; }
.prod-vine-br { bottom: 0; right: 0; width: 240px; transform: rotate(180deg); }

/* Section header */
.prod-header {
  position: relative;
  z-index: 2;
  text-align: center;
  margin-bottom: 3.5rem;
  padding: 0 1.5rem;
}
.prod-eyebrow {
  display: inline-flex;
  align-items: center;
  gap: .6rem;
  font-family: 'Jost', sans-serif;
  font-size: .7rem;
  font-weight: 500;
  letter-spacing: .18em;
  text-transform: uppercase;
  color: var(--rose, #c0485a);
  margin-bottom: 1rem;
}
.prod-eyebrow::before,
.prod-eyebrow::after {
  content: '';
  display: block;
  width: 36px; height: 1px;
  background: var(--rose, #c0485a);
  opacity: .4;
}
.prod-title {
  font-family: 'Cormorant Garamond', serif;
  font-size: clamp(2rem, 4vw, 3rem);
  font-weight: 300;
  color: #2a1018;
  line-height: 1.15;
  margin-bottom: .6rem;
}
.prod-title em { font-style: italic; color: var(--rose, #c0485a); }
.prod-subtitle {
  font-family: 'Jost', sans-serif;
  font-size: .9rem;
  color: #9a7070;
}

/* Grid — desktop */
.prod-grid {
  position: relative;
  z-index: 2;
  max-width: 1280px;
  margin: 0 auto;
  padding: 0 2rem;
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  gap: 1.4rem;
}
@media (min-width: 640px)  { .prod-grid { grid-template-columns: repeat(3, 1fr); } }
@media (min-width: 1024px) { .prod-grid { grid-template-columns: repeat(4, 1fr); } }

/* ── MOBILE SLIDER ── */
@media (max-width: 639px) {
  .prod-grid-wrap {
    position: relative;
    z-index: 2;
  }

  .prod-grid {
    display: flex;
    overflow-x: auto;
    scroll-snap-type: x mandatory;
    -webkit-overflow-scrolling: touch;
    gap: 1rem;

    padding: 0 10vw 1rem; /* was: 0 1.2rem 1rem */
    scroll-padding: 10vw; /* NEW */

    scrollbar-width: none;
    max-width: 100%;
  }
  .prod-grid::-webkit-scrollbar { display: none; }

  .prod-card {
    flex: 0 0 78vw; /* was: 72vw */
    max-width: 260px; /* was: 280px */
    scroll-snap-align: center; /* was: start */
  }

  /* Dot indicators */
  .prod-dots {
    display: flex;
    justify-content: center;
    gap: .5rem;
    margin-top: 1.2rem;
  }
  .prod-dot {
    width: 6px; height: 6px;
    border-radius: 50%;
    background: rgba(192,72,90,.2);
    border: none;
    padding: 0;
    cursor: pointer;
    transition: background .2s, width .2s;
  }
  .prod-dot.active {
    background: var(--rose, #c0485a);
    width: 20px;
    border-radius: 3px;
  }

  /* Prev/Next arrows */
  .prod-arrows {
    display: flex;
    justify-content: center;
    gap: .8rem;
    margin-top: .8rem;
  }
  .prod-arrow {
    width: 36px; height: 36px;
    border-radius: 50%;
    border: 1.5px solid rgba(192,72,90,.25);
    background: rgba(255,255,255,.85);
    color: var(--rose, #c0485a);
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: background .2s, border-color .2s;
  }
  .prod-arrow:hover {
    background: var(--rose, #c0485a);
    border-color: var(--rose, #c0485a);
    color: #fff;
  }
  .prod-arrow:disabled {
    opacity: .3;
    cursor: default;
  }
}

/* Hide dots/arrows on desktop */
@media (min-width: 640px) {
  .prod-dots, .prod-arrows { display: none !important; }
  .prod-grid-wrap { display: contents; }
}

/* Card */
.prod-card {
  position: relative;
  background: #fff;
  border-radius: 20px;
  overflow: hidden;
  border: 1px solid rgba(192,72,90,.07);
  box-shadow: 0 2px 16px rgba(122,31,46,.05);
  transition: transform .38s cubic-bezier(.22,1,.36,1),
              box-shadow .38s ease,
              border-color .2s;
  animation: prodReveal .55s ease both;
}
.prod-card:hover {
  transform: translateY(-8px);
  box-shadow: 0 24px 56px rgba(122,31,46,.13);
  border-color: rgba(192,72,90,.18);
}

@keyframes prodReveal {
  from { opacity:0; transform:translateY(28px); }
  to   { opacity:1; transform:translateY(0); }
}
.prod-card:nth-child(1) { animation-delay:.06s; }
.prod-card:nth-child(2) { animation-delay:.13s; }
.prod-card:nth-child(3) { animation-delay:.20s; }
.prod-card:nth-child(4) { animation-delay:.27s; }
.prod-card:nth-child(5) { animation-delay:.34s; }
.prod-card:nth-child(6) { animation-delay:.41s; }
.prod-card:nth-child(7) { animation-delay:.48s; }
.prod-card:nth-child(8) { animation-delay:.55s; }

/* Badge terlaris */
.prod-badge {
  position: absolute;
  top: 10px; left: 10px;
  z-index: 5;
  background: linear-gradient(135deg, var(--gold, #c9a84c), #e8c96a);
  color: #5a3a00;
  font-family: 'Jost', sans-serif;
  font-size: .6rem;
  font-weight: 600;
  letter-spacing: .08em;
  text-transform: uppercase;
  padding: .25rem .65rem;
  border-radius: 999px;
  box-shadow: 0 2px 8px rgba(201,168,76,.35);
}

/* Image */
.prod-img-wrap {
  position: relative;
  height: 190px;
  overflow: hidden;
  background: linear-gradient(135deg, #f2d4d7, #f0f4ed);
}
.prod-img-wrap img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  transition: transform .5s cubic-bezier(.22,1,.36,1);
}
.prod-card:hover .prod-img-wrap img { transform: scale(1.09); }

/* Overlay gradient */
.prod-img-overlay {
  position: absolute;
  inset: 0;
  background: linear-gradient(180deg,
    transparent 45%,
    rgba(42,16,24,.55) 100%);
  opacity: 0;
  transition: opacity .35s ease;
}
.prod-card:hover .prod-img-overlay { opacity: 1; }

/* Quick action on hover */
.prod-quick {
  position: absolute;
  bottom: 12px; left: 50%;
  transform: translateX(-50%) translateY(8px);
  opacity: 0;
  transition: opacity .3s, transform .3s;
  white-space: nowrap;
  z-index: 4;
}
.prod-card:hover .prod-quick {
  opacity: 1;
  transform: translateX(-50%) translateY(0);
}
.prod-quick a {
  display: inline-flex;
  align-items: center;
  gap: .4rem;
  background: rgba(255,255,255,.95);
  color: #25a244;
  font-family: 'Jost', sans-serif;
  font-size: .72rem;
  font-weight: 600;
  padding: .45rem 1rem;
  border-radius: 999px;
  text-decoration: none;
  box-shadow: 0 4px 16px rgba(0,0,0,.15);
  border: 1px solid rgba(37,162,68,.2);
  transition: background .2s, color .2s;
}
.prod-quick a:hover { background: #25a244; color: #fff; }

/* Category ribbon */
.prod-cat-ribbon {
  position: absolute;
  top: 10px; right: -1px;
  background: rgba(192,72,90,.9);
  color: #fff;
  font-family: 'Jost', sans-serif;
  font-size: .58rem;
  font-weight: 500;
  letter-spacing: .06em;
  text-transform: uppercase;
  padding: .2rem .6rem .2rem .5rem;
  border-radius: 4px 0 0 4px;
  z-index: 5;
}

/* Card body */
.prod-body {
  padding: 1rem 1.1rem 1.2rem;
}

.prod-name {
  font-family: 'Cormorant Garamond', serif;
  font-size: 1rem;
  font-weight: 600;
  color: #2a1018;
  line-height: 1.25;
  margin-bottom: .5rem;
  transition: color .2s;
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}
.prod-card:hover .prod-name { color: var(--rose, #c0485a); }

/* Price row */
.prod-price-row {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-bottom: .9rem;
}
.prod-price {
  font-family: 'Jost', sans-serif;
  font-size: .95rem;
  font-weight: 600;
  color: var(--rose, #c0485a);
}

/* Leaf icon accent */
.prod-leaf {
  color: var(--sage, #7d9b76);
  font-size: .85rem;
  opacity: .7;
}

/* CTA button */
.prod-btn {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: .45rem;
  width: 100%;
  padding: .6rem;
  border-radius: 12px;
  border: 1.5px solid rgba(37,162,68,.25);
  background: rgba(37,162,68,.04);
  color: #1a8a40;
  font-family: 'Jost', sans-serif;
  font-size: .75rem;
  font-weight: 600;
  text-decoration: none;
  transition: background .25s, color .25s, border-color .25s, transform .2s;
}
.prod-btn:hover {
  background: #25a244;
  color: #fff;
  border-color: #25a244;
  transform: scale(1.02);
}

/* Bottom CTA */
.prod-footer {
  position: relative;
  z-index: 2;
  text-align: center;
  margin-top: 3rem;
  padding: 0 1.5rem;
}
.prod-footer-ornament {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 1.2rem;
  margin-bottom: 1.4rem;
  color: rgba(201,168,76,.35);
  font-size: 1rem;
  letter-spacing: .25em;
}
.prod-footer-ornament::before,
.prod-footer-ornament::after {
  content: '';
  flex: 1;
  max-width: 120px;
  height: 1px;
  background: linear-gradient(90deg, transparent, rgba(201,168,76,.3), transparent);
}
</style>

<?php if (!empty($featured)): ?>
<section class="prod-section">
  <div class="prod-bg"></div>
  <div class="prod-trellis"></div>

  <!-- Vine SVG top-left -->
  <svg class="prod-vine prod-vine-tl" viewBox="0 0 260 300" fill="none" xmlns="http://www.w3.org/2000/svg">
    <path d="M10 280 Q40 200 20 120 Q0 60 60 20" stroke="#7d9b76" stroke-width="2.5" fill="none" stroke-dasharray="6 4"/>
    <ellipse cx="55" cy="80"  rx="22" ry="14" fill="#7d9b76" transform="rotate(-35 55 80)"/>
    <ellipse cx="25" cy="150" rx="20" ry="13" fill="#7d9b76" transform="rotate(20 25 150)"/>
    <ellipse cx="45" cy="220" rx="18" ry="12" fill="#7d9b76" transform="rotate(-15 45 220)"/>
    <!-- flower 1 -->
    <ellipse cx="80"  cy="45"  rx="8" ry="14" fill="#c0485a" transform="rotate(-30 80 45)"/>
    <ellipse cx="90"  cy="38"  rx="8" ry="14" fill="#c0485a" transform="rotate(15 90 38)"/>
    <ellipse cx="72"  cy="38"  rx="8" ry="14" fill="#c0485a" transform="rotate(-75 72 38)"/>
    <ellipse cx="82"  cy="52"  rx="8" ry="14" fill="#f2d4d7" transform="rotate(50 82 52)"/>
    <circle cx="82" cy="44" r="5" fill="#fdf6ee"/>
    <circle cx="82" cy="44" r="2.5" fill="#c9a84c"/>
    <!-- flower 2 -->
    <ellipse cx="10"  cy="100" rx="7" ry="12" fill="#c9a84c" transform="rotate(-20 10 100)"/>
    <ellipse cx="20"  cy="94"  rx="7" ry="12" fill="#c9a84c" transform="rotate(25 20 94)"/>
    <ellipse cx="4"   cy="94"  rx="7" ry="12" fill="#c9a84c" transform="rotate(-65 4 94)"/>
    <circle cx="12" cy="98" r="4" fill="#fdf6ee"/>
    <circle cx="12" cy="98" r="2" fill="#c0485a"/>
  </svg>

  <!-- Vine SVG bottom-right -->
  <svg class="prod-vine prod-vine-br" viewBox="0 0 260 300" fill="none" xmlns="http://www.w3.org/2000/svg">
    <path d="M10 280 Q40 200 20 120 Q0 60 60 20" stroke="#7d9b76" stroke-width="2.5" fill="none" stroke-dasharray="6 4"/>
    <ellipse cx="55" cy="80"  rx="22" ry="14" fill="#7d9b76" transform="rotate(-35 55 80)"/>
    <ellipse cx="25" cy="160" rx="20" ry="13" fill="#7d9b76" transform="rotate(20 25 160)"/>
    <ellipse cx="50" cy="55"  rx="8" ry="14" fill="#f2d4d7" transform="rotate(-30 50 55)"/>
    <ellipse cx="62" cy="48"  rx="8" ry="14" fill="#c0485a" transform="rotate(15 62 48)"/>
    <ellipse cx="44" cy="48"  rx="8" ry="14" fill="#c0485a" transform="rotate(-75 44 48)"/>
    <circle cx="53" cy="52" r="5" fill="#fdf6ee"/>
    <circle cx="53" cy="52" r="2.5" fill="#c9a84c"/>
  </svg>

  <!-- Header -->
  <div class="prod-header">
    <div class="prod-eyebrow">✿ Produk Pilihan ✿</div>
    <h2 class="prod-title">Koleksi Bunga <em>Terlaris</em><br>dari Kebun Kami</h2>
    <p class="prod-subtitle">Dipilih dengan cinta — favorit pelanggan setia Chika Florist</p>
  </div>

  <!-- Grid -->
  <div class="prod-grid-wrap">
    <div class="prod-grid" id="prodSlider">
    <?php foreach ($featured as $i => $prod): ?>
    <div class="prod-card">

      <?php if ($i < 3): ?>
      <div class="prod-badge">✦ Terlaris</div>
      <?php endif; ?>

      <div class="prod-cat-ribbon"><?= clean($prod['cat_name']) ?></div>

      <!-- Image -->
      <div class="prod-img-wrap">
        <img src="<?= UPLOAD_URL . ($prod['image'] ?? '') ?>"
             alt="<?= clean($prod['name']) ?> – Chika Florist"
             onerror="this.style.display='none'">
        <div class="prod-img-overlay"></div>
        <div class="prod-quick">
          <a href="<?= waLink('Halo, saya ingin pesan ' . $prod['name']) ?>" target="_blank">
            <svg width="13" height="13" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z"/><path d="M12 0C5.373 0 0 5.373 0 12c0 2.096.541 4.063 1.491 5.776L.057 23.04l5.43-1.424A11.947 11.947 0 0012 24c6.627 0 12-5.373 12-12S18.627 0 12 0zm0 21.818a9.818 9.818 0 01-5.015-1.371l-.36-.214-3.726.977.995-3.633-.235-.374A9.818 9.818 0 1112 21.818z"/></svg>
            Pesan Cepat
          </a>
        </div>
      </div>

      <!-- Body -->
      <div class="prod-body">
        <h3 class="prod-name"><?= clean($prod['name']) ?></h3>
        <div class="prod-price-row">
          <span class="prod-price"><?= formatHarga($prod['price_min'], $prod['price_max']) ?></span>
          <span class="prod-leaf">🌿</span>
        </div>
        <a href="<?= waLink('Halo, saya ingin pesan ' . $prod['name']) ?>" target="_blank" class="prod-btn">
          <svg width="13" height="13" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z"/><path d="M12 0C5.373 0 0 5.373 0 12c0 2.096.541 4.063 1.491 5.776L.057 23.04l5.43-1.424A11.947 11.947 0 0012 24c6.627 0 12-5.373 12-12S18.627 0 12 0zm0 21.818a9.818 9.818 0 01-5.015-1.371l-.36-.214-3.726.977.995-3.633-.235-.374A9.818 9.818 0 1112 21.818z"/></svg>
          Pesan Sekarang
        </a>
      </div>
    </div>
    <?php endforeach; ?>
    </div><!-- /.prod-grid -->

    <!-- Dots (mobile only) -->
    <div class="prod-dots" id="prodDots">
      <?php foreach ($featured as $i => $_): ?>
      <button class="prod-dot <?= $i===0?'active':'' ?>" data-idx="<?= $i ?>"></button>
      <?php endforeach; ?>
    </div>

    <!-- Arrows (mobile only) -->
    <div class="prod-arrows">
      <button class="prod-arrow" id="prodPrev" aria-label="Sebelumnya">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M15 18l-6-6 6-6"/></svg>
      </button>
      <button class="prod-arrow" id="prodNext" aria-label="Berikutnya">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M9 18l6-6-6-6"/></svg>
      </button>
    </div>

  </div><!-- /.prod-grid-wrap -->

  <script>
  (function(){
    if (window.innerWidth >= 640) return;
    var slider = document.getElementById('prodSlider');
    var dots   = document.querySelectorAll('.prod-dot');
    var prev   = document.getElementById('prodPrev');
    var next   = document.getElementById('prodNext');
    if (!slider) return;

    var current = 0;
    var cards   = slider.querySelectorAll('.prod-card');
    var total   = cards.length;

    function goTo(idx) {
      if (idx < 0) idx = 0;
      if (idx >= total) idx = total - 1;
      current = idx;
      var gap   = parseFloat(getComputedStyle(slider).gap) || 16;
      var cardW = cards[0].offsetWidth + gap;
      slider.scrollTo({ left: cardW * current, behavior: 'smooth' });
      dots.forEach(function(d, i) { d.classList.toggle('active', i === current); });
      if (prev) prev.disabled = current === 0;
      if (next) next.disabled = current === total - 1;
    }

    if (prev) prev.addEventListener('click', function(){ goTo(current - 1); });
    if (next) next.addEventListener('click', function(){ goTo(current + 1); });
    dots.forEach(function(d) {
      d.addEventListener('click', function(){ goTo(parseInt(this.dataset.idx)); });
    });

    var scrollTimer;
    slider.addEventListener('scroll', function() {
      clearTimeout(scrollTimer);
      scrollTimer = setTimeout(function() {
        var gap   = parseFloat(getComputedStyle(slider).gap) || 16;
        var cardW = cards[0].offsetWidth + gap;
        var idx   = Math.round(slider.scrollLeft / cardW);
        if (idx !== current) {
          current = idx;
          dots.forEach(function(d, i){ d.classList.toggle('active', i === current); });
          if (prev) prev.disabled = current === 0;
          if (next) next.disabled = current === total - 1;
        }
      }, 80);
    }, { passive: true });

    goTo(0);
  })();
  </script>

  <!-- Footer ornament -->
  <div class="prod-footer">
    <div class="prod-footer-ornament">✦ &nbsp; ✿ &nbsp; ✦</div>
  </div>

</section>
<?php endif; ?>