<?php
$admin_title = 'Halaman Layanan';
require_once __DIR__ . '/../includes/admin_header.php';
$pdo    = getDB();
$action = $_GET['action'] ?? 'list';
$id     = (int)($_GET['id'] ?? 0);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = [
        'title'      => clean($_POST['title'] ?? ''),
        'slug'       => makeSlug($_POST['slug'] ?? $_POST['title'] ?? ''),
        'h1_text'    => clean($_POST['h1_text'] ?? ''),
        'content'    => $_POST['content'] ?? '',
        'meta_title' => clean($_POST['meta_title'] ?? ''),
        'meta_desc'  => clean($_POST['meta_desc'] ?? ''),
        'sort_order' => (int)($_POST['sort_order'] ?? 0),
        'is_active'  => isset($_POST['is_active']) ? 1 : 0,
    ];
    if ($_POST['action_type'] === 'tambah') {
        $cols = implode(',',array_keys($data)); $vals = ':'.implode(',:',array_keys($data));
        $pdo->prepare("INSERT INTO service_pages ($cols) VALUES ($vals)")->execute($data);
        $_SESSION['success'] = 'Halaman layanan ditambahkan.';
    } elseif ($_POST['action_type'] === 'edit' && $id) {
        if (!$_POST['slug']) unset($data['slug']);
        $sets = implode(',',array_map(fn($k)=>"$k=:$k",array_keys($data)));
        $data['id'] = $id;
        $pdo->prepare("UPDATE service_pages SET $sets WHERE id=:id")->execute($data);
        $_SESSION['success'] = 'Halaman layanan diperbarui.';
    }
    redirect('/admin/layanan');
}
if (isset($_GET['toggle']) && $id) {
    $cur = $pdo->prepare("SELECT is_active FROM service_pages WHERE id=?"); $cur->execute([$id]);
    $pdo->prepare("UPDATE service_pages SET is_active=? WHERE id=?")->execute([$cur->fetchColumn()?0:1,$id]);
    redirect('/admin/layanan');
}
if (isset($_GET['hapus']) && $id) {
    $pdo->prepare("DELETE FROM service_pages WHERE id=?")->execute([$id]);
    $_SESSION['success'] = 'Halaman dihapus.'; redirect('/admin/layanan');
}

if ($action === 'tambah' || ($action === 'edit' && $id)) {
    $item = [];
    if ($action === 'edit') { $s=$pdo->prepare("SELECT * FROM service_pages WHERE id=?"); $s->execute([$id]); $item=$s->fetch(); }
    ?>
    <div class="max-w-3xl">
      <div class="flex items-center gap-3 mb-5">
        <a href="/admin/layanan" class="text-gray-400 hover:text-rose-600">← Kembali</a>
        <h2 class="font-display font-bold text-gray-800"><?= $action==='tambah'?'Tambah':'Edit' ?> Halaman Layanan</h2>
      </div>
      <form method="POST" class="bg-white rounded-xl border border-rose-100 p-6 space-y-4">
        <input type="hidden" name="action_type" value="<?= $action ?>">
        <div><label class="form-label">Judul Halaman *</label><input type="text" name="title" required class="form-input" value="<?= clean($item['title']??'') ?>"></div>
        <div><label class="form-label">Slug URL (kosong = auto dari judul)</label><input type="text" name="slug" class="form-input" value="<?= clean($item['slug']??'') ?>" placeholder="contoh: toko-bunga-online-24-jam-indonesia"></div>
        <div><label class="form-label">H1 Text</label><input type="text" name="h1_text" class="form-input" value="<?= clean($item['h1_text']??'') ?>"></div>
        <div><label class="form-label">Meta Title</label><input type="text" name="meta_title" class="form-input" value="<?= clean($item['meta_title']??'') ?>"></div>
        <div><label class="form-label">Meta Description</label><textarea name="meta_desc" class="form-input" rows="2"><?= clean($item['meta_desc']??'') ?></textarea></div>
        <div>
          <label class="form-label">Konten HTML (kosong = gunakan konten default)</label>
          <textarea name="content" class="form-input font-mono text-xs" rows="12"><?= htmlspecialchars($item['content']??'',ENT_QUOTES,'UTF-8') ?></textarea>
        </div>
        <div class="flex items-center gap-4">
          <div><label class="form-label">Sort Order</label><input type="number" name="sort_order" class="form-input w-24" value="<?= $item['sort_order']??0 ?>"></div>
          <div class="flex items-center gap-2 mt-5">
            <input type="checkbox" name="is_active" id="is_active_l" <?= ($item['is_active']??1)?'checked':'' ?>>
            <label for="is_active_l" class="text-sm text-gray-700 cursor-pointer">Aktif</label>
          </div>
        </div>
        <div class="flex gap-2 pt-2">
          <button type="submit" class="btn-primary">Simpan</button>
          <a href="/admin/layanan" class="btn-secondary">Batal</a>
        </div>
      </form>
    </div>
    <?php
} else {
    $pages = $pdo->query("SELECT * FROM service_pages ORDER BY sort_order ASC,title ASC")->fetchAll();
    ?>
    <div class="flex justify-between items-center mb-5">
      <p class="text-sm text-gray-500"><?= count($pages) ?> halaman layanan</p>
      <a href="/admin/layanan?action=tambah" class="btn-primary">+ Tambah Halaman</a>
    </div>
    <div class="bg-white rounded-xl border border-rose-100 overflow-hidden">
      <table class="admin-table w-full">
        <thead><tr><th>Judul</th><th>Slug URL</th><th style="text-align:center">Status</th><th style="text-align:center">Aksi</th></tr></thead>
        <tbody>
        <?php foreach ($pages as $p): ?>
        <tr>
          <td class="font-medium text-gray-800"><?= clean($p['title']) ?></td>
          <td class="text-gray-400 text-xs font-mono">/<?= $p['slug'] ?> <a href="/<?= $p['slug'] ?>" target="_blank" class="text-rose-400">↗</a></td>
          <td style="text-align:center"><a href="/admin/layanan?toggle=1&id=<?= $p['id'] ?>"><span class="<?= $p['is_active']?'badge-active':'badge-inactive' ?>"><?= $p['is_active']?'Aktif':'Nonaktif' ?></span></a></td>
          <td style="text-align:center">
            <div style="display:flex;gap:4px;justify-content:center">
              <a href="/admin/layanan?action=edit&id=<?= $p['id'] ?>" style="font-size:.75rem;background:#fef3c7;color:#b45309;padding:.25rem .75rem;border-radius:.5rem;border:1px solid #fde68a">Edit</a>
              <a href="/admin/layanan?hapus=1&id=<?= $p['id'] ?>" onclick="return confirm('Hapus halaman ini?')" class="btn-danger">Hapus</a>
            </div>
          </td>
        </tr>
        <?php endforeach; ?>
        </tbody>
      </table>
    </div>
<?php } ?>
<?php require_once __DIR__ . '/../includes/admin_footer.php'; ?>
