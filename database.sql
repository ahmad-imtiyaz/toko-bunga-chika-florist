-- ============================================================
-- CHIKA FLORIST - DATABASE SCHEMA
-- ============================================================
CREATE DATABASE IF NOT EXISTS chikaflorist CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE chikaflorist;

-- ADMIN USERS
CREATE TABLE admin_users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    full_name VARCHAR(100),
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;
INSERT INTO admin_users (username, password, full_name) VALUES
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Administrator');
-- password: admin123

-- SETTINGS
CREATE TABLE settings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    setting_key VARCHAR(100) NOT NULL UNIQUE,
    setting_value TEXT,
    setting_label VARCHAR(150),
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;
INSERT INTO settings (setting_key, setting_value, setting_label) VALUES
('site_name','Chika Florist','Nama Website'),
('site_tagline','Toko Bunga Online 24 Jam Indonesia','Tagline Website'),
('whatsapp_number','628131241986','Nomor WhatsApp'),
('whatsapp_text','Halo Chika Florist, saya ingin memesan bunga','Pesan Default WhatsApp'),
('email','info@chikaflorist.com','Email'),
('address','Indonesia','Alamat'),
('instagram','https://instagram.com/chikaflorist','Instagram'),
('facebook','https://facebook.com/chikaflorist','Facebook'),
('tiktok','','TikTok'),
('meta_title_home','Toko Bunga Online 24 Jam Indonesia | Kirim Bunga Cepat – Chika Florist','Meta Title Homepage'),
('meta_desc_home','Toko bunga online 24 jam Indonesia melayani bunga papan, buket bunga & standing flower. Order kapan saja, kirim cepat seluruh Indonesia.','Meta Description Homepage'),
('logo','logo.jpeg','Logo'),
('favicon','favicon.ico','Favicon'),
('footer_text','Chika Florist – Toko Bunga Online 24 Jam Indonesia. Melayani pengiriman bunga ke seluruh kota di Indonesia.','Teks Footer');

-- CATEGORIES
CREATE TABLE categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    parent_id INT DEFAULT NULL,
    name VARCHAR(150) NOT NULL,
    slug VARCHAR(200) NOT NULL UNIQUE,
    description TEXT,
    image VARCHAR(255),
    meta_title VARCHAR(255),
    meta_desc TEXT,
    sort_order INT DEFAULT 0,
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (parent_id) REFERENCES categories(id) ON DELETE SET NULL
) ENGINE=InnoDB;
INSERT INTO categories (parent_id,name,slug,description,image,meta_title,meta_desc,sort_order) VALUES
(NULL,'Bunga Papan','bunga-papan','Bunga papan untuk berbagai acara.','bunga-papan.jpg','Bunga Papan 24 Jam | Florist Online – Chika Florist','Bunga papan elegan untuk grand opening, wedding & duka cita.',1),
(NULL,'Buket Bunga','buket-bunga','Buket bunga untuk hadiah spesial.','buket-bunga.jpg','Buket Bunga 24 Jam | Florist Online – Chika Florist','Buket bunga cantik untuk hadiah ulang tahun, wisuda & anniversary.',2),
(NULL,'Standing Flower','standing-flower','Standing flower untuk acara formal.','standing-flower.jpg','Standing Flower 24 Jam | Florist Online – Chika Florist','Standing flower elegan untuk pernikahan & peresmian kantor.',3),
(NULL,'Karangan Bunga Custom','karangan-bunga-custom','Karangan bunga desain khusus.','karangan-custom.jpg','Karangan Bunga Custom | Florist Online – Chika Florist','Terima pesanan karangan bunga custom desain khusus sesuai tema acara.',4);
INSERT INTO categories (parent_id,name,slug,description,image,sort_order) VALUES
(1,'Bunga Papan Ucapan','bunga-papan-ucapan','Bunga papan untuk ucapan selamat.','bunga-papan-ucapan.jpg',1),
(1,'Bunga Papan Duka','bunga-papan-duka','Bunga papan belasungkawa.','bunga-papan-duka.jpg',2),
(1,'Bunga Papan Wedding','bunga-papan-wedding','Bunga papan pernikahan.','bunga-papan-wedding.jpg',3),
(2,'Buket Mawar','buket-mawar','Buket bunga mawar segar.','buket-mawar.jpg',1),
(2,'Buket Wisuda','buket-wisuda','Buket bunga untuk wisuda.','buket-wisuda.jpg',2),
(2,'Buket Anniversary','buket-anniversary','Buket bunga untuk anniversary.','buket-anniversary.jpg',3),
(3,'Standing Flower Wedding','standing-flower-wedding','Standing flower pernikahan.','standing-wedding.jpg',1),
(3,'Standing Flower Duka','standing-flower-duka','Standing flower belasungkawa.','standing-duka.jpg',2),
(3,'Standing Flower Grand Opening','standing-flower-grand-opening','Standing flower grand opening.','standing-grand-opening.jpg',3);

-- PRODUCTS
CREATE TABLE products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    category_id INT NOT NULL,
    name VARCHAR(255) NOT NULL,
    slug VARCHAR(300) NOT NULL UNIQUE,
    description TEXT,
    short_desc VARCHAR(500),
    price DECIMAL(12,2) DEFAULT 0,
    price_min DECIMAL(12,2) DEFAULT 0,
    price_max DECIMAL(12,2) DEFAULT 0,
    image VARCHAR(255),
    meta_title VARCHAR(255),
    meta_desc TEXT,
    sort_order INT DEFAULT 0,
    is_featured TINYINT(1) DEFAULT 0,
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE CASCADE
) ENGINE=InnoDB;
INSERT INTO products (category_id,name,slug,short_desc,price_min,price_max,image,is_featured,sort_order) VALUES
(1,'Bunga Papan Selamat Grand Opening','bunga-papan-selamat-grand-opening','Bunga papan elegan untuk ucapan selamat pembukaan usaha baru.',350000,750000,'produk-bunga-papan-grand-opening.jpg',1,1),
(1,'Bunga Papan Ucapan Selamat','bunga-papan-ucapan-selamat','Bunga papan cantik untuk berbagai ucapan selamat.',300000,600000,'produk-bunga-papan-ucapan.jpg',1,2),
(2,'Bunga Papan Duka Cita','bunga-papan-duka-cita','Bunga papan belasungkawa yang tulus dan bermartabat.',400000,800000,'produk-bunga-papan-duka.jpg',1,3),
(4,'Buket Mawar Merah','buket-mawar-merah','Buket mawar merah segar untuk momen romantis spesial.',150000,450000,'produk-buket-mawar-merah.jpg',1,1),
(5,'Buket Wisuda Elegant','buket-wisuda-elegant','Buket wisuda cantik untuk momen kelulusan berkesan.',200000,500000,'produk-buket-wisuda.jpg',1,2),
(3,'Standing Flower Wedding','standing-flower-wedding-produk','Standing flower elegan untuk dekorasi pernikahan.',500000,1200000,'produk-standing-wedding.jpg',1,1),
(3,'Standing Flower Grand Opening','standing-flower-grand-opening-produk','Standing flower meriah untuk peresmian usaha.',450000,1000000,'produk-standing-grand-opening.jpg',1,2),
(4,'Karangan Bunga Custom Premium','karangan-bunga-custom-premium','Desain karangan bunga khusus sesuai kebutuhan pelanggan.',300000,1500000,'produk-custom-premium.jpg',0,1);

-- CITIES
CREATE TABLE cities (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(150) NOT NULL,
    slug VARCHAR(200) NOT NULL UNIQUE,
    province VARCHAR(150),
    tier TINYINT DEFAULT 1,
    description TEXT,
    landmark_notes TEXT,
    meta_title VARCHAR(255),
    meta_desc TEXT,
    sort_order INT DEFAULT 0,
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- AREAS
CREATE TABLE areas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    city_id INT NOT NULL,
    name VARCHAR(150) NOT NULL,
    slug VARCHAR(200) NOT NULL UNIQUE,
    description TEXT,
    landmarks TEXT,
    nearby_areas TEXT,
    meta_title VARCHAR(255),
    meta_desc TEXT,
    sort_order INT DEFAULT 0,
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (city_id) REFERENCES cities(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- SERVICE PAGES
CREATE TABLE service_pages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    slug VARCHAR(200) NOT NULL UNIQUE,
    h1_text VARCHAR(255),
    content LONGTEXT,
    meta_title VARCHAR(255),
    meta_desc TEXT,
    is_active TINYINT(1) DEFAULT 1,
    sort_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;
INSERT INTO service_pages (title,slug,h1_text,meta_title,meta_desc,sort_order) VALUES
('Toko Bunga Online 24 Jam Indonesia','toko-bunga-online-24-jam-indonesia','Toko Bunga Online 24 Jam Indonesia – Layanan Kirim Bunga Cepat Seluruh Kota','Toko Bunga Online 24 Jam Indonesia | Kirim Bunga Cepat – Chika Florist','Toko bunga online 24 jam Indonesia melayani bunga papan, buket bunga & standing flower.',1),
('Kirim Bunga Hari Ini','kirim-bunga-hari-ini','Kirim Bunga Hari Ini – Same Day Delivery Seluruh Indonesia','Kirim Bunga Hari Ini | Same Day Delivery – Chika Florist','Layanan kirim bunga hari ini dengan same day delivery ke seluruh Indonesia.',2),
('Florist Terdekat','florist-terdekat','Florist Terdekat – Toko Bunga Online Terpercaya Indonesia','Florist Terdekat | Toko Bunga Online – Chika Florist','Cari florist terdekat? Chika Florist melayani pengiriman bunga ke seluruh Indonesia.',3),
('Pesan Bunga Online','pesan-bunga-online','Pesan Bunga Online – Mudah, Cepat & Terpercaya','Pesan Bunga Online | Mudah & Cepat – Chika Florist','Cara pesan bunga online mudah dan cepat.',4);

-- TESTIMONIALS
CREATE TABLE testimonials (
    id INT AUTO_INCREMENT PRIMARY KEY,
    customer_name VARCHAR(150) NOT NULL,
    customer_city VARCHAR(100),
    rating TINYINT DEFAULT 5,
    content TEXT NOT NULL,
    image VARCHAR(255),
    is_active TINYINT(1) DEFAULT 1,
    sort_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;
INSERT INTO testimonials (customer_name,customer_city,rating,content,sort_order) VALUES
('Sari Dewi','Jakarta',5,'Bunga papannya sangat cantik! Pengiriman tepat waktu dan kondisi bunga masih segar. Pasti order lagi!',1),
('Budi Santoso','Bandung',5,'Pelayanan admin sangat responsif, buket wisuda untuk adik saya sangat memuaskan. Recommended!',2),
('Fitri Rahayu','Surabaya',5,'Standing flower untuk pernikahan kami luar biasa indah. Tim florist sangat profesional.',3),
('Ahmad Fauzi','Semarang',5,'Pesan tengah malam langsung direspon, bunga papan grand opening sampai pagi. Mantap!',4),
('Maya Putri','Yogyakarta',5,'Buket mawar untuk anniversary sangat cantik, harga transparan, tidak ada biaya tersembunyi.',5),
('Rizki Pratama','Medan',5,'Kualitas bunga fresh dan desain elegan. Sangat puas dengan pelayanan Chika Florist.',6);

-- GALLERY
CREATE TABLE gallery (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255),
    image VARCHAR(255) NOT NULL,
    category VARCHAR(100),
    alt_text VARCHAR(255),
    sort_order INT DEFAULT 0,
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;
INSERT INTO gallery (title,image,category,alt_text,sort_order) VALUES
('Bunga Papan Grand Opening','gallery-1.jpg','Bunga Papan','bunga papan grand opening elegan Chika Florist',1),
('Buket Mawar Merah','gallery-2.jpg','Buket Bunga','buket mawar merah cantik Chika Florist',2),
('Standing Flower Wedding','gallery-3.jpg','Standing Flower','standing flower pernikahan elegan Chika Florist',3),
('Bunga Papan Duka Cita','gallery-4.jpg','Bunga Papan','bunga papan duka cita bermartabat Chika Florist',4),
('Buket Wisuda Colorful','gallery-5.jpg','Buket Bunga','buket wisuda colorful Chika Florist',5),
('Karangan Bunga Custom','gallery-6.jpg','Custom','karangan bunga custom premium Chika Florist',6);

-- FAQ
CREATE TABLE faqs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    question TEXT NOT NULL,
    answer TEXT NOT NULL,
    category VARCHAR(100) DEFAULT 'Umum',
    sort_order INT DEFAULT 0,
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;
INSERT INTO faqs (question,answer,category,sort_order) VALUES
('Apakah benar bisa pesan tengah malam?','Ya, layanan pemesanan Chika Florist tersedia 24 jam setiap hari termasuk hari libur dan tengah malam.','Layanan',1),
('Apakah bisa kirim bunga di hari yang sama?','Bisa, kami menyediakan layanan same day delivery di banyak kota besar di Indonesia.','Pengiriman',2),
('Apakah melayani seluruh Indonesia?','Ya, kami melayani pengiriman bunga ke berbagai kota di seluruh Indonesia melalui jaringan florist terpercaya.','Pengiriman',3),
('Apakah bisa request desain bunga?','Tentu, kami menerima request desain custom sesuai kebutuhan acara, warna favorit, maupun tema tertentu.','Produk',4),
('Bagaimana cara memesan bunga?','Pilih produk di website, hubungi admin via WhatsApp, kirim detail ucapan dan alamat pengiriman, pesanan langsung diproses.','Pemesanan',5),
('Apakah harga sudah termasuk ongkir?','Harga produk belum termasuk ongkos kirim. Ongkir akan dikonfirmasi oleh admin sesuai lokasi pengiriman.','Harga',6),
('Berapa lama proses pengiriman?','Untuk same day delivery, bunga dikirim di hari yang sama. Estimasi waktu bergantung jarak lokasi pengiriman.','Pengiriman',7),
('Apakah tersedia layanan untuk perusahaan?','Ya, kami melayani kebutuhan bunga untuk event perusahaan, grand opening, maupun pesanan rutin korporat.','Layanan',8);
