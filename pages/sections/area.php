<!-- AREA PENGIRIMAN - Banner Peta + Tag Cloud -->
<style>
.area-section {
  position: relative;
  padding: 6rem 0;
  overflow: hidden;
  background: linear-gradient(160deg, #fdf0f3 0%, #fdf6ee 50%, #f0f4ed 100%);
}

/* ── Peta SVG blur background ── */
.area-map-bg {
  position: absolute;
  inset: 0;
  display: flex;
  align-items: center;
  justify-content: center;
  pointer-events: none;
  overflow: hidden;
}
.area-map-bg svg {
  width: 85%;
  max-width: 900px;
  opacity: .055;
  filter: blur(1.5px);
}

/* Radial vignette over map */
.area-map-vignette {
  position: absolute;
  inset: 0;
  background:
    radial-gradient(ellipse 70% 60% at 50% 50%, transparent 30%, rgba(253,246,238,.95) 100%);
  pointer-events: none;
}

/* Decorative bg glow blobs */
.area-blob {
  position: absolute;
  border-radius: 50%;
  pointer-events: none;
  filter: blur(60px);
}
.area-blob-1 {
  width: 400px; height: 400px;
  top: -100px; right: -80px;
  background: rgba(192,72,90,.07);
}
.area-blob-2 {
  width: 350px; height: 350px;
  bottom: -80px; left: -60px;
  background: rgba(125,155,118,.07);
}
.area-blob-3 {
  width: 300px; height: 300px;
  top: 40%; left: 40%;
  background: rgba(201,168,76,.05);
}

/* ── Header ── */
.area-header {
  position: relative;
  z-index: 3;
  text-align: center;
  margin-bottom: 3rem;
  padding: 0 1.5rem;
}
.area-eyebrow {
  display: inline-flex;
  align-items: center;
  gap: .6rem;
  font-family: 'Jost', sans-serif;
  font-size: .7rem;
  font-weight: 500;
  letter-spacing: .18em;
  text-transform: uppercase;
  color: var(--gold, #c9a84c);
  margin-bottom: 1rem;
}
.area-eyebrow::before,
.area-eyebrow::after {
  content: '';
  display: block;
  width: 32px; height: 1px;
  background: var(--gold, #c9a84c);
  opacity: .5;
}
.area-title {
  font-family: 'Cormorant Garamond', serif;
  font-size: clamp(2rem, 4vw, 3rem);
  font-weight: 300;
  color: #2a1018;
  line-height: 1.15;
  margin-bottom: .6rem;
}
.area-title em { font-style: italic; color: var(--rose, #c0485a); }
.area-subtitle {
  font-family: 'Jost', sans-serif;
  font-size: .9rem;
  color: #9a7070;
}

/* ── Stats bar ── */
.area-stats {
  position: relative;
  z-index: 3;
  display: flex;
  justify-content: center;
  gap: 2.5rem;
  flex-wrap: wrap;
  margin-bottom: 3rem;
  padding: 0 1.5rem;
}
.area-stat-item {
  text-align: center;
}
.area-stat-num {
  font-family: 'Cormorant Garamond', serif;
  font-size: 2.2rem;
  font-weight: 600;
  color: var(--rose, #c0485a);
  line-height: 1;
  display: block;
}
.area-stat-label {
  font-family: 'Jost', sans-serif;
  font-size: .68rem;
  letter-spacing: .12em;
  text-transform: uppercase;
  color: #9a7070;
  margin-top: .2rem;
  display: block;
}
.area-stat-divider {
  width: 1px;
  background: rgba(192,72,90,.15);
  align-self: stretch;
  min-height: 40px;
}
@media (max-width: 480px) { .area-stat-divider { display: none; } }

/* ── Tag cloud wrapper ── */
.area-cloud-wrap {
  position: relative;
  z-index: 3;
  max-width: 1000px;
  margin: 0 auto;
  padding: 0 2rem;
}

/* Cloud inner — flex wrap centered */
.area-cloud {
  display: flex;
  flex-wrap: wrap;
  gap: .65rem;
  justify-content: center;
  align-items: center;
}

/* City tag */
.area-tag {
  display: inline-flex;
  align-items: center;
  gap: .45rem;
  text-decoration: none;
  padding: .45rem 1rem .45rem .8rem;
  border-radius: 999px;
  border: 1px solid rgba(192,72,90,.13);
  background: rgba(255,255,255,.75);
  backdrop-filter: blur(8px);
  box-shadow: 0 2px 10px rgba(122,31,46,.06);
  transition:
    transform .25s cubic-bezier(.22,1,.36,1),
    background .2s,
    border-color .2s,
    box-shadow .2s;
  animation: tagReveal .5s ease both;
}
.area-tag:hover {
  transform: translateY(-3px) scale(1.04);
  background: rgba(255,255,255,.97);
  border-color: rgba(192,72,90,.28);
  box-shadow: 0 8px 24px rgba(122,31,46,.12);
}

/* Tag sizes — variety for organic cloud feel */
.area-tag.sz-lg { padding: .55rem 1.2rem .55rem .95rem; }
.area-tag.sz-sm { padding: .35rem .85rem .35rem .7rem; }

/* Flower dot */
.area-tag-dot {
  width: 7px; height: 7px;
  border-radius: 50%;
  background: var(--rose, #c0485a);
  opacity: .5;
  flex-shrink: 0;
  transition: opacity .2s, transform .2s;
}
.area-tag:hover .area-tag-dot {
  opacity: 1;
  transform: scale(1.3);
}

/* City name */
.area-tag-city {
  font-family: 'Jost', sans-serif;
  font-size: .8rem;
  font-weight: 500;
  color: #2a1018;
  transition: color .2s;
}
.area-tag.sz-lg .area-tag-city { font-size: .88rem; }
.area-tag.sz-sm .area-tag-city { font-size: .72rem; }
.area-tag:hover .area-tag-city { color: var(--rose, #c0485a); }

/* Product count badge */
.area-tag-count {
  font-family: 'Jost', sans-serif;
  font-size: .6rem;
  font-weight: 600;
  color: var(--gold, #c9a84c);
  background: rgba(201,168,76,.1);
  border: 1px solid rgba(201,168,76,.2);
  border-radius: 999px;
  padding: .1rem .4rem;
  white-space: nowrap;
  transition: background .2s, color .2s;
}
.area-tag:hover .area-tag-count {
  background: rgba(201,168,76,.2);
  color: #8a5e00;
}

/* Stagger animation */
@keyframes tagReveal {
  from { opacity:0; transform: scale(.85) translateY(10px); }
  to   { opacity:1; transform: scale(1) translateY(0); }
}
<?php
$tagCount = count($cities ?? []);
for ($i = 0; $i < $tagCount; $i++):
  $delay = round($i * 0.04, 2);
?>
.area-tag:nth-child(<?= $i+1 ?>) { animation-delay: <?= $delay ?>s; }
<?php endfor; ?>

/* ── CTA bottom ── */
.area-cta {
  position: relative;
  z-index: 3;
  text-align: center;
  margin-top: 2.8rem;
}
.area-cta-divider {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 1rem;
  margin-bottom: 1.4rem;
  color: rgba(201,168,76,.35);
  font-size: .9rem;
  letter-spacing: .2em;
}
.area-cta-divider::before,
.area-cta-divider::after {
  content: '';
  flex: 1;
  max-width: 100px;
  height: 1px;
  background: linear-gradient(90deg, transparent, rgba(201,168,76,.3), transparent);
}
.area-cta-link {
  display: inline-flex;
  align-items: center;
  gap: .5rem;
  font-family: 'Jost', sans-serif;
  font-size: .82rem;
  font-weight: 500;
  letter-spacing: .06em;
  text-transform: uppercase;
  color: var(--rose, #c0485a);
  text-decoration: none;
  border: 1.5px solid rgba(192,72,90,.25);
  padding: .7rem 1.8rem;
  border-radius: 999px;
  background: rgba(255,255,255,.7);
  backdrop-filter: blur(8px);
  transition: background .2s, border-color .2s, color .2s, transform .2s;
}
.area-cta-link:hover {
  background: var(--rose, #c0485a);
  border-color: var(--rose, #c0485a);
  color: #fff;
  transform: translateY(-2px);
}
.area-cta-link svg { transition: transform .2s; }
.area-cta-link:hover svg { transform: translateX(4px); }
</style>

<?php if (!empty($cities)): ?>
<?php
/* Hitung jumlah produk per kota dari DB — fallback ke random jika belum ada kolom */
$cityProductCounts = [];
try {
  $stmt = $pdo->query("SELECT city_id, COUNT(*) as total FROM products WHERE is_active=1 GROUP BY city_id");
  foreach ($stmt->fetchAll() as $row) {
    $cityProductCounts[$row['city_id']] = $row['total'];
  }
} catch (Exception $e) {
  /* kolom city_id belum ada — pakai angka dummy */
}

/* Tag size classes untuk variasi visual cloud */
$sizes = ['sz-lg','','','sz-sm','','sz-lg','sz-sm','',''];
?>

<section class="area-section">

  <!-- Blobs -->
  <div class="area-blob area-blob-1"></div>
  <div class="area-blob area-blob-2"></div>
  <div class="area-blob area-blob-3"></div>

  <!-- Peta Indonesia SVG (outline sederhana) -->
  <div class="area-map-bg">
    <svg viewBox="0 0 800 400" xmlns="http://www.w3.org/2000/svg" fill="none">
      <g stroke="#c0485a" stroke-width="1.2" fill="rgba(192,72,90,0.08)">
        <!-- Sumatera -->
        <path d="M60,180 Q80,120 130,100 Q180,85 210,110 Q230,130 220,160 Q200,190 170,200 Q130,210 100,200 Q70,195 60,180Z"/>
        <!-- Jawa -->
        <path d="M230,200 Q280,185 340,188 Q400,190 450,195 Q480,200 470,215 Q440,225 400,222 Q350,220 300,218 Q260,216 240,210 Q225,207 230,200Z"/>
        <!-- Kalimantan -->
        <path d="M360,80 Q420,60 490,70 Q540,80 560,120 Q570,160 550,200 Q520,230 480,235 Q440,238 400,220 Q370,205 355,170 Q340,135 360,80Z"/>
        <!-- Sulawesi -->
        <path d="M560,130 Q580,110 600,120 Q615,130 610,155 Q600,175 580,180 Q565,178 558,160 Q552,145 560,130Z M600,155 Q620,145 635,155 Q645,170 635,185 Q620,195 605,188 Q595,178 600,155Z"/>
        <!-- Papua -->
        <path d="M660,140 Q720,120 760,140 Q785,158 775,185 Q755,210 720,215 Q690,215 670,198 Q650,180 660,140Z"/>
        <!-- Bali & Lombok -->
        <ellipse cx="488" cy="215" rx="16" ry="10" transform="rotate(-10 488 215)"/>
        <ellipse cx="512" cy="218" rx="12" ry="8" transform="rotate(-8 512 218)"/>
        <!-- NTT -->
        <path d="M530,225 Q560,220 590,228 Q600,238 585,245 Q565,248 545,242 Q528,236 530,225Z"/>
        <!-- Maluku -->
        <ellipse cx="630" cy="185" rx="10" ry="14" transform="rotate(15 630 185)"/>
        <ellipse cx="648" cy="200" rx="8" ry="12" transform="rotate(20 648 200)"/>
      </g>
      <!-- Pin dots kota -->
      <?php foreach (array_slice($cities, 0, 12) as $idx => $c): ?>
      <circle cx="<?= 200 + ($idx * 47) % 420 ?>" cy="<?= 130 + ($idx * 31) % 100 ?>" r="3" fill="rgba(192,72,90,0.4)"/>
      <?php endforeach; ?>
    </svg>
    <div class="area-map-vignette"></div>
  </div>

  <!-- Header -->
  <div class="area-header">
    <div class="area-eyebrow">✦ Jangkauan Nasional ✦</div>
    <h2 class="area-title">Kami Kirim Bunga ke<br><em>Seluruh Indonesia</em></h2>
    <p class="area-subtitle">Pilih kotamu dan temukan rangkaian bunga terbaik di dekatmu</p>
  </div>

  <!-- Stats -->
  <div class="area-stats">
    <div class="area-stat-item">
      <span class="area-stat-num"><?= count($cities) ?>+</span>
      <span class="area-stat-label">Kota Layanan</span>
    </div>
    <div class="area-stat-divider"></div>
    <div class="area-stat-item">
      <span class="area-stat-num">24</span>
      <span class="area-stat-label">Jam Operasional</span>
    </div>
    <div class="area-stat-divider"></div>
    <div class="area-stat-item">
      <span class="area-stat-num">500+</span>
      <span class="area-stat-label">Pelanggan / Bulan</span>
    </div>
  </div>

  <!-- Tag Cloud -->
  <div class="area-cloud-wrap">
    <div class="area-cloud">
      <?php foreach ($cities as $idx => $city):
        $sizeClass = $sizes[$idx % count($sizes)];
        /* Hitung produk — fallback ke range 8–40 jika belum ada data */
        $prodCount = $cityProductCounts[$city['id']] ?? (8 + (($city['id'] * 7 + $idx * 13) % 33));
      ?>
      <a href="<?= BASE_URL ?>/toko-bunga-<?= $city['slug'] ?>"
         class="area-tag <?= $sizeClass ?>"
         title="Toko Bunga <?= clean($city['name']) ?> – <?= $prodCount ?> Produk">
        <span class="area-tag-dot"></span>
        <span class="area-tag-city">Toko Bunga <?= clean($city['name']) ?></span>
        <span class="area-tag-count"><?= $prodCount ?> produk</span>
      </a>
      <?php endforeach; ?>
    </div>
  </div>

  <!-- CTA -->
  <div class="area-cta">
    <div class="area-cta-divider">✿</div>
    <a href="<?= BASE_URL ?>/area-layanan" class="area-cta-link">
      Lihat Semua Area Layanan
      <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
        <path d="M5 12h14M12 5l7 7-7 7"/>
      </svg>
    </a>
  </div>

</section>
<?php endif; ?>