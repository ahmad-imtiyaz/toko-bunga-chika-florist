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
$meId   = (int)($_SESSION['admin_id'] ?? 0);

// AJAX check username unik
if (isset($_GET['check_username'])) {
    $u   = clean($_GET['check_username']);
    $eid = (int)($_GET['exclude_id'] ?? 0);
    $s   = $pdo->prepare("SELECT COUNT(*) FROM admins WHERE username=? AND id!=?");
    $s->execute([$u, $eid]);
    echo json_encode(['taken' => (bool)$s->fetchColumn()]);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $actionType = $_POST['action_type'] ?? '';

    // ── Tambah akun ──────────────────────────────────────────
    if ($actionType === 'tambah') {
        $username  = clean($_POST['username'] ?? '');
        $fullName  = clean($_POST['full_name'] ?? '');
        $password  = $_POST['password'] ?? '';
        $password2 = $_POST['password2'] ?? '';
        $isActive  = isset($_POST['is_active']) ? 1 : 0;

        // Validasi
        if (strlen($username) < 3) {
            $_SESSION['error'] = 'Username minimal 3 karakter.';
            header('Location: ' . $b . '/admin/akun?action=tambah'); exit;
        }
        if (strlen($password) < 6) {
            $_SESSION['error'] = 'Password minimal 6 karakter.';
            header('Location: ' . $b . '/admin/akun?action=tambah'); exit;
        }
        if ($password !== $password2) {
            $_SESSION['error'] = 'Konfirmasi password tidak cocok.';
            header('Location: ' . $b . '/admin/akun?action=tambah'); exit;
        }
        // Cek username unik
        $check = $pdo->prepare("SELECT COUNT(*) FROM admins WHERE username=?");
        $check->execute([$username]);
        if ($check->fetchColumn()) {
            $_SESSION['error'] = "Username \"$username\" sudah dipakai.";
            header('Location: ' . $b . '/admin/akun?action=tambah'); exit;
        }

        $pdo->prepare("INSERT INTO admins (username, password, full_name, is_active) VALUES (?,?,?,?)")
            ->execute([$username, password_hash($password, PASSWORD_DEFAULT), $fullName, $isActive]);
        $_SESSION['success'] = "Akun \"$username\" berhasil dibuat.";
        header('Location: ' . $b . '/admin/akun'); exit;
    }

    // ── Edit akun ────────────────────────────────────────────
    if ($actionType === 'edit' && $id) {
        $fullName = clean($_POST['full_name'] ?? '');
        $isActive = isset($_POST['is_active']) ? 1 : 0;
        $password  = $_POST['password'] ?? '';
        $password2 = $_POST['password2'] ?? '';

        // Jangan bisa nonaktifkan akun sendiri
        if ($id === $meId && !$isActive) {
            $_SESSION['error'] = 'Kamu tidak bisa menonaktifkan akunmu sendiri.';
            header('Location: ' . $b . '/admin/akun?action=edit&id=' . $id); exit;
        }

        if ($password !== '') {
            if (strlen($password) < 6) {
                $_SESSION['error'] = 'Password minimal 6 karakter.';
                header('Location: ' . $b . '/admin/akun?action=edit&id=' . $id); exit;
            }
            if ($password !== $password2) {
                $_SESSION['error'] = 'Konfirmasi password tidak cocok.';
                header('Location: ' . $b . '/admin/akun?action=edit&id=' . $id); exit;
            }
            $pdo->prepare("UPDATE admins SET full_name=?, is_active=?, password=?, updated_at=NOW() WHERE id=?")
                ->execute([$fullName, $isActive, password_hash($password, PASSWORD_DEFAULT), $id]);
        } else {
            $pdo->prepare("UPDATE admins SET full_name=?, is_active=?, updated_at=NOW() WHERE id=?")
                ->execute([$fullName, $isActive, $id]);
        }
        $_SESSION['success'] = 'Akun berhasil diperbarui.';
        header('Location: ' . $b . '/admin/akun'); exit;
    }
}

// ── Toggle aktif ──────────────────────────────────────────────
if (isset($_GET['toggle']) && $id) {
    if ($id === $meId) {
        $_SESSION['error'] = 'Kamu tidak bisa menonaktifkan akunmu sendiri.';
    } else {
        $cur = $pdo->prepare("SELECT is_active FROM admins WHERE id=?");
        $cur->execute([$id]);
        $pdo->prepare("UPDATE admins SET is_active=? WHERE id=?")->execute([$cur->fetchColumn() ? 0 : 1, $id]);
        $_SESSION['success'] = 'Status akun diperbarui.';
    }
    header('Location: ' . $b . '/admin/akun'); exit;
}

// ── Hapus akun ────────────────────────────────────────────────
if (isset($_GET['hapus']) && $id) {
    if ($id === $meId) {
        $_SESSION['error'] = 'Kamu tidak bisa menghapus akunmu sendiri.';
    } else {
        $total = (int)$pdo->query("SELECT COUNT(*) FROM admins WHERE is_active=1")->fetchColumn();
        if ($total <= 1) {
            $_SESSION['error'] = 'Tidak bisa menghapus — harus ada minimal 1 akun aktif.';
        } else {
            $pdo->prepare("DELETE FROM admins WHERE id=?")->execute([$id]);
            $_SESSION['success'] = 'Akun berhasil dihapus.';
        }
    }
    header('Location: ' . $b . '/admin/akun'); exit;
}

// ============================================================
$admin_title = 'Manajemen Akun';
require_once __DIR__ . '/../includes/admin_header.php';
?>

<?php
// ============================================================
// FORM TAMBAH / EDIT
// ============================================================
if ($action === 'tambah' || ($action === 'edit' && $id)):
    $item = ['username'=>'','full_name'=>'','is_active'=>1];
    if ($action === 'edit') {
        $s = $pdo->prepare("SELECT * FROM admins WHERE id=?");
        $s->execute([$id]);
        $item = $s->fetch();
        if (!$item) { echo '<p class="text-red-500">Akun tidak ditemukan.</p>'; require_once __DIR__ . '/../includes/admin_footer.php'; exit; }
    }
?>
<div class="max-w-lg">
  <div class="flex items-center gap-3 mb-5">
    <a href="<?= $b ?>/admin/akun" class="text-gray-400 hover:text-rose-600">← Kembali</a>
    <h2 class="font-display font-bold text-gray-800">
      <?= $action === 'tambah' ? 'Tambah Akun Admin' : 'Edit Akun: ' . clean($item['username']) ?>
    </h2>
    <?php if ($action === 'edit' && $id === $meId): ?>
    <span class="text-xs bg-blue-100 text-blue-700 px-2 py-0.5 rounded-full">Akun Kamu</span>
    <?php endif; ?>
  </div>

  <?php if (!empty($_SESSION['error'])): ?>
  <div class="bg-red-50 border border-red-200 text-red-600 text-sm rounded-lg px-4 py-3 mb-4">
    ⚠️ <?= clean($_SESSION['error']) ?>
  </div>
  <?php unset($_SESSION['error']); ?>
  <?php endif; ?>

  <form method="POST" class="space-y-4" id="akunForm">
    <input type="hidden" name="action_type" value="<?= $action ?>">

    <!-- Info akun -->
    <div class="bg-white rounded-xl border border-rose-100 p-6 space-y-4">
      <h3 class="font-semibold text-gray-700 text-sm border-b border-gray-100 pb-2">👤 Informasi Akun</h3>

      <?php if ($action === 'tambah'): ?>
      <div>
        <label class="form-label">Username *</label>
        <div class="relative">
          <input type="text" name="username" id="usernameInput" class="form-input pr-10"
                 value="<?= clean($item['username']) ?>"
                 placeholder="contoh: admin2"
                 required minlength="3" autocomplete="off">
          <span id="usernameStatus" class="absolute right-3 top-1/2 -translate-y-1/2 text-sm"></span>
        </div>
        <p id="usernameMsg" class="text-xs mt-1 text-gray-400">Min. 3 karakter, tanpa spasi</p>
      </div>
      <?php else: ?>
      <div>
        <label class="form-label">Username</label>
        <div class="form-input bg-gray-50 text-gray-400 cursor-not-allowed"><?= clean($item['username']) ?></div>
        <p class="text-xs text-gray-400 mt-1">Username tidak bisa diubah</p>
      </div>
      <?php endif; ?>

      <div>
        <label class="form-label">Nama Lengkap</label>
        <input type="text" name="full_name" class="form-input"
               value="<?= clean($item['full_name'] ?? '') ?>"
               placeholder="Nama yang ditampilkan di admin panel">
      </div>

      <div class="flex items-center gap-2">
        <input type="checkbox" name="is_active" id="is_active_a"
               <?= $item['is_active'] ? 'checked' : '' ?>
               <?= ($action === 'edit' && $id === $meId) ? 'disabled' : '' ?>>
        <label for="is_active_a" class="text-sm text-gray-700 cursor-pointer">Akun aktif</label>
        <?php if ($action === 'edit' && $id === $meId): ?>
        <span class="text-xs text-gray-400">(tidak bisa menonaktifkan akun sendiri)</span>
        <?php endif; ?>
      </div>
    </div>

    <!-- Password -->
    <div class="bg-white rounded-xl border border-rose-100 p-6 space-y-4">
      <h3 class="font-semibold text-gray-700 text-sm border-b border-gray-100 pb-2">
        🔑 <?= $action === 'tambah' ? 'Password' : 'Ganti Password' ?>
        <?php if ($action === 'edit'): ?>
        <span class="text-gray-400 font-normal text-xs">(kosongkan jika tidak ingin ganti)</span>
        <?php endif; ?>
      </h3>

      <div>
        <label class="form-label">Password <?= $action === 'tambah' ? '*' : 'Baru' ?></label>
        <div class="relative">
          <input type="password" name="password" id="passInput" class="form-input pr-10"
                 placeholder="Min. 6 karakter"
                 <?= $action === 'tambah' ? 'required minlength="6"' : '' ?>
                 autocomplete="new-password">
          <button type="button" onclick="togglePass('passInput','eyePass')"
                  class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 text-sm">
            <span id="eyePass">👁️</span>
          </button>
        </div>
        <!-- Strength indicator -->
        <div class="mt-1.5 flex gap-1" id="strengthBars">
          <div class="h-1 flex-1 rounded-full bg-gray-100" id="bar1"></div>
          <div class="h-1 flex-1 rounded-full bg-gray-100" id="bar2"></div>
          <div class="h-1 flex-1 rounded-full bg-gray-100" id="bar3"></div>
          <div class="h-1 flex-1 rounded-full bg-gray-100" id="bar4"></div>
        </div>
        <p id="strengthLabel" class="text-xs text-gray-400 mt-0.5"></p>
      </div>

      <div>
        <label class="form-label">Konfirmasi Password <?= $action === 'tambah' ? '*' : '' ?></label>
        <div class="relative">
          <input type="password" name="password2" id="pass2Input" class="form-input pr-10"
                 placeholder="Ulangi password"
                 <?= $action === 'tambah' ? 'required' : '' ?>
                 autocomplete="new-password">
          <button type="button" onclick="togglePass('pass2Input','eyePass2')"
                  class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 text-sm">
            <span id="eyePass2">👁️</span>
          </button>
        </div>
        <p id="matchMsg" class="text-xs mt-1 hidden"></p>
      </div>
    </div>

    <div class="flex gap-2 pb-6">
      <button type="submit" id="submitBtn" class="btn-primary">
        <?= $action === 'tambah' ? '➕ Buat Akun' : '💾 Simpan Perubahan' ?>
      </button>
      <a href="<?= $b ?>/admin/akun" class="btn-secondary">Batal</a>
    </div>
  </form>
</div>

<script>
// ── Toggle show/hide password ─────────────────────────────────
function togglePass(inputId, eyeId) {
    const inp = document.getElementById(inputId);
    const eye = document.getElementById(eyeId);
    if (inp.type === 'password') { inp.type = 'text'; eye.textContent = '🙈'; }
    else { inp.type = 'password'; eye.textContent = '👁️'; }
}

// ── Username availability check ───────────────────────────────
<?php if ($action === 'tambah'): ?>
const usernameInput  = document.getElementById('usernameInput');
const usernameStatus = document.getElementById('usernameStatus');
const usernameMsg    = document.getElementById('usernameMsg');
let usernameTimer;

usernameInput.addEventListener('input', function() {
    const val = this.value.trim();
    clearTimeout(usernameTimer);
    if (val.length < 3) {
        usernameStatus.textContent = '';
        usernameMsg.textContent = 'Min. 3 karakter, tanpa spasi';
        usernameMsg.className = 'text-xs mt-1 text-gray-400';
        return;
    }
    usernameStatus.textContent = '⏳';
    usernameTimer = setTimeout(() => {
        fetch('?check_username=' + encodeURIComponent(val) + '&exclude_id=0')
        .then(r => r.json()).then(d => {
            if (d.taken) {
                usernameStatus.textContent = '❌';
                usernameMsg.textContent = 'Username sudah dipakai';
                usernameMsg.className = 'text-xs mt-1 text-red-500';
            } else {
                usernameStatus.textContent = '✅';
                usernameMsg.textContent = 'Username tersedia';
                usernameMsg.className = 'text-xs mt-1 text-green-600';
            }
        });
    }, 500);
});
<?php endif; ?>

// ── Password strength ─────────────────────────────────────────
const passInput    = document.getElementById('passInput');
const pass2Input   = document.getElementById('pass2Input');
const matchMsg     = document.getElementById('matchMsg');
const strengthLbl  = document.getElementById('strengthLabel');
const bars         = [document.getElementById('bar1'),document.getElementById('bar2'),document.getElementById('bar3'),document.getElementById('bar4')];
const colors       = ['bg-red-400','bg-amber-400','bg-amber-400','bg-green-500'];
const labels       = ['Lemah','Sedang','Cukup kuat','Kuat'];

function calcStrength(p) {
    let score = 0;
    if (p.length >= 6)  score++;
    if (p.length >= 10) score++;
    if (/[A-Z]/.test(p) && /[a-z]/.test(p)) score++;
    if (/[0-9]/.test(p) && /[^A-Za-z0-9]/.test(p)) score++;
    return score;
}

passInput.addEventListener('input', function() {
    const val = this.value;
    if (!val) {
        bars.forEach(b => b.className = 'h-1 flex-1 rounded-full bg-gray-100');
        strengthLbl.textContent = '';
        return;
    }
    const score = calcStrength(val);
    bars.forEach((b, i) => {
        b.className = 'h-1 flex-1 rounded-full ' + (i < score ? colors[score-1] : 'bg-gray-100');
    });
    strengthLbl.textContent = labels[score-1] || '';
    strengthLbl.className = 'text-xs mt-0.5 ' + (score <= 1 ? 'text-red-400' : score <= 2 ? 'text-amber-500' : 'text-green-600');
    checkMatch();
});

pass2Input.addEventListener('input', checkMatch);

function checkMatch() {
    const p1 = passInput.value;
    const p2 = pass2Input.value;
    if (!p2) { matchMsg.classList.add('hidden'); return; }
    matchMsg.classList.remove('hidden');
    if (p1 === p2) {
        matchMsg.textContent = '✅ Password cocok';
        matchMsg.className = 'text-xs mt-1 text-green-600';
    } else {
        matchMsg.textContent = '❌ Password tidak cocok';
        matchMsg.className = 'text-xs mt-1 text-red-500';
    }
}

// ── Form submit validation ────────────────────────────────────
document.getElementById('akunForm').addEventListener('submit', function(e) {
    const p1 = passInput.value;
    const p2 = pass2Input.value;
    <?php if ($action === 'edit'): ?>
    if (p1 === '' && p2 === '') return; // boleh kosong saat edit
    <?php endif; ?>
    if (p1 !== p2) {
        e.preventDefault();
        matchMsg.classList.remove('hidden');
        matchMsg.textContent = '❌ Password tidak cocok';
        matchMsg.className = 'text-xs mt-1 text-red-500';
        pass2Input.focus();
    }
});
</script>

<?php
// ============================================================
// LIST VIEW
// ============================================================
else:
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

    $list = $pdo->query("SELECT * FROM admins ORDER BY is_active DESC, id ASC")->fetchAll();
    $totalAktif = count(array_filter($list, fn($a) => $a['is_active']));
?>

  <div class="flex items-center justify-between mb-5">
    <div>
      <p class="text-sm text-gray-500">
        <span class="font-semibold text-gray-700"><?= count($list) ?></span> akun ·
        <span class="text-green-600 font-medium"><?= $totalAktif ?> aktif</span>
      </p>
      <p class="text-xs text-gray-400 mt-0.5">Kelola siapa saja yang bisa mengakses panel admin</p>
    </div>
    <a href="<?= $b ?>/admin/akun?action=tambah" class="btn-primary">➕ Tambah Akun</a>
  </div>

  <div class="space-y-3 max-w-2xl">
    <?php foreach ($list as $a):
      $isMe = ($a['id'] == $meId);
      $initial = strtoupper(mb_substr($a['full_name'] ?: $a['username'], 0, 1));
      $avatarColors = ['bg-rose-200 text-rose-700','bg-amber-200 text-amber-700','bg-blue-200 text-blue-700','bg-green-200 text-green-700','bg-violet-200 text-violet-700'];
      $color = $avatarColors[$a['id'] % 5];
    ?>
    <div class="bg-white border <?= $isMe ? 'border-blue-200' : 'border-rose-100' ?> rounded-xl p-4 flex items-center gap-4 hover:shadow-sm transition-shadow">
      <!-- Avatar -->
      <div class="w-12 h-12 rounded-xl <?= $color ?> flex items-center justify-center text-lg font-bold flex-shrink-0">
        <?= $initial ?>
      </div>

      <!-- Info -->
      <div class="flex-1 min-w-0">
        <div class="flex items-center gap-2 flex-wrap">
          <p class="font-semibold text-gray-800"><?= clean($a['full_name'] ?: $a['username']) ?></p>
          <?php if ($isMe): ?>
          <span class="text-xs bg-blue-100 text-blue-700 px-2 py-0.5 rounded-full">Kamu</span>
          <?php endif; ?>
          <span class="<?= $a['is_active'] ? 'badge-active' : 'badge-inactive' ?> text-xs">
            <?= $a['is_active'] ? 'Aktif' : 'Nonaktif' ?>
          </span>
        </div>
        <p class="text-xs text-gray-400 mt-0.5">
          @<?= clean($a['username']) ?>
          <?php if (!empty($a['full_name'])): ?>
          · <?= clean($a['full_name']) ?>
          <?php endif; ?>
        </p>
        <p class="text-xs text-gray-300 mt-0.5">
          Dibuat <?= date('d M Y', strtotime($a['created_at'])) ?>
          <?php if ($a['updated_at'] !== $a['created_at']): ?>
          · Diperbarui <?= date('d M Y', strtotime($a['updated_at'])) ?>
          <?php endif; ?>
        </p>
      </div>

      <!-- Aksi -->
      <div class="flex items-center gap-2 flex-shrink-0">
        <a href="<?= $b ?>/admin/akun?action=edit&id=<?= $a['id'] ?>"
           class="text-xs bg-amber-50 text-amber-700 border border-amber-200 px-3 py-1.5 rounded-lg hover:bg-amber-100 font-medium">
          ✏️ Edit
        </a>
        <?php if (!$isMe): ?>
        <a href="<?= $b ?>/admin/akun?toggle=1&id=<?= $a['id'] ?>"
           class="text-xs bg-gray-50 text-gray-600 border border-gray-200 px-2.5 py-1.5 rounded-lg hover:bg-gray-100"
           title="<?= $a['is_active'] ? 'Nonaktifkan' : 'Aktifkan' ?>">
          <?= $a['is_active'] ? '🔕' : '✅' ?>
        </a>
        <a href="<?= $b ?>/admin/akun?hapus=1&id=<?= $a['id'] ?>"
           onclick="return confirm('Hapus akun @<?= clean($a['username']) ?>? Tindakan ini tidak bisa dibatalkan.')"
           class="text-xs text-red-400 hover:text-red-600 px-2 py-1.5 rounded-lg hover:bg-red-50 border border-transparent hover:border-red-100">
          🗑️
        </a>
        <?php else: ?>
        <span class="text-xs text-gray-300 px-2">—</span>
        <?php endif; ?>
      </div>
    </div>
    <?php endforeach; ?>
  </div>

  <!-- Info keamanan -->
  <div class="mt-6 max-w-2xl bg-blue-50 border border-blue-200 rounded-xl p-4">
    <p class="text-xs text-blue-700 font-semibold mb-1">🔒 Catatan Keamanan</p>
    <ul class="text-xs text-blue-600 space-y-0.5 list-disc list-inside">
      <li>Password disimpan terenkripsi (bcrypt) — tidak bisa dilihat siapapun</li>
      <li>Minimal harus ada 1 akun aktif agar tidak terkunci dari panel admin</li>
      <li>Kamu tidak bisa menghapus atau menonaktifkan akunmu sendiri</li>
    </ul>
  </div>

<?php endif; ?>
<?php require_once __DIR__ . '/../includes/admin_footer.php'; ?>