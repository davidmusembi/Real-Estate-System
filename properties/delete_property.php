<?php require_once('../includes/auth.php'); require_once('../includes/db.php');
$id=intval($_GET['id']);
$row=$conn->query("SELECT * FROM properties WHERE id=$id")->fetch_assoc();
if(!$row) die('Not found');
if($_SESSION['role']=='agent' && $row['agent_id']!=$_SESSION['user_id']) die('Forbidden');
$conn->query("DELETE FROM properties WHERE id=$id");
header("Location: my_properties.php");
