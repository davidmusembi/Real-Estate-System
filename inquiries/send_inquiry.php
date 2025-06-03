<?php require_once('../includes/auth.php'); require_once('../includes/db.php');
if ($_SESSION['role'] != 'client') die('Forbidden');
$pid=intval($_GET['id']);
if ($_SERVER['REQUEST_METHOD']=='POST') {
    $msg=trim($_POST['message']);
    $cid=$_SESSION['user_id'];
    $conn->query("INSERT INTO inquiries (client_id,property_id,message) VALUES ($cid,$pid,'$msg')");
    header("Location: my_inquiries.php");
}
$p=$conn->query("SELECT title FROM properties WHERE id=$pid")->fetch_assoc();
?>
<!DOCTYPE html>
<html><head>
<title>Send Inquiry</title>
<link rel="stylesheet" href="../assets/css/bootstrap.min.css">
</head>
<body>
    <?php include('../includes/sidebar.php'); ?>
<div class="container mt-5">
    <h3>Inquiry: <?=$p['title']?></h3>
    <form method="post">
        <div class="mb-3"><textarea name="message" class="form-control" required placeholder="Write your message"></textarea></div>
        <button class="btn btn-success">Send Inquiry</button>
    </form>
</div>
</body>
</html>
