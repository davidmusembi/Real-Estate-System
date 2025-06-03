<?php
require_once('../includes/auth.php');
require_once('../includes/db.php');
if ($_SESSION['role'] != 'admin') die('Forbidden');

// Total properties
$total_props = $conn->query("SELECT COUNT(*) FROM properties")->fetch_row()[0];
// Available
$avail_props = $conn->query("SELECT COUNT(*) FROM properties WHERE status='available'")->fetch_row()[0];
// Sold
$sold_props = $conn->query("SELECT COUNT(*) FROM properties WHERE status='sold'")->fetch_row()[0];

// Users by role
$admins = $conn->query("SELECT COUNT(*) FROM users WHERE role='admin'")->fetch_row()[0];
$agents = $conn->query("SELECT COUNT(*) FROM users WHERE role='agent'")->fetch_row()[0];
$clients = $conn->query("SELECT COUNT(*) FROM users WHERE role='client'")->fetch_row()[0];

// Inquiries
$total_inq = $conn->query("SELECT COUNT(*) FROM inquiries")->fetch_row()[0];
$pending_inq = $conn->query("SELECT COUNT(*) FROM inquiries WHERE status='pending'")->fetch_row()[0];
$replied_inq = $conn->query("SELECT COUNT(*) FROM inquiries WHERE status='replied'")->fetch_row()[0];

// Sales
$total_sales = $conn->query("SELECT COUNT(*) FROM sales")->fetch_row()[0];
$total_revenue = $conn->query("SELECT IFNULL(SUM(sale_price),0) FROM sales")->fetch_row()[0];

// Property type breakdown (for mini chart)
$types = ['apartment', 'house', 'land', 'commercial'];
$type_counts = [];
foreach($types as $type) {
    $type_counts[$type] = $conn->query("SELECT COUNT(*) FROM properties WHERE type='$type'")->fetch_row()[0];
}
$type_total = array_sum($type_counts);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Admin dashboard | Urban Realty</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="../assets/css/bootstrap.min.css">
    <style>
        .analytics-card { border-radius:1rem; }
        .progress-bar { font-size: .95rem; }
    </style>
</head>
<body>
<?php include('../includes/sidebar.php'); ?>

<div class="container mt-4">
    <h2 class="mb-4">Analytics Dashboard</h2>
    <div class="row g-4 mb-4">
        <div class="col-md-3">
            <div class="card analytics-card shadow text-center p-3">
                <div class="h5 mb-2 text-primary"><?= $total_props ?></div>
                <div>Total Properties</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card analytics-card shadow text-center p-3">
                <div class="h5 mb-2 text-success"><?= $avail_props ?></div>
                <div>Available Properties</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card analytics-card shadow text-center p-3">
                <div class="h5 mb-2 text-danger"><?= $sold_props ?></div>
                <div>Sold Properties</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card analytics-card shadow text-center p-3">
                <div class="h5 mb-2 text-info"><?= $total_sales ?></div>
                <div>Properties Sold (Sales)</div>
            </div>
        </div>
    </div>
    <div class="row g-4 mb-4">
        <div class="col-md-3">
            <div class="card analytics-card shadow text-center p-3">
                <div class="h5 mb-2 text-secondary"><?= $clients ?></div>
                <div>Total Clients</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card analytics-card shadow text-center p-3">
                <div class="h5 mb-2 text-secondary"><?= $agents ?></div>
                <div>Total Agents</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card analytics-card shadow text-center p-3">
                <div class="h5 mb-2 text-secondary"><?= $admins ?></div>
                <div>Total Admins</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card analytics-card shadow text-center p-3">
                <div class="h5 mb-2 text-warning"><?= $total_inq ?></div>
                <div>Total Inquiries</div>
            </div>
        </div>
    </div>
    <div class="row g-4 mb-4">
        <div class="col-md-6">
            <div class="card analytics-card shadow p-3">
                <h6 class="mb-3">Inquiries Status</h6>
                <div>Pending: <span class="badge bg-warning"><?= $pending_inq ?></span></div>
                <div>Replied: <span class="badge bg-success"><?= $replied_inq ?></span></div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card analytics-card shadow p-3">
                <h6 class="mb-3">Total Revenue (Sales)</h6>
                <div class="h4 text-success">$<?= number_format($total_revenue) ?></div>
            </div>
        </div>
    </div>
    <div class="row g-4 mb-5">
        <div class="col-md-12">
            <div class="card analytics-card shadow p-3">
                <h6 class="mb-3">Property Type Distribution</h6>
                <?php foreach($types as $type):
                    $percent = $type_total ? round($type_counts[$type] / $type_total * 100) : 0;
                ?>
                    <div class="mb-2"><strong><?= ucfirst($type) ?>:</strong>
                        <span class="ms-1"><?= $type_counts[$type] ?> (<?= $percent ?>%)</span>
                        <div class="progress mt-1" style="height:20px;">
                            <div class="progress-bar bg-primary" role="progressbar" style="width: <?= $percent ?>%;" aria-valuenow="<?= $percent ?>" aria-valuemin="0" aria-valuemax="100"><?= $percent ?>%</div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>
<script src="../assets/js/bootstrap.bundle.min.js"></script>
</body>
</html>
