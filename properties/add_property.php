<?php require_once('../includes/auth.php');
if ($_SESSION['role'] != 'agent') die('Forbidden');
require_once('../includes/db.php');
if ($_SERVER['REQUEST_METHOD']=='POST') {
    $title = $_POST['title']; $desc = $_POST['description'];
    $price = $_POST['price']; $loc = $_POST['location'];
    $type = $_POST['type']; $img = '';
    if (isset($_FILES['image']) && $_FILES['image']['tmp_name']) {
        $img = time().'_'.$_FILES['image']['name'];
        move_uploaded_file($_FILES['image']['tmp_name'], "../assets/images/".$img);
    }
    $sql = "INSERT INTO properties (agent_id,title,description,price,location,type,image) VALUES (?,?,?,?,?,?,?)";
    $stmt = $conn->prepare($sql); $stmt->bind_param("issdsss",$_SESSION['user_id'],$title,$desc,$price,$loc,$type,$img);
    $stmt->execute();
    header("Location: my_properties.php");
}
?>
<!DOCTYPE html>
<html><head>
    <title>Add Property</title>
    <link rel="stylesheet" href="../assets/css/bootstrap.min.css">
</head><body>
<?php include('../includes/sidebar.php'); ?>
<div class="container mt-5">
    <h3>Add New Property</h3>
    <form method="post" enctype="multipart/form-data">
        <div class="mb-3"><input name="title" class="form-control" placeholder="Title" required></div>
        <div class="mb-3"><textarea name="description" class="form-control" placeholder="Description" required></textarea></div>
        <div class="mb-3"><input name="price" class="form-control" placeholder="Price" type="number" step="0.01" required></div>
        <div class="mb-3">
            <select name="location" class="form-select" required>
            <option value="">Select County</option>
            <option value="Baringo">Baringo</option>
            <option value="Bomet">Bomet</option>
            <option value="Bungoma">Bungoma</option>
            <option value="Busia">Busia</option>
            <option value="Elgeyo Marakwet">Elgeyo Marakwet</option>
            <option value="Embu">Embu</option>
            <option value="Garissa">Garissa</option>
            <option value="Homa Bay">Homa Bay</option>
            <option value="Isiolo">Isiolo</option>
            <option value="Kajiado">Kajiado</option>
            <option value="Kakamega">Kakamega</option>
            <option value="Kericho">Kericho</option>
            <option value="Kiambu">Kiambu</option>
            <option value="Kilifi">Kilifi</option>
            <option value="Kirinyaga">Kirinyaga</option>
            <option value="Kisii">Kisii</option>
            <option value="Kisumu">Kisumu</option>
            <option value="Kitui">Kitui</option>
            <option value="Kwale">Kwale</option>
            <option value="Laikipia">Laikipia</option>
            <option value="Lamu">Lamu</option>
            <option value="Machakos">Machakos</option>
            <option value="Makueni">Makueni</option>
            <option value="Mandera">Mandera</option>
            <option value="Marsabit">Marsabit</option>
            <option value="Meru">Meru</option>
            <option value="Migori">Migori</option>
            <option value="Mombasa">Mombasa</option>
            <option value="Murang'a">Murang'a</option>
            <option value="Nairobi">Nairobi</option>
            <option value="Nakuru">Nakuru</option>
            <option value="Nandi">Nandi</option>
            <option value="Narok">Narok</option>
            <option value="Nyamira">Nyamira</option>
            <option value="Nyandarua">Nyandarua</option>
            <option value="Nyeri">Nyeri</option>
            <option value="Samburu">Samburu</option>
            <option value="Siaya">Siaya</option>
            <option value="Taita Taveta">Taita Taveta</option>
            <option value="Tana River">Tana River</option>
            <option value="Tharaka Nithi">Tharaka Nithi</option>
            <option value="Trans Nzoia">Trans Nzoia</option>
            <option value="Turkana">Turkana</option>
            <option value="Uasin Gishu">Uasin Gishu</option>
            <option value="Vihiga">Vihiga</option>
            <option value="Wajir">Wajir</option>
            <option value="West Pokot">West Pokot</option>
            </select>
        </div>
        <div class="mb-3">
            <select name="type" class="form-select" required>
                <option value="apartment">Apartment</option>
                <option value="house">House</option>
                <option value="land">Land</option>
                <option value="commercial">Commercial</option>
            </select>
        </div>
        <div class="mb-3"><input type="file" name="image" class="form-control"></div>
        <button class="btn btn-success">Add Property</button>
    </form>
</div></body></html>
