<?php
// content-only partial used by DashboardController::index()
// Expects $stats, $recentUsers, $activityLogs and $admin in scope.

// Ensure recent users are fetched via UsersController
require_once __DIR__ . '/../controllers/UsersController.php';
$usersCtrl = new UsersController();
$recentUsers = $usersCtrl->getRecentUsers(5);
?>
<section class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
  <div class="bg-white rounded-lg shadow-sm border border-slate-200 p-5">
    <div class="flex items-center justify-between">
      <div>
        <h3 class="text-sm text-slate-500">Total Students</h3>
        <p class="mt-2 text-2xl font-semibold text-slate-900"><?= number_format($stats['total_students'] ?? 0) ?></p>
      </div>
      <div class="bg-indigo-50 p-3 rounded-md">
        <svg data-lucide="users" class="w-6 h-6 text-indigo-600" stroke-width="1.5" fill="none"></svg>
      </div>
    </div>
  </div>

  <div class="bg-white rounded-lg shadow-sm border border-slate-200 p-5">
    <div class="flex items-center justify-between">
      <div>
        <h3 class="text-sm text-slate-500">Active Courses</h3>
        <p class="mt-2 text-2xl font-semibold text-slate-900"><?= number_format($stats['total_courses'] ?? 0) ?></p>
      </div>
      <div class="bg-indigo-50 p-3 rounded-md">
        <svg data-lucide="book-open" class="w-6 h-6 text-indigo-600" stroke-width="1.5" fill="none"></svg>
      </div>
    </div>
  </div>

  <div class="bg-white rounded-lg shadow-sm border border-slate-200 p-5">
    <div class="flex items-center justify-between">
      <div>
        <h3 class="text-sm text-slate-500">Total Instructors</h3>
        <p class="mt-2 text-2xl font-semibold text-slate-900"><?= number_format($stats['total_instructors'] ?? 0) ?></p>
      </div>
      <div class="bg-indigo-50 p-3 rounded-md">
        <svg data-lucide="briefcase" class="w-6 h-6 text-indigo-600" stroke-width="1.5" fill="none"></svg>
      </div>
    </div>
  </div>

  <div class="bg-white rounded-lg shadow-sm border border-slate-200 p-5">
    <div class="flex items-center justify-between">
      <div>
        <h3 class="text-sm text-slate-500">Monthly Revenue</h3>
        <p class="mt-2 text-2xl font-semibold text-slate-900">$<?= number_format($stats['monthly_revenue'] ?? 0, 2) ?></p>
      </div>
      <div class="bg-indigo-50 p-3 rounded-md">
        <svg data-lucide="dollar-sign" class="w-6 h-6 text-indigo-600" stroke-width="1.5" fill="none"></svg>
      </div>
    </div>
  </div>
</section>

<section class="grid grid-cols-1 lg:grid-cols-3 gap-6">
  <div class="lg:col-span-2 bg-white rounded-lg border border-slate-200 p-5 shadow-sm">
    <div class="flex items-center justify-between mb-4">
      <h2 class="text-lg font-semibold">Real-time Analytics</h2>
      <div class="text-sm text-slate-500">Student Growth (last 12 months)</div>
    </div>

    <div class="h-64">
      <canvas id="studentGrowthChart"></canvas>
    </div>
  </div>

  <!-- Activity Feed widget -->
  <aside class="bg-white rounded-lg border border-slate-200 p-4 shadow-sm">
    <div class="flex items-center justify-between mb-3">
      <h3 class="text-sm font-semibold">Activity Feed</h3>
      <a href="/LMS/admin/src/views/logs.php" class="text-xs text-indigo-600 hover:underline">View all</a>
    </div>

    <ul class="space-y-3">
      <?php if (!empty($activityLogs)): ?>
        <?php foreach ($activityLogs as $log): ?>
          <li class="text-sm">
            <div class="flex items-start gap-3">
              <div class="w-9 h-9 rounded-full bg-slate-100 flex items-center justify-center text-xs text-slate-600">
                <?= htmlspecialchars(substr($log['admin_name'] ?? 'A', 0, 1)) ?>
              </div>
              <div class="flex-1">
                <div class="text-xs text-slate-600">
                  <span class="font-medium text-slate-800"><?= htmlspecialchars($log['admin_name'] ?? 'System') ?></span>
                  <span class="mx-1">•</span>
                  <span class="text-slate-500"><?= htmlspecialchars($log['action']) ?></span>
                </div>
                <div class="text-xs text-slate-400 mt-1"><?= date('M j, Y H:i', strtotime($log['created_at'])) ?></div>
              </div>
            </div>
          </li>
        <?php endforeach; ?>
      <?php else: ?>
        <li class="py-6 text-center text-xs text-slate-400">No recent activity</li>
      <?php endif; ?>
    </ul>
  </aside>
</section>

<div class="bg-white rounded-lg border border-slate-200 p-5 shadow-sm mt-6">
  <h3 class="text-sm font-semibold mb-4">Recent Users</h3>
  <div class="overflow-x-auto">
    <table class="w-full text-left">
      <thead class="text-xs text-slate-500 border-b">
        <tr>
          <th class="py-3">Name</th>
          <th class="py-3">Email</th>
          <th class="py-3">Role</th>
        </tr>
      </thead>
      <tbody class="divide-y bg-white">
        <?php if (!empty($recentUsers)): ?>
          <?php foreach ($recentUsers as $u): ?>
            <tr>
              <td class="py-4">
                <div class="text-sm font-medium text-slate-900"><?= htmlspecialchars($u['name'] ?? '—') ?></div>
              </td>
              <td class="py-4 text-sm text-slate-600"><?= htmlspecialchars($u['email'] ?? '—') ?></td>
              <td class="py-4 text-sm"><?= htmlspecialchars($u['role'] ?? '—') ?></td>
            </tr>
          <?php endforeach; ?>
        <?php else: ?>
          <tr><td colspan="3" class="py-6 text-center text-sm text-slate-500">No recent users</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<script>
  // fetch real enrollments per month and render Chart.js
  (async function(){
    const endpoint = '/LMS/admin/src/controllers/DashboardController.php?action=chart_enrollments';
    try {
      const res = await fetch(endpoint, { credentials: 'same-origin' });
      const json = await res.json();
      if (!json.ok) return;
      const payload = json.data;
      const ctx = document.getElementById('studentGrowthChart').getContext('2d');
      new Chart(ctx, {
        type: 'line',
        data: {
          labels: payload.labels,
          datasets: [{
            label: 'Enrollments',
            data: payload.data,
            borderColor: '#6366f1',
            backgroundColor: 'rgba(99,102,241,0.08)',
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
    } catch (e) {
      console.error(e);
    }
  })();
</script>