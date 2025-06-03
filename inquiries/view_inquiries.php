<?php require_once('../includes/auth.php'); require_once('../includes/db.php');
if ($_SESSION['role'] != 'agent') die('Forbidden');
$aid=$_SESSION['user_id'];
$sql="SELECT i.*, p.title FROM inquiries i JOIN properties p ON i.property_id=p.id WHERE p.agent_id=$aid ORDER BY i.id DESC";
$res=$conn->query($sql);
?>
<!DOCTYPE html>
<html><head><title>Property Inquiries</title><link rel="stylesheet" href="../assets/css/bootstrap.min.css"></head>
<body>
    <?php include('../includes/sidebar.php'); ?>
    <div class="container mt-5">
    <h3>Inquiries for Your Properties</h3>
    <table class="table table-bordered">
        <tr><th>Property</th><th>Message</th><th>Status</th><th>Reply</th><th>Action</th><th>Date</th></tr>
        <?php while($row=$res->fetch_assoc()): ?>
        <tr>
            <td><?=$row['title']?></td>
            <td><?=htmlspecialchars($row['message'])?></td>
            <td><?=$row['status']?></td>
            <td><?=htmlspecialchars($row['reply'] ?? '')?></td>
            <td>
                <?php if($row['status']=='pending'): ?>
                <a href="reply_inquiry.php?id=<?=$row['id']?>" class="btn btn-sm btn-primary">Reply</a>
                <?php endif; ?>
            </td>
            <td><?=$row['created_at']?></td>
        </tr>
        <?php endwhile; ?>
    </table>
</div></body></html>
