<?php
/*****Ajout des séances à la création d'un bulletin****/

$sql_bulletin = "SELECT * FROM pm_bulletin WHERE id_bulletin = '$id_bulletin'";
$result_bulletin = mysqli_queryi($connexion, $sql_bulletin) or die(mysqli_error());
$data_bulletin = mysqli_fetch_array($result_bulletin);
   $start_bul = strtotime($data_bulletin['start']);
   $end_bul = strtotime($data_bulletin['end']);

   echo $start_bul.'<br />';
   echo $end_bul.'<br />';
    echo date("Y-m-d", $start_bul).'<br />';
   echo '<br />';
   
   
for ($start_day = $start_bul; $start_day <= $end_bul; $start_day = strtotime(date("Y-m-d", $start_day) . " +1 day")) {
    $start_day1 = date("Y-m-d 15:00:00", $start_day);
    $start_day2 = date("Y-m-d 18:30:00", $start_day);
    $start_day3 = date("Y-m-d 21:00:00", $start_day);
    
    $end_day1 = date("Y-m-d 16:00:00", $start_day);
    $end_day2 = date("Y-m-d 19:30:00", $start_day);
    $end_day3 = date("Y-m-d 22:00:00", $start_day);

	$sql1 = "INSERT INTO pm_seance (start, end) VALUES ('$start_day1', '$end_day1')";
	$result1 = mysqli_query($connexion, $sql1) or die(mysqli_error());

	$sql2 = "INSERT INTO pm_seance (start, end) VALUES ('$start_day2', '$end_day2')";
	$result2 = mysqli_query($connexion, $sql2) or die(mysqli_error());

	$sql3 = "INSERT INTO pm_seance (start, end) VALUES ('$start_day3', '$end_day3')";
	$result3 = mysqli_query($connexion, $sql3) or die(mysqli_error());
}

?>

