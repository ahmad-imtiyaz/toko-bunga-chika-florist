<?php
$admin_title = 'Manajemen Kota';
require_once __DIR__ . '/../includes/admin_header.php';
$pdo    = getDB();
$action = $_GET['action'] ?? 'list';
$id     = (int)($_GET['id'] ?? 0);

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
        'sort_order'     => (int)($_POST['sort_order'] ?? 0),
        'is_active'      => isset($_POST['is_active']) ? 1 : 0,
    ];
    if ($_POST['action_type'] === 'tambah') {
        $cols = implode(',',array_keys($data)); $vals = ':'.implode(',:',array_keys($data));
        $pdo->prepare("INSERT INTO cities ($cols) VALUES ($vals)")->execute($data);
        $_SESSION['success'] = 'Kota berhasil ditambahkan.';
    } elseif ($_POST['action_type'] === 'edit' && $id) {
        unset($data['slug']);
        $sets = implode(',',array_map(fn($k)=>"$k=:$k",array_keys($data)));
        $data['id'] = $id;
        $pdo->prepare("UPDATE cities SET $sets WHERE id=:id")->execute($data);
        $_SESSION['success'] = 'Kota berhasil diperbarui.';
    }
    redirect('/admin/kota');
}
if (isset($_GET['toggle']) && $id) {
    $cur = $pdo->prepare("SELECT is_active FROM cities WHERE id=?"); $cur->execute([$id]);
    $pdo->prepare("UPDATE cities SET is_active=? WHERE id=?")->execute([$cur->fetchColumn()?0:1,$id]);
    redirect('/admin/kota');
}
if (isset($_GET['hapus']) && $id) {
    $pdo->prepare("DELETE FROM cities WHERE id=?")->execute([$id]);
    $_SESSION['success'] = 'Kota berhasil dihapus.'; redirect('/admin/kota');
}

if ($action === 'tambah' || ($action === 'edit' && $id)) {
    $item = [];
    if ($action === 'edit') { $s=$pdo->prepare("SELECT * FROM cities WHERE id=?"); $s->execute([$id]); $item=$s->fetch(); }
    ?>
    <div class="max-w-2xl">
      <div class="flex items-center gap-3 mb-5">
        <a href="/admin/kota" class="text-gray-400 hover:text-rose-600">← Kembali</a>
        <h2 class="font-display font-bold text-gray-800"><?= $action==='tambah'?'Tambah':'Edit' ?> Kota</h2>
      </div>
      <form method="POST" class="bg-white rounded-xl border border-rose-100 p-6 space-y-4">
        <input type="hidden" name="action_type" value="<?= $action ?>">
        <div class="grid grid-cols-2 gap-4">
          <div><label class="form-label">Nama Kota *</label><input type="text" name="name" required class="form-input" value="<?= clean($item['name']??'') ?>"></div>
          <div><label class="form-label">Provinsi</label><input type="text" name="province" class="form-input" value="<?= clean($item['province']??'') ?>"></div>
          <div>
            <label class="form-label">Tier Prioritas</label>
            <select name="tier" class="form-input">
              <option value="1" <?= ($item['tier']??1)==1?'selected':'' ?>>Tier 1 (Kota Besar)</option>
              <option value="2" <?= ($item['tier']??1)==2?'selected':'' ?>>Tier 2</option>
              <option value="3" <?= ($item['tier']??1)==3?'selected':'' ?>>Tier 3</option>
            </select>
          </div>
          <div><label class="form-label">Sort Order</label><input type="number" name="sort_order" class="form-input" value="<?= $item['sort_order']??0 ?>"></div>
          <div class="col-span-2"><label class="form-label">Deskripsi</label><textarea name="description" class="form-input" rows="2"><?= clean($item['description']??'') ?></textarea></div>
          <div class="col-span-2">
            <label class="form-label">Landmark Kota (untuk SEO – nama jalan, RS, mall, kampus)</label>
            <textarea name="landmark_notes" class="form-input" rows="3" placeholder="Jl. Sudirman, RS Siloam, Grand Indonesia, UI..."><?= clean($item['landmark_notes']??'') ?></textarea>
          </div>
          <div class="col-span-2"><label class="form-label">Meta Title</label><input type="text" name="meta_title" class="form-input" value="<?= clean($item['meta_title']??'') ?>"></div>
          <div class="col-span-2"><label class="form-label">Meta Description</label><textarea name="meta_desc" class="form-input" rows="2"><?= clean($item['meta_desc']??'') ?></textarea></div>
          <div class="flex items-center gap-2">
            <input type="checkbox" name="is_active" id="is_active_c" <?= ($item['is_active']??1)?'checked':'' ?>>
            <label for="is_active_c" class="text-sm text-gray-700 cursor-pointer">Aktif (tampil di website)</label>
          </div>
        </div>
        <div class="flex gap-2 pt-2">
          <button type="submit" class="btn-primary">Simpan Kota</button>
          <a href="/admin/kota" class="btn-secondary">Batal</a>
        </div>
      </form>
    </div>
    <?php
} else {
    $cities = $pdo->query("SELECT c.*,(SELECT COUNT(*) FROM areas WHERE city_id=c.id) as area_count FROM cities c ORDER BY c.tier ASC,c.sort_order ASC,c.name ASC")->fetchAll();
    ?>
    <div class="flex justify-between items-center mb-5">
      <p class="text-sm text-gray-500"><?= count($cities) ?> kota</p>
      <a href="/admin/kota?action=tambah" class="btn-primary">+ Tambah Kota</a>
    </div>
    <div class="bg-white rounded-xl border border-rose-100 overflow-hidden">
      <table class="admin-table w-full">
        <thead><tr><th>Nama Kota</th><th>Provinsi</th><th style="text-align:center">Tier</th><th style="text-align:center">Area</th><th style="text-align:center">Status</th><th style="text-align:center">Aksi</th></tr></thead>
        <tbody>
        <?php foreach ($cities as $c): ?>
        <tr>
          <td class="font-medium text-gray-800"><?= clean($c['name']) ?> <a href="/toko-bunga-<?= $c['slug'] ?>" target="_blank" class="text-xs text-rose-400 hover:underline ml-1">↗</a></td>
          <td class="text-gray-500 text-xs"><?= clean($c['province']??'-') ?></td>
          <td style="text-align:center"><span style="font-size:.7rem;background:#fef3c7;color:#b45309;padding:.15rem .5rem;border-radius:9999px;border:1px solid #fde68a">T<?= $c['tier'] ?></span></td>
          <td style="text-align:center"><a href="/admin/area?city_id=<?= $c['id'] ?>" style="color:#2563eb;font-size:.8rem"><?= $c['area_count'] ?> area</a></td>
          <td style="text-align:center"><a href="/admin/kota?toggle=1&id=<?= $c['id'] ?>"><span class="<?= $c['is_active']?'badge-active':'badge-inactive' ?>"><?= $c['is_active']?'Aktif':'Nonaktif' ?></span></a></td>
          <td style="text-align:center">
            <div style="display:flex;gap:4px;justify-content:center">
              <a href="/admin/kota?action=edit&id=<?= $c['id'] ?>" style="font-size:.75rem;background:#fef3c7;color:#b45309;padding:.25rem .75rem;border-radius:.5rem;border:1px solid #fde68a">Edit</a>
              <a href="/admin/area?action=tambah&city_id=<?= $c['id'] ?>" style="font-size:.75rem;background:#eff6ff;color:#2563eb;padding:.25rem .75rem;border-radius:.5rem;border:1px solid #bfdbfe">+ Area</a>
              <a href="/admin/kota?hapus=1&id=<?= $c['id'] ?>" onclick="return confirm('Hapus kota dan semua areanya?')" class="btn-danger">Hapus</a>
            </div>
          </td>
        </tr>
        <?php endforeach; ?>
        </tbody>
      </table>
    </div>
<?php } ?>
<?php require_once __DIR__ . '/../includes/admin_footer.php'; ?>
