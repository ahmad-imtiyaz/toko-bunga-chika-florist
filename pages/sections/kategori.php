<!-- KATEGORI -->
<section class="section-padding bg-white">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="text-center mb-10">
      <h2 class="font-display text-2xl sm:text-3xl font-bold text-gray-900 mb-2">Pilihan Produk Bunga Terlengkap</h2>
      <p class="text-gray-500 text-sm">Berbagai rangkaian bunga untuk setiap momen spesial Anda</p>
    </div>
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
      <?php foreach ($categories as $cat): ?>
      <a href="<?= BASE_URL ?>/<?= $cat['slug'] ?>" class="card-hover group bg-amber-50 hover:bg-rose-50 rounded-2xl overflow-hidden border border-amber-100 hover:border-rose-200 transition-colors">
        <div class="h-40 overflow-hidden bg-gradient-to-br from-rose-100 to-amber-100">
          <img src="<?= UPLOAD_URL . ($cat['image'] ?? '') ?>" alt="<?= clean($cat['name']) ?> Chika Florist"
               class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300" onerror="this.style.display='none'">
        </div>
        <div class="p-4">
          <h3 class="font-display font-semibold text-gray-800 group-hover:text-rose-600 transition-colors text-sm sm:text-base"><?= clean($cat['name']) ?></h3>
          <p class="text-xs text-gray-500 mt-1 line-clamp-2"><?= clean(substr($cat['description'] ?? '', 0, 80)) ?></p>
          <span class="inline-block mt-2 text-xs text-rose-600 font-semibold">Lihat Produk →</span>
        </div>
      </a>
      <?php endforeach; ?>
    </div>
  </div>
</section>