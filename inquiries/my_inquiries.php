<?php require_once('../includes/auth.php'); require_once('../includes/db.php');
$cid = $_SESSION['user_id'];
$res = $conn->query("SELECT i.*, p.title FROM inquiries i JOIN properties p ON i.property_id=p.id WHERE i.client_id=$cid ORDER BY i.id DESC");
?>
<!DOCTYPE html>
<html><head><title>My Inquiries</title><link rel="stylesheet" href="../assets/css/bootstrap.min.css"></head>
<body><div class="container mt-5">
    <?php include('../includes/sidebar.php'); ?>
    <h3>My Inquiries</h3>
    <table class="table table-bordered">
        <tr><th>Property</th><th>Message</th><th>Status</th><th>Reply</th><th>Date</th></tr>
        <?php while($row = $res->fetch_assoc()): ?>
        <tr>
            <td><?=$row['title']?></td>
            <td><?=htmlspecialchars($row['message'])?></td>
            <td><?=$row['status']?></td>
            <td><?=htmlspecialchars($row['reply'] ?? '')?></td>
            <td><?=$row['created_at']?></td>
        </tr>
        <?php endwhile; ?>
    </table>
</div></body></html>
