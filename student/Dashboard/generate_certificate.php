<?php
session_start();
require_once '../../config/db.php';
require('../../assets/fpdf/fpdf.php');

$student_id = $_SESSION['student_id'];
$course_id = $_GET['course_id'] ?? die("Course not found");


$stmt = $conn->prepare("
    SELECT a.name AS student_name, c.title AS course_title
    FROM account a
    JOIN course c ON c.id = ?
    WHERE a.id = ?
");
$stmt->bind_param("ii", $course_id, $student_id);
$stmt->execute();
$result = $stmt->get_result();
if($result->num_rows == 0){
    die("Invalid student or course");
}
$data = $result->fetch_assoc();

$pdf = new FPDF();
$pdf->AddPage();
$pdf->SetFont('Arial','B',16);
$pdf->Cell(0,10,'Certificate of Completion',0,1,'C');
$pdf->Ln(10);
$pdf->SetFont('Arial','',12);
$pdf->Cell(0,10,"This is to certify that ".$data['student_name']." has successfully completed the course ".$data['course_title'].".",0,1,'C');
$pdf->Ln(20);
$pdf->Cell(0,10,"Date: ".date('Y-m-d'),0,1,'C');


$filename = "../../certificates/certificate_".$student_id."_".$course_id.".pdf";
$pdf->Output('F', $filename);

$update = $conn->prepare("UPDATE certificates SET status='Generated', issue_date=NOW() WHERE student_id=? AND course_id=?");
$update->bind_param("ii",$student_id,$course_id);
$update->execute();

header("Location: certificates.php");
exit();
