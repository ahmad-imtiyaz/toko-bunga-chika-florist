<?php
$admin_title = 'Manajemen Kategori';
require_once __DIR__ . '/../includes/admin_header.php';
$pdo    = getDB();
$action = $_GET['action'] ?? 'list';
$id     = (int)($_GET['id'] ?? 0);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = [
        'parent_id'   => ($_POST['parent_id'] !== '') ? (int)$_POST['parent_id'] : null,
        'name'        => clean($_POST['name'] ?? ''),
        'slug'        => makeSlug($_POST['name'] ?? ''),
        'description' => clean($_POST['description'] ?? ''),
        'meta_title'  => clean($_POST['meta_title'] ?? ''),
        'meta_desc'   => clean($_POST['meta_desc'] ?? ''),
        'sort_order'  => (int)($_POST['sort_order'] ?? 0),
        'is_active'   => isset($_POST['is_active']) ? 1 : 0,
    ];
    if (!empty($_FILES['image']['name'])) {
        $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
        if (in_array($ext,['jpg','jpeg','png','webp'])) {
            $fn = 'cat-'.$data['slug'].'.'.$ext;
            move_uploaded_file($_FILES['image']['tmp_name'], UPLOAD_DIR.$fn);
            $data['image'] = $fn;
        }
    }
    if ($_POST['action_type'] === 'tambah') {
        $cols = implode(',',array_keys($data)); $vals = ':'.implode(',:',array_keys($data));
        $pdo->prepare("INSERT INTO categories ($cols) VALUES ($vals)")->execute($data);
        $_SESSION['success'] = 'Kategori berhasil ditambahkan.';
    } elseif ($_POST['action_type'] === 'edit' && $id) {
        unset($data['slug']); if (empty($data['image'])) unset($data['image']);
        $sets = implode(',',array_map(fn($k)=>"$k=:$k",array_keys($data)));
        $data['id'] = $id;
        $pdo->prepare("UPDATE categories SET $sets WHERE id=:id")->execute($data);
        $_SESSION['success'] = 'Kategori diperbarui.';
    }
    redirect('/admin/kategori');
}
if (isset($_GET['toggle']) && $id) {
    $cur = $pdo->prepare("SELECT is_active FROM categories WHERE id=?"); $cur->execute([$id]);
    $pdo->prepare("UPDATE categories SET is_active=? WHERE id=?")->execute([$cur->fetchColumn()?0:1,$id]);
    redirect('/admin/kategori');
}
if (isset($_GET['hapus']) && $id) {
    $pdo->prepare("DELETE FROM categories WHERE id=?")->execute([$id]);
    $_SESSION['success'] = 'Kategori dihapus.'; redirect('/admin/kategori');
}

$mainCats = $pdo->query("SELECT id,name FROM categories WHERE parent_id IS NULL AND is_active=1 ORDER BY sort_order")->fetchAll();

if ($action === 'tambah' || ($action === 'edit' && $id)) {
    $item = [];
    if ($action === 'edit') { $s=$pdo->prepare("SELECT * FROM categories WHERE id=?"); $s->execute([$id]); $item=$s->fetch(); }
    ?>
    <div class="max-w-2xl">
      <div class="flex items-center gap-3 mb-5">
        <a href="/admin/kategori" class="text-gray-400 hover:text-rose-600">← Kembali</a>
        <h2 class="font-display font-bold text-gray-800"><?= $action==='tambah'?'Tambah':'Edit' ?> Kategori</h2>
      </div>
      <form method="POST" enctype="multipart/form-data" class="bg-white rounded-xl border border-rose-100 p-6 space-y-4">
        <input type="hidden" name="action_type" value="<?= $action ?>">
        <div class="grid grid-cols-2 gap-4">
          <div><label class="form-label">Nama Kategori *</label><input type="text" name="name" required class="form-input" value="<?= clean($item['name']??'') ?>"></div>
          <div>
            <label class="form-label">Parent (kosong = kategori inti)</label>
            <select name="parent_id" class="form-input">
              <option value="">-- Kategori Inti --</option>
              <?php foreach ($mainCats as $mc): if ($mc['id'] != $id): ?>
              <option value="<?= $mc['id'] ?>" <?= ($item['parent_id']??null)==$mc['id']?'selected':'' ?>><?= clean($mc['name']) ?></option>
              <?php endif; endforeach; ?>
            </select>
          </div>
          <div class="col-span-2"><label class="form-label">Deskripsi</label><textarea name="description" class="form-input" rows="2"><?= clean($item['description']??'') ?></textarea></div>
          <div><label class="form-label">Meta Title</label><input type="text" name="meta_title" class="form-input" value="<?= clean($item['meta_title']??'') ?>"></div>
          <div><label class="form-label">Sort Order</label><input type="number" name="sort_order" class="form-input" value="<?= $item['sort_order']??0 ?>"></div>
          <div class="col-span-2"><label class="form-label">Meta Description</label><textarea name="meta_desc" class="form-input" rows="2"><?= clean($item['meta_desc']??'') ?></textarea></div>
          <div class="col-span-2">
            <label class="form-label">Gambar Kategori</label>
            <?php if (!empty($item['image'])): ?><div class="mb-2"><img src="<?= UPLOAD_URL.$item['image'] ?>" class="h-16 rounded-lg object-cover"></div><?php endif; ?>
            <input type="file" name="image" accept="image/*" class="form-input">
          </div>
          <div class="flex items-center gap-2">
            <input type="checkbox" name="is_active" id="is_active_k" <?= ($item['is_active']??1)?'checked':'' ?>>
            <label for="is_active_k" class="text-sm text-gray-700 cursor-pointer">Aktif</label>
          </div>
        </div>
        <div class="flex gap-2 pt-2">
          <button type="submit" class="btn-primary">Simpan Kategori</button>
          <a href="/admin/kategori" class="btn-secondary">Batal</a>
        </div>
      </form>
    </div>
    <?php
} else {
    $cats = $pdo->query("SELECT c.*,p.name as parent_name,(SELECT COUNT(*) FROM products WHERE category_id=c.id) as prod_count FROM categories c LEFT JOIN categories p ON c.parent_id=p.id ORDER BY c.parent_id IS NOT NULL,c.sort_order ASC,c.name")->fetchAll();
    ?>
    <div class="flex justify-between items-center mb-5">
      <p class="text-sm text-gray-500"><?= count($cats) ?> kategori</p>
      <a href="/admin/kategori?action=tambah" class="btn-primary">+ Tambah Kategori</a>
    </div>
    <div class="bg-white rounded-xl border border-rose-100 overflow-hidden">
      <table class="admin-table w-full">
        <thead><tr><th>Nama</th><th>Parent</th><th style="text-align:center">Produk</th><th style="text-align:center">Status</th><th style="text-align:center">Aksi</th></tr></thead>
        <tbody>
        <?php foreach ($cats as $c): ?>
        <tr>
          <td class="font-medium text-gray-800">
            <?= $c['parent_id']?'<span style="color:#d1d5db;margin-right:4px">—</span>':'' ?><?= clean($c['name']) ?>
            <a href="/<?= $c['slug'] ?>" target="_blank" class="text-xs text-rose-400 hover:underline ml-1">↗</a>
          </td>
          <td class="text-gray-500 text-xs"><?= clean($c['parent_name']??'(Inti)') ?></td>
          <td style="text-align:center" class="text-xs text-gray-500"><?= $c['prod_count'] ?></td>
          <td style="text-align:center"><a href="/admin/kategori?toggle=1&id=<?= $c['id'] ?>"><span class="<?= $c['is_active']?'badge-active':'badge-inactive' ?>"><?= $c['is_active']?'Aktif':'Nonaktif' ?></span></a></td>
          <td style="text-align:center">
            <div style="display:flex;gap:4px;justify-content:center">
              <a href="/admin/kategori?action=edit&id=<?= $c['id'] ?>" style="font-size:.75rem;background:#fef3c7;color:#b45309;padding:.25rem .75rem;border-radius:.5rem;border:1px solid #fde68a">Edit</a>
              <a href="/admin/kategori?hapus=1&id=<?= $c['id'] ?>" onclick="return confirm('Hapus kategori ini?')" class="btn-danger">Hapus</a>
            </div>
          </td>
        </tr>
        <?php endforeach; ?>
        </tbody>
      </table>
    </div>
<?php } ?>
<?php require_once __DIR__ . '/../includes/admin_footer.php'; ?>
