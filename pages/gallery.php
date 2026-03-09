<?php
require_once __DIR__ . '/../includes/config.php';
$pdo   = getDB();
$items = $pdo->query("SELECT * FROM gallery WHERE is_active=1 ORDER BY sort_order ASC")->fetchAll();
$page_title    = 'Galeri Bunga | Chika Florist – Toko Bunga Online 24 Jam';
$meta_desc     = 'Galeri karya Chika Florist – bunga papan, buket bunga, standing flower & karangan bunga custom.';
$canonical_url = BASE_URL . '/galeri';
$breadcrumbs   = [['label'=>'Beranda','url'=>'/'],['label'=>'Galeri']];
require_once __DIR__ . '/../includes/header.php';
?>
<section class="bg-gradient-to-br from-rose-50 to-amber-50 py-12 px-4 border-b border-rose-100">
  <div class="max-w-4xl mx-auto text-center">
    <h1 class="font-display text-3xl sm:text-4xl font-bold text-gray-900 mb-3">Galeri Bunga Chika Florist</h1>
    <p class="text-gray-500">Karya terbaik kami untuk berbagai momen spesial</p>
  </div>
</section>
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
  <?php if (!empty($items)): ?>
  <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4">
    <?php foreach ($items as $item): ?>
    <div class="card-hover rounded-xl overflow-hidden border border-amber-100 group bg-white">
      <div class="h-48 overflow-hidden bg-rose-50">
        <img src="<?= UPLOAD_URL . $item['image'] ?>" alt="<?= clean($item['alt_text'] ?: $item['title']) ?>"
             class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300" onerror="this.style.background='#fdf8f0'">
      </div>
      <?php if ($item['title']): ?>
      <div class="p-3">
        <p class="text-sm font-medium text-gray-700"><?= clean($item['title']) ?></p>
        <?php if ($item['category']): ?><p class="text-xs text-rose-500"><?= clean($item['category']) ?></p><?php endif; ?>
      </div>
      <?php endif; ?>
    </div>
    <?php endforeach; ?>
  </div>
  <?php else: ?>
  <div class="text-center py-16 text-gray-400"><p>Belum ada foto di galeri.</p></div>
  <?php endif; ?>
  <div class="text-center mt-10">
    <a href="<?= waLink() ?>" target="_blank" class="inline-flex items-center gap-2 bg-green-500 hover:bg-green-600 text-white font-semibold px-7 py-3 rounded-full transition-colors">Pesan Bunga Sekarang</a>
  </div>
</div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
