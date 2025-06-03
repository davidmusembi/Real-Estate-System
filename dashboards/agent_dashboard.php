<?php require_once('../includes/auth.php'); require_once('../includes/db.php'); 
$aid = $_SESSION['user_id'];
// Fetch username for welcome
$user_row = $conn->query("SELECT username FROM users WHERE id=$aid")->fetch_assoc();
$username = $user_row ? $user_row['username'] : 'Agent';

// Metrics
$my_listings = $conn->query("SELECT COUNT(*) FROM properties WHERE agent_id=$aid")->fetch_row()[0];
$total_inquiries = $conn->query("SELECT COUNT(*) FROM inquiries i JOIN properties p ON i.property_id=p.id WHERE p.agent_id=$aid")->fetch_row()[0];
$props_sold = $conn->query("SELECT COUNT(*) FROM properties WHERE agent_id=$aid AND status='sold'")->fetch_row()[0];
$total_sales = $conn->query("SELECT IFNULL(SUM(s.sale_price),0)
    FROM sales s
    JOIN properties p ON s.property_id = p.id
    WHERE p.agent_id=$aid")->fetch_row()[0];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Agent Dashboard | Urban Realty</title>
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
                <div class="h4 mb-2 text-primary"><?=$my_listings?></div>
                <div>My Listings</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card dashboard-card shadow text-center p-3">
                <div class="h4 mb-2 text-success"><?=$total_inquiries?></div>
                <div>Total Inquiries</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card dashboard-card shadow text-center p-3">
                <div class="h4 mb-2 text-info"><?=$props_sold?></div>
                <div>Properties Sold</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card dashboard-card shadow text-center p-3">
                <div class="h4 mb-2 text-warning">$<?=number_format($total_sales)?></div>
                <div>Total Sales Value</div>
            </div>
        </div>
    </div>
    <!-- Quick Action Links -->
    <div class="row g-4 dashboard-section">
        <div class="col-md-3">
            <a href="../properties/add_property.php" class="card quick-link-card text-decoration-none shadow text-center p-4 bg-primary text-white">
                <div class="fs-2 mb-1"><i class="bi bi-plus-square"></i></div>
                Add Listing
            </a>
        </div>
        <div class="col-md-3">
            <a href="../properties/my_properties.php" class="card quick-link-card text-decoration-none shadow text-center p-4 bg-success text-white">
                <div class="fs-2 mb-1"><i class="bi bi-houses"></i></div>
                My Properties
            </a>
        </div>
        <div class="col-md-3">
            <a href="../inquiries/view_inquiries.php" class="card quick-link-card text-decoration-none shadow text-center p-4 bg-warning text-dark">
                <div class="fs-2 mb-1"><i class="bi bi-chat-dots"></i></div>
                Inquiries
            </a>
        </div>
        <div class="col-md-3">
            <a href="../users/profile.php" class="card quick-link-card text-decoration-none shadow text-center p-4 bg-info text-white">
                <div class="fs-2 mb-1"><i class="bi bi-person-circle"></i></div>
                My Profile
            </a>
        </div>
    </div>

    <!-- Recent Properties -->
    <div class="dashboard-section">
        <h4 class="mb-3">My Recent Properties</h4>
        <div class="table-responsive">
        <table class="table table-bordered bg-white">
            <thead class="table-light">
                <tr>
                    <th>Title</th>
                    <th>Type</th>
                    <th>Price</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
            <?php
            $sql = "SELECT * FROM properties WHERE agent_id=$aid ORDER BY id DESC LIMIT 5";
            $res = $conn->query($sql);
            if ($res->num_rows == 0): ?>
                <tr><td colspan="5" class="text-center text-muted">No listings yet.</td></tr>
            <?php endif;
            while($row = $res->fetch_assoc()): ?>
            <tr>
                <td><?=htmlspecialchars($row['title'])?></td>
                <td><?=htmlspecialchars($row['type'])?></td>
                <td>$<?=number_format($row['price'])?></td>
                <td>
                    <span class="badge <?=$row['status']=='available'?'bg-success':'bg-secondary'?>">
                        <?=ucfirst($row['status'])?>
                    </span>
                </td>
                <td>
                    <a href="../properties/edit_property.php?id=<?=$row['id']?>" class="btn btn-sm btn-warning">Edit</a>
                    <a href="../properties/delete_property.php?id=<?=$row['id']?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete?')">Delete</a>
                </td>
            </tr>
            <?php endwhile; ?>
            </tbody>
        </table>
        </div>
        <div><a href="../properties/my_properties.php" class="btn btn-outline-primary btn-sm">View All Properties</a></div>
    </div>

    <!-- Recent Inquiries -->
    <div class="dashboard-section">
        <h4 class="mb-3">Recent Inquiries</h4>
        <div class="table-responsive">
        <table class="table table-bordered bg-white">
            <thead class="table-light">
                <tr>
                    <th>Client</th>
                    <th>Property</th>
                    <th>Message</th>
                    <th>Status</th>
                    <th>Reply</th>
                </tr>
            </thead>
            <tbody>
            <?php
            $sql = "SELECT i.*, p.title as property_title, u.username as client_name
                    FROM inquiries i
                    JOIN properties p ON i.property_id = p.id
                    JOIN users u ON i.client_id = u.id
                    WHERE p.agent_id = $aid
                    ORDER BY i.id DESC LIMIT 5";
            $res = $conn->query($sql);
            if ($res->num_rows == 0): ?>
                <tr><td colspan="5" class="text-center text-muted">No inquiries yet.</td></tr>
            <?php endif;
            while($row = $res->fetch_assoc()): ?>
            <tr>
                <td><?=htmlspecialchars($row['client_name'])?></td>
                <td><?=htmlspecialchars($row['property_title'])?></td>
                <td><?=htmlspecialchars($row['message'])?></td>
                <td>
                    <span class="badge <?=$row['status']=='replied'?'bg-success':'bg-warning text-dark'?>">
                        <?=ucfirst($row['status'])?>
                    </span>
                </td>
                <td>
                    <?php if($row['status']=='replied') echo htmlspecialchars($row['reply']); else echo '<span class="text-muted">-</span>'; ?>
                </td>
            </tr>
            <?php endwhile; ?>
            </tbody>
        </table>
        </div>
        <div><a href="../inquiries/view_inquiries.php" class="btn btn-outline-success btn-sm">View All Inquiries</a></div>
    </div>
</div>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<script src="../assets/js/bootstrap.bundle.min.js"></script>
</body>
</html>
