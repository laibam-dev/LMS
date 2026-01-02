<?php
if (session_status() === PHP_SESSION_NONE) session_start();
$flash = $_SESSION['flash_profile'] ?? null;
unset($_SESSION['flash_profile']);
if (empty($_SESSION['csrf_profile'])) $_SESSION['csrf_profile'] = bin2hex(random_bytes(16));
$csrf = $_SESSION['csrf_profile'];
?>
<?php $title = 'Settings'; include 'includes/header.php'; ?>

    <div class="max-w-3xl mx-auto">
      <div class="bg-white p-6 rounded-lg shadow">
        <h3 class="text-lg font-semibold mb-4">Settings</h3>
        <p class="text-sm text-slate-500 mb-6">Manage account settings and preferences.</p>

        <?php if ($flash): ?>
          <div class="mb-4 text-sm text-white bg-indigo-600 px-3 py-2 rounded"><?= htmlspecialchars($flash) ?></div>
        <?php endif; ?>

        <form method="post" action="/LMS/admin/src/controllers/ProfileController.php?action=update_settings" class="space-y-4">
          <input type="hidden" name="csrf" value="<?= htmlspecialchars($csrf) ?>">
          <label class="block">
            <span class="text-sm text-slate-600">Full name</span>
            <input name="name" type="text" value="<?= htmlspecialchars($admin['name'] ?? '') ?>" class="mt-1 block w-full rounded-lg border border-slate-200 px-3 py-2 bg-white">
          </label>
          <label class="block">
            <span class="text-sm text-slate-600">Email</span>
            <input name="email" type="email" value="<?= htmlspecialchars($admin['email'] ?? '') ?>" class="mt-1 block w-full rounded-lg border border-slate-200 px-3 py-2 bg-white">
          </label>
          <div class="flex gap-2">
            <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded-lg">Save changes</button>
            <a href="/LMS/admin/src/views/profile.php" class="px-4 py-2 border rounded-lg text-sm">Cancel</a>
          </div>
        </form>
      </div>
    </div>
  </main>
</div>

  <script> if (window.lucide) lucide.replace();
    document.getElementById('profileToggle')?.addEventListener('click', function(){ document.getElementById('profileMenu')?.classList.toggle('hidden'); });
  </script>
</body>
</html>
