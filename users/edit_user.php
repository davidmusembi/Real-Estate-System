<?php
require_once('../includes/auth.php');
require_once('../includes/db.php');

if ($_SESSION['role'] != 'admin') die('Forbidden');

$id = intval($_GET['id']);
$result = $conn->query("SELECT * FROM users WHERE id = $id");
$user = $result->fetch_assoc();

if (!$user) die("User not found.");

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $role = $_POST['role'];
    $passwordPart = '';
    if (!empty($_POST['password'])) {
        $hashed = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $passwordPart = ", password = '$hashed'";
    }
    $conn->query("UPDATE users SET username='$username', email='$email', role='$role' $passwordPart WHERE id=$id");
    header("Location: manage_users.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Edit User | Urban Realty</title>
    <link rel="stylesheet" href="../assets/css/bootstrap.min.css">
</head>
<body class="bg-light">
    <?php include('../includes/sidebar.php'); ?>
<div class="container mt-5" style="max-width:600px;">
    <h3 class="mb-4">Edit User</h3>
    <form method="post">
        <div class="form-floating mb-3">
            <input type="text" name="username" value="<?=htmlspecialchars($user['username'])?>" class="form-control" id="floatingUsername" required>
            <label for="floatingUsername">Username</label>
        </div>
        <div class="form-floating mb-3">
            <input type="email" name="email" value="<?=htmlspecialchars($user['email'])?>" class="form-control" id="floatingEmail" required>
            <label for="floatingEmail">Email</label>
        </div>
        <div class="form-floating mb-3">
            <select name="role" class="form-select" id="floatingRole" required>
                <option value="admin" <?=$user['role']=='admin'?'selected':''?>>Admin</option>
                <option value="agent" <?=$user['role']=='agent'?'selected':''?>>Agent</option>
                <option value="client" <?=$user['role']=='client'?'selected':''?>>Client</option>
            </select>
            <label for="floatingRole">Role</label>
        </div>
        <div class="form-floating mb-3">
            <input type="password" name="password" class="form-control" id="floatingPassword">
            <label for="floatingPassword">New Password (leave blank to keep current)</label>
        </div>
        <button class="btn btn-primary" type="submit">Save Changes</button>
        <a href="manage_users.php" class="btn btn-secondary">Cancel</a>
    </form>
</div>
</body>
</html>
