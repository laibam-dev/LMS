<?php
require_once __DIR__ . '/../../db/db_connection.php';
require_once __DIR__ . '/../middleware/auth.php';
require_once __DIR__ . '/../helpers/ActivityLogger.php';

$admin = $_SESSION['user'] ?? null;
$db = DB::getConnection();
$logs = ActivityLogger::fetchRecent($db, 50);

ob_start();
?>
<div>
  <div class="flex items-center justify-between mb-6">
    <div>
      <h1 class="text-2xl font-semibold">Activity Logs</h1>
      <p class="text-sm text-slate-500">Recent administrative actions</p>
    </div>
  </div>

  <div class="bg-white rounded-lg border border-slate-200 p-4 shadow-sm overflow-x-auto">
    <table class="w-full text-left">
      <thead class="text-xs text-slate-500 border-b">
        <tr>
          <th class="py-2">When</th>
          <th class="py-2">Admin</th>
          <th class="py-2">Action</th>
          <th class="py-2">Meta</th>
        </tr>
      </thead>
      <tbody class="divide-y">
        <?php if (!empty($logs)): foreach ($logs as $l): ?>
          <tr>
            <td class="py-3 text-sm text-slate-500"><?= htmlspecialchars(date('M j, Y H:i', strtotime($l['created_at']))) ?></td>
            <td class="py-3 text-sm"><?= htmlspecialchars($l['admin_name'] ?? 'System') ?></td>
            <td class="py-3 text-sm font-medium text-slate-800"><?= htmlspecialchars($l['action']) ?></td>
            <td class="py-3 text-sm text-slate-600"><?= htmlspecialchars($l['meta'] ?? '') ?></td>
          </tr>
        <?php endforeach; else: ?>
          <tr><td colspan="4" class="py-8 text-center text-slate-400">No logs</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>
<?php
$content = ob_get_clean();
$title = 'Activity Logs';
require __DIR__ . '/layout.php';