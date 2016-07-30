<?php
/**
* INDEX RESERVATION / SM0 - Gestion des catégorie
*
* Gestion des événements avec réservation
*
* @copyright  Cinémathèque suisse (c) 2015 CHASSOT Cindy
* @author Cindy Chassot
* @version    0.1 - 29.04.2015 - 03.08.2015
*/


/************************************* APPLICATION *************************************/
?>
<h2>Gestion des événements</h2>

<div class="left">
  <h3>Événements</h3>
<?php if($_COOKIE['role']=='e') { ?>
  <form class="" action="<?php $_SERVER['PHP_SELF']; ?>" method="get">
    <input type="hidden" value="event" name="zone">
    <input type="hidden" value="1" name="add">
    <input type="submit" class="btn" value="Ajouter">
  </form>
<?php } ?>
	
  <table class="table">
    <thead>
      <tr>
	<th>ID</th>
	<th>Événements à venir</th>
	<th>Date</th>
	<th>Liste</th>
<?php if($_COOKIE['role']=='e') echo '<th style="width:45px;">&nbsp; </th>'; ?>
      </tr>
    </thead>
    <tbody>
<?php
/****************************************************************************************************************************************/
/********************************************************* AFFICHAGE DES EVENTS *********************************************************/
/****************************************************************************************************************************************/

    $sqlEventAtt = "SELECT re_event.id_event, re_event.titre, date, COUNT(DISTINCT re_reserv.id_reserv) AS reserv FROM re_event
			LEFT JOIN re_reserv
			ON re_reserv.id_event = re_event.id_event
			WHERE date >= CURDATE()
			AND concours IS NULL
			GROUP BY re_event.id_event
			ORDER BY date ";
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
      echo '<td><a href="'.$_SERVER['PHP_SELF'].'?zone=event&amp;add=0&amp;event='.$dataEventAtt['id_event'].'">'.utf8_encode($dataEventAtt['titre']).'</a></td>'; //renvoi l'id de la catégorie en "GET"
      
      //date de l'évent
      echo '<td>'.date("d-m-Y H:i",strtotime($dataEventAtt['date'])).'</td>';
      
      //n. de liste
      echo '<td>'.$dataEventAtt['reserv'].'</td>';
      
      //Formulaire d'édition / suppression
      if($_COOKIE['role']=='e') {	
	echo '<td>';
	  echo '<form name="suppr_news" action="'.$_SERVER['PHP_SELF'].'" method="post" onclick="return confirm(\'voulez-vous vraiment supprimé cet événement?\')">
	      <input type="hidden" name="id_event" value="'.$dataEventAtt['id_event'].'">
	      <input name="deleteEvent" class="btn_suppr" type="submit" value="Supprimer">
	  </form>';
	echo '</td>';
      }
			    
      echo '</tr>'; //fermeture de la ligne
    }
?>
    </tbody>
  </table>
</div>

<?php
/********************************************************* FORMULAIRE ADD/MODIF *********************************************************/
if($_REQUEST['zone']=='event' || $_REQUEST['zone']=='reserv' ) {
	if($add == 0 || $add == 2 ) {
	  $sqlEventSelect = "SELECT * FROM re_event WHERE id_event ='$event'"; // nom de la table ! requette
	  $resultEventSelect = mysqli_query($connexion, $sqlEventSelect ) or die(mysqli_error());
	  $dataEventSelect = mysqli_fetch_array($resultEventSelect);
		
	  $titre1 = 'Modifier '.utf8_encode($dataEventSelect['titre']);
	
?>
<div class="right">
<!-----------------------------------/Recherche FILM LIER--------------------------------------------------------->
  <?php include($_COOKIE['indexApp']."/inc/rechercher_film.php"); ?>
</div>
<?php 
} else {
	  $titre1 = 'Ajout';
}
?>
<div class="middle">
	<h4><?php echo $titre1; ?></h4>
	<form action="<?php $_SERVER['PHP_SELF']; ?>" method="post">
	    <div>
		<label for="titre">Titre</label>
		<textarea id="titre" name="titre"><?php echo utf8_encode($dataEventSelect['titre']); ?></textarea>
	    </div>
	    <div class="input21">
		<label for="date">Date et heure</label>
		<input type="date" min="2015-01-01" placeholder="2015-01-01 20:30" name="date" value="<?php echo utf8_encode($dataEventSelect['date']); ?>">
	    </div>
	    <div class="input22">
		<label for="id_salle">Salle</label>
		<select name="id_salle">
<?php
//requète pour récupérer la liste des salles
$sql_salle = "SELECT id_salle, titre FROM pm_salle";
$result_salle = mysqli_query($connexion, $sql_salle) or die(mysqli_error());	
while ($data_salle = mysqli_fetch_array($result_salle)) {
  echo '<option value="'.$data_salle['id_salle'].'"';
  if($dataEventSelect['id_salle'] == $data_salle['id_salle']) echo ' selected="selected"';
  echo '>'.utf8_encode($data_salle['titre']).'</option>';	
}
?>
		</select>
	    </div>
	    <div>
		<label for="commentaire">Commentaire</label>
		<textarea id="commentaire" name="commentaire"><?php echo utf8_encode($dataEventSelect['commentaire']); ?></textarea>
	    </div>
	    <input type="hidden" name="id_event" value="<?php echo $event; ?>">
	    <?php if($_COOKIE['role']=='e') { echo '<input type="submit" name="saveEvent" class="btn" value="Enregistrer">'; } ?>
	</form>
</div>

<hr class="clear" id="liste" name="liste">

<h2><?php echo utf8_encode($dataEventSelect['titre']); ?></h2>
<div class="left">
<h3>Listes de réservations</h3>

<?php if($_COOKIE['role']=='e') { ?>
  <form class="" action="<?php $_SERVER['PHP_SELF']; ?>#liste" method="get">
    <input type="hidden" value="reserv" name="zone">
    <input type="hidden" value="2" name="add">
    <input type="hidden" value="<?php echo $event; ?>" name="event">
    <input type="submit" class="btn" value="Ajouter">
  </form>
<?php } ?>

<table class="table">
  <thead>
    <tr>
      <th>Cat.</th>
      <th>Titre</th>
      <th>Url</th>
      <th>Quotas</th>
      <th>Réserv.</th>
  <?php if($_COOKIE['role']=='e') echo '<th style="width:45px;">&nbsp; </th>'; ?> 
    </tr>
  </thead>
  <tbody>
<?php 
/****************************************************************************************************************************************/
/********************************************************* LISTE DES RESERVATIONS *********************************************************/
/****************************************************************************************************************************************/ 
$sqlReservAtt = "SELECT id_reserv, id_categorie, titre, quotas, url FROM re_reserv WHERE id_event = '$event'"; // nom de la table ! requette
$resultReservAtt = mysqli_query($connexion, $sqlReservAtt ) or die(mysqli_error());   
while ($dataReservAtt = mysqli_fetch_array($resultReservAtt)) {
  echo '<tr>';
    //catégorie
    echo '<td>'.$dataReservAtt['id_categorie'].'</td>';
    
    //titre
    echo '<td><a href="'.$_SERVER['PHP_SELF'].'?zone=reserv&amp;add=0&amp;event='.$event.'&amp;reserv='.$dataReservAtt['id_reserv'].'#liste">'.utf8_encode($dataReservAtt['titre']).'</a></td>';
    
    //url
    echo '<td>'.$dataReservAtt['url'].'</td>';
    
    //Quotas
    echo '<td>'.$dataReservAtt['quotas'].'</td>';
    
    //réservés
$sql_Allreserv = "SELECT SUM(place) AS place FROM re_inscription WHERE id_reserv = '$dataReservAtt[id_reserv]' GROUP BY id_reserv"; // nom de la table ! requette
$result_Allreserv = mysqli_query($connexion, $sql_Allreserv ) or die(mysqli_error());   
$data_Allreserv = mysqli_fetch_array($result_Allreserv);
    if(($dataReservAtt['quotas'] - $data_Allreserv['place']) < 2 ) {
      echo '<td class="import">';
    } elseif(($dataReservAtt['quotas'] - $data_Allreserv['place']) < 10 ) {
      echo '<td class="warning">';
    } else echo '<td>';
    echo $data_Allreserv['place'].'</td>';
    
    //Formulaire d'édition / suppression
    if($_COOKIE['role']=='e') {	
      echo '<td>';
	echo '<form name="suppr_news" action="'.$_SERVER['PHP_SELF'].'#liste" method="post" onclick="return confirm(\'voulez-vous vraiment supprimé cet événement?\')">
	    <input type="hidden" name="id_reserv" value="'.$dataReservAtt['id_reserv'].'">
	    <input name="deleteReserv" class="btn_suppr" type="submit" value="Supprimer">
	</form>';
      echo '</td>';
    }
  echo '</tr>';
}
?>
  </tbody>
</table>
</div>
<?php 
/********************************************************* FORMULAIRE ADD/MODIF *********************************************************/
if($_REQUEST['zone']=='reserv') {
  if($add == 0) {
    $sqlReservSelect = "SELECT * FROM re_reserv WHERE id_reserv ='$reserv'"; // nom de la table ! requette
    $resultReservSelect = mysqli_query($connexion, $sqlReservSelect ) or die(mysqli_error());
    $dataReservSelect = mysqli_fetch_array($resultReservSelect);
		
    $titre2 = 'Modifier '.$dataReservSelect['titre'];
    $new_reserv = FALSE;
	

  } else {
    $titre2 = 'Ajout';
    $new_reserv = TRUE;
  }
?>
<div class="right"></div>
<div class="middle">

    <h4><?php echo $titre2; ?></h4>
    <form action="<?php $_SERVER['PHP_SELF']; ?>?add=0&amp;zone=reserv&amp;event=<?php echo $event; ?>#liste" method="post">
	<div>
	    <label for="id_categorie">Catégorie</label>
	    <select name="id_categorie">
<?php
//requète pour récupérer la liste des salles
$sql_cat = "SELECT id_categorie, titre FROM re_categorie";
$result_cat = mysqli_query($connexion, $sql_cat) or die(mysqli_error());	
while ($data_cat = mysqli_fetch_array($result_cat)) {
  echo '<option value="'.$data_cat['id_categorie'].'"';
  if($dataReservSelect['id_categorie'] == $data_cat['id_categorie']) echo ' selected="selected"';
  echo '>'.utf8_encode($data_cat['titre']).'</option>';	
}
?>
	    </select>
	</div>
	<div>
	    <label for="titre">Titre</label>
	    <input id="titre" type="text" name="titre" value="<?php echo utf8_encode($dataReservSelect['titre']); ?>">
	</div>
	<div>
	    <label for="quotas">Quotas</label>
	    <input id="quotas" type="text" name="quotas" value="<?php echo utf8_encode($dataReservSelect['quotas']); ?>">
	</div>
	<div>
	    <label for="url">URL</label>
	    <input id="url" type="text" name="url" value="<?php echo utf8_encode($dataReservSelect['url']); ?>">
	</div>
	<div>
	    <label for="text_info">Texte d'information</label>
	    <textarea id="text_info" name="text_info"><?php if($new_reserv){echo "Invitation - Inscription à la soirée";}else {echo utf8_encode($dataReservSelect['text_info']);} ?></textarea>
	</div>
	<div>
	    <label for="text_complet">Texte si séance complète</label>
	    <textarea id="text_complet" name="text_complet"><?php if($new_reserv){echo "La Cinémathèque suisse ne dispose malheureusement plus d'invitation pour cet événement.";}else {echo utf8_encode($dataReservSelect['text_complet']);} ?></textarea>
	</div>
	<div>
	    <label for="text_fin">Texte si réservation terminée</label>
	    <textarea id="text_fin" name="text_fin"><?php if($new_reserv){echo "Les inscriptions pour cette séance sont closes.";}else {echo utf8_encode($dataReservSelect['text_fin']);} ?></textarea>
	</div>
	<input type="hidden" name="id_event" value="<?php echo $event; ?>">
	<input type="hidden" name="id_reserv" value="<?php echo $reserv; ?>">
	<?php if($_COOKIE['role']=='e') { echo '<input type="submit" name="saveReserv" class="btn" value="Enregistrer">'; } ?>
    </form>
</div>

<?php } /*fin reserv*/ ?>
<?php } /*fin event*/?>
<hr class="clear">