<?php
// ============================================================
// SEMUA LOGIKA CRUD DI SINI — SEBELUM ADMIN HEADER (output HTML)
// ============================================================
require_once __DIR__ . '/../../includes/config.php';
requireAdminLogin();

$pdo    = getDB();
$action = $_GET['action'] ?? 'list';
$id     = (int)($_GET['id'] ?? 0);
$b      = BASE_URL;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $price_min = (float)preg_replace('/[^0-9]/', '', $_POST['price_min'] ?? '0');
    $price_max = (float)preg_replace('/[^0-9]/', '', $_POST['price_max'] ?? '0');

    // Validasi harga
    if ($price_max > 0 && $price_min > $price_max) {
        $_SESSION['error']      = 'Harga minimal tidak boleh lebih besar dari harga maksimal.';
        $_SESSION['form_data']  = $_POST;
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
        'sort_order'  => (int)($_POST['sort_order'] ?? 0),
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
// BARU LOAD HEADER SETELAH SEMUA REDIRECT SELESAI
// ============================================================
$admin_title = 'Manajemen Produk';
require_once __DIR__ . '/../includes/admin_header.php';

$allCats = $pdo->query("SELECT id,name,parent_id FROM categories WHERE is_active=1 ORDER BY parent_id IS NOT NULL,sort_order")->fetchAll();

if ($action === 'tambah' || ($action === 'edit' && $id)) {
    $item = [];
    if ($action === 'edit') {
        $s = $pdo->prepare("SELECT * FROM products WHERE id=?");
        $s->execute([$id]);
        $item = $s->fetch();
    }

    // Ambil form_data dari session jika ada (setelah validasi gagal)
    $fd = $_SESSION['form_data'] ?? null;
    unset($_SESSION['form_data']);

    // Nilai harga untuk ditampilkan di form (pakai form_data jika ada, fallback ke DB)
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
          <span>⚠️</span> <?= clean($_SESSION['error']) ?>
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

          <!-- Harga Min -->
          <div>
            <label class="form-label">Harga Minimal (Rp)</label>
            <div class="relative">
              <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm font-medium">Rp</span>
              <input type="text" id="price_min_display" inputmode="numeric"
                     class="form-input pl-9" placeholder="0"
                     value="<?= $val_min > 0 ? number_format((float)$val_min, 0, ',', '.') : '' ?>">
              <input type="hidden" name="price_min" id="price_min_raw" value="<?= (int)$val_min ?>">
            </div>
          </div>

          <!-- Harga Max -->
          <div>
            <label class="form-label">Harga Maksimal (Rp) <span class="text-gray-400 font-normal text-xs">(opsional)</span></label>
            <div class="relative">
              <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm font-medium">Rp</span>
              <input type="text" id="price_max_display" inputmode="numeric"
                     class="form-input pl-9" placeholder="0 = sama dengan minimal"
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

          <div>
            <label class="form-label">Sort Order</label>
            <input type="number" name="sort_order" class="form-input"
                   value="<?= (int)($fd['sort_order'] ?? $item['sort_order'] ?? 0) ?>">
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

          <div class="flex items-center gap-4">
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
    // Format angka dengan titik ribuan
    function formatRupiah(val) {
        val = val.replace(/\D/g, '');
        return val.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
    }

    function setupHargaInput(displayId, rawId) {
        const display = document.getElementById(displayId);
        const raw     = document.getElementById(rawId);
        display.addEventListener('input', function () {
            const clean = this.value.replace(/\D/g, '');
            this.value  = clean ? formatRupiah(clean) : '';
            raw.value   = clean || '0';
            validateHarga();
        });
    }

    setupHargaInput('price_min_display', 'price_min_raw');
    setupHargaInput('price_max_display', 'price_max_raw');

    function validateHarga() {
        const min = parseInt(document.getElementById('price_min_raw').value) || 0;
        const max = parseInt(document.getElementById('price_max_raw').value) || 0;
        const err = document.getElementById('price_error');
        const btn = document.getElementById('btnSimpan');
        if (max > 0 && min > max) {
            err.classList.remove('hidden');
            btn.disabled = true;
            btn.classList.add('opacity-50', 'cursor-not-allowed');
            return false;
        } else {
            err.classList.add('hidden');
            btn.disabled = false;
            btn.classList.remove('opacity-50', 'cursor-not-allowed');
            return true;
        }
    }

    document.getElementById('formProduk').addEventListener('submit', function (e) {
        if (!validateHarga()) e.preventDefault();
    });
    </script>
    <?php
} else {
    if (!empty($_SESSION['success'])): ?>
      <div class="bg-green-50 border border-green-200 text-green-700 text-sm rounded-lg px-4 py-3 mb-5 flex items-center gap-2">
        <span>✅</span> <?= clean($_SESSION['success']) ?>
      </div>
    <?php unset($_SESSION['success']); endif;

    $products = $pdo->query("SELECT p.*,c.name as cat_name FROM products p JOIN categories c ON p.category_id=c.id ORDER BY p.is_active DESC,p.sort_order ASC,p.name ASC")->fetchAll();
    ?>
    <div class="flex justify-between items-center mb-5">
      <p class="text-sm text-gray-500"><?= count($products) ?> produk</p>
      <a href="<?= $b ?>/admin/produk?action=tambah" class="btn-primary">+ Tambah Produk</a>
    </div>
    <div class="bg-white rounded-xl border border-rose-100 overflow-hidden">
      <table class="admin-table w-full">
        <thead>
          <tr>
            <th>Gambar</th><th>Nama Produk</th><th>Kategori</th><th>Harga</th>
            <th style="text-align:center">Unggulan</th>
            <th style="text-align:center">Status</th>
            <th style="text-align:center">Aksi</th>
          </tr>
        </thead>
        <tbody>
        <?php foreach ($products as $p): ?>
        <tr>
          <td><img src="<?= UPLOAD_URL . ($p['image'] ?? '') ?>" class="w-12 h-10 object-cover rounded-lg" onerror="this.style.display='none'"></td>
          <td class="font-medium text-gray-800"><?= clean($p['name']) ?></td>
          <td class="text-gray-500 text-xs"><?= clean($p['cat_name']) ?></td>
          <td class="text-xs text-rose-600 font-semibold"><?= formatHarga($p['price_min'], $p['price_max']) ?></td>
          <td style="text-align:center">
            <?= $p['is_featured'] ? '<span style="color:#d97706;font-size:.8rem;font-weight:bold">★ Ya</span>' : '<span style="color:#d1d5db">-</span>' ?>
          </td>
          <td style="text-align:center">
            <a href="<?= $b ?>/admin/produk?toggle=1&id=<?= $p['id'] ?>">
              <span class="<?= $p['is_active'] ? 'badge-active' : 'badge-inactive' ?>"><?= $p['is_active'] ? 'Aktif' : 'Nonaktif' ?></span>
            </a>
          </td>
          <td style="text-align:center">
            <div style="display:flex;gap:4px;justify-content:center">
              <a href="<?= $b ?>/admin/produk?action=edit&id=<?= $p['id'] ?>" style="font-size:.75rem;background:#fef3c7;color:#b45309;padding:.25rem .75rem;border-radius:.5rem;border:1px solid #fde68a">Edit</a>
              <a href="<?= $b ?>/admin/produk?hapus=1&id=<?= $p['id'] ?>" onclick="return confirm('Hapus produk ini?')" class="btn-danger">Hapus</a>
            </div>
          </td>
        </tr>
        <?php endforeach; ?>
        </tbody>
      </table>
    </div>
<?php } ?>
<?php require_once __DIR__ . '/../includes/admin_footer.php'; ?>