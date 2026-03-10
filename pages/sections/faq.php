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
