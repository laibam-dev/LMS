<?php



// Expects optional $admin for header/avatar

?>

<?php $title = 'User Management'; include 'includes/header.php'; ?>

      <!-- Search / Filter row (Create User aligned right) -->

      <div class="flex items-start justify-between mb-6 gap-4">

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

        </div>



        <div class="flex-shrink-0">

          <a href="/LMS/admin/src/views/create_user.php" class="inline-flex items-center gap-2 bg-indigo-600 text-white px-4 py-2 rounded-lg shadow-sm">

            <svg data-lucide="user-plus" class="w-4 h-4"></svg><span>Create User</span>

          </a>

        </div>

      </div>



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

    // show loading state

    renderLoading();

    const q = document.getElementById('search').value.trim();

    const role = document.getElementById('roleFilter').value;

    const url = new URL(apiBase, window.location.origin);

    url.searchParams.set('action', 'list');

    if (q) url.searchParams.set('search', q);

    if (role) url.searchParams.set('role', role);



    const res = await fetch(url.toString(), { credentials: 'same-origin' });

    const json = await res.json();

    if (!json.ok) {

      renderEmpty('No users found');

      return;

    }

    renderUsers(json.data || []);

  }



  function renderEmpty(msg) {

    document.getElementById('usersTbody').innerHTML = `<tr><td colspan="6" class="py-8 text-center text-sm text-slate-400">${msg}</td></tr>`;

  }



  function renderLoading(){

    document.getElementById('usersTbody').innerHTML = `<tr><td colspan="6" class="py-8 text-center text-sm text-slate-400">Loading…</td></tr>`;

  }



  function renderUsers(users) {

    if (!users.length) {

      renderEmpty('No users found');

      return;

    }

    const rows = users.map(u => {

      // render a stable avatar: use provided avatar if present, otherwise show initial in a gray circle

      const nameOrEmail = (u.name || u.email || '').toString().trim();

      const initial = nameOrEmail ? escapeHtml(nameOrEmail.charAt(0).toUpperCase()) : '?';

      const avatarHtml = u.avatar ?

        `<img src="${u.avatar}" class="w-9 h-9 rounded-full object-cover" alt="">` :

        `<div class="w-9 h-9 rounded-full bg-indigo-50 text-indigo-700 flex items-center justify-center text-sm font-semibold">${initial}</div>`;

      // normalize role for badge styling (case-insensitive)

      const roleRaw = (u.role || '').toString();

      const role = roleRaw.toLowerCase();

      const roleLabel = roleRaw ? roleRaw.charAt(0).toUpperCase() + roleRaw.slice(1).toLowerCase() : '—';

      const roleBadge = `<span class="px-2 py-1 rounded-full text-xs ${role==='admin'?'bg-indigo-50 text-indigo-700':role==='instructor'?'bg-indigo-50 text-indigo-700':'bg-slate-50 text-slate-700'}">${escapeHtml(roleLabel)}</span>`;

      const statusVal = (u.status || 'active').toString();

      const statusLabel = statusVal ? statusVal.charAt(0).toUpperCase() + statusVal.slice(1).toLowerCase() : '—';

      const statusBadge = `<span class="inline-flex items-center gap-2 px-2 py-0.5 rounded-full text-xs ${statusVal.toLowerCase()==='active'?'bg-emerald-50 text-emerald-600':'bg-slate-100 text-slate-700'}"><span class="w-2 h-2 rounded-full ${statusVal.toLowerCase()==='active'?'bg-emerald-600':'bg-slate-400'}"></span><span class="leading-none">${escapeHtml(statusLabel)}</span></span>`;



      return `

        <tr data-id="${u.id}">

          <td class="py-4 w-36">

            <div class="flex items-center gap-3">

              ${avatarHtml}

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

              <a href="/LMS/admin/src/views/edit_user.php?id=${u.id}" class="inline-flex items-center px-2 py-1 bg-indigo-600 text-white text-xs rounded hover:bg-indigo-700" title="Edit">

                <svg data-lucide="edit-2" class="w-4 h-4 mr-1"></svg>

              </a>

              <button onclick="confirmDelete(${u.id})" class="inline-flex items-center px-2 py-1 bg-rose-50 text-rose-700 text-xs rounded hover:bg-rose-100" title="Delete">

                <svg data-lucide="trash" class="w-4 h-4 mr-1"></svg>

              </button>

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