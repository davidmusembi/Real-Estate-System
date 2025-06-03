<?php require_once('../includes/auth.php'); require_once('../includes/db.php');
if ($_SESSION['role'] != 'agent') die('Forbidden');
$aid = $_SESSION['user_id'];
$res = $conn->query("SELECT * FROM properties WHERE agent_id=$aid ORDER BY id DESC");
?>
<!DOCTYPE html>
<html><head>
    <title>My Properties</title>
    <link rel="stylesheet" href="../assets/css/bootstrap.min.css">
</head><body>
    <?php include('../includes/sidebar.php'); ?>
<div class="container mt-5">
    <h3>My Property Listings</h3>
    <a href="add_property.php" class="btn btn-primary mb-3">Add Property</a>
    <table class="table table-bordered">
        <tr><th>Title</th><th>Type</th><th>Price</th><th>Status</th><th>Actions</th></tr>
        <?php while($row=$res->fetch_assoc()): ?>
        <tr>
            <td><?=$row['title']?></td>
            <td><?=$row['type']?></td>
            <td>$<?=number_format($row['price'])?></td>
            <td><?=$row['status']?></td>
            <td>
                <a href="edit_property.php?id=<?=$row['id']?>" class="btn btn-sm btn-warning">Edit</a>
                <a href="delete_property.php?id=<?=$row['id']?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete?')">Delete</a>
            </td>
        </tr>
        <?php endwhile; ?>
    </table>
</div></body></html>
