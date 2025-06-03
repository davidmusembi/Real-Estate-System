<?php
$role = $_SESSION['role'];
$user_id = $_SESSION['user_id'] ?? null;

// Fetch minimal user info for mini-profile
$user_name = '';
$user_email = '';
if ($user_id) {
    $user_res = $conn->query("SELECT username,email FROM users WHERE id=$user_id");
    if ($user_res && $user_res->num_rows) {
        $u = $user_res->fetch_assoc();
        $user_name = htmlspecialchars($u['username']);
        $user_email = htmlspecialchars($u['email']);
    }
}
?>
<style>
   .sidebar {
    position: fixed;
    top: 0; left: 0;
    height: 100vh;
    width: 220px;
    background: linear-gradient(180deg,#23272f 0,#171923 100%);
    color: #fff;
    padding: 1.2rem 0.6rem 1rem 0.6rem;
    z-index: 1000;
    border-right: 1px solid #292b36;
    box-shadow: 1px 0 6px rgba(0,0,0,0.09);
    display: flex;
    flex-direction: column;
    font-size: 0.96rem; /* smaller sidebar text */
    overflow-y: auto;
    min-height: 0;
    max-height: 100vh;
}
.sidebar-brand {
    font-size: 1.13rem;
    font-weight: 600;
    letter-spacing: .5px;
    margin-bottom: 1.3rem;
    color: #00bcd4;
    display: flex; align-items:center;
    gap: 0.5rem;
}
.sidebar .user-mini-profile {
    background: rgba(255,255,255,0.07);
    border-radius: .75rem;
    padding: .6rem .6rem .5rem .6rem;
    margin-bottom: 1.5rem;
    font-size: .89rem;
    display: flex; align-items: center; gap: 0.6rem;
}
.sidebar .user-mini-profile .bi-person-circle {
    font-size: 1.7rem;
    color: #00bcd4;
}
.sidebar .user-mini-profile .user-details span {
    display: block;
    color: #00bcd4;
    font-weight: 500;
    margin-bottom: 0;
    font-size: 0.99em;
}
.sidebar .nav {
    flex: 1 1 auto;
    margin-bottom: 1.4rem;
}
.sidebar .nav-link {
    color: #d1d6de;
    margin-bottom: .10rem;
    padding: .52rem 0.8rem;
    font-size: 0.99rem;
    border-radius: .6rem;
    display: flex; align-items: center; gap: .60rem;
    transition: background .14s, color .13s;
    font-weight: 500;
}
.sidebar .nav-link.active, .sidebar .nav-link:hover, .sidebar .nav-link:focus {
    color: #fff !important;
    background: #283043 !important;
    box-shadow: 0 1px 7px rgba(0,188,212,.09);
}
.sidebar .nav-link .bi {
    font-size: 1.12em;
    opacity: 0.82;
}
.sidebar .nav-link.text-danger { color: #e57373 !important;}
.sidebar .nav-link.text-danger:hover { background: #442727; color: #fff!important;}
body { margin-left: 220px !important; }
@media (max-width: 767px) {
    .sidebar { position: static; width: 100%; height: auto; max-height: none; border-right: none; }
    body { margin-left: 0 !important; }
}

</style>
<head>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

</head>
<nav class="sidebar">
    <div class="sidebar-brand">
        <i class="bi bi-building"></i> Urban Realty
    </div>
    <div class="user-mini-profile mb-2">
        <i class="bi bi-person-circle"></i>
        <div class="user-details">
            <span><?= $user_name ?: ucfirst($role) ?></span>
            <small style="color:#a5a5a5;font-size:0.95em;"><?= $user_email ?></small>
        </div>
    </div>
    <ul class="nav flex-column">
        <?php if($role=='admin'): ?>
            <li><a href="../dashboards/admin_dashboard.php" class="nav-link <?=strpos($_SERVER['PHP_SELF'],'admin_analytics.php')!==false?'active':''?>"><i class="bi bi-speedometer2"></i> Dashboard</a></li>
            <li><a href="../users/manage_users.php" class="nav-link <?=strpos($_SERVER['PHP_SELF'],'manage_users.php')!==false?'active':''?>"><i class="bi bi-people"></i> Users</a></li>
            <li><a href="../properties/manage_properties.php" class="nav-link <?=strpos($_SERVER['PHP_SELF'],'manage_properties.php')!==false?'active':''?>"><i class="bi bi-house-gear"></i> Properties</a></li>
            <li><a href="../sales/view_sales.php" class="nav-link <?=strpos($_SERVER['PHP_SELF'],'view_sales.php')!==false?'active':''?>"><i class="bi bi-bag-check"></i> Sales</a></li>
            <li><a href="../payments/view_payments.php" class="nav-link <?=strpos($_SERVER['PHP_SELF'],'view_payments.php')!==false?'active':''?>"><i class="bi bi-cash-stack"></i> Payments</a></li>
        <?php elseif($role=='agent'): ?>
            <li><a href="../dashboards/agent_dashboard.php" class="nav-link <?=strpos($_SERVER['PHP_SELF'],'agent_dashboard.php')!==false?'active':''?>"><i class="bi bi-speedometer2"></i> Dashboard</a></li>
            <li><a href="../properties/my_properties.php" class="nav-link <?=strpos($_SERVER['PHP_SELF'],'my_properties.php')!==false?'active':''?>"><i class="bi bi-houses"></i> My Listings</a></li>
            <li><a href="../properties/add_property.php" class="nav-link <?=strpos($_SERVER['PHP_SELF'],'add_property.php')!==false?'active':''?>"><i class="bi bi-plus-square"></i> Add Property</a></li>
            <li><a href="../inquiries/view_inquiries.php" class="nav-link <?=strpos($_SERVER['PHP_SELF'],'view_inquiries.php')!==false?'active':''?>"><i class="bi bi-chat-dots"></i> Inquiries</a></li>
            <li><a href="../sales/view_sales.php" class="nav-link <?=strpos($_SERVER['PHP_SELF'],'view_sales.php')!==false?'active':''?>"><i class="bi bi-bag-check"></i> Sales</a></li>
            <li><a href="../payments/view_payments.php" class="nav-link <?=strpos($_SERVER['PHP_SELF'],'view_payments.php')!==false?'active':''?>"><i class="bi bi-cash-stack"></i> Payments</a></li>
            <li><a href="../users/profile.php" class="nav-link <?=strpos($_SERVER['PHP_SELF'],'profile.php')!==false?'active':''?>"><i class="bi bi-person-circle"></i> Profile</a></li>
        <?php else: ?>
            <li><a href="../dashboards/client_dashboard.php" class="nav-link <?=strpos($_SERVER['PHP_SELF'],'client_dashboard.php')!==false?'active':''?>"><i class="bi bi-house-door"></i> Home</a></li>
            <li><a href="../properties/browse.php" class="nav-link <?=strpos($_SERVER['PHP_SELF'],'browse.php')!==false?'active':''?>"><i class="bi bi-search"></i> Browse Properties</a></li>
            <li><a href="../inquiries/my_inquiries.php" class="nav-link <?=strpos($_SERVER['PHP_SELF'],'my_inquiries.php')!==false?'active':''?>"><i class="bi bi-chat-left-dots"></i> Inquiries</a></li>
            <li><a href="../sales/view_sales.php" class="nav-link <?=strpos($_SERVER['PHP_SELF'],'view_sales.php')!==false?'active':''?>"><i class="bi bi-bag"></i> My Purchases</a></li>
            <li><a href="../payments/view_payments.php" class="nav-link <?=strpos($_SERVER['PHP_SELF'],'view_payments.php')!==false?'active':''?>"><i class="bi bi-cash"></i> Payments</a></li>
            <li><a href="../users/profile.php" class="nav-link <?=strpos($_SERVER['PHP_SELF'],'profile.php')!==false?'active':''?>"><i class="bi bi-person-circle"></i> Profile</a></li>
        <?php endif; ?>
        <li><a href="../auth/logout.php" class="nav-link text-danger"><i class="bi bi-box-arrow-right"></i> Logout</a></li>
    </ul>
</nav>
