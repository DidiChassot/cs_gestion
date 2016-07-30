<?php
include('../../../inc/ge_connexion.php');
 
/* VALUES */
$id_seance=$_POST['id_seance'];
$start=$_POST['start'];
$end=$_POST['end'];
 
$sql = "UPDATE pm_seance SET start='$start', end='$end' WHERE id_seance='$id_seance'";
$result = mysqli_query($connexion, $sql) or die(mysqli_error());
?>