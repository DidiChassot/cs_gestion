<?php
/**
* MàJ Ajax de l'ordre du journal
*
* Mise à jour de l'ordre du journal via drag&drop
*
* @copyright  Cinémathèque suisse (c) 2015 CHASSOT Cindy
* @author Cindy Chassot
* @version    0.1 - 15.07.2015
*/

include('pm_connexion.php');
 
/* VALUES */
$donnees = $_POST['pos'];

foreach($donnees as $ordre => $element) {
  $ordre+=1;
  
  $text = array('pos_');
  $result = array('');
  $id_journal = str_replace($text, $result, $element);
	
  $sql = "UPDATE pm_pub SET ordre='$ordre' WHERE id_pub = '$id_pub'";
  $result = mysqli_query($connexion, $sql) or die(mysqli_error());
} 
?>