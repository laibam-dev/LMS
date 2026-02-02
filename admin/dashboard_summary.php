<?php
include '../config/db.php';
// Get last 5 activities
$activities = mysqli_query($conn, "SELECT * FROM activity_log ORDER BY created_at DESC LIMIT 5");
?>
<table class="table table-sm table-bordered mb-0">
    <thead style="background:#003366;color:#FFD700;">
        <tr>
            <th>Activity</th>
            <th>Date</th>
        </tr>
    </thead>
    <tbody>
    <?php
    if (mysqli_num_rows($activities) > 0) {
        while($row = mysqli_fetch_assoc($activities)) {
            echo '<tr>';
            echo '<td>'.htmlspecialchars($row['description']).'</td>';
            echo '<td>'.htmlspecialchars($row['created_at']).'</td>';
            echo '</tr>';
        }
    } else {
        echo '<tr><td colspan="2" class="text-center">No recent activity.</td></tr>';
    }
    ?>
    </tbody>
</table>
