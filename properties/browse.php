<?php 
require_once('../includes/auth.php');
require_once('../includes/db.php');

// Filtering logic
$where = "WHERE status='available'";
$params = [];
if (!empty($_GET['location'])) {
    $where .= " AND location LIKE ?";
    $params[] = "%".$_GET['location']."%";
}
if (!empty($_GET['price'])) {
    $where .= " AND price <= ?";
    $params[] = $_GET['price'];
}
if (!empty($_GET['type'])) {
    $where .= " AND type = ?";
    $params[] = $_GET['type'];
}

$sql = "SELECT * FROM properties $where ORDER BY id DESC";
$stmt = $conn->prepare($sql);
if ($params) {
    $types = '';
    foreach($params as $p) { $types .= is_numeric($p) ? 'd' : 's'; }
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$properties = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Browse Properties</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Bootstrap CSS (CDN, always up to date) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .property-card { transition: box-shadow 0.2s; border-radius: 1rem; }
        .property-card:hover { box-shadow: 0 0 1rem #ced4da; }
        .property-img { height:180px; object-fit:cover; border-top-left-radius:1rem; border-top-right-radius:1rem; }
        .card-footer { background: none; border-top: none; }
        body { background: #f8f9fa; }
        @media (max-width: 767px) { .property-img { height:120px; } }
    </style>
</head>
<body>
<?php include('../includes/sidebar.php'); ?>
<div class="container mt-4">
    <h2 class="mb-4">Browse Properties</h2>
    <form class="row g-2 mb-4" method="get" autocomplete="off">
        <div class="col-md-4">
            <input name="location" class="form-control" placeholder="Location" value="<?=@htmlspecialchars($_GET['location'])?>">
        </div>
        <div class="col-md-3">
            <input name="price" class="form-control" placeholder="Max Price" value="<?=@htmlspecialchars($_GET['price'])?>">
        </div>
        <div class="col-md-3">
            <select name="type" class="form-select">
                <option value="">Property Type</option>
                <option value="apartment" <?=@$_GET['type']=='apartment'?'selected':''?>>Apartment</option>
                <option value="house" <?=@$_GET['type']=='house'?'selected':''?>>House</option>
                <option value="land" <?=@$_GET['type']=='land'?'selected':''?>>Land</option>
                <option value="commercial" <?=@$_GET['type']=='commercial'?'selected':''?>>Commercial</option>
            </select>
        </div>
        <div class="col-md-2">
            <button class="btn btn-outline-primary w-100">Filter</button>
        </div>
    </form>
    <div class="row">
        <?php if ($properties->num_rows == 0): ?>
            <div class="col-12"><div class="alert alert-info">No properties found matching your criteria.</div></div>
        <?php endif; ?>
        <?php while($row = $properties->fetch_assoc()): ?>
        <div class="col-md-4 mb-4">
            <div class="card property-card h-100 shadow-sm">
                <img src="../assets/images/<?=htmlspecialchars($row['image'])?>" class="card-img-top property-img" alt="Property">
                <div class="card-body">
                    <h5 class="card-title"><?=htmlspecialchars($row['title'])?></h5>
                    <p class="card-text"><?=htmlspecialchars(mb_strimwidth($row['description'],0,70,'...'))?></p>
                    <span class="badge bg-info"><?=$row['type']?></span>
                    <p class="mb-0 mt-2">Price: <strong>$<?=number_format($row['price'])?></strong></p>
                    <p class="mb-0 text-secondary small"><?=htmlspecialchars($row['location'])?></p>
                </div>
                <div class="card-footer d-flex gap-1">
                    <a href="view_property.php?id=<?=$row['id']?>" class="btn btn-sm btn-outline-primary">View Details</a>
                    <?php if($_SESSION['role']=='client'): ?>
                        <a href="../inquiries/send_inquiry.php?id=<?=$row['id']?>" class="btn btn-sm btn-outline-success">Inquire</a>
                        <?php if($row['status']=='available'): ?>
                            <a href="../sales/buy_property.php?id=<?=$row['id']?>" class="btn btn-sm btn-success">Buy</a>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <?php endwhile; ?>
    </div>
</div>
<!-- Bootstrap JS (for responsive nav, not required for static content) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
