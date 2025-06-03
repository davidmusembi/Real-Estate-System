<?php
require_once('../includes/auth.php');
require_once('../includes/db.php');

if ($_SESSION['role'] != 'admin') die('Forbidden');

// Handle user deletion
if (isset($_GET['delete'])) {
    $deleteId = intval($_GET['delete']);
    $conn->query("DELETE FROM users WHERE id = $deleteId");
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}

// Handle new user addition
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['username'])) {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $pass = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role = $_POST['role'];
    $conn->query("INSERT INTO users (username,password,email,role) VALUES ('$username','$pass','$email','$role')");
}

$res = $conn->query("SELECT * FROM users ORDER BY id DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Manage Users | Urban Realty</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="../assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        body { background: #f8f9fa;}
        .manage-users-card { border-radius:1.2rem; box-shadow:0 2px 16px rgba(30,46,80,.09);}
        .table-users th, .table-users td { font-size:1.04rem;}
        .table-users tr:hover { background: #f2f7fa; }
        .badge-role { font-size:.97rem; }
        .badge-role.agent { background: #43cea2; color:#fff; }
        .badge-role.client { background: #2196f3; color:#fff; }
        .badge-role.admin { background: #ff9800; color:#fff; }
        .form-title { color: #185a9d; font-weight: 600; }
        .btn-add-user { font-weight:600; }
        @media (max-width: 800px) {
            .table-responsive { font-size: 0.96em;}
        }
    </style>
</head>
<body>
<?php include('../includes/sidebar.php'); ?>
<div class="container" style="max-width:1000px;">
    <div class="d-flex align-items-center mt-5 mb-4">
        <i class="bi bi-people fs-2 me-2 text-primary"></i>
        <h3 class="form-title mb-0">Manage Users</h3>
    </div>
    <div class="card manage-users-card p-4 mb-4">
        <form method="post" autocomplete="off">
            <div class="row g-2 align-items-center mb-2">
                <div class="col-md-3">
                    <div class="form-floating">
                        <input name="username" class="form-control" id="floatingUsername" placeholder="Username" required>
                        <label for="floatingUsername"><i class="bi bi-person"></i> Username</label>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-floating">
                        <input name="email" class="form-control" id="floatingEmail" placeholder="Email" type="email" required>
                        <label for="floatingEmail"><i class="bi bi-envelope"></i> Email</label>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-floating">
                        <input name="password" type="password" class="form-control" id="floatingPassword" placeholder="Password" required>
                        <label for="floatingPassword"><i class="bi bi-lock"></i> Password</label>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-floating">
                        <select name="role" class="form-select" id="floatingRole" required>
                            <option value="agent">Agent</option>
                            <option value="client">Client</option>
                            <option value="admin">Admin</option>
                        </select>
                        <label for="floatingRole"><i class="bi bi-person-badge"></i> Role</label>
                    </div>
                </div>
                <div class="col-md-1">
                    <button class="btn btn-success w-100 btn-add-user" type="submit"><i class="bi bi-plus-circle"></i></button>
                </div>
            </div>
        </form>
    </div>

    <div class="card manage-users-card p-4 mb-5">
        <div class="table-responsive">
            <table class="table table-hover align-middle table-users mb-0">
                <thead class="table-light">
                <tr>
                    <th><i class="bi bi-person"></i> Username</th>
                    <th><i class="bi bi-envelope"></i> Email</th>
                    <th><i class="bi bi-person-badge"></i> Role</th>
                    <th><i class="bi bi-calendar-event"></i> Date</th>
                    <th><i class="bi bi-gear"></i> Action</th>
                </tr>
                </thead>
                <tbody>
                <?php while($row = $res->fetch_assoc()): ?>
                    <tr>
                        <td><?=htmlspecialchars($row['username'])?></td>
                        <td><?=htmlspecialchars($row['email'])?></td>
                        <td>
                            <?php
                            $role = $row['role'];
                            $badge = $role == 'admin' ? 'admin' : ($role == 'agent' ? 'agent' : 'client');
                            ?>
                            <span class="badge badge-role <?=$badge?>"><?=ucfirst($role)?></span>
                        </td>
                        <td><?=date('d M Y', strtotime($row['created_at'] ?? date('Y-m-d')))?></td>
                        <td>
                            <a href="edit_user.php?id=<?=$row['id']?>" class="btn btn-sm btn-primary"><i class="bi bi-pencil"></i></a>
                            <a href="?delete=<?=$row['id']?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete this user?');"><i class="bi bi-trash"></i></a>
                        </td>
                    </tr>
                <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
</body>
</html>
