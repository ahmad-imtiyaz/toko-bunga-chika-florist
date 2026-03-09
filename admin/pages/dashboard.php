<?php
require_once __DIR__ . '/../../includes/config.php';
requireAdminLogin();

$pdo = getDB();
$b   = BASE_URL;

function safeCount($pdo, $sql) {
    try { return (int)$pdo->query($sql)->fetchColumn(); }
    catch (Exception $e) { return 0; }
}
function safeFetch($pdo, $sql) {
    try { return $pdo->query($sql)->fetchAll(); }
    catch (Exception $e) { return []; }
}

$stats = [
    'produk'     => safeCount($pdo, "SELECT COUNT(*) FROM products WHERE is_active=1"),
    'produk_all' => safeCount($pdo, "SELECT COUNT(*) FROM products"),
    'kategori'   => safeCount($pdo, "SELECT COUNT(*) FROM categories WHERE is_active=1"),
    'kota'       => safeCount($pdo, "SELECT COUNT(*) FROM cities WHERE is_active=1"),
    'area'       => safeCount($pdo, "SELECT COUNT(*) FROM areas WHERE is_active=1"),
    'testimoni'  => safeCount($pdo, "SELECT COUNT(*) FROM testimonials WHERE is_active=1"),
    'galeri'     => safeCount($pdo, "SELECT COUNT(*) FROM gallery WHERE is_active=1"),
    'faq'        => safeCount($pdo, "SELECT COUNT(*) FROM faqs WHERE is_active=1"),
];

$featuredCount = safeCount($pdo, "SELECT COUNT(*) FROM products WHERE is_featured=1 AND is_active=1");
$emptyCats     = safeCount($pdo, "SELECT COUNT(*) FROM categories c WHERE c.is_active=1 AND (SELECT COUNT(*) FROM products WHERE category_id=c.id AND is_active=1)=0");

$avgRating = 0;
try { $avgRating = round((float)$pdo->query("SELECT AVG(rating) FROM testimonials WHERE is_active=1")->fetchColumn(), 1); } catch(Exception $e){}

$recentProducts     = safeFetch($pdo, "SELECT p.name,p.price_min,p.price_max,p.is_active,c.name as cat_name FROM products p LEFT JOIN categories c ON p.category_id=c.id ORDER BY p.id DESC LIMIT 5");
$recentTestimonials = safeFetch($pdo, "SELECT customer_name,city,rating,is_active FROM testimonials ORDER BY id DESC LIMIT 4");

$hour  = (int)date('H');
$greet = $hour < 11 ? '🌅 Selamat pagi' : ($hour < 15 ? '☀️ Selamat siang' : ($hour < 18 ? '🌤️ Selamat sore' : '🌙 Selamat malam'));

$admin_title = 'Dashboard';
require_once __DIR__ . '/../includes/admin_header.php';
?>

<div class="mb-6">
  <h1 class="text-xl font-display font-bold text-gray-800"><?= $greet ?>, Admin!</h1>
  <p class="text-sm text-gray-400 mt-0.5"><?= date('l, d F Y') ?> · Berikut ringkasan toko kamu hari ini</p>
</div>

<div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mb-4">
  <a href="<?= $b ?>/admin/produk" class="group bg-white border border-rose-100 rounded-xl p-4 hover:shadow-md hover:border-rose-300 transition-all">
    <div class="flex items-start justify-between mb-3">
      <div class="w-10 h-10 bg-rose-100 rounded-xl flex items-center justify-center text-xl group-hover:scale-110 transition-transform">🌺</div>
      <span class="text-xs text-gray-400">aktif</span>
    </div>
    <div class="text-2xl font-bold font-display text-gray-800"><?= $stats['produk'] ?></div>
    <div class="text-xs text-gray-500 mt-0.5">Produk</div>
    <div class="text-xs text-gray-300 mt-1"><?= $stats['produk_all'] ?> total<?= $featuredCount > 0 ? " · $featuredCount unggulan" : '' ?></div>
  </a>
  <a href="<?= $b ?>/admin/kategori" class="group bg-white border border-amber-100 rounded-xl p-4 hover:shadow-md hover:border-amber-300 transition-all">
    <div class="flex items-start justify-between mb-3">
      <div class="w-10 h-10 bg-amber-100 rounded-xl flex items-center justify-center text-xl group-hover:scale-110 transition-transform">📁</div>
      <span class="text-xs text-gray-400">aktif</span>
    </div>
    <div class="text-2xl font-bold font-display text-gray-800"><?= $stats['kategori'] ?></div>
    <div class="text-xs text-gray-500 mt-0.5">Kategori</div>
    <div class="text-xs <?= $emptyCats > 0 ? 'text-amber-400' : 'text-gray-300' ?> mt-1"><?= $emptyCats > 0 ? "⚠️ $emptyCats tanpa produk" : '✅ Semua berisi produk' ?></div>
  </a>
  <a href="<?= $b ?>/admin/kota" class="group bg-white border border-blue-100 rounded-xl p-4 hover:shadow-md hover:border-blue-300 transition-all">
    <div class="flex items-start justify-between mb-3">
      <div class="w-10 h-10 bg-blue-100 rounded-xl flex items-center justify-center text-xl group-hover:scale-110 transition-transform">🏙️</div>
      <span class="text-xs text-gray-400">aktif</span>
    </div>
    <div class="text-2xl font-bold font-display text-gray-800"><?= $stats['kota'] ?></div>
    <div class="text-xs text-gray-500 mt-0.5">Kota</div>
    <div class="text-xs text-gray-300 mt-1"><?= $stats['area'] ?> area terdaftar</div>
  </a>
  <a href="<?= $b ?>/admin/testimoni" class="group bg-white border border-purple-100 rounded-xl p-4 hover:shadow-md hover:border-purple-300 transition-all">
    <div class="flex items-start justify-between mb-3">
      <div class="w-10 h-10 bg-purple-100 rounded-xl flex items-center justify-center text-xl group-hover:scale-110 transition-transform">💬</div>
      <span class="text-xs text-gray-400">aktif</span>
    </div>
    <div class="text-2xl font-bold font-display text-gray-800"><?= $stats['testimoni'] ?></div>
    <div class="text-xs text-gray-500 mt-0.5">Testimoni</div>
    <div class="text-xs text-gray-300 mt-1"><?= $avgRating > 0 ? '⭐ Rata-rata '.$avgRating.'/5' : '— belum ada rating' ?></div>
  </a>
</div>

<div class="grid grid-cols-3 gap-3 mb-6">
  <a href="<?= $b ?>/admin/area" class="bg-green-50 border border-green-200 rounded-xl p-3 flex items-center gap-3 hover:shadow-sm transition-shadow">
    <div class="w-9 h-9 bg-green-100 rounded-xl flex items-center justify-center text-lg">📍</div>
    <div><div class="font-bold text-gray-800"><?= $stats['area'] ?></div><div class="text-xs text-gray-500">Area</div></div>
  </a>
  <a href="<?= $b ?>/admin/galeri" class="bg-pink-50 border border-pink-200 rounded-xl p-3 flex items-center gap-3 hover:shadow-sm transition-shadow">
    <div class="w-9 h-9 bg-pink-100 rounded-xl flex items-center justify-center text-lg">🖼️</div>
    <div><div class="font-bold text-gray-800"><?= $stats['galeri'] ?></div><div class="text-xs text-gray-500">Galeri</div></div>
  </a>
  <a href="<?= $b ?>/admin/faq" class="bg-violet-50 border border-violet-200 rounded-xl p-3 flex items-center gap-3 hover:shadow-sm transition-shadow">
    <div class="w-9 h-9 bg-violet-100 rounded-xl flex items-center justify-center text-lg">❓</div>
    <div><div class="font-bold text-gray-800"><?= $stats['faq'] ?></div><div class="text-xs text-gray-500">FAQ</div></div>
  </a>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-5 mb-6">
  <div class="lg:col-span-2 bg-white border border-rose-100 rounded-xl overflow-hidden">
    <div class="flex items-center justify-between px-5 py-3.5 border-b border-rose-50">
      <h2 class="font-semibold text-gray-800 text-sm">🌺 Produk Terbaru</h2>
      <a href="<?= $b ?>/admin/produk" class="text-xs text-rose-400 hover:text-rose-600 font-medium">Lihat semua →</a>
    </div>
    <div class="divide-y divide-rose-50">
      <?php if (empty($recentProducts)): ?>
        <div class="px-5 py-8 text-center text-gray-400 text-sm">Belum ada produk</div>
      <?php else: foreach ($recentProducts as $p): ?>
        <div class="flex items-center gap-3 px-5 py-3 hover:bg-rose-50/40 transition-colors">
          <div class="w-8 h-8 bg-rose-100 rounded-lg flex items-center justify-center flex-shrink-0">🌸</div>
          <div class="flex-1 min-w-0">
            <p class="text-sm font-medium text-gray-800 truncate"><?= clean($p['name']) ?></p>
            <p class="text-xs text-gray-400"><?= clean($p['cat_name'] ?? '—') ?></p>
          </div>
          <div class="text-right flex-shrink-0">
            <p class="text-xs font-semibold text-gray-700">
              <?php if (!empty($p['price_min'])): ?>
                Rp <?= number_format($p['price_min'],0,',','.') ?>
                <?php if (!empty($p['price_max']) && $p['price_max'] != $p['price_min']): ?>
                  <span class="text-gray-400">–<?= number_format($p['price_max'],0,',','.') ?></span>
                <?php endif; ?>
              <?php else: ?><span class="text-gray-300">—</span><?php endif; ?>
            </p>
            <span class="text-xs <?= $p['is_active'] ? 'text-green-500' : 'text-gray-300' ?>"><?= $p['is_active'] ? '● Aktif' : '● Nonaktif' ?></span>
          </div>
        </div>
      <?php endforeach; endif; ?>
    </div>
    <div class="px-5 py-3 border-t border-rose-50">
      <a href="<?= $b ?>/admin/produk?action=tambah" class="text-xs text-rose-500 hover:text-rose-700 font-medium">+ Tambah produk baru</a>
    </div>
  </div>

  <div class="bg-white border border-purple-100 rounded-xl overflow-hidden">
    <div class="flex items-center justify-between px-5 py-3.5 border-b border-purple-50">
      <h2 class="font-semibold text-gray-800 text-sm">💬 Testimoni Terbaru</h2>
      <a href="<?= $b ?>/admin/testimoni" class="text-xs text-purple-400 hover:text-purple-600 font-medium">Semua →</a>
    </div>
    <div class="divide-y divide-purple-50">
      <?php if (empty($recentTestimonials)): ?>
        <div class="px-5 py-8 text-center text-gray-400 text-sm">Belum ada testimoni</div>
      <?php else:
        $avatarColors = ['bg-rose-200 text-rose-700','bg-amber-200 text-amber-700','bg-violet-200 text-violet-700','bg-green-200 text-green-700'];
        foreach ($recentTestimonials as $t):
          $initials = strtoupper(mb_substr($t['customer_name'],0,1));
          $color = $avatarColors[abs(crc32($t['customer_name'])) % 4];
      ?>
        <div class="flex items-center gap-3 px-4 py-3">
          <div class="w-8 h-8 rounded-full <?= $color ?> flex items-center justify-center text-xs font-bold flex-shrink-0"><?= $initials ?></div>
          <div class="flex-1 min-w-0">
            <p class="text-xs font-semibold text-gray-800 truncate"><?= clean($t['customer_name']) ?></p>
            <p class="text-xs text-gray-400"><?= clean($t['city'] ?? '—') ?></p>
          </div>
          <div class="flex-shrink-0 text-right">
            <p class="text-xs text-amber-500"><?= str_repeat('★',(int)$t['rating']) ?><span class="text-gray-200"><?= str_repeat('★',5-(int)$t['rating']) ?></span></p>
            <span class="text-xs <?= $t['is_active'] ? 'text-green-500' : 'text-gray-300' ?>"><?= $t['is_active'] ? '● Aktif' : '● Nonaktif' ?></span>
          </div>
        </div>
      <?php endforeach; endif; ?>
    </div>
    <?php if ($avgRating > 0): ?>
    <div class="px-4 py-3 border-t border-purple-50 bg-purple-50/50">
      <p class="text-xs text-purple-600 text-center font-medium">⭐ Rating rata-rata: <strong><?= $avgRating ?> / 5</strong></p>
    </div>
    <?php endif; ?>
  </div>
</div>

<div class="bg-white border border-rose-100 rounded-xl p-5 mb-6">
  <h2 class="font-semibold text-gray-800 text-sm mb-3">⚡ Aksi Cepat</h2>
  <div class="flex flex-wrap gap-2">
    <a href="<?= $b ?>/admin/produk?action=tambah"   class="btn-primary text-xs px-3 py-1.5">🌺 Tambah Produk</a>
    <a href="<?= $b ?>/admin/kategori?action=tambah" class="btn-secondary text-xs px-3 py-1.5">📁 Tambah Kategori</a>
    <a href="<?= $b ?>/admin/kota?action=tambah"     class="btn-secondary text-xs px-3 py-1.5">🏙️ Tambah Kota</a>
    <a href="<?= $b ?>/admin/area?action=tambah"     class="btn-secondary text-xs px-3 py-1.5">📍 Tambah Area</a>
    <a href="<?= $b ?>/admin/galeri?action=tambah"   class="btn-secondary text-xs px-3 py-1.5">🖼️ Upload Galeri</a>
    <a href="<?= $b ?>/admin/faq?action=tambah"      class="btn-secondary text-xs px-3 py-1.5">❓ Tambah FAQ</a>
    <a href="<?= $b ?>/admin/pengaturan"             class="btn-secondary text-xs px-3 py-1.5">⚙️ Pengaturan</a>
    <a href="<?= $b ?>" target="_blank"              class="btn-secondary text-xs px-3 py-1.5">🌐 Lihat Website ↗</a>
  </div>
</div>

<div class="bg-gradient-to-br from-amber-50 to-orange-50 border border-amber-200 rounded-xl p-5">
  <div class="flex items-center justify-between mb-4">
    <h2 class="font-semibold text-amber-800 text-sm">📊 Status SEO Silo</h2>
    <span class="text-xs text-amber-600 bg-amber-100 px-2 py-0.5 rounded-full"><?= $stats['kota']+$stats['area']+$stats['produk'] ?> total halaman SEO</span>
  </div>
  <div class="grid grid-cols-3 gap-3 mb-4">
    <?php
    foreach ([
      ['label'=>'Halaman Kota',   'val'=>$stats['kota'],   'target'=>50,  'color'=>'bg-blue-500'],
      ['label'=>'Halaman Area',   'val'=>$stats['area'],   'target'=>200, 'color'=>'bg-green-500'],
      ['label'=>'Halaman Produk', 'val'=>$stats['produk'], 'target'=>300, 'color'=>'bg-rose-500'],
    ] as $item):
      $pct = min(100, round($item['val']/$item['target']*100));
    ?>
    <div class="bg-white rounded-xl p-3 border border-amber-100">
      <div class="text-xl font-bold text-gray-800"><?= $item['val'] ?></div>
      <div class="text-xs text-gray-500 mb-2"><?= $item['label'] ?></div>
      <div class="h-1.5 bg-gray-100 rounded-full overflow-hidden">
        <div class="h-full <?= $item['color'] ?> rounded-full" style="width:<?= $pct ?>%"></div>
      </div>
      <div class="text-xs text-gray-400 mt-1">Target <?= $item['target'] ?> · <?= $pct ?>%</div>
    </div>
    <?php endforeach; ?>
  </div>
  <p class="text-xs text-amber-600">
    💡 Target: <strong>50+ kota</strong>, <strong>200+ area</strong>, <strong>300+ produk</strong> untuk authority nasional maksimal.
    <?php
    $r = [];
    if ($stats['kota']<50)   $r[] = (50-$stats['kota']).' kota lagi';
    if ($stats['area']<200)  $r[] = (200-$stats['area']).' area lagi';
    if ($stats['produk']<300)$r[] = (300-$stats['produk']).' produk lagi';
    if ($r) echo 'Butuh '.implode(', ',$r).'.';
    ?>
  </p>
</div>

<?php require_once __DIR__ . '/../includes/admin_footer.php'; ?>