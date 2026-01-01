<?php
require_once __DIR__ . '/../../db/db_connection.php';
require_once __DIR__ . '/../middleware/auth.php';

$admin = $_SESSION['user'] ?? null;

ob_start();
?>
<div>
  <div class="flex items-center justify-between mb-6">
    <div>
      <h1 class="text-2xl font-semibold">Analytics</h1>
      <p class="text-sm text-slate-500">Platform metrics</p>
    </div>
  </div>

  <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <div class="bg-white rounded-lg border p-4 shadow-sm">
      <h3 class="text-sm font-medium mb-2">Student vs Instructor Registrations</h3>
      <canvas id="roleCountsChart" style="height:220px"></canvas>
    </div>

    <div class="bg-white rounded-lg border p-4 shadow-sm">
      <h3 class="text-sm font-medium mb-2">Enrollments (last 12 months)</h3>
      <canvas id="enrollmentsChart" style="height:220px"></canvas>
    </div>
  </div>

  <script>
    (async()=>{
      try{
        const r = await fetch('/LMS/admin/src/controllers/UsersController.php?action=role_counts',{credentials:'same-origin'});
        const j = await r.json();
        if(j.ok){
          const ctx = document.getElementById('roleCountsChart').getContext('2d');
          new Chart(ctx,{type:'bar',data:{labels:j.data.labels, datasets:[{data:j.data.data, backgroundColor:['#6366f1','#10b981']}]}, options:{plugins:{legend:{display:false}}, scales:{y:{beginAtZero:true}}}});
        }
      }catch(e){console.error(e)}
    })();

    (async()=>{
      try{
        const r = await fetch('/LMS/admin/src/controllers/DashboardController.php?action=chart_enrollments',{credentials:'same-origin'});
        const j = await r.json();
        if(j.ok){
          const ctx = document.getElementById('enrollmentsChart').getContext('2d');
          new Chart(ctx,{type:'line',data:{labels:j.data.labels,datasets:[{data:j.data.data,borderColor:'#6366f1',backgroundColor:'rgba(99,102,241,0.08)',fill:true}]} , options:{plugins:{legend:{display:false}}, scales:{y:{beginAtZero:true}}}});
        }
      }catch(e){console.error(e)}
    })();
  </script>
</div>
<?php
$content = ob_get_clean();
$title = 'Analytics';
require __DIR__ . '/layout.php';