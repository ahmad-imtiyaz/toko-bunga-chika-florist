<?php
// blog-sidebar.php — Rose × Gold × Cream theme (Chika Florist gen 2)
// Variabel tersedia dari parent (blog.php): $blog_cats, $filter_cat, $wa_url, $cities, $pdo

// Artikel terbaru
$sidebar_recent = $pdo->query("
    SELECT b.title, b.slug, b.thumbnail, b.created_at, bc.name AS cat_name
    FROM blogs b
    LEFT JOIN blog_categories bc ON b.blog_category_id = bc.id
    WHERE b.status = 'active'
    ORDER BY b.created_at DESC LIMIT 5
")->fetchAll();

// Kategori produk (gen 2: is_active, sort_order)
$sidebar_categories = $pdo->query("
    SELECT * FROM categories
    WHERE is_active = 1 AND (parent_id IS NULL OR parent_id = 0)
    ORDER BY sort_order ASC, id ASC
")->fetchAll();

// Produk searchable
$sidebar_products = $pdo->query("
    SELECT p.*, c.name AS cat_name FROM products p
    LEFT JOIN categories c ON p.category_id = c.id
    WHERE p.is_active = 1
    ORDER BY p.sort_order ASC, p.id ASC
    LIMIT 30
")->fetchAll();

// Cities grouped by tier untuk area pengiriman
$sidebar_cities = $pdo->query("
    SELECT * FROM cities WHERE is_active = 1
    ORDER BY tier ASC, sort_order ASC, name ASC
")->fetchAll();

$cities_by_tier = [];
foreach ($sidebar_cities as $city) {
    $cities_by_tier[$city['tier']][] = $city;
}
?>

<style>
/* ══════════════════════════════════════════
   SIDEBAR — Rose × Gold × Cream (Chika gen 2)
══════════════════════════════════════════ */
.sb-card {
  background: var(--paper);
  border: 1px solid var(--cream-dd);
  border-radius: 16px; overflow: hidden;
  box-shadow: 0 4px 20px rgba(225,29,72,.04);
}
.sb-head {
  padding: 13px 18px 10px;
  border-bottom: 1px solid var(--cream-dd);
  background: rgba(225,29,72,.03);
}
.sb-head-label {
  font-family: 'Lato', sans-serif;
  font-size: 9px; font-weight: 700;
  text-transform: uppercase; letter-spacing: .14em;
  color: rgba(225,29,72,.45); margin-bottom: 3px;
}
.sb-head-title {
  font-family: 'Playfair Display', serif;
  font-size: 16px; font-weight: 700; color: var(--ink);
}
.sb-link {
  display: flex; align-items: center; justify-content: space-between;
  padding: 8px 12px; border-radius: 10px; text-decoration: none;
  margin-bottom: 2px; transition: background .2s, color .2s;
  font-family: 'Lato', sans-serif;
  font-size: 12px; font-weight: 400; color: var(--muted);
}
.sb-link:hover, .sb-link.active { background: rgba(225,29,72,.07); color: var(--rose); }
.sb-link.active { font-weight: 700; }
.sb-badge {
  font-family: 'Lato', sans-serif;
  font-size: 10px; background: rgba(225,29,72,.06);
  padding: 2px 8px; border-radius: 999px; color: var(--muted);
}
.sb-dot {
  width: 5px; height: 5px; border-radius: 50%;
  background: rgba(225,29,72,.25); display: inline-block; flex-shrink: 0;
}
.sb-accent-btn {
  display: block; background: var(--ink);
  color: var(--cream); font-family: 'Lato', sans-serif;
  font-size: 12px; font-weight: 700; padding: 11px;
  border-radius: 999px; text-decoration: none; text-align: center;
  letter-spacing: .04em; box-shadow: 0 4px 16px rgba(31,41,55,.16);
  transition: opacity .2s, transform .2s;
}
.sb-accent-btn:hover { opacity: .85; transform: translateY(-1px); }

/* Prod scroll */
#sidebar-prod-list-ck::-webkit-scrollbar { width: 3px; }
#sidebar-prod-list-ck::-webkit-scrollbar-track { background: rgba(225,29,72,.04); border-radius: 3px; }
#sidebar-prod-list-ck::-webkit-scrollbar-thumb { background: rgba(225,29,72,.22); border-radius: 3px; }
#sidebar-prod-list-ck::-webkit-scrollbar-thumb:hover { background: var(--rose); }

/* Cat slider */
#cat-slider-track-ck { overflow: hidden; }
#cat-slider-inner-ck {
  display: flex; gap: 8px;
  transition: transform .35s cubic-bezier(.4,0,.2,1);
  will-change: transform;
}
.sb-nav-btn {
  width: 26px; height: 26px; border-radius: 50%; cursor: pointer;
  background: rgba(225,29,72,.06);
  border: 1px solid rgba(225,29,72,.2);
  color: var(--rose); font-size: 15px; font-weight: 600;
  display: flex; align-items: center; justify-content: center;
  transition: all .2s; line-height: 1;
}
.sb-nav-btn:hover { background: var(--ink); color: var(--cream); border-color: var(--ink); }

.sb-recent-item {
  display: flex; gap: 10px; align-items: flex-start;
  padding: 9px 0; border-bottom: 1px solid var(--cream-dd);
  text-decoration: none; transition: opacity .2s;
}
.sb-recent-item:hover { opacity: .75; }
.sb-recent-item:last-child { border-bottom: none; }

.sb-prod-item {
  display: flex; align-items: center; gap: 10px; padding: 8px 10px;
  border-radius: 10px; text-decoration: none; margin-bottom: 2px;
  transition: background .2s;
}
.sb-prod-item:hover { background: rgba(225,29,72,.05); }

/* Area tier badges */
.sb-tier-label {
  font-family: 'Lato', sans-serif;
  font-size: 9px; font-weight: 700;
  text-transform: uppercase; letter-spacing: .14em;
  color: var(--gold); margin: 10px 0 5px;
  display: flex; align-items: center; gap: 7px;
}
.sb-tier-label::after {
  content: '';
  flex: 1; height: 1px;
  background: linear-gradient(90deg, rgba(201,168,76,.3), transparent);
}
.area-pill-sb {
  font-family: 'Lato', sans-serif;
  font-size: 11px; font-weight: 400;
  color: var(--muted); text-decoration: none;
  padding: 5px 0; display: flex; align-items: center; gap: 8px;
  transition: color .2s;
}
.area-pill-sb:hover { color: var(--rose); }
</style>

<div style="display:flex;flex-direction:column;gap:14px;">

  <!-- ── 1. Kategori Artikel ── -->
  <div class="sb-card">
    <div class="sb-head">
      <p class="sb-head-label">Filter Artikel</p>
      <h3 class="sb-head-title">Kategori Artikel</h3>
    </div>
    <div style="padding:8px 10px;max-height:230px;overflow-y:auto;">
      <a href="<?= BASE_URL ?>/blog" class="sb-link <?= !$filter_cat ? 'active' : '' ?>">
        <span>Semua Artikel</span>
        <span class="sb-badge"><?= array_sum(array_column($blog_cats,'total')) ?></span>
      </a>
      <?php foreach ($blog_cats as $bc): $act = ($filter_cat === $bc['slug']); ?>
      <a href="<?= BASE_URL ?>/blog?kategori=<?= clean($bc['slug']) ?>"
         class="sb-link <?= $act ? 'active' : '' ?>">
        <span style="display:flex;align-items:center;gap:7px;">
          <span class="sb-dot" style="<?= $act ? 'background:var(--rose);' : '' ?>"></span>
          <?= clean($bc['name']) ?>
        </span>
        <span class="sb-badge"><?= $bc['total'] ?></span>
      </a>
      <?php endforeach; ?>
    </div>
  </div>

  <!-- ── 2. Slider Kategori Bunga ── -->
  <?php if (!empty($sidebar_categories)): ?>
  <div class="sb-card">
    <div class="sb-head" style="display:flex;align-items:center;justify-content:space-between;">
      <div>
        <p class="sb-head-label">Produk</p>
        <h3 class="sb-head-title">Kategori Bunga</h3>
      </div>
      <div style="display:flex;gap:5px;">
        <button class="sb-nav-btn" onclick="slideCatCk(-1)">‹</button>
        <button class="sb-nav-btn" onclick="slideCatCk(1)">›</button>
      </div>
    </div>
    <div style="padding:12px;">
      <div id="cat-slider-track-ck">
        <div id="cat-slider-inner-ck">
          <?php foreach ($sidebar_categories as $sc):
            $cat_img = !empty($sc['image']) && file_exists(UPLOAD_DIR . $sc['image'])
                       ? UPLOAD_URL . $sc['image']
                       : 'https://images.unsplash.com/photo-1490750967868-88df5691cc69?w=120&h=120&fit=crop';
          ?>
          <a href="<?= BASE_URL ?>/<?= clean($sc['slug']) ?>"
             style="flex-shrink:0;width:calc(50% - 4px);text-align:center;text-decoration:none;display:block;">
            <div style="aspect-ratio:1/1;border-radius:12px;overflow:hidden;margin-bottom:6px;
                        border:1px solid var(--cream-dd);
                        transition:border-color .25s;"
                 onmouseover="this.style.borderColor='rgba(225,29,72,.35)';this.querySelector('img').style.transform='scale(1.08)';"
                 onmouseout="this.style.borderColor='var(--cream-dd)';this.querySelector('img').style.transform='scale(1)';">
              <img src="<?= clean($cat_img) ?>" alt="<?= clean($sc['name']) ?>"
                   style="width:100%;height:100%;object-fit:cover;transition:transform .5s ease;" loading="lazy">
            </div>
            <p style="font-family:'Lato',sans-serif;font-size:11px;font-weight:600;
                      color:var(--ink);line-height:1.3;
                      display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;
                      line-clamp:2;overflow:hidden;">
              <?= clean($sc['name']) ?>
            </p>
          </a>
          <?php endforeach; ?>
        </div>
      </div>
      <div id="cat-dots-ck" style="display:flex;justify-content:center;gap:5px;margin-top:10px;"></div>
    </div>
  </div>
  <?php endif; ?>

  <!-- ── 3. Produk Searchable ── -->
  <?php if (!empty($sidebar_products)): ?>
  <div class="sb-card">
    <div class="sb-head">
      <p class="sb-head-label">Toko Bunga</p>
      <h3 class="sb-head-title">Produk Kami</h3>
    </div>
    <div style="padding:10px 14px 8px;">
      <input type="text" id="sidebar-prod-search-ck" placeholder="Cari produk..."
             style="width:100%;padding:8px 14px;box-sizing:border-box;
                    font-family:'Lato',sans-serif;font-size:13px;font-weight:300;
                    border:1.5px solid var(--cream-dd);border-radius:999px;
                    outline:none;color:var(--ink);background:var(--cream);
                    transition:border-color .2s,box-shadow .2s;"
             onfocus="this.style.borderColor='var(--rose-l)';this.style.boxShadow='0 0 0 3px rgba(225,29,72,.08)';"
             onblur="this.style.borderColor='var(--cream-dd)';this.style.boxShadow='none';">
    </div>
    <div id="sidebar-prod-list-ck" style="padding:4px 10px 10px;max-height:280px;overflow-y:auto;">
      <?php foreach ($sidebar_products as $prod):
        $thumb   = !empty($prod['image']) && file_exists(UPLOAD_DIR . $prod['image'])
                   ? UPLOAD_URL . $prod['image']
                   : 'https://images.unsplash.com/photo-1487530811015-780780dde0e4?w=80&h=80&fit=crop';
        $wa_prod = urlencode("Halo, saya tertarik memesan *{$prod['name']}*. Apakah masih tersedia?");
      ?>
      <a href="<?= clean($wa_url) ?>?text=<?= $wa_prod ?>" target="_blank"
         class="sb-prod-item sidebar-prod-item-ck"
         data-name="<?= strtolower(clean($prod['name'])) ?>">
        <img src="<?= clean($thumb) ?>" alt="<?= clean($prod['name']) ?>"
             style="width:46px;height:46px;border-radius:10px;object-fit:cover;
                    flex-shrink:0;border:1px solid var(--cream-dd);">
        <div style="flex:1;min-width:0;">
          <p style="font-family:'Lato',sans-serif;font-size:12px;font-weight:600;
                    color:var(--ink);line-height:1.3;
                    display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;
                    line-clamp:2;overflow:hidden;margin-bottom:3px;">
            <?= clean($prod['name']) ?>
          </p>
          <p style="font-family:'Lato',sans-serif;font-size:11px;font-weight:700;color:var(--rose);">
            <?= formatHarga($prod['price_min'], $prod['price_max']) ?>
          </p>
        </div>
        <svg style="width:16px;height:16px;flex-shrink:0;color:#22c55e;opacity:.7;" fill="currentColor" viewBox="0 0 24 24">
          <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z"/>
          <path d="M12 0C5.373 0 0 5.373 0 12c0 2.127.558 4.126 1.533 5.861L0 24l6.305-1.508A11.954 11.954 0 0012 24c6.627 0 12-5.373 12-12S18.627 0 12 0zm0 21.818a9.818 9.818 0 01-5.002-1.374l-.36-.214-3.735.893.944-3.639-.234-.374A9.818 9.818 0 1112 21.818z"/>
        </svg>
      </a>
      <?php endforeach; ?>
      <p id="sidebar-prod-nores-ck" style="display:none;text-align:center;
         font-family:'Lato',sans-serif;font-size:12px;color:var(--muted);padding:14px 0;">
        Produk tidak ditemukan 🌸
      </p>
    </div>
  </div>
  <?php endif; ?>

  <!-- ── 4. CTA WhatsApp ── -->
  <div style="position:relative;overflow:hidden;
              background:linear-gradient(135deg,rgba(225,29,72,.09),rgba(201,168,76,.07));
              border:1px solid rgba(225,29,72,.2);border-radius:16px;
              padding:20px;text-align:center;">
    <div style="position:absolute;top:-30px;right:-30px;width:110px;height:110px;
                background:radial-gradient(circle,rgba(225,29,72,.2),transparent 65%);
                pointer-events:none;"></div>
    <div style="position:absolute;bottom:-20px;left:-20px;width:80px;height:80px;
                background:radial-gradient(circle,rgba(201,168,76,.15),transparent 65%);
                pointer-events:none;"></div>
    <div style="position:relative;z-index:2;">
      <div style="font-size:28px;margin-bottom:9px;">💬</div>
      <p style="font-family:'Playfair Display',serif;font-weight:700;color:var(--ink);
                font-size:17px;margin-bottom:5px;">Mau Pesan Bunga?</p>
      <p style="font-family:'Lato',sans-serif;font-size:12px;font-weight:300;
                color:var(--muted);margin-bottom:16px;line-height:1.55;">
        Konsultasi gratis via WhatsApp.<br>Siap 24 jam!
      </p>
      <a href="<?= clean($wa_url) ?>" target="_blank" class="sb-accent-btn">Chat WhatsApp Sekarang</a>
    </div>
  </div>

  <!-- ── 5. Artikel Terbaru ── -->
  <?php if (!empty($sidebar_recent)): ?>
  <div class="sb-card">
    <div class="sb-head">
      <p class="sb-head-label">Terbaru</p>
      <h3 class="sb-head-title">Artikel Terbaru</h3>
    </div>
    <div style="padding:8px 14px 10px;">
      <?php foreach ($sidebar_recent as $sr):
        $sr_thumb = !empty($sr['thumbnail']) && file_exists(UPLOAD_DIR . $sr['thumbnail'])
                    ? UPLOAD_URL . $sr['thumbnail']
                    : 'https://images.unsplash.com/photo-1487530811015-780780dde0e4?w=80&h=80&fit=crop';
      ?>
      <a href="<?= BASE_URL ?>/blog/<?= clean($sr['slug']) ?>" class="sb-recent-item">
        <div style="flex-shrink:0;width:52px;height:52px;border-radius:10px;overflow:hidden;
                    border:1px solid var(--cream-dd);">
          <img src="<?= clean($sr_thumb) ?>" alt="" style="width:100%;height:100%;object-fit:cover;" loading="lazy">
        </div>
        <div style="flex:1;min-width:0;">
          <?php if ($sr['cat_name']): ?>
          <span style="font-family:'Lato',sans-serif;font-size:9px;font-weight:700;
                       color:var(--rose);text-transform:uppercase;letter-spacing:.08em;">
            <?= clean($sr['cat_name']) ?>
          </span>
          <?php endif; ?>
          <p style="font-family:'Lato',sans-serif;font-size:12px;font-weight:600;
                    color:var(--ink-l);line-height:1.35;margin-top:2px;
                    display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;
                    line-clamp:2;overflow:hidden;">
            <?= clean($sr['title']) ?>
          </p>
          <p style="font-family:'Lato',sans-serif;font-size:10px;font-weight:300;
                    color:rgba(31,41,55,.35);margin-top:3px;">
            <?= date('d M Y', strtotime($sr['created_at'])) ?>
          </p>
        </div>
      </a>
      <?php endforeach; ?>
    </div>
  </div>
  <?php endif; ?>

  <!-- ── 6. Area Pengiriman (grouped by tier) ── -->
  <?php if (!empty($cities_by_tier)): ?>
  <div class="sb-card" style="padding:16px 18px;">
    <h3 style="font-family:'Playfair Display',serif;font-size:16px;font-weight:700;
               color:var(--ink);margin-bottom:4px;display:flex;align-items:center;gap:7px;">
      <span>📍</span> Area Pengiriman
    </h3>
    <p style="font-family:'Lato',sans-serif;font-size:10px;color:var(--muted);
              margin-bottom:10px;letter-spacing:.05em;">Kami melayani wilayah Jakarta &amp; sekitarnya</p>

    <?php
    $tier_labels = [1 => 'Kota Utama', 2 => 'Kota Sekitar', 3 => 'Area Lainnya'];
    foreach ($cities_by_tier as $tier => $tier_cities):
    ?>
    <div class="sb-tier-label"><?= $tier_labels[$tier] ?? 'Area Tier '.$tier ?></div>
    <div style="display:flex;flex-direction:column;gap:1px;margin-bottom:4px;">
      <?php foreach ($tier_cities as $city): ?>
      <a href="<?= BASE_URL ?>/toko-bunga-<?= clean($city['slug']) ?>" class="area-pill-sb">
        <span class="sb-dot" style="background:<?= $tier==1?'var(--rose)':($tier==2?'var(--gold)':'rgba(225,29,72,.2)') ?>;"></span>
        <?= clean($city['name']) ?>
        <?php if (!empty($city['province'])): ?>
        <span style="font-size:9px;opacity:.5;"><?= clean($city['province']) ?></span>
        <?php endif; ?>
      </a>
      <?php endforeach; ?>
    </div>
    <?php endforeach; ?>
  </div>
  <?php endif; ?>

</div>

<script>
/* ── Product search ── */
(function(){
  const input = document.getElementById('sidebar-prod-search-ck');
  const items = document.querySelectorAll('.sidebar-prod-item-ck');
  const noRes = document.getElementById('sidebar-prod-nores-ck');
  if (!input) return;
  input.addEventListener('input', function(){
    const q = this.value.toLowerCase().trim(); let vis = 0;
    items.forEach(item => {
      const show = !q || item.dataset.name.includes(q);
      item.style.display = show ? '' : 'none';
      if (show) vis++;
    });
    noRes.style.display = vis > 0 ? 'none' : 'block';
  });
})();

/* ── Category slider — 2 per page ── */
(function(){
  const inner  = document.getElementById('cat-slider-inner-ck');
  const dotsEl = document.getElementById('cat-dots-ck');
  if (!inner) return;
  const items  = inner.querySelectorAll('a');
  const perPage = 2;
  const pages  = Math.ceil(items.length / perPage);
  let cur = 0;

  for (let i = 0; i < pages; i++) {
    const d = document.createElement('button');
    d.style.cssText = `width:${i===0?'16px':'6px'};height:6px;border-radius:3px;border:none;cursor:pointer;transition:all .25s;background:${i===0?'var(--rose)':'rgba(225,29,72,.15)'};padding:0;`;
    d.onclick = () => goTo(i);
    dotsEl.appendChild(d);
  }

  function goTo(idx) {
    cur = Math.max(0, Math.min(idx, pages - 1));
    const trackW = inner.parentElement.offsetWidth;
    inner.style.transform = `translateX(-${cur * (trackW + 8)}px)`;
    dotsEl.querySelectorAll('button').forEach((d, i) => {
      d.style.width      = i === cur ? '16px' : '6px';
      d.style.background = i === cur ? 'var(--rose)' : 'rgba(225,29,72,.15)';
    });
  }

  window.slideCatCk = function(dir) { goTo(cur + dir); };
})();
</script>