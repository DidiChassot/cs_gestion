<?php
include('../../../inc/ge_connexion.php');
 
$start=$_POST['start'];
$end=$_POST['end'];
 
$sql = "INSERT INTO pm_seance (start, end) VALUES ('$start', '$end')";
$result = mysqli_query($connexion, $sql) or die(mysqlio_error());
?>
