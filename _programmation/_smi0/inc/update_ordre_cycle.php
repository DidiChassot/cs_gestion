<?php
/*** MàJ Ajax de l'ordre des cycles ***/
// 
//Cindy Chassot 15.01.2015 - 21.01.2015
//© Cinémathèque suisse

include('pm_connexion.php');
 
/* VALUES */
$id_bulletin = $_POST['id_bulletin'];
$donnees = $_POST['pos'];

echo '$donnees = '.$donnees.' - $id_cycle = '.$id_cycle.' - $id_bulletin = '.$id_bulletin.'<br />';
foreach($donnees as $ordre => $element) {
  $ordre+=1;
  
  $text = array('pos_');
  $result = array('');
  $id_cycle = str_replace($text, $result, $element);
	
  $sql = "UPDATE pm_bulletin_cycle SET ordre='$ordre' WHERE id_bulletin='$id_bulletin' AND id_cycle = '$id_cycle'";
  $result = mysqli_query($connexion, $sql) or die(mysqli_error());
} 
?>