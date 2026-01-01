<?php
<?php
// Expects optional $admin for header/avatar
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>User Management · Polymath Path Institute</title>

  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    tailwind.config = { theme: { extend: { colors: { brand: { 50: '#eef2ff', 500: '#6366f1' } } } } };
  </script>

  <script src="https://unpkg.com/lucide@0.286.0/dist/lucide.min.js"></script>
</head>
<body class="bg-slate-50 text-slate-800 antialiased">

  <div class="flex">
    <!-- Sidebar (reuse styling) -->
    <aside class="sidebar fixed inset-y-0 left-0 bg-white border-r border-slate-200 px-6 py-8" style="width:280px;">
      <div class="mb-6">
        <h1 class="text-lg font-semibold">Polymath Path</h1>
      </div>
      <nav class="space-y-1 text-sm">
        <a href="/admin" class="flex items-center gap-3 px-3 py-2 rounded-md hover:bg-slate-50"><svg data-lucide="home" class="w-5 h-5"></svg>Dashboard</a>
        <a href="/admin/users" class="flex items-center gap-3 px-3 py-2 rounded-md bg-indigo-50 text-indigo-700"><svg data-lucide="users" class="w-5 h-5"></svg>User Management</a>
        <a href="/admin/analytics" class="flex items-center gap-3 px-3 py-2 rounded-md hover:bg-slate-50"><svg data-lucide="bar-chart-2" class="w-5 h-5"></svg>Analytics</a>
        <a href="/admin/settings" class="flex items-center gap-3 px-3 py-2 rounded-md hover:bg-slate-50"><svg data-lucide="settings" class="w-5 h-5"></svg>Settings</a>
      </nav>
    </aside>

    <!-- Content -->
    <main class="flex-1 ml-[280px] p-8">
      <header class="flex items-center justify-between mb-6">
        <div>
          <h2 class="text-2xl font-semibold">User Management</h2>
          <p class="text-sm text-slate-500">Manage users, roles and status</p>
        </div>

        <div class="flex items-center gap-4">
          <div class="relative">
            <input id="search" class="pl-10 pr-4 py-2 w-80 rounded-lg border border-slate-200 bg-white shadow-sm" placeholder="Search by name or email">
            <div class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"><svg data-lucide="search" class="w-4 h-4"></svg></div>
          </div>

          <select id="roleFilter" class="px-3 py-2 rounded-lg border border-slate-200 bg-white">
            <option value="">All roles</option>
            <option value="admin">Admin</option>
            <option value="instructor">Instructor</option>
            <option value="student">Student</option>
          </select>

          <a href="/admin/users/create.php" class="inline-flex items-center gap-2 bg-indigo-600 text-white px-4 py-2 rounded-lg shadow-sm">
            <svg data-lucide="user-plus" class="w-4 h-4"></svg><span>Create User</span>
          </a>
        </div>
      </header>

      <section class="bg-white rounded-lg border border-slate-200 shadow-sm p-4">
        <div class="overflow-x-auto">
          <table id="usersTable" class="w-full text-left">
            <thead class="text-xs text-slate-500 border-b">
              <tr>
                <th class="py-3">Profile</th>
                <th class="py-3">Name</th>
                <th class="py-3">Email</th>
                <th class="py-3">Role</th>
                <th class="py-3">Status</th>
                <th class="py-3">Actions</th>
              </tr>
            </thead>
            <tbody class="divide-y bg-white" id="usersTbody">
              <tr><td colspan="6" class="py-8 text-center text-sm text-slate-400">Loading…</td></tr>
            </tbody>
          </table>
        </div>
      </section>
    </main>
  </div>

<script>
  if (window.lucide) lucide.replace();

  const apiBase = '/LMS/admin/src/controllers/UsersController.php';

  async function fetchUsers() {
    const q = document.getElementById('search').value.trim();
    const role = document.getElementById('roleFilter').value;
    const url = new URL(apiBase, window.location.origin);
    url.searchParams.set('action', 'list');
    if (q) url.searchParams.set('search', q);
    if (role) url.searchParams.set('role', role);

    const res = await fetch(url.toString(), { credentials: 'same-origin' });
    const json = await res.json();
    if (!json.ok) {
      renderEmpty('Failed to load users');
      return;
    }
    renderUsers(json.data || []);
  }

  function renderEmpty(msg) {
    document.getElementById('usersTbody').innerHTML = `<tr><td colspan="6" class="py-8 text-center text-sm text-slate-400">${msg}</td></tr>`;
  }

  function renderUsers(users) {
    if (!users.length) {
      renderEmpty('No users found');
      return;
    }
    const rows = users.map(u => {
      const avatar = u.avatar || `https://i.pravatar.cc/48?u=${encodeURIComponent(u.email)}`;
      const roleBadge = `<span class="px-2 py-1 rounded-full text-xs ${u.role==='admin'?'bg-indigo-50 text-indigo-700':u.role==='instructor'?'bg-amber-50 text-amber-700':'bg-slate-50 text-slate-700'}">${escapeHtml(u.role)}</span>`;
      const statusBadge = `<span class="px-2 py-1 rounded-full text-xs ${u.status==='active'?'bg-emerald-50 text-emerald-600':'bg-slate-100 text-slate-700'}">${escapeHtml(u.status)}</span>`;
      return `
        <tr data-id="${u.id}">
          <td class="py-4 w-36">
            <div class="flex items-center gap-3">
              <img src="${avatar}" class="w-9 h-9 rounded-full object-cover" alt="">
            </div>
          </td>
          <td class="py-4">
            <div class="text-sm font-medium text-slate-900">${escapeHtml(u.name)}</div>
            <div class="text-xs text-slate-400">#${u.id}</div>
          </td>
          <td class="py-4 text-sm text-slate-600">${escapeHtml(u.email)}</td>
          <td class="py-4 text-sm">${roleBadge}</td>
          <td class="py-4 text-sm">${statusBadge}</td>
          <td class="py-4 text-sm">
            <div class="flex items-center gap-2">
              <a href="/admin/users/edit.php?id=${u.id}" class="p-2 rounded hover:bg-slate-50" title="Edit"><svg data-lucide="edit-2" class="w-4 h-4"></svg></a>
              <button onclick="confirmDelete(${u.id})" class="p-2 rounded hover:bg-slate-50" title="Delete"><svg data-lucide="trash" class="w-4 h-4 text-rose-600"></svg></button>
              <button onclick="toggleStatus(${u.id}, '${u.status}')" class="p-2 rounded hover:bg-slate-50" title="Toggle Status"><svg data-lucide="zap" class="w-4 h-4"></svg></button>
            </div>
          </td>
        </tr>
      `;
    }).join('');
    document.getElementById('usersTbody').innerHTML = rows;
    if (window.lucide) lucide.replace();
  }

  function escapeHtml(s){ return (s===null||s===undefined)?'':String(s).replace(/[&<>"']/g, c=>({ '&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'})[c]); }

  async function confirmDelete(id) {
    if (!confirm('Delete this user? This action cannot be undone.')) return;
    const form = new FormData();
    form.append('id', id);
    const res = await fetch(apiBase + '?action=delete', { method: 'POST', body: form, credentials: 'same-origin' });
    const json = await res.json();
    if (json.ok) {
      document.querySelector(`tr[data-id="${id}"]`)?.remove();
    } else {
      alert('Delete failed');
    }
  }

  async function toggleStatus(id, current) {
    const next = (current === 'active') ? 'inactive' : 'active';
    const form = new FormData();
    form.append('id', id);
    form.append('status', next);
    const res = await fetch(apiBase + '?action=update_status', { method: 'POST', body: form, credentials: 'same-origin' });
    const json = await res.json();
    if (json.ok) {
      // refresh row
      await fetchUsers();
    } else {
      alert('Update failed');
    }
  }

  // events
  document.getElementById('search').addEventListener('input', debounce(fetchUsers, 350));
  document.getElementById('roleFilter').addEventListener('change', fetchUsers);

  // initial load
  fetchUsers();

  function debounce(fn, wait){
    let t;
    return function(...a){ clearTimeout(t); t = setTimeout(()=> fn.apply(this,a), wait); };
  }
</script>
</body>
</html>