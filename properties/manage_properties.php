<?php
require_once('../includes/auth.php');
require_once('../includes/db.php');
if ($_SESSION['role'] != 'admin') die('Forbidden');

// Handle delete
if (isset($_GET['delete'])) {
    $did = intval($_GET['delete']);
    $conn->query("DELETE FROM properties WHERE id=$did");
    header("Location: manage_properties.php");
    exit;
}

// Filters
$where = "WHERE 1=1";
if (!empty($_GET['location'])) $where .= " AND location LIKE '%".$conn->real_escape_string($_GET['location'])."%'";
if (!empty($_GET['type'])) $where .= " AND type='".$conn->real_escape_string($_GET['type'])."'";
if (!empty($_GET['status'])) $where .= " AND status='".$conn->real_escape_string($_GET['status'])."'";
if (!empty($_GET['agent'])) $where .= " AND agent_id=".intval($_GET['agent']);

$sql = "SELECT p.*, u.username agent FROM properties p JOIN users u ON p.agent_id=u.id $where ORDER BY p.id DESC";
$res = $conn->query($sql);

// Get agents for filter dropdown
$agents = $conn->query("SELECT id, username FROM users WHERE role='agent'");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Manage Properties | Urban Realty</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="../assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        body { background: #f8f9fa;}
        .manage-props-card { border-radius:1.2rem; box-shadow:0 2px 16px rgba(30,46,80,.09);}
        .table-props th, .table-props td { font-size:1.05rem;}
        .table-props tr:hover { background: #f2f7fa; }
        .badge-type { background: linear-gradient(90deg,#43cea2,#185a9d); color:#fff; font-size:.97rem; }
        .badge-status { font-size:.98rem;}
        .table-responsive { border-radius:1.1rem;}
        .btn-action { margin-right:0.4rem; }
        .filter-label { font-weight:600; font-size:1.05rem; color:#185a9d;}
    </style>
</head>
<body>
<?php include('../includes/sidebar.php'); ?>
<div class="container" style="max-width:1100px;">
    <div class="d-flex align-items-center mt-5 mb-3">
        <i class="bi bi-house-gear fs-2 me-2 text-primary"></i>
        <h3 class="fw-bold mb-0" style="color:#185a9d;">Manage Properties</h3>
    </div>
    <div class="card manage-props-card p-4 mb-4">
        <form class="row g-2 mb-3" method="get" autocomplete="off">
            <div class="col-md-3">
                <input name="location" class="form-control" placeholder="Location" value="<?=@$_GET['location']?>">
            </div>
            <div class="col-md-2">
                <select name="type" class="form-select">
                    <option value="">Type</option>
                    <option value="apartment" <?=@$_GET['type']=='apartment'?'selected':''?>>Apartment</option>
                    <option value="house" <?=@$_GET['type']=='house'?'selected':''?>>House</option>
                    <option value="land" <?=@$_GET['type']=='land'?'selected':''?>>Land</option>
                    <option value="commercial" <?=@$_GET['type']=='commercial'?'selected':''?>>Commercial</option>
                </select>
            </div>
            <div class="col-md-2">
                <select name="status" class="form-select">
                    <option value="">Status</option>
                    <option value="available" <?=@$_GET['status']=='available'?'selected':''?>>Available</option>
                    <option value="sold" <?=@$_GET['status']=='sold'?'selected':''?>>Sold</option>
                </select>
            </div>
            <div class="col-md-3">
                <select name="agent" class="form-select">
                    <option value="">Agent</option>
                    <?php $agents->data_seek(0); while($a = $agents->fetch_assoc()): ?>
                        <option value="<?=$a['id']?>" <?=@$_GET['agent']==$a['id']?'selected':''?>><?=$a['username']?></option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="col-md-2">
                <button class="btn btn-outline-primary w-100"><i class="bi bi-funnel-fill me-1"></i>Filter</button>
            </div>
        </form>
        <div class="table-responsive">
            <table class="table table-hover align-middle table-props mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Title</th>
                        <th>Type</th>
                        <th>Location</th>
                        <th>Price</th>
                        <th>Status</th>
                        <th>Agent</th>
                        <th class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                <?php while($row = $res->fetch_assoc()): ?>
                    <tr>
                        <td><span class="fw-bold"><?=htmlspecialchars($row['title'])?></span></td>
                        <td>
                            <span class="badge badge-type"><?=ucfirst($row['type'])?></span>
                        </td>
                        <td>
                            <span class="badge bg-info text-dark"><?=$row['location']?></span>
                        </td>
                        <td><span class="text-success fw-bold">$<?=number_format($row['price'])?></span></td>
                        <td>
                            <?php if($row['status']=='available'): ?>
                                <span class="badge bg-success badge-status">Available</span>
                            <?php else: ?>
                                <span class="badge bg-secondary badge-status">Sold</span>
                            <?php endif; ?>
                        </td>
                        <td><span class="text-primary"><?=$row['agent']?></span></td>
                        <td class="text-center">
                            <a href="edit_property.php?id=<?=$row['id']?>" class="btn btn-sm btn-warning btn-action" title="Edit"><i class="bi bi-pencil-square"></i></a>
                            <a href="manage_properties.php?delete=<?=$row['id']?>" class="btn btn-sm btn-danger btn-action" onclick="return confirm('Delete property?')" title="Delete"><i class="bi bi-trash"></i></a>
                            <a href="view_property.php?id=<?=$row['id']?>" class="btn btn-sm btn-info btn-action" title="View"><i class="bi bi-eye"></i></a>
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
