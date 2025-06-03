<?php require_once('../includes/auth.php'); require_once('../includes/db.php');
$id=intval($_GET['id']);
$sql="SELECT * FROM properties WHERE id=$id";
$res=$conn->query($sql)->fetch_assoc();
if(!$res) die('Not found.');
if ($_SESSION['role']=='agent' && $res['agent_id']!=$_SESSION['user_id']) die('Forbidden');
if ($_SERVER['REQUEST_METHOD']=='POST') {
    $title=$_POST['title'];$desc=$_POST['description'];$price=$_POST['price'];
    $loc=$_POST['location'];$type=$_POST['type'];
    $img=$res['image'];
    if(isset($_FILES['image']) && $_FILES['image']['tmp_name']) {
        $img=time().'_'.$_FILES['image']['name'];
        move_uploaded_file($_FILES['image']['tmp_name'],"../assets/images/".$img);
    }
    $sql="UPDATE properties SET title=?,description=?,price=?,location=?,type=?,image=? WHERE id=?";
    $stmt=$conn->prepare($sql);$stmt->bind_param("ssdsssi",$title,$desc,$price,$loc,$type,$img,$id);$stmt->execute();
    header("Location: my_properties.php");
}
?>
<!DOCTYPE html>
<html><head><title>Edit Property</title><link rel="stylesheet" href="../assets/css/bootstrap.min.css"></head><body>
        <?php include('../includes/sidebar.php'); ?>

<div class="container mt-5"><h3>Edit Property</h3>
<form method="post" enctype="multipart/form-data">
    <div class="mb-3"><input name="title" class="form-control" value="<?=$res['title']?>" required></div>
    <div class="mb-3"><textarea name="description" class="form-control" required><?=$res['description']?></textarea></div>
    <div class="mb-3"><input name="price" class="form-control" value="<?=$res['price']?>" required></div>
    <div class="mb-3"><input name="location" class="form-control" value="<?=$res['location']?>" required></div>
    <div class="mb-3">
        <select name="type" class="form-select" required>
            <option <?=$res['type']=='apartment'?'selected':''?>>apartment</option>
            <option <?=$res['type']=='house'?'selected':''?>>house</option>
            <option <?=$res['type']=='land'?'selected':''?>>land</option>
            <option <?=$res['type']=='commercial'?'selected':''?>>commercial</option>
        </select>
    </div>
    <div class="mb-3">
        <input type="file" name="image" class="form-control">
        <?php if($res['image']): ?><img src="../assets/images/<?=$res['image']?>" width="100"><?php endif; ?>
    </div>
    <button class="btn btn-success">Save Changes</button>
</form></div></body></html>
