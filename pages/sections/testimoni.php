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