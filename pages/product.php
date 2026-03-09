<?php
// Halaman Detail Produk - /produk/[slug]
require_once __DIR__ . '/../includes/config.php';

$slug = $_GET['slug'] ?? '';
$pdo  = getDB();

$stmt = $pdo->prepare("SELECT p.*, c.name as cat_name, c.slug as cat_slug, c.parent_id,
    (SELECT name FROM categories WHERE id=c.parent_id) as parent_cat_name,
    (SELECT slug FROM categories WHERE id=c.parent_id) as parent_cat_slug
    FROM products p JOIN categories c ON p.category_id=c.id 
    WHERE p.slug=? AND p.is_active=1");
$stmt->execute([$slug]);
$product = $stmt->fetch();

if (!$product) {
    header("HTTP/1.0 404 Not Found");
    require_once __DIR__ . '/404.php';
    exit();
}

// Produk terkait (kategori sama)
$related = $pdo->prepare("SELECT p.*, c.name as cat_name FROM products p JOIN categories c ON p.category_id=c.id 
    WHERE p.category_id=? AND p.id!=? AND p.is_active=1 ORDER BY p.is_featured DESC LIMIT 4");
$related->execute([$product['category_id'], $product['id']]);
$related = $related->fetchAll();

$cities = getActiveCities(12);

$nama = clean($product['name']);
$page_title    = $product['meta_title'] ?: "{$nama} 24 Jam | Florist Online – Chika Florist";
$meta_desc     = $product['meta_desc'] ?: "Pesan {$nama} online dengan layanan 24 jam. Tersedia berbagai ukuran dan desain, pengiriman cepat ke seluruh Indonesia.";
$canonical_url = BASE_URL . '/produk/' . $product['slug'];

$breadcrumbs = [['label'=>'Beranda','url'=>'/']];
if ($product['parent_id'] && $product['parent_cat_slug']) {
    $breadcrumbs[] = ['label'=>$product['parent_cat_name'],'url'=>'/'.$product['parent_cat_slug']];
}
$breadcrumbs[] = ['label'=>$product['cat_name'],'url'=>'/'.$product['cat_slug']];
$breadcrumbs[] = ['label'=>$nama];

require_once __DIR__ . '/../includes/header.php';
?>

<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

    <!-- Produk Detail -->
    <div class="bg-white rounded-2xl border border-amber-100 overflow-hidden mb-8">
        <div class="grid grid-cols-1 md:grid-cols-2">
            <!-- Gambar -->
            <div class="bg-gradient-to-br from-rose-50 to-amber-50 flex items-center justify-center p-6 min-h-64">
                <img src="<?= UPLOAD_URL . ($product['image'] ?? 'placeholder.jpg') ?>"
                     alt="<?= $nama ?> Chika Florist"
                     class="max-h-80 w-full object-contain rounded-xl"
                     onerror="this.style.background='#fdf8f0'">
            </div>
            <!-- Info -->
            <div class="p-6 sm:p-8">
                <div class="flex items-center gap-2 mb-3">
                    <?php if ($product['parent_cat_slug']): ?>
                    <a href="<?= BASE_URL ?>/<?= $product['parent_cat_slug'] ?>" class="text-xs text-rose-500 hover:underline font-medium"><?= clean($product['parent_cat_name']) ?></a>
                    <span class="text-gray-300">/</span>
                    <?php endif; ?>
                    <a href="<?= BASE_URL ?>/<?= $product['cat_slug'] ?>" class="text-xs text-rose-500 hover:underline font-medium"><?= clean($product['cat_name']) ?></a>
                </div>
                <h1 class="font-display text-2xl sm:text-3xl font-bold text-gray-900 mb-3 leading-snug"><?= $nama ?></h1>
                <?php if ($product['short_desc']): ?>
                <p class="text-gray-500 text-sm leading-relaxed mb-4"><?= clean($product['short_desc']) ?></p>
                <?php endif; ?>
                <div class="bg-rose-50 rounded-xl px-5 py-4 mb-6 inline-block border border-rose-100">
                    <p class="text-xs text-gray-500 mb-0.5">Harga mulai</p>
                    <p class="font-display font-bold text-2xl text-rose-600"><?= formatHarga($product['price_min'], $product['price_max']) ?></p>
                    <p class="text-xs text-gray-400 mt-1">*Belum termasuk ongkos kirim</p>
                </div>
                <div class="space-y-3">
                    <a href="<?= waLink("Halo Chika Florist, saya ingin pesan {$nama}") ?>" target="_blank"
                       class="flex items-center justify-center gap-2 bg-green-500 hover:bg-green-600 text-white font-bold py-3 px-6 rounded-full transition-colors w-full">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z"/><path d="M12 0C5.373 0 0 5.373 0 12c0 2.096.541 4.063 1.491 5.776L.057 23.04l5.43-1.424A11.947 11.947 0 0012 24c6.627 0 12-5.373 12-12S18.627 0 12 0zm0 21.818a9.818 9.818 0 01-5.015-1.371l-.36-.214-3.726.977.995-3.633-.235-.374A9.818 9.818 0 1112 21.818z"/></svg>
                        Pesan via WhatsApp
                    </a>
                    <a href="<?= BASE_URL ?>/<?= $product['cat_slug'] ?>"
                       class="flex items-center justify-center bg-warm-50 hover:bg-amber-100 text-amber-700 font-semibold py-2.5 px-6 rounded-full border border-amber-200 transition-colors text-sm w-full">
                        Lihat Semua <?= clean($product['cat_name']) ?>
                    </a>
                </div>
                <!-- Trust -->
                <div class="mt-5 flex flex-wrap gap-3 text-xs text-gray-500">
                    <span class="flex items-center gap-1"><span class="text-green-500">✓</span> 24 Jam Nonstop</span>
                    <span class="flex items-center gap-1"><span class="text-green-500">✓</span> Bunga Fresh</span>
                    <span class="flex items-center gap-1"><span class="text-green-500">✓</span> Same Day Delivery</span>
                    <span class="flex items-center gap-1"><span class="text-green-500">✓</span> Harga Transparan</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Deskripsi lengkap -->
    <?php if ($product['description']): ?>
    <div class="seo-content bg-white rounded-xl border border-amber-100 p-6 mb-8">
        <h2>Tentang <?= $nama ?></h2>
        <?= nl2br(clean($product['description'])) ?>
    </div>
    <?php endif; ?>

    <!-- SEO Content & Internal Linking -->
    <div class="seo-content bg-white rounded-xl border border-amber-100 p-6 mb-6">
        <h2>Informasi <?= $nama ?></h2>
        <p>Chika Florist menyediakan <?= $nama ?> berkualitas tinggi untuk berbagai kebutuhan acara. Setiap produk dibuat dengan bunga segar pilihan oleh tim florist profesional kami.</p>
        <h2><?= $nama ?> Tersedia di Seluruh Indonesia</h2>
        <p>Produk ini dapat dipesan melalui layanan <a href="<?= BASE_URL ?>/toko-bunga-online-24-jam-indonesia">toko bunga online 24 jam Indonesia</a> Chika Florist. 
        Kami melayani pengiriman ke berbagai kota di Indonesia.</p>
    </div>

    <!-- Internal Links - Tersedia di Kota -->
    <?php if (!empty($cities)): ?>
    <div class="p-5 bg-warm-50 rounded-xl border border-amber-100 mb-6">
        <p class="text-xs text-gray-500 uppercase tracking-wider font-semibold mb-3"><?= $nama ?> Tersedia di</p>
        <div class="internal-links">
            <?php foreach ($cities as $city): ?>
            <a href="<?= BASE_URL ?>/toko-bunga-<?= $city['slug'] ?>"><?= $nama ?> <?= clean($city['name']) ?></a>
            <?php endforeach; ?>
            <a href="<?= BASE_URL ?>/">toko bunga online 24 jam Indonesia</a>
        </div>
    </div>
    <?php endif; ?>

    <!-- Produk Terkait -->
    <?php if (!empty($related)): ?>
    <div class="mt-8">
        <h2 class="font-display text-xl font-bold text-gray-900 mb-5">Produk <?= clean($product['cat_name']) ?> Lainnya</h2>
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
            <?php foreach ($related as $r): ?>
            <a href="<?= BASE_URL ?>/produk/<?= $r['slug'] ?>" class="card-hover bg-white rounded-xl overflow-hidden border border-amber-100 group">
                <div class="h-40 overflow-hidden bg-rose-50">
                    <img src="<?= UPLOAD_URL . ($r['image'] ?? 'placeholder.jpg') ?>" alt="<?= clean($r['name']) ?>" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300" onerror="this.style.display='none'">
                </div>
                <div class="p-3">
                    <p class="font-semibold text-gray-800 text-xs leading-snug mb-1"><?= clean($r['name']) ?></p>
                    <p class="text-rose-600 text-xs font-bold"><?= formatHarga($r['price_min'], $r['price_max']) ?></p>
                </div>
            </a>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>
</div>

<!-- CTA -->
<section class="bg-rose-600 py-10 px-4 text-center">
    <h2 class="font-display text-xl font-bold text-white mb-2">Pesan <?= $nama ?> Sekarang</h2>
    <p class="text-rose-100 text-sm mb-5">Hubungi admin kami 24 jam untuk konsultasi dan pemesanan.</p>
    <a href="<?= waLink("Halo, saya ingin pesan {$nama}") ?>" target="_blank"
       class="inline-flex items-center gap-2 bg-white text-rose-600 font-bold px-7 py-3 rounded-full hover:bg-rose-50 transition-colors">
        Pesan via WhatsApp
    </a>
</section>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
