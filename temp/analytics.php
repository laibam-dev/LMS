<?php
include 'session.php';       // Session check
include 'header.php';
include 'sidebar.php';
include '../config/db.php';

// Fetch total counts
$total_users = $conn->query("SELECT COUNT(*) as total FROM users")->fetch_assoc()['total'];
$total_students = $conn->query("SELECT COUNT(*) as total FROM users WHERE role='student'")->fetch_assoc()['total'];
$total_instructors = $conn->query("SELECT COUNT(*) as total FROM users WHERE role='instructor'")->fetch_assoc()['total'];

// Monthly registrations data
$monthly = [];
for($i=1; $i<=12; $i++){
    $month_data = $conn->query("SELECT COUNT(*) as total FROM users WHERE MONTH(created_at)=$i")->fetch_assoc()['total'];
    $monthly[] = $month_data;
}
?>

<div style="margin-left:220px; padding:20px;">
    <h2>Analytics Dashboard</h2>
    
    <div class="card">
        <h3><?php echo $total_users; ?></h3>
        <p>Total Users</p>
    </div>
    <div class="card">
        <h3><?php echo $total_students; ?></h3>
        <p>Total Students</p>
    </div>
    <div class="card">
        <h3><?php echo $total_instructors; ?></h3>
        <p>Total Instructors</p>
    </div>
    
    <h3>Monthly Registrations</h3>
    <canvas id="monthlyChart" style="background:#fff; padding:20px; border-radius:10px;"></canvas>
</div>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
var ctx = document.getElementById('monthlyChart').getContext('2d');
var monthlyChart = new Chart(ctx, {
    type: 'bar',
    data: {
        labels: ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'],
        datasets: [{
            label: 'Registrations',
            data: <?php echo json_encode($monthly); ?>,
            backgroundColor: 'rgba(64,115,158,0.7)',
            borderColor: 'rgba(64,115,158,1)',
            borderWidth: 1
        }]
    },
    options: {
        responsive: true,
        scales: {
            y: { beginAtZero: true }
        }
    }
});
</script>
