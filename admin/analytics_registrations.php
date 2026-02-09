<?php
// Database include (BASE_URL yahan se define ho jata hai lekin JSON output mein use nahi hota)
include '../config/db.php';

header('Content-Type: application/json');

// Get monthly student registration trend (Aapka original logic)
$sql = "SELECT DATE_FORMAT(created_at, '%b %Y') as month, COUNT(id) as registrations 
        FROM users 
        WHERE role = 'student' 
        GROUP BY YEAR(created_at), MONTH(created_at) 
        ORDER BY YEAR(created_at), MONTH(created_at)";

$result = mysqli_query($conn, $sql);

$months = [];
$registrations = [];

while ($row = mysqli_fetch_assoc($result)) {
    $months[] = $row['month'];
    $registrations[] = (int)$row['registrations'];
}

// Data ko JSON mein encode kar ke bhejna
echo json_encode([
    'months' => $months,
    'registrations' => $registrations
]);