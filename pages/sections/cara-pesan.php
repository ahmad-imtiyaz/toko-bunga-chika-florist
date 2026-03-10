<!-- CARA PESAN - Zigzag + Curved Arrows -->
<style>
.carapesen-section {
  position: relative;
  padding: 6rem 0;
  overflow: hidden;
  background: linear-gradient(160deg, #fdf0f3 0%, #fdf6ee 60%, #f0f4ed 100%);
}

.carapesen-bg {
  position: absolute;
  inset: 0;
  pointer-events: none;
  background-image:
    radial-gradient(circle at 15% 30%, rgba(192,72,90,.06) 0%, transparent 50%),
    radial-gradient(circle at 85% 70%, rgba(125,155,118,.06) 0%, transparent 50%);
}

/* Trellis subtle */
.carapesen-trellis {
  position: absolute;
  inset: 0;
  opacity: .018;
  background-image:
    repeating-linear-gradient(45deg,  #7a1f2e 0, #7a1f2e 1px, transparent 0, transparent 50%),
    repeating-linear-gradient(-45deg, #7a1f2e 0, #7a1f2e 1px, transparent 0, transparent 50%);
  background-size: 32px 32px;
  pointer-events: none;
}

/* ── Header ── */
.carapesen-header {
  position: relative;
  z-index: 2;
  text-align: center;
  margin-bottom: 4rem;
  padding: 0 1.5rem;
}
.carapesen-eyebrow {
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
.carapesen-eyebrow::before,
.carapesen-eyebrow::after {
  content: '';
  display: block;
  width: 32px; height: 1px;
  background: var(--rose, #c0485a);
  opacity: .4;
}
.carapesen-title {
  font-family: 'Cormorant Garamond', serif;
  font-size: clamp(2rem, 4vw, 3rem);
  font-weight: 300;
  color: #2a1018;
  line-height: 1.15;
  margin-bottom: .5rem;
}
.carapesen-title em { font-style: italic; color: var(--rose, #c0485a); }
.carapesen-subtitle {
  font-family: 'Jost', sans-serif;
  font-size: .9rem;
  color: #9a7070;
}

/* ── Zigzag container ── */
.carapesen-zz {
  position: relative;
  z-index: 2;
  max-width: 860px;
  margin: 0 auto;
  padding: 0 2rem;
  display: flex;
  flex-direction: column;
  gap: 0;
}

/* Each row: step + arrow */
.carapesen-row {
  display: grid;
  grid-template-columns: 1fr 80px 1fr;
  align-items: center;
  min-height: 160px;
}

/* Odd rows: step left, arrow, empty right */
.carapesen-row.odd  .carapesen-step  { grid-column: 1; }
.carapesen-row.odd  .carapesen-arrow { grid-column: 2; }
.carapesen-row.odd  .carapesen-empty { grid-column: 3; }

/* Even rows: empty left, arrow, step right */
.carapesen-row.even .carapesen-empty { grid-column: 1; }
.carapesen-row.even .carapesen-arrow { grid-column: 2; }
.carapesen-row.even .carapesen-step  { grid-column: 3; }

/* Last row — no arrow */
.carapesen-row.last {
  grid-template-columns: 1fr;
  justify-items: center;
  min-height: auto;
  margin-top: .5rem;
}

/* ── Step card ── */
.carapesen-step {
  background: rgba(255,255,255,.88);
  backdrop-filter: blur(10px);
  border: 1px solid rgba(192,72,90,.1);
  border-radius: 24px;
  padding: 1.6rem 1.5rem;
  box-shadow: 0 4px 24px rgba(122,31,46,.07);
  display: flex;
  flex-direction: column;
  gap: .8rem;
  transition: transform .3s ease, box-shadow .3s ease, border-color .2s;
  animation: stepReveal .6s ease both;
  position: relative;
  overflow: hidden;
}
.carapesen-step:hover {
  transform: translateY(-5px);
  box-shadow: 0 16px 40px rgba(122,31,46,.12);
  border-color: rgba(192,72,90,.22);
}

/* Decorative number watermark */
.carapesen-step::before {
  content: attr(data-num);
  position: absolute;
  bottom: -10px; right: 10px;
  font-family: 'Cormorant Garamond', serif;
  font-size: 5.5rem;
  font-weight: 700;
  color: rgba(192,72,90,.05);
  line-height: 1;
  pointer-events: none;
  user-select: none;
}

@keyframes stepReveal {
  from { opacity:0; transform: translateY(20px); }
  to   { opacity:1; transform: translateY(0); }
}
.carapesen-row:nth-child(1) .carapesen-step { animation-delay: .1s; }
.carapesen-row:nth-child(2) .carapesen-step { animation-delay: .25s; }
.carapesen-row:nth-child(3) .carapesen-step { animation-delay: .4s; }
.carapesen-row:nth-child(4) .carapesen-step { animation-delay: .55s; }

/* Step top: icon + badge */
.carapesen-step-top {
  display: flex;
  align-items: center;
  gap: .8rem;
}

/* Icon wrap */
.carapesen-icon-wrap {
  width: 48px; height: 48px;
  border-radius: 14px;
  background: linear-gradient(135deg, #f2d4d7, #fdf6ee);
  border: 1px solid rgba(192,72,90,.12);
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
  transition: background .3s, transform .3s;
}
.carapesen-step:hover .carapesen-icon-wrap {
  background: linear-gradient(135deg, var(--rose, #c0485a), #a03248);
  transform: rotate(-6deg) scale(1.08);
}
.carapesen-step:hover .carapesen-icon-wrap svg { filter: brightness(0) invert(1); }

/* Step number badge */
.carapesen-num {
  font-family: 'Cormorant Garamond', serif;
  font-size: .75rem;
  font-weight: 600;
  color: var(--rose, #c0485a);
  background: rgba(192,72,90,.08);
  border: 1px solid rgba(192,72,90,.15);
  border-radius: 999px;
  padding: .15rem .55rem;
  letter-spacing: .04em;
}

/* Step title */
.carapesen-step-title {
  font-family: 'Cormorant Garamond', serif;
  font-size: 1.15rem;
  font-weight: 600;
  color: #2a1018;
  line-height: 1.2;
  transition: color .2s;
}
.carapesen-step:hover .carapesen-step-title { color: var(--rose, #c0485a); }

/* Step desc */
.carapesen-step-desc {
  font-family: 'Jost', sans-serif;
  font-size: .78rem;
  color: #9a7070;
  line-height: 1.7;
}

/* ── Curved Arrow SVG ── */
.carapesen-arrow {
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 0 .5rem;
}
.carapesen-arrow svg {
  width: 64px;
  overflow: visible;
  animation: arrowPulse 2.5s ease-in-out infinite;
}
@keyframes arrowPulse {
  0%,100% { transform: translateX(0) translateY(0); opacity:.7; }
  50%      { transform: translateX(3px) translateY(-2px); opacity:1; }
}

/* ── CTA row (last) ── */
.carapesen-cta-wrap {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 1.2rem;
  margin-top: 3rem;
  position: relative;
  z-index: 2;
}

/* Down arrow to CTA */
.carapesen-down-arrow {
  animation: bounceDown 1.5s ease-in-out infinite;
  color: rgba(192,72,90,.35);
}
@keyframes bounceDown {
  0%,100% { transform: translateY(0); }
  50%      { transform: translateY(6px); }
}

.carapesen-btn {
  display: inline-flex;
  align-items: center;
  gap: .6rem;
  font-family: 'Jost', sans-serif;
  font-size: .88rem;
  font-weight: 600;
  color: #fff;
  background: linear-gradient(135deg, #25d366, #128c50);
  padding: 1rem 2.4rem;
  border-radius: 999px;
  text-decoration: none;
  box-shadow: 0 10px 30px rgba(18,140,80,.28);
  transition: transform .25s, box-shadow .25s;
}
.carapesen-btn:hover {
  transform: translateY(-3px);
  box-shadow: 0 16px 40px rgba(18,140,80,.38);
}

/* ── MOBILE: vertikal simple ── */
@media (max-width: 700px) {
  .carapesen-row {
    grid-template-columns: 1fr;
    min-height: auto;
    gap: 0;
  }
  .carapesen-row.odd .carapesen-step,
  .carapesen-row.even .carapesen-step { grid-column: 1; grid-row: 1; }
  .carapesen-row.odd .carapesen-arrow,
  .carapesen-row.even .carapesen-arrow { grid-column: 1; grid-row: 2; justify-content: center; padding: .3rem 0; }
  .carapesen-empty { display: none; }

  .carapesen-arrow svg {
    transform: rotate(90deg);
    width: 48px;
  }
  @keyframes arrowPulse {
    0%,100% { transform: rotate(90deg) translateX(0); opacity:.7; }
    50%      { transform: rotate(90deg) translateX(4px); opacity:1; }
  }
}
</style>

<section class="carapesen-section">
  <div class="carapesen-bg"></div>
  <div class="carapesen-trellis"></div>

  <!-- Header -->
  <div class="carapesen-header">
    <div class="carapesen-eyebrow">✿ Mudah & Cepat ✿</div>
    <h2 class="carapesen-title">Cara Pesan <em>Bunga Online</em></h2>
    <p class="carapesen-subtitle">Hanya 4 langkah — bunga tiba di tangan Anda</p>
  </div>

  <!-- Zigzag Steps -->
  <div class="carapesen-zz">

    <?php
    $steps = [
      [
        'num'   => '01',
        'title' => 'Pilih Produk',
        'desc'  => 'Telusuri koleksi bunga kami dan pilih rangkaian yang paling sesuai momen Anda.',
        'icon'  => '<svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#c0485a" stroke-width="1.7" stroke-linecap="round"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>',
      ],
      [
        'num'   => '02',
        'title' => 'Hubungi WhatsApp',
        'desc'  => 'Klik tombol WhatsApp dan admin kami siap menyambut Anda dalam hitungan menit.',
        'icon'  => '<svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#c0485a" stroke-width="1.7" stroke-linecap="round"><path d="M21 15a2 2 0 01-2 2H7l-4 4V5a2 2 0 012-2h14a2 2 0 012 2z"/></svg>',
      ],
      [
        'num'   => '03',
        'title' => 'Kirim Detail',
        'desc'  => 'Beritahu ucapan, alamat lengkap, dan waktu pengiriman yang Anda inginkan.',
        'icon'  => '<svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#c0485a" stroke-width="1.7" stroke-linecap="round"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>',
      ],
      [
        'num'   => '04',
        'title' => 'Bunga Dikirim',
        'desc'  => 'Pesanan dikemas dengan indah dan dikirim tepat waktu ke tujuan Anda.',
        'icon'  => '<svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#c0485a" stroke-width="1.7" stroke-linecap="round"><path d="M5 17H3a2 2 0 01-2-2V5a2 2 0 012-2h11a2 2 0 012 2v3"/><rect x="9" y="11" width="14" height="10" rx="2"/><circle cx="12" cy="21" r="1"/><circle cx="20" cy="21" r="1"/></svg>',
      ],
    ];
    ?>

    <!-- Step 1 (odd — left) -->
    <div class="carapesen-row odd">
      <div class="carapesen-step" data-num="<?= $steps[0]['num'] ?>">
        <div class="carapesen-step-top">
          <div class="carapesen-icon-wrap"><?= $steps[0]['icon'] ?></div>
          <span class="carapesen-num">Langkah <?= $steps[0]['num'] ?></span>
        </div>
        <div class="carapesen-step-title"><?= $steps[0]['title'] ?></div>
        <div class="carapesen-step-desc"><?= $steps[0]['desc'] ?></div>
      </div>

      <!-- Curved arrow: right-down → left -->
      <div class="carapesen-arrow">
        <svg viewBox="0 0 64 120" fill="none" xmlns="http://www.w3.org/2000/svg">
          <path d="M10 10 Q54 10 54 60 Q54 110 10 110"
                stroke="#c0485a" stroke-width="1.8" stroke-dasharray="5 4"
                fill="none" stroke-linecap="round"/>
          <polyline points="16,100 8,112 20,116"
                    stroke="#c0485a" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
      </div>
      <div class="carapesen-empty"></div>
    </div>

    <!-- Step 2 (even — right) -->
    <div class="carapesen-row even">
      <div class="carapesen-empty"></div>

      <!-- Curved arrow: left-down → right -->
      <div class="carapesen-arrow">
        <svg viewBox="0 0 64 120" fill="none" xmlns="http://www.w3.org/2000/svg">
          <path d="M54 10 Q10 10 10 60 Q10 110 54 110"
                stroke="#c0485a" stroke-width="1.8" stroke-dasharray="5 4"
                fill="none" stroke-linecap="round"/>
          <polyline points="48,100 56,112 44,116"
                    stroke="#c0485a" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
      </div>

      <div class="carapesen-step" data-num="<?= $steps[1]['num'] ?>">
        <div class="carapesen-step-top">
          <div class="carapesen-icon-wrap"><?= $steps[1]['icon'] ?></div>
          <span class="carapesen-num">Langkah <?= $steps[1]['num'] ?></span>
        </div>
        <div class="carapesen-step-title"><?= $steps[1]['title'] ?></div>
        <div class="carapesen-step-desc"><?= $steps[1]['desc'] ?></div>
      </div>
    </div>

    <!-- Step 3 (odd — left) -->
    <div class="carapesen-row odd">
      <div class="carapesen-step" data-num="<?= $steps[2]['num'] ?>">
        <div class="carapesen-step-top">
          <div class="carapesen-icon-wrap"><?= $steps[2]['icon'] ?></div>
          <span class="carapesen-num">Langkah <?= $steps[2]['num'] ?></span>
        </div>
        <div class="carapesen-step-title"><?= $steps[2]['title'] ?></div>
        <div class="carapesen-step-desc"><?= $steps[2]['desc'] ?></div>
      </div>

      <!-- Curved arrow: right-down → left -->
      <div class="carapesen-arrow">
        <svg viewBox="0 0 64 120" fill="none" xmlns="http://www.w3.org/2000/svg">
          <path d="M10 10 Q54 10 54 60 Q54 110 10 110"
                stroke="#c0485a" stroke-width="1.8" stroke-dasharray="5 4"
                fill="none" stroke-linecap="round"/>
          <polyline points="16,100 8,112 20,116"
                    stroke="#c0485a" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
      </div>
      <div class="carapesen-empty"></div>
    </div>

    <!-- Step 4 (even — right / last) -->
    <div class="carapesen-row even">
      <div class="carapesen-empty"></div>
      <div class="carapesen-arrow">
        <!-- small down arrow only -->
        <svg viewBox="0 0 64 60" fill="none" xmlns="http://www.w3.org/2000/svg">
          <path d="M54 10 Q10 10 10 30 Q10 50 32 55"
                stroke="#c0485a" stroke-width="1.8" stroke-dasharray="5 4"
                fill="none" stroke-linecap="round"/>
          <polyline points="26,50 32,58 38,50"
                    stroke="#c0485a" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
      </div>
      <div class="carapesen-step" data-num="<?= $steps[3]['num'] ?>">
        <div class="carapesen-step-top">
          <div class="carapesen-icon-wrap"><?= $steps[3]['icon'] ?></div>
          <span class="carapesen-num">Langkah <?= $steps[3]['num'] ?></span>
        </div>
        <div class="carapesen-step-title"><?= $steps[3]['title'] ?></div>
        <div class="carapesen-step-desc"><?= $steps[3]['desc'] ?></div>
      </div>
    </div>

  </div>

  <!-- CTA -->
  <div class="carapesen-cta-wrap">
    <div class="carapesen-down-arrow">
      <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
        <path d="M12 5v14M5 12l7 7 7-7"/>
      </svg>
    </div>
    <a href="<?= waLink() ?>" target="_blank" class="carapesen-btn">
      <svg width="18" height="18" fill="currentColor" viewBox="0 0 24 24">
        <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z"/>
        <path d="M12 0C5.373 0 0 5.373 0 12c0 2.096.541 4.063 1.491 5.776L.057 23.04l5.43-1.424A11.947 11.947 0 0012 24c6.627 0 12-5.373 12-12S18.627 0 12 0zm0 21.818a9.818 9.818 0 01-5.015-1.371l-.36-.214-3.726.977.995-3.633-.235-.374A9.818 9.818 0 1112 21.818z"/>
      </svg>
      Pesan Sekarang via WhatsApp
    </a>
  </div>

</section>