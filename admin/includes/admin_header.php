<?php
require_once __DIR__ . '/../../includes/config.php';
requireAdminLogin();
$current_page = basename($_SERVER['PHP_SELF'], '.php');
$admin_name   = $_SESSION['admin_name'] ?? 'Admin';
$admin_title  = $admin_title ?? 'Admin Panel';

$nav_items = [
    ['href'=>'/admin/dashboard','label'=>'Dashboard','icon'=>'🏠','page'=>'dashboard'],
    ['href'=>'/admin/produk','label'=>'Produk','icon'=>'🌺','page'=>'produk'],
    ['href'=>'/admin/kategori','label'=>'Kategori','icon'=>'📁','page'=>'kategori'],
    ['href'=>'/admin/kota','label'=>'Kota','icon'=>'🏙️','page'=>'kota'],
    ['href'=>'/admin/area','label'=>'Area','icon'=>'📍','page'=>'area'],
    ['href'=>'/admin/layanan','label'=>'Layanan','icon'=>'⚡','page'=>'layanan'],
    ['href'=>'/admin/testimoni','label'=>'Testimoni','icon'=>'💬','page'=>'testimoni'],
    ['href'=>'/admin/galeri','label'=>'Galeri','icon'=>'🖼️','page'=>'galeri'],
    ['href'=>'/admin/faq','label'=>'FAQ','icon'=>'❓','page'=>'faq'],
    ['href'=>'/admin/pengaturan','label'=>'Pengaturan','icon'=>'⚙️','page'=>'pengaturan'],
];
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= clean($admin_title) ?> – Admin Chika Florist</title>
<meta name="robots" content="noindex,nofollow">
<script src="https://cdn.tailwindcss.com"></script>
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700&family=Lato:wght@400;700&display=swap" rel="stylesheet">
<style>
body{font-family:'Lato',sans-serif}
.font-display{font-family:'Playfair Display',serif}
.sidebar-link{display:flex;align-items:center;gap:.6rem;padding:.5rem 1rem;border-radius:.5rem;font-size:.875rem;color:#374151;transition:all .15s;text-decoration:none}
.sidebar-link:hover{background:#fff1f2;color:#e11d48}
.sidebar-link.active{background:#e11d48;color:white;font-weight:600}
.admin-table th{background:#fff1f2;color:#be123c;font-weight:600;font-size:.75rem;text-transform:uppercase;letter-spacing:.05em;padding:.65rem 1rem;text-align:left}
.admin-table td{padding:.65rem 1rem;border-bottom:1px solid #fef2f2;font-size:.875rem;vertical-align:middle}
.admin-table tr:hover td{background:#fff8f8}
.badge-active{background:#dcfce7;color:#15803d;font-size:.7rem;padding:.15rem .6rem;border-radius:9999px;font-weight:600;display:inline-block}
.badge-inactive{background:#fef2f2;color:#dc2626;font-size:.7rem;padding:.15rem .6rem;border-radius:9999px;font-weight:600;display:inline-block}
.form-input{width:100%;padding:.55rem .85rem;border:1.5px solid #fecdd3;border-radius:.5rem;font-size:.875rem;color:#374151;outline:none;transition:border-color .15s}
.form-input:focus{border-color:#e11d48;box-shadow:0 0 0 3px rgba(225,29,72,.08)}
.form-label{display:block;font-size:.85rem;font-weight:600;color:#374151;margin-bottom:.35rem}
.btn-primary{background:#e11d48;color:white;font-weight:600;padding:.5rem 1.25rem;border-radius:.5rem;font-size:.875rem;transition:background .15s;border:none;cursor:pointer;text-decoration:none;display:inline-block}
.btn-primary:hover{background:#be123c}
.btn-secondary{background:#f3f4f6;color:#374151;font-weight:600;padding:.5rem 1.25rem;border-radius:.5rem;font-size:.875rem;transition:background .15s;border:none;cursor:pointer;text-decoration:none;display:inline-block}
.btn-secondary:hover{background:#e5e7eb}
.btn-danger{background:#fef2f2;color:#dc2626;font-weight:600;padding:.4rem 1rem;border-radius:.5rem;font-size:.8rem;transition:all .15s;border:1px solid #fecaca;cursor:pointer;text-decoration:none;display:inline-block}
.btn-danger:hover{background:#dc2626;color:white}
</style>
</head>
<body class="bg-gray-50">
<div class="flex min-h-screen">

<!-- Sidebar -->
<aside class="w-56 bg-white border-r border-rose-100 flex flex-col fixed h-full z-30 shadow-sm">
  <div class="p-4 border-b border-rose-100">
    <a href="/admin/dashboard" class="flex items-center gap-2">
      <div class="w-8 h-8 bg-rose-600 rounded-lg flex items-center justify-center"><span class="text-white text-sm">🌸</span></div>
      <div>
        <p class="font-display font-bold text-gray-900 text-sm leading-tight">Chika Florist</p>
        <p class="text-xs text-gray-400">Admin Panel</p>
      </div>
    </a>
  </div>
  <nav class="flex-1 p-3 overflow-y-auto">
    <?php foreach ($nav_items as $item): ?>
    <a href="<?= $item['href'] ?>" class="sidebar-link <?= ($current_page === $item['page']) ? 'active' : '' ?>">
      <span><?= $item['icon'] ?></span> <?= $item['label'] ?>
    </a>
    <?php endforeach; ?>
  </nav>
  <div class="p-3 border-t border-rose-100">
    <div class="flex items-center gap-2 mb-2 px-2">
      <div class="w-7 h-7 bg-rose-100 rounded-full flex items-center justify-center text-rose-600 text-xs font-bold"><?= mb_strtoupper(mb_substr($admin_name,0,1)) ?></div>
      <span class="text-xs text-gray-600 truncate"><?= clean($admin_name) ?></span>
    </div>
    <a href="/admin/?logout=1" class="sidebar-link" style="color:#dc2626">
      <span>🚪</span> Logout
    </a>
  </div>
</aside>

<!-- Main -->
<main class="flex-1 ml-56">
  <div class="bg-white border-b border-rose-100 px-6 py-3 flex items-center justify-between sticky top-0 z-20 shadow-sm">
    <h1 class="font-display font-bold text-gray-800 text-base"><?= clean($admin_title) ?></h1>
    <a href="/" target="_blank" class="text-rose-600 hover:underline text-xs">Lihat Website →</a>
  </div>

  <?php if (!empty($_SESSION['success'])): ?>
  <div class="mx-6 mt-4 bg-green-50 border border-green-200 text-green-700 text-sm rounded-lg px-4 py-3 flex justify-between">
    <span><?= clean($_SESSION['success']) ?></span>
    <button onclick="this.parentElement.remove()">✕</button>
  </div>
  <?php unset($_SESSION['success']); endif; ?>

  <?php if (!empty($_SESSION['error'])): ?>
  <div class="mx-6 mt-4 bg-red-50 border border-red-200 text-red-600 text-sm rounded-lg px-4 py-3 flex justify-between">
    <span><?= clean($_SESSION['error']) ?></span>
    <button onclick="this.parentElement.remove()">✕</button>
  </div>
  <?php unset($_SESSION['error']); endif; ?>

  <div class="p-6">
