<?php
// ============================================================
// LOGIKA CRUD — SEBELUM ADMIN HEADER
// ============================================================
require_once __DIR__ . '/../../includes/config.php';
requireAdminLogin();

$pdo    = getDB();
$action = $_GET['action'] ?? 'list';
$id     = (int)($_GET['id'] ?? 0);
$b      = BASE_URL;

// ── Helper slug ───────────────────────────────────────────
function makeBlogCatSlug(string $text): string {
    $text = strtolower(trim($text));
    $text = preg_replace('/[^a-z0-9\s-]/', '', $text);
    $text = preg_replace('/[\s-]+/', '-', $text);
    return trim($text, '-');
}

// ── AJAX: simpan urutan drag-and-drop ────────────────────
if (isset($_POST['reorder']) && !empty($_POST['ids'])) {
    $ids = array_filter(array_map('intval', explode(',', $_POST['ids'])));
    $stmt = $pdo->prepare("UPDATE blog_categories SET urutan=? WHERE id=?");
    foreach (array_values($ids) as $i => $cid) {
        $stmt->execute([$i + 1, $cid]);
    }
    echo json_encode(['ok' => true]);
    exit;
}

// ── POST: Tambah / Edit ───────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name   = clean($_POST['name']        ?? '');
    $slug   = makeBlogCatSlug($_POST['slug'] ?? $name);
    $desc   = clean($_POST['description'] ?? '');
    $status = in_array($_POST['status'] ?? '', ['active','inactive']) ? $_POST['status'] : 'active';

    if (!$name || !$slug) {
        $_SESSION['error'] = 'Nama dan slug wajib diisi.';
    } else {
        try {
            if ($_POST['action_type'] === 'tambah') {
                // Auto urutan = MAX + 1
                $maxUrutan = (int)$pdo->query("SELECT COALESCE(MAX(urutan),0) FROM blog_categories")->fetchColumn();
                $pdo->prepare("INSERT INTO blog_categories (name, slug, description, status, urutan) VALUES (?,?,?,?,?)")
                    ->execute([$name, $slug, $desc, $status, $maxUrutan + 1]);
                $_SESSION['success'] = 'Kategori blog berhasil ditambahkan.';
            } elseif ($_POST['action_type'] === 'edit' && $id) {
                $urutan = (int)($_POST['urutan'] ?? 0);
                $pdo->prepare("UPDATE blog_categories SET name=?, slug=?, description=?, status=?, urutan=? WHERE id=?")
                    ->execute([$name, $slug, $desc, $status, $urutan, $id]);
                $_SESSION['success'] = 'Kategori blog berhasil diperbarui.';
            }
        } catch (PDOException $e) {
            $_SESSION['error'] = 'Gagal menyimpan: ' . $e->getMessage();
        }
    }
    header('Location: ' . $b . '/admin/blog-categories');
    exit;
}

// ── Toggle status ─────────────────────────────────────────
if (isset($_GET['toggle']) && $id) {
    $cur = $pdo->prepare("SELECT status FROM blog_categories WHERE id=?");
    $cur->execute([$id]);
    $newStatus = $cur->fetchColumn() === 'active' ? 'inactive' : 'active';
    $pdo->prepare("UPDATE blog_categories SET status=? WHERE id=?")->execute([$newStatus, $id]);
    header('Location: ' . $b . '/admin/blog-categories');
    exit;
}

// ── Hapus ─────────────────────────────────────────────────
if (isset($_GET['hapus']) && $id) {
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM blogs WHERE blog_category_id=?");
    $stmt->execute([$id]);
    $count = (int)$stmt->fetchColumn();
    if ($count > 0) {
        $_SESSION['error'] = "Kategori tidak bisa dihapus — masih memiliki {$count} artikel.";
    } else {
        $pdo->prepare("DELETE FROM blog_categories WHERE id=?")->execute([$id]);
        $_SESSION['success'] = 'Kategori blog berhasil dihapus.';
    }
    header('Location: ' . $b . '/admin/blog-categories');
    exit;
}

// ── Fetch data ────────────────────────────────────────────
$categories = $pdo->query("
    SELECT bc.*, COUNT(b.id) AS total_blogs
    FROM blog_categories bc
    LEFT JOIN blogs b ON b.blog_category_id = bc.id
    GROUP BY bc.id
    ORDER BY bc.urutan ASC, bc.id ASC
")->fetchAll();

$edit_item = null;
if ($action === 'edit' && $id) {
    $s = $pdo->prepare("SELECT * FROM blog_categories WHERE id=?");
    $s->execute([$id]);
    $edit_item = $s->fetch();
}

// ============================================================
$admin_title = 'Kategori Blog';
require_once __DIR__ . '/../includes/admin_header.php';
?>

<!-- Top bar -->
<div class="flex items-center justify-between mb-6">
  <div>
    <p class="text-xs text-gray-400 mt-0.5">⠿ Seret kartu untuk mengubah urutan tampil</p>
  </div>
  <a href="<?= $b ?>/admin/blog" class="text-sm bg-gray-100 hover:bg-gray-200 text-gray-600 px-4 py-2 rounded-xl font-medium transition">← Ke Blog</a>
</div>

<!-- Toast reorder -->
<div id="toastReorder" class="hidden fixed bottom-5 right-5 bg-gray-800 text-white text-sm px-4 py-2.5 rounded-xl shadow-lg z-50">
  ✅ Urutan berhasil disimpan
</div>

<div class="grid lg:grid-cols-3 gap-6">

  <!-- ══ FORM TAMBAH / EDIT ══════════════════════════════ -->
  <div class="lg:col-span-1">
    <div class="bg-white rounded-xl border border-rose-100 p-6 sticky top-20">
      <h2 class="font-display font-bold text-gray-800 text-base mb-5">
        <?= $edit_item ? '✏️ Edit Kategori' : '➕ Tambah Kategori' ?>
      </h2>

      <form method="POST" class="space-y-4">
        <input type="hidden" name="action_type" value="<?= $edit_item ? 'edit' : 'tambah' ?>">
        <?php if ($edit_item): ?>
        <input type="hidden" name="id" value="<?= $edit_item['id'] ?>">
        <?php endif; ?>

        <div>
          <label class="form-label">Nama Kategori *</label>
          <input type="text" name="name" id="cat-name" required
                 class="form-input"
                 value="<?= clean($edit_item['name'] ?? '') ?>"
                 placeholder="Contoh: Tips &amp; Trik"
                 oninput="autoSlugCat(this.value)">
        </div>

        <div>
          <label class="form-label">Slug *</label>
          <input type="text" name="slug" id="cat-slug" required
                 class="form-input"
                 value="<?= clean($edit_item['slug'] ?? '') ?>"
                 placeholder="tips-trik">
          <p class="text-xs text-gray-400 mt-1">
            /blog?kategori=<strong id="slug-preview" class="text-rose-500"><?= clean($edit_item['slug'] ?? '') ?></strong>
          </p>
        </div>

        <div>
          <label class="form-label">Deskripsi</label>
          <textarea name="description" class="form-input" rows="3"
                    placeholder="Deskripsi singkat kategori..."><?= clean($edit_item['description'] ?? '') ?></textarea>
        </div>

        <div class="grid grid-cols-2 gap-3">
          <div>
            <label class="form-label">Status</label>
            <select name="status" class="form-input">
              <option value="active"   <?= ($edit_item['status'] ?? 'active') === 'active'   ? 'selected' : '' ?>>Aktif</option>
              <option value="inactive" <?= ($edit_item['status'] ?? '') === 'inactive' ? 'selected' : '' ?>>Nonaktif</option>
            </select>
          </div>
          <?php if ($edit_item): ?>
          <div>
            <label class="form-label">Urutan</label>
            <input type="number" name="urutan" class="form-input"
                   value="<?= $edit_item['urutan'] ?? 0 ?>" min="0">
          </div>
          <?php endif; ?>
        </div>

        <div class="flex gap-2 pt-1">
          <button type="submit" class="btn-primary flex-1">
            <?= $edit_item ? 'Simpan Perubahan' : 'Tambah Kategori' ?>
          </button>
          <?php if ($edit_item): ?>
          <a href="<?= $b ?>/admin/blog-categories" class="btn-secondary">Batal</a>
          <?php endif; ?>
        </div>
      </form>
    </div>
  </div>

  <!-- ══ DAFTAR KATEGORI ══════════════════════════════════ -->
  <div class="lg:col-span-2">

    <!-- Search -->
    <div class="flex items-center gap-2 mb-4">
      <div class="relative flex-1">
        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm">🔍</span>
        <input type="text" id="searchKat" placeholder="Cari kategori..."
               class="pl-8 pr-4 py-2 text-sm border border-gray-200 rounded-xl focus:outline-none focus:border-rose-400 w-full">
      </div>
      <span class="text-xs text-gray-400 whitespace-nowrap"><?= count($categories) ?> kategori</span>
    </div>

    <!-- Empty state search -->
    <div id="emptySearch" class="hidden text-center py-10 text-gray-400">
      <div class="text-3xl mb-2">🔍</div>
      <p class="text-sm">Tidak ada kategori yang cocok</p>
    </div>

    <?php if (empty($categories)): ?>
    <div class="bg-white rounded-xl border border-rose-100 text-center py-16 text-gray-400">
      <div class="text-4xl mb-3">📂</div>
      <p class="font-medium text-sm">Belum ada kategori blog.</p>
      <p class="text-xs mt-1">Tambahkan kategori pertama di form sebelah kiri.</p>
    </div>
    <?php else: ?>

    <!-- Card grid — sortable -->
    <div id="catGrid" class="grid grid-cols-1 sm:grid-cols-2 gap-3">
      <?php foreach ($categories as $cat): ?>
      <div class="cat-card bg-white border border-rose-100 rounded-xl p-4 hover:shadow-md transition-shadow cursor-grab active:cursor-grabbing"
           data-id="<?= $cat['id'] ?>"
           data-name="<?= strtolower(clean($cat['name'])) ?>">

        <div class="flex items-start justify-between gap-2 mb-2">
          <div class="flex items-center gap-2 min-w-0">
            <div class="text-gray-300 hover:text-gray-500 select-none text-lg flex-shrink-0">⠿</div>
            <div class="w-7 h-7 rounded-lg bg-rose-50 flex items-center justify-center flex-shrink-0 text-rose-400 text-sm">📂</div>
            <p class="font-semibold text-gray-800 text-sm truncate"><?= clean($cat['name']) ?></p>
          </div>
          <span class="<?= $cat['status'] === 'active' ? 'badge-active' : 'badge-inactive' ?> flex-shrink-0">
            <?= $cat['status'] === 'active' ? 'Aktif' : 'Nonaktif' ?>
          </span>
        </div>

        <p class="text-xs text-gray-400 mb-1 font-mono truncate pl-9">slug: <?= clean($cat['slug']) ?></p>

        <?php if (!empty($cat['description'])): ?>
        <p class="text-xs text-gray-500 mb-2 line-clamp-2 pl-9"><?= clean($cat['description']) ?></p>
        <?php endif; ?>

        <div class="flex items-center justify-between mt-3 pl-9">
          <div class="flex items-center gap-1.5">
            <span class="text-xs bg-rose-50 text-rose-500 border border-rose-100 px-2 py-0.5 rounded-full font-medium">
              📝 <?= $cat['total_blogs'] ?> artikel
            </span>
          </div>
          <div class="flex items-center gap-1.5">
            <a href="<?= $b ?>/admin/blog-categories?action=edit&id=<?= $cat['id'] ?>"
               class="text-xs bg-amber-50 text-amber-700 border border-amber-200 px-2 py-0.5 rounded-lg hover:bg-amber-100">Edit</a>
            <a href="<?= $b ?>/admin/blog-categories?toggle=1&id=<?= $cat['id'] ?>"
               class="text-xs bg-gray-50 text-gray-600 border border-gray-200 px-2 py-0.5 rounded-lg hover:bg-gray-100">
              <?= $cat['status'] === 'active' ? 'Nonaktifkan' : 'Aktifkan' ?>
            </a>
            <?php if ($cat['total_blogs'] == 0): ?>
            <a href="<?= $b ?>/admin/blog-categories?hapus=1&id=<?= $cat['id'] ?>"
               onclick="return confirm('Hapus kategori \'<?= clean($cat['name']) ?>\'?')"
               class="text-xs text-red-400 hover:text-red-600">Hapus</a>
            <?php else: ?>
            <span class="text-xs text-gray-300 cursor-not-allowed"
                  title="Tidak bisa dihapus — masih ada <?= $cat['total_blogs'] ?> artikel">Hapus</span>
            <?php endif; ?>
          </div>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
    <?php endif; ?>

  </div>
</div>

<!-- SortableJS -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/Sortable/1.15.2/Sortable.min.js"></script>
<script>
// ── Toast ─────────────────────────────────────────────────
function showToast() {
    const t = document.getElementById('toastReorder');
    t.classList.remove('hidden');
    clearTimeout(t._t);
    t._t = setTimeout(() => t.classList.add('hidden'), 2500);
}

// ── Drag-drop reorder ─────────────────────────────────────
const catGrid = document.getElementById('catGrid');
if (catGrid) {
    Sortable.create(catGrid, {
        animation: 150,
        ghostClass: 'opacity-40',
        handle: '.cat-card',
        onEnd() {
            const ids = [...catGrid.querySelectorAll('.cat-card')]
                .filter(c => c.style.display !== 'none')
                .map(c => c.dataset.id).join(',');
            fetch('', {
                method: 'POST',
                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                body: 'reorder=1&ids=' + ids
            }).then(r => r.json()).then(d => { if (d.ok) showToast(); });
        }
    });
}

// ── Auto slug ──────────────────────────────────────────────
function autoSlugCat(val) {
    const slugEl = document.getElementById('cat-slug');
    if (slugEl.dataset.manual) return;
    const slug = val.toLowerCase()
        .replace(/[^a-z0-9\s-]/g, '')
        .replace(/[\s-]+/g, '-')
        .replace(/^-+|-+$/g, '');
    slugEl.value = slug;
    document.getElementById('slug-preview').textContent = slug;
}
document.getElementById('cat-slug').addEventListener('input', function () {
    this.dataset.manual = '1';
    document.getElementById('slug-preview').textContent = this.value;
});

// ── Search ──────────────────────────────────────────────────
document.getElementById('searchKat')?.addEventListener('input', function () {
    const q = this.value.toLowerCase().trim();
    let visible = 0;
    document.querySelectorAll('.cat-card').forEach(card => {
        const match = !q || card.dataset.name.includes(q);
        card.style.display = match ? '' : 'none';
        if (match) visible++;
    });
    document.getElementById('emptySearch').classList.toggle('hidden', visible > 0 || !q);
});
</script>

<?php require_once __DIR__ . '/../includes/admin_footer.php'; ?>