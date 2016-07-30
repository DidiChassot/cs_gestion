<?php
/**
* INDEX RESERVATION / SM2 - INSCRIPTIONS LACS MULTIPLE
*
* Gestion des inscriptions
*
* @copyright  Cinémathèque suisse (c) 2015 CHASSOT Cindy
* @author Cindy Chassot
* @version    0.1 - 29.04.2015
*/

/************************************* APPLICATION *************************************/
?>
<h2>Inscription LACS</h2>

<form action="<?php $_SERVER['PHP_SELF']; ?>" method="post">
<div class="left">
  <div>
    <label for="prenom">Prénom</label>
    <input type="text" name="prenom" id="prenom" value="<?php echo utf8_encode($prenom); ?>">
  </div>
  <div>
    <label for="nom">Nom</label>
    <input type="text" name="nom" id="nom" value="<?php echo utf8_encode($nom); ?>">
  </div>
  <div>
    <label for="email">Email</label>
    <input type="text" name="email" id="email" value="<?php echo utf8_encode($email); ?>">
  </div>
  <div>
    <label for="place">Nombre d'invitations souhaitées</label>
    <input type="text" name="place" id="place" value="<?php echo utf8_encode($place); ?>">
  </div>
</div>
<div class="right demi">
 <?php
/****************************************************************************************************************************************/
/********************************************************* AFFICHAGE DES EVENTS *********************************************************/
/****************************************************************************************************************************************/

    $sqlEventAtt = "SELECT re_reserv.id_reserv, re_event.titre, date, SUM(place) AS place, re_reserv.quotas FROM re_event
			JOIN re_reserv
			ON re_reserv.id_event = re_event.id_event
			LEFT JOIN re_inscription
			ON re_inscription.id_reserv = re_reserv.id_reserv
			WHERE date >= CURDATE()
			AND re_reserv.id_categorie = 2
			GROUP BY re_event.id_event
			ORDER BY date ";
    $resultEventAtt = mysqli_query($connexion, $sqlEventAtt ) or die(mysqli_error());
	   
    while ($dataEventAtt = mysqli_fetch_array($resultEventAtt)) {
	if($dataEventAtt['place'] >= $dataEventAtt['quotas']) {
	  echo '<span class="block"><s><input type="checkbox" style="visibility:hidden">'.date("d-m-Y H:i",strtotime($dataEventAtt['date'])).' - '.utf8_encode($dataEventAtt['titre']).'</s></span>';
	} else {
	  echo '<span class="block"><input type="checkbox" name="reserv[]" value="'.$dataEventAtt['id_reserv'].'">'.date("d.m.Y H:i",strtotime($dataEventAtt['date'])).' - '.utf8_encode($dataEventAtt['titre']).'</span>';
	}
    }
?>
  <input type="submit" name="addMultiLacs" class="btn" value="Enregistrer">
</div>
</form>
<hr class="clear">
<?php
if($addMultiLacs) {
  echo '<div class="left"><h3>Réservation '.$place.' place(s)</h3>';
  foreach ($reserv as $id_reserv) {
			
		$sqlEventAtt = "SELECT re_reserv.id_reserv, re_event.titre
			FROM re_event
			JOIN re_reserv
			ON re_reserv.id_event = re_event.id_event
			WHERE re_reserv.id_reserv = '$id_reserv' ORDER BY date ";
		$resultEventAtt = mysqli_query($connexion, $sqlEventAtt ) or die(mysqli_error());
		$dataEventAtt = mysqli_fetch_array($resultEventAtt);
		echo '<p class="clear">'.$dataEventAtt['titre'].'</p>';
	}
echo '</div><hr class="clear">';
}
?>