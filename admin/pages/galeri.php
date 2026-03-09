<?php
$admin_title = 'Manajemen Galeri';
require_once __DIR__ . '/../includes/admin_header.php';
$pdo    = getDB();
$action = $_GET['action'] ?? 'list';
$id     = (int)($_GET['id'] ?? 0);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = [
        'title'      => clean($_POST['title'] ?? ''),
        'category'   => clean($_POST['category'] ?? ''),
        'alt_text'   => clean($_POST['alt_text'] ?? ''),
        'sort_order' => (int)($_POST['sort_order'] ?? 0),
        'is_active'  => isset($_POST['is_active']) ? 1 : 0,
    ];
    // Upload gambar
    if (!empty($_FILES['image']['name'])) {
        $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
        if (in_array($ext,['jpg','jpeg','png','webp'])) {
            $fn = 'gallery-'.time().'.'.$ext;
            move_uploaded_file($_FILES['image']['tmp_name'], UPLOAD_DIR.$fn);
            $data['image'] = $fn;
        }
    }
    if ($_POST['action_type'] === 'tambah' && isset($data['image'])) {
        $cols = implode(',',array_keys($data)); $vals = ':'.implode(',:',array_keys($data));
        $pdo->prepare("INSERT INTO gallery ($cols) VALUES ($vals)")->execute($data);
        $_SESSION['success'] = 'Foto berhasil diupload.';
    } elseif ($_POST['action_type'] === 'edit' && $id) {
        if (empty($data['image'])) unset($data['image']);
        $sets = implode(',',array_map(fn($k)=>"$k=:$k",array_keys($data)));
        $data['id'] = $id;
        $pdo->prepare("UPDATE gallery SET $sets WHERE id=:id")->execute($data);
        $_SESSION['success'] = 'Foto diperbarui.';
    } elseif ($_POST['action_type'] === 'tambah' && !isset($data['image'])) {
        $_SESSION['error'] = 'Harap pilih file gambar.';
    }
    redirect('/admin/galeri');
}
if (isset($_GET['toggle']) && $id) {
    $cur = $pdo->prepare("SELECT is_active FROM gallery WHERE id=?"); $cur->execute([$id]);
    $pdo->prepare("UPDATE gallery SET is_active=? WHERE id=?")->execute([$cur->fetchColumn()?0:1,$id]);
    redirect('/admin/galeri');
}
if (isset($_GET['hapus']) && $id) {
    $row = $pdo->prepare("SELECT image FROM gallery WHERE id=?"); $row->execute([$id]); $r=$row->fetch();
    if ($r && $r['image'] && file_exists(UPLOAD_DIR.$r['image'])) @unlink(UPLOAD_DIR.$r['image']);
    $pdo->prepare("DELETE FROM gallery WHERE id=?")->execute([$id]);
    $_SESSION['success'] = 'Foto dihapus.'; redirect('/admin/galeri');
}

if ($action === 'tambah' || ($action === 'edit' && $id)) {
    $item = [];
    if ($action === 'edit') { $s=$pdo->prepare("SELECT * FROM gallery WHERE id=?"); $s->execute([$id]); $item=$s->fetch(); }
    ?>
    <div class="max-w-xl">
      <div class="flex items-center gap-3 mb-5">
        <a href="/admin/galeri" class="text-gray-400 hover:text-rose-600">← Kembali</a>
        <h2 class="font-display font-bold text-gray-800"><?= $action==='tambah'?'Upload Foto':'Edit Foto' ?></h2>
      </div>
      <form method="POST" enctype="multipart/form-data" class="bg-white rounded-xl border border-rose-100 p-6 space-y-4">
        <input type="hidden" name="action_type" value="<?= $action ?>">
        <?php if (!empty($item['image'])): ?>
        <div><img src="<?= UPLOAD_URL.$item['image'] ?>" class="h-32 rounded-xl object-cover mb-2"></div>
        <?php endif; ?>
        <div><label class="form-label"><?= $action==='tambah'?'Pilih Gambar *':'Ganti Gambar (opsional)' ?></label><input type="file" name="image" accept="image/*" class="form-input" <?= $action==='tambah'?'required':'' ?>></div>
        <div class="grid grid-cols-2 gap-4">
          <div><label class="form-label">Judul</label><input type="text" name="title" class="form-input" value="<?= clean($item['title']??'') ?>"></div>
          <div><label class="form-label">Kategori</label><input type="text" name="category" class="form-input" value="<?= clean($item['category']??'') ?>" placeholder="Bunga Papan, Buket, dll"></div>
          <div class="col-span-2"><label class="form-label">Alt Text (untuk SEO)</label><input type="text" name="alt_text" class="form-input" value="<?= clean($item['alt_text']??'') ?>"></div>
          <div><label class="form-label">Sort Order</label><input type="number" name="sort_order" class="form-input" value="<?= $item['sort_order']??0 ?>"></div>
          <div class="flex items-center gap-2 self-end pb-1">
            <input type="checkbox" name="is_active" id="is_active_g" <?= ($item['is_active']??1)?'checked':'' ?>>
            <label for="is_active_g" class="text-sm text-gray-700 cursor-pointer">Aktif</label>
          </div>
        </div>
        <div class="flex gap-2 pt-2">
          <button type="submit" class="btn-primary"><?= $action==='tambah'?'Upload Foto':'Simpan' ?></button>
          <a href="/admin/galeri" class="btn-secondary">Batal</a>
        </div>
      </form>
    </div>
    <?php
} else {
    $list = $pdo->query("SELECT * FROM gallery ORDER BY is_active DESC,sort_order ASC")->fetchAll();
    ?>
    <div class="flex justify-between items-center mb-5">
      <p class="text-sm text-gray-500"><?= count($list) ?> foto</p>
      <a href="/admin/galeri?action=tambah" class="btn-primary">+ Upload Foto</a>
    </div>
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4">
      <?php foreach ($list as $g): ?>
      <div class="bg-white rounded-xl border border-rose-100 overflow-hidden">
        <div class="h-36 overflow-hidden bg-rose-50">
          <img src="<?= UPLOAD_URL.$g['image'] ?>" alt="<?= clean($g['alt_text']??$g['title']) ?>" class="w-full h-full object-cover">
        </div>
        <div class="p-3">
          <p class="text-xs font-medium text-gray-700 truncate"><?= clean($g['title']??'-') ?></p>
          <p class="text-xs text-gray-400"><?= clean($g['category']??'') ?></p>
          <div class="flex items-center justify-between mt-2">
            <a href="/admin/galeri?toggle=1&id=<?= $g['id'] ?>"><span class="<?= $g['is_active']?'badge-active':'badge-inactive' ?>"><?= $g['is_active']?'Aktif':'Nonaktif' ?></span></a>
            <div style="display:flex;gap:4px">
              <a href="/admin/galeri?action=edit&id=<?= $g['id'] ?>" style="font-size:.7rem;background:#fef3c7;color:#b45309;padding:.2rem .6rem;border-radius:.375rem;border:1px solid #fde68a">Edit</a>
              <a href="/admin/galeri?hapus=1&id=<?= $g['id'] ?>" onclick="return confirm('Hapus foto?')" class="btn-danger" style="font-size:.7rem">Hapus</a>
            </div>
          </div>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
<?php } ?>
<?php require_once __DIR__ . '/../includes/admin_footer.php'; ?>
