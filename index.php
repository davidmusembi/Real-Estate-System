<?php
require_once('includes/db.php');
session_start();

// Property filter
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
$sql = "SELECT * FROM properties $where ORDER BY id DESC LIMIT 12";
$stmt = $conn->prepare($sql);
if ($params) {
    $types = str_repeat('s', count($params));
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$properties = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Urban Realty | Property Listing</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <style>
        body { background: #f8f9fa; }
        .navbar { margin-bottom: 2rem; }
        .property-img {height:180px;object-fit:cover;}
        .modal-img {width:100%;height:230px;object-fit:cover;}
        .card { border-radius:1rem;}
        .card-footer { background: none; border-top: none;}
    </style>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm sticky-top">
    <div class="container-fluid">
        <a class="navbar-brand fw-bold" href="#" style="font-size:1.6rem;">
            Urban Realty
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
            aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse flex-row-reverse" id="navbarNav">
            <ul class="navbar-nav">
                <li class="nav-item ms-2">
                    <a class="btn btn-outline-warning px-4" href="index.html">
                        <i class="bi bi-arrow-left"></i> Back to Website
                    </a>
                </li>
                <li class="nav-item ms-2">
                    <a class="btn btn-outline-light px-4" href="auth/login.php">Login</a>
                </li>
                <li class="nav-item ms-2">
                    <a class="btn btn-light px-4" href="auth/register.php">Register</a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<div class="container">
    <h2 class="mb-4">Find Your Property</h2>
    <form class="row g-2 mb-3" method="get">
        <div class="col-md-4">
            <select name="location" class="form-control">
            <option value="">-- Select County --</option>
            <?php
            $counties = [
                "Baringo","Bomet","Bungoma","Busia","Elgeyo Marakwet","Embu","Garissa","Homa Bay","Isiolo","Kajiado",
                "Kakamega","Kericho","Kiambu","Kilifi","Kirinyaga","Kisii","Kisumu","Kitui","Kwale","Laikipia",
                "Lamu","Machakos","Makueni","Mandera","Marsabit","Meru","Migori","Mombasa","Murang'a","Nairobi",
                "Nakuru","Nandi","Narok","Nyamira","Nyandarua","Nyeri","Samburu","Siaya","Taita Taveta","Tana River",
                "Tharaka Nithi","Trans Nzoia","Turkana","Uasin Gishu","Vihiga","Wajir","West Pokot"
            ];
            $selected = isset($_GET['location']) ? $_GET['location'] : '';
            foreach ($counties as $county) {
                $isSelected = ($selected === $county) ? 'selected' : '';
                echo "<option value=\"".htmlspecialchars($county)."\" $isSelected>".htmlspecialchars($county)."</option>";
            }
            ?>
            </select>
        </div>
        <div class="col-md-4"><input name="price" class="form-control" placeholder="Max Price" value="<?=@$_GET['price']?>"></div>
        <div class="col-md-2">
            <button class="btn btn-outline-primary w-100">Filter</button>
        </div>
    </form>
    <div class="row">
    <?php
    $all_properties = [];
    while($row = $properties->fetch_assoc()):
        $all_properties[] = $row; // For possible JS extension
    ?>
        <div class="col-md-4 mb-4">
            <div class="card h-100 shadow-sm">
                <img src="assets/images/<?=$row['image']?>" class="card-img-top property-img" alt="Property">
                <div class="card-body">
                    <h5 class="card-title"><?=htmlspecialchars($row['title'])?></h5>
                    <p class="card-text"><?=htmlspecialchars(substr($row['description'],0,70))?>...</p>
                    <span class="badge bg-info"><?=$row['type']?></span>
                    <p class="mb-0">Price: <strong>$<?=number_format($row['price'])?></strong></p>
                </div>
                <div class="card-footer">
                    <button 
                        class="btn btn-sm btn-outline-primary view-details-btn"
                        data-id="<?=$row['id']?>"
                        data-title="<?=htmlspecialchars($row['title'])?>"
                        data-desc="<?=htmlspecialchars($row['description'])?>"
                        data-type="<?=htmlspecialchars($row['type'])?>"
                        data-location="<?=htmlspecialchars($row['location'])?>"
                        data-image="assets/images/<?=htmlspecialchars($row['image'])?>"
                        data-price="<?=number_format($row['price'])?>"
                    >
                        View Details
                    </button>
                </div>
            </div>
        </div>
    <?php endwhile; ?>
    </div>
</div>

<!-- MODAL for property details -->
<div class="modal fade" id="propertyModal" tabindex="-1" aria-labelledby="propertyModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="propertyModalLabel"></h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <img id="modal-image" class="modal-img mb-3" src="" alt="Property">
        <p><span class="badge bg-info" id="modal-type"></span> <span class="ms-2" id="modal-location"></span></p>
        <p id="modal-desc"></p>
        <h4 class="mt-3">Price: <span id="modal-price"></span></h4>
      </div>
      <div class="modal-footer">
        <button id="modal-buy-btn" class="btn btn-success">Buy Now</button>
        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

<script src="assets/js/bootstrap.bundle.min.js"></script>
<script>
    const isClient = false; // always false, since navbar is for guests only
    const loggedIn = false;
    let selectedPropertyId = null;

    document.querySelectorAll('.view-details-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            selectedPropertyId = this.getAttribute('data-id');
            document.getElementById('propertyModalLabel').innerText = this.getAttribute('data-title');
            document.getElementById('modal-desc').innerText = this.getAttribute('data-desc');
            document.getElementById('modal-type').innerText = this.getAttribute('data-type');
            document.getElementById('modal-location').innerText = this.getAttribute('data-location');
            document.getElementById('modal-image').src = this.getAttribute('data-image');
            document.getElementById('modal-price').innerText = "$" + this.getAttribute('data-price');
            let modal = new bootstrap.Modal(document.getElementById('propertyModal'));
            modal.show();
        });
    });

    document.getElementById('modal-buy-btn').onclick = function() {
        // Always redirect to login (as only guests see this navbar)
        window.location.href = "auth/login.php?next=buy";
    }
</script>
</body>
</html>
