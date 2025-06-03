<?php
require_once('../includes/auth.php');
require_once('../includes/db.php');
$r = $_SESSION['role']; $uid = $_SESSION['user_id'];
$where = ($r=='client') ? "WHERE s.client_id=$uid" : (($r=='agent') ? "WHERE s.agent_id=$uid" : "");
$sql="SELECT s.*, p.title, p.type, p.location FROM sales s JOIN properties p ON s.property_id=p.id $where ORDER BY s.id DESC";
$res=$conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Sales | Urban Realty</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="../assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        body { background: #f8f9fa; }
        .sales-card { border-radius: 1.2rem; box-shadow: 0 2px 18px rgba(30,46,80,.08); }
        .table-sales th, .table-sales td { font-size:1.08rem; }
        .table-sales td { vertical-align: middle;}
        .badge-sale { background: linear-gradient(90deg,#43cea2,#185a9d); font-size:.97rem;}
        .table-sales tr:hover { background: #f0f5fa;}
    </style>
</head>
<body>
    <?php include('../includes/sidebar.php'); ?>
    <div class="container" style="max-width:1000px;">
        <div class="d-flex align-items-center mt-5 mb-4">
            <i class="bi bi-bag-check fs-2 me-2 text-success"></i>
            <h3 class="fw-bold mb-0" style="color:#185a9d;letter-spacing:.5px;">Sales History</h3>
        </div>
        <div class="card sales-card p-4 mb-5">
            <div class="table-responsive">
                <table class="table table-hover align-middle table-sales">
                    <thead class="table-light">
                        <tr>
                            <th><i class="bi bi-building me-1"></i> Property</th>
                            <th><i class="bi bi-geo-alt me-1"></i> Location</th>
                            <th><i class="bi bi-house-door me-1"></i> Type</th>
                            <th><i class="bi bi-cash-stack me-1"></i> Sale Price</th>
                            <th><i class="bi bi-calendar-event me-1"></i> Date</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php while($row=$res->fetch_assoc()): ?>
                        <tr>
                            <td>
                                <span class="fw-bold"><?=htmlspecialchars($row['title'])?></span>
                            </td>
                            <td>
                                <span class="badge bg-info text-dark"><?=$row['location']?></span>
                            </td>
                            <td>
                                <span class="badge badge-sale text-white"><?=ucfirst($row['type'] ?? 'N/A')?></span>
                            </td>
                            <td>
                                <span class="text-success fw-bold">$<?=number_format($row['sale_price'])?></span>
                            </td>
                            <td>
                                <?=date('d M Y', strtotime($row['sale_date']))?>
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
