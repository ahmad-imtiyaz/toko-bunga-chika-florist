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
    $stmt = $pdo->prepare("UPDATE gallery SET sort_order=? WHERE id=?");
    foreach (array_values($ids) as $i => $gid) {
        $stmt->execute([$i + 1, $gid]);
    }
    echo json_encode(['ok' => true]);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = [
        'title'     => clean($_POST['title'] ?? ''),
        'category'  => clean($_POST['category'] ?? ''),
        'alt_text'  => clean($_POST['alt_text'] ?? ''),
        'is_active' => isset($_POST['is_active']) ? 1 : 0,
    ];

    // Upload gambar
    if (!empty($_FILES['image']['name'])) {
        $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
        if (in_array($ext, ['jpg','jpeg','png','webp'])) {
            $fn = 'gallery-' . time() . '-' . mt_rand(100,999) . '.' . $ext;
            move_uploaded_file($_FILES['image']['tmp_name'], UPLOAD_DIR . $fn);
            $data['image'] = $fn;
        } else {
            $_SESSION['error'] = 'Format file tidak didukung. Gunakan JPG, PNG, atau WebP.';
            header('Location: ' . $b . '/admin/galeri?action=' . $_POST['action_type'] . ($id ? "&id=$id" : ''));
            exit;
        }
    }

    if ($_POST['action_type'] === 'tambah') {
        if (!isset($data['image'])) {
            $_SESSION['error'] = 'Harap pilih file gambar.';
            header('Location: ' . $b . '/admin/galeri?action=tambah');
            exit;
        }
        $max = (int)$pdo->query("SELECT MAX(sort_order) FROM gallery")->fetchColumn();
        $data['sort_order'] = $max + 1;
        $cols = implode(',', array_keys($data));
        $vals = ':' . implode(',:', array_keys($data));
        $pdo->prepare("INSERT INTO gallery ($cols) VALUES ($vals)")->execute($data);
        $_SESSION['success'] = 'Foto berhasil diupload.';
    } elseif ($_POST['action_type'] === 'edit' && $id) {
        if (!isset($data['image'])) unset($data['image']);
        $sets = implode(',', array_map(fn($k) => "$k=:$k", array_keys($data)));
        $data['id'] = $id;
        $pdo->prepare("UPDATE gallery SET $sets WHERE id=:id")->execute($data);
        $_SESSION['success'] = 'Foto berhasil diperbarui.';
    }
    header('Location: ' . $b . '/admin/galeri');
    exit;
}

if (isset($_GET['toggle']) && $id) {
    $cur = $pdo->prepare("SELECT is_active FROM gallery WHERE id=?");
    $cur->execute([$id]);
    $pdo->prepare("UPDATE gallery SET is_active=? WHERE id=?")->execute([$cur->fetchColumn() ? 0 : 1, $id]);
    header('Location: ' . $b . '/admin/galeri');
    exit;
}

if (isset($_GET['hapus']) && $id) {
    $row = $pdo->prepare("SELECT image FROM gallery WHERE id=?");
    $row->execute([$id]);
    $r = $row->fetch();
    if ($r && $r['image'] && file_exists(UPLOAD_DIR . $r['image'])) {
        @unlink(UPLOAD_DIR . $r['image']);
    }
    $pdo->prepare("DELETE FROM gallery WHERE id=?")->execute([$id]);
    $_SESSION['success'] = 'Foto berhasil dihapus.';
    header('Location: ' . $b . '/admin/galeri');
    exit;
}

// ============================================================
$admin_title = 'Manajemen Galeri';
require_once __DIR__ . '/../includes/admin_header.php';

// Ambil semua kategori dari tabel categories (aktif, urut sort_order)
// Ambil parent + sub terpisah untuk grouping di dropdown
$allCategories = $pdo->query("
    SELECT c.id, c.name, c.parent_id, p.name as parent_name
    FROM categories c
    LEFT JOIN categories p ON c.parent_id = p.id
    WHERE c.is_active = 1
    ORDER BY COALESCE(c.parent_id, c.id), c.parent_id IS NOT NULL, c.sort_order, c.name
")->fetchAll();

// Pisah parent dan build grouped structure
$catParents = [];
$catChildren = [];
foreach ($allCategories as $cat) {
    if ($cat['parent_id'] === null) {
        $catParents[$cat['id']] = $cat['name'];
    } else {
        $catChildren[$cat['parent_id']][] = $cat;
    }
}

// ============================================================
// FORM TAMBAH / EDIT
// ============================================================
if ($action === 'tambah' || ($action === 'edit' && $id)) {
    $item = [];
    if ($action === 'edit') {
        $s = $pdo->prepare("SELECT * FROM gallery WHERE id=?");
        $s->execute([$id]);
        $item = $s->fetch();
    }
    ?>
    <div class="max-w-xl">
      <div class="flex items-center gap-3 mb-5">
        <a href="<?= $b ?>/admin/galeri" class="text-gray-400 hover:text-rose-600">← Kembali</a>
        <h2 class="font-display font-bold text-gray-800"><?= $action === 'tambah' ? 'Upload Foto' : 'Edit Foto' ?></h2>
      </div>

      <?php if (!empty($_SESSION['error'])): ?>
        <div class="bg-red-50 border border-red-200 text-red-600 text-sm rounded-lg px-4 py-3 mb-4">
          ⚠️ <?= clean($_SESSION['error']) ?>
        </div>
        <?php unset($_SESSION['error']); ?>
      <?php endif; ?>

      <form method="POST" enctype="multipart/form-data" class="space-y-4">
        <input type="hidden" name="action_type" value="<?= $action ?>">

        <!-- Upload area -->
        <div class="bg-white rounded-xl border border-rose-100 p-6">
          <h3 class="font-semibold text-gray-700 text-sm border-b border-gray-100 pb-2 mb-4">🖼️ Foto</h3>

          <?php if ($action === 'edit' && !empty($item['image'])): ?>
          <div class="mb-4">
            <p class="text-xs text-gray-400 mb-2">Foto saat ini:</p>
            <img src="<?= UPLOAD_URL . $item['image'] ?>" alt="<?= clean($item['alt_text'] ?? '') ?>"
                 class="h-40 w-full object-cover rounded-xl border border-rose-100">
          </div>
          <?php endif; ?>

          <!-- Drag & drop upload zone -->
          <div id="dropZone"
               class="border-2 border-dashed border-rose-200 rounded-xl p-8 text-center cursor-pointer hover:border-rose-400 hover:bg-rose-50 transition-all"
               onclick="document.getElementById('fileInput').click()">
            <div id="dropIcon" class="text-4xl mb-2">📷</div>
            <p id="dropText" class="text-sm font-medium text-gray-500">
              <?= $action === 'tambah' ? 'Klik atau seret foto ke sini' : 'Klik untuk ganti foto (opsional)' ?>
            </p>
            <p class="text-xs text-gray-400 mt-1">JPG, PNG, WebP — Maks 5MB</p>
            <input type="file" id="fileInput" name="image" accept="image/jpeg,image/png,image/webp"
                   class="hidden" <?= $action === 'tambah' ? 'required' : '' ?>>
          </div>

          <!-- Preview gambar yang dipilih -->
          <div id="imagePreviewWrap" class="hidden mt-3">
            <img id="imagePreview" src="" alt="" class="w-full max-h-48 object-cover rounded-xl border border-rose-100">
            <p id="imageFileName" class="text-xs text-gray-400 mt-1 text-center"></p>
          </div>
        </div>

        <!-- Info foto -->
        <div class="bg-white rounded-xl border border-rose-100 p-6 space-y-4">
          <h3 class="font-semibold text-gray-700 text-sm border-b border-gray-100 pb-2">📝 Informasi Foto</h3>

          <div>
            <label class="form-label">Judul Foto</label>
            <input type="text" name="title" class="form-input"
                   value="<?= clean($item['title'] ?? '') ?>"
                   placeholder="Rangkaian Bunga Mawar Merah">
          </div>

          <!-- KATEGORI: dropdown dari tabel categories -->
          <div>
            <label class="form-label">Kategori</label>
            <select name="category" class="form-input">
              <option value="">-- Pilih Kategori --</option>
              <?php foreach ($catParents as $pid => $pname): ?>
                <?php if (empty($catChildren[$pid])): ?>
                  <!-- Parent tanpa sub -->
                  <option value="<?= clean($pname) ?>"
                          <?= ($item['category'] ?? '') === $pname ? 'selected' : '' ?>>
                    <?= clean($pname) ?>
                  </option>
                <?php else: ?>
                  <!-- Parent sebagai optgroup -->
                  <optgroup label="<?= clean($pname) ?>">
                    <!-- Opsi untuk parent itu sendiri -->
                    <option value="<?= clean($pname) ?>"
                            <?= ($item['category'] ?? '') === $pname ? 'selected' : '' ?>>
                      <?= clean($pname) ?> (semua)
                    </option>
                    <!-- Sub-kategorinya -->
                    <?php foreach ($catChildren[$pid] as $child): ?>
                    <option value="<?= clean($child['name']) ?>"
                            <?= ($item['category'] ?? '') === $child['name'] ? 'selected' : '' ?>>
                      └ <?= clean($child['name']) ?>
                    </option>
                    <?php endforeach; ?>
                  </optgroup>
                <?php endif; ?>
              <?php endforeach; ?>
            </select>
            <p class="text-xs text-gray-400 mt-1">Kategori diambil dari manajemen kategori produk</p>
          </div>

          <div>
            <label class="form-label">
              Alt Text
              <span class="text-gray-400 font-normal text-xs">(teks alternatif untuk SEO & aksesibilitas)</span>
            </label>
            <input type="text" name="alt_text" class="form-input"
                   value="<?= clean($item['alt_text'] ?? '') ?>"
                   placeholder="Rangkaian bunga mawar merah untuk ulang tahun di Jakarta">
          </div>

          <div class="flex items-center gap-2">
            <input type="checkbox" name="is_active" id="is_active_g" <?= ($item['is_active'] ?? 1) ? 'checked' : '' ?>>
            <label for="is_active_g" class="text-sm text-gray-700 cursor-pointer">Aktif (tampil di galeri website)</label>
          </div>
        </div>

        <div class="flex gap-2 pb-6">
          <button type="submit" class="btn-primary">
            <?= $action === 'tambah' ? '📤 Upload Foto' : '💾 Simpan Perubahan' ?>
          </button>
          <a href="<?= $b ?>/admin/galeri" class="btn-secondary">Batal</a>
        </div>
      </form>
    </div>

    <script>
    // ── Drag & drop upload zone ───────────────────────────────
    const dropZone  = document.getElementById('dropZone');
    const fileInput = document.getElementById('fileInput');
    const prevWrap  = document.getElementById('imagePreviewWrap');
    const prevImg   = document.getElementById('imagePreview');
    const prevName  = document.getElementById('imageFileName');

    function handleFile(file) {
        if (!file) return;
        const reader = new FileReader();
        reader.onload = e => {
            prevImg.src = e.target.result;
            prevName.textContent = file.name + ' (' + (file.size / 1024).toFixed(0) + ' KB)';
            prevWrap.classList.remove('hidden');
            document.getElementById('dropIcon').textContent = '✅';
            document.getElementById('dropText').textContent = 'Foto siap diupload';
        };
        reader.readAsDataURL(file);
    }

    fileInput.addEventListener('change', () => handleFile(fileInput.files[0]));

    dropZone.addEventListener('dragover', e => { e.preventDefault(); dropZone.classList.add('border-rose-400','bg-rose-50'); });
    dropZone.addEventListener('dragleave', () => dropZone.classList.remove('border-rose-400','bg-rose-50'));
    dropZone.addEventListener('drop', e => {
        e.preventDefault();
        dropZone.classList.remove('border-rose-400','bg-rose-50');
        const file = e.dataTransfer.files[0];
        if (file) {
            const dt = new DataTransfer();
            dt.items.add(file);
            fileInput.files = dt.files;
            handleFile(file);
        }
    });
    </script>

<?php
// ============================================================
// LIST VIEW — PHOTO GRID
// ============================================================
} else {
    if (!empty($_SESSION['success'])): ?>
      <div class="bg-green-50 border border-green-200 text-green-700 text-sm rounded-lg px-4 py-3 mb-4">
        ✅ <?= clean($_SESSION['success']) ?>
      </div>
    <?php unset($_SESSION['success']); endif;

    if (!empty($_SESSION['error'])): ?>
      <div class="bg-red-50 border border-red-200 text-red-600 text-sm rounded-lg px-4 py-3 mb-4">
        ⚠️ <?= clean($_SESSION['error']) ?>
      </div>
    <?php unset($_SESSION['error']); endif;

    $list = $pdo->query("SELECT * FROM gallery ORDER BY sort_order ASC, id ASC")->fetchAll();
    $totalAktif = count(array_filter($list, fn($g) => $g['is_active']));

    // ── Bangun daftar kategori untuk filter pill dari tabel categories ──
    // Ambil nama kategori yang BENAR-BENAR dipakai di gallery
    $usedCatNames = array_unique(array_filter(array_column($list, 'category')));

    // Ambil semua kategori aktif dari tabel categories, urut sort_order
    $allCatRows = $pdo->query("
        SELECT name FROM categories
        WHERE is_active = 1
        ORDER BY COALESCE(parent_id, id), parent_id IS NOT NULL, sort_order, name
    ")->fetchAll(PDO::FETCH_COLUMN);

    // Filter: hanya tampilkan kategori yang ada fotonya di gallery
    $filterCats = array_values(array_filter($allCatRows, fn($name) => in_array($name, $usedCatNames)));
    ?>

    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-5">
      <div>
        <p class="text-sm text-gray-500">
          <span class="font-semibold text-gray-700"><?= count($list) ?></span> foto ·
          <span class="text-green-600 font-medium"><?= $totalAktif ?> aktif</span>
          <?php if (!empty($filterCats)): ?>
          · <span class="text-gray-400"><?= count($filterCats) ?> kategori</span>
          <?php endif; ?>
        </p>
        <p class="text-xs text-gray-400 mt-0.5">⠿ Seret foto untuk mengubah urutan tampil</p>
      </div>
      <a href="<?= $b ?>/admin/galeri?action=tambah" class="btn-primary whitespace-nowrap">📤 Upload Foto</a>
    </div>

    <!-- Filter & Search -->
    <div class="flex flex-col gap-3 mb-5">
      <!-- Baris 1: Search + Filter status -->
      <div class="flex gap-2">
        <div class="relative flex-1">
          <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm">🔍</span>
          <input type="text" id="searchGaleri" placeholder="Cari judul atau alt text..."
                 class="pl-8 pr-4 py-2 text-sm border border-gray-200 rounded-xl focus:outline-none focus:border-rose-400 w-full">
        </div>
        <select id="filterStatus" class="text-sm border border-gray-200 rounded-xl px-3 py-2 focus:outline-none focus:border-rose-400 bg-white">
          <option value="">Semua Status</option>
          <option value="1">Aktif</option>
          <option value="0">Nonaktif</option>
        </select>
      </div>
      <!-- Baris 2: Filter kategori pill (dari tabel categories) -->
      <?php if (!empty($filterCats)): ?>
      <div class="flex flex-wrap gap-1.5 items-center">
        <button onclick="filterKat('')"
                class="kat-btn active text-xs px-3 py-1.5 rounded-xl border border-rose-300 bg-rose-500 text-white font-medium transition-all"
                data-kat="">Semua</button>
        <?php foreach ($filterCats as $catName): ?>
        <button onclick="filterKat('<?= addslashes(strtolower(clean($catName))) ?>')"
                class="kat-btn text-xs px-3 py-1.5 rounded-xl border border-gray-200 bg-white text-gray-600 hover:border-rose-300 hover:text-rose-600 font-medium transition-all"
                data-kat="<?= strtolower(clean($catName)) ?>">
          <?= clean($catName) ?>
        </button>
        <?php endforeach; ?>
      </div>
      <?php endif; ?>
    </div>

    <!-- Toast -->
    <div id="toastReorder" class="hidden fixed bottom-5 right-5 bg-gray-800 text-white text-sm px-4 py-2.5 rounded-xl shadow-lg z-50">
      ✅ Urutan berhasil disimpan
    </div>

    <!-- Lightbox -->
    <div id="lightbox" class="hidden fixed inset-0 bg-black/80 z-50 flex items-center justify-center p-4" onclick="closeLightbox()">
      <div class="relative max-w-3xl w-full" onclick="event.stopPropagation()">
        <button onclick="closeLightbox()" class="absolute -top-10 right-0 text-white text-2xl hover:text-rose-300">✕</button>
        <img id="lightboxImg" src="" alt="" class="w-full max-h-[80vh] object-contain rounded-2xl">
        <p id="lightboxCaption" class="text-center text-white text-sm mt-3 opacity-75"></p>
      </div>
    </div>

    <!-- Empty state -->
    <div id="emptySearch" class="hidden text-center py-16 text-gray-400">
      <div class="text-4xl mb-2">🔍</div>
      <p class="text-sm">Tidak ada foto yang cocok</p>
    </div>

    <?php if (empty($list)): ?>
    <div class="text-center py-20 text-gray-400">
      <div class="text-6xl mb-3">🖼️</div>
      <p class="text-base font-medium text-gray-500">Galeri masih kosong</p>
      <p class="text-sm mt-1">Upload foto pertama untuk mulai membangun galeri</p>
      <a href="<?= $b ?>/admin/galeri?action=tambah" class="mt-4 inline-block btn-primary">📤 Upload Foto Pertama</a>
    </div>
    <?php else: ?>

    <!-- Photo Grid -->
    <div id="galeriGrid" class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-3">
      <?php foreach ($list as $g): ?>
      <div class="galeri-card group relative bg-white border border-rose-100 rounded-2xl overflow-hidden cursor-grab active:cursor-grabbing hover:shadow-xl transition-all duration-200 <?= !$g['is_active'] ? 'opacity-50' : '' ?>"
           data-id="<?= $g['id'] ?>"
           data-title="<?= strtolower(clean($g['title'] ?? '')) ?>"
           data-alt="<?= strtolower(clean($g['alt_text'] ?? '')) ?>"
           data-kat="<?= strtolower(clean($g['category'] ?? '')) ?>"
           data-active="<?= $g['is_active'] ?>">

        <!-- Foto -->
        <div class="aspect-square overflow-hidden bg-rose-50 relative"
             onclick="openLightbox('<?= UPLOAD_URL . $g['image'] ?>', '<?= clean($g['alt_text'] ?: $g['title']) ?>')">
          <img src="<?= UPLOAD_URL . $g['image'] ?>"
               alt="<?= clean($g['alt_text'] ?? $g['title'] ?? '') ?>"
               loading="lazy"
               class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300 cursor-zoom-in">

          <!-- Overlay -->
          <div class="absolute inset-0 bg-black/0 group-hover:bg-black/30 transition-all duration-200 flex items-center justify-center">
            <span class="text-white text-2xl opacity-0 group-hover:opacity-100 transition-opacity">🔍</span>
          </div>

          <!-- Nonaktif overlay -->
          <?php if (!$g['is_active']): ?>
          <div class="absolute top-2 left-2">
            <span class="badge-inactive text-xs">Nonaktif</span>
          </div>
          <?php endif; ?>

          <!-- Kategori badge -->
          <?php if (!empty($g['category'])): ?>
          <div class="absolute top-2 right-2">
            <span class="text-xs bg-black/50 text-white px-2 py-0.5 rounded-full backdrop-blur-sm">
              <?= clean($g['category']) ?>
            </span>
          </div>
          <?php endif; ?>

          <!-- Drag handle -->
          <div class="absolute bottom-2 right-2 text-white/60 group-hover:text-white/90 select-none text-sm transition-colors">⠿</div>
        </div>

        <!-- Info + Aksi -->
        <div class="p-2.5">
          <p class="text-xs font-medium text-gray-700 truncate mb-2">
            <?= !empty($g['title']) ? clean($g['title']) : '<span class="text-gray-300">Tanpa judul</span>' ?>
          </p>
          <div class="flex items-center gap-1">
            <a href="<?= $b ?>/admin/galeri?action=edit&id=<?= $g['id'] ?>"
               class="flex-1 text-center text-xs bg-amber-50 text-amber-700 border border-amber-200 px-1.5 py-1 rounded-lg hover:bg-amber-100 font-medium">
              ✏️ Edit
            </a>
            <a href="<?= $b ?>/admin/galeri?toggle=1&id=<?= $g['id'] ?>"
               class="text-xs bg-gray-50 text-gray-600 border border-gray-200 px-1.5 py-1 rounded-lg hover:bg-gray-100"
               title="<?= $g['is_active'] ? 'Nonaktifkan' : 'Aktifkan' ?>">
              <?= $g['is_active'] ? '🔕' : '✅' ?>
            </a>
            <a href="<?= $b ?>/admin/galeri?hapus=1&id=<?= $g['id'] ?>"
               onclick="return confirm('Hapus foto ini? File gambar juga akan dihapus permanen.')"
               class="text-xs text-red-400 hover:text-red-600 px-1.5 py-1 rounded-lg hover:bg-red-50 border border-transparent hover:border-red-100">
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

    // ── Lightbox ──────────────────────────────────────────────
    function openLightbox(src, caption) {
        document.getElementById('lightboxImg').src = src;
        document.getElementById('lightboxCaption').textContent = caption;
        document.getElementById('lightbox').classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }
    function closeLightbox() {
        document.getElementById('lightbox').classList.add('hidden');
        document.body.style.overflow = '';
    }
    document.addEventListener('keydown', e => { if (e.key === 'Escape') closeLightbox(); });

    // ── Sortable ─────────────────────────────────────────────
    const grid = document.getElementById('galeriGrid');
    Sortable.create(grid, {
        animation: 200,
        ghostClass: 'opacity-30',
        handle: '.galeri-card',
        filter: 'a, button',
        preventOnFilter: false,
        onEnd() {
            const ids = [...grid.querySelectorAll('.galeri-card')]
                        .filter(c => c.style.display !== 'none')
                        .map(c => c.dataset.id).join(',');
            fetch('', {
                method: 'POST',
                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                body: 'reorder=1&ids=' + ids
            }).then(r => r.json()).then(d => { if (d.ok) showToast(); });
        }
    });

    // ── Filter kategori (dari tabel categories) ───────────────
    let activeKat = '';
    function filterKat(kat) {
        activeKat = kat;
        document.querySelectorAll('.kat-btn').forEach(btn => {
            const isActive = btn.dataset.kat === kat;
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
        const q      = document.getElementById('searchGaleri').value.toLowerCase().trim();
        const status = document.getElementById('filterStatus').value;
        let visible  = 0;

        document.querySelectorAll('.galeri-card').forEach(card => {
            const match =
                (!q         || card.dataset.title.includes(q) || card.dataset.alt.includes(q)) &&
                (!activeKat || card.dataset.kat === activeKat) &&
                (!status    || card.dataset.active === status);
            card.style.display = match ? '' : 'none';
            if (match) visible++;
        });

        document.getElementById('emptySearch').classList.toggle('hidden', visible > 0);
    }

    document.getElementById('searchGaleri').addEventListener('input', applyFilter);
    document.getElementById('filterStatus').addEventListener('change', applyFilter);
    </script>
    <?php endif; ?>
<?php } ?>
<?php require_once __DIR__ . '/../includes/admin_footer.php'; ?>