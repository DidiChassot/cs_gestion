<?php
/*** MàJ Ajax de l'ordre des cycles ***/
// 
//Cindy Chassot 15.01.2015 - 21.01.2015
//© Cinémathèque suisse

include('../../../inc/ge_connexion.php');
 
/* VALUES */
$id_seance = $_POST['id_seance'];
$donnees = $_POST['pos'];

//echo '$donnees = '.$donnees.' - $id_cycle = '.$id_cycle.' - $id_bulletin = '.$id_bulletin.'<br />';
foreach($donnees as $ordre => $element) {
  $ordre+=1;
  
  $text = array('pos_');
  $result = array('');
  $id_film = str_replace($text, $result, $element);
  //
  $sql = "UPDATE pm_film_seance SET ordre='$ordre' WHERE id_film='$id_film' AND id_seance = '$id_seance'";
  $result = mysqli_query($connexion, $sql) or die(mysqli_error());
} 
?>