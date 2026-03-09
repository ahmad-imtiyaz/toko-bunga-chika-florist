<?php
require_once __DIR__ . '/../includes/config.php';
$slug = $_GET['slug'] ?? '';
$pdo  = getDB();
$stmt = $pdo->prepare("SELECT * FROM cities WHERE slug=? AND is_active=1");
$stmt->execute([$slug]);
$city = $stmt->fetch();
if (!$city) { http_response_code(404); require __DIR__ . '/404.php'; exit(); }

$areas     = getAreasByCity($city['id']);
$prodCats  = getMainCategories();
$nearby    = $pdo->query("SELECT name,slug FROM cities WHERE is_active=1 AND id!={$city['id']} ORDER BY tier ASC LIMIT 6")->fetchAll();
$featured  = $pdo->query("SELECT p.*,c.name as cat_name FROM products p JOIN categories c ON p.category_id=c.id WHERE p.is_featured=1 AND p.is_active=1 ORDER BY RAND() LIMIT 4")->fetchAll();

$kota = clean($city['name']);
$page_title    = $city['meta_title'] ?: "Toko Bunga {$kota} 24 Jam | Florist & Kirim Bunga Cepat – Chika Florist";
$meta_desc     = $city['meta_desc'] ?: "Toko bunga {$kota} melayani bunga papan, buket bunga & standing flower. Layanan florist online 24 jam dengan pengiriman cepat.";
$canonical_url = BASE_URL . '/toko-bunga-' . $city['slug'];
$breadcrumbs   = [['label'=>'Beranda','url'=>'/'],['label'=>'Toko Bunga '.$kota]];
require_once __DIR__ . '/../includes/header.php';
?>

<section class="bg-gradient-to-br from-rose-50 to-amber-50 py-12 px-4 border-b border-rose-100">
  <div class="max-w-4xl mx-auto text-center">
    <p class="text-rose-500 text-sm font-semibold tracking-wider uppercase mb-2">Florist Online 24 Jam</p>
    <h1 class="font-display text-3xl sm:text-4xl font-bold text-gray-900 mb-4">Toko Bunga <?= $kota ?> – Florist Online 24 Jam & Pengiriman Cepat</h1>
    <p class="text-gray-600 leading-relaxed mb-6">Chika Florist merupakan toko bunga di <?= $kota ?> yang melayani pemesanan bunga secara online selama 24 jam dengan layanan pengiriman cepat dan profesional.</p>
    <a href="<?= waLink("Halo Chika Florist, saya ingin pesan bunga di {$kota}") ?>" target="_blank"
       class="inline-flex items-center gap-2 bg-green-500 hover:bg-green-600 text-white font-semibold px-7 py-3 rounded-full transition-colors">Pesan Bunga di <?= $kota ?></a>
  </div>
</section>

<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
  <div class="seo-content">
    <h2>Layanan Toko Bunga di <?= $kota ?></h2>
    <p>Sebagai florist online yang melayani wilayah <?= $kota ?>, Chika Florist menyediakan berbagai rangkaian bunga untuk berbagai acara penting seperti ucapan selamat, pernikahan, belasungkawa, hingga peresmian usaha.</p>
    <ul>
      <li>Pemesanan bunga online 24 jam</li>
      <li>Pengiriman bunga same day di area <?= $kota ?></li>
      <li>Desain bunga elegan dan profesional</li>
      <li>Request ucapan custom</li>
      <li>Layanan cepat dan responsif</li>
    </ul>

    <h2>Pilihan Karangan Bunga di <?= $kota ?></h2>
    <?php foreach ($prodCats as $cat): ?>
    <h3><a href="<?= BASE_URL ?>/<?= $cat['slug'] ?>"><?= clean($cat['name']) ?> <?= $kota ?></a></h3>
    <p><?= clean($cat['description'] ?? '') ?></p>
    <?php endforeach; ?>

    <?php if (!empty($areas)): ?>
    <h2>Area Pengiriman Toko Bunga <?= $kota ?></h2>
    <p>Chika Florist melayani pengiriman bunga ke berbagai wilayah di <?= $kota ?> dan sekitarnya:</p>
    <div class="internal-links my-4">
      <?php foreach ($areas as $area): ?>
      <a href="<?= BASE_URL ?>/toko-bunga-<?= $area['slug'] ?>">Toko Bunga <?= clean($area['name']) ?></a>
      <?php endforeach; ?>
    </div>
    <?php endif; ?>

    <?php if ($city['landmark_notes']): ?>
    <p class="text-gray-500 text-sm"><?= nl2br(clean($city['landmark_notes'])) ?></p>
    <?php endif; ?>

    <h2>Kenapa Memilih Chika Florist di <?= $kota ?>?</h2>
    <ul>
      <li>Layanan florist online 24 jam</li>
      <li>Pengiriman cepat &amp; tepat waktu</li>
      <li>Bunga fresh berkualitas</li>
      <li>Harga transparan</li>
      <li>Tim florist profesional</li>
    </ul>

    <h2>Cara Pesan Bunga di <?= $kota ?></h2>
    <p>1. Pilih produk bunga di website. 2. Hubungi admin melalui WhatsApp. 3. Kirim detail ucapan dan alamat pengiriman di <?= $kota ?>. 4. Pesanan diproses dan langsung dikirim.</p>

    <h2>FAQ Toko Bunga <?= $kota ?></h2>
    <p><strong>Apakah bisa kirim bunga di hari yang sama di <?= $kota ?>?</strong><br>Ya, tersedia layanan same day delivery.</p>
    <p><strong>Apakah bisa pesan bunga malam hari?</strong><br>Bisa, layanan kami tersedia 24 jam.</p>
    <p><strong>Apakah bisa request desain bunga?</strong><br>Tentu, kami menerima desain custom.</p>

    <h2>Pesan Bunga di <?= $kota ?> Sekarang</h2>
    <p>Jika Anda membutuhkan toko bunga di <?= $kota ?> dengan layanan cepat, Chika Florist siap membantu melalui layanan <a href="<?= BASE_URL ?>/toko-bunga-online-24-jam-indonesia">toko bunga online 24 jam Indonesia</a>.</p>
  </div>

  <div class="mt-8 p-5 bg-amber-50 rounded-xl border border-amber-100">
    <p class="text-xs text-gray-500 uppercase tracking-wider font-semibold mb-3">Produk Tersedia di <?= $kota ?></p>
    <div class="internal-links">
      <?php foreach ($prodCats as $cat): ?>
      <a href="<?= BASE_URL ?>/<?= $cat['slug'] ?>"><?= clean($cat['name']) ?> <?= $kota ?></a>
      <?php endforeach; ?>
      <a href="<?= BASE_URL ?>/toko-bunga-online-24-jam-indonesia">toko bunga online 24 jam</a>
      <a href="<?= BASE_URL ?>/">florist online 24 jam Indonesia</a>
    </div>
  </div>

  <?php if (!empty($nearby)): ?>
  <div class="mt-5 p-5 bg-white rounded-xl border border-amber-100">
    <p class="text-xs text-gray-500 uppercase tracking-wider font-semibold mb-3">Kota Sekitar</p>
    <div class="internal-links">
      <?php foreach ($nearby as $nc): ?>
      <a href="<?= BASE_URL ?>/toko-bunga-<?= $nc['slug'] ?>">Toko Bunga <?= clean($nc['name']) ?></a>
      <?php endforeach; ?>
    </div>
  </div>
  <?php endif; ?>
</div>

<?php if (!empty($featured)): ?>
<section class="bg-amber-50 py-10 px-4 border-t border-amber-100">
  <div class="max-w-5xl mx-auto">
    <h2 class="font-display text-xl font-bold text-gray-900 mb-6">Produk Populer di <?= $kota ?></h2>
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
      <?php foreach ($featured as $prod): ?>
      <a href="<?= waLink('Halo, saya ingin pesan '.$prod['name'].' di '.$kota) ?>" target="_blank" class="card-hover bg-white rounded-xl overflow-hidden border border-amber-100 group">
        <div class="h-36 overflow-hidden bg-rose-50">
          <img src="<?= UPLOAD_URL.($prod['image']??'') ?>" alt="<?= clean($prod['name']) ?> <?= $kota ?>" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300" onerror="this.style.display='none'">
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
  <h2 class="font-display text-xl font-bold text-white mb-2">Pesan Bunga di <?= $kota ?> Sekarang</h2>
  <p class="text-rose-100 text-sm mb-5">Hubungi kami sekarang dan lakukan pemesanan bunga dengan mudah.</p>
  <a href="<?= waLink("Halo, saya ingin pesan bunga di {$kota}") ?>" target="_blank"
     class="inline-flex items-center gap-2 bg-white text-rose-600 font-bold px-7 py-3 rounded-full hover:bg-rose-50 transition-colors">Pesan via WhatsApp</a>
</section>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
