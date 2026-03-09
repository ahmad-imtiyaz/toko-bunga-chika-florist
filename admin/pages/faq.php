<?php
// ============================================================
// SEMUA LOGIKA CRUD DI SINI — SEBELUM ADMIN HEADER
// ============================================================
require_once __DIR__ . '/../../includes/config.php';
requireAdminLogin();

$pdo    = getDB();
$action = $_GET['action'] ?? 'list';
$id     = (int)($_GET['id'] ?? 0);
$b      = BASE_URL;

// AJAX: simpan urutan drag-and-drop
if (isset($_POST['reorder']) && !empty($_POST['ids'])) {
    $ids  = array_filter(array_map('intval', explode(',', $_POST['ids'])));
    $stmt = $pdo->prepare("UPDATE faqs SET sort_order=? WHERE id=?");
    foreach (array_values($ids) as $i => $fid) {
        $stmt->execute([$i + 1, $fid]);
    }
    echo json_encode(['ok' => true]);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = [
        'question'  => clean($_POST['question'] ?? ''),
        'answer'    => clean($_POST['answer'] ?? ''),
        'category'  => clean($_POST['category'] ?? 'Umum'),
        'is_active' => isset($_POST['is_active']) ? 1 : 0,
    ];

    if ($_POST['action_type'] === 'tambah') {
        $max = (int)$pdo->query("SELECT MAX(sort_order) FROM faqs")->fetchColumn();
        $data['sort_order'] = $max + 1;
        $cols = implode(',', array_keys($data));
        $vals = ':' . implode(',:', array_keys($data));
        $pdo->prepare("INSERT INTO faqs ($cols) VALUES ($vals)")->execute($data);
        $_SESSION['success'] = 'FAQ berhasil ditambahkan.';
    } elseif ($_POST['action_type'] === 'edit' && $id) {
        $sets = implode(',', array_map(fn($k) => "$k=:$k", array_keys($data)));
        $data['id'] = $id;
        $pdo->prepare("UPDATE faqs SET $sets WHERE id=:id")->execute($data);
        $_SESSION['success'] = 'FAQ berhasil diperbarui.';
    }
    header('Location: ' . $b . '/admin/faq');
    exit;
}

if (isset($_GET['toggle']) && $id) {
    $cur = $pdo->prepare("SELECT is_active FROM faqs WHERE id=?");
    $cur->execute([$id]);
    $pdo->prepare("UPDATE faqs SET is_active=? WHERE id=?")->execute([$cur->fetchColumn() ? 0 : 1, $id]);
    header('Location: ' . $b . '/admin/faq');
    exit;
}

if (isset($_GET['hapus']) && $id) {
    $pdo->prepare("DELETE FROM faqs WHERE id=?")->execute([$id]);
    $_SESSION['success'] = 'FAQ berhasil dihapus.';
    header('Location: ' . $b . '/admin/faq');
    exit;
}

// ============================================================
$admin_title = 'Manajemen FAQ';
require_once __DIR__ . '/../includes/admin_header.php';

// Ambil semua kategori unik untuk filter + datalist
$existingCats = $pdo->query("SELECT DISTINCT category FROM faqs WHERE category != '' ORDER BY category ASC")->fetchAll(PDO::FETCH_COLUMN);

// ============================================================
// FORM TAMBAH / EDIT
// ============================================================
if ($action === 'tambah' || ($action === 'edit' && $id)) {
    $item = [];
    if ($action === 'edit') {
        $s = $pdo->prepare("SELECT * FROM faqs WHERE id=?");
        $s->execute([$id]);
        $item = $s->fetch();
    }
    ?>
    <div class="max-w-2xl">
      <div class="flex items-center gap-3 mb-5">
        <a href="<?= $b ?>/admin/faq" class="text-gray-400 hover:text-rose-600">← Kembali</a>
        <h2 class="font-display font-bold text-gray-800"><?= $action === 'tambah' ? 'Tambah' : 'Edit' ?> FAQ</h2>
      </div>

      <?php if (!empty($_SESSION['error'])): ?>
        <div class="bg-red-50 border border-red-200 text-red-600 text-sm rounded-lg px-4 py-3 mb-4">
          ⚠️ <?= clean($_SESSION['error']) ?>
        </div>
        <?php unset($_SESSION['error']); ?>
      <?php endif; ?>

      <form method="POST" class="space-y-4">
        <input type="hidden" name="action_type" value="<?= $action ?>">

        <!-- Pertanyaan -->
        <div class="bg-white rounded-xl border border-rose-100 p-6 space-y-4">
          <h3 class="font-semibold text-gray-700 text-sm border-b border-gray-100 pb-2">❓ Pertanyaan & Jawaban</h3>

          <div>
            <label class="form-label">Pertanyaan *</label>
            <textarea name="question" required class="form-input" rows="2"
                      placeholder="Berapa lama waktu pengiriman bunga?"><?= clean($item['question'] ?? '') ?></textarea>
          </div>

          <div>
            <label class="form-label">Jawaban *</label>
            <textarea name="answer" required id="answerField" class="form-input" rows="5"
                      placeholder="Jelaskan jawaban secara detail..."><?= clean($item['answer'] ?? '') ?></textarea>
            <p class="text-xs text-gray-400 mt-1">
              <span id="answerCount">0</span> karakter
            </p>
          </div>
        </div>

        <!-- Pengaturan -->
        <div class="bg-white rounded-xl border border-rose-100 p-6 space-y-4">
          <h3 class="font-semibold text-gray-700 text-sm border-b border-gray-100 pb-2">⚙️ Pengaturan</h3>

          <div>
            <label class="form-label">
              Kategori
              <?php if (!empty($existingCats)): ?>
              <span class="text-gray-400 font-normal text-xs">— atau pilih yang ada:</span>
              <?php endif; ?>
            </label>
            <input type="text" name="category" id="categoryInput" list="catListFaq"
                   class="form-input"
                   value="<?= clean($item['category'] ?? 'Umum') ?>"
                   placeholder="Umum, Pengiriman, Pembayaran...">
            <datalist id="catListFaq">
              <?php foreach ($existingCats as $cat): ?>
              <option value="<?= clean($cat) ?>">
              <?php endforeach; ?>
            </datalist>
            <!-- Tag kategori cepat -->
            <?php if (!empty($existingCats)): ?>
            <div class="flex flex-wrap gap-1.5 mt-2">
              <?php foreach ($existingCats as $cat): ?>
              <button type="button"
                      onclick="document.getElementById('categoryInput').value='<?= addslashes(clean($cat)) ?>'"
                      class="text-xs bg-violet-50 text-violet-600 border border-violet-200 px-2 py-0.5 rounded-full hover:bg-violet-100">
                <?= clean($cat) ?>
              </button>
              <?php endforeach; ?>
            </div>
            <?php endif; ?>
          </div>

          <!-- Preview FAQ -->
          <div>
            <label class="form-label">Preview Tampilan</label>
            <div class="border border-rose-100 rounded-xl overflow-hidden">
              <div class="bg-rose-50 px-4 py-3 flex items-start justify-between gap-2 cursor-pointer"
                   onclick="this.nextElementSibling.classList.toggle('hidden')">
                <p id="previewQ" class="text-sm font-semibold text-gray-800 flex-1">
                  <?= !empty($item['question']) ? clean($item['question']) : 'Pertanyaan akan muncul di sini...' ?>
                </p>
                <span class="text-rose-400 font-bold text-lg leading-none mt-0.5">+</span>
              </div>
              <div class="px-4 py-3 bg-white hidden">
                <p id="previewA" class="text-sm text-gray-600 leading-relaxed">
                  <?= !empty($item['answer']) ? clean($item['answer']) : 'Jawaban akan muncul di sini...' ?>
                </p>
              </div>
            </div>
            <p class="text-xs text-gray-400 mt-1">Klik untuk expand/collapse preview</p>
          </div>

          <div class="flex items-center gap-2">
            <input type="checkbox" name="is_active" id="is_active_f" <?= ($item['is_active'] ?? 1) ? 'checked' : '' ?>>
            <label for="is_active_f" class="text-sm text-gray-700 cursor-pointer">Aktif (tampil di halaman FAQ website)</label>
          </div>
        </div>

        <div class="flex gap-2 pb-6">
          <button type="submit" class="btn-primary">
            <?= $action === 'tambah' ? '➕ Tambah FAQ' : '💾 Simpan Perubahan' ?>
          </button>
          <a href="<?= $b ?>/admin/faq" class="btn-secondary">Batal</a>
        </div>
      </form>
    </div>

    <script>
    // ── Live preview ─────────────────────────────────────────
    const qField = document.querySelector('textarea[name="question"]');
    const aField = document.getElementById('answerField');
    const preQ   = document.getElementById('previewQ');
    const preA   = document.getElementById('previewA');
    const ctr    = document.getElementById('answerCount');

    function updatePreview() {
        preQ.textContent = qField.value.trim() || 'Pertanyaan akan muncul di sini...';
        preA.textContent = aField.value.trim() || 'Jawaban akan muncul di sini...';
        ctr.textContent  = aField.value.length;
    }

    qField.addEventListener('input', updatePreview);
    aField.addEventListener('input', updatePreview);
    updatePreview(); // init counter
    </script>

<?php
// ============================================================
// LIST VIEW
// ============================================================
} else {
    if (!empty($_SESSION['success'])): ?>
      <div class="bg-green-50 border border-green-200 text-green-700 text-sm rounded-lg px-4 py-3 mb-4">
        ✅ <?= clean($_SESSION['success']) ?>
      </div>
    <?php unset($_SESSION['success']); endif;

    $list = $pdo->query("SELECT * FROM faqs ORDER BY sort_order ASC, id ASC")->fetchAll();
    $totalAktif = count(array_filter($list, fn($f) => $f['is_active']));

    // Group by category
    $grouped = [];
    foreach ($list as $f) {
        $grouped[$f['category'] ?: 'Umum'][] = $f;
    }
    ?>

    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-5">
      <div>
        <p class="text-sm text-gray-500">
          <span class="font-semibold text-gray-700"><?= count($list) ?></span> FAQ ·
          <span class="text-green-600 font-medium"><?= $totalAktif ?> aktif</span> ·
          <span class="text-gray-400"><?= count($grouped) ?> kategori</span>
        </p>
        <p class="text-xs text-gray-400 mt-0.5">⠿ Seret untuk mengubah urutan tampil</p>
      </div>
      <a href="<?= $b ?>/admin/faq?action=tambah" class="btn-primary whitespace-nowrap">➕ Tambah FAQ</a>
    </div>

    <!-- Search + Filter -->
    <div class="flex flex-wrap gap-2 mb-5">
      <div class="relative flex-1 min-w-48">
        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm">🔍</span>
        <input type="text" id="searchFaq" placeholder="Cari pertanyaan atau jawaban..."
               class="pl-8 pr-4 py-2 text-sm border border-gray-200 rounded-xl focus:outline-none focus:border-rose-400 w-full">
      </div>
      <!-- Filter kategori pill -->
      <div class="flex flex-wrap gap-1.5 items-center">
        <button onclick="filterCat('')"
                class="cat-btn active text-xs px-3 py-1.5 rounded-xl border border-rose-300 bg-rose-500 text-white font-medium transition-all"
                data-cat="">Semua</button>
        <?php foreach (array_keys($grouped) as $catName): ?>
        <button onclick="filterCat('<?= addslashes(clean($catName)) ?>')"
                class="cat-btn text-xs px-3 py-1.5 rounded-xl border border-gray-200 bg-white text-gray-600 hover:border-violet-300 hover:text-violet-600 font-medium transition-all"
                data-cat="<?= clean($catName) ?>">
          <?= clean($catName) ?>
          <span class="ml-1 text-gray-400"><?= count($grouped[$catName]) ?></span>
        </button>
        <?php endforeach; ?>
      </div>
      <select id="filterStatus" class="text-sm border border-gray-200 rounded-xl px-3 py-2 focus:outline-none focus:border-rose-400 bg-white">
        <option value="">Semua Status</option>
        <option value="1">Aktif</option>
        <option value="0">Nonaktif</option>
      </select>
    </div>

    <!-- Toast -->
    <div id="toastReorder" class="hidden fixed bottom-5 right-5 bg-gray-800 text-white text-sm px-4 py-2.5 rounded-xl shadow-lg z-50">
      ✅ Urutan berhasil disimpan
    </div>

    <!-- Empty state -->
    <div id="emptySearch" class="hidden text-center py-16 text-gray-400">
      <div class="text-4xl mb-2">🔍</div>
      <p class="text-sm">Tidak ada FAQ yang cocok</p>
    </div>

    <?php if (empty($list)): ?>
    <div class="text-center py-20 text-gray-400">
      <div class="text-6xl mb-3">❓</div>
      <p class="text-base font-medium text-gray-500">Belum ada FAQ</p>
      <p class="text-sm mt-1">Tambahkan FAQ pertama untuk membantu pelanggan</p>
      <a href="<?= $b ?>/admin/faq?action=tambah" class="mt-4 inline-block btn-primary">➕ Tambah FAQ Pertama</a>
    </div>
    <?php else: ?>

    <!-- FAQ List (drag-and-drop, accordion preview) -->
    <div id="faqList" class="space-y-2">
      <?php foreach ($list as $f): ?>
      <div class="faq-item bg-white border border-rose-100 rounded-xl overflow-hidden hover:shadow-md transition-shadow"
           data-id="<?= $f['id'] ?>"
           data-q="<?= strtolower(clean($f['question'])) ?>"
           data-a="<?= strtolower(clean($f['answer'])) ?>"
           data-cat="<?= clean($f['category'] ?: 'Umum') ?>"
           data-active="<?= $f['is_active'] ?>">

        <!-- Row utama -->
        <div class="flex items-start gap-3 px-4 py-3">
          <!-- Drag handle -->
          <div class="text-gray-300 hover:text-gray-500 cursor-grab active:cursor-grabbing select-none text-lg leading-none pt-1 flex-shrink-0">⠿</div>

          <!-- Nomor urut -->
          <div class="faq-num w-6 h-6 rounded-full bg-rose-100 text-rose-600 text-xs font-bold flex items-center justify-center flex-shrink-0 mt-0.5">
            <?= array_search($f, $list) + 1 ?>
          </div>

          <!-- Konten -->
          <div class="flex-1 min-w-0">
            <div class="flex items-start justify-between gap-2">
              <p class="text-sm font-semibold text-gray-800 leading-snug">
                <?= clean($f['question']) ?>
              </p>
              <!-- Toggle preview -->
              <button type="button" onclick="togglePreview(this)"
                      class="text-gray-300 hover:text-rose-400 text-lg leading-none flex-shrink-0 transition-colors mt-0.5"
                      title="Lihat jawaban">▼</button>
            </div>
            <div class="flex items-center gap-2 mt-1.5 flex-wrap">
              <!-- Kategori badge -->
              <span class="text-xs bg-violet-50 text-violet-600 border border-violet-100 px-2 py-0.5 rounded-full">
                <?= clean($f['category'] ?: 'Umum') ?>
              </span>
              <!-- Status -->
              <a href="<?= $b ?>/admin/faq?toggle=1&id=<?= $f['id'] ?>">
                <span class="<?= $f['is_active'] ? 'badge-active' : 'badge-inactive' ?> text-xs cursor-pointer">
                  <?= $f['is_active'] ? 'Aktif' : 'Nonaktif' ?>
                </span>
              </a>
            </div>
          </div>

          <!-- Aksi -->
          <div class="flex items-center gap-1.5 flex-shrink-0">
            <a href="<?= $b ?>/admin/faq?action=edit&id=<?= $f['id'] ?>"
               class="text-xs bg-amber-50 text-amber-700 border border-amber-200 px-2.5 py-1 rounded-lg hover:bg-amber-100 font-medium">
              ✏️ Edit
            </a>
            <a href="<?= $b ?>/admin/faq?hapus=1&id=<?= $f['id'] ?>"
               onclick="return confirm('Hapus FAQ ini?')"
               class="text-xs text-red-400 hover:text-red-600 px-2 py-1 rounded-lg hover:bg-red-50 border border-transparent hover:border-red-100">
              🗑️
            </a>
          </div>
        </div>

        <!-- Preview jawaban (hidden by default) -->
        <div class="faq-preview hidden border-t border-rose-50 px-4 py-3 bg-rose-50/40">
          <p class="text-xs text-gray-400 mb-1 font-medium uppercase tracking-wide">Jawaban:</p>
          <p class="text-sm text-gray-600 leading-relaxed"><?= nl2br(clean($f['answer'])) ?></p>
        </div>
      </div>
      <?php endforeach; ?>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/Sortable/1.15.2/Sortable.min.js"></script>
    <script>
    // ── Toast ─────────────────────────────────────────────────
    function showToast() {
        const t = document.getElementById('toastReorder');
        t.classList.remove('hidden');
        clearTimeout(t._t);
        t._t = setTimeout(() => t.classList.add('hidden'), 2500);
    }

    // ── Toggle preview jawaban ────────────────────────────────
    function togglePreview(btn) {
        const preview = btn.closest('.faq-item').querySelector('.faq-preview');
        const isHidden = preview.classList.contains('hidden');
        preview.classList.toggle('hidden', !isHidden);
        btn.style.transform = isHidden ? 'rotate(180deg)' : '';
        btn.style.color     = isHidden ? '#f43f5e' : '';
    }

    // ── Update nomor urut visual ──────────────────────────────
    function updateNumbers() {
        document.querySelectorAll('#faqList .faq-item').forEach((el, i) => {
            const num = el.querySelector('.faq-num');
            if (num) num.textContent = i + 1;
        });
    }

    // ── Sortable ──────────────────────────────────────────────
    Sortable.create(document.getElementById('faqList'), {
        animation: 200,
        ghostClass: 'opacity-30',
        handle: '[class*="cursor-grab"]',
        filter: 'a, button',
        preventOnFilter: false,
        onEnd() {
            updateNumbers();
            const ids = [...document.querySelectorAll('#faqList .faq-item')]
                        .filter(c => c.style.display !== 'none')
                        .map(c => c.dataset.id).join(',');
            fetch('', {
                method: 'POST',
                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                body: 'reorder=1&ids=' + ids
            }).then(r => r.json()).then(d => { if (d.ok) showToast(); });
        }
    });

    // ── Filter kategori ───────────────────────────────────────
    let activeCat = '';
    function filterCat(cat) {
        activeCat = cat;
        document.querySelectorAll('.cat-btn').forEach(btn => {
            const isActive = btn.dataset.cat === cat;
            btn.classList.toggle('bg-rose-500',    isActive);
            btn.classList.toggle('text-white',      isActive);
            btn.classList.toggle('border-rose-300', isActive);
            btn.classList.toggle('bg-white',       !isActive);
            btn.classList.toggle('text-gray-600',  !isActive);
            btn.classList.toggle('border-gray-200',!isActive);
        });
        applyFilter();
    }

    // ── Search + Filter gabungan ──────────────────────────────
    function applyFilter() {
        const q      = document.getElementById('searchFaq').value.toLowerCase().trim();
        const status = document.getElementById('filterStatus').value;
        let visible  = 0;

        document.querySelectorAll('.faq-item').forEach(item => {
            const match =
                (!q         || item.dataset.q.includes(q) || item.dataset.a.includes(q)) &&
                (!activeCat || item.dataset.cat === activeCat) &&
                (!status    || item.dataset.active === status);
            item.style.display = match ? '' : 'none';
            if (match) visible++;
        });

        document.getElementById('emptySearch').classList.toggle('hidden', visible > 0);
    }

    document.getElementById('searchFaq').addEventListener('input', applyFilter);
    document.getElementById('filterStatus').addEventListener('change', applyFilter);
    </script>
    <?php endif; ?>
<?php } ?>
<?php require_once __DIR__ . '/../includes/admin_footer.php'; ?>