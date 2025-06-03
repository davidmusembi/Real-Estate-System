<?php require_once('../includes/auth.php'); require_once('../includes/db.php');
if ($_SESSION['role'] != 'client') die('Forbidden');
$property_id = intval($_GET['id']);
$property = $conn->query("SELECT * FROM properties WHERE id=$property_id AND status='available'")->fetch_assoc();
if (!$property) die('Property not available.');
if ($_SERVER['REQUEST_METHOD']=='POST') {
    $cid=$_SESSION['user_id']; $aid=$property['agent_id']; $price=$property['price'];
    $conn->query("UPDATE properties SET status='sold' WHERE id=$property_id");
    $conn->query("INSERT INTO sales (property_id,client_id,agent_id,sale_price) VALUES ($property_id,$cid,$aid,$price)");
    $sale_id = $conn->insert_id;
    header("Location: ../payments/make_payment.php?sale_id=$sale_id");
}
?>
<!DOCTYPE html>
<html><head><title>Buy Property</title><link rel="stylesheet" href="../assets/css/bootstrap.min.css"></head>
<body>
    <?php include('../includes/sidebar.php'); ?>
    <div class="container mt-5">
        <h3>Property Details</h3>
        <div class="card mb-3">
            <!-- <img src="../assets/images/<?=$property['image']?>" class="card-img-top" alt="<?=$property['title']?>"> -->
            <div class="card-body">
                <h5 class="card-title
<div class="container mt-5">
    <h3>Buy <?=$property['title']?> - $<?=number_format($property['price'])?></h3>
    <form method="post">
        <button class="btn btn-success">Confirm & Pay</button>
    </form>
</div></body></html>
