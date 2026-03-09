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
    $stmt = $pdo->prepare("UPDATE testimonials SET sort_order=? WHERE id=?");
    foreach (array_values($ids) as $i => $tid) {
        $stmt->execute([$i + 1, $tid]);
    }
    echo json_encode(['ok' => true]);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = [
        'customer_name' => clean($_POST['customer_name'] ?? ''),
        'customer_city' => clean($_POST['customer_city'] ?? ''),
        'rating'        => min(5, max(1, (int)($_POST['rating'] ?? 5))),
        'content'       => clean($_POST['content'] ?? ''),
        'is_active'     => isset($_POST['is_active']) ? 1 : 0,
    ];
    if ($_POST['action_type'] === 'tambah') {
        $max = (int)$pdo->query("SELECT MAX(sort_order) FROM testimonials")->fetchColumn();
        $data['sort_order'] = $max + 1;
        $cols = implode(',', array_keys($data));
        $vals = ':' . implode(',:', array_keys($data));
        $pdo->prepare("INSERT INTO testimonials ($cols) VALUES ($vals)")->execute($data);
        $_SESSION['success'] = 'Testimoni berhasil ditambahkan.';
    } elseif ($_POST['action_type'] === 'edit' && $id) {
        $sets = implode(',', array_map(fn($k) => "$k=:$k", array_keys($data)));
        $data['id'] = $id;
        $pdo->prepare("UPDATE testimonials SET $sets WHERE id=:id")->execute($data);
        $_SESSION['success'] = 'Testimoni berhasil diperbarui.';
    }
    header('Location: ' . $b . '/admin/testimoni');
    exit;
}

if (isset($_GET['toggle']) && $id) {
    $cur = $pdo->prepare("SELECT is_active FROM testimonials WHERE id=?");
    $cur->execute([$id]);
    $pdo->prepare("UPDATE testimonials SET is_active=? WHERE id=?")->execute([$cur->fetchColumn() ? 0 : 1, $id]);
    header('Location: ' . $b . '/admin/testimoni');
    exit;
}

if (isset($_GET['hapus']) && $id) {
    $pdo->prepare("DELETE FROM testimonials WHERE id=?")->execute([$id]);
    $_SESSION['success'] = 'Testimoni berhasil dihapus.';
    header('Location: ' . $b . '/admin/testimoni');
    exit;
}

// ============================================================
$admin_title = 'Manajemen Testimoni';
require_once __DIR__ . '/../includes/admin_header.php';

// ============================================================
// FORM TAMBAH / EDIT
// ============================================================
if ($action === 'tambah' || ($action === 'edit' && $id)) {
    $item = [];
    if ($action === 'edit') {
        $s = $pdo->prepare("SELECT * FROM testimonials WHERE id=?");
        $s->execute([$id]);
        $item = $s->fetch();
    }
    $curRating = (int)($item['rating'] ?? 5);
    ?>
    <div class="max-w-xl">
      <div class="flex items-center gap-3 mb-5">
        <a href="<?= $b ?>/admin/testimoni" class="text-gray-400 hover:text-rose-600">← Kembali</a>
        <h2 class="font-display font-bold text-gray-800"><?= $action === 'tambah' ? 'Tambah' : 'Edit' ?> Testimoni</h2>
      </div>

      <?php if (!empty($_SESSION['error'])): ?>
        <div class="bg-red-50 border border-red-200 text-red-600 text-sm rounded-lg px-4 py-3 mb-4">
          ⚠️ <?= clean($_SESSION['error']) ?>
        </div>
        <?php unset($_SESSION['error']); ?>
      <?php endif; ?>

      <form method="POST" class="bg-white rounded-xl border border-rose-100 p-6 space-y-5">
        <input type="hidden" name="action_type" value="<?= $action ?>">

        <div class="grid grid-cols-2 gap-4">
          <div>
            <label class="form-label">Nama Pelanggan *</label>
            <input type="text" name="customer_name" required class="form-input"
                   value="<?= clean($item['customer_name'] ?? '') ?>"
                   placeholder="Budi Santoso">
          </div>
          <div>
            <label class="form-label">Kota Asal</label>
            <input type="text" name="customer_city" class="form-input"
                   value="<?= clean($item['customer_city'] ?? '') ?>"
                   placeholder="Jakarta Selatan">
          </div>
        </div>

        <!-- Rating bintang interaktif -->
        <div>
          <label class="form-label">Rating</label>
          <div class="flex items-center gap-1" id="starGroup">
            <?php for ($r = 1; $r <= 5; $r++): ?>
            <button type="button" data-val="<?= $r ?>"
                    class="star-btn text-3xl transition-transform hover:scale-110 focus:outline-none
                           <?= $r <= $curRating ? 'text-amber-400' : 'text-gray-200' ?>"
                    onclick="setRating(<?= $r ?>)">★</button>
            <?php endfor; ?>
            <span id="ratingLabel" class="ml-2 text-sm font-semibold text-amber-600"><?= $curRating ?> Bintang</span>
          </div>
          <input type="hidden" name="rating" id="ratingInput" value="<?= $curRating ?>">
        </div>

        <div>
          <div class="flex items-center justify-between mb-1">
            <label class="form-label mb-0">Isi Testimoni *</label>
            <span id="contentCount" class="text-xs text-gray-400"><?= mb_strlen($item['content'] ?? '') ?>/300</span>
          </div>
          <textarea name="content" id="contentArea" required class="form-input" rows="4"
                    placeholder="Pelayanan sangat memuaskan, bunga dikirim tepat waktu dan sangat cantik..."><?= clean($item['content'] ?? '') ?></textarea>
        </div>

        <!-- Preview kartu -->
        <div class="bg-rose-50 border border-rose-100 rounded-xl p-4">
          <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-3">👁️ Preview kartu di website</p>
          <div class="bg-white rounded-xl p-4 shadow-sm border border-rose-100">
            <div class="flex items-center gap-1 mb-2" id="previewStars">
              <?php for ($r = 1; $r <= 5; $r++): ?>
              <span class="preview-star text-base <?= $r <= $curRating ? 'text-amber-400' : 'text-gray-200' ?>">★</span>
              <?php endfor; ?>
            </div>
            <p id="previewContent" class="text-sm text-gray-600 italic leading-relaxed mb-3">
              "<?= clean($item['content'] ?? 'Isi testimoni akan tampil di sini...') ?>"
            </p>
            <div class="flex items-center gap-2">
              <div class="w-8 h-8 rounded-full bg-rose-100 flex items-center justify-center text-rose-500 font-bold text-sm" id="previewInitial">
                <?= strtoupper(substr($item['customer_name'] ?? 'A', 0, 1)) ?>
              </div>
              <div>
                <p id="previewName" class="text-sm font-semibold text-gray-800"><?= clean($item['customer_name'] ?? 'Nama Pelanggan') ?></p>
                <p id="previewCity" class="text-xs text-gray-400"><?= clean($item['customer_city'] ?? 'Kota') ?></p>
              </div>
            </div>
          </div>
        </div>

        <div class="flex items-center gap-2">
          <input type="checkbox" name="is_active" id="is_active_t" <?= ($item['is_active'] ?? 1) ? 'checked' : '' ?>>
          <label for="is_active_t" class="text-sm text-gray-700 cursor-pointer">Aktif (tampil di website)</label>
        </div>

        <div class="flex gap-2 pt-1">
          <button type="submit" class="btn-primary">Simpan Testimoni</button>
          <a href="<?= $b ?>/admin/testimoni" class="btn-secondary">Batal</a>
        </div>
      </form>
    </div>

    <script>
    // ── Rating bintang ────────────────────────────────────────
    function setRating(val) {
        document.getElementById('ratingInput').value = val;
        document.getElementById('ratingLabel').textContent = val + ' Bintang';
        document.querySelectorAll('.star-btn').forEach((btn, i) => {
            btn.classList.toggle('text-amber-400', i < val);
            btn.classList.toggle('text-gray-200',  i >= val);
        });
        document.querySelectorAll('.preview-star').forEach((s, i) => {
            s.classList.toggle('text-amber-400', i < val);
            s.classList.toggle('text-gray-200',  i >= val);
        });
    }

    // ── Counter konten ────────────────────────────────────────
    const area = document.getElementById('contentArea');
    const cnt  = document.getElementById('contentCount');
    area.addEventListener('input', function() {
        const len = this.value.length;
        cnt.textContent = len + '/300';
        cnt.className = 'text-xs ' + (len > 300 ? 'text-red-500 font-semibold' : 'text-gray-400');
        document.getElementById('previewContent').textContent = '"' + (this.value || 'Isi testimoni akan tampil di sini...') + '"';
    });

    // ── Preview nama & kota ───────────────────────────────────
    document.querySelector('[name="customer_name"]').addEventListener('input', function() {
        document.getElementById('previewName').textContent    = this.value || 'Nama Pelanggan';
        document.getElementById('previewInitial').textContent = (this.value[0] || 'A').toUpperCase();
    });
    document.querySelector('[name="customer_city"]').addEventListener('input', function() {
        document.getElementById('previewCity').textContent = this.value || 'Kota';
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

    $list = $pdo->query("SELECT * FROM testimonials ORDER BY sort_order ASC, id ASC")->fetchAll();

    $totalAktif = count(array_filter($list, fn($t) => $t['is_active']));
    $avgRating  = count($list) ? round(array_sum(array_column($list, 'rating')) / count($list), 1) : 0;

    // Hitung distribusi rating
    $ratingDist = array_fill(1, 5, 0);
    foreach ($list as $t) $ratingDist[$t['rating']] = ($ratingDist[$t['rating']] ?? 0) + 1;
    ?>

    <!-- Header + stats -->
    <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-4 mb-5">
      <div>
        <p class="text-sm text-gray-500">
          <span class="font-semibold text-gray-700"><?= count($list) ?></span> testimoni ·
          <span class="text-green-600 font-medium"><?= $totalAktif ?> aktif</span> ·
          <span class="text-amber-500 font-medium">★ <?= $avgRating ?> rata-rata</span>
        </p>
        <p class="text-xs text-gray-400 mt-0.5">⠿ Seret kartu untuk mengubah urutan tampil</p>
        <!-- Mini distribusi rating -->
        <?php if (count($list) > 0): ?>
        <div class="flex items-center gap-2 mt-2">
          <?php for ($r = 5; $r >= 1; $r--): ?>
          <div class="flex items-center gap-1">
            <span class="text-amber-400 text-xs">★<?= $r ?></span>
            <div class="w-12 h-1.5 bg-gray-100 rounded-full overflow-hidden">
              <div class="h-full bg-amber-400 rounded-full"
                   style="width:<?= count($list) ? round(($ratingDist[$r] / count($list)) * 100) : 0 ?>%"></div>
            </div>
            <span class="text-xs text-gray-400"><?= $ratingDist[$r] ?></span>
          </div>
          <?php endfor; ?>
        </div>
        <?php endif; ?>
      </div>
      <a href="<?= $b ?>/admin/testimoni?action=tambah" class="btn-primary whitespace-nowrap">+ Tambah Testimoni</a>
    </div>

    <!-- Search + Filter -->
    <div class="flex flex-col sm:flex-row gap-2 mb-5">
      <div class="relative flex-1">
        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm">🔍</span>
        <input type="text" id="searchTesti" placeholder="Cari nama atau isi testimoni..."
               class="pl-8 pr-4 py-2 text-sm border border-gray-200 rounded-xl focus:outline-none focus:border-rose-400 w-full">
      </div>
      <select id="filterRating" class="text-sm border border-gray-200 rounded-xl px-3 py-2 focus:outline-none focus:border-rose-400 bg-white">
        <option value="">Semua Rating</option>
        <option value="5">★★★★★ 5 Bintang</option>
        <option value="4">★★★★☆ 4 Bintang</option>
        <option value="3">★★★☆☆ 3 Bintang</option>
        <option value="2">★★☆☆☆ 2 Bintang</option>
        <option value="1">★☆☆☆☆ 1 Bintang</option>
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
      <p class="text-sm">Tidak ada testimoni yang cocok</p>
    </div>

    <?php if (empty($list)): ?>
    <div class="text-center py-16 text-gray-400">
      <div class="text-5xl mb-3">💬</div>
      <p class="text-sm font-medium text-gray-500">Belum ada testimoni</p>
      <a href="<?= $b ?>/admin/testimoni?action=tambah" class="mt-3 inline-block btn-primary text-sm">+ Tambah Testimoni Pertama</a>
    </div>
    <?php else: ?>

    <!-- Card Grid -->
    <div id="testiGrid" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
      <?php foreach ($list as $t):
            $initial = strtoupper(substr($t['customer_name'], 0, 1));
            $stars   = str_repeat('★', $t['rating']) . str_repeat('☆', 5 - $t['rating']);
            $avatarColors = ['bg-rose-100 text-rose-600','bg-amber-100 text-amber-600','bg-blue-100 text-blue-600',
                             'bg-green-100 text-green-600','bg-purple-100 text-purple-600','bg-pink-100 text-pink-600'];
            $avatarColor  = $avatarColors[crc32($t['customer_name']) % count($avatarColors)];
      ?>
      <div class="testi-card group bg-white border border-rose-100 rounded-2xl p-4 flex flex-col cursor-grab active:cursor-grabbing hover:shadow-lg transition-all duration-200 <?= !$t['is_active'] ? 'opacity-60' : '' ?>"
           data-id="<?= $t['id'] ?>"
           data-name="<?= strtolower(clean($t['customer_name'])) ?>"
           data-content="<?= strtolower(clean($t['content'])) ?>"
           data-rating="<?= $t['rating'] ?>"
           data-active="<?= $t['is_active'] ?>">

        <!-- Top: rating + drag handle + status -->
        <div class="flex items-center justify-between mb-3">
          <div class="flex items-center gap-0.5">
            <?php for ($r = 1; $r <= 5; $r++): ?>
            <span class="text-lg <?= $r <= $t['rating'] ? 'text-amber-400' : 'text-gray-200' ?>">★</span>
            <?php endfor; ?>
          </div>
          <div class="flex items-center gap-2">
            <?php if (!$t['is_active']): ?>
            <span class="badge-inactive text-xs">Nonaktif</span>
            <?php endif; ?>
            <div class="text-gray-200 group-hover:text-gray-400 select-none text-base transition-colors">⠿</div>
          </div>
        </div>

        <!-- Konten -->
        <p class="text-sm text-gray-600 italic leading-relaxed flex-1 mb-4">
          "<?= clean(mb_strlen($t['content']) > 120 ? mb_substr($t['content'], 0, 120) . '...' : $t['content']) ?>"
        </p>

        <!-- Author -->
        <div class="flex items-center gap-3 mb-3 pt-3 border-t border-gray-50">
          <div class="w-9 h-9 rounded-full <?= $avatarColor ?> flex items-center justify-center font-bold text-sm flex-shrink-0">
            <?= $initial ?>
          </div>
          <div class="min-w-0">
            <p class="font-semibold text-gray-800 text-sm truncate"><?= clean($t['customer_name']) ?></p>
            <p class="text-xs text-gray-400"><?= clean($t['customer_city'] ?: '-') ?></p>
          </div>
        </div>

        <!-- Aksi -->
        <div class="flex items-center gap-1.5">
          <a href="<?= $b ?>/admin/testimoni?action=edit&id=<?= $t['id'] ?>"
             class="flex-1 text-center text-xs bg-amber-50 text-amber-700 border border-amber-200 px-2 py-1.5 rounded-lg hover:bg-amber-100 font-medium">
            ✏️ Edit
          </a>
          <a href="<?= $b ?>/admin/testimoni?toggle=1&id=<?= $t['id'] ?>"
             class="text-xs bg-gray-50 text-gray-600 border border-gray-200 px-2 py-1.5 rounded-lg hover:bg-gray-100"
             title="<?= $t['is_active'] ? 'Nonaktifkan' : 'Aktifkan' ?>">
            <?= $t['is_active'] ? '🔕' : '✅' ?>
          </a>
          <a href="<?= $b ?>/admin/testimoni?hapus=1&id=<?= $t['id'] ?>"
             onclick="return confirm('Hapus testimoni dari <?= clean($t['customer_name']) ?>?')"
             class="text-xs text-red-400 hover:text-red-600 px-2 py-1.5 rounded-lg hover:bg-red-50 border border-transparent hover:border-red-100">
            🗑️
          </a>
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
    const grid = document.getElementById('testiGrid');
    Sortable.create(grid, {
        animation: 200,
        ghostClass: 'opacity-30',
        handle: '.testi-card',
        filter: 'a, button',
        preventOnFilter: false,
        onEnd() {
            const ids = [...grid.querySelectorAll('.testi-card')]
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
        const q      = document.getElementById('searchTesti').value.toLowerCase().trim();
        const rating = document.getElementById('filterRating').value;
        const status = document.getElementById('filterStatus').value;
        let visible  = 0;

        document.querySelectorAll('.testi-card').forEach(card => {
            const match =
                (!q      || card.dataset.name.includes(q) || card.dataset.content.includes(q)) &&
                (!rating || card.dataset.rating === rating) &&
                (!status || card.dataset.active === status);
            card.style.display = match ? '' : 'none';
            if (match) visible++;
        });

        document.getElementById('emptySearch').classList.toggle('hidden', visible > 0);
    }

    document.getElementById('searchTesti').addEventListener('input', applyFilter);
    document.getElementById('filterRating').addEventListener('change', applyFilter);
    document.getElementById('filterStatus').addEventListener('change', applyFilter);
    </script>
    <?php endif; ?>
<?php } ?>
<?php require_once __DIR__ . '/../includes/admin_footer.php'; ?>