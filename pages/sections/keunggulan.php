<!-- KEUNGGULAN - Split Layout Elegan -->
<style>
.unggulan-section {
  position: relative;
  padding: 6rem 0;
  overflow: hidden;
  background: linear-gradient(160deg, #fdf6ee 0%, #fff 50%, #f0f4ed 100%);
}

.unggulan-bg {
  position: absolute;
  inset: 0;
  pointer-events: none;
  background-image:
    radial-gradient(circle at 5% 50%, rgba(125,155,118,.07) 0%, transparent 50%),
    radial-gradient(circle at 95% 50%, rgba(192,72,90,.05) 0%, transparent 50%);
}

/* ── Inner container ── */
.unggulan-inner {
  position: relative;
  z-index: 2;
  max-width: 1280px;
  margin: 0 auto;
  padding: 0 2rem;
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 5rem;
  align-items: center;
}
@media (max-width: 900px) {
  .unggulan-inner {
    grid-template-columns: 1fr;
    gap: 3rem;
  }
}

/* ══════════════════════════
   LEFT — FOTO
══════════════════════════ */
.unggulan-photo {
  position: relative;
  animation: fadeUp .9s ease both;
}

/* Main photo frame */
.unggulan-photo-main {
  position: relative;
  border-radius: 32px 32px 120px 32px;
  overflow: hidden;
  aspect-ratio: 4/5;
  box-shadow:
    0 40px 90px rgba(122,31,46,.16),
    0 0 0 1px rgba(192,72,90,.08);
}
.unggulan-photo-main img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  transition: transform .6s ease;
}
.unggulan-photo-main:hover img { transform: scale(1.04); }

/* Gradient overlay bottom */
.unggulan-photo-main::after {
  content: '';
  position: absolute;
  inset: 0;
  background: linear-gradient(180deg,
    transparent 55%,
    rgba(42,16,24,.4) 100%);
  pointer-events: none;
}

/* Caption di dalam foto */
.unggulan-photo-caption {
  position: absolute;
  bottom: 1.5rem;
  left: 1.5rem;
  right: 1.5rem;
  z-index: 3;
  color: #fff;
}
.unggulan-photo-caption p {
  font-family: 'Cormorant Garamond', serif;
  font-size: 1.3rem;
  font-style: italic;
  font-weight: 300;
  line-height: 1.3;
  text-shadow: 0 2px 12px rgba(0,0,0,.3);
}
.unggulan-photo-caption span {
  font-family: 'Jost', sans-serif;
  font-size: .65rem;
  letter-spacing: .18em;
  text-transform: uppercase;
  opacity: .8;
  display: block;
  margin-top: .3rem;
}

/* Gold border corner accent */
.unggulan-photo-border {
  position: absolute;
  top: -12px; left: -12px;
  right: 12px; bottom: -12px;
  border: 1.5px solid rgba(201,168,76,.3);
  border-radius: 36px 36px 124px 36px;
  pointer-events: none;
  z-index: 0;
}

/* Small secondary photo */
.unggulan-photo-mini {
  position: absolute;
  bottom: -28px;
  right: -28px;
  width: 150px;
  height: 150px;
  border-radius: 50%;
  overflow: hidden;
  border: 5px solid #fff;
  box-shadow: 0 12px 40px rgba(122,31,46,.18);
  z-index: 4;
}
.unggulan-photo-mini img {
  width: 100%; height: 100%;
  object-fit: cover;
}

/* Floating stat card */
.unggulan-stat {
  position: absolute;
  top: 12%;
  left: -36px;
  background: rgba(255,255,255,.95);
  backdrop-filter: blur(12px);
  border: 1px solid rgba(201,168,76,.2);
  border-radius: 18px;
  padding: 1rem 1.3rem;
  box-shadow: 0 12px 40px rgba(122,31,46,.12);
  z-index: 5;
  animation: floatCard 4s ease-in-out infinite;
}
@keyframes floatCard {
  0%,100% { transform: translateY(0); }
  50%      { transform: translateY(-7px); }
}
.unggulan-stat .stat-num {
  font-family: 'Cormorant Garamond', serif;
  font-size: 2rem;
  font-weight: 600;
  color: var(--rose, #c0485a);
  line-height: 1;
}
.unggulan-stat .stat-label {
  font-family: 'Jost', sans-serif;
  font-size: .65rem;
  letter-spacing: .1em;
  text-transform: uppercase;
  color: #9a7070;
  margin-top: .15rem;
}

/* SVG floral decoration */
.unggulan-floral {
  position: absolute;
  top: -30px; right: -20px;
  width: 130px;
  opacity: .15;
  pointer-events: none;
  z-index: 0;
  animation: spinSlow 40s linear infinite;
}

/* ══════════════════════════
   RIGHT — KEUNGGULAN LIST
══════════════════════════ */
.unggulan-right {
  animation: fadeUp .9s .15s ease both;
}

.unggulan-eyebrow {
  display: inline-flex;
  align-items: center;
  gap: .6rem;
  font-family: 'Jost', sans-serif;
  font-size: .7rem;
  font-weight: 500;
  letter-spacing: .18em;
  text-transform: uppercase;
  color: var(--sage, #7d9b76);
  margin-bottom: 1rem;
}
.unggulan-eyebrow::before {
  content: '';
  display: block;
  width: 28px; height: 1px;
  background: var(--sage, #7d9b76);
  opacity: .5;
}

.unggulan-title {
  font-family: 'Cormorant Garamond', serif;
  font-size: clamp(1.9rem, 3.5vw, 2.8rem);
  font-weight: 300;
  color: #2a1018;
  line-height: 1.15;
  margin-bottom: .7rem;
}
.unggulan-title em { font-style: italic; color: var(--rose, #c0485a); }

.unggulan-lead {
  font-family: 'Jost', sans-serif;
  font-size: .9rem;
  color: #9a7070;
  line-height: 1.8;
  margin-bottom: 2.5rem;
  max-width: 420px;
}

/* Feature item */
.unggulan-list {
  display: flex;
  flex-direction: column;
  gap: 1.4rem;
}

.unggulan-item {
  display: flex;
  gap: 1.1rem;
  align-items: flex-start;
  padding: 1.1rem 1.2rem;
  border-radius: 16px;
  background: rgba(255,255,255,.7);
  border: 1px solid rgba(192,72,90,.07);
  box-shadow: 0 2px 12px rgba(122,31,46,.04);
  transition: transform .3s ease, box-shadow .3s ease, border-color .2s, background .2s;
  cursor: default;
}
.unggulan-item:hover {
  transform: translateX(6px);
  box-shadow: 0 8px 28px rgba(122,31,46,.09);
  border-color: rgba(192,72,90,.15);
  background: #fff;
}

/* Icon circle */
.unggulan-icon-wrap {
  flex-shrink: 0;
  width: 48px; height: 48px;
  border-radius: 14px;
  display: flex;
  align-items: center;
  justify-content: center;
  background: linear-gradient(135deg, #f2d4d7, #fdf6ee);
  border: 1px solid rgba(192,72,90,.12);
  transition: background .3s, transform .3s;
}
.unggulan-item:hover .unggulan-icon-wrap {
  background: linear-gradient(135deg, var(--rose, #c0485a), #a03248);
  transform: rotate(-5deg) scale(1.08);
}
.unggulan-icon-wrap svg { transition: filter .3s; }
.unggulan-item:hover .unggulan-icon-wrap svg { filter: brightness(0) invert(1); }
.unggulan-item-title {
  font-family: 'Cormorant Garamond', serif;
  font-size: 1.05rem;
  font-weight: 600;
  color: #2a1018;
  margin-bottom: .2rem;
  transition: color .2s;
}
.unggulan-item:hover .unggulan-item-title { color: var(--rose, #c0485a); }
.unggulan-item-desc {
  font-family: 'Jost', sans-serif;
  font-size: .78rem;
  color: #9a7070;
  line-height: 1.65;
}
</style>

<section class="unggulan-section">
  <div class="unggulan-bg"></div>

  <div class="unggulan-inner">

    <!-- ═══ LEFT: FOTO ═══ -->
    <div class="unggulan-photo">

      <!-- Decorative gold border -->
      <div class="unggulan-photo-border"></div>

      <!-- Rotating floral SVG -->
      <svg class="unggulan-floral" viewBox="0 0 130 130" fill="none" xmlns="http://www.w3.org/2000/svg">
        <?php foreach (range(0,7) as $i): ?>
        <?php $angle = $i * 45; ?>
        <ellipse cx="65" cy="65" rx="8" ry="24"
          fill="#c0485a"
          transform="rotate(<?= $angle ?> 65 65) translate(0 -20)"/>
        <?php endforeach; ?>
        <circle cx="65" cy="65" r="14" fill="#fdf6ee"/>
        <circle cx="65" cy="65" r="7"  fill="#c9a84c"/>
        <?php foreach (range(0,7) as $i): ?>
        <?php $angle = $i * 45 + 22.5; ?>
        <ellipse cx="65" cy="65" rx="5" ry="16"
          fill="#f2d4d7"
          transform="rotate(<?= $angle ?> 65 65) translate(0 -20)"/>
        <?php endforeach; ?>
      </svg>

      <!-- Main photo -->
      <div class="unggulan-photo-main">
        <img src="<?= BASE_URL ?>/assets/images/1a.jpg"
             alt="Chika Florist – Bunga Segar Berkualitas"
             onerror="this.parentElement.style.background='linear-gradient(135deg,#f2d4d7,#f0f4ed)';this.style.display='none'">
        <div class="unggulan-photo-caption">
          <p>"Setiap bunga punya cerita,<br>biarkan kami yang menceritakannya."</p>
          <span>— Chika Florist</span>
        </div>
      </div>

      <!-- Mini circle photo -->
      <div class="unggulan-photo-mini">
        <img src="<?= BASE_URL ?>/assets/images/1a.jpg"
             alt="Detail Bunga Chika Florist"
             onerror="this.parentElement.style.background='linear-gradient(135deg,#f2d4d7,#f0f4ed)';this.style.display='none'">
      </div>

      <!-- Floating stat -->
      <div class="unggulan-stat">
        <div class="stat-num">500+</div>
        <div class="stat-label">Pelanggan Puas<br>Setiap Bulan</div>
      </div>

    </div>

    <!-- ═══ RIGHT: KEUNGGULAN ═══ -->
    <div class="unggulan-right">

      <div class="unggulan-eyebrow">Mengapa Kami</div>
      <h2 class="unggulan-title">
        Kenapa Memilih<br>
        <em>Chika Florist?</em>
      </h2>
      <p class="unggulan-lead">
        Lebih dari sekadar toko bunga — kami adalah mitra terpercaya untuk setiap momen berharga dalam hidup Anda.
      </p>

      <div class="unggulan-list">
        <?php
        $keunggulan = [
          [
            'title' => 'Layanan 24 Jam',
            'desc'  => 'Pemesanan tersedia kapan saja, termasuk malam hari dan hari libur nasional.',
            'svg'   => '<svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#c0485a" stroke-width="1.8" stroke-linecap="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>',
          ],
          [
            'title' => 'Same Day Delivery',
            'desc'  => 'Pengiriman di hari yang sama untuk banyak kota besar di seluruh Indonesia.',
            'svg'   => '<svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#c0485a" stroke-width="1.8" stroke-linecap="round"><path d="M5 17H3a2 2 0 01-2-2V5a2 2 0 012-2h11a2 2 0 012 2v3"/><rect x="9" y="11" width="14" height="10" rx="2"/><circle cx="12" cy="21" r="1"/><circle cx="20" cy="21" r="1"/></svg>',
          ],
          [
            'title' => 'Bunga Fresh Berkualitas',
            'desc'  => 'Setiap rangkaian dibuat dari bunga segar pilihan dengan desain elegan dan tahan lama.',
            'svg'   => '<svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#c0485a" stroke-width="1.8" stroke-linecap="round"><path d="M12 22V12"/><path d="M12 12C12 12 7 9 7 5a5 5 0 0110 0c0 4-5 7-5 7z"/><path d="M12 12C12 12 17 9 17 5"/><path d="M5 22h14"/></svg>',
          ],
          [
            'title' => 'Admin Responsif',
            'desc'  => 'Tim admin siap membantu melalui WhatsApp dengan cepat, ramah, dan profesional.',
            'svg'   => '<svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#c0485a" stroke-width="1.8" stroke-linecap="round"><path d="M21 15a2 2 0 01-2 2H7l-4 4V5a2 2 0 012-2h14a2 2 0 012 2z"/></svg>',
          ],
          [
            'title' => 'Harga Transparan',
            'desc'  => 'Tidak ada biaya tersembunyi. Semua harga diinformasikan dengan jelas sejak awal.',
            'svg'   => '<svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#c0485a" stroke-width="1.8" stroke-linecap="round"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 000 7h5a3.5 3.5 0 010 7H6"/></svg>',
          ],
          [
            'title' => 'Desain Custom',
            'desc'  => 'Kami menerima request desain khusus sesuai tema, warna, dan kebutuhan Anda.',
            'svg'   => '<svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#c0485a" stroke-width="1.8" stroke-linecap="round"><path d="M12 20h9"/><path d="M16.5 3.5a2.121 2.121 0 013 3L7 19l-4 1 1-4L16.5 3.5z"/></svg>',
          ],
        ];
        foreach ($keunggulan as $item): ?>
        <div class="unggulan-item">
          <div class="unggulan-icon-wrap">
            <?= $item['svg'] ?>
          </div>
          <div class="unggulan-item-text">
            <div class="unggulan-item-title"><?= $item['title'] ?></div>
            <div class="unggulan-item-desc"><?= $item['desc'] ?></div>
          </div>
        </div>
        <?php endforeach; ?>
      </div>

    </div>
  </div>
</section>