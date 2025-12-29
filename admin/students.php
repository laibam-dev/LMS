<?php
include 'session.php';
include 'header.php';
include 'sidebar.php';

// Dummy students data
$students = [
    ['id'=>1, 'name'=>'Ali Khan', 'email'=>'ali@example.com', 'program'=>'CS', 'semester'=>6, 'status'=>'active'],
    ['id'=>2, 'name'=>'Sara Ahmed', 'email'=>'sara@example.com', 'program'=>'IT', 'semester'=>4, 'status'=>'blocked'],
    ['id'=>3, 'name'=>'Zara Malik', 'email'=>'zara@example.com', 'program'=>'CS', 'semester'=>5, 'status'=>'active']
];
?>

<div style="margin-left:220px; padding:20px;">
    <h2>Students Management (Dummy)</h2>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Program</th>
                <th>Semester</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($students as $student){ ?>
            <tr>
                <td><?php echo $student['id']; ?></td>
                <td><?php echo $student['name']; ?></td>
                <td><?php echo $student['email']; ?></td>
                <td><?php echo $student['program']; ?></td>
                <td><?php echo $student['semester']; ?></td>
                <td><?php echo $student['status']; ?></td>
            </tr>
            <?php } ?>
        </tbody>
    </table>
</div>
