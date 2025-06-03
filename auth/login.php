<?php
require_once('../includes/db.php');
session_start();
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username']; $password = $_POST['password'];
    $sql = "SELECT * FROM users WHERE username=?";
    $stmt = $conn->prepare($sql); $stmt->bind_param('s',$username); $stmt->execute();
    $res = $stmt->get_result()->fetch_assoc();
    if ($res && password_verify($password, $res['password'])) {
        $_SESSION['user_id'] = $res['id']; $_SESSION['role'] = $res['role'];
        header("Location: ../dashboards/{$res['role']}_dashboard.php"); exit;
    } else $err = "Invalid credentials.";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Login | Urban Realty</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="../assets/css/bootstrap.min.css">
    <!-- Bootstrap Icons CDN -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        body {
            background: linear-gradient(120deg,#43cea2,#185a9d 90%);
            min-height: 100vh;
        }
        .login-card {
            max-width: 410px;
            margin: 3.5rem auto;
            border-radius: 1.4rem;
            box-shadow: 0 4px 24px rgba(30,46,80,.11);
            background: #fff;
            padding: 2.3rem 2rem 1.5rem 2rem;
        }
        .brand-logo {
            width: 52px; height: 52px; 
            background: #e1f5fe; 
            border-radius: 50%; 
            display: flex; align-items: center; justify-content: center;
            margin: 0 auto 1.3rem auto;
            font-size: 2rem;
            color: #2196f3;
        }
        .form-floating > .form-control,
        .form-floating > .form-label {
            font-size: 1.05rem;
        }
        .btn-primary {
            background: linear-gradient(90deg,#43cea2 0,#185a9d 100%);
            border: none;
            font-weight: 600;
        }
        .login-links a {
            text-decoration: none;
            color: #2196f3;
            transition: color 0.17s;
        }
        .login-links a:hover { color: #0d47a1; text-decoration: underline; }
        .alert { font-size: 0.97rem;}
        @media (max-width: 575px) {
            .login-card { padding: 1.3rem 0.7rem;}
        }
    </style>
</head>
<body>
<div class="container">
    <div class="login-card mt-5">
        <div class="brand-logo mb-2">
            <i class="bi bi-building"></i>
        </div>
        <h3 class="text-center mb-4 fw-bold" style="letter-spacing:.5px;color:#185a9d">Sign in</h3>
        <?php if(isset($err)): ?>
            <div class="alert alert-danger text-center"><?=$err?></div>
        <?php endif; ?>
        <form method="post" autocomplete="off">
            <div class="form-floating mb-3">
                <input name="username" class="form-control" id="floatingInput" placeholder="Username" required autofocus>
                <label for="floatingInput"><i class="bi bi-person me-1"></i>Username</label>
            </div>
            <div class="form-floating mb-3">
                <input name="password" type="password" class="form-control" id="floatingPassword" placeholder="Password" required>
                <label for="floatingPassword"><i class="bi bi-lock me-1"></i>Password</label>
            </div>
            <button class="btn btn-primary w-100 py-2 mb-2" type="submit">
                <i class="bi bi-box-arrow-in-right me-1"></i>Login
            </button>
        </form>
        <div class="mt-3 text-center login-links">
            <span>New here?</span>
            <a href="register.php" class="ms-1">Register as Client</a>
        </div>
    </div>
</div>
</body>
</html>
