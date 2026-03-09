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
    $stmt = $pdo->prepare("UPDATE products SET sort_order=? WHERE id=?");
    foreach (array_values($ids) as $i => $pid) {
        $stmt->execute([$i + 1, $pid]);
    }
    echo json_encode(['ok' => true]);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $price_min = (float)preg_replace('/[^0-9]/', '', $_POST['price_min'] ?? '0');
    $price_max = (float)preg_replace('/[^0-9]/', '', $_POST['price_max'] ?? '0');

    if ($price_max > 0 && $price_min > $price_max) {
        $_SESSION['error']     = 'Harga minimal tidak boleh lebih besar dari harga maksimal.';
        $_SESSION['form_data'] = $_POST;
        $redirect_action = $_POST['action_type'] === 'tambah'
            ? $b . '/admin/produk?action=tambah'
            : $b . '/admin/produk?action=edit&id=' . $id;
        header('Location: ' . $redirect_action);
        exit;
    }

    $data = [
        'category_id' => (int)$_POST['category_id'],
        'name'        => clean($_POST['name'] ?? ''),
        'slug'        => makeSlug($_POST['slug'] ?? $_POST['name'] ?? ''),
        'short_desc'  => clean($_POST['short_desc'] ?? ''),
        'price_min'   => $price_min,
        'price_max'   => $price_max,
        'meta_title'  => clean($_POST['meta_title'] ?? ''),
        'meta_desc'   => clean($_POST['meta_desc'] ?? ''),
        'is_featured' => isset($_POST['is_featured']) ? 1 : 0,
        'is_active'   => isset($_POST['is_active']) ? 1 : 0,
    ];

    if (!empty($_FILES['image']['name'])) {
        $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
        if (in_array($ext, ['jpg','jpeg','png','webp'])) {
            $fn = 'produk-' . $data['slug'] . '.' . $ext;
            move_uploaded_file($_FILES['image']['tmp_name'], UPLOAD_DIR . $fn);
            $data['image'] = $fn;
        }
    }

    if ($_POST['action_type'] === 'tambah') {
        $max = (int)$pdo->query("SELECT MAX(sort_order) FROM products")->fetchColumn();
        $data['sort_order'] = $max + 1;
        $cols = implode(',', array_keys($data));
        $vals = ':' . implode(',:', array_keys($data));
        $pdo->prepare("INSERT INTO products ($cols) VALUES ($vals)")->execute($data);
        $_SESSION['success'] = 'Produk berhasil ditambahkan.';
    } elseif ($_POST['action_type'] === 'edit' && $id) {
        unset($data['slug']);
        if (empty($data['image'])) unset($data['image']);
        $sets = implode(',', array_map(fn($k) => "$k=:$k", array_keys($data)));
        $data['id'] = $id;
        $pdo->prepare("UPDATE products SET $sets WHERE id=:id")->execute($data);
        $_SESSION['success'] = 'Produk berhasil diperbarui.';
    }
    header('Location: ' . $b . '/admin/produk');
    exit;
}

if (isset($_GET['toggle']) && $id) {
    $cur = $pdo->prepare("SELECT is_active FROM products WHERE id=?");
    $cur->execute([$id]);
    $pdo->prepare("UPDATE products SET is_active=? WHERE id=?")->execute([$cur->fetchColumn() ? 0 : 1, $id]);
    header('Location: ' . $b . '/admin/produk');
    exit;
}

if (isset($_GET['hapus']) && $id) {
    $pdo->prepare("DELETE FROM products WHERE id=?")->execute([$id]);
    $_SESSION['success'] = 'Produk berhasil dihapus.';
    header('Location: ' . $b . '/admin/produk');
    exit;
}

// ============================================================
$admin_title = 'Manajemen Produk';
require_once __DIR__ . '/../includes/admin_header.php';

$allCats = $pdo->query("SELECT id,name,parent_id FROM categories WHERE is_active=1 ORDER BY parent_id IS NOT NULL,sort_order")->fetchAll();

// ============================================================
// FORM TAMBAH / EDIT
// ============================================================
if ($action === 'tambah' || ($action === 'edit' && $id)) {
    $item = [];
    if ($action === 'edit') {
        $s = $pdo->prepare("SELECT * FROM products WHERE id=?");
        $s->execute([$id]);
        $item = $s->fetch();
    }

    $fd      = $_SESSION['form_data'] ?? null;
    unset($_SESSION['form_data']);
    $val_min = $fd ? preg_replace('/[^0-9]/', '', $fd['price_min'] ?? '0') : ($item['price_min'] ?? 0);
    $val_max = $fd ? preg_replace('/[^0-9]/', '', $fd['price_max'] ?? '0') : ($item['price_max'] ?? 0);
    ?>
    <div class="max-w-2xl">
      <div class="flex items-center gap-3 mb-5">
        <a href="<?= $b ?>/admin/produk" class="text-gray-400 hover:text-rose-600">← Kembali</a>
        <h2 class="font-display font-bold text-gray-800"><?= $action === 'tambah' ? 'Tambah' : 'Edit' ?> Produk</h2>
      </div>

      <?php if (!empty($_SESSION['error'])): ?>
        <div class="bg-red-50 border border-red-200 text-red-600 text-sm rounded-lg px-4 py-3 mb-4 flex items-center gap-2">
          ⚠️ <?= clean($_SESSION['error']) ?>
        </div>
        <?php unset($_SESSION['error']); ?>
      <?php endif; ?>

      <form method="POST" enctype="multipart/form-data" class="bg-white rounded-xl border border-rose-100 p-6 space-y-4" id="formProduk">
        <input type="hidden" name="action_type" value="<?= $action ?>">
        <div class="grid grid-cols-2 gap-4">

          <div class="col-span-2">
            <label class="form-label">Nama Produk *</label>
            <input type="text" name="name" required class="form-input"
                   value="<?= clean($fd['name'] ?? $item['name'] ?? '') ?>">
          </div>

          <div class="col-span-2">
            <label class="form-label">Kategori *</label>
            <select name="category_id" required class="form-input">
              <option value="">-- Pilih Kategori --</option>
              <?php foreach ($allCats as $cat): ?>
              <option value="<?= $cat['id'] ?>"
                <?= ($fd['category_id'] ?? $item['category_id'] ?? 0) == $cat['id'] ? 'selected' : '' ?>>
                <?= $cat['parent_id'] ? '— ' : '' ?><?= clean($cat['name']) ?>
              </option>
              <?php endforeach; ?>
            </select>
          </div>

          <div class="col-span-2">
            <label class="form-label">Deskripsi Singkat</label>
            <input type="text" name="short_desc" class="form-input"
                   value="<?= clean($fd['short_desc'] ?? $item['short_desc'] ?? '') ?>">
          </div>

          <div>
            <label class="form-label">Harga Minimal (Rp)</label>
            <div class="relative">
              <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm font-medium">Rp</span>
              <input type="text" id="price_min_display" inputmode="numeric" class="form-input pl-9" placeholder="0"
                     value="<?= $val_min > 0 ? number_format((float)$val_min, 0, ',', '.') : '' ?>">
              <input type="hidden" name="price_min" id="price_min_raw" value="<?= (int)$val_min ?>">
            </div>
          </div>

          <div>
            <label class="form-label">Harga Maksimal (Rp) <span class="text-gray-400 font-normal text-xs">(opsional)</span></label>
            <div class="relative">
              <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm font-medium">Rp</span>
              <input type="text" id="price_max_display" inputmode="numeric" class="form-input pl-9" placeholder="kosong = harga tunggal"
                     value="<?= $val_max > 0 ? number_format((float)$val_max, 0, ',', '.') : '' ?>">
              <input type="hidden" name="price_max" id="price_max_raw" value="<?= (int)$val_max ?>">
            </div>
            <p id="price_error" class="text-red-500 text-xs mt-1 hidden">⚠️ Harga maksimal harus lebih besar dari harga minimal</p>
          </div>

          <div>
            <label class="form-label">Meta Title</label>
            <input type="text" name="meta_title" class="form-input"
                   value="<?= clean($fd['meta_title'] ?? $item['meta_title'] ?? '') ?>">
          </div>

          <div class="col-span-2">
            <label class="form-label">Meta Description</label>
            <textarea name="meta_desc" class="form-input" rows="2"><?= clean($fd['meta_desc'] ?? $item['meta_desc'] ?? '') ?></textarea>
          </div>

          <div class="col-span-2">
            <label class="form-label">Gambar Produk</label>
            <?php if (!empty($item['image'])): ?>
              <div class="mb-2"><img src="<?= UPLOAD_URL . $item['image'] ?>" class="h-20 rounded-lg object-cover"></div>
            <?php endif; ?>
            <input type="file" name="image" accept="image/*" class="form-input">
          </div>

          <div class="flex items-center gap-4 col-span-2">
            <label class="flex items-center gap-2 cursor-pointer">
              <input type="checkbox" name="is_featured"
                     <?= ($fd ? isset($fd['is_featured']) : ($item['is_featured'] ?? 0)) ? 'checked' : '' ?>>
              <span class="text-sm text-gray-700">Produk Unggulan</span>
            </label>
            <label class="flex items-center gap-2 cursor-pointer">
              <input type="checkbox" name="is_active"
                     <?= ($fd ? isset($fd['is_active']) : ($item['is_active'] ?? 1)) ? 'checked' : '' ?>>
              <span class="text-sm text-gray-700">Aktif</span>
            </label>
          </div>
        </div>

        <div class="flex gap-2 pt-2">
          <button type="submit" id="btnSimpan" class="btn-primary">Simpan Produk</button>
          <a href="<?= $b ?>/admin/produk" class="btn-secondary">Batal</a>
        </div>
      </form>
    </div>

    <script>
    function formatRupiah(val) {
        val = val.replace(/\D/g, '');
        return val.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
    }
    function setupHarga(dispId, rawId) {
        const disp = document.getElementById(dispId);
        const raw  = document.getElementById(rawId);
        disp.addEventListener('input', function() {
            const n = this.value.replace(/\D/g, '');
            this.value = n ? formatRupiah(n) : '';
            raw.value  = n || '0';
            validateHarga();
        });
    }
    setupHarga('price_min_display','price_min_raw');
    setupHarga('price_max_display','price_max_raw');

    function validateHarga() {
        const min = parseInt(document.getElementById('price_min_raw').value) || 0;
        const max = parseInt(document.getElementById('price_max_raw').value) || 0;
        const err = document.getElementById('price_error');
        const btn = document.getElementById('btnSimpan');
        const bad = max > 0 && min > max;
        err.classList.toggle('hidden', !bad);
        btn.disabled = bad;
        btn.classList.toggle('opacity-50', bad);
        btn.classList.toggle('cursor-not-allowed', bad);
        return !bad;
    }
    document.getElementById('formProduk').addEventListener('submit', e => {
        if (!validateHarga()) e.preventDefault();
    });
    </script>

<?php
// ============================================================
// LIST VIEW — CARD GRID
// ============================================================
} else {
    if (!empty($_SESSION['success'])): ?>
      <div class="bg-green-50 border border-green-200 text-green-700 text-sm rounded-lg px-4 py-3 mb-4">
        ✅ <?= clean($_SESSION['success']) ?>
      </div>
    <?php unset($_SESSION['success']); endif;

    $products = $pdo->query("
        SELECT p.*, c.name as cat_name, c.id as cat_id
        FROM products p
        JOIN categories c ON p.category_id = c.id
        ORDER BY p.sort_order ASC, p.name ASC
    ")->fetchAll();

    // Kategori unik untuk filter dropdown
    $catOptions = [];
    foreach ($products as $p) {
        $catOptions[$p['cat_id']] = $p['cat_name'];
    }

    $total        = count($products);
    $totalAktif   = count(array_filter($products, fn($p) => $p['is_active']));
    $totalFeatured = count(array_filter($products, fn($p) => $p['is_featured']));
    ?>

    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-5">
      <div>
        <p class="text-sm text-gray-500">
          <span class="font-semibold text-gray-700"><?= $total ?></span> produk ·
          <span class="text-green-600 font-medium"><?= $totalAktif ?> aktif</span> ·
          <span class="text-amber-600 font-medium">★ <?= $totalFeatured ?> unggulan</span>
        </p>
        <p class="text-xs text-gray-400 mt-0.5">⠿ Seret kartu untuk mengubah urutan tampil</p>
      </div>
      <a href="<?= $b ?>/admin/produk?action=tambah" class="btn-primary whitespace-nowrap">+ Tambah Produk</a>
    </div>

    <!-- Search + Filter -->
    <div class="flex flex-col sm:flex-row gap-2 mb-5">
      <div class="relative flex-1">
        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm">🔍</span>
        <input type="text" id="searchProduk" placeholder="Cari nama produk..."
               class="pl-8 pr-4 py-2 text-sm border border-gray-200 rounded-xl focus:outline-none focus:border-rose-400 w-full">
      </div>
      <select id="filterKat" class="text-sm border border-gray-200 rounded-xl px-3 py-2 focus:outline-none focus:border-rose-400 bg-white">
        <option value="">Semua Kategori</option>
        <?php foreach ($catOptions as $cid => $cname): ?>
        <option value="<?= $cid ?>"><?= clean($cname) ?></option>
        <?php endforeach; ?>
      </select>
      <select id="filterStatus" class="text-sm border border-gray-200 rounded-xl px-3 py-2 focus:outline-none focus:border-rose-400 bg-white">
        <option value="">Semua Status</option>
        <option value="aktif">Aktif</option>
        <option value="nonaktif">Nonaktif</option>
        <option value="unggulan">Unggulan</option>
      </select>
    </div>

    <!-- Toast -->
    <div id="toastReorder" class="hidden fixed bottom-5 right-5 bg-gray-800 text-white text-sm px-4 py-2.5 rounded-xl shadow-lg z-50">
      ✅ Urutan berhasil disimpan
    </div>

    <!-- Empty state -->
    <div id="emptySearch" class="hidden text-center py-16 text-gray-400">
      <div class="text-4xl mb-2">🔍</div>
      <p class="text-sm">Tidak ada produk yang cocok</p>
    </div>

    <!-- Card Grid -->
    <div id="produkGrid" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
      <?php foreach ($products as $i => $p): ?>
      <div class="prod-card group bg-white border border-rose-100 rounded-2xl overflow-hidden cursor-grab active:cursor-grabbing hover:shadow-lg transition-all duration-200 flex flex-col"
           data-id="<?= $p['id'] ?>"
           data-name="<?= strtolower(clean($p['name'])) ?>"
           data-cat="<?= $p['cat_id'] ?>"
           data-active="<?= $p['is_active'] ?>"
           data-featured="<?= $p['is_featured'] ?>">

        <!-- Gambar -->
        <div class="relative w-full aspect-square bg-rose-50 overflow-hidden">
          <?php if (!empty($p['image'])): ?>
            <img src="<?= UPLOAD_URL . $p['image'] ?>"
                 class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                 onerror="this.parentElement.innerHTML='<div class=\'w-full h-full flex items-center justify-center text-rose-200 text-5xl\'>🌸</div>'">
          <?php else: ?>
            <div class="w-full h-full flex items-center justify-center text-rose-200 text-5xl">🌸</div>
          <?php endif; ?>

          <!-- Drag handle overlay -->
          <div class="absolute top-2 left-2 bg-white/80 backdrop-blur-sm rounded-lg px-1.5 py-1 text-gray-400 text-sm select-none opacity-0 group-hover:opacity-100 transition-opacity">⠿</div>

          <!-- Sort order badge -->
          <div class="absolute top-2 right-2 bg-white/90 backdrop-blur-sm rounded-lg px-2 py-0.5 text-xs font-bold text-gray-500">
            #<?= $i + 1 ?>
          </div>

          <!-- Badge unggulan -->
          <?php if ($p['is_featured']): ?>
          <div class="absolute bottom-2 left-2 bg-amber-400 text-white text-xs font-bold px-2 py-0.5 rounded-lg">
            ★ Unggulan
          </div>
          <?php endif; ?>

          <!-- Badge nonaktif -->
          <?php if (!$p['is_active']): ?>
          <div class="absolute inset-0 bg-gray-900/40 flex items-center justify-center">
            <span class="bg-gray-800 text-white text-xs font-bold px-3 py-1 rounded-full">Nonaktif</span>
          </div>
          <?php endif; ?>
        </div>

        <!-- Info -->
        <div class="p-3 flex flex-col flex-1">
          <p class="font-semibold text-gray-800 text-sm leading-snug line-clamp-2 mb-1"><?= clean($p['name']) ?></p>
          <p class="text-xs text-rose-400 mb-1">📁 <?= clean($p['cat_name']) ?></p>
          <p class="text-sm font-bold text-rose-600 mb-3"><?= formatHarga($p['price_min'], $p['price_max']) ?></p>

          <!-- Aksi -->
          <div class="flex items-center gap-1.5 mt-auto flex-wrap">
            <a href="<?= $b ?>/admin/produk?action=edit&id=<?= $p['id'] ?>"
               class="flex-1 text-center text-xs bg-amber-50 text-amber-700 border border-amber-200 px-2 py-1 rounded-lg hover:bg-amber-100 font-medium">
              ✏️ Edit
            </a>
            <a href="<?= $b ?>/admin/produk?toggle=1&id=<?= $p['id'] ?>"
               class="flex-1 text-center text-xs bg-gray-50 text-gray-600 border border-gray-200 px-2 py-1 rounded-lg hover:bg-gray-100">
              <?= $p['is_active'] ? '🔕 Nonaktif' : '✅ Aktifkan' ?>
            </a>
            <a href="<?= $b ?>/admin/produk?hapus=1&id=<?= $p['id'] ?>"
               onclick="return confirm('Hapus produk \'<?= clean($p['name']) ?>\'?')"
               class="text-xs text-red-400 hover:text-red-600 px-1 py-1 rounded-lg hover:bg-red-50 border border-transparent hover:border-red-100">
              🗑️
            </a>
          </div>
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

    // ── Sortable ─────────────────────────────────────────────
    const grid = document.getElementById('produkGrid');
    Sortable.create(grid, {
        animation: 200,
        ghostClass: 'opacity-30',
        handle: '.prod-card',
        filter: 'a, button',
        preventOnFilter: false,
        onEnd() {
            // Update badge nomor urut
            grid.querySelectorAll('.prod-card:not([style*="display: none"])').forEach((card, i) => {
                const badge = card.querySelector('.absolute.top-2.right-2');
                if (badge) badge.textContent = '#' + (i + 1);
            });
            // Kirim semua ID yang visible
            const ids = [...grid.querySelectorAll('.prod-card')]
                        .filter(c => c.style.display !== 'none')
                        .map(c => c.dataset.id).join(',');
            fetch('', {
                method: 'POST',
                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                body: 'reorder=1&ids=' + ids
            }).then(r => r.json()).then(d => { if (d.ok) showToast(); });
        }
    });

    // ── Filter & Search ───────────────────────────────────────
    function applyFilter() {
        const q      = document.getElementById('searchProduk').value.toLowerCase().trim();
        const katVal = document.getElementById('filterKat').value;
        const stVal  = document.getElementById('filterStatus').value;
        let visible  = 0;

        document.querySelectorAll('.prod-card').forEach((card, i) => {
            const nameMatch   = !q      || card.dataset.name.includes(q);
            const katMatch    = !katVal || card.dataset.cat === katVal;
            const statusMatch = !stVal
                || (stVal === 'aktif'    && card.dataset.active   === '1')
                || (stVal === 'nonaktif' && card.dataset.active   === '0')
                || (stVal === 'unggulan' && card.dataset.featured === '1');

            const show = nameMatch && katMatch && statusMatch;
            card.style.display = show ? '' : 'none';
            if (show) visible++;
        });

        // Re-nomor badge
        let num = 1;
        document.querySelectorAll('.prod-card').forEach(card => {
            if (card.style.display !== 'none') {
                const badge = card.querySelector('.absolute.top-2.right-2');
                if (badge) badge.textContent = '#' + num++;
            }
        });

        document.getElementById('emptySearch').classList.toggle('hidden', visible > 0);
    }

    document.getElementById('searchProduk').addEventListener('input', applyFilter);
    document.getElementById('filterKat').addEventListener('change', applyFilter);
    document.getElementById('filterStatus').addEventListener('change', applyFilter);
    </script>
<?php } ?>
<?php require_once __DIR__ . '/../includes/admin_footer.php'; ?>