<?php
// ============================================================
// LOGIKA CRUD — SEBELUM ADMIN HEADER
// ============================================================
require_once __DIR__ . '/../../includes/config.php';
requireAdminLogin();

$pdo = getDB();
$b = BASE_URL;
$action = $_GET['action'] ?? 'list';
$id = (int) ($_GET['id'] ?? 0);

// ── Handle TinyMCE image upload ───────────────────────────
if ($action === 'upload' && $_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['file'])) {
    header('Content-Type: application/json');
    $file = $_FILES['file'];
    $allowed = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
    if (!in_array($file['type'], $allowed)) {
        echo json_encode(['error' => 'Tipe file tidak diizinkan']);
        exit();
    }
    $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
    $filename = 'blog_img_' . uniqid() . '.' . $ext;
    if (move_uploaded_file($file['tmp_name'], UPLOAD_DIR . $filename)) {
        echo json_encode(['location' => UPLOAD_URL . $filename]);
    } else {
        echo json_encode(['error' => 'Gagal upload. Cek permission folder uploads/']);
    }
    exit();
}

// ── Helpers ───────────────────────────────────────────────
function makeBlogSlug(string $text): string
{
    $text = strtolower(trim($text));
    $text = preg_replace('/[^a-z0-9\s-]/', '', $text);
    $text = preg_replace('/[\s-]+/', '-', $text);
    return trim($text, '-');
}
function handleThumbUpload(string $field): string
{
    if (empty($_FILES[$field]['tmp_name'])) {
        return '';
    }
    $file = $_FILES[$field];
    $allowed = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
    if (!in_array($file['type'], $allowed)) {
        return '';
    }
    $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
    $filename = 'blog_' . uniqid() . '.' . $ext;
    if (move_uploaded_file($file['tmp_name'], UPLOAD_DIR . $filename)) {
        return $filename;
    }
    return '';
}

// ── AJAX: simpan urutan drag-and-drop ────────────────────
if (isset($_POST['reorder']) && !empty($_POST['ids'])) {
    $ids = array_filter(array_map('intval', explode(',', $_POST['ids'])));
    $stmt = $pdo->prepare('UPDATE blogs SET urutan=? WHERE id=?');
    foreach (array_values($ids) as $i => $bid) {
        $stmt->execute([$i + 1, $bid]);
    }
    echo json_encode(['ok' => true]);
    exit();
}

// ── POST: Tambah / Edit / Hapus ───────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action_type = $_POST['action_type'] ?? '';

    if (in_array($action_type, ['tambah', 'edit'])) {
        $title = clean($_POST['title'] ?? '');
        $slug = makeBlogSlug(trim($_POST['slug'] ?? '') ?: $title);
        $cat_id_raw = (int) ($_POST['blog_category_id'] ?? 0);
        if (!$cat_id_raw) {
            $first_cat = $pdo->query("SELECT id FROM blog_categories WHERE status='active' ORDER BY urutan ASC LIMIT 1")->fetchColumn();
            $cat_id_raw = (int) ($first_cat ?: 0);
        }
        $cat_id = $cat_id_raw ?: null;
        $excerpt = clean($_POST['excerpt'] ?? '');
        $content = $_POST['content'] ?? '';
        $meta_title = clean($_POST['meta_title'] ?? '');
        $meta_desc = clean($_POST['meta_desc'] ?? '');
        $meta_kw = clean($_POST['meta_keywords'] ?? '');
        $status = in_array($_POST['status'] ?? '', ['active', 'inactive', 'draft']) ? $_POST['status'] : 'active';
        $new_thumb = handleThumbUpload('thumbnail');

        if (!$title || !$slug) {
            $_SESSION['error'] = 'Judul dan slug wajib diisi.';
            header('Location: ' . $b . '/admin/blog?action=' . ($action_type === 'edit' ? 'edit&id=' . $id : 'tambah'));
            exit();
        }

        try {
            if ($action_type === 'tambah') {
                // Auto urutan = MAX + 1
                $maxUrutan = (int) $pdo->query('SELECT COALESCE(MAX(urutan),0) FROM blogs')->fetchColumn();
                $urutan = $maxUrutan + 1;
                $pdo->prepare('INSERT INTO blogs (blog_category_id,title,slug,thumbnail,excerpt,content,meta_title,meta_desc,meta_keywords,status,urutan) VALUES (?,?,?,?,?,?,?,?,?,?,?)')->execute([$cat_id, $title, $slug, $new_thumb, $excerpt, $content, $meta_title, $meta_desc, $meta_kw, $status, $urutan]);
                $_SESSION['success'] = 'Artikel berhasil dipublikasikan.';
            } else {
                $thumb_val = $new_thumb ?: clean($_POST['old_thumbnail'] ?? '');
                $pdo->prepare('UPDATE blogs SET blog_category_id=?,title=?,slug=?,thumbnail=?,excerpt=?,content=?,meta_title=?,meta_desc=?,meta_keywords=?,status=?,urutan=? WHERE id=?')->execute([$cat_id, $title, $slug, $thumb_val, $excerpt, $content, $meta_title, $meta_desc, $meta_kw, $status, $urutan, $id]);
                $_SESSION['success'] = 'Artikel berhasil diperbarui.';
            }
        } catch (PDOException $e) {
            $_SESSION['error'] = 'Gagal menyimpan: ' . $e->getMessage();
        }
        header('Location: ' . $b . '/admin/blog');
        exit();
    }

    if (($_POST['action_type'] ?? '') === 'hapus') {
        $del_id = (int) ($_POST['del_id'] ?? 0);
        if ($del_id) {
            $pdo->prepare('DELETE FROM blogs WHERE id=?')->execute([$del_id]);
        }
        $_SESSION['success'] = 'Artikel berhasil dihapus.';
        header('Location: ' . $b . '/admin/blog');
        exit();
    }
}

// ── Fetch kategori untuk dropdown ─────────────────────────
$blog_cats = $pdo->query("SELECT id, name FROM blog_categories WHERE status='active' ORDER BY urutan ASC")->fetchAll();

// ── Edit mode: fetch artikel ──────────────────────────────
$edit_blog = null;
if ($action === 'edit' && $id) {
    $s = $pdo->prepare('SELECT * FROM blogs WHERE id=?');
    $s->execute([$id]);
    $edit_blog = $s->fetch();
    if (!$edit_blog) {
        $action = 'list';
    }
}

// ── List: filter & search ─────────────────────────────────
$search_q = trim($_GET['q'] ?? '');
$filter_cat = (int) ($_GET['cat'] ?? 0);
$filter_status = trim($_GET['status'] ?? '');

$where = ['1=1'];
$params = [];
if ($search_q) {
    $where[] = 'b.title LIKE ?';
    $params[] = "%$search_q%";
}
if ($filter_cat) {
    $where[] = 'b.blog_category_id = ?';
    $params[] = $filter_cat;
}
if ($filter_status) {
    $where[] = 'b.status = ?';
    $params[] = $filter_status;
}
$stmt = $pdo->prepare(
    "
    SELECT b.*, bc.name AS cat_name
    FROM blogs b
    LEFT JOIN blog_categories bc ON b.blog_category_id = bc.id
    WHERE " .
        implode(' AND ', $where) .
        "
    ORDER BY b.urutan ASC, b.created_at DESC
",
);
$stmt->execute($params);
$blogs_list = $stmt->fetchAll();

$total = count($blogs_list);
$totalAktif = count(array_filter($blogs_list, fn($x) => $x['status'] === 'active'));
$totalDraft = count(array_filter($blogs_list, fn($x) => $x['status'] === 'draft'));

// ============================================================
$admin_title = 'Manajemen Blog';
require_once __DIR__ . '/../includes/admin_header.php';
?>

<?php if ($action === 'tambah' || $action === 'edit'): ?>
<!-- ══════════════════════════════════════════════════════════
     FORM TAMBAH / EDIT
══════════════════════════════════════════════════════════ -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/tinymce/6.8.4/tinymce.min.js" referrerpolicy="origin"></script>
<script>
    tinymce.init({
        selector: '#blog-content',
        height: 520,
        relative_urls: false,
        remove_script_host: false,
        convert_urls: false,
        menubar: true,
        plugins: ['advlist', 'autolink', 'lists', 'link', 'image', 'charmap', 'preview', 'anchor',
            'searchreplace', 'visualblocks', 'code', 'fullscreen', 'insertdatetime', 'media', 'table',
            'help', 'wordcount', 'emoticons', 'codesample'
        ],
        toolbar: 'undo redo | styles | bold italic underline | alignleft aligncenter alignright | bullist numlist | link image media | forecolor backcolor | blockquote codesample | removeformat | fullscreen code',
        style_formats: [{
                title: 'Heading 1',
                format: 'h1'
            }, {
                title: 'Heading 2',
                format: 'h2'
            },
            {
                title: 'Heading 3',
                format: 'h3'
            }, {
                title: 'Paragraph',
                format: 'p'
            },
            {
                title: 'Blockquote',
                format: 'blockquote'
            }, {
                title: 'Code',
                format: 'pre'
            },
        ],
        content_style: `
        body{font-family:'Lato',sans-serif;font-size:15px;color:#374151;padding:16px 20px;line-height:1.8;}
        h1,h2,h3,h4{font-family:'Playfair Display',Georgia,serif;color:#1f2937;}
        h1{font-size:1.875rem;font-weight:700;margin:1.5rem 0 0.75rem;}
        h2{font-size:1.5rem;font-weight:700;margin:1.5rem 0 0.75rem;border-bottom:2px solid #fef2f2;padding-bottom:.5rem;}
        h3{font-size:1.2rem;font-weight:600;margin:1.25rem 0 .5rem;}
        p{margin:.75rem 0;}
        a{color:#e11d48;}
        img{max-width:100%;height:auto;border-radius:8px;}
        blockquote{border-left:4px solid #e11d48;background:#fff5f5;padding:1rem 1.25rem;margin:1.25rem 0;border-radius:0 8px 8px 0;font-style:italic;}
        table{border-collapse:collapse;width:100%;}
        th{background:#e11d48;color:white;padding:8px 12px;text-align:left;}
        td{border:1px solid #fecdd3;padding:8px 12px;}
    `,
        images_upload_url: '<?= $b ?>/admin/blog?action=upload',
        images_upload_handler: function(blobInfo) {
            return new Promise(function(resolve, reject) {
                const fd = new FormData();
                fd.append('file', blobInfo.blob(), blobInfo.filename());
                fetch('<?= $b ?>/admin/blog?action=upload', {
                        method: 'POST',
                        body: fd
                    })
                    .then(r => r.json())
                    .then(data => {
                        if (data.location) resolve(data.location);
                        else reject({
                            message: data.error || 'Upload gagal',
                            remove: true
                        });
                    })
                    .catch(() => reject({
                        message: 'Network error',
                        remove: true
                    }));
            });
        },
        automatic_uploads: true,
        image_advtab: true,
        branding: false,
        promotion: false,
    });
</script>

<div class="flex items-center gap-3 mb-5">
    <a href="<?= $b ?>/admin/blog" class="text-gray-400 hover:text-rose-600">← Kembali</a>
    <h2 class="font-display font-bold text-gray-800">
        <?= $action === 'edit' ? 'Edit Artikel' : 'Tulis Artikel Baru' ?>
    </h2>
    <?php if ($action === 'edit' && $edit_blog): ?>
    <span class="text-xs text-gray-400 font-mono">/blog/<?= clean($edit_blog['slug']) ?></span>
    <?php endif; ?>
</div>

<form method="POST" enctype="multipart/form-data" id="blog-form">
    <input type="hidden" name="action_type" value="<?= $action === 'edit' ? 'edit' : 'tambah' ?>">
    <?php if ($edit_blog): ?>
    <input type="hidden" name="id" value="<?= $edit_blog['id'] ?>">
    <input type="hidden" name="old_thumbnail" value="<?= clean($edit_blog['thumbnail'] ?? '') ?>">
    <?php endif; ?>

    <div class="grid lg:grid-cols-4 gap-5 items-start">

        <!-- ── Kolom kiri 3/4 ── -->
        <div class="lg:col-span-3 space-y-4">

            <!-- Judul -->
            <div class="bg-white rounded-xl border border-rose-100 p-5">
                <label class="form-label">Judul Artikel *</label>
                <input type="text" name="title" id="blog-title" required class="form-input text-base font-semibold"
                    placeholder="Tulis judul artikel di sini..." value="<?= clean($edit_blog['title'] ?? '') ?>"
                    oninput="autoSlugBlog(this.value)">
            </div>

            <!-- Konten TinyMCE -->
            <div class="bg-white rounded-xl border border-rose-100 overflow-hidden">
                <div class="px-5 py-3 border-b border-rose-50 flex items-center gap-2">
                    <span class="text-sm font-semibold text-gray-700">📄 Konten Artikel</span>
                    <span class="text-xs text-gray-400">— Bisa tambah gambar, heading, tabel, dll</span>
                </div>
                <div class="p-1">
                    <textarea name="content" id="blog-content"><?= $edit_blog['content'] ?? '' ?></textarea>
                </div>
            </div>

            <!-- Excerpt -->
            <div class="bg-white rounded-xl border border-rose-100 p-5">
                <label class="form-label">Ringkasan / Excerpt</label>
                <textarea name="excerpt" class="form-input" rows="3"
                    placeholder="Ringkasan singkat artikel (tampil di halaman list blog)..."><?= clean($edit_blog['excerpt'] ?? '') ?></textarea>
                <p class="text-xs text-gray-400 mt-1.5">Kosongkan untuk otomatis dari 200 karakter pertama konten.</p>
            </div>

            <!-- SEO -->
            <div class="bg-white rounded-xl border border-rose-100 p-5">
                <h3 class="font-display font-bold text-gray-800 text-sm mb-4">🔍 SEO Meta</h3>
                <div class="grid grid-cols-2 gap-4">
                    <div class="col-span-2">
                        <label class="form-label">Meta Title <span class="text-gray-400 font-normal text-xs">(maks 70
                                karakter)</span></label>
                        <input type="text" name="meta_title" class="form-input" maxlength="70"
                            value="<?= clean($edit_blog['meta_title'] ?? '') ?>" placeholder="Judul untuk Google...">
                    </div>
                    <div class="col-span-2">
                        <label class="form-label">Meta Description <span class="text-gray-400 font-normal text-xs">(maks
                                160 karakter)</span></label>
                        <textarea name="meta_desc" class="form-input" rows="2" maxlength="160"
                            placeholder="Deskripsi untuk mesin pencari..."><?= clean($edit_blog['meta_desc'] ?? '') ?></textarea>
                    </div>
                    <div class="col-span-2">
                        <label class="form-label">Meta Keywords</label>
                        <input type="text" name="meta_keywords" class="form-input"
                            value="<?= clean($edit_blog['meta_keywords'] ?? '') ?>"
                            placeholder="tips bunga, rangkaian bunga, florist jakarta">
                    </div>
                </div>
            </div>
        </div>

        <!-- ── Kolom kanan 1/4 ── -->
        <div class="lg:col-span-1 space-y-4 lg:sticky lg:top-20">

            <!-- Publikasi -->
            <div class="bg-white rounded-xl border border-rose-100 p-5">
                <h3 class="font-display font-bold text-gray-800 text-sm mb-4">🚀 Publikasi</h3>
                <div class="space-y-3">
                    <div>
                        <label class="form-label">Status</label>
                        <select name="status" class="form-input">
                            <option value="active"
                                <?= ($edit_blog['status'] ?? 'active') === 'active' ? 'selected' : '' ?>>✅ Aktif /
                                Publish</option>
                            <option value="draft" <?= ($edit_blog['status'] ?? '') === 'draft' ? 'selected' : '' ?>>
                                📝 Draft</option>
                            <option value="inactive"
                                <?= ($edit_blog['status'] ?? '') === 'inactive' ? 'selected' : '' ?>>⏸ Nonaktif</option>
                        </select>
                    </div>
                    <button type="submit" class="btn-primary w-full">
                        <?= $action === 'edit' ? 'Simpan Perubahan' : 'Publikasikan' ?>
                    </button>
                </div>
            </div>

            <!-- Kategori -->
            <div class="bg-white rounded-xl border border-rose-100 p-5">
                <h3 class="font-display font-bold text-gray-800 text-sm mb-3">📂 Kategori Blog</h3>
                <select name="blog_category_id" class="form-input">
                    <?php foreach ($blog_cats as $i => $bc): ?>
                    <option value="<?= $bc['id'] ?>"
                        <?= ($edit_blog['blog_category_id'] ?? ($blog_cats[0]['id'] ?? '')) == $bc['id'] ? 'selected' : '' ?>>
                        <?= clean($bc['name']) ?>
                    </option>
                    <?php endforeach; ?>
                </select>
                <a href="<?= $b ?>/admin/blog-categories" target="_blank"
                    class="text-xs text-rose-400 hover:text-rose-600 mt-2 block">+ Tambah kategori baru ↗</a>
            </div>

            <!-- Thumbnail -->
            <div class="bg-white rounded-xl border border-rose-100 p-5">
                <h3 class="font-display font-bold text-gray-800 text-sm mb-3">🖼️ Thumbnail</h3>
                <?php if (!empty($edit_blog['thumbnail'])): ?>
                <div class="mb-3">
                    <img src="<?= UPLOAD_URL . clean($edit_blog['thumbnail']) ?>" alt=""
                        class="w-full rounded-lg object-cover border border-rose-100" style="max-height:140px">
                    <p class="text-xs text-gray-400 mt-1 text-center">Thumbnail saat ini</p>
                </div>
                <?php endif; ?>
                <input type="file" name="thumbnail" class="form-input text-sm" accept="image/*"
                    onchange="previewThumb(this)">
                <div id="thumb-preview" class="mt-2 hidden">
                    <img id="thumb-preview-img" src="" alt=""
                        class="w-full rounded-lg object-cover border border-rose-100" style="max-height:140px">
                </div>
                <p class="text-xs text-gray-400 mt-1.5">JPG, PNG, WebP. Rekomendasi 800×500px.</p>
            </div>

            <!-- Slug -->
            <div class="bg-white rounded-xl border border-rose-100 p-5">
                <h3 class="font-display font-bold text-gray-800 text-sm mb-3">🔗 Slug URL</h3>
                <input type="text" name="slug" id="blog-slug" class="form-input text-sm"
                    value="<?= clean($edit_blog['slug'] ?? '') ?>" placeholder="url-artikel"
                    oninput="document.getElementById('slug-preview').textContent=this.value">
                <p class="text-xs text-gray-400 mt-1.5 break-all">
                    /blog/<strong id="slug-preview"
                        class="text-rose-500"><?= clean($edit_blog['slug'] ?? '') ?></strong>
                </p>
            </div>

        </div>
    </div>
</form>

<script>
    function autoSlugBlog(val) {
        if (document.getElementById('blog-slug').dataset.manual === '1') return;
        const s = val.toLowerCase()
            .replace(/[^a-z0-9\s-]/g, '')
            .replace(/[\s-]+/g, '-')
            .replace(/^-+|-+$/g, '');
        document.getElementById('blog-slug').value = s;
        document.getElementById('slug-preview').textContent = s;
    }
    document.getElementById('blog-slug').addEventListener('input', function() {
        this.dataset.manual = '1';
        document.getElementById('slug-preview').textContent = this.value;
    });

    function previewThumb(input) {
        if (input.files && input.files[0]) {
            const r = new FileReader();
            r.onload = e => {
                document.getElementById('thumb-preview-img').src = e.target.result;
                document.getElementById('thumb-preview').classList.remove('hidden');
            };
            r.readAsDataURL(input.files[0]);
        }
    }
    document.getElementById('blog-form').addEventListener('submit', function() {
        if (typeof tinymce !== 'undefined') tinymce.triggerSave();
    });
</script>

<?php else: ?>
<!-- ══════════════════════════════════════════════════════════
     LIST VIEW
══════════════════════════════════════════════════════════ -->

<!-- Header -->
<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-5">
    <div>
        <p class="text-sm text-gray-500">
            <span class="font-semibold text-gray-700"><?= $total ?></span> artikel ·
            <span class="text-green-600 font-medium"><?= $totalAktif ?> aktif</span> ·
            <span class="text-amber-600 font-medium">📝 <?= $totalDraft ?> draft</span>
        </p>
        <p class="text-xs text-gray-400 mt-0.5">⠿ Seret kartu untuk mengubah urutan tampil</p>
    </div>
    <div class="flex gap-2">
        <a href="<?= $b ?>/admin/blog-categories"
            class="text-sm bg-gray-100 hover:bg-gray-200 text-gray-600 px-4 py-2 rounded-xl font-medium transition whitespace-nowrap">📂
            Kategori</a>
        <a href="<?= $b ?>/admin/blog?action=tambah" class="btn-primary whitespace-nowrap">+ Tulis Artikel</a>
    </div>
</div>

<!-- Filter & Search -->
<form method="GET" action="<?= $b ?>/admin/blog" class="bg-white rounded-xl border border-rose-100 p-4 mb-5">
    <div class="flex flex-wrap gap-3 items-center">
        <div class="relative flex-1 min-w-[160px]">
            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm">🔍</span>
            <input type="text" name="q" value="<?= clean($search_q) ?>"
                class="pl-8 pr-4 py-2 text-sm border border-gray-200 rounded-xl focus:outline-none focus:border-rose-400 w-full"
                placeholder="Cari judul artikel...">
        </div>
        <select name="cat"
            class="text-sm border border-gray-200 rounded-xl px-3 py-2 focus:outline-none focus:border-rose-400 bg-white">
            <option value="">Semua Kategori</option>
            <?php foreach ($blog_cats as $bc): ?>
            <option value="<?= $bc['id'] ?>" <?= $filter_cat == $bc['id'] ? 'selected' : '' ?>>
                <?= clean($bc['name']) ?></option>
            <?php endforeach; ?>
        </select>
        <select name="status"
            class="text-sm border border-gray-200 rounded-xl px-3 py-2 focus:outline-none focus:border-rose-400 bg-white">
            <option value="">Semua Status</option>
            <option value="active" <?= $filter_status === 'active' ? 'selected' : '' ?>>✅ Aktif</option>
            <option value="draft" <?= $filter_status === 'draft' ? 'selected' : '' ?>>📝 Draft</option>
            <option value="inactive" <?= $filter_status === 'inactive' ? 'selected' : '' ?>>⏸ Nonaktif</option>
        </select>
        <button type="submit" class="btn-primary text-sm">Filter</button>
        <a href="<?= $b ?>/admin/blog" class="btn-secondary text-sm">Reset</a>
    </div>
</form>

<!-- Toast reorder -->
<div id="toastReorder"
    class="hidden fixed bottom-5 right-5 bg-gray-800 text-white text-sm px-4 py-2.5 rounded-xl shadow-lg z-50">
    ✅ Urutan berhasil disimpan
</div>

<!-- Toast hapus -->
<div id="toastHapus"
    class="hidden fixed bottom-5 right-5 bg-gray-800 text-white text-sm px-4 py-2.5 rounded-xl shadow-lg z-50">
    ✅ Artikel berhasil dihapus
</div>

<!-- Empty state -->
<?php if (empty($blogs_list)): ?>
<div class="bg-white rounded-xl border border-rose-100 text-center py-16 text-gray-400">
    <div class="text-5xl mb-3">📝</div>
    <p class="font-medium text-sm">Belum ada artikel<?= $search_q ? ' yang cocok' : '' ?>.</p>
    <?php if (!$search_q): ?>
    <a href="<?= $b ?>/admin/blog?action=tambah"
        class="text-rose-400 hover:text-rose-600 text-sm mt-1 inline-block">Tulis artikel pertama →</a>
    <?php endif; ?>
</div>

<?php else: ?>

<!-- Card Grid artikel -->
<div id="blogGrid" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
    <?php foreach ($blogs_list as $i => $bl):
    $badge_class = match($bl['status']) {
        'active'   => 'badge-active',
        'draft'    => 'badge-draft',
        default    => 'badge-inactive',
    };
    $badge_label = match($bl['status']) {
        'active'   => 'Aktif',
        'draft'    => 'Draft',
        default    => 'Nonaktif',
    };
    $thumb = !empty($bl['thumbnail']) ? UPLOAD_URL . $bl['thumbnail'] : '';
  ?>
    <div class="blog-card group bg-white border border-rose-100 rounded-2xl overflow-hidden hover:shadow-lg transition-all duration-200 flex flex-col cursor-grab active:cursor-grabbing"
        data-id="<?= $bl['id'] ?>" data-name="<?= strtolower(clean($bl['title'])) ?>"
        data-cat="<?= $bl['blog_category_id'] ?>" data-status="<?= $bl['status'] ?>">

        <!-- Thumbnail -->
        <div class="relative w-full bg-rose-50 overflow-hidden" style="height:160px">
            <?php if ($thumb): ?>
            <img src="<?= $thumb ?>" alt="<?= clean($bl['title']) ?>"
                class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                onerror="this.parentElement.innerHTML='<div class=\'w-full h-full flex items-center justify-center text-rose-200 text-4xl\'>📝</div>'">
            <?php else: ?>
            <div class="w-full h-full flex items-center justify-center text-rose-200 text-4xl">📝</div>
            <?php endif; ?>

            <!-- Drag handle -->
            <div
                class="absolute top-2 left-2 bg-white/80 backdrop-blur-sm rounded-lg px-1.5 py-1 text-gray-400 text-sm select-none opacity-0 group-hover:opacity-100 transition-opacity">
                ⠿</div>

            <!-- Status badge -->
            <div class="absolute top-2 right-2">
                <span class="<?= $badge_class ?> text-xs shadow-sm"><?= $badge_label ?></span>
            </div>

            <!-- Kategori badge -->
            <?php if (!empty($bl['cat_name'])): ?>
            <div
                class="absolute bottom-2 left-2 bg-white/90 backdrop-blur-sm rounded-lg px-2 py-0.5 text-xs font-medium text-gray-600">
                <?= clean($bl['cat_name']) ?>
            </div>
            <?php endif; ?>
        </div>

        <!-- Info -->
        <div class="p-4 flex flex-col flex-1">
            <h3 class="font-display font-bold text-gray-800 text-sm leading-snug line-clamp-2 mb-1">
                <?= clean($bl['title']) ?>
            </h3>
            <p class="text-xs text-gray-400 font-mono mb-2 truncate">/blog/<?= clean($bl['slug']) ?></p>

            <?php if (!empty($bl['excerpt'])): ?>
            <p class="text-xs text-gray-500 line-clamp-2 mb-3 flex-1"><?= clean($bl['excerpt']) ?></p>
            <?php else: ?>
            <div class="flex-1"></div>
            <?php endif; ?>

            <div class="flex items-center justify-between text-xs text-gray-400 mb-3">
                <span><?= date('d M Y', strtotime($bl['created_at'])) ?></span>
                <span>urutan #<?= $bl['urutan'] ?></span>
            </div>

            <!-- Aksi -->
            <div class="flex items-center gap-1.5 flex-wrap">
                <?php if ($bl['status'] === 'active'): ?>
                <a href="<?= $b ?>/blog/<?= clean($bl['slug']) ?>" target="_blank"
                    class="flex-1 text-center text-xs bg-green-50 text-green-700 border border-green-200 px-2 py-1 rounded-lg hover:bg-green-100 font-medium">
                    👁 Lihat
                </a>
                <?php endif; ?>
                <a href="<?= $b ?>/admin/blog?action=edit&id=<?= $bl['id'] ?>"
                    class="flex-1 text-center text-xs bg-amber-50 text-amber-700 border border-amber-200 px-2 py-1 rounded-lg hover:bg-amber-100 font-medium">
                    ✏️ Edit
                </a>
                <button type="button"
                    onclick="hapusArtikel(<?= $bl['id'] ?>, '<?= clean(addslashes($bl['title'])) ?>')"
                    class="text-xs text-red-400 hover:text-red-600 px-1 py-1 rounded-lg hover:bg-red-50 border border-transparent hover:border-red-100">
                    🗑️
                </button>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<!-- Hidden form untuk hapus -->
<form method="POST" id="formHapus" style="display:none">
    <input type="hidden" name="action_type" value="hapus">
    <input type="hidden" name="del_id" id="hapusId" value="">
</form>

<script src="https://cdnjs.cloudflare.com/ajax/libs/Sortable/1.15.2/Sortable.min.js"></script>
<script>
    // ── Toast ─────────────────────────────────────────────────
    function showToast(id) {
        const t = document.getElementById(id);
        t.classList.remove('hidden');
        clearTimeout(t._t);
        t._t = setTimeout(() => t.classList.add('hidden'), 2500);
    }

    // ── Sortable drag-drop ────────────────────────────────────
    const blogGrid = document.getElementById('blogGrid');
    if (blogGrid) {
        Sortable.create(blogGrid, {
            animation: 200,
            ghostClass: 'opacity-30',
            handle: '.blog-card',
            filter: 'a, button',
            preventOnFilter: false,
            onEnd() {
                const ids = [...blogGrid.querySelectorAll('.blog-card')]
                    .filter(c => c.style.display !== 'none')
                    .map(c => c.dataset.id).join(',');
                fetch('', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    body: 'reorder=1&ids=' + ids
                }).then(r => r.json()).then(d => {
                    if (d.ok) showToast('toastReorder');
                });
            }
        });
    }

    // ── Hapus ─────────────────────────────────────────────────
    function hapusArtikel(id, title) {
        if (!confirm('Hapus artikel "' + title + '"?')) return;
        document.getElementById('hapusId').value = id;
        document.getElementById('formHapus').submit();
    }
</script>

<?php endif; ?>

<?php endif; // ← tutup if action tambah/edit vs list ?>

<?php require_once __DIR__ . '/../includes/admin_footer.php'; ?>