<?php
/*** MàJ Ajax de l'ordre des cycles ***/
// 
//Cindy Chassot 15.01.2015 - 21.01.2015
//© Cinémathèque suisse

include('re_connexion.php');
 
/* VALUES */
$donnees = $_POST['pos'];

echo '$donnees = '.$donnees.' - $id_form = '.$id_form.' <br />';
foreach($donnees as $ordre => $element) {
  $ordre+=1;
  
  $text = array('pos_');
  $result = array('');
  $id_form = str_replace($text, $result, $element);
	
  $sql = "UPDATE re_form SET ordre='$ordre' WHERE id_form ='$id_form'";
  $result = mysqli_query($connexion, $sql) or die(mysqli_error());
} 
?>