<?php
// ============================================================
// SEMUA LOGIKA DI SINI — SEBELUM ADMIN HEADER
// ============================================================
require_once __DIR__ . '/../../includes/config.php';
requireAdminLogin();

$pdo = getDB();
$b   = BASE_URL;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Handle logo upload
    if (!empty($_FILES['logo']['name'])) {
        $ext = strtolower(pathinfo($_FILES['logo']['name'], PATHINFO_EXTENSION));
        if (in_array($ext, ['jpg','jpeg','png','webp','gif','svg'])) {
            $fn = 'logo.' . $ext;
            move_uploaded_file($_FILES['logo']['tmp_name'], UPLOAD_DIR . $fn);
            $pdo->prepare("UPDATE settings SET setting_value=? WHERE setting_key='logo'")->execute([$fn]);
        }
    }

    // Update semua settings
    $keys = [
        'site_name','site_tagline','whatsapp_number','whatsapp_text',
        'email','address','instagram','facebook','tiktok',
        'meta_title_home','meta_desc_home','footer_text'
    ];
    foreach ($keys as $key) {
        if (isset($_POST[$key])) {
            $val = clean($_POST[$key]);
            $pdo->prepare("UPDATE settings SET setting_value=? WHERE setting_key=?")->execute([$val, $key]);
        }
    }

    $_SESSION['success'] = 'Pengaturan berhasil disimpan.';
    header('Location: ' . $b . '/admin/pengaturan');
    exit;
}

// Ambil semua settings
$s = [];
$labels = [];
foreach ($pdo->query("SELECT setting_key, setting_value, setting_label FROM settings") as $row) {
    $s[$row['setting_key']]      = $row['setting_value'];
    $labels[$row['setting_key']] = $row['setting_label'];
}

// ============================================================
$admin_title = 'Pengaturan Website';
require_once __DIR__ . '/../includes/admin_header.php';
?>

<?php if (!empty($_SESSION['success'])): ?>
  <div class="bg-green-50 border border-green-200 text-green-700 text-sm rounded-lg px-4 py-3 mb-5">
    ✅ <?= clean($_SESSION['success']) ?>
  </div>
  <?php unset($_SESSION['success']); ?>
<?php endif; ?>

<form method="POST" enctype="multipart/form-data" class="max-w-3xl">

  <!-- Tab nav -->
  <div class="flex gap-1 bg-gray-100 p-1 rounded-xl mb-6 flex-wrap">
    <?php
    $tabs = [
      'identitas' => ['icon' => '🏪', 'label' => 'Identitas'],
      'kontak'    => ['icon' => '📱', 'label' => 'Kontak & WA'],
      'sosmed'    => ['icon' => '📲', 'label' => 'Sosial Media'],
      'seo'       => ['icon' => '🔍', 'label' => 'SEO'],
    ];
    foreach ($tabs as $tid => $tab): ?>
    <button type="button" onclick="switchTab('<?= $tid ?>')"
            id="tab-<?= $tid ?>"
            class="tab-btn flex-1 flex items-center justify-center gap-1.5 text-xs font-semibold px-3 py-2 rounded-lg transition-all text-gray-500 hover:text-gray-700">
      <span><?= $tab['icon'] ?></span> <?= $tab['label'] ?>
    </button>
    <?php endforeach; ?>
  </div>

  <!-- ==================== TAB: IDENTITAS ==================== -->
  <div id="panel-identitas" class="tab-panel space-y-5">

    <!-- Logo -->
    <div class="bg-white rounded-xl border border-rose-100 p-6">
      <h3 class="font-semibold text-gray-700 text-sm border-b border-gray-100 pb-2 mb-4">🖼️ Logo Toko</h3>

      <div class="flex items-start gap-6 flex-wrap">
        <!-- Preview logo saat ini -->
        <div class="flex flex-col items-center gap-2">
          <div id="logoPreviewWrap" class="w-32 h-24 rounded-xl border-2 border-dashed border-rose-200 bg-rose-50 flex items-center justify-center overflow-hidden">
            <?php if (!empty($s['logo'])): ?>
              <img id="logoPreview" src="<?= UPLOAD_URL . $s['logo'] ?>" class="max-h-20 max-w-full object-contain p-1">
            <?php else: ?>
              <img id="logoPreview" src="" class="hidden max-h-20 max-w-full object-contain p-1">
              <span id="logoPlaceholder" class="text-3xl">🌸</span>
            <?php endif; ?>
          </div>
          <p class="text-xs text-gray-400">Logo saat ini</p>
        </div>

        <!-- Upload area -->
        <div class="flex-1 min-w-48">
          <div id="logoDropZone"
               class="border-2 border-dashed border-rose-200 rounded-xl p-5 text-center cursor-pointer hover:border-rose-400 hover:bg-rose-50 transition-all"
               onclick="document.getElementById('logoInput').click()">
            <p class="text-sm font-medium text-gray-500">Klik atau seret logo baru ke sini</p>
            <p class="text-xs text-gray-400 mt-1">JPG, PNG, SVG, WebP — Disarankan 200×80px</p>
            <input type="file" id="logoInput" name="logo" accept="image/*" class="hidden">
          </div>
          <p id="logoFileName" class="text-xs text-gray-400 mt-1.5 hidden"></p>
        </div>
      </div>
    </div>

    <!-- Info dasar -->
    <div class="bg-white rounded-xl border border-rose-100 p-6 space-y-4">
      <h3 class="font-semibold text-gray-700 text-sm border-b border-gray-100 pb-2">📋 Informasi Dasar</h3>
      <div class="grid grid-cols-2 gap-4">
        <div>
          <label class="form-label"><?= $labels['site_name'] ?? 'Nama Website' ?></label>
          <input type="text" name="site_name" class="form-input"
                 value="<?= clean($s['site_name'] ?? '') ?>"
                 placeholder="Chika Florist">
        </div>
        <div>
          <label class="form-label"><?= $labels['site_tagline'] ?? 'Tagline' ?></label>
          <input type="text" name="site_tagline" class="form-input"
                 value="<?= clean($s['site_tagline'] ?? '') ?>"
                 placeholder="Bunga untuk setiap momen istimewa">
        </div>
        <div>
          <label class="form-label"><?= $labels['email'] ?? 'Email' ?></label>
          <input type="email" name="email" class="form-input"
                 value="<?= clean($s['email'] ?? '') ?>"
                 placeholder="hello@chikaflorist.com">
        </div>
        <div>
          <label class="form-label"><?= $labels['address'] ?? 'Alamat' ?></label>
          <input type="text" name="address" class="form-input"
                 value="<?= clean($s['address'] ?? '') ?>"
                 placeholder="Jl. Bunga Raya No. 1, Jakarta">
        </div>
        <div class="col-span-2">
          <label class="form-label"><?= $labels['footer_text'] ?? 'Teks Footer' ?></label>
          <textarea name="footer_text" class="form-input" rows="2"
                    placeholder="© 2025 Chika Florist. All rights reserved."><?= clean($s['footer_text'] ?? '') ?></textarea>
        </div>
      </div>
    </div>
  </div>

  <!-- ==================== TAB: KONTAK ==================== -->
  <div id="panel-kontak" class="tab-panel hidden space-y-5">

    <div class="bg-white rounded-xl border border-rose-100 p-6 space-y-4">
      <h3 class="font-semibold text-gray-700 text-sm border-b border-gray-100 pb-2">📱 WhatsApp</h3>

      <div>
        <label class="form-label">
          <?= $labels['whatsapp_number'] ?? 'Nomor WhatsApp' ?>
          <span class="text-gray-400 font-normal text-xs">— format: 628xxxxxxxxxx (tanpa + atau spasi)</span>
        </label>
        <div class="relative">
          <span class="absolute left-3 top-1/2 -translate-y-1/2 text-sm text-gray-400">📞</span>
          <input type="text" name="whatsapp_number" id="waNumber" class="form-input pl-8"
                 value="<?= clean($s['whatsapp_number'] ?? '') ?>"
                 placeholder="6281234567890"
                 oninput="updateWaPreview()">
        </div>
      </div>

      <div>
        <label class="form-label"><?= $labels['whatsapp_text'] ?? 'Teks Pesan Otomatis' ?></label>
        <input type="text" name="whatsapp_text" id="waText" class="form-input"
               value="<?= clean($s['whatsapp_text'] ?? '') ?>"
               placeholder="Halo Chika Florist, saya ingin memesan bunga..."
               oninput="updateWaPreview()">
      </div>

      <!-- Preview link WA -->
      <div class="bg-green-50 border border-green-200 rounded-xl p-4">
        <p class="text-xs text-gray-500 font-medium mb-2">Preview Link WhatsApp:</p>
        <a id="waPreviewLink" href="#" target="_blank"
           class="text-sm text-green-700 break-all hover:underline font-mono">
          —
        </a>
        <p class="text-xs text-gray-400 mt-2">Klik untuk test link WA</p>
      </div>
    </div>

    <div class="bg-white rounded-xl border border-rose-100 p-6 space-y-4">
      <h3 class="font-semibold text-gray-700 text-sm border-b border-gray-100 pb-2">📧 Kontak Lainnya</h3>
      <div class="grid grid-cols-2 gap-4">
        <div class="col-span-2">
          <label class="form-label"><?= $labels['email'] ?? 'Email' ?></label>
          <div class="relative">
            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-sm text-gray-400">✉️</span>
            <input type="email" name="email" class="form-input pl-8"
                   value="<?= clean($s['email'] ?? '') ?>"
                   placeholder="hello@chikaflorist.com">
          </div>
          <p class="text-xs text-gray-400 mt-1">Ini akan diisi ulang di tab Identitas juga (sinkron)</p>
        </div>
      </div>
    </div>
  </div>

  <!-- ==================== TAB: SOSIAL MEDIA ==================== -->
  <div id="panel-sosmed" class="tab-panel hidden space-y-5">

    <div class="bg-white rounded-xl border border-rose-100 p-6 space-y-4">
      <h3 class="font-semibold text-gray-700 text-sm border-b border-gray-100 pb-2">📲 Link Sosial Media</h3>
      <p class="text-xs text-gray-400 -mt-2">Isi dengan URL lengkap. Kosongkan jika tidak dipakai.</p>

      <div>
        <label class="form-label flex items-center gap-2">
          <span class="text-lg">📸</span> <?= $labels['instagram'] ?? 'Instagram' ?>
        </label>
        <input type="url" name="instagram" class="form-input"
               value="<?= clean($s['instagram'] ?? '') ?>"
               placeholder="https://instagram.com/chikaflorist">
      </div>

      <div>
        <label class="form-label flex items-center gap-2">
          <span class="text-lg">👥</span> <?= $labels['facebook'] ?? 'Facebook' ?>
        </label>
        <input type="url" name="facebook" class="form-input"
               value="<?= clean($s['facebook'] ?? '') ?>"
               placeholder="https://facebook.com/chikaflorist">
      </div>

      <div>
        <label class="form-label flex items-center gap-2">
          <span class="text-lg">🎵</span> <?= $labels['tiktok'] ?? 'TikTok' ?>
        </label>
        <input type="url" name="tiktok" class="form-input"
               value="<?= clean($s['tiktok'] ?? '') ?>"
               placeholder="https://tiktok.com/@chikaflorist">
      </div>

      <!-- Social media preview -->
      <div class="grid grid-cols-3 gap-3 pt-2">
        <?php
        $socials = [
          'instagram' => ['emoji' => '📸', 'label' => 'Instagram', 'color' => 'bg-pink-50 border-pink-200 text-pink-700'],
          'facebook'  => ['emoji' => '👥', 'label' => 'Facebook',  'color' => 'bg-blue-50 border-blue-200 text-blue-700'],
          'tiktok'    => ['emoji' => '🎵', 'label' => 'TikTok',    'color' => 'bg-gray-50 border-gray-200 text-gray-700'],
        ];
        foreach ($socials as $key => $info):
          $val = $s[$key] ?? '';
        ?>
        <div class="<?= $info['color'] ?> border rounded-xl p-3 text-center">
          <div class="text-2xl mb-1"><?= $info['emoji'] ?></div>
          <p class="text-xs font-semibold"><?= $info['label'] ?></p>
          <p class="text-xs mt-1 <?= $val ? 'text-green-600' : 'text-gray-400' ?>">
            <?= $val ? '✅ Terhubung' : '— Belum diisi' ?>
          </p>
          <?php if ($val): ?>
          <a href="<?= clean($val) ?>" target="_blank" class="text-xs underline mt-0.5 block">Buka ↗</a>
          <?php endif; ?>
        </div>
        <?php endforeach; ?>
      </div>
    </div>
  </div>

  <!-- ==================== TAB: SEO ==================== -->
  <div id="panel-seo" class="tab-panel hidden space-y-5">

    <div class="bg-white rounded-xl border border-rose-100 p-6 space-y-5">
      <h3 class="font-semibold text-gray-700 text-sm border-b border-gray-100 pb-2">🔍 SEO Homepage</h3>

      <div>
        <label class="form-label">
          <?= $labels['meta_title_home'] ?? 'Meta Title' ?>
          <span class="text-gray-400 font-normal text-xs">— ideal 50–60 karakter</span>
        </label>
        <input type="text" name="meta_title_home" id="seoTitle" class="form-input"
               value="<?= clean($s['meta_title_home'] ?? '') ?>"
               placeholder="Chika Florist - Toko Bunga Online Jakarta"
               oninput="updateSeoPreview()" maxlength="80">
        <div class="flex justify-between text-xs mt-1">
          <span id="titleCount" class="text-gray-400">0 karakter</span>
          <span id="titleWarn" class="hidden text-amber-500">⚠️ Idealnya di bawah 60 karakter</span>
        </div>
      </div>

      <div>
        <label class="form-label">
          <?= $labels['meta_desc_home'] ?? 'Meta Description' ?>
          <span class="text-gray-400 font-normal text-xs">— ideal 150–160 karakter</span>
        </label>
        <textarea name="meta_desc_home" id="seoDesc" class="form-input" rows="3"
                  placeholder="Pesan bunga online di Jakarta. Tersedia hand bouquet, bunga papan, standing flower untuk berbagai acara..."
                  oninput="updateSeoPreview()" maxlength="200"><?= clean($s['meta_desc_home'] ?? '') ?></textarea>
        <div class="flex justify-between text-xs mt-1">
          <span id="descCount" class="text-gray-400">0 karakter</span>
          <span id="descWarn" class="hidden text-amber-500">⚠️ Idealnya di bawah 160 karakter</span>
        </div>
      </div>

      <!-- SERP Preview -->
      <div>
        <label class="form-label">Preview Google (SERP)</label>
        <div class="border border-gray-200 rounded-xl p-4 bg-white font-sans">
          <p class="text-xs text-green-700 mb-0.5"><?= rtrim($b, '/') ?>/</p>
          <p id="serp-title" class="text-blue-700 text-lg font-medium hover:underline cursor-pointer leading-snug truncate">
            <?= clean($s['meta_title_home'] ?? 'Judul halaman...') ?>
          </p>
          <p id="serp-desc" class="text-sm text-gray-600 mt-1 leading-relaxed line-clamp-2">
            <?= clean($s['meta_desc_home'] ?? 'Deskripsi meta akan muncul di sini. Pastikan mengandung kata kunci utama dan ajakan bertindak yang menarik.') ?>
          </p>
        </div>
      </div>
    </div>
  </div>

  <!-- Tombol simpan (sticky) -->
  <div class="sticky bottom-4 mt-6">
    <div class="bg-white border border-rose-100 rounded-xl px-5 py-3 flex items-center justify-between shadow-lg">
      <p class="text-xs text-gray-400">Perubahan belum tersimpan sampai kamu klik tombol ini</p>
      <button type="submit" class="btn-primary">
        💾 Simpan Semua Pengaturan
      </button>
    </div>
  </div>

</form>

<script>
// ── Tab system ────────────────────────────────────────────────
function switchTab(id) {
    document.querySelectorAll('.tab-panel').forEach(p => p.classList.add('hidden'));
    document.querySelectorAll('.tab-btn').forEach(b => {
        b.classList.remove('bg-white', 'text-rose-600', 'shadow-sm');
        b.classList.add('text-gray-500');
    });
    document.getElementById('panel-' + id).classList.remove('hidden');
    const btn = document.getElementById('tab-' + id);
    btn.classList.add('bg-white', 'text-rose-600', 'shadow-sm');
    btn.classList.remove('text-gray-500');
    localStorage.setItem('pengaturan_tab', id);
}
// Restore tab terakhir
const lastTab = localStorage.getItem('pengaturan_tab') || 'identitas';
switchTab(lastTab);

// ── Logo drag-and-drop preview ────────────────────────────────
const logoInput   = document.getElementById('logoInput');
const logoPreview = document.getElementById('logoPreview');
const logoHolder  = document.getElementById('logoPlaceholder');
const logoFName   = document.getElementById('logoFileName');
const logoDropZ   = document.getElementById('logoDropZone');

function previewLogo(file) {
    if (!file) return;
    const reader = new FileReader();
    reader.onload = e => {
        logoPreview.src = e.target.result;
        logoPreview.classList.remove('hidden');
        if (logoHolder) logoHolder.classList.add('hidden');
        logoFName.textContent = '📎 ' + file.name;
        logoFName.classList.remove('hidden');
    };
    reader.readAsDataURL(file);
}

logoInput.addEventListener('change', () => previewLogo(logoInput.files[0]));
logoDropZ.addEventListener('dragover', e => { e.preventDefault(); logoDropZ.classList.add('border-rose-400','bg-rose-50'); });
logoDropZ.addEventListener('dragleave', () => logoDropZ.classList.remove('border-rose-400','bg-rose-50'));
logoDropZ.addEventListener('drop', e => {
    e.preventDefault();
    logoDropZ.classList.remove('border-rose-400','bg-rose-50');
    const file = e.dataTransfer.files[0];
    if (file) {
        const dt = new DataTransfer();
        dt.items.add(file);
        logoInput.files = dt.files;
        previewLogo(file);
    }
});

// ── WhatsApp preview link ─────────────────────────────────────
function updateWaPreview() {
    const num  = document.getElementById('waNumber').value.trim().replace(/\D/g,'');
    const text = encodeURIComponent(document.getElementById('waText').value.trim());
    const link = document.getElementById('waPreviewLink');
    if (num) {
        const url = 'https://wa.me/' + num + (text ? '?text=' + text : '');
        link.href        = url;
        link.textContent = url;
    } else {
        link.href        = '#';
        link.textContent = '— Isi nomor WA terlebih dahulu';
    }
}
updateWaPreview();

// ── SEO counter + SERP preview ────────────────────────────────
function updateSeoPreview() {
    const title = document.getElementById('seoTitle').value;
    const desc  = document.getElementById('seoDesc').value;

    document.getElementById('titleCount').textContent = title.length + ' karakter';
    document.getElementById('descCount').textContent  = desc.length  + ' karakter';
    document.getElementById('titleWarn').classList.toggle('hidden', title.length <= 60);
    document.getElementById('descWarn').classList.toggle('hidden',  desc.length  <= 160);

    document.getElementById('serp-title').textContent = title || 'Judul halaman...';
    document.getElementById('serp-desc').textContent  = desc  || 'Deskripsi meta akan muncul di sini.';
}
updateSeoPreview();
</script>

<?php require_once __DIR__ . '/../includes/admin_footer.php'; ?>