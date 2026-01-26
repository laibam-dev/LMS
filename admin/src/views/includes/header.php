<?php
if (session_status() === PHP_SESSION_NONE) session_start();
$admin = $_SESSION['user'] ?? null;
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title><?= htmlspecialchars($title ?? 'Admin · Polymath Path Institute') ?></title>

  <script src="https://cdn.tailwindcss.com"></script>
  <script src="https://unpkg.com/lucide@0.286.0/dist/lucide.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

  <style>
    :root { --sidebar-w: 280px; }
    .sidebar { width: var(--sidebar-w); }
    main { margin-left: var(--sidebar-w); }
  </style>
</head>
<body class="bg-slate-50 text-slate-800 antialiased">
  <div class="flex">
    <aside class="sidebar fixed left-0 top-0 h-full bg-white border-r border-slate-200 px-6 py-8">
      <div class="flex items-center gap-3 mb-8">
        <div class="bg-indigo-600 text-white rounded-lg p-2">
          <svg data-lucide="award" class="w-6 h-6"></svg>
        </div>
        <div>
          <h1 class="text-lg font-semibold text-slate-900">Polymath Path</h1>
          <p class="text-xs text-slate-500">Admin Console</p>
        </div>
      </div>

      <nav class="space-y-1">
        <a href="/LMS/admin/src/views/dashboard.php" class="flex items-center gap-3 px-3 py-2 rounded-md hover:bg-slate-50 text-slate-700">
          <svg data-lucide="home" class="w-5 h-5"></svg><span>Dashboard</span>
        </a>

        <a href="/LMS/admin/src/views/users.php" class="flex items-center gap-3 px-3 py-2 rounded-md hover:bg-slate-50 text-slate-700">
          <svg data-lucide="users" class="w-5 h-5"></svg><span>User Management</span>
        </a>

        <a href="/LMS/admin/src/views/analytics.php" class="flex items-center gap-3 px-3 py-2 rounded-md hover:bg-slate-50 text-slate-700">
          <svg data-lucide="bar-chart-2" class="w-5 h-5"></svg><span>Analytics</span>
        </a>

        <a href="/LMS/admin/src/views/settings.php" class="flex items-center gap-3 px-3 py-2 rounded-md hover:bg-slate-50 text-slate-700">
          <svg data-lucide="settings" class="w-5 h-5"></svg><span>Settings</span>
        </a>
      </nav>

      <div class="mt-8 pt-6 border-t border-slate-100 text-sm text-slate-500">
        <div class="flex items-center gap-3">
          <?php
            $adminAvatar = $admin['avatar'] ?? null;
            $adminInitial = htmlspecialchars(strtoupper(substr($admin['name'] ?? 'A', 0, 1)));
          ?>
          <?php if ($adminAvatar): ?>
            <img src="<?= htmlspecialchars($adminAvatar) ?>" class="w-9 h-9 rounded-full object-cover" alt="avatar">
          <?php else: ?>
            <div class="w-9 h-9 rounded-full bg-indigo-50 text-indigo-700 flex items-center justify-center text-sm font-semibold"><?= $adminInitial ?></div>
          <?php endif; ?>
          <div>
            <div class="text-slate-900 font-medium"><?= htmlspecialchars($admin['name'] ?? 'Admin') ?></div>
            <div class="text-xs">Administrator</div>
          </div>
        </div>
      </div>
    </aside>

    <main class="min-h-screen p-8 lg:ml-[var(--sidebar-w)]">

      <!-- Top navbar -->
      <header class="flex items-center justify-between mb-6">
        <div>
          <h2 class="text-2xl font-semibold"><?= htmlspecialchars($title ?? 'Dashboard') ?></h2>
        </div>

        <div class="flex items-center gap-4">
          <div class="relative">
            <input id="globalSearch" type="text" placeholder="Search..." class="px-4 py-2 rounded-lg border border-slate-200 bg-white shadow-sm w-80">
            <div class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400">
              <svg data-lucide="search" class="w-4 h-4"></svg>
            </div>
          </div>

          <div class="relative">
            <button id="profileToggle" aria-expanded="false" class="flex items-center gap-3 bg-white border border-slate-100 px-3 py-1 rounded-full shadow-sm">
              <?php if ($adminAvatar): ?>
                <img src="<?= htmlspecialchars($adminAvatar) ?>" class="w-8 h-8 rounded-full object-cover" alt="avatar">
              <?php else: ?>
                <div class="w-8 h-8 rounded-full bg-indigo-50 text-indigo-700 flex items-center justify-center text-sm font-semibold"><?= $adminInitial ?></div>
              <?php endif; ?>
              <span class="text-sm"><?= htmlspecialchars($admin['name'] ?? 'Admin') ?></span>
              <svg data-lucide="chevron-down" class="w-4 h-4 text-slate-400"></svg>
            </button>

            <div id="profileMenu" class="hidden absolute right-0 mt-2 w-44 bg-white border border-slate-200 rounded-md shadow-lg py-2 z-50">
              <a href="/LMS/admin/src/views/settings.php" class="block px-4 py-2 text-sm text-slate-700 hover:bg-slate-50">My Profile</a>
              <a href="/LMS/admin/src/views/settings.php" class="block px-4 py-2 text-sm text-slate-700 hover:bg-slate-50">Settings</a>
              <form method="POST" action="/LMS/admin/src/controllers/LoginController.php?action=logout" class="m-0">
                <button type="submit" class="w-full text-left px-4 py-2 text-sm text-rose-600 hover:bg-slate-50">Logout</button>
              </form>
            </div>
          </div>
        </div>
      </header>
