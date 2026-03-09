<?php
require_once __DIR__ . '/../includes/config.php';
$page_title = '404 – Halaman Tidak Ditemukan | Chika Florist';
$meta_desc  = 'Halaman yang Anda cari tidak ditemukan.';
require_once __DIR__ . '/../includes/header.php';
?>
<div class="min-h-[60vh] flex flex-col items-center justify-center px-4 py-20 text-center">
  <div class="text-6xl mb-4">🌸</div>
  <h1 class="font-display text-3xl font-bold text-gray-800 mb-3">Halaman Tidak Ditemukan</h1>
  <p class="text-gray-500 mb-8">Maaf, halaman yang Anda cari tidak ada atau telah dipindahkan.</p>
  <div class="flex gap-3">
    <a href="<?= BASE_URL ?>/" class="bg-rose-600 hover:bg-rose-700 text-white font-semibold px-6 py-2.5 rounded-full transition-colors">Kembali ke Beranda</a>
    <a href="<?= waLink() ?>" target="_blank" class="bg-green-500 hover:bg-green-600 text-white font-semibold px-6 py-2.5 rounded-full transition-colors">Hubungi Kami</a>
  </div>
</div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
