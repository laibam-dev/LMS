<?php
include '../config/db.php';
header('Content-Type: application/json');

// Get monthly student registration trend
$sql = "SELECT DATE_FORMAT(created_at, '%b %Y') as month, COUNT(id) as registrations FROM users WHERE role = 'student' GROUP BY YEAR(created_at), MONTH(created_at) ORDER BY YEAR(created_at), MONTH(created_at)";
$result = mysqli_query($conn, $sql);

$months = [];
$registrations = [];
while ($row = mysqli_fetch_assoc($result)) {
    $months[] = $row['month'];
    $registrations[] = (int)$row['registrations'];
}

echo json_encode([
    'months' => $months,
    'registrations' => $registrations
]);
