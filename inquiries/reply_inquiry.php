<?php require_once('../includes/auth.php'); require_once('../includes/db.php');
if ($_SESSION['role'] != 'agent') die('Forbidden');
$id=intval($_GET['id']);
$row=$conn->query("SELECT i.*, p.agent_id FROM inquiries i JOIN properties p ON i.property_id=p.id WHERE i.id=$id")->fetch_assoc();
if(!$row || $row['agent_id']!=$_SESSION['user_id']) die('Forbidden');
if ($_SERVER['REQUEST_METHOD']=='POST') {
    $reply=$_POST['reply'];
    $conn->query("UPDATE inquiries SET reply='$reply',status='replied' WHERE id=$id");
    header("Location: view_inquiries.php");
}
?>
<!DOCTYPE html>
<html><head><title>Reply Inquiry</title><link rel="stylesheet" href="../assets/css/bootstrap.min.css"></head>
<body>
    <?php include('../includes/sidebar.php'); ?>
<div class="container mt-5">
    <h3>Reply to Inquiry</h3>
    <p><strong>Client Message:</strong> <?=htmlspecialchars($row['message'])?></p>
    <form method="post">
        <div class="mb-3"><textarea name="reply" class="form-control" required></textarea></div>
        <button class="btn btn-primary">Send Reply</button>
    </form>
</div></body></html>
