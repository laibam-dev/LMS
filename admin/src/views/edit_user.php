<?php
// Absolute path setup taake "Failed to open stream" wala error na aaye
$basePath = $_SERVER['DOCUMENT_ROOT'] . '/LMS';
require_once $basePath . '/admin/src/controllers/UsersController.php';

if (session_status() === PHP_SESSION_NONE) session_start();

$ctrl = new UsersController();
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id <= 0) {
    header('Location: /LMS/admin/src/views/users.php');
    exit;
}

$user = $ctrl->getUserById($id);

if (!$user) {
    header('Location: /LMS/admin/src/views/users.php');
    exit;
}

// Header include karein (Path handle kiya gaya hai)
include 'includes/header.php'; 
?>

<main class="p-6 bg-slate-50 min-h-screen">
  <div class="max-w-2xl mx-auto">
    <a href="users.php" class="flex items-center text-sm text-indigo-600 mb-4 hover:underline">
        <i data-lucide="arrow-left" class="w-4 h-4 mr-1"></i> Back to Users
    </a>

    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-8">
      <div class="flex items-center gap-3 mb-6">
          <div class="w-12 h-12 bg-indigo-100 text-indigo-700 rounded-full flex items-center justify-center font-bold text-xl">
              <?= strtoupper(substr($user['name'], 0, 1)) ?>
          </div>
          <div>
              <h3 class="text-xl font-semibold text-slate-800">Edit User Profile</h3>
              <p class="text-sm text-slate-500">Updating details for ID: #<?= htmlspecialchars($user['id']) ?></p>
          </div>
      </div>

      <form id="editUserForm" class="space-y-5">
        <input type="hidden" name="id" value="<?= htmlspecialchars($user['id']) ?>">

        <div>
          <label class="block text-sm font-medium text-slate-700 mb-1">Full Name</label>
          <input name="name" type="text" required class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none" value="<?= htmlspecialchars($user['name'] ?? '') ?>" />
        </div>

        <div>
          <label class="block text-sm font-medium text-slate-700 mb-1">Email Address</label>
          <input name="email" type="email" required class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none" value="<?= htmlspecialchars($user['email'] ?? '') ?>" />
        </div>

        <div>
          <label class="block text-sm font-medium text-slate-700 mb-1">Password</label>
          <input name="password" type="password" placeholder="Leave blank to keep current password" class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none" />
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-medium text-slate-700 mb-1">Role</label>
              <select name="role" class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none bg-white">
                <option value="student" <?= (strtolower($user['role'] ?? '') === 'student') ? 'selected' : '' ?>>Student</option>
                <option value="instructor" <?= (strtolower($user['role'] ?? '') === 'instructor') ? 'selected' : '' ?>>Instructor</option>
                <option value="admin" <?= (strtolower($user['role'] ?? '') === 'admin') ? 'selected' : '' ?>>Admin</option>
              </select>
            </div>

            <div>
              <label class="block text-sm font-medium text-slate-700 mb-1">Account Status</label>
              <select name="status" class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none bg-white">
                <option value="active" <?= (strtolower($user['status'] ?? '') === 'active') ? 'selected' : '' ?>>Active</option>
                <option value="inactive" <?= (strtolower($user['status'] ?? '') === 'inactive') ? 'selected' : '' ?>>Inactive</option>
                <option value="suspended" <?= (strtolower($user['status'] ?? '') === 'suspended') ? 'selected' : '' ?>>Suspended</option>
              </select>
            </div>
        </div>

        <div class="pt-4 flex items-center justify-end gap-3">
          <a href="/LMS/admin/src/views/users.php" class="px-6 py-2 border border-slate-300 rounded-lg text-sm font-medium text-slate-600 hover:bg-slate-50 transition">Cancel</a>
          <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2 rounded-lg text-sm font-medium shadow-sm transition">Save Changes</button>
        </div>
      </form>

      <div id="editMsg" class="mt-4 text-center text-sm font-medium"></div>
    </div>
  </div>
</main>

<script>
// Lucide icons ko initialize karein
if (window.lucide) lucide.replace();

const form = document.getElementById('editUserForm');
const msgDiv = document.getElementById('editMsg');

form.addEventListener('submit', async function(e){
  e.preventDefault();
  msgDiv.className = "mt-4 text-center text-sm font-medium text-indigo-600";
  msgDiv.textContent = 'Updating...';
  
  const fd = new FormData(form);
  try {
    const res = await fetch('/LMS/admin/src/controllers/UsersController.php?action=update', { 
        method: 'POST', 
        body: fd 
    });
    const text = await res.text();
    let json = null;
    try { json = JSON.parse(text); } catch (err) { }

    if (json && json.ok) {
      msgDiv.className = "mt-4 text-center text-sm font-medium text-green-600";
      msgDiv.textContent = 'User updated successfully! Redirecting...';
      setTimeout(() => {
          window.location.href = '/LMS/admin/src/views/users.php';
      }, 1500);
      return;
    }
    
    msgDiv.className = "mt-4 text-center text-sm font-medium text-red-600";
    msgDiv.textContent = (json && json.error) ? json.error : 'Update failed. Check database.';
  } catch (err) {
    msgDiv.className = "mt-4 text-center text-sm font-medium text-red-600";
    msgDiv.textContent = 'Connection error. Please try again.';
  }
});
</script>