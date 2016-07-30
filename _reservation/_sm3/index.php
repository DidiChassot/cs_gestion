<?php
/**
* INDEX RESERVATION / SM3 - Inscription newsletter
*
* Liste des inscrits à la newsletter
*
* @copyright  Cinémathèque suisse (c) 2015 CHASSOT Cindy
* @author Cindy Chassot
* @version    0.1 - 05.08.2015
*/


/************************************* APPLICATION *************************************/
?>
<h2>Gestion des événements</h2>

<div class="left demi">
  <h3>Événements</h3>
	
  <table class="table">
    <thead>
      <tr>
	<th>ID</th>
	<th>Événements</th>
<?php if($_COOKIE['role']=='e') echo '<th style="width:45px;">&nbsp; </th>'; ?>
      </tr>
    </thead>
    <tbody>
<?php
/****************************************************************************************************************************************/
/********************************************************* AFFICHAGE DES EVENTS *********************************************************/
/****************************************************************************************************************************************/

    $sqlEventAtt = "SELECT re_event.id_event, re_event.titre FROM re_event
			WHERE date < CURDATE()
			AND concours IS NULL
			AND export IS NULL
			ORDER BY date DESC ";
    $resultEventAtt = mysqli_query($connexion, $sqlEventAtt ) or die(mysqli_error());
	   
    while ($dataEventAtt = mysqli_fetch_array($resultEventAtt)) {
      // définir si le film a été sélectionné	
      if ($event == $dataEventAtt['id_event'] ) {
	echo '<tr class="select">'; // pour l'altérnance des couleurs
      } else {
	echo '<tr>'; // pour l'altérnance des couleurs
      }
      //id de la catégorie
      echo '<td>'.$dataEventAtt['id_event'].'</td>';
      
      //titre de la catégorie
      echo '<td>'.utf8_encode($dataEventAtt['titre']).'</td>'; //renvoi l'id de la catégorie en "GET"
      
      //Formulaire d'édition / suppression
      if($_COOKIE['role']=='e') {	
	echo '<td>';
	  echo '<form name="exportNews" action="_reservation/inc/export_news.php" method="post">
	      <input type="hidden" name="id_event" value="'.$dataEventAtt['id_event'].'">
	      <input name="exportNews" class="btn_export" type="submit" value="exportNews">
	  </form>';
	  echo '<form name="exportNews" action="'.$_SERVER['PHP_SELF'].'" method="post">
	      <input type="hidden" name="id_event" value="'.$dataEventAtt['id_event'].'">
	      <input name="exportNews" class="btn_suppr" type="submit" value="exportNews">
	  </form>';
	echo '</td>';
      }
			    
      echo '</tr>'; //fermeture de la ligne
    }
?>
    </tbody>
  </table>
</div>
<hr class="clear">