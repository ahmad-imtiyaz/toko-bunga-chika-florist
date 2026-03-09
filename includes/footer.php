<?php
$site_name   = getSetting('site_name','Chika Florist');
$footer_text = getSetting('footer_text','');
$instagram   = getSetting('instagram','');
$facebook    = getSetting('facebook','');
$allCities   = getActiveCities();
$mainCats    = getMainCategories();
?>
<footer class="bg-gray-900 text-gray-300 mt-16">

  <!-- Footer Nasional SEO -->
  <?php if (!empty($allCities)): ?>
  <div class="border-b border-gray-800 py-8 px-4">
    <div class="max-w-7xl mx-auto">
      <p class="text-xs text-gray-500 uppercase tracking-widest mb-4 font-semibold">Area Layanan Populer</p>
      <div class="flex flex-wrap gap-x-3 gap-y-2">
        <?php foreach ($allCities as $i => $city): ?>
        <a href="/toko-bunga-<?= $city['slug'] ?>" class="text-sm text-gray-400 hover:text-rose-400 transition-colors">Toko Bunga <?= clean($city['name']) ?></a>
        <?php if ($i < count($allCities)-1): ?><span class="text-gray-700 text-xs self-center">|</span><?php endif; ?>
        <?php endforeach; ?>
      </div>
    </div>
  </div>
  <?php endif; ?>

  <!-- Footer Main -->
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-10">

    <!-- Brand -->
    <div>
      <a href="/" class="inline-block mb-4">
        <img src="<?= UPLOAD_URL . getSetting('logo','logo.jpeg') ?>" alt="<?= clean($site_name) ?>"
             class="h-12 w-auto object-contain brightness-0 invert opacity-90"
             onerror="this.style.display='none';this.nextElementSibling.style.display='block'">
        <span class="font-display text-xl font-bold text-white" style="display:none"><?= clean($site_name) ?></span>
      </a>
      <p class="text-sm text-gray-400 leading-relaxed mb-4"><?= clean($footer_text) ?></p>
      <div class="flex gap-3">
        <?php if ($instagram): ?>
        <a href="<?= $instagram ?>" target="_blank" rel="noopener" class="w-8 h-8 rounded-full bg-gray-800 hover:bg-rose-600 flex items-center justify-center transition-colors">
          <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zM12 0C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.052.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98C8.333 23.986 8.741 24 12 24c3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 100 12.324 6.162 6.162 0 000-12.324zM12 16a4 4 0 110-8 4 4 0 010 8zm6.406-11.845a1.44 1.44 0 100 2.881 1.44 1.44 0 000-2.881z"/></svg>
        </a>
        <?php endif; ?>
        <?php if ($facebook): ?>
        <a href="<?= $facebook ?>" target="_blank" rel="noopener" class="w-8 h-8 rounded-full bg-gray-800 hover:bg-blue-600 flex items-center justify-center transition-colors">
          <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
        </a>
        <?php endif; ?>
        <a href="<?= waLink() ?>" target="_blank" rel="noopener" class="w-8 h-8 rounded-full bg-gray-800 hover:bg-green-600 flex items-center justify-center transition-colors">
          <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z"/><path d="M12 0C5.373 0 0 5.373 0 12c0 2.096.541 4.063 1.491 5.776L.057 23.04l5.43-1.424A11.947 11.947 0 0012 24c6.627 0 12-5.373 12-12S18.627 0 12 0zm0 21.818a9.818 9.818 0 01-5.015-1.371l-.36-.214-3.726.977.995-3.633-.235-.374A9.818 9.818 0 1112 21.818z"/></svg>
        </a>
      </div>
    </div>

    <!-- Produk -->
    <div>
      <h4 class="text-white font-semibold mb-4 font-display">Produk Bunga</h4>
      <ul class="space-y-2 text-sm">
        <?php foreach ($mainCats as $cat): ?>
        <li><a href="/<?= $cat['slug'] ?>" class="text-gray-400 hover:text-rose-400 transition-colors"><?= clean($cat['name']) ?></a></li>
        <?php endforeach; ?>
      </ul>
    </div>

    <!-- Layanan -->
    <div>
      <h4 class="text-white font-semibold mb-4 font-display">Layanan</h4>
      <ul class="space-y-2 text-sm">
        <li><a href="/toko-bunga-online-24-jam-indonesia" class="text-gray-400 hover:text-rose-400 transition-colors">Toko Bunga Online 24 Jam</a></li>
        <li><a href="/kirim-bunga-hari-ini" class="text-gray-400 hover:text-rose-400 transition-colors">Kirim Bunga Hari Ini</a></li>
        <li><a href="/florist-terdekat" class="text-gray-400 hover:text-rose-400 transition-colors">Florist Terdekat</a></li>
        <li><a href="/pesan-bunga-online" class="text-gray-400 hover:text-rose-400 transition-colors">Pesan Bunga Online</a></li>
        <li><a href="/area-layanan" class="text-gray-400 hover:text-rose-400 transition-colors">Semua Area Layanan</a></li>
      </ul>
    </div>

    <!-- Kota -->
    <div>
      <h4 class="text-white font-semibold mb-4 font-display">Kota Populer</h4>
      <ul class="space-y-2 text-sm">
        <?php foreach (array_slice($allCities, 0, 8) as $city): ?>
        <li><a href="/toko-bunga-<?= $city['slug'] ?>" class="text-gray-400 hover:text-rose-400 transition-colors">Toko Bunga <?= clean($city['name']) ?></a></li>
        <?php endforeach; ?>
        <?php if (count($allCities) > 8): ?>
        <li><a href="/area-layanan" class="text-rose-400 hover:text-rose-300 text-xs transition-colors">+ <?= count($allCities)-8 ?> kota lainnya →</a></li>
        <?php endif; ?>
      </ul>
    </div>
  </div>

  <!-- Footer Bottom -->
  <div class="border-t border-gray-800 py-5 px-4">
    <div class="max-w-7xl mx-auto flex flex-col sm:flex-row justify-between items-center gap-2 text-xs text-gray-500">
      <p>© <?= date('Y') ?> <?= clean($site_name) ?>. All rights reserved.</p>
      <p><a href="/toko-bunga-online-24-jam-indonesia" class="hover:text-rose-400 transition-colors">Toko Bunga Online 24 Jam Indonesia</a></p>
    </div>
  </div>
</footer>

<!-- Sticky WA Button -->
<a href="<?= waLink() ?>" target="_blank" rel="noopener"
   class="fixed bottom-6 right-6 z-50 flex items-center gap-2 bg-green-500 hover:bg-green-600 text-white font-semibold text-sm py-3 px-4 rounded-full shadow-lg transition-all hover:scale-105"
   aria-label="Pesan via WhatsApp">
  <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z"/><path d="M12 0C5.373 0 0 5.373 0 12c0 2.096.541 4.063 1.491 5.776L.057 23.04l5.43-1.424A11.947 11.947 0 0012 24c6.627 0 12-5.373 12-12S18.627 0 12 0zm0 21.818a9.818 9.818 0 01-5.015-1.371l-.36-.214-3.726.977.995-3.633-.235-.374A9.818 9.818 0 1112 21.818z"/></svg>
  <span class="hidden sm:inline">Pesan Sekarang</span>
</a>

</body>
</html>
