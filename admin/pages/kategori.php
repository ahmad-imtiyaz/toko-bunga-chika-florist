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
    $ids = array_filter(array_map('intval', explode(',', $_POST['ids'])));
    $stmt = $pdo->prepare("UPDATE categories SET sort_order=? WHERE id=?");
    foreach (array_values($ids) as $i => $cid) {
        $stmt->execute([$i + 1, $cid]);
    }
    echo json_encode(['ok' => true]);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = [
        'parent_id'   => ($_POST['parent_id'] !== '') ? (int)$_POST['parent_id'] : null,
        'name'        => clean($_POST['name'] ?? ''),
        'slug'        => makeSlug($_POST['name'] ?? ''),
        'description' => clean($_POST['description'] ?? ''),
        'meta_title'  => clean($_POST['meta_title'] ?? ''),
        'meta_desc'   => clean($_POST['meta_desc'] ?? ''),
        'is_active'   => isset($_POST['is_active']) ? 1 : 0,
    ];
    if (!empty($_FILES['image']['name'])) {
        $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
        if (in_array($ext, ['jpg','jpeg','png','webp'])) {
            $fn = 'cat-' . $data['slug'] . '.' . $ext;
            move_uploaded_file($_FILES['image']['tmp_name'], UPLOAD_DIR . $fn);
            $data['image'] = $fn;
        }
    }
    if ($_POST['action_type'] === 'tambah') {
        $max = (int)$pdo->query("SELECT MAX(sort_order) FROM categories")->fetchColumn();
        $data['sort_order'] = $max + 1;
        $cols = implode(',', array_keys($data));
        $vals = ':' . implode(',:', array_keys($data));
        $pdo->prepare("INSERT INTO categories ($cols) VALUES ($vals)")->execute($data);
        $_SESSION['success'] = 'Kategori berhasil ditambahkan.';
    } elseif ($_POST['action_type'] === 'edit' && $id) {
        unset($data['slug']);
        if (empty($data['image'])) unset($data['image']);
        $sets = implode(',', array_map(fn($k) => "$k=:$k", array_keys($data)));
        $data['id'] = $id;
        $pdo->prepare("UPDATE categories SET $sets WHERE id=:id")->execute($data);
        $_SESSION['success'] = 'Kategori berhasil diperbarui.';
    }
    header('Location: ' . $b . '/admin/kategori');
    exit;
}

if (isset($_GET['toggle']) && $id) {
    $cur = $pdo->prepare("SELECT is_active FROM categories WHERE id=?");
    $cur->execute([$id]);
    $pdo->prepare("UPDATE categories SET is_active=? WHERE id=?")->execute([$cur->fetchColumn() ? 0 : 1, $id]);
    header('Location: ' . $b . '/admin/kategori');
    exit;
}

if (isset($_GET['hapus']) && $id) {
    $pdo->prepare("DELETE FROM categories WHERE id=?")->execute([$id]);
    $_SESSION['success'] = 'Kategori berhasil dihapus.';
    header('Location: ' . $b . '/admin/kategori');
    exit;
}

// ============================================================
$admin_title = 'Manajemen Kategori';
require_once __DIR__ . '/../includes/admin_header.php';

$mainCats = $pdo->query("SELECT id,name FROM categories WHERE parent_id IS NULL AND is_active=1 ORDER BY sort_order")->fetchAll();

// ============================================================
// FORM TAMBAH / EDIT
// ============================================================
if ($action === 'tambah' || ($action === 'edit' && $id)) {
    $item = [];
    if ($action === 'edit') {
        $s = $pdo->prepare("SELECT * FROM categories WHERE id=?");
        $s->execute([$id]);
        $item = $s->fetch();
    }
    ?>
    <div class="max-w-2xl">
      <div class="flex items-center gap-3 mb-5">
        <a href="<?= $b ?>/admin/kategori" class="text-gray-400 hover:text-rose-600">← Kembali</a>
        <h2 class="font-display font-bold text-gray-800"><?= $action === 'tambah' ? 'Tambah' : 'Edit' ?> Kategori</h2>
      </div>

      <?php if (!empty($_SESSION['error'])): ?>
        <div class="bg-red-50 border border-red-200 text-red-600 text-sm rounded-lg px-4 py-3 mb-4">
          ⚠️ <?= clean($_SESSION['error']) ?>
        </div>
        <?php unset($_SESSION['error']); ?>
      <?php endif; ?>

      <form method="POST" enctype="multipart/form-data" class="bg-white rounded-xl border border-rose-100 p-6 space-y-4">
        <input type="hidden" name="action_type" value="<?= $action ?>">
        <div class="grid grid-cols-2 gap-4">
          <div>
            <label class="form-label">Nama Kategori *</label>
            <input type="text" name="name" required class="form-input" value="<?= clean($item['name'] ?? '') ?>">
          </div>
          <div>
            <label class="form-label">Parent <span class="text-gray-400 font-normal text-xs">(kosong = kategori inti)</span></label>
            <select name="parent_id" class="form-input">
              <option value="">-- Kategori Inti --</option>
              <?php foreach ($mainCats as $mc): if ($mc['id'] != $id): ?>
              <option value="<?= $mc['id'] ?>" <?= ($item['parent_id'] ?? null) == $mc['id'] ? 'selected' : '' ?>>
                <?= clean($mc['name']) ?>
              </option>
              <?php endif; endforeach; ?>
            </select>
          </div>
          <div class="col-span-2">
            <label class="form-label">Deskripsi</label>
            <textarea name="description" class="form-input" rows="2"><?= clean($item['description'] ?? '') ?></textarea>
          </div>
          <div>
            <label class="form-label">Meta Title</label>
            <input type="text" name="meta_title" class="form-input" value="<?= clean($item['meta_title'] ?? '') ?>">
          </div>
          <div class="col-span-2">
            <label class="form-label">Meta Description</label>
            <textarea name="meta_desc" class="form-input" rows="2"><?= clean($item['meta_desc'] ?? '') ?></textarea>
          </div>
          <div class="col-span-2">
            <label class="form-label">Gambar Kategori</label>
            <?php if (!empty($item['image'])): ?>
              <div class="mb-2"><img src="<?= UPLOAD_URL . $item['image'] ?>" class="h-16 rounded-lg object-cover"></div>
            <?php endif; ?>
            <input type="file" name="image" accept="image/*" class="form-input">
          </div>
          <div class="flex items-center gap-2">
            <input type="checkbox" name="is_active" id="is_active_k" <?= ($item['is_active'] ?? 1) ? 'checked' : '' ?>>
            <label for="is_active_k" class="text-sm text-gray-700 cursor-pointer">Aktif</label>
          </div>
        </div>
        <div class="flex gap-2 pt-2">
          <button type="submit" class="btn-primary">Simpan Kategori</button>
          <a href="<?= $b ?>/admin/kategori" class="btn-secondary">Batal</a>
        </div>
      </form>
    </div>

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

    // Ambil semua data
    $allCats = $pdo->query("
        SELECT c.*, p.name as parent_name,
               (SELECT COUNT(*) FROM products WHERE category_id=c.id) as prod_count
        FROM categories c
        LEFT JOIN categories p ON c.parent_id = p.id
        ORDER BY c.sort_order ASC, c.name
    ")->fetchAll();

    $parents  = array_filter($allCats, fn($c) => $c['parent_id'] === null);
    // Group children by parent_id
    $childMap = [];
    foreach ($allCats as $c) {
        if ($c['parent_id'] !== null) {
            $childMap[$c['parent_id']][] = $c;
        }
    }

    $totalParent = count($parents);
    $totalChild  = count($allCats) - $totalParent;
    ?>

    <!-- Header + Search -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-5">
      <div>
        <p class="text-sm text-gray-500">
          <span class="font-semibold text-gray-700"><?= $totalParent ?></span> kategori inti,
          <span class="font-semibold text-gray-700"><?= $totalChild ?></span> sub-kategori
        </p>
        <p class="text-xs text-gray-400 mt-0.5">⠿ Seret kartu untuk mengubah urutan tampil</p>
      </div>
      <div class="flex items-center gap-2">
        <!-- Searchbar -->
        <div class="relative">
          <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm">🔍</span>
          <input type="text" id="searchKat" placeholder="Cari kategori..."
                 class="pl-8 pr-4 py-2 text-sm border border-gray-200 rounded-xl focus:outline-none focus:border-rose-400 w-48">
        </div>
        <a href="<?= $b ?>/admin/kategori?action=tambah" class="btn-primary whitespace-nowrap">+ Tambah</a>
      </div>
    </div>

    <!-- Toast -->
    <div id="toastReorder" class="hidden fixed bottom-5 right-5 bg-gray-800 text-white text-sm px-4 py-2.5 rounded-xl shadow-lg z-50">
      ✅ Urutan berhasil disimpan
    </div>

    <!-- Empty state saat search tidak ketemu -->
    <div id="emptySearch" class="hidden text-center py-12 text-gray-400">
      <div class="text-4xl mb-2">🔍</div>
      <p class="text-sm">Tidak ada kategori yang cocok</p>
    </div>

    <!-- ===================== KATEGORI INTI ===================== -->
    <div class="mb-2" id="sectionParent">
      <h3 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-3">Kategori Inti</h3>
      <div id="sortParent" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
        <?php foreach ($parents as $c):
              $childCount = count($childMap[$c['id']] ?? []);
        ?>
        <div class="cat-card bg-white border border-rose-100 rounded-xl p-4 flex gap-3 items-start cursor-grab active:cursor-grabbing hover:shadow-md transition-shadow"
             data-id="<?= $c['id'] ?>" data-name="<?= strtolower(clean($c['name'])) ?>">
          <div class="text-gray-300 hover:text-gray-500 mt-0.5 select-none text-lg leading-none pt-0.5">⠿</div>
          <?php if (!empty($c['image'])): ?>
            <img src="<?= UPLOAD_URL . $c['image'] ?>" class="w-10 h-10 rounded-lg object-cover flex-shrink-0">
          <?php else: ?>
            <div class="w-10 h-10 rounded-lg bg-rose-50 flex items-center justify-center flex-shrink-0 text-rose-300 text-lg">🌸</div>
          <?php endif; ?>
          <div class="flex-1 min-w-0">
            <div class="flex items-start justify-between gap-1">
              <p class="font-semibold text-gray-800 text-sm truncate"><?= clean($c['name']) ?></p>
              <span class="<?= $c['is_active'] ? 'badge-active' : 'badge-inactive' ?> flex-shrink-0 text-xs">
                <?= $c['is_active'] ? 'Aktif' : 'Nonaktif' ?>
              </span>
            </div>
            <p class="text-xs text-gray-400 mt-0.5">
              <?= $c['prod_count'] ?> produk
              <?php if ($childCount > 0): ?>
                · <span class="text-rose-400"><?= $childCount ?> sub</span>
              <?php endif; ?>
            </p>
            <div class="flex items-center gap-2 mt-2 flex-wrap">
              <a href="<?= $b ?>/admin/kategori?action=edit&id=<?= $c['id'] ?>"
                 class="text-xs bg-amber-50 text-amber-700 border border-amber-200 px-2 py-0.5 rounded-lg hover:bg-amber-100">Edit</a>
              <a href="<?= $b ?>/admin/kategori?toggle=1&id=<?= $c['id'] ?>"
                 class="text-xs bg-gray-50 text-gray-600 border border-gray-200 px-2 py-0.5 rounded-lg hover:bg-gray-100">
                <?= $c['is_active'] ? 'Nonaktifkan' : 'Aktifkan' ?>
              </a>
              <a href="<?= $b ?>/<?= $c['slug'] ?>" target="_blank" class="text-xs text-rose-400 hover:underline">↗</a>
              <a href="<?= $b ?>/admin/kategori?hapus=1&id=<?= $c['id'] ?>"
                 onclick="return confirm('Hapus kategori ini?')"
                 class="text-xs text-red-400 hover:text-red-600 ml-auto">Hapus</a>
            </div>
          </div>
        </div>
        <?php endforeach; ?>
      </div>
    </div>

    <!-- ===================== SUB-KATEGORI (ACCORDION) ===================== -->
    <?php if (!empty($childMap)): ?>
    <div class="mt-8" id="sectionChild">
      <h3 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-3">Sub-Kategori</h3>

      <div class="space-y-2" id="accordionContainer">
        <?php foreach ($parents as $parent):
              $subs = $childMap[$parent['id']] ?? [];
              if (empty($subs)) continue;
        ?>
        <div class="accordion-group border border-amber-100 rounded-xl overflow-hidden bg-white"
             data-parent-name="<?= strtolower(clean($parent['name'])) ?>">

          <!-- Accordion Header -->
          <button type="button"
                  onclick="toggleAccordion(this)"
                  class="accordion-btn w-full flex items-center justify-between px-4 py-3 hover:bg-amber-50 transition-colors text-left">
            <div class="flex items-center gap-3">
              <?php if (!empty($parent['image'])): ?>
                <img src="<?= UPLOAD_URL . $parent['image'] ?>" class="w-7 h-7 rounded-lg object-cover flex-shrink-0">
              <?php else: ?>
                <div class="w-7 h-7 rounded-lg bg-rose-50 flex items-center justify-center text-rose-300 text-sm flex-shrink-0">🌸</div>
              <?php endif; ?>
              <span class="font-semibold text-gray-800 text-sm"><?= clean($parent['name']) ?></span>
              <span class="text-xs bg-amber-100 text-amber-700 px-2 py-0.5 rounded-full font-medium">
                <?= count($subs) ?> sub
              </span>
            </div>
            <span class="accordion-chevron text-gray-400 transition-transform duration-200 text-sm">▼</span>
          </button>

          <!-- Accordion Body -->
          <div class="accordion-body hidden border-t border-amber-50 px-4 py-3">
            <div class="sub-sort-list space-y-2" data-parent="<?= $parent['id'] ?>">
              <?php foreach ($subs as $sub): ?>
              <div class="sub-card flex items-center gap-3 bg-amber-50 border border-amber-100 rounded-xl px-3 py-2.5 cursor-grab active:cursor-grabbing hover:shadow-sm transition-shadow"
                   data-id="<?= $sub['id'] ?>" data-name="<?= strtolower(clean($sub['name'])) ?>">
                <!-- Drag handle -->
                <div class="text-gray-300 hover:text-gray-500 select-none text-base leading-none flex-shrink-0">⠿</div>
                <!-- Gambar -->
                <?php if (!empty($sub['image'])): ?>
                  <img src="<?= UPLOAD_URL . $sub['image'] ?>" class="w-8 h-8 rounded-lg object-cover flex-shrink-0">
                <?php else: ?>
                  <div class="w-8 h-8 rounded-lg bg-white flex items-center justify-center flex-shrink-0 text-amber-300">🌼</div>
                <?php endif; ?>
                <!-- Info -->
                <div class="flex-1 min-w-0">
                  <p class="font-medium text-gray-800 text-sm truncate"><?= clean($sub['name']) ?></p>
                  <p class="text-xs text-gray-400"><?= $sub['prod_count'] ?> produk</p>
                </div>
                <!-- Badge status -->
                <span class="<?= $sub['is_active'] ? 'badge-active' : 'badge-inactive' ?> text-xs flex-shrink-0">
                  <?= $sub['is_active'] ? 'Aktif' : 'Nonaktif' ?>
                </span>
                <!-- Aksi -->
                <div class="flex items-center gap-1.5 flex-shrink-0">
                  <a href="<?= $b ?>/admin/kategori?action=edit&id=<?= $sub['id'] ?>"
                     class="text-xs bg-white text-amber-700 border border-amber-200 px-2 py-0.5 rounded-lg hover:bg-amber-50">Edit</a>
                  <a href="<?= $b ?>/admin/kategori?toggle=1&id=<?= $sub['id'] ?>"
                     class="text-xs bg-white text-gray-600 border border-gray-200 px-2 py-0.5 rounded-lg hover:bg-gray-50">
                    <?= $sub['is_active'] ? 'Nonaktif' : 'Aktif' ?>
                  </a>
                  <a href="<?= $b ?>/<?= $sub['slug'] ?>" target="_blank" class="text-xs text-rose-400 hover:underline">↗</a>
                  <a href="<?= $b ?>/admin/kategori?hapus=1&id=<?= $sub['id'] ?>"
                     onclick="return confirm('Hapus sub-kategori ini?')"
                     class="text-xs text-red-400 hover:text-red-600">✕</a>
                </div>
              </div>
              <?php endforeach; ?>
            </div>
            <!-- Shortcut tambah sub -->
            <a href="<?= $b ?>/admin/kategori?action=tambah"
               class="mt-2 flex items-center gap-1.5 text-xs text-rose-400 hover:text-rose-600 w-fit">
              <span>+</span> Tambah sub-kategori untuk <?= clean($parent['name']) ?>
            </a>
          </div>
        </div>
        <?php endforeach; ?>
      </div>
    </div>
    <?php endif; ?>

    <!-- SortableJS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Sortable/1.15.2/Sortable.min.js"></script>
    <script>
    // ── Toast ────────────────────────────────────────────────
    function showToast() {
        const t = document.getElementById('toastReorder');
        t.classList.remove('hidden');
        clearTimeout(t._timer);
        t._timer = setTimeout(() => t.classList.add('hidden'), 2500);
    }

    // ── Simpan urutan via AJAX ───────────────────────────────
    function saveOrder(container, cardClass) {
        const ids = [...container.querySelectorAll('.' + cardClass)]
                    .map(c => c.dataset.id).join(',');
        fetch('', {
            method: 'POST',
            headers: {'Content-Type': 'application/x-www-form-urlencoded'},
            body: 'reorder=1&ids=' + ids
        }).then(r => r.json()).then(d => { if (d.ok) showToast(); });
    }

    // ── Sortable: Kategori Inti ──────────────────────────────
    const parentEl = document.getElementById('sortParent');
    if (parentEl) {
        Sortable.create(parentEl, {
            animation: 150,
            ghostClass: 'opacity-40',
            handle: '.cat-card',
            onEnd: () => saveOrder(parentEl, 'cat-card')
        });
    }

    // ── Sortable: tiap Sub-Kategori list ────────────────────
    document.querySelectorAll('.sub-sort-list').forEach(el => {
        Sortable.create(el, {
            animation: 150,
            ghostClass: 'opacity-40',
            handle: '.sub-card',
            onEnd: () => saveOrder(el, 'sub-card')
        });
    });

    // ── Accordion ───────────────────────────────────────────
    function toggleAccordion(btn) {
        const body    = btn.nextElementSibling;
        const chevron = btn.querySelector('.accordion-chevron');
        const isOpen  = !body.classList.contains('hidden');
        body.classList.toggle('hidden', isOpen);
        chevron.style.transform = isOpen ? '' : 'rotate(180deg)';
    }

    // ── Searchbar ───────────────────────────────────────────
    document.getElementById('searchKat').addEventListener('input', function () {
        const q = this.value.toLowerCase().trim();

        // Filter kartu kategori inti
        let visibleParent = 0;
        document.querySelectorAll('#sortParent .cat-card').forEach(card => {
            const match = !q || card.dataset.name.includes(q);
            card.style.display = match ? '' : 'none';
            if (match) visibleParent++;
        });

        // Filter accordion sub-kategori
        let visibleChild = 0;
        document.querySelectorAll('.accordion-group').forEach(group => {
            let groupMatch = false;
            const parentName = group.dataset.parentName;

            group.querySelectorAll('.sub-card').forEach(card => {
                const match = !q || card.dataset.name.includes(q) || parentName.includes(q);
                card.style.display = match ? '' : 'none';
                if (match) groupMatch = true;
            });

            // Kalau ada sub yang match, buka accordion-nya
            if (q && groupMatch) {
                const body    = group.querySelector('.accordion-body');
                const chevron = group.querySelector('.accordion-chevron');
                body.classList.remove('hidden');
                chevron.style.transform = 'rotate(180deg)';
            }
            group.style.display = groupMatch || !q ? '' : 'none';
            if (groupMatch) visibleChild++;
        });

        // Sembunyikan section header kalau kosong
        const sp = document.getElementById('sectionParent');
        const sc = document.getElementById('sectionChild');
        if (sp) sp.style.display = visibleParent === 0 && q ? 'none' : '';
        if (sc) sc.style.display = visibleChild === 0 && q ? 'none' : '';

        // Empty state
        document.getElementById('emptySearch').classList.toggle(
            'hidden', visibleParent > 0 || visibleChild > 0 || !q
        );
    });
    </script>
<?php } ?>
<?php require_once __DIR__ . '/../includes/admin_footer.php'; ?>