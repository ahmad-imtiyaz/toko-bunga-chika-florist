<?php
$admin_title = 'Pengaturan Website';
require_once __DIR__ . '/../includes/admin_header.php';
$pdo = getDB();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Handle logo upload
    if (!empty($_FILES['logo']['name'])) {
        $ext = strtolower(pathinfo($_FILES['logo']['name'], PATHINFO_EXTENSION));
        if (in_array($ext,['jpg','jpeg','png','webp','gif','svg'])) {
            $fn = 'logo.'.$ext;
            move_uploaded_file($_FILES['logo']['tmp_name'], UPLOAD_DIR.$fn);
            $pdo->prepare("UPDATE settings SET setting_value=? WHERE setting_key='logo'")->execute([$fn]);
        }
    }
    // Update semua settings
    $keys = ['site_name','site_tagline','whatsapp_number','whatsapp_text','email','address',
             'instagram','facebook','tiktok','meta_title_home','meta_desc_home','footer_text'];
    foreach ($keys as $key) {
        if (isset($_POST[$key])) {
            $val = clean($_POST[$key]);
            $pdo->prepare("UPDATE settings SET setting_value=? WHERE setting_key=?")->execute([$val, $key]);
        }
    }
    $_SESSION['success'] = 'Pengaturan berhasil disimpan.';
    redirect('/admin/pengaturan');
}

$settings = $pdo->query("SELECT setting_key, setting_value, setting_label FROM settings ORDER BY id")->fetchAll(PDO::FETCH_KEY_PAIR + 0);
// rebuild as key=>value
$s = [];
foreach ($pdo->query("SELECT setting_key, setting_value FROM settings") as $row) {
    $s[$row['setting_key']] = $row['setting_value'];
}
$labels = [];
foreach ($pdo->query("SELECT setting_key, setting_label FROM settings") as $row) {
    $labels[$row['setting_key']] = $row['setting_label'];
}
?>

<form method="POST" enctype="multipart/form-data" class="max-w-3xl space-y-6">

  <!-- Identitas -->
  <div class="bg-white rounded-xl border border-rose-100 p-6">
    <h2 class="font-display font-bold text-gray-800 mb-4">Identitas Website</h2>
    <div class="space-y-4">
      <div>
        <label class="form-label">Logo</label>
        <?php if (!empty($s['logo'])): ?>
        <div class="mb-2 flex items-center gap-3">
          <img src="<?= UPLOAD_URL.$s['logo'] ?>" class="h-12 object-contain rounded border border-rose-100">
          <span class="text-xs text-gray-400">Logo saat ini: <?= clean($s['logo']) ?></span>
        </div>
        <?php endif; ?>
        <input type="file" name="logo" accept="image/*" class="form-input">
      </div>
      <div class="grid grid-cols-2 gap-4">
        <div><label class="form-label"><?= $labels['site_name'] ?></label><input type="text" name="site_name" class="form-input" value="<?= clean($s['site_name']??'') ?>"></div>
        <div><label class="form-label"><?= $labels['site_tagline'] ?></label><input type="text" name="site_tagline" class="form-input" value="<?= clean($s['site_tagline']??'') ?>"></div>
        <div><label class="form-label"><?= $labels['email'] ?></label><input type="email" name="email" class="form-input" value="<?= clean($s['email']??'') ?>"></div>
        <div><label class="form-label"><?= $labels['address'] ?></label><input type="text" name="address" class="form-input" value="<?= clean($s['address']??'') ?>"></div>
      </div>
      <div><label class="form-label"><?= $labels['footer_text'] ?></label><textarea name="footer_text" class="form-input" rows="2"><?= clean($s['footer_text']??'') ?></textarea></div>
    </div>
  </div>

  <!-- WhatsApp -->
  <div class="bg-white rounded-xl border border-rose-100 p-6">
    <h2 class="font-display font-bold text-gray-800 mb-4">WhatsApp & Kontak</h2>
    <div class="grid grid-cols-2 gap-4">
      <div><label class="form-label"><?= $labels['whatsapp_number'] ?> (format: 628xxx)</label><input type="text" name="whatsapp_number" class="form-input" value="<?= clean($s['whatsapp_number']??'') ?>"></div>
      <div class="col-span-2"><label class="form-label"><?= $labels['whatsapp_text'] ?></label><input type="text" name="whatsapp_text" class="form-input" value="<?= clean($s['whatsapp_text']??'') ?>"></div>
    </div>
  </div>

  <!-- Sosial Media -->
  <div class="bg-white rounded-xl border border-rose-100 p-6">
    <h2 class="font-display font-bold text-gray-800 mb-4">Sosial Media</h2>
    <div class="space-y-4">
      <div><label class="form-label"><?= $labels['instagram'] ?></label><input type="url" name="instagram" class="form-input" value="<?= clean($s['instagram']??'') ?>" placeholder="https://instagram.com/..."></div>
      <div><label class="form-label"><?= $labels['facebook'] ?></label><input type="url" name="facebook" class="form-input" value="<?= clean($s['facebook']??'') ?>" placeholder="https://facebook.com/..."></div>
      <div><label class="form-label"><?= $labels['tiktok'] ?></label><input type="url" name="tiktok" class="form-input" value="<?= clean($s['tiktok']??'') ?>" placeholder="https://tiktok.com/..."></div>
    </div>
  </div>

  <!-- SEO Homepage -->
  <div class="bg-white rounded-xl border border-rose-100 p-6">
    <h2 class="font-display font-bold text-gray-800 mb-4">SEO Homepage</h2>
    <div class="space-y-4">
      <div><label class="form-label"><?= $labels['meta_title_home'] ?></label><input type="text" name="meta_title_home" class="form-input" value="<?= clean($s['meta_title_home']??'') ?>"></div>
      <div><label class="form-label"><?= $labels['meta_desc_home'] ?></label><textarea name="meta_desc_home" class="form-input" rows="2"><?= clean($s['meta_desc_home']??'') ?></textarea></div>
    </div>
  </div>

  <div class="flex gap-3">
    <button type="submit" class="btn-primary">Simpan Semua Pengaturan</button>
  </div>
</form>

<?php require_once __DIR__ . '/../includes/admin_footer.php'; ?>
