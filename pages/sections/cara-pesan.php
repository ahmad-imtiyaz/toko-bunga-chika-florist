<!-- CARA PESAN -->
<section class="section-padding bg-amber-50">
  <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
    <h2 class="font-display text-2xl sm:text-3xl font-bold text-gray-900 mb-10">Cara Pesan Bunga Online</h2>
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-6">
      <?php foreach ([['1','Pilih Produk','Pilih produk bunga sesuai kebutuhan di website kami.'],['2','Hubungi WhatsApp','Hubungi admin kami melalui WhatsApp yang tersedia.'],['3','Kirim Detail','Kirim ucapan dan alamat pengiriman lengkap.'],['4','Bunga Dikirim','Pesanan diproses dan bunga langsung dikirim.']] as $s): ?>
      <div class="text-center">
        <div class="w-12 h-12 rounded-full bg-rose-600 text-white font-display font-bold text-lg flex items-center justify-center mx-auto mb-3"><?= $s[0] ?></div>
        <h3 class="font-semibold text-gray-800 text-sm mb-1"><?= $s[1] ?></h3>
        <p class="text-gray-500 text-xs leading-relaxed"><?= $s[2] ?></p>
      </div>
      <?php endforeach; ?>
    </div>
    <div class="mt-8">
      <a href="<?= waLink() ?>" target="_blank" class="inline-flex items-center gap-2 bg-green-500 hover:bg-green-600 text-white font-semibold px-8 py-3.5 rounded-full transition-colors">Pesan Sekarang via WhatsApp</a>
    </div>
  </div>
</section>