<!-- LAYANAN ACARA -->
<section class="section-padding bg-white">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="text-center mb-8">
      <h2 class="font-display text-2xl sm:text-3xl font-bold text-gray-900 mb-2">Toko Bunga Online untuk Berbagai Acara</h2>
    </div>
    <div class="flex flex-wrap gap-3 justify-center">
      <?php foreach (['Wedding & Lamaran','Duka Cita & Belasungkawa','Ulang Tahun','Wisuda','Grand Opening','Anniversary','Corporate Event','Hari Raya'] as $a): ?>
      <span class="bg-amber-50 text-amber-800 text-sm px-4 py-2 rounded-full border border-amber-200"><?= $a ?></span>
      <?php endforeach; ?>
    </div>
    <div class="text-center mt-8">
      <a href="<?= BASE_URL ?>/toko-bunga-online-24-jam-indonesia" class="inline-block bg-rose-600 hover:bg-rose-700 text-white font-semibold px-8 py-3.5 rounded-full transition-colors">Lihat Layanan Toko Bunga Online 24 Jam</a>
    </div>
  </div>
</section>