<?php
// Expects:
// $admin (array) - ['name','avatar']
// $stats (array) - ['total_students','active_courses','total_instructors','monthly_revenue']
// $labels (array) - chart labels
// $dataPoints (array) - chart data
// $recentUsers (array) - last 5 users: ['id','name','email','role','status','created_at']
?>
<?php $title = 'Dashboard'; include 'includes/header.php'; ?>

    <!-- Summary cards -->
    <section class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
      <div class="bg-white rounded-xl shadow border border-slate-200 p-5 h-full">
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

      <div class="bg-white rounded-xl shadow border border-slate-200 p-5 h-full">
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

      <div class="bg-white rounded-xl shadow border border-slate-200 p-5 h-full">
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

      <div class="bg-white rounded-xl shadow border border-slate-200 p-5 h-full">
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

    <!-- Charts: line (wide) + role counts (narrow) -->
    <section class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
      <div class="bg-white rounded-xl border border-slate-200 p-5 shadow lg:col-span-2">
        <div class="flex items-center justify-between mb-4">
          <h3 class="text-lg font-semibold">Real-time Analytics</h3>
          <div class="text-sm text-slate-500">Student Growth</div>
        </div>
        <div class="h-80">
          <canvas id="studentGrowthChart" class="w-full h-full"></canvas>
        </div>
        <p class="mt-3 text-xs text-slate-500">Placeholder chart — connect to realtime data source for live updates.</p>
      </div>

      <div class="bg-white rounded-xl border border-slate-200 p-5 shadow">
        <div class="flex items-center justify-between mb-4">
          <h3 class="text-lg font-semibold">Registrations by Role</h3>
          <div class="text-sm text-slate-500">Last 30 days</div>
        </div>
        <div id="roleCountsChartContainer" class="h-64">
          <canvas id="roleCountsChart" class="w-full h-full"></canvas>
        </div>
      </div>
    </section>

    <!-- Recent Activity (full width) -->
    <section class="bg-white rounded-xl border border-slate-200 p-5 shadow">
      <div class="flex items-center justify-between mb-4">
        <h3 class="text-lg font-semibold">Recent Activity</h3>
        <a href="/LMS/admin/src/views/users.php" class="text-indigo-600 text-sm hover:underline">View all</a>
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
    </section>
  </main>
</div>

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
          maintainAspectRatio: false,
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
    const endpoint = '../controllers/UsersController.php?action=role_counts';
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
          maintainAspectRatio: false,
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