<?php
// ============================================================
// SEMUA LOGIKA CRUD DI SINI — SEBELUM ADMIN HEADER
// ============================================================
require_once __DIR__ . '/../../includes/config.php';
requireAdminLogin();

$pdo     = getDB();
$action  = $_GET['action'] ?? 'list';
$id      = (int)($_GET['id'] ?? 0);
$city_id = (int)($_GET['city_id'] ?? 0);
$b       = BASE_URL;

// Helper redirect area (bawa city_id kalau ada)
function redirectArea($base, $city_id) {
    header('Location: ' . $base . '/admin/area' . ($city_id ? "?city_id=$city_id" : ''));
    exit;
}

// AJAX: simpan urutan drag-and-drop
if (isset($_POST['reorder']) && !empty($_POST['ids'])) {
    $ids  = array_filter(array_map('intval', explode(',', $_POST['ids'])));
    $stmt = $pdo->prepare("UPDATE areas SET sort_order=? WHERE id=?");
    foreach (array_values($ids) as $i => $aid) {
        $stmt->execute([$i + 1, $aid]);
    }
    echo json_encode(['ok' => true]);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $cid  = (int)$_POST['city_id'];
    $data = [
        'city_id'      => $cid,
        'name'         => clean($_POST['name'] ?? ''),
        'slug'         => makeSlug($_POST['name'] ?? ''),
        'description'  => clean($_POST['description'] ?? ''),
        'landmarks'    => $_POST['landmarks'] ?? '',
        'nearby_areas' => $_POST['nearby_areas'] ?? '',
        'content'      => $_POST['content'] ?? '',
        'meta_title'   => clean($_POST['meta_title'] ?? ''),
        'meta_desc'    => clean($_POST['meta_desc'] ?? ''),
        'is_active'    => isset($_POST['is_active']) ? 1 : 0,
    ];
    if ($_POST['action_type'] === 'tambah') {
        $max = (int)$pdo->prepare("SELECT MAX(sort_order) FROM areas WHERE city_id=?")->execute([$cid]) ? 
               $pdo->query("SELECT MAX(sort_order) FROM areas WHERE city_id=$cid")->fetchColumn() : 0;
        $data['sort_order'] = $max + 1;
        $cols = implode(',', array_keys($data));
        $vals = ':' . implode(',:', array_keys($data));
        $pdo->prepare("INSERT INTO areas ($cols) VALUES ($vals)")->execute($data);
        $_SESSION['success'] = 'Area berhasil ditambahkan.';
    } elseif ($_POST['action_type'] === 'edit' && $id) {
        unset($data['slug']);
        $sets = implode(',', array_map(fn($k) => "$k=:$k", array_keys($data)));
        $data['id'] = $id;
        $pdo->prepare("UPDATE areas SET $sets WHERE id=:id")->execute($data);
        $_SESSION['success'] = 'Area berhasil diperbarui.';
    }
    redirectArea($b, $city_id ?: $cid);
}

if (isset($_GET['toggle']) && $id) {
    $cur = $pdo->prepare("SELECT is_active FROM areas WHERE id=?");
    $cur->execute([$id]);
    $pdo->prepare("UPDATE areas SET is_active=? WHERE id=?")->execute([$cur->fetchColumn() ? 0 : 1, $id]);
    redirectArea($b, $city_id);
}

if (isset($_GET['hapus']) && $id) {
    // ambil city_id dari area sebelum hapus
    $row = $pdo->prepare("SELECT city_id FROM areas WHERE id=?");
    $row->execute([$id]);
    $cid_hapus = (int)($row->fetchColumn() ?: $city_id);
    $pdo->prepare("DELETE FROM areas WHERE id=?")->execute([$id]);
    $_SESSION['success'] = 'Area berhasil dihapus.';
    redirectArea($b, $cid_hapus);
}

// ============================================================
$admin_title = 'Manajemen Area/Kecamatan';
require_once __DIR__ . '/../includes/admin_header.php';

$allCities = $pdo->query("SELECT id,name FROM cities WHERE is_active=1 ORDER BY tier ASC, name ASC")->fetchAll();

// ============================================================
// FORM TAMBAH / EDIT
// ============================================================
if ($action === 'tambah' || ($action === 'edit' && $id)) {
    $item = ['city_id' => $city_id];
    if ($action === 'edit') {
        $s = $pdo->prepare("SELECT * FROM areas WHERE id=?");
        $s->execute([$id]);
        $item = $s->fetch();
    }
    ?>
    <div class="max-w-2xl">
      <div class="flex items-center gap-3 mb-5">
        <a href="<?= $b ?>/admin/area<?= $city_id ? "?city_id=$city_id" : '' ?>" class="text-gray-400 hover:text-rose-600">← Kembali</a>
        <h2 class="font-display font-bold text-gray-800"><?= $action === 'tambah' ? 'Tambah' : 'Edit' ?> Area</h2>
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
            <label class="form-label">Nama Area/Kecamatan *</label>
            <input type="text" name="name" required class="form-input" value="<?= clean($item['name'] ?? '') ?>">
          </div>

          <div>
            <label class="form-label">Kota Induk *</label>
            <select name="city_id" required class="form-input">
              <option value="">-- Pilih Kota --</option>
              <?php foreach ($allCities as $c): ?>
              <option value="<?= $c['id'] ?>" <?= ($item['city_id'] ?? 0) == $c['id'] ? 'selected' : '' ?>>
                <?= clean($c['name']) ?>
              </option>
              <?php endforeach; ?>
            </select>
          </div>

          <div class="col-span-2">
            <label class="form-label">Deskripsi</label>
            <textarea name="description" class="form-input" rows="2"><?= clean($item['description'] ?? '') ?></textarea>
          </div>

          <div class="col-span-2">
            <label class="form-label">
              Landmark Area
              <span class="text-gray-400 font-normal text-xs">(SEO hyper-local – jalan, RS, mall, kampus)</span>
            </label>
            <textarea name="landmarks" class="form-input" rows="3"
                      placeholder="Jl. Tebet Raya, RS Tebet, Mal Tebet Green, Perumahan Tebet Indah..."><?= clean($item['landmarks'] ?? '') ?></textarea>
          </div>

          <div class="col-span-2">
            <label class="form-label">
              Area Terdekat
              <span class="text-gray-400 font-normal text-xs">(internal linking SEO)</span>
            </label>
            <textarea name="nearby_areas" class="form-input" rows="2"
                      placeholder="Menteng, Cikini, Manggarai, Pancoran..."><?= clean($item['nearby_areas'] ?? '') ?></textarea>
            <p class="text-xs text-gray-400 mt-1">Nama area/kecamatan terdekat untuk internal linking dan SEO kontekstual.</p>
          </div>

          <div class="col-span-2">
  <label class="form-label">
    Konten Halaman (SEO)
    <span class="text-gray-400 font-normal text-xs">(HTML diperbolehkan – h2, p, ul, strong, em)</span>
  </label>
  <textarea name="content" class="form-input font-mono text-xs" rows="10"
            placeholder="<h2>Toko Bunga di Denpasar Barat</h2>&#10;<p>Kami melayani pengiriman bunga...</p>"><?= htmlspecialchars($item['content'] ?? '') ?></textarea>
  <p class="text-xs text-gray-400 mt-1">Konten ini akan tampil di halaman area sebagai artikel SEO. Gunakan tag HTML seperti &lt;h2&gt;, &lt;p&gt;, &lt;ul&gt;, &lt;strong&gt;.</p>
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
            <input type="checkbox" name="is_active" id="is_active_a" <?= ($item['is_active'] ?? 1) ? 'checked' : '' ?>>
            <label for="is_active_a" class="text-sm text-gray-700 cursor-pointer">Aktif (tampil di website)</label>
          </div>
        </div>

        <div class="flex gap-2 pt-2">
          <button type="submit" class="btn-primary">Simpan Area</button>
          <a href="<?= $b ?>/admin/area<?= $city_id ? "?city_id=$city_id" : '' ?>" class="btn-secondary">Batal</a>
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

    // Ambil areas
    $where  = $city_id ? 'WHERE a.city_id=?' : '';
    $params = $city_id ? [$city_id] : [];
    $stmt   = $pdo->prepare("
        SELECT a.*, c.name as city_name, c.tier as city_tier
        FROM areas a
        JOIN cities c ON a.city_id = c.id
        $where
        ORDER BY c.tier ASC, c.name ASC, a.sort_order ASC, a.name ASC
    ");
    $stmt->execute($params);
    $areas = $stmt->fetchAll();

    $filterCityName = '';
    if ($city_id) {
        $fc = $pdo->prepare("SELECT name FROM cities WHERE id=?");
        $fc->execute([$city_id]);
        $filterCityName = $fc->fetchColumn();
    }

    $totalAktif = count(array_filter($areas, fn($a) => $a['is_active']));

    // Kota unik yang ada di hasil untuk filter
    $kotaOptions = [];
    foreach ($areas as $a) {
        $kotaOptions[$a['city_id']] = $a['city_name'];
    }
    ?>

    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-5">
      <div>
        <p class="text-sm text-gray-500">
          <span class="font-semibold text-gray-700"><?= count($areas) ?></span> area
          <?= $filterCityName ? "di <span class='font-semibold text-rose-600'>$filterCityName</span>" : '' ?> ·
          <span class="text-green-600 font-medium"><?= $totalAktif ?> aktif</span>
        </p>
        <p class="text-xs text-gray-400 mt-0.5">⠿ Seret kartu untuk mengubah urutan tampil</p>
      </div>
      <div class="flex items-center gap-2">
        <?php if ($city_id): ?>
          <a href="<?= $b ?>/admin/area" class="text-xs text-gray-400 hover:text-rose-600 border border-gray-200 px-3 py-1.5 rounded-lg">Semua Area</a>
        <?php endif; ?>
        <a href="<?= $b ?>/admin/area?action=tambah<?= $city_id ? "&city_id=$city_id" : '' ?>" class="btn-primary whitespace-nowrap">+ Tambah Area</a>
      </div>
    </div>

    <!-- Search + Filter -->
    <div class="flex flex-col sm:flex-row gap-2 mb-5">
      <div class="relative flex-1">
        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm">🔍</span>
        <input type="text" id="searchArea" placeholder="Cari nama area..."
               class="pl-8 pr-4 py-2 text-sm border border-gray-200 rounded-xl focus:outline-none focus:border-rose-400 w-full">
      </div>
      <?php if (!$city_id): ?>
      <select id="filterKota" class="text-sm border border-gray-200 rounded-xl px-3 py-2 focus:outline-none focus:border-rose-400 bg-white"
              onchange="window.location='<?= $b ?>/admin/area?city_id='+this.value">
        <option value="">Semua Kota</option>
        <?php foreach ($allCities as $c): ?>
        <option value="<?= $c['id'] ?>" <?= $city_id == $c['id'] ? 'selected' : '' ?>><?= clean($c['name']) ?></option>
        <?php endforeach; ?>
      </select>
      <?php endif; ?>
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
      <p class="text-sm">Tidak ada area yang cocok</p>
    </div>

    <!-- Card Grid -->
    <?php if (empty($areas)): ?>
    <div class="text-center py-16 text-gray-400">
      <div class="text-5xl mb-3">📍</div>
      <p class="text-sm font-medium text-gray-500">Belum ada area</p>
      <a href="<?= $b ?>/admin/area?action=tambah<?= $city_id ? "&city_id=$city_id" : '' ?>"
         class="mt-3 inline-block btn-primary text-sm">+ Tambah Area Pertama</a>
    </div>
    <?php else: ?>
    <div id="areaGrid" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
      <?php
      $tierColors = [
          1 => 'border-rose-200',
          2 => 'border-amber-200',
          3 => 'border-gray-200',
      ];
      $tierDot = [1 => 'bg-rose-400', 2 => 'bg-amber-400', 3 => 'bg-gray-400'];
      foreach ($areas as $a):
          $tc = $tierColors[$a['city_tier']] ?? 'border-gray-200';
          $td = $tierDot[$a['city_tier']]   ?? 'bg-gray-400';
      ?>
      <div class="area-card group bg-white border <?= $tc ?> rounded-2xl overflow-hidden cursor-grab active:cursor-grabbing hover:shadow-lg transition-all duration-200 flex flex-col"
           data-id="<?= $a['id'] ?>"
           data-name="<?= strtolower(clean($a['name'])) ?>"
           data-city="<?= $a['city_id'] ?>"
           data-active="<?= $a['is_active'] ?>">

        <!-- Header -->
        <div class="px-4 pt-4 pb-3 bg-gray-50 relative border-b border-gray-100">
          <!-- Drag handle -->
          <div class="absolute top-3 right-3 text-gray-300 group-hover:text-gray-500 select-none text-base transition-colors">⠿</div>

          <!-- Kota badge -->
          <div class="flex items-center gap-1.5 mb-2">
            <span class="w-1.5 h-1.5 rounded-full <?= $td ?>"></span>
            <span class="text-xs text-gray-500 font-medium"><?= clean($a['city_name']) ?></span>
          </div>

          <h3 class="font-bold text-gray-800 text-sm leading-tight pr-6"><?= clean($a['name']) ?></h3>
          <p class="text-xs text-gray-400 font-mono mt-0.5 truncate">/toko-bunga-<?= $a['slug'] ?></p>
        </div>

        <!-- Body -->
        <div class="px-4 py-3 flex-1 flex flex-col">
          <!-- Status -->
          <div class="flex items-center justify-between mb-2">
            <span class="<?= $a['is_active'] ? 'badge-active' : 'badge-inactive' ?> text-xs">
              <?= $a['is_active'] ? 'Aktif' : 'Nonaktif' ?>
            </span>
          </div>

          <!-- Landmark preview -->
          <?php if (!empty($a['landmarks'])): ?>
          <p class="text-xs text-gray-400 line-clamp-2 mb-2 italic">"<?= clean(substr($a['landmarks'], 0, 70)) ?>..."</p>
          <?php endif; ?>

          <!-- Nearby areas preview -->
          <?php if (!empty($a['nearby_areas'])): ?>
          <p class="text-xs text-blue-400 mb-2">🔗 <?= clean(substr($a['nearby_areas'], 0, 50)) ?>...</p>
          <?php endif; ?>

          <!-- Aksi -->
          <div class="flex items-center gap-1.5 mt-auto flex-wrap pt-2 border-t border-gray-50">
            <a href="<?= $b ?>/admin/area?action=edit&id=<?= $a['id'] ?>"
               class="flex-1 text-center text-xs bg-amber-50 text-amber-700 border border-amber-200 px-2 py-1 rounded-lg hover:bg-amber-100 font-medium">
              ✏️ Edit
            </a>
            <a href="<?= $b ?>/admin/area?toggle=1&id=<?= $a['id'] ?>&city_id=<?= $a['city_id'] ?>"
               class="text-xs bg-gray-50 text-gray-600 border border-gray-200 px-2 py-1 rounded-lg hover:bg-gray-100">
              <?= $a['is_active'] ? '🔕' : '✅' ?>
            </a>
            <a href="<?= $b ?>/toko-bunga-<?= $a['slug'] ?>" target="_blank"
               class="text-xs text-rose-400 hover:text-rose-600 px-1 py-1 border border-transparent hover:border-rose-100 hover:bg-rose-50 rounded-lg">
              ↗
            </a>
            <a href="<?= $b ?>/admin/area?hapus=1&id=<?= $a['id'] ?>&city_id=<?= $a['city_id'] ?>"
               onclick="return confirm('Hapus area \'<?= clean($a['name']) ?>\'?')"
               class="text-xs text-red-400 hover:text-red-600 px-1 py-1 border border-transparent hover:border-red-100 hover:bg-red-50 rounded-lg">
              🗑️
            </a>
          </div>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
    <?php endif; ?>

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
    const grid = document.getElementById('areaGrid');
    if (grid) {
        Sortable.create(grid, {
            animation: 200,
            ghostClass: 'opacity-30',
            handle: '.area-card',
            filter: 'a, button, select',
            preventOnFilter: false,
            onEnd() {
                const ids = [...grid.querySelectorAll('.area-card')]
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

    // ── Filter & Search ───────────────────────────────────────
    function applyFilter() {
        const q      = document.getElementById('searchArea').value.toLowerCase().trim();
        const status = document.getElementById('filterStatus').value;
        let visible  = 0;

        document.querySelectorAll('.area-card').forEach(card => {
            const match =
                (!q      || card.dataset.name.includes(q)) &&
                (!status || card.dataset.active === status);
            card.style.display = match ? '' : 'none';
            if (match) visible++;
        });

        document.getElementById('emptySearch').classList.toggle('hidden', visible > 0 || !grid);
    }

    const sa = document.getElementById('searchArea');
    const fs = document.getElementById('filterStatus');
    if (sa) sa.addEventListener('input', applyFilter);
    if (fs) fs.addEventListener('change', applyFilter);
    </script>
<?php } ?>
<?php require_once __DIR__ . '/../includes/admin_footer.php'; ?>