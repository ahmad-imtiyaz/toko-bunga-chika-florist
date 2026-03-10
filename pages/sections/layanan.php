<!-- LAYANAN ACARA - Grid Card Elegan -->
<style>
.layanan-section {
  position: relative;
  padding: 6rem 0;
  overflow: hidden;
  background: linear-gradient(160deg, #f0f4ed 0%, #fdf6ee 50%, #fdf0f3 100%);
}

.layanan-bg {
  position: absolute;
  inset: 0;
  pointer-events: none;
  background-image:
    radial-gradient(circle at 10% 20%, rgba(125,155,118,.07) 0%, transparent 45%),
    radial-gradient(circle at 90% 80%, rgba(192,72,90,.06) 0%, transparent 45%);
}

/* Subtle dot pattern */
.layanan-dots-bg {
  position: absolute;
  inset: 0;
  background-image: radial-gradient(circle, rgba(192,72,90,.08) 1px, transparent 1px);
  background-size: 32px 32px;
  mask-image: radial-gradient(ellipse 80% 80% at 50% 50%, black 20%, transparent 100%);
  pointer-events: none;
}

/* ── Header ── */
.layanan-header {
  position: relative;
  z-index: 2;
  text-align: center;
  margin-bottom: 3.5rem;
  padding: 0 1.5rem;
}
.layanan-eyebrow {
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
.layanan-eyebrow::before,
.layanan-eyebrow::after {
  content: '';
  display: block;
  width: 32px; height: 1px;
  background: var(--sage, #7d9b76);
  opacity: .5;
}
.layanan-title {
  font-family: 'Cormorant Garamond', serif;
  font-size: clamp(2rem, 4vw, 3rem);
  font-weight: 300;
  color: #2a1018;
  line-height: 1.15;
  margin-bottom: .6rem;
}
.layanan-title em { font-style: italic; color: var(--rose, #c0485a); }
.layanan-subtitle {
  font-family: 'Jost', sans-serif;
  font-size: .9rem;
  color: #9a7070;
}

/* ── Grid ── */
.layanan-grid {
  position: relative;
  z-index: 2;
  max-width: 1280px;
  margin: 0 auto;
  padding: 0 2rem;
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  gap: 1.2rem;
}
@media (min-width: 640px)  { .layanan-grid { grid-template-columns: repeat(3, 1fr); gap: 1.4rem; } }
@media (min-width: 1024px) { .layanan-grid { grid-template-columns: repeat(4, 1fr); gap: 1.5rem; } }

/* ── Card ── */
.layanan-card {
  position: relative;
  border-radius: 22px;
  overflow: hidden;
  border: 1px solid rgba(255,255,255,.6);
  box-shadow: 0 4px 20px rgba(122,31,46,.07);
  cursor: pointer;
  transition: transform .35s cubic-bezier(.22,1,.36,1),
              box-shadow .35s ease;
  animation: layananReveal .55s ease both;
  text-decoration: none;
  display: block;
}
.layanan-card:hover {
  transform: translateY(-7px) scale(1.02);
  box-shadow: 0 20px 50px rgba(122,31,46,.15);
}

@keyframes layananReveal {
  from { opacity:0; transform: translateY(24px); }
  to   { opacity:1; transform: translateY(0); }
}
.layanan-card:nth-child(1) { animation-delay:.05s; }
.layanan-card:nth-child(2) { animation-delay:.10s; }
.layanan-card:nth-child(3) { animation-delay:.15s; }
.layanan-card:nth-child(4) { animation-delay:.20s; }
.layanan-card:nth-child(5) { animation-delay:.25s; }
.layanan-card:nth-child(6) { animation-delay:.30s; }
.layanan-card:nth-child(7) { animation-delay:.35s; }
.layanan-card:nth-child(8) { animation-delay:.40s; }

/* Gradient background per card */
.layanan-card-inner {
  padding: 1.8rem 1.4rem 1.5rem;
  height: 100%;
  display: flex;
  flex-direction: column;
  gap: .9rem;
  position: relative;
  z-index: 1;
}

/* Per-event gradient */
.layanan-card[data-event="wedding"]     { background: linear-gradient(145deg, #fff0f5, #ffe4ef); }
.layanan-card[data-event="duka"]        { background: linear-gradient(145deg, #f0f0f8, #e8e8f5); }
.layanan-card[data-event="ultah"]       { background: linear-gradient(145deg, #fff8e8, #fff0d0); }
.layanan-card[data-event="wisuda"]      { background: linear-gradient(145deg, #f0f8f0, #e0f2e0); }
.layanan-card[data-event="opening"]     { background: linear-gradient(145deg, #fff5e8, #ffe8d0); }
.layanan-card[data-event="anniversary"] { background: linear-gradient(145deg, #fdf0f8, #f8e0f0); }
.layanan-card[data-event="corporate"]   { background: linear-gradient(145deg, #f0f4f8, #e4ecf5); }
.layanan-card[data-event="hariraya"]    { background: linear-gradient(145deg, #f8f5e8, #f5ecd0); }

/* Icon circle */
.layanan-icon {
  width: 52px; height: 52px;
  border-radius: 16px;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
  transition: transform .3s ease;
}
.layanan-card:hover .layanan-icon { transform: scale(1.12) rotate(-5deg); }

.layanan-card[data-event="wedding"]     .layanan-icon { background: rgba(192,72,90,.12);  }
.layanan-card[data-event="duka"]        .layanan-icon { background: rgba(100,100,160,.12); }
.layanan-card[data-event="ultah"]       .layanan-icon { background: rgba(201,168,76,.15); }
.layanan-card[data-event="wisuda"]      .layanan-icon { background: rgba(125,155,118,.15); }
.layanan-card[data-event="opening"]     .layanan-icon { background: rgba(220,130,50,.12); }
.layanan-card[data-event="anniversary"] .layanan-icon { background: rgba(180,60,140,.12); }
.layanan-card[data-event="corporate"]   .layanan-icon { background: rgba(60,100,180,.12); }
.layanan-card[data-event="hariraya"]    .layanan-icon { background: rgba(180,150,40,.15); }

/* Card text */
.layanan-card-name {
  font-family: 'Cormorant Garamond', serif;
  font-size: 1.05rem;
  font-weight: 600;
  color: #2a1018;
  line-height: 1.25;
  transition: color .2s;
}
.layanan-card:hover .layanan-card-name { color: var(--rose, #c0485a); }

.layanan-card-desc {
  font-family: 'Jost', sans-serif;
  font-size: .73rem;
  color: #9a7070;
  line-height: 1.6;
  flex: 1;
}

/* Arrow */
.layanan-card-arrow {
  display: inline-flex;
  align-items: center;
  gap: .3rem;
  font-family: 'Jost', sans-serif;
  font-size: .68rem;
  font-weight: 600;
  letter-spacing: .06em;
  text-transform: uppercase;
  color: var(--rose, #c0485a);
  opacity: 0;
  transform: translateY(4px);
  transition: opacity .25s, transform .25s;
}
.layanan-card:hover .layanan-card-arrow {
  opacity: 1;
  transform: translateY(0);
}
.layanan-card-arrow svg { transition: transform .2s; }
.layanan-card:hover .layanan-card-arrow svg { transform: translateX(3px); }

/* Decorative blob per card */
.layanan-card-blob {
  position: absolute;
  bottom: -20px; right: -20px;
  width: 90px; height: 90px;
  border-radius: 50%;
  opacity: .12;
  transition: transform .4s ease, opacity .3s;
  pointer-events: none;
}
.layanan-card:hover .layanan-card-blob {
  transform: scale(1.4);
  opacity: .18;
}
.layanan-card[data-event="wedding"]     .layanan-card-blob { background: #c0485a; }
.layanan-card[data-event="duka"]        .layanan-card-blob { background: #6464a0; }
.layanan-card[data-event="ultah"]       .layanan-card-blob { background: #c9a84c; }
.layanan-card[data-event="wisuda"]      .layanan-card-blob { background: #7d9b76; }
.layanan-card[data-event="opening"]     .layanan-card-blob { background: #dc8232; }
.layanan-card[data-event="anniversary"] .layanan-card-blob { background: #b43c8c; }
.layanan-card[data-event="corporate"]   .layanan-card-blob { background: #3c64b4; }
.layanan-card[data-event="hariraya"]    .layanan-card-blob { background: #b49628; }

/* ── CTA ── */
.layanan-cta {
  position: relative;
  z-index: 2;
  text-align: center;
  margin-top: 3rem;
  padding: 0 1.5rem;
}
.layanan-cta-divider {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 1rem;
  margin-bottom: 1.5rem;
}
.layanan-cta-divider::before,
.layanan-cta-divider::after {
  content: '';
  flex: 1;
  max-width: 100px;
  height: 1px;
  background: linear-gradient(90deg, transparent, rgba(201,168,76,.3), transparent);
}
.layanan-cta-divider span {
  font-size: .9rem;
  color: rgba(201,168,76,.5);
  letter-spacing: .2em;
}
.layanan-btn {
  display: inline-flex;
  align-items: center;
  gap: .6rem;
  font-family: 'Jost', sans-serif;
  font-size: .85rem;
  font-weight: 600;
  letter-spacing: .04em;
  color: #fff;
  background: linear-gradient(135deg, var(--rose, #c0485a), #a03248);
  padding: .9rem 2.2rem;
  border-radius: 999px;
  text-decoration: none;
  box-shadow: 0 8px 24px rgba(192,72,90,.3);
  transition: transform .25s, box-shadow .25s;
}
.layanan-btn:hover {
  transform: translateY(-3px);
  box-shadow: 0 14px 36px rgba(192,72,90,.4);
}
.layanan-btn svg { transition: transform .2s; }
.layanan-btn:hover svg { transform: translateX(4px); }
</style>

<section class="layanan-section">
  <div class="layanan-bg"></div>
  <div class="layanan-dots-bg"></div>

  <!-- Header -->
  <div class="layanan-header">
    <div class="layanan-eyebrow">✦ Untuk Setiap Momen ✦</div>
    <h2 class="layanan-title">Bunga untuk Setiap<br><em>Perayaan Spesial</em></h2>
    <p class="layanan-subtitle">Dari kebun kami, untuk momen paling berharga dalam hidupmu</p>
  </div>

  <!-- Grid -->
  <div class="layanan-grid">

    <?php
    $acara = [
      [
        'event' => 'wedding',
        'name'  => 'Wedding & Lamaran',
        'desc'  => 'Rangkaian bunga romantis untuk hari sakral pernikahan dan lamaran.',
        'icon'  => '<svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="#c0485a" stroke-width="1.6" stroke-linecap="round"><path d="M20.84 4.61a5.5 5.5 0 00-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 00-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 000-7.78z"/></svg>',
      ],
      [
        'event' => 'duka',
        'name'  => 'Duka Cita',
        'desc'  => 'Bunga belasungkawa yang penuh hormat untuk menyampaikan rasa empati.',
        'icon'  => '<svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="#6464a0" stroke-width="1.6" stroke-linecap="round"><path d="M12 22V12"/><path d="M12 12C12 12 7 9 7 5a5 5 0 0110 0c0 4-5 7-5 7z"/><path d="M5 22h14"/></svg>',
      ],
      [
        'event' => 'ultah',
        'name'  => 'Ulang Tahun',
        'desc'  => 'Kejutkan orang tersayang dengan rangkaian bunga segar penuh warna.',
        'icon'  => '<svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="#c9a84c" stroke-width="1.6" stroke-linecap="round"><path d="M20 7H4a2 2 0 00-2 2v10a2 2 0 002 2h16a2 2 0 002-2V9a2 2 0 00-2-2z"/><path d="M16 7V5a2 2 0 00-2-2h-4a2 2 0 00-2 2v2"/><line x1="12" y1="12" x2="12" y2="16"/><line x1="10" y1="14" x2="14" y2="14"/></svg>',
      ],
      [
        'event' => 'wisuda',
        'name'  => 'Wisuda',
        'desc'  => 'Rayakan pencapaian akademis dengan buket bunga yang membanggakan.',
        'icon'  => '<svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="#7d9b76" stroke-width="1.6" stroke-linecap="round"><path d="M22 10v6M2 10l10-5 10 5-10 5z"/><path d="M6 12v5c3 3 9 3 12 0v-5"/></svg>',
      ],
      [
        'event' => 'opening',
        'name'  => 'Grand Opening',
        'desc'  => 'Standing flower & papan bunga mewah untuk pembukaan usaha baru.',
        'icon'  => '<svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="#dc8232" stroke-width="1.6" stroke-linecap="round"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>',
      ],
      [
        'event' => 'anniversary',
        'name'  => 'Anniversary',
        'desc'  => 'Ungkapkan cinta abadi dengan rangkaian bunga elegan di hari jadi.',
        'icon'  => '<svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="#b43c8c" stroke-width="1.6" stroke-linecap="round"><circle cx="12" cy="12" r="10"/><path d="M8 14s1.5 2 4 2 4-2 4-2"/><line x1="9" y1="9" x2="9.01" y2="9"/><line x1="15" y1="9" x2="15.01" y2="9"/></svg>',
      ],
      [
        'event' => 'corporate',
        'name'  => 'Corporate Event',
        'desc'  => 'Dekorasi bunga profesional untuk acara kantor, seminar, dan gala dinner.',
        'icon'  => '<svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="#3c64b4" stroke-width="1.6" stroke-linecap="round"><rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 21V5a2 2 0 00-2-2h-4a2 2 0 00-2 2v16"/></svg>',
      ],
      [
        'event' => 'hariraya',
        'name'  => 'Hari Raya',
        'desc'  => 'Bunga spesial bernuansa hangat untuk merayakan hari besar keagamaan.',
        'icon'  => '<svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="#b49628" stroke-width="1.6" stroke-linecap="round"><circle cx="12" cy="12" r="5"/><line x1="12" y1="1" x2="12" y2="3"/><line x1="12" y1="21" x2="12" y2="23"/><line x1="4.22" y1="4.22" x2="5.64" y2="5.64"/><line x1="18.36" y1="18.36" x2="19.78" y2="19.78"/><line x1="1" y1="12" x2="3" y2="12"/><line x1="21" y1="12" x2="23" y2="12"/><line x1="4.22" y1="19.78" x2="5.64" y2="18.36"/><line x1="18.36" y1="5.64" x2="19.78" y2="4.22"/></svg>',
      ],
    ];
    foreach ($acara as $item): ?>

    <a href="<?= waLink('Halo Chika Florist, saya ingin pesan bunga untuk ' . $item['name']) ?>"
       target="_blank"
       class="layanan-card"
       data-event="<?= $item['event'] ?>">

      <div class="layanan-card-blob"></div>

      <div class="layanan-card-inner">
        <div class="layanan-icon"><?= $item['icon'] ?></div>
        <div>
          <div class="layanan-card-name"><?= $item['name'] ?></div>
        </div>
        <div class="layanan-card-desc"><?= $item['desc'] ?></div>
        <div class="layanan-card-arrow">
          Pesan
          <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
            <path d="M5 12h14M12 5l7 7-7 7"/>
          </svg>
        </div>
      </div>
    </a>

    <?php endforeach; ?>
  </div>

  <!-- CTA -->
  <div class="layanan-cta">
    <div class="layanan-cta-divider"><span>✿</span></div>
    <a href="<?= BASE_URL ?>/toko-bunga-online-24-jam-indonesia" class="layanan-btn">
      Lihat Layanan 24 Jam Lengkap
      <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
        <path d="M5 12h14M12 5l7 7-7 7"/>
      </svg>
    </a>
  </div>

</section>