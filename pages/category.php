<?php
require_once __DIR__ . '/../includes/config.php';
$slug = $_GET['slug'] ?? '';
$pdo  = getDB();
$stmt = $pdo->prepare("SELECT * FROM categories WHERE slug=? AND is_active=1");
$stmt->execute([$slug]);
$category = $stmt->fetch();
if (!$category) { http_response_code(404); require __DIR__ . '/404.php'; exit(); }

$subCats = getSubCategories($category['id']);
$parent  = null;
if ($category['parent_id']) {
    $s2 = $pdo->prepare("SELECT * FROM categories WHERE id=?");
    $s2->execute([$category['parent_id']]);
    $parent = $s2->fetch();
}

$cat_ids = [$category['id']];
foreach ($subCats as $sc) $cat_ids[] = $sc['id'];
$placeholders = implode(',', array_fill(0, count($cat_ids), '?'));
$s3 = $pdo->prepare("SELECT p.*,c.name as cat_name,c.slug as cat_slug FROM products p JOIN categories c ON p.category_id=c.id WHERE p.category_id IN ($placeholders) AND p.is_active=1 ORDER BY p.is_featured DESC, p.sort_order ASC");
$s3->execute($cat_ids);
$products = $s3->fetchAll();
$cities   = getActiveCities(12);

$nama = clean($category['name']);
$page_title    = $category['meta_title'] ?: "{$nama} 24 Jam | Florist Online – Chika Florist";
$meta_desc     = $category['meta_desc'] ?: "Pesan {$nama} online 24 jam. Tersedia berbagai pilihan desain elegan dengan pengiriman cepat ke seluruh Indonesia.";
$canonical_url = BASE_URL . '/' . $category['slug'];

$breadcrumbs = [['label'=>'Beranda','url'=>'/']];
if ($parent) $breadcrumbs[] = ['label'=>$parent['name'],'url'=>'/'.$parent['slug']];
$breadcrumbs[] = ['label'=>$nama];
require_once __DIR__ . '/../includes/header.php';
?>

<section class="bg-gradient-to-br from-rose-50 to-amber-50 py-12 px-4 border-b border-rose-100">
  <div class="max-w-4xl mx-auto text-center">
    <?php if ($parent): ?>
    <p class="text-rose-500 text-sm font-semibold mb-1"><a href="<?= BASE_URL ?>/<?= $parent['slug'] ?>" class="hover:underline"><?= clean($parent['name']) ?></a></p>
    <?php endif; ?>
    <h1 class="font-display text-3xl sm:text-4xl font-bold text-gray-900 mb-4"><?= $nama ?></h1>
    <p class="text-gray-600 leading-relaxed max-w-2xl mx-auto"><?= clean($category['description'] ?? "Temukan berbagai pilihan {$nama} berkualitas dari Chika Florist untuk berbagai acara.") ?></p>
  </div>
</section>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">

  <?php if (!empty($subCats)): ?>
  <div class="mb-10">
    <h2 class="font-display text-xl font-bold text-gray-800 mb-4">Jenis <?= $nama ?></h2>
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4">
      <?php foreach ($subCats as $sc): ?>
      <a href="<?= BASE_URL ?>/<?= $sc['slug'] ?>" class="card-hover bg-white rounded-xl overflow-hidden border border-amber-100 hover:border-rose-200 p-4 text-center group">
        <div class="h-28 overflow-hidden rounded-lg bg-rose-50 mb-3">
          <img src="<?= UPLOAD_URL.($sc['image']??'') ?>" alt="<?= clean($sc['name']) ?>" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300" onerror="this.style.display='none'">
        </div>
        <h3 class="font-semibold text-gray-800 text-sm group-hover:text-rose-600 transition-colors"><?= clean($sc['name']) ?></h3>
      </a>
      <?php endforeach; ?>
    </div>
  </div>
  <?php endif; ?>

  <?php if (!empty($products)): ?>
  <h2 class="font-display text-xl font-bold text-gray-800 mb-6"><?= count($products) ?> Produk <?= $nama ?></h2>
  <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4 sm:gap-5">
    <?php foreach ($products as $prod): ?>
    <div class="card-hover bg-white rounded-xl overflow-hidden border border-amber-100 hover:border-rose-200 group">
      <div class="h-48 overflow-hidden bg-rose-50 relative">
        <img src="<?= UPLOAD_URL.($prod['image']??'') ?>" alt="<?= clean($prod['name']) ?>" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300" onerror="this.style.display='none'">
        <?php if ($prod['is_featured']): ?><span class="absolute top-2 left-2 bg-amber-400 text-amber-900 text-xs font-bold px-2 py-0.5 rounded-full">Terlaris</span><?php endif; ?>
      </div>
      <div class="p-4">
        <p class="text-xs text-rose-500 font-medium mb-1"><?= clean($prod['cat_name']) ?></p>
        <h3 class="font-semibold text-gray-800 text-sm leading-snug mb-2"><?= clean($prod['name']) ?></h3>
        <p class="text-xs text-gray-500 mb-2 line-clamp-2"><?= clean($prod['short_desc'] ?? '') ?></p>
        <p class="text-rose-600 font-bold text-sm mb-3"><?= formatHarga($prod['price_min'],$prod['price_max']) ?></p>
        <a href="<?= waLink('Halo, saya ingin pesan '.$prod['name']) ?>" target="_blank"
           class="block w-full text-center bg-green-50 hover:bg-green-500 text-green-600 hover:text-white text-xs font-semibold py-2 rounded-lg border border-green-200 hover:border-green-500 transition-colors">
          Pesan Sekarang
        </a>
      </div>
    </div>
    <?php endforeach; ?>
  </div>
  <?php else: ?>
  <div class="text-center py-16 text-gray-400"><p>Belum ada produk di kategori ini.</p></div>
  <?php endif; ?>

  <div class="seo-content mt-12 max-w-4xl">
    <h2>Tentang <?= $nama ?> Chika Florist</h2>
    <p>Chika Florist menyediakan berbagai pilihan <?= $nama ?> berkualitas untuk kebutuhan acara Anda. Setiap produk dibuat dari bunga segar pilihan oleh tim florist profesional.</p>
    <h2><?= $nama ?> untuk Berbagai Acara</h2>
    <ul>
      <li>Wedding &amp; Lamaran</li><li>Duka Cita &amp; Belasungkawa</li><li>Ulang Tahun</li>
      <li>Wisuda</li><li>Grand Opening</li><li>Anniversary</li><li>Corporate Event</li>
    </ul>
    <h2>Cara Pesan <?= $nama ?></h2>
    <p>1. Pilih produk <?= $nama ?>. 2. Hubungi admin via WhatsApp. 3. Kirim detail ucapan dan alamat. 4. Pesanan diproses dan langsung dikirim.</p>
    <p>Produk ini dapat dipesan melalui layanan <a href="/">toko bunga online 24 jam Indonesia</a> Chika Florist.</p>
  </div>

  <?php if (!empty($cities)): ?>
  <div class="mt-8 p-5 bg-amber-50 rounded-xl border border-amber-100">
    <p class="text-xs text-gray-500 uppercase tracking-wider font-semibold mb-3"><?= $nama ?> Tersedia di</p>
    <div class="internal-links">
      <?php foreach ($cities as $city): ?>
      <a href="<?= BASE_URL ?>/toko-bunga-<?= $city['slug'] ?>"><?= $nama ?> <?= clean($city['name']) ?></a>
      <?php endforeach; ?>
    </div>
  </div>
  <?php endif; ?>
</div>

<section class="bg-rose-600 py-10 px-4 text-center">
  <h2 class="font-display text-xl font-bold text-white mb-2">Pesan <?= $nama ?> Sekarang</h2>
  <p class="text-rose-100 text-sm mb-5">Hubungi kami untuk konsultasi dan pemesanan terbaik.</p>
  <a href="<?= waLink("Halo, saya ingin pesan {$nama}") ?>" target="_blank"
     class="inline-flex items-center gap-2 bg-white text-rose-600 font-bold px-7 py-3 rounded-full hover:bg-rose-50 transition-colors">Pesan via WhatsApp</a>
</section>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
