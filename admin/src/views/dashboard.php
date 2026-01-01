<?php
// Expects:
// $admin (array) - ['name','avatar']
// $stats (array) - ['total_students','active_courses','total_instructors','monthly_revenue']
// $labels (array) - chart labels
// $dataPoints (array) - chart data
// $recentUsers (array) - last 5 users: ['id','name','email','role','status','created_at']
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>Admin Dashboard · Polymath Path Institute</title>

  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    tailwind.config = {
      theme: {
        extend: {
          colors: {
            indigo: {
              600: '#4f46e5'
            },
            slate: {
              50: '#f8fafc',
              700: '#334155'
            }
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
  </style>
</head>
<body class="bg-slate-50 text-slate-800 antialiased">

  <!-- Sidebar -->
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
      <a href="/admin" class="flex items-center gap-3 px-3 py-2 rounded-md bg-indigo-50 text-indigo-700">
        <svg data-lucide="home" class="w-5 h-5"></svg><span class="font-medium">Dashboard</span>
      </a>

      <a href="/admin/users" class="flex items-center gap-3 px-3 py-2 rounded-md hover:bg-slate-50 text-slate-700">
        <svg data-lucide="users" class="w-5 h-5"></svg><span>User Management</span>
      </a>

      <a href="/admin/analytics" class="flex items-center gap-3 px-3 py-2 rounded-md hover:bg-slate-50 text-slate-700">
        <svg data-lucide="bar-chart-2" class="w-5 h-5"></svg><span>Analytics</span>
      </a>

      <a href="/admin/settings" class="flex items-center gap-3 px-3 py-2 rounded-md hover:bg-slate-50 text-slate-700">
        <svg data-lucide="settings" class="w-5 h-5"></svg><span>Settings</span>
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
  <main class="min-h-screen p-8">
    <!-- Top navbar -->
    <header class="flex items-center justify-between mb-6">
      <div>
        <h2 class="text-2xl font-semibold">Dashboard</h2>
        <p class="text-sm text-slate-500">Overview of platform activity</p>
      </div>

      <div class="flex items-center gap-4">
        <div class="relative">
          <input id="search" type="text" placeholder="Search users, courses..." class="px-4 py-2 rounded-lg border border-slate-200 bg-white shadow-sm w-80">
          <div class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400">
            <svg data-lucide="search" class="w-4 h-4"></svg>
          </div>
        </div>

        <button class="p-2 rounded-md hover:bg-slate-100" title="Notifications">
          <svg data-lucide="bell" class="w-5 h-5"></svg>
        </button>

        <div class="relative">
          <button id="profileBtn" class="flex items-center gap-3 bg-white border border-slate-100 px-3 py-1 rounded-full shadow-sm">
            <img src="<?= htmlspecialchars($admin['avatar'] ?? 'https://i.pravatar.cc/48') ?>" class="w-8 h-8 rounded-full" alt="avatar">
            <span class="text-sm"><?= htmlspecialchars($admin['name'] ?? 'Admin') ?></span>
            <svg data-lucide="chevron-down" class="w-4 h-4 text-slate-400"></svg>
          </button>

          <div id="profileDropdown" class="hidden absolute right-0 mt-2 w-44 bg-white border border-slate-200 rounded-md shadow-lg py-2 z-20">
            <a href="/admin/profile" class="block px-4 py-2 text-sm text-slate-700 hover:bg-slate-50">Profile</a>
            <a href="/admin/settings" class="block px-4 py-2 text-sm text-slate-700 hover:bg-slate-50">Settings</a>
            <form method="POST" action="/admin/logout"><button type="submit" class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-slate-50">Sign out</button></form>
          </div>
        </div>
      </div>
    </header>

    <!-- Summary cards -->
    <section class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
      <div class="bg-white rounded-xl shadow border border-slate-200 p-5">
        <div class="flex items-center justify-between">
          <div>
            <h3 class="text-sm text-slate-500">Total Students</h3>
            <div class="mt-2 text-2xl font-semibold text-slate-900"><?= number_format($stats['total_students'] ?? 0) ?></div>
          </div>
          <div class="bg-indigo-50 p-3 rounded-md">
            <svg data-lucide="users" class="w-6 h-6 text-indigo-600"></svg>
          </div>
        </div>
      </div>

      <div class="bg-white rounded-xl shadow border border-slate-200 p-5">
        <div class="flex items-center justify-between">
          <div>
            <h3 class="text-sm text-slate-500">Active Courses</h3>
            <div class="mt-2 text-2xl font-semibold text-slate-900"><?= number_format($stats['active_courses'] ?? 0) ?></div>
          </div>
          <div class="bg-indigo-50 p-3 rounded-md">
            <svg data-lucide="book-open" class="w-6 h-6 text-indigo-600"></svg>
          </div>
        </div>
      </div>

      <div class="bg-white rounded-xl shadow border border-slate-200 p-5">
        <div class="flex items-center justify-between">
          <div>
            <h3 class="text-sm text-slate-500">Total Instructors</h3>
            <div class="mt-2 text-2xl font-semibold text-slate-900"><?= number_format($stats['total_instructors'] ?? 0) ?></div>
          </div>
          <div class="bg-indigo-50 p-3 rounded-md">
            <svg data-lucide="award" class="w-6 h-6 text-indigo-600"></svg>
          </div>
        </div>
      </div>

      <div class="bg-white rounded-xl shadow border border-slate-200 p-5">
        <div class="flex items-center justify-between">
          <div>
            <h3 class="text-sm text-slate-500">Monthly Revenue</h3>
            <div class="mt-2 text-2xl font-semibold text-slate-900">$<?= number_format($stats['monthly_revenue'] ?? 0, 2) ?></div>
          </div>
          <div class="bg-indigo-50 p-3 rounded-md">
            <svg data-lucide="dollar-sign" class="w-6 h-6 text-indigo-600"></svg>
          </div>
        </div>
      </div>
    </section>

    <!-- Analytics & Recent Activity -->
    <section class="grid grid-cols-1 lg:grid-cols-2 gap-6">
      <div class="bg-white rounded-xl border border-slate-200 p-5 shadow">
        <div class="flex items-center justify-between mb-4">
          <h3 class="text-lg font-semibold">Real-time Analytics</h3>
          <div class="text-sm text-slate-500">Student Growth</div>
        </div>
        <div class="h-64">
          <canvas id="studentGrowthChart" class="w-full h-full"></canvas>
        </div>
        <p class="mt-3 text-xs text-slate-500">Placeholder chart — connect to realtime data source for live updates.</p>
      </div>

      <div class="bg-white rounded-xl border border-slate-200 p-5 shadow">
        <div class="flex items-center justify-between mb-4">
          <h3 class="text-lg font-semibold">Recent Activity</h3>
          <a href="/admin/users" class="text-indigo-600 text-sm hover:underline">View all</a>
        </div>

        <div class="overflow-x-auto">
          <table class="w-full text-left">
            <thead class="text-xs text-slate-500 border-b">
              <tr>
                <th class="py-3">Name</th>
                <th class="py-3">Email</th>
                <th class="py-3">Role</th>
                <th class="py-3">Status</th>
                <th class="py-3">Joined</th>
              </tr>
            </thead>
            <tbody class="divide-y bg-white">
              <?php if (!empty($recentUsers)): ?>
                <?php foreach ($recentUsers as $u): ?>
                  <tr>
                    <td class="py-4">
                      <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-full bg-slate-100 flex items-center justify-center text-slate-500 text-sm">
                          <?= htmlspecialchars(substr($u['name'] ?? 'U', 0, 1)) ?>
                        </div>
                        <div>
                          <div class="text-sm font-medium text-slate-900"><?= htmlspecialchars($u['name'] ?? '—') ?></div>
                          <div class="text-xs text-slate-400">#<?= htmlspecialchars($u['id'] ?? '') ?></div>
                        </div>
                      </div>
                    </td>
                    <td class="py-4 text-sm text-slate-600"><?= htmlspecialchars($u['email'] ?? '—') ?></td>
                    <td class="py-4 text-sm"><?= htmlspecialchars($u['role'] ?? '—') ?></td>
                    <td class="py-4 text-sm">
                      <?php
                        $status = $u['status'] ?? '';
                        $badge = 'bg-slate-100 text-slate-700';
                        if ($status === 'active') $badge = 'bg-emerald-50 text-emerald-600';
                        if ($status === 'suspended') $badge = 'bg-amber-50 text-amber-700';
                        if ($status === 'inactive') $badge = 'bg-slate-50 text-slate-600';
                      ?>
                      <span class="px-2 py-1 rounded-full text-xs <?= $badge ?>"><?= htmlspecialchars($status ?: '—') ?></span>
                    </td>
                    <td class="py-4 text-sm text-slate-500"><?= isset($u['created_at']) ? date('M j, Y', strtotime($u['created_at'])) : '—' ?></td>
                  </tr>
                <?php endforeach; ?>
              <?php else: ?>
                <tr><td colspan="5" class="py-6 text-center text-sm text-slate-500">No recent users</td></tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>
    </section>
  </main>

<script>
  if (window.lucide) lucide.replace();

  // Profile dropdown
  document.getElementById('profileBtn')?.addEventListener('click', function(e){
    e.preventDefault();
    document.getElementById('profileDropdown')?.classList.toggle('hidden');
  });

  // Chart.js - Student Growth (placeholder if no data provided)
  const ctx = document.getElementById('studentGrowthChart');
  if (ctx) {
    const labels = <?= json_encode($labels ?? array_map(function($i){ return date('M j', strtotime("-$i days")); }, array_reverse(range(0,6)))) ?>;
    const data = <?= json_encode($dataPoints ?? [12,18,22,30,28,40,45]) ?>;

    new Chart(ctx, {
      type: 'line',
      data: {
        labels: labels,
        datasets: [{
          label: 'New Students',
          data: data,
          borderColor: '#4f46e5',
          backgroundColor: 'rgba(79,70,229,0.08)',
          fill: true,
          tension: 0.35,
          pointRadius: 3
        }]
      },
      options: {
        responsive: true,
        plugins: { legend: { display: false } },
        scales: {
          x: { grid: { display: false }, ticks: { color: '#475569' } },
          y: { beginAtZero: true, ticks: { color: '#475569' } }
        }
      }
    });
  }

  // Student vs Instructor Registration Bar Chart (fetches counts from UsersController)
  (async function(){
    const endpoint = '/LMS/admin/src/controllers/UsersController.php?action=role_counts';
    try {
      const res = await fetch(endpoint, { credentials: 'same-origin' });
      const json = await res.json();
      if (!json.ok) {
        console.warn('role_counts fetch failed', json);
        return;
      }
      const payload = json.data || { labels: ['Students','Instructors'], data: [0,0] };

      // create or reuse canvas
      const container = document.getElementById('roleCountsChartContainer');
      if (!container) {
        // create a small container in the dashboard if missing
        const el = document.createElement('div');
        el.className = 'bg-white rounded-lg border p-4 shadow-sm';
        el.innerHTML = '<h4 class="text-sm font-semibold mb-2">Registrations by Role</h4><canvas id="roleCountsChart" style="height:220px"></canvas>';
        // place it near the top cards
        document.querySelector('main')?.insertBefore(el, document.querySelector('main > section'));
      }

      const ctx = document.getElementById('roleCountsChart')?.getContext('2d');
      if (!ctx) return;

      new Chart(ctx, {
        type: 'bar',
        data: {
          labels: payload.labels,
          datasets: [{
            label: 'Registrations',
            data: payload.data,
            backgroundColor: ['rgba(99,102,241,0.9)','rgba(16,185,129,0.85)'],
            borderRadius: 6,
            barThickness: 28
          }]
        },
        options: {
          responsive: true,
          plugins: { legend: { display: false } },
          scales: {
            x: { grid: { display: false }, ticks: { color: '#475569' } },
            y: { beginAtZero: true, ticks: { color: '#475569' } }
          }
        }
      });
    } catch (e) {
      console.error(e);
    }
  })();
</script>
</body>
</html>