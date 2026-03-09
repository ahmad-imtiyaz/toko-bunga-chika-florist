<?php
require_once __DIR__ . '/../includes/config.php';
if (isset($_GET['logout'])) { session_destroy(); redirect('/admin/'); }
if (isAdminLoggedIn()) redirect('/admin/dashboard');
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = clean($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    if ($username && $password) {
        $pdo  = getDB();
        $stmt = $pdo->prepare("SELECT * FROM admin_users WHERE username=? AND is_active=1");
        $stmt->execute([$username]);
        $user = $stmt->fetch();
        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['admin_id']   = $user['id'];
            $_SESSION['admin_name'] = $user['full_name'];
            $_SESSION['admin_user'] = $user['username'];
            redirect('/admin/dashboard');
        } else { $error = 'Username atau password salah.'; }
    } else { $error = 'Harap isi semua field.'; }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Login Admin – Chika Florist</title>
<meta name="robots" content="noindex,nofollow">
<script src="https://cdn.tailwindcss.com"></script>
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700&family=Lato:wght@400;700&display=swap" rel="stylesheet">
<style>body{font-family:'Lato',sans-serif}.font-display{font-family:'Playfair Display',serif}</style>
</head>
<body class="min-h-screen bg-gradient-to-br from-rose-50 via-white to-amber-50 flex items-center justify-center px-4">
<div class="w-full max-w-sm">
  <div class="text-center mb-8">
    <div class="w-16 h-16 bg-rose-600 rounded-2xl flex items-center justify-center mx-auto mb-4 shadow-lg">
      <span class="text-white text-2xl">🌸</span>
    </div>
    <h1 class="font-display text-2xl font-bold text-gray-900">Chika Florist</h1>
    <p class="text-gray-500 text-sm mt-1">Panel Admin</p>
  </div>
  <div class="bg-white rounded-2xl shadow-xl border border-rose-100 p-8">
    <h2 class="font-display text-lg font-bold text-gray-800 mb-6 text-center">Masuk ke Admin</h2>
    <?php if ($error): ?>
    <div class="bg-red-50 border border-red-200 text-red-600 text-sm rounded-lg px-4 py-3 mb-5"><?= clean($error) ?></div>
    <?php endif; ?>
    <form method="POST">
      <div class="mb-4">
        <label class="block text-sm font-semibold text-gray-700 mb-1.5">Username</label>
        <input type="text" name="username" value="<?= clean($_POST['username'] ?? '') ?>"
               class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:outline-none focus:border-rose-400 text-sm" required autofocus>
      </div>
      <div class="mb-6">
        <label class="block text-sm font-semibold text-gray-700 mb-1.5">Password</label>
        <input type="password" name="password" class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:outline-none focus:border-rose-400 text-sm" required>
      </div>
      <button type="submit" class="w-full bg-rose-600 hover:bg-rose-700 text-white font-bold py-2.5 rounded-xl transition-colors">Masuk</button>
    </form>
  </div>
  <p class="text-center text-xs text-gray-400 mt-4">Chika Florist Admin Panel © <?= date('Y') ?></p>
</div>
</body>
</html>
