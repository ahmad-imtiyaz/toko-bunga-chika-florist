<?php
$admin_title = 'Manajemen FAQ';
require_once __DIR__ . '/../includes/admin_header.php';
$pdo    = getDB();
$action = $_GET['action'] ?? 'list';
$id     = (int)($_GET['id'] ?? 0);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = [
        'question'   => clean($_POST['question'] ?? ''),
        'answer'     => clean($_POST['answer'] ?? ''),
        'category'   => clean($_POST['category'] ?? 'Umum'),
        'sort_order' => (int)($_POST['sort_order'] ?? 0),
        'is_active'  => isset($_POST['is_active']) ? 1 : 0,
    ];
    if ($_POST['action_type'] === 'tambah') {
        $cols = implode(',',array_keys($data)); $vals = ':'.implode(',:',array_keys($data));
        $pdo->prepare("INSERT INTO faqs ($cols) VALUES ($vals)")->execute($data);
        $_SESSION['success'] = 'FAQ ditambahkan.';
    } elseif ($_POST['action_type'] === 'edit' && $id) {
        $sets = implode(',',array_map(fn($k)=>"$k=:$k",array_keys($data)));
        $data['id'] = $id;
        $pdo->prepare("UPDATE faqs SET $sets WHERE id=:id")->execute($data);
        $_SESSION['success'] = 'FAQ diperbarui.';
    }
    redirect('/admin/faq');
}
if (isset($_GET['toggle']) && $id) {
    $cur = $pdo->prepare("SELECT is_active FROM faqs WHERE id=?"); $cur->execute([$id]);
    $pdo->prepare("UPDATE faqs SET is_active=? WHERE id=?")->execute([$cur->fetchColumn()?0:1,$id]);
    redirect('/admin/faq');
}
if (isset($_GET['hapus']) && $id) {
    $pdo->prepare("DELETE FROM faqs WHERE id=?")->execute([$id]);
    $_SESSION['success'] = 'FAQ dihapus.'; redirect('/admin/faq');
}

if ($action === 'tambah' || ($action === 'edit' && $id)) {
    $item = [];
    if ($action === 'edit') { $s=$pdo->prepare("SELECT * FROM faqs WHERE id=?"); $s->execute([$id]); $item=$s->fetch(); }
    ?>
    <div class="max-w-xl">
      <div class="flex items-center gap-3 mb-5">
        <a href="/admin/faq" class="text-gray-400 hover:text-rose-600">← Kembali</a>
        <h2 class="font-display font-bold text-gray-800"><?= $action==='tambah'?'Tambah':'Edit' ?> FAQ</h2>
      </div>
      <form method="POST" class="bg-white rounded-xl border border-rose-100 p-6 space-y-4">
        <input type="hidden" name="action_type" value="<?= $action ?>">
        <div><label class="form-label">Pertanyaan *</label><textarea name="question" required class="form-input" rows="2"><?= clean($item['question']??'') ?></textarea></div>
        <div><label class="form-label">Jawaban *</label><textarea name="answer" required class="form-input" rows="4"><?= clean($item['answer']??'') ?></textarea></div>
        <div class="grid grid-cols-2 gap-4">
          <div><label class="form-label">Kategori</label><input type="text" name="category" class="form-input" value="<?= clean($item['category']??'Umum') ?>"></div>
          <div><label class="form-label">Sort Order</label><input type="number" name="sort_order" class="form-input" value="<?= $item['sort_order']??0 ?>"></div>
          <div class="flex items-center gap-2">
            <input type="checkbox" name="is_active" id="is_active_f" <?= ($item['is_active']??1)?'checked':'' ?>>
            <label for="is_active_f" class="text-sm text-gray-700 cursor-pointer">Aktif</label>
          </div>
        </div>
        <div class="flex gap-2 pt-2">
          <button type="submit" class="btn-primary">Simpan</button>
          <a href="/admin/faq" class="btn-secondary">Batal</a>
        </div>
      </form>
    </div>
    <?php
} else {
    $list = $pdo->query("SELECT * FROM faqs ORDER BY sort_order ASC")->fetchAll();
    ?>
    <div class="flex justify-between items-center mb-5">
      <p class="text-sm text-gray-500"><?= count($list) ?> FAQ</p>
      <a href="/admin/faq?action=tambah" class="btn-primary">+ Tambah FAQ</a>
    </div>
    <div class="bg-white rounded-xl border border-rose-100 overflow-hidden">
      <table class="admin-table w-full">
        <thead><tr><th>Pertanyaan</th><th>Kategori</th><th style="text-align:center">Status</th><th style="text-align:center">Aksi</th></tr></thead>
        <tbody>
        <?php foreach ($list as $f): ?>
        <tr>
          <td class="font-medium text-gray-800 text-sm max-w-sm"><?= clean(substr($f['question'],0,100)) ?></td>
          <td class="text-gray-500 text-xs"><?= clean($f['category']) ?></td>
          <td style="text-align:center"><a href="/admin/faq?toggle=1&id=<?= $f['id'] ?>"><span class="<?= $f['is_active']?'badge-active':'badge-inactive' ?>"><?= $f['is_active']?'Aktif':'Nonaktif' ?></span></a></td>
          <td style="text-align:center">
            <div style="display:flex;gap:4px;justify-content:center">
              <a href="/admin/faq?action=edit&id=<?= $f['id'] ?>" style="font-size:.75rem;background:#fef3c7;color:#b45309;padding:.25rem .75rem;border-radius:.5rem;border:1px solid #fde68a">Edit</a>
              <a href="/admin/faq?hapus=1&id=<?= $f['id'] ?>" onclick="return confirm('Hapus FAQ?')" class="btn-danger">Hapus</a>
            </div>
          </td>
        </tr>
        <?php endforeach; ?>
        </tbody>
      </table>
    </div>
<?php } ?>
<?php require_once __DIR__ . '/../includes/admin_footer.php'; ?>
