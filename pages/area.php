<?php
require_once __DIR__ . '/../includes/config.php';
$slug = $_GET['slug'] ?? '';
$pdo  = getDB();
$stmt = $pdo->prepare("SELECT a.*,c.name as city_name,c.slug as city_slug FROM areas a JOIN cities c ON a.city_id=c.id WHERE a.slug=? AND a.is_active=1");
$stmt->execute([$slug]);
$area = $stmt->fetch();
if (!$area) { http_response_code(404); require __DIR__ . '/404.php'; exit(); }

$prodCats = getMainCategories();
$nearbyQ  = $pdo->prepare("SELECT name,slug FROM areas WHERE city_id=? AND id!=? AND is_active=1 ORDER BY sort_order ASC LIMIT 8");
$nearbyQ->execute([$area['city_id'], $area['id']]);
$nearby   = $nearbyQ->fetchAll();
$featured = $pdo->query("SELECT p.*,c.name as cat_name FROM products p JOIN categories c ON p.category_id=c.id WHERE p.is_featured=1 AND p.is_active=1 ORDER BY RAND() LIMIT 4")->fetchAll();

$nama = clean($area['name']);
$kota = clean($area['city_name']);
$page_title    = $area['meta_title'] ?: "Toko Bunga {$nama} {$kota} 24 Jam | Florist Terdekat – Chika Florist";
$meta_desc     = $area['meta_desc'] ?: "Toko bunga {$nama} {$kota} melayani bunga papan, buket bunga & standing flower. Florist online 24 jam dengan pengiriman cepat.";
$canonical_url = BASE_URL . '/toko-bunga-' . $area['slug'];
$breadcrumbs   = [['label'=>'Beranda','url'=>'/'],['label'=>'Toko Bunga '.$kota,'url'=>'/toko-bunga-'.$area['city_slug']],['label'=>'Toko Bunga '.$nama]];
require_once __DIR__ . '/../includes/header.php';
?>

<section class="bg-gradient-to-br from-rose-50 to-amber-50 py-12 px-4 border-b border-rose-100">
  <div class="max-w-4xl mx-auto text-center">
    <p class="text-rose-500 text-sm font-semibold tracking-wider uppercase mb-2">Florist Online 24 Jam</p>
    <h1 class="font-display text-3xl sm:text-4xl font-bold text-gray-900 mb-4">Toko Bunga <?= $nama ?> <?= $kota ?> – Florist Online 24 Jam & Pengiriman Cepat</h1>
    <p class="text-gray-600 leading-relaxed mb-3">Chika Florist melayani pemesanan dan pengiriman bunga di wilayah <?= $nama ?> <?= $kota ?> dengan layanan florist online 24 jam.</p>
    <p class="text-gray-500 text-sm mb-6">Melalui layanan <a href="<?= BASE_URL ?>/toko-bunga-online-24-jam-indonesia" class="text-rose-600 hover:underline">toko bunga online terpercaya</a>, pelanggan di <?= $nama ?> dapat memesan bunga kapan saja tanpa datang ke toko.</p>
    <a href="<?= waLink("Halo Chika Florist, saya ingin pesan bunga di {$nama} {$kota}") ?>" target="_blank"
       class="inline-flex items-center gap-2 bg-green-500 hover:bg-green-600 text-white font-semibold px-7 py-3 rounded-full transition-colors">Pesan Bunga di <?= $nama ?></a>
  </div>
</section>

<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
  <div class="seo-content">
    <h2>Layanan Florist di <?= $nama ?> <?= $kota ?></h2>
    <p>Sebagai toko bunga yang melayani area <?= $nama ?>, Chika Florist menyediakan berbagai rangkaian bunga untuk berbagai kebutuhan acara.</p>
    <ul>
      <li>Pemesanan bunga online 24 jam</li>
      <li>Pengiriman bunga cepat area <?= $nama ?></li>
      <li>Desain bunga profesional</li>
      <li>Custom ucapan papan bunga</li>
      <li>Same day delivery</li>
    </ul>

    <h2>Pilihan Karangan Bunga di <?= $nama ?></h2>
    <?php foreach ($prodCats as $cat): ?>
    <h3><a href="<?= BASE_URL ?>/<?= $cat['slug'] ?>"><?= clean($cat['name']) ?> <?= $nama ?></a></h3>
    <p><?= clean($cat['description'] ?? '') ?></p>
    <?php endforeach; ?>

    <?php if ($area['landmarks']): ?>
    <h2>Area Pengiriman Sekitar <?= $nama ?></h2>
    <p>Pengiriman bunga mencakup wilayah sekitar <?= $nama ?>:</p>
    <div class="bg-amber-50 rounded-lg p-4 border border-amber-100 my-4">
      <p class="text-gray-600 text-sm"><?= nl2br(clean($area['landmarks'])) ?></p>
    </div>
    <?php endif; ?>

    <h2>Kenapa Memilih Chika Florist di <?= $nama ?>?</h2>
    <ul>
      <li>Layanan toko bunga online 24 jam</li>
      <li>Pengiriman cepat area <?= $nama ?></li>
      <li>Bunga fresh berkualitas</li>
      <li>Harga transparan tanpa biaya tersembunyi</li>
      <li>Admin responsif via WhatsApp</li>
    </ul>

    <h2>Cara Pesan Bunga di <?= $nama ?></h2>
    <p>1. Pilih produk bunga di website. 2. Hubungi admin melalui WhatsApp. 3. Kirim alamat pengiriman di <?= $nama ?>. 4. Tim florist langsung memproses pesanan.</p>

    <h2>FAQ Toko Bunga <?= $nama ?></h2>
    <p><strong>Apakah bisa kirim bunga hari yang sama di <?= $nama ?>?</strong><br>Ya, tersedia layanan same day delivery.</p>
    <p><strong>Apakah bisa pesan tengah malam?</strong><br>Bisa, layanan florist online kami tersedia 24 jam.</p>

    <h2>Pesan Bunga di <?= $nama ?> Sekarang</h2>
    <p>Layanan ini merupakan bagian dari layanan <a href="<?= BASE_URL ?>/toko-bunga-<?= $area['city_slug'] ?>">toko bunga <?= $kota ?></a> oleh Chika Florist.
    Kami siap membantu kapan saja melalui <a href="<?= BASE_URL ?>/toko-bunga-online-24-jam-indonesia">toko bunga online 24 jam Indonesia</a>.</p>
  </div>

  <div class="mt-8 p-5 bg-amber-50 rounded-xl border border-amber-100">
    <p class="text-xs text-gray-500 uppercase tracking-wider font-semibold mb-3">Produk Populer di <?= $nama ?></p>
    <div class="internal-links">
      <?php foreach ($prodCats as $cat): ?>
      <a href="<?= BASE_URL ?>/<?= $cat['slug'] ?>">pesan <?= clean($cat['name']) ?> di <?= $nama ?></a>
      <?php endforeach; ?>
      <a href="<?= BASE_URL ?>/toko-bunga-online-24-jam-indonesia">toko bunga online 24 jam</a>
    </div>
  </div>

  <?php if (!empty($nearby)): ?>
  <div class="mt-5 p-5 bg-white rounded-xl border border-amber-100">
    <p class="text-xs text-gray-500 uppercase tracking-wider font-semibold mb-3">Area Sekitar <?= $nama ?></p>
    <div class="internal-links">
      <?php foreach ($nearby as $n): ?>
      <a href="<?= BASE_URL ?>/toko-bunga-<?= $n['slug'] ?>">Toko Bunga <?= clean($n['name']) ?></a>
      <?php endforeach; ?>
      <a href="<?= BASE_URL ?>/toko-bunga-<?= $area['city_slug'] ?>">Toko Bunga <?= $kota ?></a>
    </div>
  </div>
  <?php endif; ?>
</div>

<?php if (!empty($featured)): ?>
<section class="bg-amber-50 py-10 px-4 border-t border-amber-100">
  <div class="max-w-5xl mx-auto">
    <h2 class="font-display text-xl font-bold text-gray-900 mb-6">Produk Populer di <?= $nama ?></h2>
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
      <?php foreach ($featured as $prod): ?>
      <a href="<?= waLink('Halo, saya ingin pesan '.$prod['name'].' di '.$nama) ?>" target="_blank" class="card-hover bg-white rounded-xl overflow-hidden border border-amber-100 group">
        <div class="h-36 overflow-hidden bg-rose-50">
          <img src="<?= UPLOAD_URL.($prod['image']??'') ?>" alt="<?= clean($prod['name']) ?> <?= $nama ?>" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300" onerror="this.style.display='none'">
        </div>
        <div class="p-3">
          <p class="font-semibold text-gray-800 text-xs leading-snug mb-1"><?= clean($prod['name']) ?></p>
          <p class="text-rose-600 text-xs font-bold"><?= formatHarga($prod['price_min'],$prod['price_max']) ?></p>
        </div>
      </a>
      <?php endforeach; ?>
    </div>
  </div>
</section>
<?php endif; ?>

<section class="bg-rose-600 py-10 px-4 text-center">
  <h2 class="font-display text-xl font-bold text-white mb-2">Pesan Bunga di <?= $nama ?> Sekarang</h2>
  <p class="text-rose-100 text-sm mb-5">Hubungi kami sekarang untuk pemesanan cepat.</p>
  <a href="<?= waLink("Halo, saya ingin pesan bunga di {$nama} {$kota}") ?>" target="_blank"
     class="inline-flex items-center gap-2 bg-white text-rose-600 font-bold px-7 py-3 rounded-full hover:bg-rose-50 transition-colors">Pesan via WhatsApp</a>
</section>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
