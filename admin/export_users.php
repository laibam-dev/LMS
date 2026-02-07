<?php
include '../config/db.php';

// File ka naam set karein
$filename = "users_report_" . date('Y-m-d') . ".csv";

// Browser ko batayein ke ye file download karni hai
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=' . $filename);

// Output stream open karein
$output = fopen('php://output', 'w');

// CSV ki pehli line (Headings) - Hum wahi columns le rahe jo aapke DB mein hain
fputcsv($output, array('ID', 'Role', 'Email', 'Password Hash'));

// Database se data nikaalein
$query = "SELECT id, role, email, password_hash FROM users ORDER BY id ASC";
$result = mysqli_query($conn, $query);

while ($row = mysqli_fetch_assoc($result)) {
    fputcsv($output, $row);
}

fclose($output);
exit;
?>