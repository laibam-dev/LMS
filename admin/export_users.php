<?php
include '../config/db.php';

$filename = "users_report_" . date('Y-m-d') . ".csv";

// CSV headers 
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=' . $filename);


$output = fopen('php://output', 'w');


fputcsv($output, array('ID', 'Role', 'Email', 'Password Hash'));

// fetch data from database and write to CSV
$query = "SELECT id, role, email, password_hash FROM users ORDER BY id ASC";
$result = mysqli_query($conn, $query);

while ($row = mysqli_fetch_assoc($result)) {
    fputcsv($output, $row);
}

fclose($output);
exit;
?>