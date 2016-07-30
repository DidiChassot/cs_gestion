<?php
/*** MàJ Ajax de l'ordre des cycles ***/
// 
//Cindy Chassot 15.01.2015 - 21.01.2015
//© Cinémathèque suisse

include('fm_connexion.php');
 
/* VALUES */
$id_form = $_POST['id_form'];
$donnees = $_POST['pos'];

echo '$donnees = '.$donnees.' - $id_champ = '.$id_champ.' - $id_form = '.$id_form.'<br />';
foreach($donnees as $ordre => $element) {
  $ordre+=1;
  
  $text = array('pos_');
  $result = array('');
  $id_champ = str_replace($text, $result, $element);
	
  $sql = "UPDATE fm_champ SET ordre='$ordre' WHERE id_form='$id_form' AND id_champ = '$id_champ'";
  $result = mysqli_query($connexion, $sql) or die(mysqli_error());
} 
?>