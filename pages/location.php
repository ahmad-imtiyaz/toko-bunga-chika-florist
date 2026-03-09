<?php
require_once __DIR__ . '/../includes/config.php';
$pdo    = getDB();
$cities = $pdo->query("SELECT * FROM cities WHERE is_active=1 ORDER BY tier ASC, sort_order ASC, name ASC")->fetchAll();
$page_title    = 'Area Layanan | Toko Bunga Online Seluruh Indonesia – Chika Florist';
$meta_desc     = 'Chika Florist melayani pengiriman bunga ke seluruh kota di Indonesia. Lihat daftar area layanan kami.';
$canonical_url = BASE_URL . '/area-layanan';
$breadcrumbs   = [['label'=>'Beranda','url'=>'/'],['label'=>'Area Layanan']];
require_once __DIR__ . '/../includes/header.php';
?>
<section class="bg-gradient-to-br from-rose-50 to-amber-50 py-12 px-4 border-b border-rose-100">
  <div class="max-w-4xl mx-auto text-center">
    <h1 class="font-display text-3xl sm:text-4xl font-bold text-gray-900 mb-3">Area Layanan Chika Florist</h1>
    <p class="text-gray-500">Layanan pengiriman bunga ke <?= count($cities) ?>+ kota di seluruh Indonesia</p>
  </div>
</section>
<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
  <?php if (!empty($cities)): ?>
  <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-3">
    <?php foreach ($cities as $city): ?>
    <a href="<?= BASE_URL ?>/toko-bunga-<?= $city['slug'] ?>"
       class="card-hover bg-white hover:bg-rose-50 border border-amber-100 hover:border-rose-200 rounded-xl p-4 transition-colors group">
      <p class="font-semibold text-gray-800 group-hover:text-rose-600 text-sm transition-colors">Toko Bunga <?= clean($city['name']) ?></p>
      <?php if ($city['province']): ?><p class="text-xs text-gray-400 mt-0.5"><?= clean($city['province']) ?></p><?php endif; ?>
    </a>
    <?php endforeach; ?>
  </div>
  <?php else: ?>
  <div class="text-center py-16 text-gray-400">
    <p>Belum ada area layanan yang ditambahkan.</p>
    <p class="text-sm mt-2">Silakan tambahkan kota di panel admin.</p>
  </div>
  <?php endif; ?>
  <div class="mt-8 text-center">
    <p class="text-gray-500 text-sm">Kota Anda tidak ada di daftar? <a href="<?= waLink('Halo, saya ingin tanya apakah bisa kirim bunga ke kota saya') ?>" target="_blank" class="text-rose-600 hover:underline font-semibold">Hubungi kami</a>.</p>
  </div>
</div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
