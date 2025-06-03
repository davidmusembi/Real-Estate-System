<?php
require_once('../includes/auth.php');
require_once('../includes/db.php');
$uid = $_SESSION['user_id'];
$msg = $err = "";

// Fetch user info
$row = $conn->query("SELECT * FROM users WHERE id=$uid")->fetch_assoc();

// Handle profile update
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_profile'])) {
    $email = trim($_POST['email']);
    if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $conn->query("UPDATE users SET email='$email' WHERE id=$uid");
        $msg = "Profile updated.";
        $row['email'] = $email;
    } else {
        $err = "Please enter a valid email.";
    }
}

// Handle password change
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['change_pass'])) {
    $current_pass = $_POST['current_password'];
    $new_pass = $_POST['new_password'];
    $confirm_pass = $_POST['confirm_password'];

    // Validate
    if (empty($current_pass) || empty($new_pass) || empty($confirm_pass)) {
        $err = "All password fields are required.";
    } else if (!password_verify($current_pass, $row['password'])) {
        $err = "Current password is incorrect.";
    } else if ($new_pass !== $confirm_pass) {
        $err = "New passwords do not match.";
    } else if (strlen($new_pass) < 4) {
        $err = "New password must be at least 4 characters.";
    } else {
        $new_hash = password_hash($new_pass, PASSWORD_DEFAULT);
        $conn->query("UPDATE users SET password='$new_hash' WHERE id=$uid");
        $msg = "Password changed successfully.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>My Profile</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Bootstrap 5 CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: #f8f9fa; }
        .profile-card { max-width: 500px; margin: 3rem auto; border-radius:1rem; }
        @media (max-width: 767px) { .profile-card { margin:1rem auto; } }
    </style>
</head>
<body>
<?php include('../includes/sidebar.php'); ?>
    <div class="card p-4 shadow profile-card mt-5">
        <h3 class="mb-3">My Profile</h3>
        <?php if ($msg): ?>
            <div class="alert alert-success"><?=$msg?></div>
        <?php endif; ?>
        <?php if ($err): ?>
            <div class="alert alert-danger"><?=$err?></div>
        <?php endif; ?>
        <form method="post" class="mb-4">
            <div class="mb-3">
                <label class="form-label">Username</label>
                <input name="username" class="form-control" value="<?=htmlspecialchars($row['username'])?>" disabled>
            </div>
            <div class="mb-3">
                <label class="form-label">Email</label>
                <input name="email" type="email" class="form-control" value="<?=htmlspecialchars($row['email'])?>" required>
            </div>
            <button class="btn btn-primary" name="update_profile" type="submit">Update Profile</button>
        </form>
        <hr>
        <h5 class="mb-3">Change Password</h5>
        <form method="post" autocomplete="off">
            <div class="mb-3">
                <label class="form-label">Current Password</label>
                <input name="current_password" type="password" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">New Password</label>
                <input name="new_password" type="password" class="form-control" required minlength="6">
            </div>
            <div class="mb-3">
                <label class="form-label">Confirm New Password</label>
                <input name="confirm_password" type="password" class="form-control" required minlength="6">
            </div>
            <button class="btn btn-success" name="change_pass" type="submit">Change Password</button>
        </form>
    </div>
</div>
<!-- Bootstrap JS (not strictly required but good for any Bootstrap components) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
