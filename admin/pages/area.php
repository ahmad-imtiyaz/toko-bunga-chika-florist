<?php
$admin_title = 'Manajemen Area/Kecamatan';
require_once __DIR__ . '/../includes/admin_header.php';
$pdo     = getDB();
$action  = $_GET['action'] ?? 'list';
$id      = (int)($_GET['id'] ?? 0);
$city_id = (int)($_GET['city_id'] ?? 0);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = [
        'city_id'     => (int)$_POST['city_id'],
        'name'        => clean($_POST['name'] ?? ''),
        'slug'        => makeSlug($_POST['name'] ?? ''),
        'description' => clean($_POST['description'] ?? ''),
        'landmarks'   => $_POST['landmarks'] ?? '',
        'meta_title'  => clean($_POST['meta_title'] ?? ''),
        'meta_desc'   => clean($_POST['meta_desc'] ?? ''),
        'sort_order'  => (int)($_POST['sort_order'] ?? 0),
        'is_active'   => isset($_POST['is_active']) ? 1 : 0,
    ];
    if ($_POST['action_type'] === 'tambah') {
        $cols = implode(',',array_keys($data)); $vals = ':'.implode(',:',array_keys($data));
        $pdo->prepare("INSERT INTO areas ($cols) VALUES ($vals)")->execute($data);
        $_SESSION['success'] = 'Area berhasil ditambahkan.';
    } elseif ($_POST['action_type'] === 'edit' && $id) {
        unset($data['slug']);
        $sets = implode(',',array_map(fn($k)=>"$k=:$k",array_keys($data)));
        $data['id'] = $id;
        $pdo->prepare("UPDATE areas SET $sets WHERE id=:id")->execute($data);
        $_SESSION['success'] = 'Area berhasil diperbarui.';
    }
    redirect('/admin/area'.($city_id?"?city_id=$city_id":''));
}
if (isset($_GET['toggle']) && $id) {
    $cur = $pdo->prepare("SELECT is_active FROM areas WHERE id=?"); $cur->execute([$id]);
    $pdo->prepare("UPDATE areas SET is_active=? WHERE id=?")->execute([$cur->fetchColumn()?0:1,$id]);
    redirect('/admin/area'.($city_id?"?city_id=$city_id":''));
}
if (isset($_GET['hapus']) && $id) {
    $pdo->prepare("DELETE FROM areas WHERE id=?")->execute([$id]);
    $_SESSION['success'] = 'Area dihapus.';
    redirect('/admin/area'.($city_id?"?city_id=$city_id":''));
}

$allCities = $pdo->query("SELECT id,name FROM cities WHERE is_active=1 ORDER BY name ASC")->fetchAll();

if ($action === 'tambah' || ($action === 'edit' && $id)) {
    $item = ['city_id'=>$city_id];
    if ($action === 'edit') { $s=$pdo->prepare("SELECT * FROM areas WHERE id=?"); $s->execute([$id]); $item=$s->fetch(); }
    ?>
    <div class="max-w-2xl">
      <div class="flex items-center gap-3 mb-5">
        <a href="/admin/area<?= $city_id?"?city_id=$city_id":'' ?>" class="text-gray-400 hover:text-rose-600">← Kembali</a>
        <h2 class="font-display font-bold text-gray-800"><?= $action==='tambah'?'Tambah':'Edit' ?> Area</h2>
      </div>
      <form method="POST" class="bg-white rounded-xl border border-rose-100 p-6 space-y-4">
        <input type="hidden" name="action_type" value="<?= $action ?>">
        <div class="grid grid-cols-2 gap-4">
          <div><label class="form-label">Nama Area/Kecamatan *</label><input type="text" name="name" required class="form-input" value="<?= clean($item['name']??'') ?>"></div>
          <div>
            <label class="form-label">Kota Induk *</label>
            <select name="city_id" required class="form-input">
              <option value="">-- Pilih Kota --</option>
              <?php foreach ($allCities as $c): ?>
              <option value="<?= $c['id'] ?>" <?= ($item['city_id']??0)==$c['id']?'selected':'' ?>><?= clean($c['name']) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="col-span-2"><label class="form-label">Deskripsi</label><textarea name="description" class="form-input" rows="2"><?= clean($item['description']??'') ?></textarea></div>
          <div class="col-span-2">
            <label class="form-label">Landmark Area (SEO Hyper-Local)</label>
            <textarea name="landmarks" class="form-input" rows="4" placeholder="Jl. Raya Tebet, RSCM, Mal Tebet, Perumahan..."><?= clean($item['landmarks']??'') ?></textarea>
            <p class="text-xs text-gray-400 mt-1">Tulis nama jalan, RS, mall, kampus, perumahan untuk meningkatkan SEO lokal.</p>
          </div>
          <div class="col-span-2"><label class="form-label">Meta Title</label><input type="text" name="meta_title" class="form-input" value="<?= clean($item['meta_title']??'') ?>"></div>
          <div class="col-span-2"><label class="form-label">Meta Description</label><textarea name="meta_desc" class="form-input" rows="2"><?= clean($item['meta_desc']??'') ?></textarea></div>
          <div><label class="form-label">Sort Order</label><input type="number" name="sort_order" class="form-input" value="<?= $item['sort_order']??0 ?>"></div>
          <div class="flex items-center gap-2 self-end pb-1">
            <input type="checkbox" name="is_active" id="is_active_a" <?= ($item['is_active']??1)?'checked':'' ?>>
            <label for="is_active_a" class="text-sm text-gray-700 cursor-pointer">Aktif</label>
          </div>
        </div>
        <div class="flex gap-2 pt-2">
          <button type="submit" class="btn-primary">Simpan Area</button>
          <a href="/admin/area<?= $city_id?"?city_id=$city_id":'' ?>" class="btn-secondary">Batal</a>
        </div>
      </form>
    </div>
    <?php
} else {
    $where = ''; $params = [];
    $filterCity = null;
    if ($city_id) {
        $where = 'WHERE a.city_id=?'; $params = [$city_id];
        $fc = $pdo->prepare("SELECT name FROM cities WHERE id=?"); $fc->execute([$city_id]); $filterCity = $fc->fetchColumn();
    }
    $stmt = $pdo->prepare("SELECT a.*,c.name as city_name FROM areas a JOIN cities c ON a.city_id=c.id $where ORDER BY c.name ASC,a.sort_order ASC,a.name ASC");
    $stmt->execute($params);
    $areas = $stmt->fetchAll();
    ?>
    <div class="flex justify-between items-center mb-5 flex-wrap gap-3">
      <div class="flex items-center gap-3">
        <p class="text-sm text-gray-500"><?= count($areas) ?> area<?= $filterCity?" di $filterCity":'' ?></p>
        <?php if ($city_id): ?><a href="/admin/area" class="text-xs text-rose-600 hover:underline">Lihat semua</a><?php endif; ?>
      </div>
      <div class="flex gap-2">
        <select onchange="window.location='/admin/area?city_id='+this.value" class="text-sm border border-rose-200 rounded-lg px-3 py-1.5 outline-none focus:border-rose-400">
          <option value="">Filter Kota</option>
          <?php foreach ($allCities as $c): ?>
          <option value="<?= $c['id'] ?>" <?= $city_id==$c['id']?'selected':'' ?>><?= clean($c['name']) ?></option>
          <?php endforeach; ?>
        </select>
        <a href="/admin/area?action=tambah<?= $city_id?"&city_id=$city_id":'' ?>" class="btn-primary">+ Tambah Area</a>
      </div>
    </div>
    <div class="bg-white rounded-xl border border-rose-100 overflow-hidden">
      <table class="admin-table w-full">
        <thead><tr><th>Nama Area</th><th>Kota</th><th>URL Slug</th><th style="text-align:center">Status</th><th style="text-align:center">Aksi</th></tr></thead>
        <tbody>
        <?php foreach ($areas as $a): ?>
        <tr>
          <td class="font-medium text-gray-800"><?= clean($a['name']) ?> <a href="/toko-bunga-<?= $a['slug'] ?>" target="_blank" class="text-xs text-rose-400 hover:underline ml-1">↗</a></td>
          <td class="text-gray-500 text-xs"><?= clean($a['city_name']) ?></td>
          <td class="text-gray-400 text-xs font-mono">/toko-bunga-<?= $a['slug'] ?></td>
          <td style="text-align:center"><a href="/admin/area?toggle=1&id=<?= $a['id'] ?><?= $city_id?"&city_id=$city_id":'' ?>"><span class="<?= $a['is_active']?'badge-active':'badge-inactive' ?>"><?= $a['is_active']?'Aktif':'Nonaktif' ?></span></a></td>
          <td style="text-align:center">
            <div style="display:flex;gap:4px;justify-content:center">
              <a href="/admin/area?action=edit&id=<?= $a['id'] ?>" style="font-size:.75rem;background:#fef3c7;color:#b45309;padding:.25rem .75rem;border-radius:.5rem;border:1px solid #fde68a">Edit</a>
              <a href="/admin/area?hapus=1&id=<?= $a['id'] ?><?= $city_id?"&city_id=$city_id":'' ?>" onclick="return confirm('Hapus area ini?')" class="btn-danger">Hapus</a>
            </div>
          </td>
        </tr>
        <?php endforeach; ?>
        <?php if (empty($areas)): ?>
        <tr><td colspan="5" style="text-align:center;padding:2rem;color:#9ca3af">Belum ada area. <a href="/admin/area?action=tambah" style="color:#e11d48">Tambah sekarang</a></td></tr>
        <?php endif; ?>
        </tbody>
      </table>
    </div>
<?php } ?>
<?php require_once __DIR__ . '/../includes/admin_footer.php'; ?>
