<!-- AREA PENGIRIMAN NASIONAL (SILO SEO) -->
<?php if (!empty($cities)): ?>
<section class="section-padding bg-rose-50">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="text-center mb-8">
      <h2 class="font-display text-2xl sm:text-3xl font-bold text-gray-900 mb-2">Area Pengiriman Indonesia</h2>
      <p class="text-gray-500 text-sm">Layanan pengiriman bunga ke <?= count($cities) ?>+ kota di seluruh Indonesia</p>
    </div>
    <div class="internal-links text-center">
      <?php foreach ($cities as $city): ?>
      <a href="<?= BASE_URL ?>/toko-bunga-<?= $city['slug'] ?>">Toko Bunga <?= clean($city['name']) ?></a>
      <?php endforeach; ?>
    </div>
    <div class="text-center mt-6">
      <a href="<?= BASE_URL ?>/area-layanan" class="inline-block text-rose-600 hover:text-rose-700 font-semibold text-sm border-b border-rose-300 transition-colors">Lihat Semua Area Layanan →</a>
    </div>
  </div>
</section>
<?php endif; ?>