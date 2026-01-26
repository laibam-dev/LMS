<?php $title = 'Analytics'; include 'includes/header.php'; ?>

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

    <div class="mt-6 bg-white rounded-lg border p-4 shadow-sm">
      <h3 class="text-lg font-semibold mb-3">Student Signups (last 12 months)</h3>
      <canvas id="monthlyStudentsChart" style="height:240px"></canvas>
    </div>

  </main>
</div>

  <script>
    if (window.lucide) lucide.replace();

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

    // monthly students chart
    (async()=>{
      try{
        const r = await fetch('/LMS/admin/src/controllers/UsersController.php?action=monthly_students',{credentials:'same-origin'});
        const j = await r.json();
        if (j.ok) {
          const ctx = document.getElementById('monthlyStudentsChart').getContext('2d');
          new Chart(ctx, {
            type: 'bar',
            data: {
              labels: j.data.labels,
              datasets: [{ label: 'New Students', data: j.data.data, backgroundColor: 'rgba(79,70,229,0.9)' }]
            },
            options: {
              responsive: true,
              plugins: { legend: { display: false } },
              scales: { y: { beginAtZero: true } }
            }
          });
        }
      } catch(e) { console.error(e); }
    })();
  </script>
</body>
</html>