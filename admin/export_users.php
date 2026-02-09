<?php
// Database include (BASE_URL yahan se initialize ho jayega magar CSV mein zarurat nahi)
include '../config/db.php';

// File name with current date
$filename = "users_report_" . date('Y-m-d') . ".csv";

// CSV headers - Yeh browser ko batata hai ke file download karni hai
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=' . $filename);

// Output stream open karna
$output = fopen('php://output', 'w');

// CSV ki pehli line (Columns headings)
fputcsv($output, array('ID', 'Role', 'Email', 'Password Hash'));

// Database se data nikal kar CSV mein likhna
$query = "SELECT id, role, email, password_hash FROM users ORDER BY id ASC";
$result = mysqli_query($conn, $query);

if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        fputcsv($output, $row);
    }
}

// Stream close karna
fclose($output);
exit;
?>