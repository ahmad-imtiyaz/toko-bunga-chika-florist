<!-- KEUNGGULAN -->
<section class="section-padding bg-white">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="text-center mb-10">
      <h2 class="font-display text-2xl sm:text-3xl font-bold text-gray-900 mb-2">Kenapa Memilih Chika Florist?</h2>
    </div>
    <div class="grid grid-cols-2 md:grid-cols-3 gap-6">
      <?php
      $keunggulan = [
        ['icon'=>'🕐','title'=>'Layanan 24 Jam','desc'=>'Pemesanan tersedia kapan saja, termasuk malam hari dan hari libur.'],
        ['icon'=>'🚀','title'=>'Same Day Delivery','desc'=>'Pengiriman di hari yang sama untuk banyak kota besar di Indonesia.'],
        ['icon'=>'🌺','title'=>'Bunga Fresh Berkualitas','desc'=>'Setiap rangkaian dibuat dari bunga segar pilihan dengan desain elegan.'],
        ['icon'=>'💬','title'=>'Admin Responsif','desc'=>'Tim admin siap membantu melalui WhatsApp dengan cepat dan ramah.'],
        ['icon'=>'💰','title'=>'Harga Transparan','desc'=>'Tidak ada biaya tersembunyi. Semua harga diinformasikan sejak awal.'],
        ['icon'=>'📦','title'=>'Desain Custom','desc'=>'Kami menerima request desain khusus sesuai tema dan kebutuhan Anda.'],
      ];
      foreach ($keunggulan as $item): ?>
      <div class="bg-amber-50 rounded-xl p-5 border border-amber-100">
        <div class="text-3xl mb-3"><?= $item['icon'] ?></div>
        <h3 class="font-display font-semibold text-gray-800 mb-1.5 text-base"><?= $item['title'] ?></h3>
        <p class="text-gray-500 text-sm leading-relaxed"><?= $item['desc'] ?></p>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>