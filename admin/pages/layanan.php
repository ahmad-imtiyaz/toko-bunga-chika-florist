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
    $stmt = $pdo->prepare("UPDATE service_pages SET sort_order=? WHERE id=?");
    foreach (array_values($ids) as $i => $lid) {
        $stmt->execute([$i + 1, $lid]);
    }
    echo json_encode(['ok' => true]);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $slug = makeSlug($_POST['slug'] ?? '') ?: makeSlug($_POST['title'] ?? '');
    $data = [
        'title'      => clean($_POST['title'] ?? ''),
        'slug'       => $slug,
        'h1_text'    => clean($_POST['h1_text'] ?? ''),
        'content'    => $_POST['content'] ?? '',
        'meta_title' => clean($_POST['meta_title'] ?? ''),
        'meta_desc'  => clean($_POST['meta_desc'] ?? ''),
        'is_active'  => isset($_POST['is_active']) ? 1 : 0,
    ];
    if ($_POST['action_type'] === 'tambah') {
        $max = (int)$pdo->query("SELECT MAX(sort_order) FROM service_pages")->fetchColumn();
        $data['sort_order'] = $max + 1;
        $cols = implode(',', array_keys($data));
        $vals = ':' . implode(',:', array_keys($data));
        $pdo->prepare("INSERT INTO service_pages ($cols) VALUES ($vals)")->execute($data);
        $_SESSION['success'] = 'Halaman layanan berhasil ditambahkan.';
    } elseif ($_POST['action_type'] === 'edit' && $id) {
        $sets = implode(',', array_map(fn($k) => "$k=:$k", array_keys($data)));
        $data['id'] = $id;
        $pdo->prepare("UPDATE service_pages SET $sets WHERE id=:id")->execute($data);
        $_SESSION['success'] = 'Halaman layanan berhasil diperbarui.';
    }
    header('Location: ' . $b . '/admin/layanan');
    exit;
}

if (isset($_GET['toggle']) && $id) {
    $cur = $pdo->prepare("SELECT is_active FROM service_pages WHERE id=?");
    $cur->execute([$id]);
    $pdo->prepare("UPDATE service_pages SET is_active=? WHERE id=?")->execute([$cur->fetchColumn() ? 0 : 1, $id]);
    header('Location: ' . $b . '/admin/layanan');
    exit;
}

if (isset($_GET['hapus']) && $id) {
    $pdo->prepare("DELETE FROM service_pages WHERE id=?")->execute([$id]);
    $_SESSION['success'] = 'Halaman berhasil dihapus.';
    header('Location: ' . $b . '/admin/layanan');
    exit;
}

// ============================================================
$admin_title = 'Halaman Layanan';
require_once __DIR__ . '/../includes/admin_header.php';

// ============================================================
// FORM TAMBAH / EDIT
// ============================================================
if ($action === 'tambah' || ($action === 'edit' && $id)) {
    $item = [];
    if ($action === 'edit') {
        $s = $pdo->prepare("SELECT * FROM service_pages WHERE id=?");
        $s->execute([$id]);
        $item = $s->fetch();
    }

    // Hitung panjang meta
    $metaTitleLen = mb_strlen($item['meta_title'] ?? '');
    $metaDescLen  = mb_strlen($item['meta_desc'] ?? '');
    ?>
    <div class="max-w-3xl">
      <div class="flex items-center gap-3 mb-5">
        <a href="<?= $b ?>/admin/layanan" class="text-gray-400 hover:text-rose-600">← Kembali</a>
        <h2 class="font-display font-bold text-gray-800"><?= $action === 'tambah' ? 'Tambah' : 'Edit' ?> Halaman Layanan</h2>
        <?php if ($action === 'edit' && !empty($item['slug'])): ?>
          <a href="<?= $b ?>/<?= $item['slug'] ?>" target="_blank"
             class="ml-auto text-xs text-rose-400 border border-rose-200 px-3 py-1 rounded-lg hover:bg-rose-50">
            Lihat Halaman ↗
          </a>
        <?php endif; ?>
      </div>

      <?php if (!empty($_SESSION['error'])): ?>
        <div class="bg-red-50 border border-red-200 text-red-600 text-sm rounded-lg px-4 py-3 mb-4">
          ⚠️ <?= clean($_SESSION['error']) ?>
        </div>
        <?php unset($_SESSION['error']); ?>
      <?php endif; ?>

      <form method="POST" class="space-y-4" id="formLayanan">
        <input type="hidden" name="action_type" value="<?= $action ?>">

        <!-- Info Dasar -->
        <div class="bg-white rounded-xl border border-rose-100 p-6 space-y-4">
          <h3 class="font-semibold text-gray-700 text-sm border-b border-gray-100 pb-2">📄 Informasi Dasar</h3>

          <div>
            <label class="form-label">Judul Halaman *</label>
            <input type="text" name="title" id="titleInput" required class="form-input"
                   value="<?= clean($item['title'] ?? '') ?>"
                   placeholder="Toko Bunga Online 24 Jam Indonesia">
          </div>

          <div>
            <label class="form-label">
              Slug URL
              <span class="text-gray-400 font-normal text-xs">(kosong = auto dari judul)</span>
            </label>
            <div class="relative">
              <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs"><?= parse_url($b, PHP_URL_HOST) ?>/</span>
              <input type="text" name="slug" id="slugInput" class="form-input pl-32 font-mono text-sm"
                     value="<?= clean($item['slug'] ?? '') ?>"
                     placeholder="toko-bunga-online-24-jam-indonesia">
            </div>
            <p class="text-xs text-gray-400 mt-1">URL: <span id="slugPreview" class="text-rose-500 font-mono"><?= $b ?>/<?= $item['slug'] ?? '...' ?></span></p>
          </div>

          <div>
            <label class="form-label">H1 Text <span class="text-gray-400 font-normal text-xs">(judul besar di halaman)</span></label>
            <input type="text" name="h1_text" class="form-input"
                   value="<?= clean($item['h1_text'] ?? '') ?>"
                   placeholder="Sama dengan judul jika dikosongkan">
          </div>

          <div class="flex items-center gap-2">
            <input type="checkbox" name="is_active" id="is_active_l" <?= ($item['is_active'] ?? 1) ? 'checked' : '' ?>>
            <label for="is_active_l" class="text-sm text-gray-700 cursor-pointer">Halaman aktif (tampil di website)</label>
          </div>
        </div>

        <!-- SEO -->
        <div class="bg-white rounded-xl border border-rose-100 p-6 space-y-4">
          <h3 class="font-semibold text-gray-700 text-sm border-b border-gray-100 pb-2">🔍 SEO</h3>

          <div>
            <div class="flex items-center justify-between mb-1">
              <label class="form-label mb-0">Meta Title</label>
              <span id="metaTitleCount" class="text-xs <?= $metaTitleLen > 60 ? 'text-red-500' : 'text-gray-400' ?>"><?= $metaTitleLen ?>/60</span>
            </div>
            <input type="text" name="meta_title" id="metaTitle" class="form-input"
                   value="<?= clean($item['meta_title'] ?? '') ?>"
                   placeholder="Toko Bunga Online 24 Jam - Pengiriman Seluruh Indonesia">
            <p class="text-xs text-gray-400 mt-1">Ideal 50–60 karakter.</p>
          </div>

          <div>
            <div class="flex items-center justify-between mb-1">
              <label class="form-label mb-0">Meta Description</label>
              <span id="metaDescCount" class="text-xs <?= $metaDescLen > 160 ? 'text-red-500' : 'text-gray-400' ?>"><?= $metaDescLen ?>/160</span>
            </div>
            <textarea name="meta_desc" id="metaDesc" class="form-input" rows="2"
                      placeholder="Pesan bunga online 24 jam dengan pengiriman same day ke seluruh Indonesia..."><?= clean($item['meta_desc'] ?? '') ?></textarea>
            <p class="text-xs text-gray-400 mt-1">Ideal 120–160 karakter.</p>
          </div>

          <!-- SERP Preview -->
          <div class="bg-gray-50 rounded-xl p-4 border border-gray-200">
            <p class="text-xs text-gray-400 font-medium mb-2">👁️ Preview Google SERP</p>
            <p id="serpTitle" class="text-blue-600 text-base font-medium leading-tight truncate">
              <?= clean($item['meta_title'] ?? $item['title'] ?? 'Meta Title') ?>
            </p>
            <p id="serpUrl" class="text-green-700 text-xs my-0.5"><?= $b ?>/<?= $item['slug'] ?? 'slug-halaman' ?></p>
            <p id="serpDesc" class="text-gray-600 text-xs line-clamp-2">
              <?= clean($item['meta_desc'] ?? 'Meta description akan tampil di sini...') ?>
            </p>
          </div>
        </div>

        <!-- Konten HTML -->
        <div class="bg-white rounded-xl border border-rose-100 p-6">
          <div class="flex items-center justify-between mb-3">
            <h3 class="font-semibold text-gray-700 text-sm">📝 Konten HTML</h3>
            <div class="flex items-center gap-2">
              <button type="button" onclick="togglePreview()"
                      class="text-xs bg-gray-100 text-gray-600 px-3 py-1 rounded-lg hover:bg-gray-200">
                👁️ Toggle Preview
              </button>
              <span class="text-xs text-gray-400">Kosong = gunakan template default</span>
            </div>
          </div>

          <!-- Editor -->
          <div id="editorWrap">
            <textarea name="content" id="contentEditor" class="form-input font-mono text-xs leading-relaxed" rows="14"
                      placeholder="<h2>Judul Konten</h2>&#10;<p>Isi konten halaman layanan...</p>"><?= htmlspecialchars($item['content'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>
          </div>

          <!-- Preview HTML -->
          <div id="htmlPreview" class="hidden mt-3 border border-gray-200 rounded-xl p-4 bg-white prose prose-sm max-w-none min-h-24 text-sm text-gray-700">
          </div>
        </div>

        <div class="flex gap-2 pb-6">
          <button type="submit" class="btn-primary">💾 Simpan Halaman</button>
          <a href="<?= $b ?>/admin/layanan" class="btn-secondary">Batal</a>
        </div>
      </form>
    </div>

    <script>
    // ── Auto slug dari judul ──────────────────────────────────
    const titleInput = document.getElementById('titleInput');
    const slugInput  = document.getElementById('slugInput');
    const slugPreview = document.getElementById('slugPreview');
    const serpUrl    = document.getElementById('serpUrl');
    const base       = '<?= $b ?>';

    function makeSlug(str) {
        return str.toLowerCase()
            .replace(/[àáâãäå]/g,'a').replace(/[èéêë]/g,'e')
            .replace(/[ìíîï]/g,'i').replace(/[òóôõö]/g,'o')
            .replace(/[ùúûü]/g,'u').replace(/[^a-z0-9\s-]/g,'')
            .replace(/[\s-]+/g,'-').replace(/^-+|-+$/g,'');
    }

    titleInput.addEventListener('input', function() {
        if (!slugInput.dataset.manual) {
            const s = makeSlug(this.value);
            slugInput.value = s;
            updateSlugPreview(s);
        }
        document.getElementById('serpTitle').textContent = this.value || 'Meta Title';
    });

    slugInput.addEventListener('input', function() {
        this.dataset.manual = '1';
        updateSlugPreview(this.value);
    });

    function updateSlugPreview(slug) {
        const url = base + '/' + (slug || '...');
        slugPreview.textContent = url;
        serpUrl.textContent = url;
    }

    // ── Counter meta ─────────────────────────────────────────
    function setupCounter(inputId, countId, max) {
        const el  = document.getElementById(inputId);
        const cnt = document.getElementById(countId);
        el.addEventListener('input', function() {
            const len = this.value.length;
            cnt.textContent = len + '/' + max;
            cnt.className   = 'text-xs ' + (len > max ? 'text-red-500 font-semibold' : 'text-gray-400');
            if (inputId === 'metaTitle') document.getElementById('serpTitle').textContent = this.value;
            if (inputId === 'metaDesc')  document.getElementById('serpDesc').textContent  = this.value;
        });
    }
    setupCounter('metaTitle', 'metaTitleCount', 60);
    setupCounter('metaDesc',  'metaDescCount',  160);

    // ── HTML Preview ──────────────────────────────────────────
    let previewOpen = false;
    function togglePreview() {
        previewOpen = !previewOpen;
        const wrap = document.getElementById('htmlPreview');
        const ed   = document.getElementById('contentEditor');
        wrap.classList.toggle('hidden', !previewOpen);
        if (previewOpen) wrap.innerHTML = ed.value || '<p class="text-gray-400">Konten kosong</p>';
    }
    document.getElementById('contentEditor').addEventListener('input', function() {
        if (previewOpen) document.getElementById('htmlPreview').innerHTML = this.value;
    });
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

    $pages = $pdo->query("SELECT * FROM service_pages ORDER BY sort_order ASC, title ASC")->fetchAll();
    $totalAktif = count(array_filter($pages, fn($p) => $p['is_active']));
    ?>

    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-5">
      <div>
        <p class="text-sm text-gray-500">
          <span class="font-semibold text-gray-700"><?= count($pages) ?></span> halaman layanan ·
          <span class="text-green-600 font-medium"><?= $totalAktif ?> aktif</span>
        </p>
        <p class="text-xs text-gray-400 mt-0.5">⠿ Seret baris untuk mengubah urutan tampil di navbar</p>
      </div>
      <a href="<?= $b ?>/admin/layanan?action=tambah" class="btn-primary whitespace-nowrap">+ Tambah Halaman</a>
    </div>

    <!-- Searchbar -->
    <div class="relative mb-4 max-w-sm">
      <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm">🔍</span>
      <input type="text" id="searchLayanan" placeholder="Cari halaman layanan..."
             class="pl-8 pr-4 py-2 text-sm border border-gray-200 rounded-xl focus:outline-none focus:border-rose-400 w-full">
    </div>

    <!-- Toast -->
    <div id="toastReorder" class="hidden fixed bottom-5 right-5 bg-gray-800 text-white text-sm px-4 py-2.5 rounded-xl shadow-lg z-50">
      ✅ Urutan berhasil disimpan
    </div>

    <!-- List drag-and-drop (bukan grid karena halaman layanan sedikit) -->
    <?php if (empty($pages)): ?>
    <div class="text-center py-16 text-gray-400">
      <div class="text-5xl mb-3">📄</div>
      <p class="text-sm font-medium text-gray-500">Belum ada halaman layanan</p>
      <a href="<?= $b ?>/admin/layanan?action=tambah" class="mt-3 inline-block btn-primary text-sm">+ Tambah Halaman Pertama</a>
    </div>
    <?php else: ?>

    <div id="layananList" class="space-y-2">
      <?php foreach ($pages as $i => $p): ?>
      <div class="layanan-card group bg-white border border-rose-100 rounded-xl px-4 py-3.5 flex items-center gap-4 cursor-grab active:cursor-grabbing hover:shadow-md transition-all"
           data-id="<?= $p['id'] ?>"
           data-name="<?= strtolower(clean($p['title'])) ?>">

        <!-- Drag handle + nomor -->
        <div class="flex items-center gap-2 flex-shrink-0">
          <div class="text-gray-300 group-hover:text-gray-500 select-none text-lg leading-none transition-colors">⠿</div>
          <span class="text-xs font-bold text-gray-300 w-5 text-center order-num"><?= $i + 1 ?></span>
        </div>

        <!-- Icon halaman -->
        <div class="w-9 h-9 rounded-xl bg-rose-50 flex items-center justify-center flex-shrink-0 text-base">
          📄
        </div>

        <!-- Info utama -->
        <div class="flex-1 min-w-0">
          <div class="flex items-center gap-2 flex-wrap">
            <p class="font-semibold text-gray-800 text-sm"><?= clean($p['title']) ?></p>
            <span class="<?= $p['is_active'] ? 'badge-active' : 'badge-inactive' ?> text-xs"><?= $p['is_active'] ? 'Aktif' : 'Nonaktif' ?></span>
          </div>
          <div class="flex items-center gap-2 mt-0.5">
            <p class="text-xs text-gray-400 font-mono">/<?= $p['slug'] ?></p>
            <a href="<?= $b ?>/<?= $p['slug'] ?>" target="_blank" class="text-xs text-rose-400 hover:underline">↗ lihat</a>
          </div>
          <?php if (!empty($p['meta_desc'])): ?>
          <p class="text-xs text-gray-400 mt-0.5 truncate max-w-lg"><?= clean(substr($p['meta_desc'], 0, 80)) ?>...</p>
          <?php endif; ?>
        </div>

        <!-- Aksi -->
        <div class="flex items-center gap-1.5 flex-shrink-0">
          <a href="<?= $b ?>/admin/layanan?action=edit&id=<?= $p['id'] ?>"
             class="text-xs bg-amber-50 text-amber-700 border border-amber-200 px-3 py-1.5 rounded-lg hover:bg-amber-100 font-medium">
            ✏️ Edit
          </a>
          <a href="<?= $b ?>/admin/layanan?toggle=1&id=<?= $p['id'] ?>"
             class="text-xs bg-gray-50 text-gray-600 border border-gray-200 px-2 py-1.5 rounded-lg hover:bg-gray-100"
             title="<?= $p['is_active'] ? 'Nonaktifkan' : 'Aktifkan' ?>">
            <?= $p['is_active'] ? '🔕' : '✅' ?>
          </a>
          <a href="<?= $b ?>/admin/layanan?hapus=1&id=<?= $p['id'] ?>"
             onclick="return confirm('Hapus halaman \'<?= clean($p['title']) ?>\'?')"
             class="text-xs text-red-400 hover:text-red-600 px-2 py-1.5 rounded-lg hover:bg-red-50 border border-transparent hover:border-red-100"
             title="Hapus">
            🗑️
          </a>
        </div>
      </div>
      <?php endforeach; ?>
    </div>

    <!-- Empty search state -->
    <div id="emptySearch" class="hidden text-center py-12 text-gray-400 mt-4">
      <div class="text-4xl mb-2">🔍</div>
      <p class="text-sm">Tidak ada halaman yang cocok</p>
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
    const list = document.getElementById('layananList');
    Sortable.create(list, {
        animation: 200,
        ghostClass: 'opacity-30',
        handle: '.layanan-card',
        filter: 'a, button',
        preventOnFilter: false,
        onEnd() {
            // Update nomor urut
            list.querySelectorAll('.order-num').forEach((el, i) => {
                el.textContent = i + 1;
            });
            const ids = [...list.querySelectorAll('.layanan-card')]
                        .filter(c => c.style.display !== 'none')
                        .map(c => c.dataset.id).join(',');
            fetch('', {
                method: 'POST',
                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                body: 'reorder=1&ids=' + ids
            }).then(r => r.json()).then(d => { if (d.ok) showToast(); });
        }
    });

    // ── Search ────────────────────────────────────────────────
    document.getElementById('searchLayanan').addEventListener('input', function() {
        const q = this.value.toLowerCase().trim();
        let visible = 0;
        document.querySelectorAll('.layanan-card').forEach(card => {
            const match = !q || card.dataset.name.includes(q);
            card.style.display = match ? '' : 'none';
            if (match) visible++;
        });
        document.getElementById('emptySearch').classList.toggle('hidden', visible > 0);
    });
    </script>
    <?php endif; ?>
<?php } ?>
<?php require_once __DIR__ . '/../includes/admin_footer.php'; ?>