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
    $stmt = $pdo->prepare("UPDATE cities SET sort_order=? WHERE id=?");
    foreach (array_values($ids) as $i => $cid) {
        $stmt->execute([$i + 1, $cid]);
    }
    echo json_encode(['ok' => true]);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = [
        'name'           => clean($_POST['name'] ?? ''),
        'slug'           => makeSlug($_POST['name'] ?? ''),
        'province'       => clean($_POST['province'] ?? ''),
        'tier'           => (int)($_POST['tier'] ?? 1),
        'description'    => clean($_POST['description'] ?? ''),
        'landmark_notes' => $_POST['landmark_notes'] ?? '',
        'meta_title'     => clean($_POST['meta_title'] ?? ''),
        'meta_desc'      => clean($_POST['meta_desc'] ?? ''),
        'is_active'      => isset($_POST['is_active']) ? 1 : 0,
    ];
    if ($_POST['action_type'] === 'tambah') {
        $max = (int)$pdo->query("SELECT MAX(sort_order) FROM cities")->fetchColumn();
        $data['sort_order'] = $max + 1;
        $cols = implode(',', array_keys($data));
        $vals = ':' . implode(',:', array_keys($data));
        $pdo->prepare("INSERT INTO cities ($cols) VALUES ($vals)")->execute($data);
        $_SESSION['success'] = 'Kota berhasil ditambahkan.';
    } elseif ($_POST['action_type'] === 'edit' && $id) {
        unset($data['slug']);
        $sets = implode(',', array_map(fn($k) => "$k=:$k", array_keys($data)));
        $data['id'] = $id;
        $pdo->prepare("UPDATE cities SET $sets WHERE id=:id")->execute($data);
        $_SESSION['success'] = 'Kota berhasil diperbarui.';
    }
    header('Location: ' . $b . '/admin/kota');
    exit;
}

if (isset($_GET['toggle']) && $id) {
    $cur = $pdo->prepare("SELECT is_active FROM cities WHERE id=?");
    $cur->execute([$id]);
    $pdo->prepare("UPDATE cities SET is_active=? WHERE id=?")->execute([$cur->fetchColumn() ? 0 : 1, $id]);
    header('Location: ' . $b . '/admin/kota');
    exit;
}

if (isset($_GET['hapus']) && $id) {
    $pdo->prepare("DELETE FROM cities WHERE id=?")->execute([$id]);
    $_SESSION['success'] = 'Kota berhasil dihapus.';
    header('Location: ' . $b . '/admin/kota');
    exit;
}

// ============================================================
$admin_title = 'Manajemen Kota';
require_once __DIR__ . '/../includes/admin_header.php';

// ============================================================
// FORM TAMBAH / EDIT
// ============================================================
if ($action === 'tambah' || ($action === 'edit' && $id)) {
    $item = [];
    if ($action === 'edit') {
        $s = $pdo->prepare("SELECT * FROM cities WHERE id=?");
        $s->execute([$id]);
        $item = $s->fetch();
    }
    ?>
    <div class="max-w-2xl">
      <div class="flex items-center gap-3 mb-5">
        <a href="<?= $b ?>/admin/kota" class="text-gray-400 hover:text-rose-600">← Kembali</a>
        <h2 class="font-display font-bold text-gray-800"><?= $action === 'tambah' ? 'Tambah' : 'Edit' ?> Kota</h2>
      </div>

      <?php if (!empty($_SESSION['error'])): ?>
        <div class="bg-red-50 border border-red-200 text-red-600 text-sm rounded-lg px-4 py-3 mb-4">
          ⚠️ <?= clean($_SESSION['error']) ?>
        </div>
        <?php unset($_SESSION['error']); ?>
      <?php endif; ?>

      <form method="POST" class="bg-white rounded-xl border border-rose-100 p-6 space-y-4">
        <input type="hidden" name="action_type" value="<?= $action ?>">
        <div class="grid grid-cols-2 gap-4">

          <div>
            <label class="form-label">Nama Kota *</label>
            <input type="text" name="name" required class="form-input" value="<?= clean($item['name'] ?? '') ?>">
          </div>

          <div>
            <label class="form-label">Provinsi</label>
            <input type="text" name="province" class="form-input" value="<?= clean($item['province'] ?? '') ?>">
          </div>

          <div class="col-span-2">
            <label class="form-label">Tier Prioritas</label>
            <div class="grid grid-cols-3 gap-2">
              <?php
              $tiers = [
                  1 => ['label' => 'Tier 1', 'sub' => 'Kota Besar', 'color' => 'border-rose-400 bg-rose-50 text-rose-700'],
                  2 => ['label' => 'Tier 2', 'sub' => 'Kota Menengah', 'color' => 'border-amber-400 bg-amber-50 text-amber-700'],
                  3 => ['label' => 'Tier 3', 'sub' => 'Kota Kecil', 'color' => 'border-gray-300 bg-gray-50 text-gray-600'],
              ];
              foreach ($tiers as $t => $info): ?>
              <label class="cursor-pointer">
                <input type="radio" name="tier" value="<?= $t ?>" class="hidden peer"
                       <?= ($item['tier'] ?? 1) == $t ? 'checked' : '' ?>>
                <div class="border-2 rounded-xl p-3 text-center transition-all peer-checked:<?= $info['color'] ?> border-gray-200 hover:border-gray-300">
                  <p class="font-bold text-sm"><?= $info['label'] ?></p>
                  <p class="text-xs text-gray-400 mt-0.5"><?= $info['sub'] ?></p>
                </div>
              </label>
              <?php endforeach; ?>
            </div>
          </div>

          <div class="col-span-2">
            <label class="form-label">Deskripsi</label>
            <textarea name="description" class="form-input" rows="2"><?= clean($item['description'] ?? '') ?></textarea>
          </div>

          <div class="col-span-2">
            <label class="form-label">
              Landmark Kota
              <span class="text-gray-400 font-normal text-xs">(untuk SEO – nama jalan, RS, mall, kampus)</span>
            </label>
            <textarea name="landmark_notes" class="form-input" rows="3"
                      placeholder="Jl. Sudirman, RS Siloam, Grand Indonesia, UI..."><?= clean($item['landmark_notes'] ?? '') ?></textarea>
          </div>

          <div>
            <label class="form-label">Meta Title</label>
            <input type="text" name="meta_title" class="form-input" value="<?= clean($item['meta_title'] ?? '') ?>">
          </div>

          <div class="col-span-2">
            <label class="form-label">Meta Description</label>
            <textarea name="meta_desc" class="form-input" rows="2"><?= clean($item['meta_desc'] ?? '') ?></textarea>
          </div>

          <div class="flex items-center gap-2 col-span-2">
            <input type="checkbox" name="is_active" id="is_active_c" <?= ($item['is_active'] ?? 1) ? 'checked' : '' ?>>
            <label for="is_active_c" class="text-sm text-gray-700 cursor-pointer">Aktif (tampil di website)</label>
          </div>
        </div>

        <div class="flex gap-2 pt-2">
          <button type="submit" class="btn-primary">Simpan Kota</button>
          <a href="<?= $b ?>/admin/kota" class="btn-secondary">Batal</a>
        </div>
      </form>
    </div>

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

    $cities = $pdo->query("
        SELECT c.*, (SELECT COUNT(*) FROM areas WHERE city_id=c.id) as area_count
        FROM cities c
        ORDER BY c.tier ASC, c.sort_order ASC, c.name ASC
    ")->fetchAll();

    $totalAktif = count(array_filter($cities, fn($c) => $c['is_active']));

    // Provinsi unik untuk filter
    $provinsiList = array_unique(array_filter(array_column($cities, 'province')));
    sort($provinsiList);
    ?>

    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-5">
      <div>
        <p class="text-sm text-gray-500">
          <span class="font-semibold text-gray-700"><?= count($cities) ?></span> kota ·
          <span class="text-green-600 font-medium"><?= $totalAktif ?> aktif</span>
        </p>
        <p class="text-xs text-gray-400 mt-0.5">⠿ Seret kartu untuk mengubah urutan tampil</p>
      </div>
      <a href="<?= $b ?>/admin/kota?action=tambah" class="btn-primary whitespace-nowrap">+ Tambah Kota</a>
    </div>

    <!-- Search + Filter -->
    <div class="flex flex-col sm:flex-row gap-2 mb-5">
      <div class="relative flex-1">
        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm">🔍</span>
        <input type="text" id="searchKota" placeholder="Cari nama kota..."
               class="pl-8 pr-4 py-2 text-sm border border-gray-200 rounded-xl focus:outline-none focus:border-rose-400 w-full">
      </div>
      <select id="filterProvinsi" class="text-sm border border-gray-200 rounded-xl px-3 py-2 focus:outline-none focus:border-rose-400 bg-white">
        <option value="">Semua Provinsi</option>
        <?php foreach ($provinsiList as $prov): ?>
        <option value="<?= strtolower(clean($prov)) ?>"><?= clean($prov) ?></option>
        <?php endforeach; ?>
      </select>
      <select id="filterTier" class="text-sm border border-gray-200 rounded-xl px-3 py-2 focus:outline-none focus:border-rose-400 bg-white">
        <option value="">Semua Tier</option>
        <option value="1">Tier 1 – Kota Besar</option>
        <option value="2">Tier 2 – Kota Menengah</option>
        <option value="3">Tier 3 – Kota Kecil</option>
      </select>
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
      <p class="text-sm">Tidak ada kota yang cocok</p>
    </div>

    <!-- Card Grid -->
    <div id="kotaGrid" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
      <?php
      $tierColors = [
          1 => ['bg' => 'bg-rose-50',   'border' => 'border-rose-200',   'badge' => 'bg-rose-100 text-rose-700',   'dot' => 'bg-rose-400'],
          2 => ['bg' => 'bg-amber-50',  'border' => 'border-amber-200',  'badge' => 'bg-amber-100 text-amber-700', 'dot' => 'bg-amber-400'],
          3 => ['bg' => 'bg-gray-50',   'border' => 'border-gray-200',   'badge' => 'bg-gray-100 text-gray-600',   'dot' => 'bg-gray-400'],
      ];
      foreach ($cities as $c):
          $tc = $tierColors[$c['tier']] ?? $tierColors[3];
      ?>
      <div class="kota-card group bg-white border <?= $tc['border'] ?> rounded-2xl overflow-hidden cursor-grab active:cursor-grabbing hover:shadow-lg transition-all duration-200 flex flex-col"
           data-id="<?= $c['id'] ?>"
           data-name="<?= strtolower(clean($c['name'])) ?>"
           data-provinsi="<?= strtolower(clean($c['province'] ?? '')) ?>"
           data-tier="<?= $c['tier'] ?>"
           data-active="<?= $c['is_active'] ?>">

        <!-- Header kartu berwarna per tier -->
        <div class="<?= $tc['bg'] ?> px-4 pt-4 pb-3 relative">
          <!-- Drag handle -->
          <div class="absolute top-3 right-3 text-gray-300 group-hover:text-gray-500 select-none text-base leading-none transition-colors">⠿</div>

          <!-- Tier badge -->
          <span class="inline-flex items-center gap-1 text-xs font-bold px-2 py-0.5 rounded-full <?= $tc['badge'] ?> mb-2">
            <span class="w-1.5 h-1.5 rounded-full <?= $tc['dot'] ?>"></span>
            Tier <?= $c['tier'] ?>
          </span>

          <h3 class="font-bold text-gray-800 text-base leading-tight pr-6"><?= clean($c['name']) ?></h3>
          <?php if (!empty($c['province'])): ?>
          <p class="text-xs text-gray-500 mt-0.5">📍 <?= clean($c['province']) ?></p>
          <?php endif; ?>
        </div>

        <!-- Body -->
        <div class="px-4 py-3 flex-1 flex flex-col">
          <!-- Stats -->
          <div class="flex items-center gap-3 mb-3">
            <div class="flex items-center gap-1.5">
              <span class="text-xs text-gray-400">Area:</span>
              <a href="<?= $b ?>/admin/area?city_id=<?= $c['id'] ?>"
                 class="text-sm font-bold text-blue-600 hover:underline"><?= $c['area_count'] ?></a>
            </div>
            <div class="h-3 w-px bg-gray-200"></div>
            <span class="<?= $c['is_active'] ? 'badge-active' : 'badge-inactive' ?> text-xs">
              <?= $c['is_active'] ? 'Aktif' : 'Nonaktif' ?>
            </span>
          </div>

          <!-- Landmark preview -->
          <?php if (!empty($c['landmark_notes'])): ?>
          <p class="text-xs text-gray-400 line-clamp-2 mb-3 italic">"<?= clean(substr($c['landmark_notes'], 0, 80)) ?>..."</p>
          <?php endif; ?>

          <!-- Aksi -->
          <div class="flex items-center gap-1.5 mt-auto flex-wrap">
            <a href="<?= $b ?>/admin/kota?action=edit&id=<?= $c['id'] ?>"
               class="flex-1 text-center text-xs bg-amber-50 text-amber-700 border border-amber-200 px-2 py-1 rounded-lg hover:bg-amber-100 font-medium">
              ✏️ Edit
            </a>
            <a href="<?= $b ?>/admin/area?action=tambah&city_id=<?= $c['id'] ?>"
               class="flex-1 text-center text-xs bg-blue-50 text-blue-700 border border-blue-200 px-2 py-1 rounded-lg hover:bg-blue-100 font-medium">
              + Area
            </a>
            <a href="<?= $b ?>/admin/kota?toggle=1&id=<?= $c['id'] ?>"
               class="text-xs bg-gray-50 text-gray-600 border border-gray-200 px-2 py-1 rounded-lg hover:bg-gray-100">
              <?= $c['is_active'] ? '🔕' : '✅' ?>
            </a>
            <a href="<?= $b ?>/toko-bunga-<?= $c['slug'] ?>" target="_blank"
               class="text-xs text-rose-400 hover:text-rose-600 px-1 py-1 border border-transparent hover:border-rose-100 hover:bg-rose-50 rounded-lg">
              ↗
            </a>
            <a href="<?= $b ?>/admin/kota?hapus=1&id=<?= $c['id'] ?>"
               onclick="return confirm('Hapus kota \'<?= clean($c['name']) ?>\' dan semua areanya?')"
               class="text-xs text-red-400 hover:text-red-600 px-1 py-1 border border-transparent hover:border-red-100 hover:bg-red-50 rounded-lg">
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
    const grid = document.getElementById('kotaGrid');
    Sortable.create(grid, {
        animation: 200,
        ghostClass: 'opacity-30',
        handle: '.kota-card',
        filter: 'a, button, select',
        preventOnFilter: false,
        onEnd() {
            const ids = [...grid.querySelectorAll('.kota-card')]
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
        const q      = document.getElementById('searchKota').value.toLowerCase().trim();
        const prov   = document.getElementById('filterProvinsi').value;
        const tier   = document.getElementById('filterTier').value;
        const status = document.getElementById('filterStatus').value;
        let visible  = 0;

        document.querySelectorAll('.kota-card').forEach(card => {
            const match =
                (!q      || card.dataset.name.includes(q)) &&
                (!prov   || card.dataset.provinsi === prov) &&
                (!tier   || card.dataset.tier === tier) &&
                (!status || card.dataset.active === status);
            card.style.display = match ? '' : 'none';
            if (match) visible++;
        });

        document.getElementById('emptySearch').classList.toggle('hidden', visible > 0);
    }

    ['searchKota','filterProvinsi','filterTier','filterStatus'].forEach(id => {
        const el = document.getElementById(id);
        el.addEventListener(el.tagName === 'INPUT' ? 'input' : 'change', applyFilter);
    });
    </script>
<?php } ?>
<?php require_once __DIR__ . '/../includes/admin_footer.php'; ?>