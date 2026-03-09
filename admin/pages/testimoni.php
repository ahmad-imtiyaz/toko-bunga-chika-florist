<?php
$admin_title = 'Manajemen Testimoni';
require_once __DIR__ . '/../includes/admin_header.php';
$pdo    = getDB();
$action = $_GET['action'] ?? 'list';
$id     = (int)($_GET['id'] ?? 0);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = [
        'customer_name' => clean($_POST['customer_name'] ?? ''),
        'customer_city' => clean($_POST['customer_city'] ?? ''),
        'rating'        => min(5, max(1, (int)($_POST['rating'] ?? 5))),
        'content'       => clean($_POST['content'] ?? ''),
        'sort_order'    => (int)($_POST['sort_order'] ?? 0),
        'is_active'     => isset($_POST['is_active']) ? 1 : 0,
    ];
    if ($_POST['action_type'] === 'tambah') {
        $cols = implode(',',array_keys($data)); $vals = ':'.implode(',:',array_keys($data));
        $pdo->prepare("INSERT INTO testimonials ($cols) VALUES ($vals)")->execute($data);
        $_SESSION['success'] = 'Testimoni ditambahkan.';
    } elseif ($_POST['action_type'] === 'edit' && $id) {
        $sets = implode(',',array_map(fn($k)=>"$k=:$k",array_keys($data)));
        $data['id'] = $id;
        $pdo->prepare("UPDATE testimonials SET $sets WHERE id=:id")->execute($data);
        $_SESSION['success'] = 'Testimoni diperbarui.';
    }
    redirect('/admin/testimoni');
}
if (isset($_GET['toggle']) && $id) {
    $cur = $pdo->prepare("SELECT is_active FROM testimonials WHERE id=?"); $cur->execute([$id]);
    $pdo->prepare("UPDATE testimonials SET is_active=? WHERE id=?")->execute([$cur->fetchColumn()?0:1,$id]);
    redirect('/admin/testimoni');
}
if (isset($_GET['hapus']) && $id) {
    $pdo->prepare("DELETE FROM testimonials WHERE id=?")->execute([$id]);
    $_SESSION['success'] = 'Testimoni dihapus.'; redirect('/admin/testimoni');
}

if ($action === 'tambah' || ($action === 'edit' && $id)) {
    $item = [];
    if ($action === 'edit') { $s=$pdo->prepare("SELECT * FROM testimonials WHERE id=?"); $s->execute([$id]); $item=$s->fetch(); }
    ?>
    <div class="max-w-xl">
      <div class="flex items-center gap-3 mb-5">
        <a href="/admin/testimoni" class="text-gray-400 hover:text-rose-600">← Kembali</a>
        <h2 class="font-display font-bold text-gray-800"><?= $action==='tambah'?'Tambah':'Edit' ?> Testimoni</h2>
      </div>
      <form method="POST" class="bg-white rounded-xl border border-rose-100 p-6 space-y-4">
        <input type="hidden" name="action_type" value="<?= $action ?>">
        <div class="grid grid-cols-2 gap-4">
          <div><label class="form-label">Nama Pelanggan *</label><input type="text" name="customer_name" required class="form-input" value="<?= clean($item['customer_name']??'') ?>"></div>
          <div><label class="form-label">Kota</label><input type="text" name="customer_city" class="form-input" value="<?= clean($item['customer_city']??'') ?>"></div>
          <div>
            <label class="form-label">Rating</label>
            <select name="rating" class="form-input">
              <?php for ($r=5;$r>=1;$r--): ?>
              <option value="<?= $r ?>" <?= ($item['rating']??5)==$r?'selected':'' ?>><?= $r ?> Bintang</option>
              <?php endfor; ?>
            </select>
          </div>
          <div><label class="form-label">Sort Order</label><input type="number" name="sort_order" class="form-input" value="<?= $item['sort_order']??0 ?>"></div>
          <div class="col-span-2"><label class="form-label">Isi Testimoni *</label><textarea name="content" required class="form-input" rows="3"><?= clean($item['content']??'') ?></textarea></div>
          <div class="flex items-center gap-2">
            <input type="checkbox" name="is_active" id="is_active_t" <?= ($item['is_active']??1)?'checked':'' ?>>
            <label for="is_active_t" class="text-sm text-gray-700 cursor-pointer">Aktif (tampil di website)</label>
          </div>
        </div>
        <div class="flex gap-2 pt-2">
          <button type="submit" class="btn-primary">Simpan</button>
          <a href="/admin/testimoni" class="btn-secondary">Batal</a>
        </div>
      </form>
    </div>
    <?php
} else {
    $list = $pdo->query("SELECT * FROM testimonials ORDER BY is_active DESC,sort_order ASC")->fetchAll();
    ?>
    <div class="flex justify-between items-center mb-5">
      <p class="text-sm text-gray-500"><?= count($list) ?> testimoni</p>
      <a href="/admin/testimoni?action=tambah" class="btn-primary">+ Tambah Testimoni</a>
    </div>
    <div class="bg-white rounded-xl border border-rose-100 overflow-hidden">
      <table class="admin-table w-full">
        <thead><tr><th>Nama</th><th>Kota</th><th>Rating</th><th>Isi</th><th style="text-align:center">Status</th><th style="text-align:center">Aksi</th></tr></thead>
        <tbody>
        <?php foreach ($list as $t): ?>
        <tr>
          <td class="font-medium text-gray-800"><?= clean($t['customer_name']) ?></td>
          <td class="text-gray-500 text-xs"><?= clean($t['customer_city']??'-') ?></td>
          <td><span style="color:#d97706"><?= str_repeat('★',$t['rating']) ?></span></td>
          <td class="text-gray-600 text-xs max-w-xs"><?= clean(substr($t['content'],0,80)) ?>...</td>
          <td style="text-align:center"><a href="/admin/testimoni?toggle=1&id=<?= $t['id'] ?>"><span class="<?= $t['is_active']?'badge-active':'badge-inactive' ?>"><?= $t['is_active']?'Aktif':'Nonaktif' ?></span></a></td>
          <td style="text-align:center">
            <div style="display:flex;gap:4px;justify-content:center">
              <a href="/admin/testimoni?action=edit&id=<?= $t['id'] ?>" style="font-size:.75rem;background:#fef3c7;color:#b45309;padding:.25rem .75rem;border-radius:.5rem;border:1px solid #fde68a">Edit</a>
              <a href="/admin/testimoni?hapus=1&id=<?= $t['id'] ?>" onclick="return confirm('Hapus?')" class="btn-danger">Hapus</a>
            </div>
          </td>
        </tr>
        <?php endforeach; ?>
        </tbody>
      </table>
    </div>
<?php } ?>
<?php require_once __DIR__ . '/../includes/admin_footer.php'; ?>
