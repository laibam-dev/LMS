<?php
include 'session.php';
include 'header.php';
include 'sidebar.php';

// Dummy instructors data
$instructors = [
    ['id'=>1, 'name'=>'Dr. Imran', 'email'=>'imran@example.com', 'department'=>'CS', 'designation'=>'Professor', 'status'=>'active'],
    ['id'=>2, 'name'=>'Mrs. Ayesha', 'email'=>'ayesha@example.com', 'department'=>'IT', 'designation'=>'Assistant Professor', 'status'=>'blocked'],
    ['id'=>3, 'name'=>'Mr. Salman', 'email'=>'salman@example.com', 'department'=>'CS', 'designation'=>'Lecturer', 'status'=>'active']
];
?>

<div style="margin-left:220px; padding:20px;">
    <h2>Instructors Management (Dummy)</h2>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Department</th>
                <th>Designation</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($instructors as $inst){ ?>
            <tr>
                <td><?php echo $inst['id']; ?></td>
                <td><?php echo $inst['name']; ?></td>
                <td><?php echo $inst['email']; ?></td>
                <td><?php echo $inst['department']; ?></td>
                <td><?php echo $inst['designation']; ?></td>
                <td><?php echo $inst['status']; ?></td>
            </tr>
            <?php } ?>
        </tbody>
    </table>
</div>
