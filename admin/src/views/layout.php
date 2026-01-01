<?php
if (session_status() === PHP_SESSION_NONE) session_start();

// capture flash messages (then clear)
$flash_success = $_SESSION['flash_success'] ?? null;
$flash_error = $_SESSION['flash_error'] ?? null;
$flash_info = $_SESSION['flash_info'] ?? null;
unset($_SESSION['flash_success'], $_SESSION['flash_error'], $_SESSION['flash_info']);
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title><?= htmlspecialchars($title ?? 'Admin · Polymath Path Institute') ?></title>

  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    tailwind.config = {
      theme: {
        extend: {
          colors: {
            brand: { 50: '#eef2ff', 500: '#6366f1', 600: '#4f46e5' },
            slate: { 50: '#f8fafc', 700: '#334155' }
          }
        }
      }
    }
  </script>

  <script src="https://unpkg.com/lucide@0.286.0/dist/lucide.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

  <style>
    :root { --sidebar-w: 280px; }
    .sidebar { width: var(--sidebar-w); }
    main { margin-left: var(--sidebar-w); }

    /* Toast styles */
    .toast-enter { opacity: 0; transform: translateY(8px) scale(.98); }
    .toast-enter-active { transition: all .25s ease; opacity: 1; transform: translateY(0) scale(1); }
    .toast-leave { opacity: 1; }
    .toast-leave-active { transition: all .2s ease; opacity: 0; transform: translateY(-8px) scale(.98); }
  </style>
</head>
<body class="bg-slate-50 text-slate-800 antialiased">
  <div class="flex">
    <!-- Sidebar -->
    <aside id="sidebar" class="sidebar fixed left-0 top-0 h-full bg-white border-r border-slate-200 px-6 py-8 transform lg:translate-x-0 lg:static transition-transform">
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
        <a href="/LMS/admin" class="flex items-center gap-3 px-3 py-2 rounded-md hover:bg-slate-50 text-slate-700">
          <svg data-lucide="home" class="w-5 h-5"></svg><span>Dashboard</span>
        </a>

        <a href="/LMS/admin/users" class="flex items-center gap-3 px-3 py-2 rounded-md hover:bg-slate-50 text-slate-700">
          <svg data-lucide="users" class="w-5 h-5"></svg><span>User Management</span>
        </a>

        <a href="/LMS/admin/courses" class="flex items-center gap-3 px-3 py-2 rounded-md hover:bg-slate-50 text-slate-700">
          <svg data-lucide="book-open" class="w-5 h-5"></svg><span>Courses</span>
        </a>

        <a href="/LMS/admin/analytics" class="flex items-center gap-3 px-3 py-2 rounded-md hover:bg-slate-50 text-slate-700">
          <svg data-lucide="bar-chart-2" class="w-5 h-5"></svg><span>Analytics</span>
        </a>

        <a href="/LMS/admin/profile" class="flex items-center gap-3 px-3 py-2 rounded-md hover:bg-slate-50 text-slate-700">
          <svg data-lucide="user" class="w-5 h-5"></svg><span>Profile</span>
        </a>

        <a href="/LMS/admin/logs" class="flex items-center gap-3 px-3 py-2 rounded-md hover:bg-slate-50 text-slate-700">
          <svg data-lucide="list" class="w-5 h-5"></svg><span>Activity Logs</span>
        </a>

        <a href="/LMS/admin/logout" class="flex items-center gap-3 px-3 py-2 rounded-md hover:bg-slate-50 text-rose-600">
          <svg data-lucide="log-out" class="w-5 h-5"></svg><span>Logout</span>
        </a>
      </nav>

      <div class="mt-8 pt-6 border-t border-slate-100 text-sm text-slate-500">
        <div class="flex items-center gap-3">
          <img src="<?= htmlspecialchars($admin['avatar'] ?? 'https://i.pravatar.cc/48') ?>" class="w-9 h-9 rounded-full object-cover" alt="avatar">
          <div>
            <div class="text-slate-900 font-medium"><?= htmlspecialchars($admin['name'] ?? 'Admin') ?></div>
            <div class="text-xs">Administrator</div>
          </div>
        </div>
      </div>
    </aside>

    <!-- Main -->
    <main class="min-h-screen p-8 flex-1 lg:ml-[var(--sidebar-w)]">
      <!-- Mobile top bar -->
      <div class="lg:hidden mb-4 flex items-center justify-between">
        <button id="mobileToggle" class="p-2 rounded-md bg-white border"><svg data-lucide="menu" class="w-5 h-5"></svg></button>
        <div class="text-lg font-semibold"><?= htmlspecialchars($title ?? 'Admin') ?></div>
        <div></div>
      </div>

      <!-- Top navbar (existing) -->
      <header class="flex items-center justify-between mb-6">
        <div class="flex items-center gap-4">
          <h2 class="text-2xl font-semibold"><?= htmlspecialchars($title ?? 'Overview') ?></h2>
        </div>

        <div class="flex items-center gap-4">
          <div class="relative">
            <input id="globalSearch" type="text" placeholder="Search..." class="px-4 py-2 rounded-lg border border-slate-200 bg-white shadow-sm w-80">
            <div class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400">
              <svg data-lucide="search" class="w-4 h-4"></svg>
            </div>
          </div>

          <button class="p-2 rounded-md hover:bg-slate-100" title="Notifications">
            <svg data-lucide="bell" class="w-5 h-5"></svg>
          </button>

          <!-- Profile dropdown -->
          <div class="relative">
            <button id="profileToggle" aria-expanded="false" class="flex items-center gap-3 bg-white border border-slate-100 px-3 py-1 rounded-full shadow-sm">
              <img src="<?= htmlspecialchars($admin['avatar'] ?? 'https://i.pravatar.cc/48') ?>" class="w-8 h-8 rounded-full object-cover" alt="avatar">
              <span class="text-sm"><?= htmlspecialchars($admin['name'] ?? 'Admin') ?></span>
              <svg data-lucide="chevron-down" class="w-4 h-4 text-slate-400"></svg>
            </button>

            <div id="profileMenu" class="hidden absolute right-0 mt-2 w-44 bg-white border border-slate-200 rounded-md shadow-lg py-2 z-50">
              <a href="/LMS/admin/profile" class="block px-4 py-2 text-sm text-slate-700 hover:bg-slate-50">My Profile</a>
              <a href="/LMS/admin/src/views/settings.php" class="block px-4 py-2 text-sm text-slate-700 hover:bg-slate-50">Settings</a>
              <form method="POST" action="/LMS/admin/logout" class="m-0">
                <button type="submit" class="w-full text-left px-4 py-2 text-sm text-rose-600 hover:bg-slate-50">Logout</button>
              </form>
            </div>
          </div>
        </div>
      </header>

      <!-- Page content -->
      <?= $content ?>

    </main>
  </div>

  <!-- Toast container -->
  <div id="toastContainer" class="fixed right-4 bottom-6 z-50 flex flex-col gap-3 items-end"></div>

<script>
  if (window.lucide) lucide.replace();

  // mobile sidebar toggle
  document.getElementById('mobileToggle')?.addEventListener('click', function(){
    const sb = document.getElementById('sidebar');
    if (!sb) return;
    sb.classList.toggle('-translate-x-full');
  });

  // profile dropdown toggle + outside click close
  (function(){
    const btn = document.getElementById('profileToggle');
    const menu = document.getElementById('profileMenu');

    if (!btn || !menu) return;

    btn.addEventListener('click', (e) => {
      const open = !menu.classList.contains('hidden');
      menu.classList.toggle('hidden');
      btn.setAttribute('aria-expanded', String(!open));
    });

    document.addEventListener('click', (e) => {
      if (!menu.classList.contains('hidden') && !btn.contains(e.target) && !menu.contains(e.target)) {
        menu.classList.add('hidden');
        btn.setAttribute('aria-expanded', 'false');
      }
    });

    // close on Escape
    document.addEventListener('keydown', (e) => {
      if (e.key === 'Escape') {
        menu.classList.add('hidden');
        btn?.setAttribute('aria-expanded', 'false');
      }
    });
  })();

  // showToast(type, message, options)
  window.showToast = function(type = 'info', message = '', opts = {}) {
    const colors = {
      success: ['bg-emerald-50','text-emerald-800','border-emerald-100'],
      error: ['bg-rose-50','text-rose-800','border-rose-100'],
      info: ['bg-indigo-50','text-indigo-800','border-indigo-100']
    };
    const [bg, text, border] = colors[type] || colors.info;
    const container = document.getElementById('toastContainer');
    if (!container) return;

    const el = document.createElement('div');
    el.className = `max-w-sm w-full ${bg} ${text} ${border} border px-4 py-3 rounded-lg shadow-lg flex items-start gap-3 toast-enter`;
    el.innerHTML = `
      <div class="flex-shrink-0 mt-0.5">
        ${type === 'success' ? '<svg data-lucide="check-circle" class="w-5 h-5"></svg>' : type === 'error' ? '<svg data-lucide="x-circle" class="w-5 h-5"></svg>' : '<svg data-lucide="info" class="w-5 h-5"></svg>'}
      </div>
      <div class="flex-1 text-sm">${message}</div>
      <button class="ml-3 pl-2 text-sm opacity-80">✕</button>
    `;
    container.appendChild(el);
    if (window.lucide) lucide.replace();

    // enter animation
    requestAnimationFrame(()=> el.classList.add('toast-enter-active'));

    // close handlers
    const close = () => {
      el.classList.remove('toast-enter-active');
      el.classList.add('toast-leave-active');
      setTimeout(()=> el.remove(), 220);
    };
    el.querySelector('button')?.addEventListener('click', close);
    const ttl = opts.duration ?? 5000;
    setTimeout(close, ttl);
  };

  // show any server-side flash (injected by PHP)
  document.addEventListener('DOMContentLoaded', function(){
    <?php if ($flash_success): ?>
      showToast('success', <?= json_encode($flash_success) ?>, { duration: 6000 });
    <?php endif; ?>
    <?php if ($flash_error): ?>
      showToast('error', <?= json_encode($flash_error) ?>, { duration: 6000 });
    <?php endif; ?>
    <?php if ($flash_info): ?>
      showToast('info', <?= json_encode($flash_info) ?>, { duration: 6000 });
    <?php endif; ?>
  });
</script>
</body>
</html>