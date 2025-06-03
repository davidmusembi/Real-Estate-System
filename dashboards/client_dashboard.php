<?php
require_once('../includes/auth.php');
require_once('../includes/db.php');
$cid = $_SESSION['user_id'];

// Fetch username for welcome message
$user_row = $conn->query("SELECT username FROM users WHERE id=$cid")->fetch_assoc();
$username = $user_row ? $user_row['username'] : 'Client';

// Quick stats
$tot_inquiries = $conn->query("SELECT COUNT(*) FROM inquiries WHERE client_id=$cid")->fetch_row()[0];
$pending_inquiries = $conn->query("SELECT COUNT(*) FROM inquiries WHERE client_id=$cid AND status='pending'")->fetch_row()[0];
$tot_purchases = $conn->query("SELECT COUNT(*) FROM sales WHERE client_id=$cid")->fetch_row()[0];

// Detect correct payments column or join
$tot_payments = 0;
$columns = [];
$res = $conn->query("SHOW COLUMNS FROM payments");
while($row = $res->fetch_assoc()) $columns[] = $row['Field'];

if (in_array('client_id', $columns)) {
    $tot_payments = $conn->query("SELECT IFNULL(SUM(amount),0) FROM payments WHERE client_id=$cid")->fetch_row()[0];
} elseif (in_array('user_id', $columns)) {
    $tot_payments = $conn->query("SELECT IFNULL(SUM(amount),0) FROM payments WHERE user_id=$cid")->fetch_row()[0];
} elseif (in_array('sale_id', $columns)) {
    $tot_payments = $conn->query("
        SELECT IFNULL(SUM(p.amount),0) 
        FROM payments p
        JOIN sales s ON p.sale_id = s.id
        WHERE s.client_id = $cid
    ")->fetch_row()[0];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Client Dashboard | Urban Realty</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="../assets/css/bootstrap.min.css">
    <style>
        body { background: #f8f9fa; }
        .dashboard-card { border-radius:1rem; }
        .dashboard-section { margin-bottom: 2.5rem; }
        .table td, .table th { vertical-align: middle; }
        .quick-link-card { border-radius:1rem; min-height:130px; }
    </style>
</head>
<body>
<?php include('../includes/sidebar.php'); ?>
<div class="container mt-4">
    <h2 class="mb-4">Welcome, <?=htmlspecialchars($username)?>!</h2>
    <!-- Quick Stats Overview -->
    <div class="row g-4 dashboard-section">
        <div class="col-md-3">
            <div class="card dashboard-card shadow text-center p-3">
                <div class="h4 mb-2 text-primary"><?=$tot_inquiries?></div>
                <div>Total Inquiries</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card dashboard-card shadow text-center p-3">
                <div class="h4 mb-2 text-warning"><?=$pending_inquiries?></div>
                <div>Pending Inquiries</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card dashboard-card shadow text-center p-3">
                <div class="h4 mb-2 text-success"><?=$tot_purchases?></div>
                <div>Properties Purchased</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card dashboard-card shadow text-center p-3">
                <div class="h4 mb-2 text-info">$<?=number_format($tot_payments)?></div>
                <div>Total Payments</div>
            </div>
        </div>
    </div>

    <!-- Quick Action Links -->
    <div class="row g-4 dashboard-section">
        <div class="col-md-3">
            <a href="../properties/browse.php" class="card quick-link-card text-decoration-none shadow text-center p-4 bg-primary text-white">
                <div class="fs-2 mb-1"><i class="bi bi-search"></i></div>
                Browse Properties
            </a>
        </div>
        <div class="col-md-3">
            <a href="../inquiries/my_inquiries.php" class="card quick-link-card text-decoration-none shadow text-center p-4 bg-warning text-dark">
                <div class="fs-2 mb-1"><i class="bi bi-chat-left-dots"></i></div>
                My Inquiries
            </a>
        </div>
        <div class="col-md-3">
            <a href="../sales/view_sales.php" class="card quick-link-card text-decoration-none shadow text-center p-4 bg-success text-white">
                <div class="fs-2 mb-1"><i class="bi bi-bag-check"></i></div>
                My Purchases
            </a>
        </div>
        <div class="col-md-3">
            <a href="../users/profile.php" class="card quick-link-card text-decoration-none shadow text-center p-4 bg-info text-white">
                <div class="fs-2 mb-1"><i class="bi bi-person-circle"></i></div>
                My Profile
            </a>
        </div>
    </div>

    <!-- Recent Inquiries -->
    <div class="dashboard-section">
        <h4 class="mb-3">Recent Inquiries</h4>
        <div class="table-responsive">
        <table class="table table-bordered bg-white">
            <thead class="table-light">
                <tr>
                    <th>Property</th>
                    <th>Message</th>
                    <th>Status</th>
                    <th>Date</th>
                    <th>Reply</th>
                </tr>
            </thead>
            <tbody>
            <?php
            $sql = "SELECT i.*, p.title FROM inquiries i JOIN properties p ON i.property_id=p.id WHERE i.client_id=$cid ORDER BY i.id DESC LIMIT 5";
            $res = $conn->query($sql);
            if ($res->num_rows == 0): ?>
                <tr><td colspan="5" class="text-center text-muted">No inquiries yet.</td></tr>
            <?php endif;
            while($row = $res->fetch_assoc()): ?>
            <tr>
                <td><?=htmlspecialchars($row['title'])?></td>
                <td><?=htmlspecialchars($row['message'])?></td>
                <td>
                    <span class="badge <?=($row['status']=='replied'?'bg-success':'bg-warning text-dark')?>"><?=$row['status']?></span>
                </td>
                <td><?=date('d M Y',strtotime($row['created_at']))?></td>
                <td>
                    <?php if($row['status']=='replied') echo htmlspecialchars($row['reply']); else echo '<span class="text-muted">-</span>'; ?>
                </td>
            </tr>
            <?php endwhile; ?>
            </tbody>
        </table>
        </div>
        <div><a href="../inquiries/my_inquiries.php" class="btn btn-outline-primary btn-sm">View All Inquiries</a></div>
    </div>

    <!-- Recent Purchases/Transactions -->
    <div class="dashboard-section">
        <h4 class="mb-3">Recent Purchases</h4>
        <div class="table-responsive">
        <table class="table table-bordered bg-white">
            <thead class="table-light">
                <tr>
                    <th>Property</th>
                    <th>Amount</th>
                    <th>Date</th>
                    <th>Invoice</th>
                </tr>
            </thead>
            <tbody>
            <?php
            $sql = "SELECT s.*, p.title FROM sales s JOIN properties p ON s.property_id=p.id WHERE s.client_id=$cid ORDER BY s.id DESC LIMIT 5";
            $res = $conn->query($sql);
            if ($res->num_rows == 0): ?>
                <tr><td colspan="4" class="text-center text-muted">No purchases yet.</td></tr>
            <?php endif;
            while($row = $res->fetch_assoc()): ?>
            <tr>
                <td><?=htmlspecialchars($row['title'])?></td>
                <td>$<?=number_format($row['sale_price'])?></td>
                <td><?=date('d M Y',strtotime($row['sale_date']))?></td>
                <td>
                    <a href="../sales/invoice.php?id=<?=$row['id']?>" class="btn btn-outline-secondary btn-sm">View Invoice</a>
                </td>
            </tr>
            <?php endwhile; ?>
            </tbody>
        </table>
        </div>
        <div><a href="../sales/view_sales.php" class="btn btn-outline-success btn-sm">View All Purchases</a></div>
    </div>
</div>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<script src="../assets/js/bootstrap.bundle.min.js"></script>
</body>
</html>
