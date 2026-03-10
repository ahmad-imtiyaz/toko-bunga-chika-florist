<!-- PRODUK UNGGULAN -->
<?php if (!empty($featured)): ?>
<section class="section-padding bg-amber-50">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="text-center mb-10">
      <h2 class="font-display text-2xl sm:text-3xl font-bold text-gray-900 mb-2">Produk Unggulan</h2>
      <p class="text-gray-500 text-sm">Pilihan terlaris dan terpopuler pelanggan kami</p>
    </div>
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4 sm:gap-5">
      <?php foreach ($featured as $prod): ?>
      <div class="card-hover bg-white rounded-xl overflow-hidden border border-amber-100 hover:border-rose-200 group">
        <div class="h-48 overflow-hidden bg-gradient-to-br from-rose-50 to-amber-50">
          <img src="<?= UPLOAD_URL . ($prod['image'] ?? '') ?>" alt="<?= clean($prod['name']) ?>"
               class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300" onerror="this.style.display='none'">
        </div>
        <div class="p-4">
          <p class="text-xs text-rose-500 font-medium mb-1"><?= clean($prod['cat_name']) ?></p>
          <h3 class="font-semibold text-gray-800 text-sm leading-snug mb-2"><?= clean($prod['name']) ?></h3>
          <p class="text-rose-600 font-bold text-sm"><?= formatHarga($prod['price_min'], $prod['price_max']) ?></p>
          <a href="<?= waLink('Halo, saya ingin pesan ' . $prod['name']) ?>" target="_blank"
             class="mt-3 block w-full text-center bg-green-50 hover:bg-green-500 text-green-600 hover:text-white text-xs font-semibold py-2 rounded-lg border border-green-200 hover:border-green-500 transition-colors">
            Pesan Sekarang
          </a>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>
<?php endif; ?>