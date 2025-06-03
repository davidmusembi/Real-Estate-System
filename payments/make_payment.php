<?php require_once('../includes/auth.php'); require_once('../includes/db.php');
$sale_id=intval($_GET['sale_id']);
$sale=$conn->query("SELECT * FROM sales WHERE id=$sale_id")->fetch_assoc();
if (!$sale) die("Invalid sale.");
if ($_SERVER['REQUEST_METHOD']=='POST') {
    $amt=$_POST['amount']; $method=$_POST['method'];
    $conn->query("INSERT INTO payments (sale_id,amount,method) VALUES ($sale_id,$amt,'$method')");
    header("Location: view_payments.php");
}
?>
<!DOCTYPE html>
<html><head><title>Make Payment</title><link rel="stylesheet" href="../assets/css/bootstrap.min.css"></head>
<body>
    <?php include('../includes/sidebar.php'); ?>
<div class="container mt-5">
    <h3>Make Payment for <?=$sale['sale_price']?></h3>
    <form method="post">
        <div class="mb-3"><input name="amount" class="form-control" value="<?=$sale['sale_price']?>" required></div>
        <div class="mb-3">
            <select name="method" class="form-select">
                <option value="Mpesa">Mpesa</option>
                <option value="Bank Transfer">Bank Transfer</option>
            </select>
        </div>
        <button class="btn btn-primary">Pay</button>
    </form>
</div></body></html>
