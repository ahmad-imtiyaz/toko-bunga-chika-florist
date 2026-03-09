<?php
require_once __DIR__ . '/../includes/config.php';
$page_title    = getSetting('meta_title_home');
$meta_desc     = getSetting('meta_desc_home');
$canonical_url = BASE_URL . '/';
$pdo = getDB();
$featured     = $pdo->query("SELECT p.*,c.name as cat_name,c.slug as cat_slug FROM products p JOIN categories c ON p.category_id=c.id WHERE p.is_featured=1 AND p.is_active=1 ORDER BY p.sort_order ASC LIMIT 8")->fetchAll();
$testimonials = $pdo->query("SELECT * FROM testimonials WHERE is_active=1 ORDER BY sort_order ASC LIMIT 6")->fetchAll();
$faqs         = $pdo->query("SELECT * FROM faqs WHERE is_active=1 ORDER BY sort_order ASC LIMIT 6")->fetchAll();
$cities       = getActiveCities(20);
$categories   = getMainCategories();
require_once __DIR__ . '/../includes/header.php';
?>

<!-- HERO -->
<section class="relative bg-gradient-to-br from-rose-50 via-white to-amber-50 overflow-hidden">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 lg:py-24 flex flex-col lg:flex-row items-center gap-12">
    <div class="flex-1 text-center lg:text-left">
      <p class="text-rose-600 text-sm font-semibold tracking-wider uppercase mb-3">Florist Online Terpercaya</p>
      <h1 class="font-display text-3xl sm:text-4xl lg:text-5xl font-bold text-gray-900 leading-tight mb-5">
        Toko Bunga Online<br>
        <span class="text-rose-600">24 Jam Indonesia</span><br>
        <span class="text-2xl sm:text-3xl text-amber-700">Kirim Bunga Cepat Seluruh Kota</span>
      </h1>
      <p class="text-gray-600 text-base lg:text-lg leading-relaxed mb-8 max-w-xl mx-auto lg:mx-0">
        Chika Florist melayani pemesanan dan pengiriman bunga ke seluruh kota Indonesia. Pesan kapan saja, bunga dikirim cepat dengan kualitas terjaga.
      </p>
      <div class="flex flex-col sm:flex-row gap-3 justify-center lg:justify-start">
        <a href="<?= waLink('Halo Chika Florist, saya ingin memesan bunga') ?>" target="_blank"
           class="inline-flex items-center justify-center gap-2 bg-green-500 hover:bg-green-600 text-white font-semibold px-7 py-3.5 rounded-full transition-colors shadow-md">
          <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z"/><path d="M12 0C5.373 0 0 5.373 0 12c0 2.096.541 4.063 1.491 5.776L.057 23.04l5.43-1.424A11.947 11.947 0 0012 24c6.627 0 12-5.373 12-12S18.627 0 12 0zm0 21.818a9.818 9.818 0 01-5.015-1.371l-.36-.214-3.726.977.995-3.633-.235-.374A9.818 9.818 0 1112 21.818z"/></svg>
          Pesan via WhatsApp
        </a>
        <a href="<?= BASE_URL ?>/toko-bunga-online-24-jam-indonesia" class="inline-flex items-center justify-center gap-2 bg-white hover:bg-rose-50 text-rose-600 font-semibold px-7 py-3.5 rounded-full border border-rose-200 transition-colors">
          Layanan 24 Jam
        </a>
      </div>
      <div class="flex flex-wrap gap-4 mt-8 justify-center lg:justify-start text-sm text-gray-500">
        <span class="flex items-center gap-1.5"><span class="text-green-500 font-bold">✓</span> Same Day Delivery</span>
        <span class="flex items-center gap-1.5"><span class="text-green-500 font-bold">✓</span> Bunga Fresh</span>
        <span class="flex items-center gap-1.5"><span class="text-green-500 font-bold">✓</span> 24 Jam Nonstop</span>
        <span class="flex items-center gap-1.5"><span class="text-green-500 font-bold">✓</span> Seluruh Indonesia</span>
      </div>
    </div>
    <div class="flex-shrink-0 w-full max-w-sm lg:max-w-md">
      <div class="rounded-2xl overflow-hidden shadow-xl border-4 border-white bg-gradient-to-br from-rose-100 to-amber-100 h-72 lg:h-96 flex items-center justify-center">
        <img src="<?= UPLOAD_URL ?>hero-bunga.jpg" alt="toko bunga online 24 jam Indonesia Chika Florist"
             class="w-full h-full object-cover" onerror="this.style.display='none'">
      </div>
    </div>
  </div>
</section>

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

<!-- TESTIMONI -->
<?php if (!empty($testimonials)): ?>
<section class="section-padding bg-white">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="text-center mb-10">
      <h2 class="font-display text-2xl sm:text-3xl font-bold text-gray-900 mb-2">Apa Kata Pelanggan Kami</h2>
    </div>
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
      <?php foreach ($testimonials as $t): ?>
      <div class="bg-amber-50 rounded-xl p-5 border border-amber-100">
        <div class="flex gap-1 mb-3"><?php for ($i=0;$i<$t['rating'];$i++): ?><span class="text-amber-400 text-sm">★</span><?php endfor; ?></div>
        <p class="text-gray-600 text-sm leading-relaxed mb-4">"<?= clean($t['content']) ?>"</p>
        <div class="flex items-center gap-2">
          <div class="w-8 h-8 rounded-full bg-rose-200 flex items-center justify-center text-rose-700 font-bold text-sm"><?= mb_strtoupper(mb_substr($t['customer_name'],0,1)) ?></div>
          <div>
            <p class="font-semibold text-gray-800 text-sm"><?= clean($t['customer_name']) ?></p>
            <?php if ($t['customer_city']): ?><p class="text-xs text-gray-400"><?= clean($t['customer_city']) ?></p><?php endif; ?>
          </div>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>
<?php endif; ?>

<!-- FAQ -->
<?php if (!empty($faqs)): ?>
<section class="section-padding bg-amber-50">
  <div class="max-w-3xl mx-auto px-4 sm:px-6">
    <div class="text-center mb-10">
      <h2 class="font-display text-2xl sm:text-3xl font-bold text-gray-900 mb-2">FAQ – Pertanyaan yang Sering Diajukan</h2>
    </div>
    <div class="space-y-3">
      <?php foreach ($faqs as $faq): ?>
      <details class="bg-white rounded-xl border border-amber-100 group">
        <summary class="flex justify-between items-center p-4 cursor-pointer font-semibold text-gray-800 text-sm list-none">
          <?= clean($faq['question']) ?>
          <span class="text-rose-500 group-open:rotate-180 transition-transform ml-2 shrink-0">▼</span>
        </summary>
        <div class="px-4 pb-4 text-gray-600 text-sm leading-relaxed border-t border-amber-50 pt-3"><?= clean($faq['answer']) ?></div>
      </details>
      <?php endforeach; ?>
    </div>
  </div>
</section>
<?php endif; ?>

<!-- CTA -->
<section class="bg-gradient-to-r from-rose-600 to-rose-700 py-14 px-4">
  <div class="max-w-3xl mx-auto text-center">
    <h2 class="font-display text-2xl sm:text-3xl font-bold text-white mb-3">Pesan Bunga Online Sekarang</h2>
    <p class="text-rose-100 mb-7 text-sm sm:text-base">Dengan layanan <a href="<?= BASE_URL ?>/toko-bunga-online-24-jam-indonesia" class="text-white underline font-semibold">toko bunga online 24 jam Indonesia</a> dari Chika Florist, Anda dapat mengirimkan bunga kapan saja.</p>
    <a href="<?= waLink() ?>" target="_blank" class="inline-flex items-center gap-2 bg-white text-rose-600 font-bold px-8 py-3.5 rounded-full hover:bg-rose-50 transition-colors shadow-lg">Hubungi Kami via WhatsApp</a>
  </div>
</section>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
