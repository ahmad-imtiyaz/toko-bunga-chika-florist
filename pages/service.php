<?php
require_once __DIR__ . '/../includes/config.php';
$slug = $_GET['slug'] ?? '';
$pdo  = getDB();
$stmt = $pdo->prepare("SELECT * FROM service_pages WHERE slug=? AND is_active=1");
$stmt->execute([$slug]);
$page = $stmt->fetch();
if (!$page) { http_response_code(404); require __DIR__ . '/404.php'; exit(); }

$cities   = getActiveCities(16);
$prodCats = getMainCategories();
$page_title    = $page['meta_title'];
$meta_desc     = $page['meta_desc'];
$canonical_url = BASE_URL . '/' . $page['slug'];
$breadcrumbs   = [['label'=>'Beranda','url'=>'/'],['label'=>$page['title']]];
require_once __DIR__ . '/../includes/header.php';
?>

<section class="bg-gradient-to-br from-rose-50 to-amber-50 py-12 px-4 border-b border-rose-100">
  <div class="max-w-4xl mx-auto text-center">
    <p class="text-rose-500 text-sm font-semibold tracking-wider uppercase mb-2">Chika Florist</p>
    <h1 class="font-display text-3xl sm:text-4xl font-bold text-gray-900 mb-4"><?= clean($page['h1_text'] ?: $page['title']) ?></h1>
    <a href="<?= waLink() ?>" target="_blank" class="inline-flex items-center gap-2 bg-green-500 hover:bg-green-600 text-white font-semibold px-7 py-3 rounded-full transition-colors">Pesan via WhatsApp</a>
  </div>
</section>

<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
  <div class="seo-content">
    <?php if ($page['content']): ?>
      <?= $page['content'] ?>
    <?php else: ?>
      <p>Chika Florist merupakan toko bunga online 24 jam Indonesia yang melayani pemesanan dan pengiriman berbagai jenis rangkaian bunga ke seluruh wilayah Indonesia.</p>
      <h2>Layanan Florist Online 24 Jam Nonstop</h2>
      <p>Tidak semua momen penting dapat direncanakan sebelumnya. Oleh karena itu, Chika Florist menyediakan layanan toko bunga online 24 jam yang memungkinkan Anda melakukan pemesanan kapan saja.</p>
      <ul>
        <li>Pemesanan bunga 24 jam nonstop</li>
        <li>Admin responsif melalui WhatsApp</li>
        <li>Proses cepat &amp; praktis</li>
        <li>Pengiriman same day delivery</li>
        <li>Jangkauan pengiriman seluruh Indonesia</li>
      </ul>
      <h2>Jenis Bunga yang Bisa Dipesan 24 Jam</h2>
      <?php foreach ($prodCats as $cat): ?>
      <h3><a href="<?= BASE_URL ?>/<?= $cat['slug'] ?>"><?= clean($cat['name']) ?></a></h3>
      <p><?= clean($cat['description'] ?? '') ?></p>
      <?php endforeach; ?>
      <h2>Kenapa Memilih Toko Bunga Online 24 Jam Chika Florist?</h2>
      <ul>
        <li>Layanan florist online 24 jam Indonesia</li>
        <li>Pengiriman cepat &amp; tepat waktu</li>
        <li>Bunga fresh dan berkualitas</li>
        <li>Harga kompetitif dan transparan</li>
        <li>Tim florist profesional berpengalaman</li>
      </ul>
      <h2>Cara Pesan Bunga Online 24 Jam</h2>
      <p>1. Pilih produk bunga. 2. Hubungi admin melalui WhatsApp. 3. Kirim detail ucapan dan alamat pengiriman. 4. Pesanan diproses dan langsung dikirim.</p>
      <h2>Pesan Bunga Sekarang</h2>
      <p>Dengan layanan <a href="<?= BASE_URL ?>/">toko bunga online 24 jam Indonesia</a> dari Chika Florist, Anda dapat mengirim bunga kapan saja dengan proses cepat.</p>
    <?php endif; ?>
  </div>

  <?php if (!empty($cities)): ?>
  <div class="mt-8 p-5 bg-amber-50 rounded-xl border border-amber-100">
    <p class="text-xs text-gray-500 uppercase tracking-wider font-semibold mb-3">Tersedia di Kota</p>
    <div class="internal-links">
      <?php foreach ($cities as $city): ?>
      <a href="<?= BASE_URL ?>/toko-bunga-<?= $city['slug'] ?>"><?= clean($page['title']) ?> <?= clean($city['name']) ?></a>
      <?php endforeach; ?>
    </div>
  </div>
  <?php endif; ?>

  <div class="mt-5 p-5 bg-white rounded-xl border border-amber-100">
    <p class="text-xs text-gray-500 uppercase tracking-wider font-semibold mb-3">Produk Bunga Kami</p>
    <div class="internal-links">
      <?php foreach ($prodCats as $cat): ?>
      <a href="<?= BASE_URL ?>/<?= $cat['slug'] ?>"><?= clean($cat['name']) ?></a>
      <?php endforeach; ?>
      <a href="<?= BASE_URL ?>/">toko bunga online 24 jam Indonesia</a>
    </div>
  </div>
</div>

<section class="bg-rose-600 py-10 px-4 text-center">
  <h2 class="font-display text-xl font-bold text-white mb-2">Pesan Bunga Online Sekarang</h2>
  <p class="text-rose-100 text-sm mb-5">Tidak perlu menunggu toko buka. Kami siap melayani 24 jam.</p>
  <a href="<?= waLink() ?>" target="_blank" class="inline-flex items-center gap-2 bg-white text-rose-600 font-bold px-7 py-3 rounded-full hover:bg-rose-50 transition-colors">Pesan via WhatsApp</a>
</section>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
