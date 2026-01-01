<?php
<?php
// Minimal page wrapper will be provided by layout.php when routed via index.php.
// This view expects to be loaded with layout, or can be used standalone.
?>
<div>
  <div class="flex items-center justify-between mb-6">
    <div>
      <h1 class="text-2xl font-semibold">Course Management</h1>
      <p class="text-sm text-slate-500">Review and moderate courses submitted by instructors.</p>
    </div>

    <div class="flex items-center gap-3">
      <input id="courseSearch" class="px-4 py-2 rounded-lg border border-slate-200 bg-white shadow-sm" placeholder="Search courses or instructor">
      <select id="statusFilter" class="px-3 py-2 rounded-lg border border-slate-200 bg-white">
        <option value="">All status</option>
        <option value="pending">Pending</option>
        <option value="approved">Approved</option>
        <option value="rejected">Rejected</option>
      </select>
      <button id="refreshCourses" class="bg-indigo-600 text-white px-4 py-2 rounded-lg">Refresh</button>
    </div>
  </div>

  <div class="bg-white rounded-lg border border-slate-200 shadow-sm p-4 overflow-x-auto">
    <table class="w-full text-left">
      <thead class="text-xs text-slate-500 border-b">
        <tr>
          <th class="py-3">Title</th>
          <th class="py-3">Instructor</th>
          <th class="py-3">Status</th>
          <th class="py-3">Submitted</th>
          <th class="py-3">Actions</th>
        </tr>
      </thead>
      <tbody id="coursesTbody" class="divide-y">
        <tr><td colspan="5" class="py-8 text-center text-slate-400">Loading…</td></tr>
      </tbody>
    </table>
  </div>
</div>

<script>
  const coursesApi = '/LMS/admin/src/controllers/CoursesController.php';
  async function fetchCourses() {
    const url = new URL(coursesApi, location.origin);
    url.searchParams.set('action', 'list');
    const q = document.getElementById('courseSearch').value.trim();
    const s = document.getElementById('statusFilter').value;
    if (q) url.searchParams.set('search', q);
    if (s) url.searchParams.set('status', s);
    const res = await fetch(url, { credentials: 'same-origin' });
    const json = await res.json();
    if (!json.ok) {
      document.getElementById('coursesTbody').innerHTML = `<tr><td colspan="5" class="py-8 text-center text-rose-500">Failed to load</td></tr>`;
      return;
    }
    renderCourses(json.data || []);
  }

  function renderCourses(rows) {
    if (!rows.length) {
      document.getElementById('coursesTbody').innerHTML = `<tr><td colspan="5" class="py-8 text-center text-slate-400">No courses</td></tr>`;
      return;
    }
    document.getElementById('coursesTbody').innerHTML = rows.map(r => {
      const statusBadge = r.status === 'pending' ? 'bg-amber-50 text-amber-700' : r.status === 'approved' ? 'bg-emerald-50 text-emerald-600' : 'bg-rose-50 text-rose-700';
      return `
        <tr data-id="${r.id}">
          <td class="py-4 font-medium text-slate-800">${escapeHtml(r.title || '—')}</td>
          <td class="py-4 text-sm text-slate-600">${escapeHtml(r.instructor_name || '—')}</td>
          <td class="py-4"><span class="px-2 py-1 rounded-full text-xs ${statusBadge}">${escapeHtml(r.status)}</span></td>
          <td class="py-4 text-sm text-slate-500">${r.created_at ? new Date(r.created_at).toLocaleString() : '—'}</td>
          <td class="py-4">
            <div class="flex items-center gap-2">
              ${r.status !== 'approved' ? `<button onclick="approve(${r.id})" class="px-3 py-1 rounded bg-emerald-600 text-white text-sm">Approve</button>` : ''}
              ${r.status !== 'rejected' ? `<button onclick="reject(${r.id})" class="px-3 py-1 rounded bg-rose-600 text-white text-sm">Reject</button>` : ''}
            </div>
          </td>
        </tr>
      `;
    }).join('');
  }

  function escapeHtml(s){ return (s===null||s===undefined)?'':String(s).replace(/[&<>"']/g, c=>({ '&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'})[c]); }

  async function approve(id){
    if (!confirm('Approve this course?')) return;
    const form = new FormData(); form.append('id', id);
    const res = await fetch(coursesApi + '?action=approve', { method: 'POST', body: form, credentials: 'same-origin' });
    const j = await res.json();
    if (j.ok) { fetchCourses(); } else alert('Failed');
  }
  async function reject(id){
    if (!confirm('Reject this course?')) return;
    const form = new FormData(); form.append('id', id);
    const res = await fetch(coursesApi + '?action=reject', { method: 'POST', body: form, credentials: 'same-origin' });
    const j = await res.json();
    if (j.ok) { fetchCourses(); } else alert('Failed');
  }

  document.getElementById('courseSearch').addEventListener('input', debounce(fetchCourses, 300));
  document.getElementById('statusFilter').addEventListener('change', fetchCourses);
  document.getElementById('refreshCourses').addEventListener('click', fetchCourses);

  fetchCourses();

  function debounce(fn, t){ let timer; return (...a)=>{ clearTimeout(timer); timer=setTimeout(()=>fn.apply(this,a), t); }; }
</script>