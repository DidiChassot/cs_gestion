<?php
/*** MàJ Ajax de l'ordre des cycles ***/
// 
//Cindy Chassot 15.01.2015 - 21.01.2015
//© Cinémathèque suisse

include('pm_connexion.php');
 
/* VALUES */
$id_cycle = $_POST['id_cycle'];
$donnees = $_POST['pos'];

echo '$donnees = '.$donnees.' - $id_film = '.$id_film.' - $id_cycle = '.$id_cycle.'<br />';
foreach($donnees as $ordre => $element) {
  $ordre+=1;
  
  $text = array('pos_');
  $result = array('');
  $id_film = str_replace($text, $result, $element);
	
  $sql = "UPDATE pm_cycle_film SET ordre='$ordre' WHERE id_cycle='$id_cycle' AND id_film = '$id_film'";
  $result = mysqli_query($connexion, $sql) or die(mysqli_error());
} 
?>