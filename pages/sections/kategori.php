<!-- KATEGORI - Kebun Bunga Elegan -->
<style>
.kat-section {
  position: relative;
  padding: 6rem 0 5rem;
  background: linear-gradient(180deg, #fff 0%, #fdf6ee 60%, #f0f4ed 100%);
  overflow: hidden;
}

/* Subtle background pattern */
.kat-bg-pattern {
  position: absolute;
  inset: 0;
  background-image:
    radial-gradient(circle at 90% 10%, rgba(201,168,76,.06) 0%, transparent 45%),
    radial-gradient(circle at 10% 90%, rgba(192,72,90,.05) 0%, transparent 45%);
  pointer-events: none;
}

/* Top wave from hero */
.kat-wave-top {
  position: absolute;
  top: -2px; left: 0; right: 0;
  pointer-events: none;
  line-height: 0;
}
.kat-wave-top svg { width: 100%; display: block; }

/* Section header */
.kat-header {
  text-align: center;
  margin-bottom: 3.5rem;
  position: relative;
  z-index: 2;
}

.kat-eyebrow {
  display: inline-flex;
  align-items: center;
  gap: .5rem;
  font-family: 'Jost', sans-serif;
  font-size: .7rem;
  font-weight: 500;
  letter-spacing: .18em;
  text-transform: uppercase;
  color: var(--gold, #c9a84c);
  margin-bottom: 1rem;
}
.kat-eyebrow::before,
.kat-eyebrow::after {
  content: '';
  display: block;
  width: 32px;
  height: 1px;
  background: var(--gold, #c9a84c);
  opacity: .5;
}

.kat-title {
  font-family: 'Cormorant Garamond', serif;
  font-size: clamp(2rem, 4vw, 3rem);
  font-weight: 300;
  color: #2a1018;
  line-height: 1.15;
  margin-bottom: .6rem;
}
.kat-title em { font-style: italic; color: var(--rose, #c0485a); }

.kat-subtitle {
  font-family: 'Jost', sans-serif;
  font-size: .9rem;
  color: #9a7070;
  max-width: 400px;
  margin: 0 auto;
}

/* Grid */
.kat-grid {
  position: relative;
  z-index: 2;
  max-width: 1280px;
  margin: 0 auto;
  padding: 0 2rem;
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  gap: 1.5rem;
}
@media (min-width: 768px)  { .kat-grid { grid-template-columns: repeat(3, 1fr); } }
@media (min-width: 1024px) { .kat-grid { grid-template-columns: repeat(4, 1fr); } }

/* Card */
.kat-card {
  position: relative;
  display: block;
  text-decoration: none;
  border-radius: 24px;
  overflow: hidden;
  background: #fff;
  border: 1px solid rgba(192,72,90,.08);
  box-shadow: 0 4px 20px rgba(122,31,46,.06);
  transition: transform .35s cubic-bezier(.22,1,.36,1),
              box-shadow .35s ease,
              border-color .2s ease;
  animation: cardReveal .6s ease both;
}
.kat-card:hover {
  transform: translateY(-6px) scale(1.01);
  box-shadow: 0 20px 50px rgba(122,31,46,.14);
  border-color: rgba(192,72,90,.2);
}

@keyframes cardReveal {
  from { opacity:0; transform: translateY(24px); }
  to   { opacity:1; transform: translateY(0); }
}
/* staggered delays */
.kat-card:nth-child(1) { animation-delay:.05s; }
.kat-card:nth-child(2) { animation-delay:.12s; }
.kat-card:nth-child(3) { animation-delay:.18s; }
.kat-card:nth-child(4) { animation-delay:.24s; }
.kat-card:nth-child(5) { animation-delay:.30s; }
.kat-card:nth-child(6) { animation-delay:.36s; }
.kat-card:nth-child(7) { animation-delay:.42s; }
.kat-card:nth-child(8) { animation-delay:.48s; }

/* Image area */
.kat-img-wrap {
  position: relative;
  height: 160px;
  overflow: hidden;
  background: linear-gradient(135deg, #f2d4d7 0%, #f0f4ed 100%);
}
.kat-img-wrap img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  transition: transform .5s cubic-bezier(.22,1,.36,1);
}
.kat-card:hover .kat-img-wrap img { transform: scale(1.08); }

/* Gradient overlay on image */
.kat-img-overlay {
  position: absolute;
  inset: 0;
  background: linear-gradient(180deg, transparent 40%, rgba(42,16,24,.3) 100%);
  opacity: 0;
  transition: opacity .3s ease;
}
.kat-card:hover .kat-img-overlay { opacity: 1; }

/* Corner floral SVG per card */
.kat-corner-svg {
  position: absolute;
  top: 8px; right: 8px;
  width: 36px;
  opacity: .22;
  pointer-events: none;
  transition: opacity .3s;
}
.kat-card:hover .kat-corner-svg { opacity: .45; }

/* Body */
.kat-body {
  padding: 1.1rem 1.2rem 1.3rem;
  position: relative;
}

/* Gold line accent */
.kat-body::before {
  content: '';
  display: block;
  width: 28px;
  height: 2px;
  background: linear-gradient(90deg, var(--gold, #c9a84c), transparent);
  margin-bottom: .7rem;
  transition: width .3s ease;
}
.kat-card:hover .kat-body::before { width: 56px; }

.kat-name {
  font-family: 'Cormorant Garamond', serif;
  font-size: 1.05rem;
  font-weight: 600;
  color: #2a1018;
  margin-bottom: .3rem;
  line-height: 1.2;
  transition: color .2s;
}
.kat-card:hover .kat-name { color: var(--rose, #c0485a); }

.kat-desc {
  font-family: 'Jost', sans-serif;
  font-size: .75rem;
  color: #9a7070;
  line-height: 1.6;
  
  display: -webkit-box;
  -webkit-box-orient: vertical;

  -webkit-line-clamp: 2;
  line-clamp: 2;

  overflow: hidden;
  margin-bottom: .8rem;
}
.kat-cta {
  display: inline-flex;
  align-items: center;
  gap: .3rem;
  font-family: 'Jost', sans-serif;
  font-size: .72rem;
  font-weight: 500;
  letter-spacing: .06em;
  text-transform: uppercase;
  color: var(--rose, #c0485a);
  transition: gap .2s;
}
.kat-card:hover .kat-cta { gap: .5rem; }
.kat-cta svg { transition: transform .2s; }
.kat-card:hover .kat-cta svg { transform: translateX(3px); }

/* Bottom ornament */
.kat-bottom {
  text-align: center;
  margin-top: 3rem;
  position: relative;
  z-index: 2;
  font-family: 'Jost', sans-serif;
}
.kat-ornament {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 1rem;
  color: rgba(201,168,76,.4);
  font-size: 1.1rem;
  letter-spacing: .3em;
  margin-bottom: 1rem;
}
</style>

<section class="kat-section">
  <div class="kat-bg-pattern"></div>

  <!-- Wave top (continues from hero) -->
  <div class="kat-wave-top">
    <svg viewBox="0 0 1440 50" preserveAspectRatio="none" xmlns="http://www.w3.org/2000/svg">
      <path d="M0,30 C360,0 720,50 1080,20 C1260,5 1380,35 1440,30 L1440,0 L0,0 Z" fill="white"/>
    </svg>
  </div>

  <!-- Header -->
  <div class="kat-header">
    <div class="kat-eyebrow">✦ Koleksi Pilihan ✦</div>
    <h2 class="kat-title">Pilihan Bunga untuk<br><em>Setiap Momen Spesial</em></h2>
    <p class="kat-subtitle">Dari kebun ke hati Anda — rangkaian bunga segar untuk setiap perayaan</p>
  </div>

  <!-- Grid -->
  <div class="kat-grid">
    <?php foreach ($categories as $cat): ?>
    <a href="<?= BASE_URL ?>/<?= $cat['slug'] ?>" class="kat-card">

      <!-- Image -->
      <div class="kat-img-wrap">
        <img src="<?= UPLOAD_URL . ($cat['image'] ?? '') ?>"
             alt="<?= clean($cat['name']) ?> – Chika Florist"
             onerror="this.style.display='none'">
        <div class="kat-img-overlay"></div>

        <!-- Decorative SVG corner -->
        <svg class="kat-corner-svg" viewBox="0 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg">
          <ellipse cx="20" cy="14" rx="6" ry="10" fill="#c0485a" transform="rotate(-20 20 14)"/>
          <ellipse cx="26" cy="12" rx="6" ry="10" fill="#c0485a" transform="rotate(15 26 12)"/>
          <ellipse cx="14" cy="16" rx="6" ry="10" fill="#c0485a" transform="rotate(-55 14 16)"/>
          <circle cx="20" cy="15" r="4" fill="#fdf6ee"/>
          <circle cx="20" cy="15" r="2" fill="#c9a84c"/>
          <path d="M12 28 Q20 20 28 28 Q20 32 12 28Z" fill="#7d9b76"/>
        </svg>
      </div>

      <!-- Body -->
      <div class="kat-body">
        <h3 class="kat-name"><?= clean($cat['name']) ?></h3>
        <p class="kat-desc"><?= clean(substr($cat['description'] ?? '', 0, 80)) ?></p>
        <span class="kat-cta">
          Lihat Koleksi
          <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
            <path d="M5 12h14M12 5l7 7-7 7"/>
          </svg>
        </span>
      </div>
    </a>
    <?php endforeach; ?>
  </div>

  <!-- Bottom ornament -->
  <div class="kat-bottom">
    <div class="kat-ornament">✦ &nbsp; ✿ &nbsp; ✦</div>
  </div>
</section>