<?php
<?php
require_once __DIR__ . '/../middleware/auth.php';
session_start();
$flash = $_SESSION['flash_profile'] ?? null;
unset($_SESSION['flash_profile']);
if (empty($_SESSION['csrf_profile'])) $_SESSION['csrf_profile'] = bin2hex(random_bytes(16));
$csrf = $_SESSION['csrf_profile'];
// $user fetched by controller when rendering ($user)
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>Profile · Polymath Path Institute</title>

  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    tailwind.config = { theme: { extend: { colors: { brand: { 50:'#eef2ff', 500:'#6366f1' } } } } };
  </script>

  <style>
    .glass { background: linear-gradient(135deg, rgba(255,255,255,0.6), rgba(255,255,255,0.35)); backdrop-filter: blur(8px) saturate(120%); }
  </style>
</head>
<body class="bg-slate-50 text-slate-800 antialiased">
  <div class="max-w-4xl mx-auto p-8">
    <h1 class="text-2xl font-semibold mb-4">Profile Settings</h1>

    <?php if ($flash): ?>
      <div class="mb-4 text-sm text-white bg-indigo-600 px-3 py-2 rounded"><?= htmlspecialchars($flash) ?></div>
    <?php endif; ?>

    <div class="bg-white rounded-lg border border-slate-200 p-6 glass shadow-sm">
      <form action="/LMS/admin/src/controllers/ProfileController.php?action=update" method="post" enctype="multipart/form-data" class="grid grid-cols-1 gap-4">
        <input type="hidden" name="csrf" value="<?= htmlspecialchars($csrf) ?>">

        <div class="flex items-center gap-6">
          <div>
            <img src="<?= htmlspecialchars($user['avatar'] ?? ($_SESSION['user']['avatar'] ?? '/LMS/admin/public/uploads/avatars/default.png')) ?>" alt="avatar" class="w-20 h-20 rounded-full object-cover border">
          </div>
          <div>
            <label class="text-sm text-slate-600">Change profile picture</label>
            <input type="file" name="avatar" accept="image/png,image/jpeg,image/webp" class="mt-2 block">
            <p class="text-xs text-slate-400 mt-2">PNG/JPG/WEBP, max 2MB.</p>
          </div>
        </div>

        <label class="block">
          <span class="text-sm text-slate-600">Name</span>
          <input name="name" type="text" value="<?= htmlspecialchars($user['name'] ?? $_SESSION['user']['name'] ?? '') ?>" required class="mt-1 block w-full rounded-lg border border-slate-200 px-3 py-2 bg-white">
        </label>

        <label class="block">
          <span class="text-sm text-slate-600">Email</span>
          <input name="email" type="email" value="<?= htmlspecialchars($user['email'] ?? $_SESSION['user']['email'] ?? '') ?>" required class="mt-1 block w-full rounded-lg border border-slate-200 px-3 py-2 bg-white">
        </label>

        <label class="block">
          <span class="text-sm text-slate-600">New Password <span class="text-xs text-slate-400">(leave blank to keep current)</span></span>
          <input name="password" type="password" class="mt-1 block w-full rounded-lg border border-slate-200 px-3 py-2 bg-white">
        </label>

        <div class="flex items-center gap-3 mt-2">
          <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded-lg shadow">Save changes</button>
          <a href="/LMS/admin/" class="text-sm text-slate-600 hover:underline">Back to dashboard</a>
        </div>
      </form>
    </div>
  </div>
</body>
</html>