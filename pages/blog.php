<?php
require_once __DIR__ . '/../includes/config.php';

$pdo = getDB();

$page_title    = 'Blog - ' . getSetting('site_name', 'Chika Florist');
$meta_desc     = 'Artikel, tips, dan inspirasi seputar bunga dari ' . getSetting('site_name', 'Chika Florist') . '.';
$canonical_url = BASE_URL . '/blog';

$filter_cat = isset($_GET['kategori']) ? trim($_GET['kategori']) : '';
$search     = isset($_GET['q'])        ? trim($_GET['q'])        : '';

$per_page   = 9;
$page       = max(1, (int)($_GET['page'] ?? 1));
$offset     = ($page - 1) * $per_page;

$where  = ["b.status = 'active'"];
$params = [];
if ($filter_cat) { $where[] = 'bc.slug = ?';                                    $params[] = $filter_cat; }
if ($search)     { $where[] = '(b.title LIKE ? OR b.excerpt LIKE ?)';           $params[] = "%$search%"; $params[] = "%$search%"; }
$where_sql = implode(' AND ', $where);

$count_stmt = $pdo->prepare("SELECT COUNT(*) FROM blogs b LEFT JOIN blog_categories bc ON b.blog_category_id = bc.id WHERE $where_sql");
$count_stmt->execute($params);
$total      = (int)$count_stmt->fetchColumn();
$total_page = (int)ceil($total / $per_page);

$stmt = $pdo->prepare("
    SELECT b.*, bc.name AS cat_name, bc.slug AS cat_slug
    FROM blogs b
    LEFT JOIN blog_categories bc ON b.blog_category_id = bc.id
    WHERE $where_sql
    ORDER BY b.urutan ASC, b.created_at DESC
    LIMIT $per_page OFFSET $offset
");
$stmt->execute($params);
$blogs = $stmt->fetchAll();

$blog_cats = $pdo->query("
    SELECT bc.*, COUNT(b.id) AS total
    FROM blog_categories bc
    LEFT JOIN blogs b ON b.blog_category_id = bc.id AND b.status = 'active'
    WHERE bc.status = 'active'
    GROUP BY bc.id ORDER BY bc.urutan ASC
")->fetchAll();

// Ambil cities untuk sidebar (gen 2 pakai cities bukan locations)
$cities = $pdo->query("SELECT * FROM cities WHERE is_active=1 ORDER BY tier ASC, sort_order ASC, name ASC LIMIT 20")->fetchAll();

$breadcrumbs = [['label' => 'Beranda', 'url' => '/'], ['label' => 'Blog']];

require_once __DIR__ . '/../includes/header.php';
?>

<style>
/* ══════════════════════════════════════════
   TOKENS — Chika Florist Blog
   Rose × Gold × Cream
══════════════════════════════════════════ */
:root {
  --cream:     #fdf6ee;
  --cream-d:   #f5e8d8;
  --cream-dd:  #e8d5bc;
  --paper:     #fffaf4;
  --ink:       #1f2937;
  --ink-l:     #374151;
  --rose:      #e11d48;
  --rose-l:    #fca5a5;
  --rose-faint:#fdf0f3;
  --gold:      #c9a84c;
  --gold-l:    #f9c784;
  --gold-faint:#fef3c7;
  --muted:     #9ca3af;
  --border:    rgba(225,29,72,.1);
}

/* ── Animasi ── */
@keyframes blgFadeUp {
  from { opacity:0; transform:translateY(20px); }
  to   { opacity:1; transform:translateY(0); }
}
@keyframes blgPetalDrift {
  0%   { transform:translateY(-10px) rotate(0deg); opacity:0; }
  8%   { opacity:.22; }
  90%  { opacity:.12; }
  100% { transform:translateY(108vh) rotate(460deg) translateX(28px); opacity:0; }
}
@keyframes blgTicker {
  from { transform:translateX(0); }
  to   { transform:translateX(-50%); }
}
@keyframes blgPulse {
  0%   { transform:scale(1);   opacity:.6; }
  100% { transform:scale(2.2); opacity:0; }
}
@keyframes blgShimmerX {
  0%   { background-position:-200% center; }
  100% { background-position: 200% center; }
}
@keyframes blgFloat {
  0%,100% { transform:translateY(0) rotate(0deg); opacity:.2; }
  50%      { transform:translateY(-18px) rotate(8deg); opacity:.38; }
}
@keyframes bounceDown {
  0%,100%{transform:translateY(0)} 50%{transform:translateY(5px)}
}

.blg-rv1 { animation:blgFadeUp .5s ease both .05s; }
.blg-rv2 { animation:blgFadeUp .5s ease both .15s; }
.blg-rv3 { animation:blgFadeUp .5s ease both .27s; }

.blg-float-petal {
  position:absolute; pointer-events:none; user-select:none;
  font-size:14px; animation:blgFloat var(--dur,8s) ease-in-out var(--del,0s) infinite;
  opacity:.2;
}

/* ══ KELOPAK JATUH ══ */
.blg-petal {
  position:fixed; pointer-events:none; z-index:9998;
  border-radius:80% 20% 80% 20% / 60% 60% 40% 40%;
  animation:blgPetalDrift linear infinite;
}

/* ══ HERO ══ */
.blg-hero {
  position:relative;
  background:var(--cream);
  overflow:hidden;
  padding:88px 24px 72px;
  text-align:center;
}
.blg-hero::before {
  content:'';
  position:absolute; inset:0;
  background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='200' height='200'%3E%3Cfilter id='n'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='.85' numOctaves='4' stitchTiles='stitch'/%3E%3CfeColorMatrix type='saturate' values='0'/%3E%3C/filter%3E%3Crect width='200' height='200' filter='url(%23n)' opacity='.018'/%3E%3C/svg%3E");
  pointer-events:none; z-index:0;
}
/* Trellis pattern */
.blg-hero::after {
  content:'';
  position:absolute; inset:0; z-index:0; pointer-events:none;
  opacity:.028;
  background-image:
    repeating-linear-gradient(45deg,  #c9a84c 0,#c9a84c 1px, transparent 0,transparent 50%),
    repeating-linear-gradient(-45deg, #c9a84c 0,#c9a84c 1px, transparent 0,transparent 50%);
  background-size:28px 28px;
}
.blg-hero-dots {
  position:absolute; inset:0; z-index:0; pointer-events:none;
  background-image:radial-gradient(circle, var(--rose) 1px, transparent 1px);
  background-size:40px 40px;
  opacity:.025;
}
.blg-hero-strip {
  position:absolute; bottom:0; left:0; right:0; height:4px; z-index:6;
  background:linear-gradient(90deg, var(--cream-dd), var(--rose), var(--gold), var(--rose), var(--cream-dd));
  background-size:200% auto;
  animation:blgShimmerX 3.5s linear infinite;
}
.blg-hero-inner {
  position:relative; z-index:5;
  max-width:640px; margin:0 auto;
}

/* Badge */
.blg-badge {
  display:inline-flex; align-items:center; gap:8px;
  padding:5px 16px 5px 12px;
  background:rgba(225,29,72,.07);
  border:1px solid rgba(225,29,72,.2);
  border-radius:20px; margin-bottom:18px;
}
.blg-badge-dot {
  width:7px; height:7px; border-radius:50%;
  background:var(--rose); position:relative;
}
.blg-badge-dot::after {
  content:'';
  position:absolute; inset:-3px; border-radius:50%;
  border:1px solid var(--rose);
  animation:blgPulse 2s ease-out infinite;
}
.blg-badge-text {
  font-family:'Lato',sans-serif;
  font-size:10.5px; font-weight:700;
  letter-spacing:.18em; text-transform:uppercase;
  color:var(--rose);
}

/* Eyebrow atas judul */
.blg-eyebrow {
  font-family:'Lato',sans-serif;
  font-size:11px; font-weight:700;
  letter-spacing:.14em; text-transform:uppercase;
  color:rgba(225,29,72,.4); margin-bottom:14px;
}

/* Judul */
.blg-h1 {
  font-family:'Playfair Display',Georgia,serif;
  font-size:clamp(2.2rem,6vw,3.6rem);
  font-weight:700; color:var(--ink);
  line-height:1.1; letter-spacing:-.01em;
  margin-bottom:8px;
}
.blg-h1-accent {
  font-style:italic; font-weight:400; color:var(--rose);
}

/* Divider emas */
.blg-gold-divider {
  width:60px; height:2px;
  background:linear-gradient(90deg,transparent,var(--gold),transparent);
  margin:.75rem auto 1rem;
}

.blg-tagline {
  font-family:'Playfair Display',serif;
  font-style:italic; font-weight:400;
  font-size:clamp(.95rem,1.9vw,1.15rem);
  color:var(--rose); margin-bottom:16px;
  letter-spacing:.02em;
}
.blg-desc {
  font-family:'Lato',sans-serif;
  font-size:14px; font-weight:300;
  line-height:1.85; color:var(--ink-l);
  margin:0 auto 26px; max-width:440px; opacity:.85;
}

/* Search */
.blg-search-form {
  display:flex; max-width:480px; margin:0 auto 28px;
  border-radius:8px; overflow:hidden;
  border:1.5px solid var(--cream-dd);
  background:var(--paper);
  box-shadow:0 4px 20px rgba(225,29,72,.06);
  transition:border-color .2s, box-shadow .2s;
}
.blg-search-form:focus-within {
  border-color:var(--rose-l);
  box-shadow:0 0 0 3px rgba(225,29,72,.08), 0 4px 20px rgba(225,29,72,.06);
}
.blg-search-input {
  flex:1; padding:13px 20px;
  font-family:'Lato',sans-serif;
  font-size:14px; font-weight:300;
  background:transparent; color:var(--ink);
  border:none; outline:none; min-width:0;
}
.blg-search-input::placeholder { color:rgba(156,163,175,.6); }
.blg-search-btn {
  padding:13px 22px;
  background:var(--ink); color:var(--cream);
  font-family:'Lato',sans-serif;
  font-size:12px; font-weight:700;
  letter-spacing:.06em; text-transform:uppercase;
  border:none; cursor:pointer; flex-shrink:0;
  transition:background .2s;
}
.blg-search-btn:hover { background:var(--rose); }

/* Stats row */
.blg-stats-row {
  display:flex; justify-content:center; gap:32px; align-items:center;
}
.blg-stat-item { text-align:center; }
.blg-stat-val {
  font-family:'Playfair Display',serif;
  font-size:22px; font-weight:700;
  color:var(--rose); margin-bottom:2px;
}
.blg-stat-lbl {
  font-family:'Lato',sans-serif;
  font-size:10px; font-weight:700;
  letter-spacing:.14em; text-transform:uppercase;
  color:var(--muted);
}
.blg-stat-div { width:1px; height:36px; background:var(--cream-dd); }

/* ══ TICKER ══ */
.blg-ticker {
  background:var(--ink); overflow:hidden; padding:9px 0;
}
.blg-ticker-inner {
  display:flex; white-space:nowrap;
  animation:blgTicker 22s linear infinite;
}
.blg-ticker-item {
  display:inline-flex; align-items:center; gap:10px;
  margin:0 20px;
  font-family:'Lato',sans-serif;
  font-size:10.5px; font-weight:700;
  letter-spacing:.14em; text-transform:uppercase;
  color:rgba(253,246,238,.3);
  text-decoration:none; flex-shrink:0;
  transition:color .2s;
}
.blg-ticker-item:hover { color:var(--gold-l); }
.blg-ticker-dot {
  width:3px; height:3px; border-radius:50%;
  background:var(--rose); opacity:.7; flex-shrink:0;
}

/* ══ BODY ══ */
.blg-body {
  background:var(--cream);
  padding:48px 0 72px;
  position:relative;
}
.blg-body::before {
  content:'';
  position:absolute; inset:0; pointer-events:none;
  background-image:
    repeating-linear-gradient(0deg,  transparent 0,transparent 47px, rgba(225,29,72,.022) 47px, rgba(225,29,72,.022) 48px),
    repeating-linear-gradient(90deg, transparent 0,transparent 47px, rgba(225,29,72,.022) 47px, rgba(225,29,72,.022) 48px);
}
.blg-container {
  position:relative; z-index:1;
  max-width:1280px; margin:0 auto; padding:0 24px;
}
.blg-layout {
  display:grid;
  grid-template-columns:1fr 268px;
  gap:44px; align-items:start;
}

/* ══ FILTER PILLS ══ */
.blg-filter-bar {
  display:flex; gap:6px; flex-wrap:wrap;
  margin-bottom:24px; padding-bottom:20px;
  border-bottom:1px solid var(--cream-dd);
}
.blg-pill {
  font-family:'Lato',sans-serif;
  font-size:11px; font-weight:700;
  padding:5px 14px; border-radius:20px;
  text-transform:uppercase; letter-spacing:.07em;
  text-decoration:none;
  border:1px solid var(--cream-dd); color:var(--muted);
  background:transparent;
  transition:all .22s ease;
}
.blg-pill:hover { color:var(--rose); border-color:rgba(225,29,72,.3); background:rgba(225,29,72,.05); }
.blg-pill.active { background:var(--ink); color:var(--cream); border-color:var(--ink); }

/* ══ ARTIKEL — horizontal card (struktur gen 1) ══ */
.blg-divider {
  height:1px;
  background:linear-gradient(90deg,rgba(225,29,72,.3),transparent);
  margin-bottom:8px;
}

.blg-article-card {
  display:flex; flex-direction:row; align-items:stretch;
  padding:20px 0; border-bottom:1px solid rgba(225,29,72,.08);
  transition:background .25s ease, padding-left .2s, padding-right .2s, border-radius .2s;
}
.blg-article-card:hover {
  background:rgba(225,29,72,.03);
  border-radius:10px;
  padding-left:10px; padding-right:10px;
  margin:0 -10px;
}

/* Thumb */
.blg-thumb-link {
  flex-shrink:0; width:195px; height:135px;
  border-radius:10px; overflow:hidden;
  position:relative; display:block;
  background:var(--cream-d);
  border:1px solid var(--cream-dd);
}
.blg-thumb-link img {
  width:100%; height:100%; object-fit:cover; display:block;
  transition:transform .6s cubic-bezier(.4,0,.2,1);
}
.blg-article-card:hover .blg-thumb-link img { transform:scale(1.07); }
.blg-read-badge {
  position:absolute; bottom:8px; right:8px;
  background:rgba(31,41,55,.65); color:var(--gold-l);
  font-family:'Lato',sans-serif;
  font-size:9px; font-weight:700;
  padding:2px 8px; border-radius:20px;
  backdrop-filter:blur(4px);
}

/* Body artikel */
.blg-article-body {
  flex:1; padding-left:18px;
  display:flex; flex-direction:column;
  justify-content:space-between; min-width:0;
}
.blg-cat-badge-inline {
  font-family:'Lato',sans-serif;
  font-size:10px; font-weight:700;
  padding:3px 10px; border-radius:20px;
  text-transform:uppercase; letter-spacing:.05em;
  text-decoration:none;
  background:rgba(225,29,72,.08);
  border:1px solid rgba(225,29,72,.2);
  color:var(--rose);
  transition:all .2s;
}
.blg-cat-badge-inline:hover { background:var(--rose); color:#fff; }
.blg-char-badge {
  font-family:'Lato',sans-serif;
  font-size:10px; color:var(--muted);
  padding:2px 8px; border-radius:20px;
  background:rgba(225,29,72,.04);
  border:1px solid var(--cream-dd);
}
.blg-article-title {
  font-family:'Playfair Display',serif;
  font-size:16px; font-weight:700;
  color:var(--ink); line-height:1.35;
  margin-bottom:6px;
  display:-webkit-box;
  -webkit-line-clamp:2; -webkit-box-orient:vertical;
  line-clamp:2; overflow:hidden;
  text-decoration:none;
  transition:color .2s;
}
.blg-article-title:hover { color:var(--rose); }
.blg-article-excerpt {
  font-family:'Lato',sans-serif;
  font-size:12px; font-weight:300; color:var(--muted);
  line-height:1.65; margin:0;
  display:-webkit-box;
  -webkit-line-clamp:2; -webkit-box-orient:vertical;
  line-clamp:2; overflow:hidden;
}
.blg-article-more {
  font-family:'Lato',sans-serif;
  font-size:11px; font-weight:700;
  color:var(--rose); text-decoration:none;
  letter-spacing:.02em;
  transition:color .2s;
}
.blg-article-more:hover { color:#be123c; }
.blg-article-date {
  font-family:'Lato',sans-serif;
  font-size:11px; color:rgba(31,41,55,.3);
}

/* ══ PAGINATION ══ */
.blg-page-btn {
  width:36px; height:36px; border-radius:50%;
  display:flex; align-items:center; justify-content:center;
  font-family:'Lato',sans-serif;
  font-size:13px; font-weight:700;
  text-decoration:none;
  border:1px solid var(--cream-dd); color:var(--muted);
  background:var(--paper);
  transition:all .2s;
}
.blg-page-btn:hover, .blg-page-btn.active {
  background:var(--ink); color:var(--cream); border-color:var(--ink);
}

/* ══ SIDEBAR ══ */
.sb-card {
  background:var(--paper);
  border:1px solid var(--cream-dd);
  border-radius:16px; overflow:hidden;
  box-shadow:0 4px 20px rgba(225,29,72,.04);
}
.sb-head {
  padding:13px 18px 10px;
  border-bottom:1px solid var(--cream-dd);
  background:rgba(225,29,72,.03);
}
.sb-head-label {
  font-family:'Lato',sans-serif;
  font-size:9px; font-weight:700;
  text-transform:uppercase; letter-spacing:.14em;
  color:rgba(225,29,72,.45); margin-bottom:3px;
}
.sb-head-title {
  font-family:'Playfair Display',serif;
  font-size:16px; font-weight:700; color:var(--ink);
}
.sb-link {
  display:flex; align-items:center; justify-content:space-between;
  padding:8px 12px; border-radius:10px; text-decoration:none;
  margin-bottom:2px; transition:background .2s, color .2s;
  font-family:'Lato',sans-serif;
  font-size:12px; font-weight:400; color:var(--muted);
}
.sb-link:hover, .sb-link.active { background:rgba(225,29,72,.07); color:var(--rose); }
.sb-link.active { font-weight:700; }
.sb-badge {
  font-family:'Lato',sans-serif;
  font-size:10px; background:rgba(225,29,72,.06);
  padding:2px 8px; border-radius:999px; color:var(--muted);
}
.sb-dot {
  width:5px; height:5px; border-radius:50%;
  background:rgba(225,29,72,.25); display:inline-block; flex-shrink:0;
}
.sb-accent-btn {
  display:block; background:var(--ink);
  color:var(--cream); font-family:'Lato',sans-serif;
  font-size:12px; font-weight:700; padding:11px;
  border-radius:999px; text-decoration:none; text-align:center;
  letter-spacing:.04em; box-shadow:0 4px 16px rgba(31,41,55,.16);
  transition:opacity .2s, transform .2s;
}
.sb-accent-btn:hover { opacity:.85; transform:translateY(-1px); }

.sb-recent-item {
  display:flex; gap:10px; align-items:flex-start;
  padding:9px 0; border-bottom:1px solid var(--cream-dd);
  text-decoration:none; transition:opacity .2s;
}
.sb-recent-item:hover { opacity:.75; }
.sb-recent-item:last-child { border-bottom:none; }

.sb-prod-item {
  display:flex; align-items:center; gap:10px; padding:8px 10px;
  border-radius:10px; text-decoration:none; margin-bottom:2px;
  transition:background .2s;
}
.sb-prod-item:hover { background:rgba(225,29,72,.05); }

.area-pill-sb {
  font-family:'Lato',sans-serif;
  font-size:11px; font-weight:400;
  color:var(--muted); text-decoration:none;
  padding:5px 0; display:flex; align-items:center; gap:8px;
  transition:color .2s;
}
.area-pill-sb:hover { color:var(--rose); }

/* ══ RESPONSIVE ══ */
@media(max-width:1023px) {
  .blg-layout { grid-template-columns:1fr !important; }
  .blg-sidebar { display:none; }
}
@media(max-width:640px) {
  .blg-thumb-link { width:110px; height:110px; }
  .blg-hero { padding-top:80px; }
}
</style>

<!-- ── Kelopak jatuh ── -->
<div style="position:fixed;inset:0;pointer-events:none;overflow:hidden;z-index:9998;" aria-hidden="true">
<?php
$petal_cols = ['#e11d48','#fca5a5','#c9a84c','#fde8b4','#fdf0f3'];
for ($i = 0; $i < 9; $i++):
  $col = $petal_cols[$i % count($petal_cols)];
  $left = rand(2,97); $del = rand(0,18); $dur = rand(14,24); $sz = rand(6,11);
?>
<div class="blg-petal" style="left:<?= $left ?>%;top:0;width:<?= $sz ?>px;height:<?= round($sz*1.4) ?>px;background:<?= $col ?>;opacity:.18;animation-duration:<?= $dur ?>s;animation-delay:-<?= $del ?>s;"></div>
<?php endfor; ?>
</div>

<!-- ════════════ HERO ════════════ -->
<section class="blg-hero">
  <div class="blg-hero-dots"></div>

  <!-- Float petals dekoratif -->
  <?php
  $fp = ['🌸','🌺','✦','❋','🌷'];
  for ($i = 0; $i < 10; $i++):
    $top = rand(5,90); $left = rand(3,95);
    $dur = rand(6,14); $del = rand(0,8);
  ?>
  <span class="blg-float-petal" style="top:<?= $top ?>%;left:<?= $left ?>%;--dur:<?= $dur ?>s;--del:<?= $del ?>s;"><?= $fp[$i%5] ?></span>
  <?php endfor; ?>

  <!-- Glow blobs -->
  <div style="position:absolute;top:-60px;right:-80px;width:480px;height:480px;background:radial-gradient(circle,rgba(225,29,72,.18),transparent 65%);filter:blur(70px);pointer-events:none;z-index:0;"></div>
  <div style="position:absolute;bottom:-40px;left:-60px;width:360px;height:360px;background:radial-gradient(circle,rgba(201,168,76,.1),transparent 65%);filter:blur(80px);pointer-events:none;z-index:0;"></div>

  <!-- SVG Bunga dekoratif TL -->
  <svg style="position:absolute;top:-8px;left:-8px;opacity:.07;z-index:4;pointer-events:none;" width="180" height="180" viewBox="0 0 180 180" fill="none">
    <?php foreach([0,60,120,180,240,300] as $r): ?>
    <ellipse cx="90" cy="48" rx="16" ry="34" fill="#e11d48" transform="rotate(<?=$r?> 90 90)"/>
    <?php endforeach; ?>
    <circle cx="90" cy="90" r="18" fill="#f9c784"/>
  </svg>
  <!-- SVG Bunga dekoratif TR -->
  <svg style="position:absolute;top:-5px;right:-5px;opacity:.065;z-index:4;pointer-events:none;transform:scaleX(-1) rotate(15deg);" width="150" height="150" viewBox="0 0 150 150" fill="none">
    <?php foreach([0,72,144,216,288] as $r): ?>
    <ellipse cx="75" cy="38" rx="12" ry="26" fill="#c9a84c" transform="rotate(<?=$r?> 75 75)"/>
    <?php endforeach; ?>
    <circle cx="75" cy="75" r="14" fill="#fca5a5"/>
  </svg>

  <div class="blg-hero-inner">

    <!-- Badge live -->
    <div class="blg-badge blg-rv1">
      <div class="blg-badge-dot"></div>
      <span class="blg-badge-text">Artikel &amp; Inspirasi Bunga</span>
    </div>

    <!-- Judul -->
    <h1 class="blg-h1 blg-rv2">
      Blog <span class="blg-h1-accent">Florist</span>
    </h1>
    <p class="blg-eyebrow blg-rv2"><?= clean(getSetting('site_name','Chika Florist')) ?></p>
    <div class="blg-gold-divider"></div>
    <p class="blg-tagline blg-rv2">Tips, Inspirasi &amp; Cerita Bunga</p>

    <p class="blg-desc blg-rv3">
      Pelajari cara merawat bunga, temukan inspirasi rangkaian untuk setiap momen, dan ikuti cerita di balik layar florist kami.
    </p>

    <!-- Search -->
    <form class="blg-search-form blg-rv3" method="GET" action="<?= BASE_URL ?>/blog">
      <input type="text" name="q" value="<?= clean($search) ?>"
             placeholder="Cari artikel, tips, inspirasi..."
             class="blg-search-input">
      <?php if ($filter_cat): ?>
      <input type="hidden" name="kategori" value="<?= clean($filter_cat) ?>">
      <?php endif; ?>
      <button type="submit" class="blg-search-btn">Cari</button>
    </form>

    <!-- Stats row -->
    <div class="blg-stats-row blg-rv3">
      <div class="blg-stat-item">
        <div class="blg-stat-val"><?= $total ?></div>
        <div class="blg-stat-lbl">Artikel</div>
      </div>
      <div class="blg-stat-div"></div>
      <div class="blg-stat-item">
        <div class="blg-stat-val"><?= count($blog_cats) ?></div>
        <div class="blg-stat-lbl">Kategori</div>
      </div>
      <div class="blg-stat-div"></div>
      <div class="blg-stat-item">
        <div class="blg-stat-val" style="color:var(--gold);">Gratis</div>
        <div class="blg-stat-lbl">Untuk Semua</div>
      </div>
    </div>

  </div>

  <!-- Wave bawah -->
  <div style="position:absolute;bottom:-1px;left:0;right:0;z-index:8;line-height:0;">
    <svg viewBox="0 0 1440 56" fill="none" xmlns="http://www.w3.org/2000/svg" preserveAspectRatio="none" style="width:100%;height:56px;display:block;">
      <path d="M0,20 C360,56 720,0 1080,28 C1260,42 1380,14 1440,22 L1440,56 L0,56 Z" fill="var(--cream)"/>
    </svg>
  </div>
  <div class="blg-hero-strip"></div>
</section>

<!-- ════════════ TICKER ════════════ -->
<div class="blg-ticker" aria-label="Kategori artikel">
  <div class="blg-ticker-inner" aria-hidden="true">
    <?php for ($r = 0; $r < 2; $r++): foreach ($blog_cats as $bc): ?>
    <a href="<?= BASE_URL ?>/blog?kategori=<?= clean($bc['slug']) ?>"
       class="blg-ticker-item <?= $filter_cat === $bc['slug'] ? 'active' : '' ?>">
      <span class="blg-ticker-dot"></span>
      <?= clean($bc['name']) ?>
      <span style="opacity:.3;font-size:9px;">(<?= $bc['total'] ?>)</span>
    </a>
    <?php endforeach; endfor; ?>
  </div>
</div>

<!-- ════════════ BODY ════════════ -->
<section class="blg-body">
  <div class="blg-container">
    <div class="blg-layout">

      <!-- ── ARTIKEL ── -->
      <div style="min-width:0;">

        <!-- Filter pills -->
        <div class="blg-filter-bar">
          <a href="<?= BASE_URL ?>/blog" class="blg-pill <?= !$filter_cat ? 'active' : '' ?>">Semua</a>
          <?php foreach ($blog_cats as $bc): ?>
          <a href="<?= BASE_URL ?>/blog?kategori=<?= clean($bc['slug']) ?>"
             class="blg-pill <?= $filter_cat === $bc['slug'] ? 'active' : '' ?>">
            <?= clean($bc['name']) ?> <span style="opacity:.5;">(<?= $bc['total'] ?>)</span>
          </a>
          <?php endforeach; ?>
        </div>

        <!-- Hasil search -->
        <?php if ($search): ?>
        <div style="display:flex;align-items:center;gap:10px;margin-bottom:22px;padding:12px 16px;background:rgba(225,29,72,.05);border:1px solid rgba(225,29,72,.15);border-radius:7px;">
          <span style="font-family:'Lato',sans-serif;font-size:13px;color:var(--muted);">
            Hasil: <strong style="color:var(--rose);">"<?= clean($search) ?>"</strong> — <?= $total ?> artikel
          </span>
          <a href="<?= BASE_URL ?>/blog"
             style="font-family:'Lato',sans-serif;font-size:11px;font-weight:700;color:var(--rose);text-decoration:none;margin-left:auto;background:rgba(225,29,72,.08);padding:4px 12px;border-radius:4px;">
            Reset ✕
          </a>
        </div>
        <?php endif; ?>

        <?php if (empty($blogs)): ?>
        <!-- Empty state -->
        <div style="text-align:center;padding:80px 0;">
          <div style="font-size:54px;margin-bottom:16px;">🌸</div>
          <p style="font-family:'Playfair Display',serif;font-size:1.4rem;font-weight:700;color:var(--ink);margin-bottom:8px;">Belum ada artikel ditemukan</p>
          <p style="font-family:'Lato',sans-serif;font-size:14px;color:var(--muted);">Coba kategori atau kata kunci lain</p>
        </div>

        <?php else: ?>

        <?php
        function blgThumb(array $blog): string {
          if (!empty($blog['thumbnail']) && file_exists(UPLOAD_DIR . $blog['thumbnail']))
            return UPLOAD_URL . $blog['thumbnail'];
          return 'https://images.unsplash.com/photo-1487530811015-780780dde0e4?w=400&h=280&fit=crop';
        }
        ?>

        <!-- Divider -->
        <div class="blg-divider"></div>

        <!-- List artikel -->
        <div style="display:flex;flex-direction:column;">
          <?php foreach ($blogs as $blog):
            $thumb      = blgThumb($blog);
            $txt        = strip_tags($blog['content'] ?? '');
            $char_count = mb_strlen($txt);
            $char_label = $char_count >= 1000 ? round($char_count/1000,1).'k karakter' : $char_count.' karakter';
            $read_min   = max(1, ceil($char_count / 1000));
            $updated    = $blog['updated_at'] ?? $blog['created_at'];
          ?>
          <article class="blg-article-card">

            <!-- Thumb -->
            <a href="<?= BASE_URL ?>/blog/<?= clean($blog['slug']) ?>" class="blg-thumb-link">
              <img src="<?= clean($thumb) ?>" alt="<?= clean($blog['title']) ?>" loading="lazy">
              <span class="blg-read-badge"><?= $read_min ?> mnt</span>
            </a>

            <!-- Body -->
            <div class="blg-article-body">
              <div>
                <!-- Badges atas -->
                <div style="display:flex;gap:6px;align-items:center;flex-wrap:wrap;margin-bottom:8px;">
                  <?php if ($blog['cat_name']): ?>
                  <a href="<?= BASE_URL ?>/blog?kategori=<?= clean($blog['cat_slug']) ?>"
                     class="blg-cat-badge-inline">
                    <?= clean($blog['cat_name']) ?>
                  </a>
                  <?php endif; ?>
                  <span class="blg-char-badge"><?= $char_label ?></span>
                </div>

                <!-- Judul -->
                <a href="<?= BASE_URL ?>/blog/<?= clean($blog['slug']) ?>" class="blg-article-title">
                  <?= clean($blog['title']) ?>
                </a>

                <!-- Excerpt -->
                <?php if ($blog['excerpt']): ?>
                <p class="blg-article-excerpt"><?= clean($blog['excerpt']) ?></p>
                <?php endif; ?>
              </div>

              <!-- Meta bawah -->
              <div style="display:flex;align-items:center;gap:10px;margin-top:10px;flex-wrap:wrap;">
                <span class="blg-article-date">Diperbarui <?= date('d M Y', strtotime($updated)) ?></span>
                <span style="width:3px;height:3px;border-radius:50%;background:var(--cream-dd);"></span>
                <a href="<?= BASE_URL ?>/blog/<?= clean($blog['slug']) ?>" class="blg-article-more">
                  Baca selengkapnya →
                </a>
              </div>
            </div>

          </article>
          <?php endforeach; ?>
        </div>

        <!-- Pagination -->
        <?php if ($total_page > 1): ?>
        <div style="display:flex;justify-content:center;align-items:center;gap:6px;margin-top:40px;flex-wrap:wrap;">
          <?php if ($page > 1):
            $qa = array_filter(['kategori'=>$filter_cat,'q'=>$search,'page'=>$page-1>1?$page-1:null]);
            $qs = $qa ? '?'.http_build_query($qa) : '';
          ?>
          <a href="<?= BASE_URL ?>/blog<?= $qs ?>" class="blg-page-btn">‹</a>
          <?php endif; ?>

          <?php for ($p = 1; $p <= $total_page; $p++):
            $qa = array_filter(['kategori'=>$filter_cat,'q'=>$search,'page'=>$p>1?$p:null]);
            $qs = $qa ? '?'.http_build_query($qa) : '';
          ?>
          <a href="<?= BASE_URL ?>/blog<?= $qs ?>" class="blg-page-btn <?= $p === $page ? 'active' : '' ?>"><?= $p ?></a>
          <?php endfor; ?>

          <?php if ($page < $total_page):
            $qa = array_filter(['kategori'=>$filter_cat,'q'=>$search,'page'=>$page+1]);
            $qs = '?'.http_build_query($qa);
          ?>
          <a href="<?= BASE_URL ?>/blog<?= $qs ?>" class="blg-page-btn">›</a>
          <?php endif; ?>
        </div>
        <?php endif; ?>

        <?php endif; ?>
      </div>
      <!-- /artikel -->

      <!-- ── SIDEBAR DESKTOP ── -->
      <aside class="blg-sidebar" style="position:sticky;top:96px;">
        <?php include __DIR__ . '/sections/blog-sidebar.php'; ?>
      </aside>

    </div>
  </div>
</section>

<!-- Sidebar mobile -->
<div id="blg-sidebar-mobile-wrap">
  <?php include __DIR__ . '/sections/blog-sidebar-mobile.php'; ?>
</div>
<style>@media(min-width:1024px){#blg-sidebar-mobile-wrap{display:none !important;}}</style>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>