<?php
// Simple create user form view
// $title may be set before include
// Ensure DB connection file is available via absolute path for any server-side checks
require_once $_SERVER['DOCUMENT_ROOT'] . '/LMS/admin/db/db_connection.php';
?>
<?php $title = 'Create User'; include 'includes/header.php'; ?>

<main>
  <div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-lg shadow border p-6">
      <h3 class="text-lg font-medium mb-4">Create User</h3>

      <form id="createUserForm" class="space-y-4">
        <div>
          <label class="block text-sm text-slate-700 mb-1">Name</label>
          <input name="name" type="text" required class="w-full px-3 py-2 border rounded-lg" />
        </div>

        <div>
          <label class="block text-sm text-slate-700 mb-1">Email</label>
          <input name="email" type="email" required class="w-full px-3 py-2 border rounded-lg" />
        </div>

        <div>
          <label class="block text-sm text-slate-700 mb-1">Password</label>
          <input name="password" type="password" required class="w-full px-3 py-2 border rounded-lg" />
        </div>

        <div>
          <label class="block text-sm text-slate-700 mb-1">Role</label>
          <select name="role" class="w-full px-3 py-2 border rounded-lg">
            <option value="student">Student</option>
            <option value="instructor">Instructor</option>
            <option value="admin">Admin</option>
          </select>
        </div>

        <div class="flex items-center justify-end gap-3">
          <a href="/LMS/admin/src/views/users.php" class="px-4 py-2 border rounded-lg text-sm">Cancel</a>
          <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded-lg text-sm">Create User</button>
        </div>
      </form>

      <div id="createMsg" class="mt-4 text-sm"></div>
    </div>
  </div>
</main>

<script>
if (window.lucide) lucide.replace();

const form = document.getElementById('createUserForm');
form.addEventListener('submit', async function(e){
  e.preventDefault();
  const fd = new FormData(form);
  try {
    const res = await fetch('/LMS/admin/src/controllers/UsersController.php?action=create', { method: 'POST', body: fd, credentials: 'same-origin' });

    // log full response for debugging
    console.log('Response status:', res.status, res.statusText);
    for (const pair of res.headers.entries()) console.log('Header:', pair[0], pair[1]);

    const text = await res.text();
    console.log('Response text:', text);
    let json = null;
    try { json = JSON.parse(text); } catch (err) { /* not JSON */ }

    if (json && json.ok) {
      window.location.href = '/LMS/admin/src/views/users.php';
      return;
    }

    // show error from JSON if available, otherwise show raw text or status
    const msg = (json && json.error) ? json.error : (text || ('Request failed: ' + res.status));
    document.getElementById('createMsg').textContent = msg;
  } catch (e) {
    console.error(e);
    document.getElementById('createMsg').textContent = 'Request failed — see console for details';
  }
});
</script>

</body>
</html>
