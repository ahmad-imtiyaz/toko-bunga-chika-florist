<!-- FAQ - Split Layout: Ilustrasi Kiri + Accordion Kanan -->
<style>
.faq-section {
  position: relative;
  padding: 6rem 0;
  overflow: hidden;
  background: linear-gradient(160deg, #f0f4ed 0%, #fdf6ee 50%, #fdf0f3 100%);
}
.faq-bg {
  position: absolute; inset: 0; pointer-events: none;
  background-image:
    radial-gradient(circle at 10% 50%, rgba(125,155,118,.07) 0%, transparent 50%),
    radial-gradient(circle at 90% 30%, rgba(192,72,90,.05) 0%, transparent 45%);
}

/* ── Inner grid ── */
.faq-inner {
  position: relative; z-index: 2;
  max-width: 1200px; margin: 0 auto;
  padding: 0 2rem;
  display: grid;
  grid-template-columns: 420px 1fr;
  gap: 5rem;
  align-items: start;
}
@media (max-width: 960px) {
  .faq-inner { grid-template-columns: 1fr; gap: 3rem; }
  .faq-left  { display: none; } /* hide illustration on small screens */
}

/* ══════════════
   LEFT — ILUSTRASI
══════════════ */
.faq-left {
  position: sticky;
  top: 6rem;
}

.faq-ilus-wrap {
  position: relative;
  padding: 2rem;
}

/* Large decorative flower SVG */
.faq-flower-main {
  display: block;
  margin: 0 auto;
  animation: floatFaq 6s ease-in-out infinite;
  filter: drop-shadow(0 20px 40px rgba(192,72,90,.18));
}
@keyframes floatFaq {
  0%,100% { transform: translateY(0) rotate(0deg); }
  33%      { transform: translateY(-10px) rotate(2deg); }
  66%      { transform: translateY(-5px) rotate(-1.5deg); }
}

/* Orbiting petals */
.faq-orbit {
  position: absolute;
  inset: 0;
  animation: orbitSpin 20s linear infinite;
  pointer-events: none;
}
@keyframes orbitSpin { to { transform: rotate(360deg); } }
.faq-orbit-petal {
  position: absolute;
  width: 18px; height: 28px;
  border-radius: 50%;
  background: linear-gradient(135deg, #f2d4d7, #c0485a);
  opacity: .35;
}

/* Header text on left */
.faq-left-header {
  text-align: center;
  margin-top: 2.5rem;
}
.faq-left-eyebrow {
  display: inline-flex; align-items: center; gap: .5rem;
  font-family: 'Jost', sans-serif; font-size: .68rem; font-weight: 500;
  letter-spacing: .18em; text-transform: uppercase;
  color: var(--sage, #7d9b76); margin-bottom: .8rem;
}
.faq-left-title {
  font-family: 'Cormorant Garamond', serif;
  font-size: 2.2rem; font-weight: 300; color: #2a1018;
  line-height: 1.2; margin-bottom: .6rem;
}
.faq-left-title em { font-style: italic; color: var(--rose, #c0485a); }
.faq-left-desc {
  font-family: 'Jost', sans-serif; font-size: .82rem;
  color: #9a7070; line-height: 1.7; max-width: 300px; margin: 0 auto 1.5rem;
}

/* Stats mini */
.faq-stats {
  display: flex; justify-content: center; gap: 2rem;
  border-top: 1px solid rgba(192,72,90,.1);
  padding-top: 1.2rem;
}
.faq-stat { text-align: center; }
.faq-stat-num {
  font-family: 'Cormorant Garamond', serif;
  font-size: 1.6rem; font-weight: 600; color: var(--rose, #c0485a);
  line-height: 1; display: block;
}
.faq-stat-label {
  font-family: 'Jost', sans-serif; font-size: .62rem;
  letter-spacing: .1em; text-transform: uppercase;
  color: #9a7070; display: block; margin-top: .2rem;
}

/* WA CTA on left */
.faq-left-cta {
  display: inline-flex; align-items: center; gap: .5rem;
  font-family: 'Jost', sans-serif; font-size: .78rem; font-weight: 600;
  background: linear-gradient(135deg, #25d366, #128c50);
  color: #fff; padding: .75rem 1.5rem; border-radius: 999px;
  text-decoration: none; margin-top: 1.2rem;
  box-shadow: 0 6px 20px rgba(18,140,80,.25);
  transition: transform .2s, box-shadow .2s;
}
.faq-left-cta:hover { transform: translateY(-2px); box-shadow: 0 10px 28px rgba(18,140,80,.35); }

/* ══════════════
   RIGHT — ACCORDION
══════════════ */
/* .faq-right {} */

.faq-right-eyebrow {
  display: inline-flex; align-items: center; gap: .6rem;
  font-family: 'Jost', sans-serif; font-size: .7rem; font-weight: 500;
  letter-spacing: .18em; text-transform: uppercase;
  color: var(--rose, #c0485a); margin-bottom: 1rem;
}
.faq-right-eyebrow::before {
  content: ''; display: block; width: 28px; height: 1px;
  background: var(--rose, #c0485a); opacity: .4;
}
.faq-right-title {
  font-family: 'Cormorant Garamond', serif;
  font-size: clamp(1.8rem, 3vw, 2.5rem); font-weight: 300;
  color: #2a1018; line-height: 1.15; margin-bottom: .5rem;
}
.faq-right-title em { font-style: italic; color: var(--rose, #c0485a); }
.faq-right-subtitle {
  font-family: 'Jost', sans-serif; font-size: .85rem; color: #9a7070;
  margin-bottom: 2.2rem; line-height: 1.6;
}

/* Accordion list */
.faq-list { display: flex; flex-direction: column; gap: .85rem; }

/* Item */
.faq-item {
  background: rgba(255,255,255,.82);
  backdrop-filter: blur(10px);
  border: 1px solid rgba(192,72,90,.08);
  border-radius: 18px;
  overflow: hidden;
  box-shadow: 0 2px 12px rgba(122,31,46,.05);
  transition: border-color .25s, box-shadow .25s, background .25s;
  animation: faqReveal .5s ease both;
}
.faq-item.open {
  border-color: rgba(192,72,90,.2);
  background: rgba(255,255,255,.97);
  box-shadow: 0 8px 30px rgba(122,31,46,.1);
}

@keyframes faqReveal {
  from { opacity:0; transform: translateY(16px); }
  to   { opacity:1; transform: translateY(0); }
}
.faq-item:nth-child(1) { animation-delay:.05s; }
.faq-item:nth-child(2) { animation-delay:.12s; }
.faq-item:nth-child(3) { animation-delay:.18s; }
.faq-item:nth-child(4) { animation-delay:.24s; }
.faq-item:nth-child(5) { animation-delay:.30s; }
.faq-item:nth-child(6) { animation-delay:.36s; }

/* Question row */
.faq-q {
  display: flex; align-items: center; gap: 1rem;
  padding: 1.1rem 1.3rem;
  cursor: pointer;
  user-select: none;
}

/* Number badge */
.faq-num {
  font-family: 'Cormorant Garamond', serif;
  font-size: .85rem; font-weight: 600;
  color: var(--rose, #c0485a);
  background: rgba(192,72,90,.08);
  border: 1px solid rgba(192,72,90,.14);
  border-radius: 999px;
  padding: .15rem .6rem;
  flex-shrink: 0;
  transition: background .2s, color .2s;
}
.faq-item.open .faq-num {
  background: var(--rose, #c0485a); color: #fff;
}

/* Question text */
.faq-q-text {
  font-family: 'Cormorant Garamond', serif;
  font-size: 1.05rem; font-weight: 600;
  color: #2a1018; line-height: 1.35; flex: 1;
  transition: color .2s;
}
.faq-item.open .faq-q-text { color: var(--rose, #c0485a); }

/* Plus/close icon */
.faq-icon {
  width: 32px; height: 32px; border-radius: 50%;
  border: 1.5px solid rgba(192,72,90,.18);
  display: flex; align-items: center; justify-content: center;
  flex-shrink: 0; color: var(--rose, #c0485a);
  transition: background .25s, border-color .25s, transform .35s;
}
.faq-item.open .faq-icon {
  background: var(--rose, #c0485a); border-color: var(--rose, #c0485a);
  color: #fff; transform: rotate(45deg);
}

/* Answer panel */
.faq-a-wrap {
  max-height: 0;
  overflow: hidden;
  transition: max-height .4s cubic-bezier(.22,1,.36,1), opacity .3s ease;
  opacity: 0;
}
.faq-item.open .faq-a-wrap {
  opacity: 1;
}

.faq-a {
  padding: 0 1.3rem 1.3rem 1.3rem;
  padding-left: calc(1.3rem + 2.2rem + 1rem); /* align with question text */
}

/* Gold line accent left of answer */
.faq-a-inner {
  border-left: 2px solid rgba(201,168,76,.3);
  padding-left: 1rem;
  font-family: 'Jost', sans-serif;
  font-size: .83rem; color: #7a5060;
  line-height: 1.75;
}

/* Mobile: show header on right column */
@media (max-width: 960px) {
  .faq-right-eyebrow,
  .faq-right-title,
  .faq-right-subtitle { display: block; }
}
</style>

<?php if (!empty($faqs)): ?>
<section class="faq-section">
  <div class="faq-bg"></div>

  <div class="faq-inner">

    <!-- ═══ LEFT: ILUSTRASI ═══ -->
    <div class="faq-left">

      <div class="faq-ilus-wrap">

        <!-- Orbiting petals -->
        <div class="faq-orbit">
          <?php
          $petalPos = [
            ['top'=>'5%',  'left'=>'50%'],
            ['top'=>'25%', 'left'=>'92%'],
            ['top'=>'70%', 'left'=>'88%'],
            ['top'=>'90%', 'left'=>'48%'],
            ['top'=>'68%', 'left'=>'5%' ],
            ['top'=>'22%', 'left'=>'8%' ],
          ];
          foreach ($petalPos as $p): ?>
          <div class="faq-orbit-petal" style="top:<?= $p['top'] ?>;left:<?= $p['left'] ?>;transform:translate(-50%,-50%) rotate(<?= rand(0,360) ?>deg)"></div>
          <?php endforeach; ?>
        </div>

        <!-- Main flower SVG illustration -->
        <svg class="faq-flower-main" width="280" height="280" viewBox="0 0 280 280" fill="none" xmlns="http://www.w3.org/2000/svg">
          <!-- Outer petals layer 1 -->
          <?php foreach (range(0,7) as $i):
            $angle = $i * 45;
          ?>
          <ellipse cx="140" cy="140" rx="18" ry="52"
            fill="url(#petalGrad1)"
            opacity=".85"
            transform="rotate(<?= $angle ?> 140 140) translate(0 -42)"/>
          <?php endforeach; ?>

          <!-- Outer petals layer 2 (offset 22.5deg) -->
          <?php foreach (range(0,7) as $i):
            $angle = $i * 45 + 22.5;
          ?>
          <ellipse cx="140" cy="140" rx="13" ry="40"
            fill="url(#petalGrad2)"
            opacity=".6"
            transform="rotate(<?= $angle ?> 140 140) translate(0 -38)"/>
          <?php endforeach; ?>

          <!-- Inner petals -->
          <?php foreach (range(0,5) as $i):
            $angle = $i * 60;
          ?>
          <ellipse cx="140" cy="140" rx="10" ry="28"
            fill="url(#petalGrad3)"
            opacity=".9"
            transform="rotate(<?= $angle ?> 140 140) translate(0 -22)"/>
          <?php endforeach; ?>

          <!-- Center circles -->
          <circle cx="140" cy="140" r="28" fill="url(#centerGrad)" opacity=".95"/>
          <circle cx="140" cy="140" r="18" fill="#fdf6ee"/>
          <circle cx="140" cy="140" r="10" fill="url(#goldGrad)"/>
          <circle cx="140" cy="140" r="4"  fill="#fff"/>

          <!-- Leaves -->
          <path d="M80 200 Q110 165 145 185 Q115 210 80 200Z"  fill="#7d9b76" opacity=".7"/>
          <path d="M200 195 Q172 162 138 180 Q165 207 200 195Z" fill="#7d9b76" opacity=".65"/>
          <path d="M70 155 Q105 145 108 168 Q88 175 70 155Z"   fill="#a8c4a0" opacity=".5"/>
          <path d="M210 152 Q176 144 172 167 Q192 172 210 152Z" fill="#a8c4a0" opacity=".5"/>

          <!-- Small accent flowers -->
          <?php foreach ([['50','80'],['230','90'],['45','195'],['235','200']] as $fc): ?>
          <circle cx="<?= $fc[0] ?>" cy="<?= $fc[1] ?>" r="12" fill="#f2d4d7" opacity=".6"/>
          <circle cx="<?= $fc[0] ?>" cy="<?= $fc[1] ?>" r="5"  fill="#c9a84c" opacity=".8"/>
          <?php endforeach; ?>

          <!-- Gradients -->
          <defs>
            <linearGradient id="petalGrad1" x1="0" y1="0" x2="0" y2="1">
              <stop offset="0%"   stop-color="#f2d4d7"/>
              <stop offset="100%" stop-color="#c0485a"/>
            </linearGradient>
            <linearGradient id="petalGrad2" x1="0" y1="0" x2="0" y2="1">
              <stop offset="0%"   stop-color="#fdf0f3"/>
              <stop offset="100%" stop-color="#e8899a"/>
            </linearGradient>
            <linearGradient id="petalGrad3" x1="0" y1="0" x2="0" y2="1">
              <stop offset="0%"   stop-color="#fff0f5"/>
              <stop offset="100%" stop-color="#c0485a" stop-opacity=".8"/>
            </linearGradient>
            <radialGradient id="centerGrad" cx="50%" cy="50%" r="50%">
              <stop offset="0%"   stop-color="#e8899a"/>
              <stop offset="100%" stop-color="#c0485a"/>
            </radialGradient>
            <linearGradient id="goldGrad" x1="0" y1="0" x2="1" y2="1">
              <stop offset="0%"   stop-color="#e8cc80"/>
              <stop offset="100%" stop-color="#c9a84c"/>
            </linearGradient>
          </defs>
        </svg>

      </div><!-- /.faq-ilus-wrap -->

      <div class="faq-left-header">
        <div class="faq-left-eyebrow">✦ Butuh Bantuan? ✦</div>
        <h3 class="faq-left-title">Masih Ada<br><em>Pertanyaan?</em></h3>
        <p class="faq-left-desc">Tim admin kami siap menjawab semua pertanyaan Anda kapan saja melalui WhatsApp.</p>

        <div class="faq-stats">
          <div class="faq-stat">
            <span class="faq-stat-num">24</span>
            <span class="faq-stat-label">Jam Siaga</span>
          </div>
          <div class="faq-stat">
            <span class="faq-stat-num">&lt;5'</span>
            <span class="faq-stat-label">Respon Admin</span>
          </div>
          <div class="faq-stat">
            <span class="faq-stat-num">500+</span>
            <span class="faq-stat-label">Pelanggan</span>
          </div>
        </div>

        <a href="<?= waLink('Halo Chika Florist, saya punya pertanyaan') ?>" target="_blank" class="faq-left-cta">
          <svg width="16" height="16" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z"/><path d="M12 0C5.373 0 0 5.373 0 12c0 2.096.541 4.063 1.491 5.776L.057 23.04l5.43-1.424A11.947 11.947 0 0012 24c6.627 0 12-5.373 12-12S18.627 0 12 0zm0 21.818a9.818 9.818 0 01-5.015-1.371l-.36-.214-3.726.977.995-3.633-.235-.374A9.818 9.818 0 1112 21.818z"/></svg>
          Tanya via WhatsApp
        </a>
      </div>

    </div><!-- /.faq-left -->

    <!-- ═══ RIGHT: ACCORDION ═══ -->
    <div class="faq-right">
      <div class="faq-right-eyebrow">✿ FAQ</div>
      <h2 class="faq-right-title">Pertanyaan yang<br><em>Sering Diajukan</em></h2>
      <p class="faq-right-subtitle">Temukan jawaban atas pertanyaan umum seputar layanan dan pemesanan bunga.</p>

      <div class="faq-list" id="faqList">
        <?php foreach ($faqs as $idx => $faq): ?>
        <div class="faq-item" data-idx="<?= $idx ?>">
          <div class="faq-q">
            <span class="faq-num"><?= str_pad($idx+1, 2, '0', STR_PAD_LEFT) ?></span>
            <span class="faq-q-text"><?= clean($faq['question']) ?></span>
            <div class="faq-icon">
              <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round">
                <line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/>
              </svg>
            </div>
          </div>
          <div class="faq-a-wrap">
            <div class="faq-a">
              <div class="faq-a-inner"><?= clean($faq['answer']) ?></div>
            </div>
          </div>
        </div>
        <?php endforeach; ?>
      </div>
    </div>

  </div>
</section>

<script>
(function(){
  var items = document.querySelectorAll('.faq-item');
  items.forEach(function(item) {
    var q    = item.querySelector('.faq-q');
    var wrap = item.querySelector('.faq-a-wrap');
    var ans  = item.querySelector('.faq-a');
    if (!q || !wrap) return;

    q.addEventListener('click', function() {
      var isOpen = item.classList.contains('open');

      /* Close all */
      items.forEach(function(el) {
        el.classList.remove('open');
        var w = el.querySelector('.faq-a-wrap');
        if (w) w.style.maxHeight = '0';
      });

      /* Open clicked if was closed */
      if (!isOpen) {
        item.classList.add('open');
        wrap.style.maxHeight = ans.scrollHeight + 40 + 'px';
      }
    });
  });

  /* Open first by default */
  if (items.length > 0) items[0].querySelector('.faq-q').click();
})();
</script>
<?php endif; ?>