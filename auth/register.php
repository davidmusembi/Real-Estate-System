<?php
require_once('../includes/db.php');
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $pass = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $email = $_POST['email'];
    $sql = "INSERT INTO users (username, password, email, role) VALUES (?, ?, ?, 'client')";
    $stmt = $conn->prepare($sql); $stmt->bind_param('sss',$username,$pass,$email);
    if ($stmt->execute()) {
        header("Location: login.php?registered=1"); exit;
    } else $err = "Error creating account. Username or email may already exist.";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Client Registration | Urban Realty</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="../assets/css/bootstrap.min.css">
    <!-- Bootstrap Icons CDN -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        body {
            background: linear-gradient(120deg,#43cea2,#185a9d 90%);
            min-height: 100vh;
        }
        .register-card {
            max-width: 430px;
            margin: 3rem auto;
            border-radius: 1.4rem;
            box-shadow: 0 4px 24px rgba(30,46,80,.13);
            background: #fff;
            padding: 2.2rem 2rem 1.5rem 2rem;
        }
        .brand-logo {
            width: 52px; height: 52px; 
            background: #e1f5fe; 
            border-radius: 50%; 
            display: flex; align-items: center; justify-content: center;
            margin: 0 auto 1.2rem auto;
            font-size: 2rem;
            color: #2196f3;
        }
        .form-floating > .form-control,
        .form-floating > .form-label {
            font-size: 1.05rem;
        }
        .btn-success {
            background: linear-gradient(90deg,#43cea2 0,#185a9d 100%);
            border: none;
            font-weight: 600;
        }
        .register-links a {
            text-decoration: none;
            color: #2196f3;
            transition: color 0.17s;
        }
        .register-links a:hover { color: #0d47a1; text-decoration: underline; }
        .alert { font-size: 0.97rem;}
        @media (max-width: 575px) {
            .register-card { padding: 1.3rem 0.7rem;}
        }
    </style>
</head>
<body>
<div class="container">
    <div class="register-card mt-5">
        <div class="brand-logo mb-2">
            <i class="bi bi-person-plus"></i>
        </div>
        <h3 class="text-center mb-4 fw-bold" style="letter-spacing:.5px;color:#185a9d">Client Registration</h3>
        <?php if(isset($err)): ?>
            <div class="alert alert-danger text-center"><?=$err?></div>
        <?php endif; ?>
        <form method="post" autocomplete="off">
            <div class="form-floating mb-3">
                <input name="username" class="form-control" id="floatingUsername" placeholder="Username" required>
                <label for="floatingUsername"><i class="bi bi-person me-1"></i>Username</label>
            </div>
            <div class="form-floating mb-3">
                <input name="email" type="email" class="form-control" id="floatingEmail" placeholder="Email" required>
                <label for="floatingEmail"><i class="bi bi-envelope me-1"></i>Email</label>
            </div>
            <div class="form-floating mb-3">
                <input name="password" type="password" class="form-control" id="floatingPassword" placeholder="Password" required>
                <label for="floatingPassword"><i class="bi bi-lock me-1"></i>Password</label>
            </div>
            <button class="btn btn-success w-100 py-2 mb-2" type="submit">
                <i class="bi bi-person-plus me-1"></i>Register
            </button>
        </form>
        <div class="mt-3 text-center register-links">
            <span>Already have an account?</span>
            <a href="login.php" class="ms-1">Login</a>
        </div>
    </div>
</div>
</body>
</html>
