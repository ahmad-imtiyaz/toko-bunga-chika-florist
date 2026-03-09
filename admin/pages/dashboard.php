<?php
$admin_title = 'Dashboard';
require_once __DIR__ . '/../includes/admin_header.php';
$pdo = getDB();
$stats = [
    'produk'   => $pdo->query("SELECT COUNT(*) FROM products WHERE is_active=1")->fetchColumn(),
    'kategori' => $pdo->query("SELECT COUNT(*) FROM categories WHERE is_active=1")->fetchColumn(),
    'kota'     => $pdo->query("SELECT COUNT(*) FROM cities WHERE is_active=1")->fetchColumn(),
    'area'     => $pdo->query("SELECT COUNT(*) FROM areas WHERE is_active=1")->fetchColumn(),
    'testimoni'=> $pdo->query("SELECT COUNT(*) FROM testimonials WHERE is_active=1")->fetchColumn(),
    'galeri'   => $pdo->query("SELECT COUNT(*) FROM gallery WHERE is_active=1")->fetchColumn(),
];
$b = BASE_URL;
$cards = [
    ['label'=>'Produk Aktif', 'value'=>$stats['produk'],   'icon'=>'🌺', 'bg'=>'bg-rose-50 border-rose-200',   'text'=>'text-rose-600',   'link'=>"$b/admin/produk"],
    ['label'=>'Kategori',     'value'=>$stats['kategori'], 'icon'=>'📁', 'bg'=>'bg-amber-50 border-amber-200', 'text'=>'text-amber-600',  'link'=>"$b/admin/kategori"],
    ['label'=>'Kota',         'value'=>$stats['kota'],     'icon'=>'🏙️', 'bg'=>'bg-blue-50 border-blue-200',   'text'=>'text-blue-600',   'link'=>"$b/admin/kota"],
    ['label'=>'Area',         'value'=>$stats['area'],     'icon'=>'📍', 'bg'=>'bg-green-50 border-green-200', 'text'=>'text-green-600',  'link'=>"$b/admin/area"],
    ['label'=>'Testimoni',    'value'=>$stats['testimoni'],'icon'=>'💬', 'bg'=>'bg-purple-50 border-purple-200','text'=>'text-purple-600', 'link'=>"$b/admin/testimoni"],
    ['label'=>'Galeri',       'value'=>$stats['galeri'],   'icon'=>'🖼️', 'bg'=>'bg-pink-50 border-pink-200',   'text'=>'text-pink-600',   'link'=>"$b/admin/galeri"],
];
?>
<div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-4 mb-8">
  <?php foreach ($cards as $c): ?>
  <a href="<?= $c['link'] ?>" class="<?= $c['bg'] ?> border rounded-xl p-4 text-center hover:shadow-md transition-shadow">
    <div class="text-2xl mb-2"><?= $c['icon'] ?></div>
    <div class="<?= $c['text'] ?> font-bold text-2xl font-display"><?= $c['value'] ?></div>
    <div class="text-gray-500 text-xs mt-1"><?= $c['label'] ?></div>
  </a>
  <?php endforeach; ?>
</div>

<div class="bg-white rounded-xl border border-rose-100 p-5 mb-6">
  <h2 class="font-display font-bold text-gray-800 mb-4 text-sm">Aksi Cepat</h2>
  <div class="flex flex-wrap gap-2">
    <a href="<?= $b ?>/admin/produk?action=tambah"    class="btn-primary text-xs px-3 py-1.5">+ Tambah Produk</a>
    <a href="<?= $b ?>/admin/kota?action=tambah"      class="btn-secondary text-xs px-3 py-1.5">+ Tambah Kota</a>
    <a href="<?= $b ?>/admin/area?action=tambah"      class="btn-secondary text-xs px-3 py-1.5">+ Tambah Area</a>
    <a href="<?= $b ?>/admin/kategori?action=tambah"  class="btn-secondary text-xs px-3 py-1.5">+ Tambah Kategori</a>
    <a href="<?= $b ?>/admin/galeri?action=tambah"    class="btn-secondary text-xs px-3 py-1.5">+ Upload Galeri</a>
    <a href="<?= $b ?>/admin/pengaturan"              class="btn-secondary text-xs px-3 py-1.5">⚙️ Pengaturan</a>
  </div>
</div>

<div class="bg-amber-50 border border-amber-200 rounded-xl p-5">
  <h2 class="font-display font-bold text-amber-800 mb-3 text-sm">📊 Status SEO Silo</h2>
  <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 text-center">
    <div><div class="text-2xl font-bold text-amber-700"><?= $stats['kota'] ?></div><div class="text-xs text-amber-600">Halaman Kota</div></div>
    <div><div class="text-2xl font-bold text-amber-700"><?= $stats['area'] ?></div><div class="text-xs text-amber-600">Halaman Area</div></div>
    <div><div class="text-2xl font-bold text-amber-700"><?= $stats['produk'] ?></div><div class="text-xs text-amber-600">Halaman Produk</div></div>
    <div><div class="text-2xl font-bold text-amber-700"><?= $stats['kota']+$stats['area']+$stats['produk'] ?></div><div class="text-xs text-amber-600">Total Halaman SEO</div></div>
  </div>
  <p class="text-xs text-amber-600 mt-4">💡 Target: 50+ kota, 200+ area, 300+ produk untuk authority nasional maksimal.</p>
</div>

<?php require_once __DIR__ . '/../includes/admin_footer.php'; ?>
