<?php
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}

$flash = $_SESSION['flash_error'] ?? null;
$csrf = $_SESSION['csrf_login'] ?? bin2hex(random_bytes(16));
$_SESSION['csrf_login'] = $csrf;
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>Admin Login · Polymath Path Institute</title>

  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    tailwind.config = { theme: { extend: { colors: { brand: { 50:'#eef2ff', 500:'#6366f1' } } } } };
  </script>

  <style>
    .glass {
      background: linear-gradient(135deg, rgba(255,255,255,0.6), rgba(255,255,255,0.35));
      backdrop-filter: blur(8px) saturate(120%);
      -webkit-backdrop-filter: blur(8px) saturate(120%);
      border: 1px solid rgba(255,255,255,0.45);
    }
  </style>
</head>
<body class="min-h-screen bg-slate-50 flex items-center justify-center">
  <div class="max-w-6xl w-full grid grid-cols-1 lg:grid-cols-2 gap-8 p-6">
    <!-- Left: Branding / Illustration -->
    <div class="hidden lg:flex flex-col justify-center px-12">
      <div class="mb-6">
        <div class="w-14 h-14 bg-indigo-600 text-white rounded-lg flex items-center justify-center text-2xl font-bold">PP</div>
      </div>
      <h1 class="text-4xl font-extrabold text-slate-900 mb-4">Polymath Path Institute</h1>
      <p class="text-slate-600 mb-8">Professional LMS for lifelong learners. Admin console — secure access to manage users, courses and analytics.</p>
      <img src="https://images.unsplash.com/photo-1521737604893-d14cc237f11d?q=80&w=1200&auto=format&fit=crop&ixlib=rb-4.0.3&s=8b7e8e7f7a2b9baf3f1f3b9f6a3b8b7d" alt="Illustration" class="rounded-xl shadow-lg object-cover w-full h-72">
    </div>

    <!-- Right: Login Form -->
    <div class="flex items-center justify-center">
      <form action="/LMS/admin/src/controllers/LoginController.php?action=login" method="post" class="glass p-8 rounded-2xl w-full max-w-md shadow-lg">
        <h2 class="text-2xl font-semibold text-slate-900 mb-2">Admin Sign in</h2>
        <p class="text-sm text-slate-500 mb-6">Enter your admin credentials to access the control panel.</p>

        <?php if ($flash): ?>
          <div class="mb-4 text-sm text-white bg-rose-600 px-3 py-2 rounded"><?= htmlspecialchars($flash) ?></div>
          <?php unset($_SESSION['flash_error']); ?>
        <?php endif; ?>

        <input type="hidden" name="csrf" value="<?= htmlspecialchars($csrf) ?>">

        <label class="block mb-3">
          <span class="text-sm text-slate-600">Email</span>
          <input name="email" type="email" required class="mt-1 block w-full rounded-lg border border-slate-200 px-3 py-2 bg-white">
        </label>

        <label class="block mb-4">
          <span class="text-sm text-slate-600">Password</span>
          <input name="password" type="password" required class="mt-1 block w-full rounded-lg border border-slate-200 px-3 py-2 bg-white">
        </label>

        <div class="flex items-center justify-between mb-6">
          <label class="inline-flex items-center text-sm text-slate-600">
            <input type="checkbox" name="remember" class="mr-2"> Remember me
          </label>
          <a href="#" class="text-sm text-indigo-600 hover:underline">Forgot?</a>
        </div>

        <button type="submit" class="w-full bg-indigo-600 text-white py-2 rounded-lg font-medium shadow">Sign in</button>

        <p class="mt-6 text-xs text-slate-400 text-center">Unauthorized access is prohibited. Contact the system administrator for assistance.</p>
      </form>
    </div>
  </div>
</body>
</html>