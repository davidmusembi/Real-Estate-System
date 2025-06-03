<?php require_once('../includes/db.php'); session_start();
$id=intval($_GET['id']);
$p=$conn->query("SELECT p.*, u.username agent FROM properties p JOIN users u ON p.agent_id=u.id WHERE p.id=$id")->fetch_assoc();
if(!$p) die('Not found.');
?>
<!DOCTYPE html>
<html>
<head>
    <title><?=$p['title']?> - Details</title>
    <link rel="stylesheet" href="../assets/css/bootstrap.min.css">
</head>
<body>
        <?php include('../includes/sidebar.php'); ?>
<div class="container mt-4">
    <div class="row">
        <div class="col-md-7">
            <img src="../assets/images/<?=$p['image']?>" class="w-100" style="max-height:350px;object-fit:cover;">
        </div>
        <div class="col-md-5">
            <h2><?=$p['title']?></h2>
            <p><?=$p['description']?></p>
            <p><strong>Type:</strong> <?=$p['type']?></p>
            <p><strong>Location:</strong> <?=$p['location']?></p>
            <p><strong>Price:</strong> $<?=number_format($p['price'])?></p>
            <p><strong>Agent:</strong> <?=$p['agent']?></p>
            <p><strong>Status:</strong> <?=$p['status']?></p>
            <?php if($p['status']=='available'): ?>
                <?php if(isset($_SESSION['role']) && $_SESSION['role']=='client'): ?>
                    <a href="../sales/buy_property.php?id=<?=$p['id']?>" class="btn btn-success mb-2">Buy Property</a>
                <?php endif; ?>
                <a href="../inquiries/send_inquiry.php?id=<?=$p['id']?>" class="btn btn-outline-primary mb-2">Contact Agent</a>
            <?php endif; ?>
        </div>
    </div>
</div>
</body>
</html>
